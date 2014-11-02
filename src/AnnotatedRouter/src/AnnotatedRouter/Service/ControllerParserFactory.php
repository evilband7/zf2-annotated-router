<?php
/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository
 * @copyright Copyright (c) 2014 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

namespace AnnotatedRouter\Service;

use AnnotatedRouter\Parser\ControllerParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates a controller parser.
 */
class ControllerParserFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ControllerParser
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $annotationManager = $serviceLocator->get('AnnotatedRouter\AnnotationManager');
        $controllerServices = $serviceLocator->get('Config')['controllers'];
        $controllers = array_replace(
                $controllerServices['invokables'],
                isset($controllerServices['factories']) ? $controllerServices['factories'] : array(),
                isset($controllerServices['services']) ? $controllerServices['services'] : array()
            );
        return new ControllerParser($annotationManager, $controllers);
    }

}