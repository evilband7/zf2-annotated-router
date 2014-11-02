<?php
/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository
 * @copyright Copyright (c) 2014 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

namespace AnnotatedRouter\Parser;

use AnnotatedRouter\Annotation\Container;
use AnnotatedRouter\Annotation\Route;
use AnnotatedRouter\Service\RouteConfigBuilder;
use Exception;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;
use Zend\Filter\FilterChain;

/**
 * Parser annotations from provided controllers.
 */
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

    /**
     * Constructor.
     *
     * @param AnnotationManager $annotationManager
     * @param array $controllers
     */
    public function __construct(AnnotationManager $annotationManager, array $controllers)
    {
        $this->annotationManager = $annotationManager;
        $this->controllers = $controllers;
    }

    /**
     * Returns complete router config from parsed controllers.
     *
     * @return array
     */
    public function getRouteConfig()
    {
        $configBuilder = new RouteConfigBuilder;
        foreach ($this->controllers as $controllerAlias => $controller) {
            $classReflection = new ClassReflection($controller);
            $routes = $this->parseController($classReflection, $controllerAlias);
            $configBuilder->addPart($routes);
        }

        return $configBuilder->toArray();
    }

    /**
     * Extract annotation tree from controller.
     *
     * @param ClassReflection $controller
     * @param string $controllerAlias An alias of controllers. Used in controller autodetection feature.
     * @return Route
     */
    public function parseController(ClassReflection $controller, $controllerAlias)
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
        return $baseRoute;
    }

    /**
     * Returns class-level annotations.
     *
     * @param ClassReflection $controller
     * @return AnnotationCollection
     */
    public function getControllerAnnotations(ClassReflection $controller)
    {
        $annotations = $controller->getAnnotations($this->annotationManager);
        if (!$annotations) {
            $annotations = new AnnotationCollection;
        }
        return $annotations;
    }

    /**
     * Returns annotations for actions.
     *
     * @param MethodReflection $method
     * @return AnnotationCollection
     */
    public function getMethodAnnotations(MethodReflection $method)
    {
        $annotations = $method->getAnnotations($this->annotationManager);
        if (!$annotations) {
            $annotations = new AnnotationCollection;
        }
        return $annotations;
    }

    /**
     * Tries to guess default values for route if there some missing ones.
     *
     * @param Route $annotation
     * @param MethodReflection $method
     * @param string $controllerKey
     * @return Route
     * @throws Exception
     */
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

    /**
     * Sanitizes action name to use in route.
     *
     * @param string $name
     * @return string
     */
    protected function filterActionMethodName($name)
    {
        $filter = new FilterChain;
        $filter->attachByName('Zend\Filter\Word\CamelCaseToDash');
        $filter->attachByName('StringToLower');
        return rtrim(preg_replace('/action$/', '', $filter->filter($name)), '-');
    }

}
