<?php

namespace AnnotatedRouterTest;

require_once 'AbstractAnnotationTestCase.php';

use AnnotatedRouter\Parser\ControllerParser;
use AnnotatedRouter\Service\RouteConfigBuilder;
use AnnotatedRouterTest\TestController\InvalidRootRouteController;
use Zend\Code\Reflection\ClassReflection;

class InvalidRootRouteAnnotationTest extends AbstractAnnotationTestCase
{

    /**
     * @expectedException AnnotatedRouter\Exception\InvalidArgumentException
     */
    public function testExceptionThrown()
    {
        /* @var $parser ControllerParser */
        $parser = $this->serviceManager->get('parser');
        $classReflection = new ClassReflection(new InvalidRootRouteController);
        $parser->parseController($classReflection, new RouteConfigBuilder, 'namespaced');
    }

}
