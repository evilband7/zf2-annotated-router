<?php

namespace AnnotatedRouterTest;


require_once 'TestController/NoBaseController.php';
require_once 'TestController/NamespacedController.php';
require_once 'TestController/NoIndexRouteController.php';
require_once 'TestController/InvalidRootRouteController.php';
require_once 'TestController/ExtendsController.php';

use PHPUnit_Framework_TestCase;
use Zend\Code\Generator\ValueGenerator;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class AbstractAnnotationTestCase extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        parent::setUp();
        $this->serviceManager = new ServiceManager;
        $controllerLoader = new ControllerManager();
        $controllerLoader->setServiceLocator($this->serviceManager);
        $controllerLoader->setInvokableClass('nobase', 'AnnotatedRouterTest\TestController\NoBaseController');
        $controllerLoader->setInvokableClass('namespaced', 'AnnotatedRouterTest\TestController\NamespacedController');
        $controllerLoader->setInvokableClass('noindexroute', 'AnnotatedRouterTest\TestController\NoIndexRouteController');
        $controllerLoader->setInvokableClass('invalidrootroute', 'AnnotatedRouterTest\TestController\InvalidRootRouteController');
        $controllerLoader->setInvokableClass('extends', 'AnnotatedRouterTest\TestController\ExtendsController');
        $this->serviceManager->setService('ControllerLoader', $controllerLoader);
        $this->serviceManager->setFactory('AnnotatedRouter\AnnotationManager', 'AnnotatedRouter\Service\AnnotationManagerFactory');
        $this->serviceManager->setFactory('parser', 'AnnotatedRouter\Service\ControllerParserFactory');
        $this->serviceManager->setService('Config', array(
            'annotated_router' => array(
                'compile_on_request' => true,
                'cache_file' => 'data/cache/router.cache.php',
                'use_cache' => true,
                'annotations' => array(
                    'AnnotatedRouter\Annotation\Base',
                    'AnnotatedRouter\Annotation\Index',
                    'AnnotatedRouter\Annotation\Route',
                )
            ),
            'router' => array(
                'routes' => array(
                    'default' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/'
                        ),
                        'child_routes' => array(
                            'help' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/help'
                                ),
                            )
                        )
                    )
                )
            )
        ));
    }

    protected function coolFormat($array)
    {
        $generator = new ValueGenerator($array);
        return $generator->generate();
    }

}
