<?php

namespace AnnotatedRouterTest;

require_once 'AbstractAnnotationTestCase.php';

use AnnotatedRouter\Parser\ControllerParser;
use AnnotatedRouter\Service\RouteConfigBuilder;
use AnnotatedRouterTest\TestController\NoIndexRouteController;
use Zend\Code\Reflection\ClassReflection;

class NoIndexRouteAnnotationTest extends AbstractAnnotationTestCase
{

    public function testIndexRouteCorrected()
    {
        /* @var $parser ControllerParser */
        $parser = $this->serviceManager->get('parser');
        $classReflection = new ClassReflection(new NoIndexRouteController);
        $builder = new RouteConfigBuilder();
        $builder->addPart($parser->parseController($classReflection, 'noindex'));

        $expected = array(
            'root' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/root',
                    'defaults' => array(
                        'controller' => 'noindex',
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
                                'controller' => 'noindex',
                                'action' => 'index'
                            ),
                            'constraints' => null
                        ),
                        'may_terminate' => true
                    ),
                    'edit' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/edit',
                            'defaults' => array(
                                'controller' => 'noindex',
                                'action' => 'edit'
                            ),
                            'constraints' => null
                        ),
                        'may_terminate' => true
                    ),
                    'remove' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'controller' => 'noindex',
                                'action' => 'remove'
                            ),
                            'constraints' => null
                        ),
                        'may_terminate' => true
                    )
                )
            )
        );

        $this->assertEquals($expected, $builder->toArray());
    }

}
