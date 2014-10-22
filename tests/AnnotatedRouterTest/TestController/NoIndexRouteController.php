<?php
namespace AnnotatedRouterTest\TestController;

use AnnotatedRouter\Annotation as Router;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @Router\Route(name="root", route="/root")
 */
class NoIndexRouteController extends AbstractActionController
{
    /**
     * @Router\Route
     */
    public function indexAction()
    {}

    /**
     * @Router\Route
     */
    public function editAction()
    {}

    /**
     * @Router\Route
     */
    public function removeAction()
    {}
}