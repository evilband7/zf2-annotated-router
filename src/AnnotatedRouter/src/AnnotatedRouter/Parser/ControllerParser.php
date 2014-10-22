<?php

namespace AnnotatedRouter\Parser;

use AnnotatedRouter\Annotation\Container;
use AnnotatedRouter\Annotation\Route;
use AnnotatedRouter\Service\RouteConfigBuilder;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;
use Zend\Filter\FilterChain;

class ControllerParser
{

    /**
     * @var AnnotationManager
     */
    protected $annotationManager;

    /**
     * @var array
     */
    protected $controllers;

    function __construct(AnnotationManager $annotationManager, array $controllers)
    {
        $this->annotationManager = $annotationManager;
        $this->controllers = $controllers;
    }

    public function getRouteConfig()
    {
        $configBuilder = new RouteConfigBuilder;
        foreach ($this->controllers as $controllerAlias => $controller) {
            $classReflection = new ClassReflection($controller);
            $this->parseController($classReflection, $configBuilder, $controllerAlias);
        }

        return $configBuilder->toArray();
    }

    public function parseController(ClassReflection $controller, RouteConfigBuilder $configBuilder, $controllerAlias)
    {
        $annotations = $this->getControllerAnnotations($controller);

        $baseRoute = new Container;
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Route) {
                $annotation->isValidForRootNode();
                $baseRoute = $annotation;
                $this->autodetectMissingFields($annotation, 'index', $controllerAlias);
                break;
            }
        }

        $methods = $controller->getMethods();
        /* @var $method MethodReflection */
        foreach ($methods as $method) {
            if (preg_match('/Action$/', $method->getName())) {
                $annotations = $this->getMethodAnnotations($method);
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Route) {
                        $annotation->isValidForChildNode();
                        $this->autodetectMissingFields($annotation, $method, $controllerAlias);
                        $baseRoute->addChild($annotation);
                    }
                }
            }
        }
        $configBuilder->addPart($baseRoute);
        return $configBuilder;
    }

    public function getControllerAnnotations(ClassReflection $controller)
    {
        $annotations = $controller->getAnnotations($this->annotationManager);
        if (!$annotations) {
            $annotations = new AnnotationCollection;
        }
        return $annotations;
    }

    public function getMethodAnnotations(MethodReflection $method)
    {
        $annotations = $method->getAnnotations($this->annotationManager);
        if (!$annotations) {
            $annotations = new AnnotationCollection;
        }
        return $annotations;
    }

    public function autodetectMissingFields(Route $annotation, $method, $controllerKey)
    {
        if ($method instanceof MethodReflection) {
            $methodName = $method->getName();
        } else if (is_string($method)) {
            $methodName = $method;
        } else {
            throw new Exception('Method must be a string or instance of MethodReflection');
        }

        if (!$annotation->hasName()) {
            $annotation->setName($this->filterActionMethodName($methodName));
        }

        if (!$annotation->hasType()) {
            $annotation->setType('literal');
        }

        if (!$annotation->hasDefaultController()) {
            $annotation->setDefaultController($controllerKey);
        }

        if (!$annotation->hasDefaultAction()) {
            $annotation->setDefaultAction($this->filterActionMethodName($methodName));
        }

        if (!$annotation->hasRoute()) {
            $annotation->setRoute('/' . $this->filterActionMethodName($methodName));
        }
        return $annotation;
    }

    protected function filterActionMethodName($name)
    {
        $filter = new FilterChain;
        $filter->attachByName('Zend\Filter\Word\CamelCaseToDash');
        $filter->attachByName('StringToLower');
        return rtrim(preg_replace('/action$/', '', $filter->filter($name)), '-');
    }

}
