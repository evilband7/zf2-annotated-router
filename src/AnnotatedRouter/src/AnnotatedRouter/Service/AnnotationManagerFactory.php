<?php
namespace AnnotatedRouter\Service;

use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnotationManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['annotated_router'];
        $annotationManager = new AnnotationManager;
        $parser = new DoctrineAnnotationParser;
        $parser->registerAnnotations($config['annotations']);
        $annotationManager->attach($parser);
        return $annotationManager;
    }

}