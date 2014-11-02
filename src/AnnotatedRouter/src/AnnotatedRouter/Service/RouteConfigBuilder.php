<?php
/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository
 * @copyright Copyright (c) 2014 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

namespace AnnotatedRouter\Service;

use AnnotatedRouter\Annotation\Container;
use AnnotatedRouter\Annotation\Route;
use Zend\Code\Annotation\AnnotationInterface;

/**
 * Convers annotations into array.
 */
class RouteConfigBuilder
{

    /**
     * Routes
     *
     * @var array
     */
    protected $parts = array();

    /**
     * Add a route.
     *
     * @param Route $annotation
     * @return RouteConfigBuilder
     */
    public function addPart(Route $annotation)
    {
        if ($annotation instanceof Container) {
            $this->parts = array_merge($this->parts, $annotation->getChildren());
        } else {
            $this->parts[] = $annotation;
        }
        return $this;
    }

    /**
     * Dump routes as array.
     *
     * @return array
     */
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

    /**
     * Extend parent route with children.
     *
     * @param array $path
     * @param array $config
     * @return array
     */
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

    /**
     * Converts annotation into ZF2 route config item.
     * 
     * @param AnnotationInterface $annotation
     * @return array
     */
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
