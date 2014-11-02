<?php
/**
 * Annotated Router module for Zend Framework 2
 *
 * @link      https://github.com/alex-oleshkevich/zf2-annotated-routerfor the canonical source repository
 * @copyright Copyright (c) 2014 Alex Oleshkevich <alex.oleshkevich@gmail.com>
 * @license   http://en.wikipedia.org/wiki/MIT_License MIT
 */

namespace AnnotatedRouter\Controller;

use Zend\Code\Generator\ValueGenerator;
use Zend\Console\Console;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controller for command-line rool
 */
class ConsoleController extends AbstractActionController
{
    /**
     * Dumps calculated routes into cache file.
     * @see annotated_router.cache_file config key
     */
    public function dumpAction()
    {
        $config = $this->serviceLocator->get('Config');
        $console = Console::getInstance();
        $console->writeLine('Dumping annotated routes into "' . $config['annotated_router']['cache_file'] . '"');
        $annotatedRouterConfig = $this->serviceLocator->get('AnnotatedRouter\ControllerParser')->getRouteConfig();

        $generator = new ValueGenerator($annotatedRouterConfig);
        $content = "<?php\n\nreturn " . $generator . ';';

        file_put_contents($config['annotated_router']['cache_file'], $content);
    }

    /**
     * Dumps all available routes (including defined in modules) into cache file.
     * @see annotated_router.cache_file config key
     */
    public function dumpCompleteAction()
    {
        $config = $this->serviceLocator->get('Config');
        $console = Console::getInstance();
        $console->writeLine('Dumping all routes into "' . $config['annotated_router']['cache_file'] . '"');
        $annotatedRouterConfig = $this->serviceLocator->get('AnnotatedRouter\ControllerParser')->getRouteConfig();
        $annotatedRouterConfig = array_replace_recursive($annotatedRouterConfig, isset($config['router']['routes']) ? $config['router']['routes'] : array());

        $generator = new ValueGenerator($annotatedRouterConfig);
        $content = "<?php\n\nreturn " . $generator . ';';

        file_put_contents($config['annotated_router']['cache_file'], $content);
    }
}