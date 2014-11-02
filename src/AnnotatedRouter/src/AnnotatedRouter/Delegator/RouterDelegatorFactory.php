<?php
namespace AnnotatedRouter\Delegator;

use Exception;
use Zend\Mvc\Router\Console\SimpleRouteStack;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouterDelegatorFactory implements DelegatorFactoryInterface
{
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