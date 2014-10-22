<?php
namespace AnnotatedRouter\Service;

use AnnotatedRouter\Parser\ControllerParser;
use Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerParserFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controllers = array();
        $annotationManager = $serviceLocator->get('AnnotatedRouter\AnnotationManager');
        $controllerServices = $serviceLocator->get('ControllerLoader')->getCanonicalNames();
        foreach ($controllerServices as $controller) {
            try {
                $controllers[$controller] = get_class($serviceLocator->get('ControllerLoader')->get($controller));
            } catch (Exception $e) {}
        }
        return new ControllerParser($annotationManager, $controllers);
    }

}