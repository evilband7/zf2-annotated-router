<?php

namespace AnnotatedRouterTest;

require_once 'AbstractAnnotationTestCase.php';

use AnnotatedRouter\Parser\ControllerParser;
use AnnotatedRouter\Service\RouteConfigBuilder;
use AnnotatedRouterTest\TestController\ExtendsController;
use Zend\Code\Reflection\ClassReflection;

class ExtendsAnnotationTest extends AbstractAnnotationTestCase
{

    public function testIndexRouteCorrected()
    {
        /* @var $parser ControllerParser */
        $parser = $this->serviceManager->get('parser');
        $classReflection = new ClassReflection(new ExtendsController);
        $config = $parser->parseController($classReflection, new RouteConfigBuilder, 'extends');

        $routeConfig = $this->serviceManager->get('Config')['router']['routes'];
        $config = array_replace_recursive($config->toArray(), $routeConfig);

        $expected = array(
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
                        'child_routes' => array(
                            'root' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/root',
                                    'defaults' => array(
                                        'controller' => 'extends',
                                        'action' => 'index'
                                    ),
                                    'constraints' => null
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'index' => array(
                                        'type' => 'literal',
                                        'options' => array(
                                            'route' => '/index',
                                            'defaults' => array(
                                                'controller' => 'extends',
                                                'action' => 'index'
                                            ),
                                            'constraints' => null
                                        ),
                                        'may_terminate' => true
                                    )
                                )
                            )
                        ),
                    )
                ),
            )
        );

        $this->assertEquals($expected, $config);
    }

}
