<?php

namespace AnnotatedRouterTest;

require_once 'AbstractAnnotationTestCase.php';

use AnnotatedRouter\Parser\ControllerParser;
use AnnotatedRouter\Service\RouteConfigBuilder;
use AnnotatedRouterTest\AbstractAnnotationTestCase;
use AnnotatedRouterTest\TestController\NamespacedController;
use Zend\Code\Reflection\ClassReflection;

class NamespacedRouteAnnotationTest extends AbstractAnnotationTestCase
{

    public function testChildNodesAdded()
    {
        /* @var $parser ControllerParser */
        $parser = $this->serviceManager->get('parser');
        $classReflection = new ClassReflection(new NamespacedController);
        $builder = new RouteConfigBuilder();
        $builder->addPart($parser->parseController($classReflection, 'namespaced'));

        $expected = array(
            'root' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/root/:id/:method',
                    'defaults' => array(
                        'controller' => 'namespaced',
                        'action' => 'index'
                    ),
                    'constraints' => array(
                        'id' => '\\d+',
                        'method' => '\\w+'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'index' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/root/:id/:method',
                            'defaults' => array(
                                'controller' => 'nobase',
                                'action' => 'complete-definition-action'
                            ),
                            'constraints' => array(
                                'id' => '\\d+',
                                'method' => '\\w+'
                            )
                        ),
                        'may_terminate' => true
                    ),
                    'edit' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/edit',
                            'defaults' => array(
                                'controller' => 'namespaced',
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
                                'controller' => 'namespaced',
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
