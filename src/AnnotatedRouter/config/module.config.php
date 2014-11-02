<?php
/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository
 * @copyright Copyright (c) 2014 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

return array(
    'annotated_router' => array(
        // parse controllers on every request. disable for prod
        'compile_on_request' => true,

        // a file to dump router config
        'cache_file' => 'data/cache/router.cache.php',

        // use cached routes instead of controllers parsing
        'use_cache' => false,
    ),
    'service_manager' => array(
        'delegators' => array(
            'Router' => array(
                'AnnotatedRouter\Delegator\RouterDelegatorFactory'
            )
        ),
        'factories' => array(
            'AnnotatedRouter\AnnotationManager' => 'AnnotatedRouter\Service\AnnotationManagerFactory',
            'AnnotatedRouter\ControllerParser' => 'AnnotatedRouter\Service\ControllerParserFactory',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'AnnotatedRouter\Controller\Console' => 'AnnotatedRouter\Controller\ConsoleController'
        )
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'router-dump' => array(
                    'options' => array(
                        'route'    => 'router dump',
                        'defaults' => array(
                            'controller' => 'AnnotatedRouter\Controller\Console',
                            'action'     => 'dump'
                        )
                    )
                ),
                'router-dump-complete' => array(
                    'options' => array(
                        'route'    => 'router dump --complete',
                        'defaults' => array(
                            'controller' => 'AnnotatedRouter\Controller\Console',
                            'action'     => 'dump-complete'
                        )
                    )
                ),
            )
        )
    )
);