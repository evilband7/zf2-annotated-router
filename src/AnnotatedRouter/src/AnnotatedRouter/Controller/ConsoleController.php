<?php

namespace AnnotatedRouter\Controller;

use Zend\Code\Generator\ValueGenerator;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    public function dumpAction()
    {
        $config = $this->serviceLocator->get('Config');
        $annotatedRouterConfig = $this->serviceLocator->get('AnnotatedRouter\ControllerParser')->getRouteConfig();

        $generator = new ValueGenerator($annotatedRouterConfig);
        $content = "<?php\n\nreturn " . $generator . ';';

        file_put_contents($config['annotated_router']['cache_file'], $content);
    }

    public function dumpCompleteAction()
    {
        $config = $this->serviceLocator->get('Config');
        $annotatedRouterConfig = $this->serviceLocator->get('AnnotatedRouter\ControllerParser')->getRouteConfig();
        $annotatedRouterConfig = array_replace_recursive($annotatedRouterConfig, $config['router']['routes']);

        $generator = new ValueGenerator($annotatedRouterConfig);
        $content = "<?php\n\nreturn " . $generator . ';';

        file_put_contents($config['annotated_router']['cache_file'], $content);
    }
}