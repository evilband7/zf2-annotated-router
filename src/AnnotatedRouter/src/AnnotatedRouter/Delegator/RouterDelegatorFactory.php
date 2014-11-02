<?php
/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository
 * @copyright Copyright (c) 2014 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

namespace AnnotatedRouter\Delegator;

use Exception;
use Zend\Mvc\Router\Console\SimpleRouteStack;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Provides annotated routes into standard router.
 * Applied on first router service call.
 */
class RouterDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @param callable $callback
     * @return SimpleRouteStack
     * @throws Exception
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /* @var $router TreeRouteStack */
        $router = $callback();
        if ($router instanceof SimpleRouteStack) {
            return $router;
        }

        $config = $serviceLocator->get('Config');
        $doCompileOnRequest = (bool) $config['annotated_router']['compile_on_request'];
        $cacheFile = $config['annotated_router']['cache_file'];
        $useCache = $config['annotated_router']['use_cache'];

        $annotatedRouterConfig = array();
        if ($doCompileOnRequest) {
            $annotatedRouterConfig = $serviceLocator->get('AnnotatedRouter\ControllerParser')->getRouteConfig();
        } else if ($useCache) {
            if (file_exists($cacheFile)) {
                $annotatedRouterConfig = include $cacheFile;
            } else {
                throw new Exception('Cache file: "' . $cacheFile . '" does not exists.');
            }
        }

        $defaultRouterConfig = isset($config['router']['routes']) ? $config['router']['routes'] : array();
        $mergedRouterConfig = array_replace_recursive($annotatedRouterConfig, $defaultRouterConfig);
        $router->addRoutes($mergedRouterConfig);
        return $router;
    }

}