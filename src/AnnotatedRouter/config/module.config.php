<?php
return array(
    'annotated_router' => array(
        'compile_on_request' => true,
        'cache_file' => 'data/cache/router.cache.php',
        'use_cache' => false,
        'annotations' => array(
            'AnnotatedRouter\Annotation\Route',
        )
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