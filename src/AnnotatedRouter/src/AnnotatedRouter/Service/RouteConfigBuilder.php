<?php

namespace AnnotatedRouter\Service;

use AnnotatedRouter\Annotation\Container;
use AnnotatedRouter\Annotation\Route;
use Zend\Code\Annotation\AnnotationInterface;

class RouteConfigBuilder
{

    protected $parts = array();

    public function addPart(Route $annotation)
    {
        if ($annotation instanceof Container) {
            $this->parts = array_merge($this->parts, $annotation->getChildren());
        } else {
            $this->parts[] = $annotation;
        }
    }

    public function toArray()
    {
        $config = array();
        /* @var $part Route */
        foreach ($this->parts as $part) {
            $routeConfig = &$this->expand(explode('/', $part->getExtends()), $config);
            $routeConfig[$part->getName()] = $this->buildRouteFromAnnotation($part);

            foreach ($part->getChildren() as $child) {
                $routeConfig[$part->getName()]['child_routes'][$child->getName()] = $this->buildRouteFromAnnotation($child);
            }
        }
        return $config;
    }

    protected function &expand(array $path, array &$config)
    {
        $path = array_filter($path, function ($value) {
            return (bool) $value;
        });

        $ref = &$config;

        if (empty($path)) {
            return $ref;
        }

        foreach ($path as $key) {
            if (!isset($ref[$key])) {
                $ref[$key] = array(
                    'child_routes' => array()
                );
            }
            $ref = &$ref[$key]['child_routes'];
        }

        return $ref;
    }

    protected function buildRouteFromAnnotation(AnnotationInterface $annotation)
    {
        return array(
            'type' => $annotation->getType(),
            'options' => array(
                'route' => $annotation->getRoute(),
                'defaults' => $annotation->getDefaults(),
                'constraints' => $annotation->getConstraints()
            ),
            'may_terminate' => (bool) $annotation->getMayTerminate()
        );
    }

}
