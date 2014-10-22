<?php
namespace AnnotatedRouterTest\TestController;

use AnnotatedRouter\Annotation as Router;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @Router\Route(
 *     name="root",
 *     route="/root/:id/:method",
 *     type="segment",
 *     defaults={"controller": "namespaced", "action": "index"},
 *     constraints={"id": "\d+", "method": "\w+"}
 * )
 */
class NamespacedController extends AbstractActionController
{
    /**
     * @Router\Route(
     *     name="index",
     *     route="/root/:id/:method",
     *     type="segment",
     *     defaults={"controller": "nobase", "action": "complete-definition-action"},
     *     constraints={"id": "\d+", "method": "\w+"}
     * )
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