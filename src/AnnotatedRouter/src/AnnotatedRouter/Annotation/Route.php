<?php

namespace AnnotatedRouter\Annotation;

use AnnotatedRouter\Exception\InvalidArgumentException;
use Zend\Code\Annotation\AnnotationInterface;

/**
 * @Annotation
 */
class Route implements AnnotationInterface
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $route;

    /**
     * @var array
     */
    public $defaults;

    /**
     * @var array
     */
    public $constraints;

    public $mayTerminate = true;

    /**
     * @var string
     */
    public $extends;

    /**
     * @var array
     */
    protected $children = array();

    /**
     * @param array $content
     */
    public function initialize($content)
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasName()
    {
        return (bool) $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function hasType()
    {
        return (bool) $this->type;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function hasRoute()
    {
        return (bool) $this->route;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function hasDefaults()
    {
        return is_array($this->defaults);
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    public function setDefaultController($name)
    {
        $this->defaults['controller'] = $name;
    }

    public function getDefaultController()
    {
        return $this->defaults['controller'];
    }

    public function setDefaultAction($name)
    {
        $this->defaults['action'] = $name;
    }

    public function getDefaultAction()
    {
        return $this->defaults['action'];
    }

    public function hasDefaultController()
    {
        return $this->hasDefaults() && isset($this->defaults['controller']);
    }

    public function hasDefaultAction()
    {
        return $this->hasDefaults() && isset($this->defaults['action']);
    }

    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(AnnotationInterface $child)
    {
        $this->children[] = $child;
    }

    public function getExtends()
    {
        return $this->extends;
    }

    public function setExtends($extends)
    {
        $this->extends = $extends;
    }

    public function getMayTerminate()
    {
        return $this->mayTerminate;
    }

    public function setMayTerminate($flag)
    {
        $this->mayTerminate = (bool) $flag;
    }

    public function isValidForRootNode()
    {
        if (!$this->name) {
            throw new InvalidArgumentException('"name" property must be set to root route.');
        }

        if (!$this->route) {
            throw new InvalidArgumentException('"route" property must be set to root route.');
        }
    }

    public function isValidForChildNode()
    {
        if ($this->extends) {
            throw new InvalidArgumentException(
                'Child route cannot extend another one (not implemented). '
                    . 'Seen in route name"' . $this->name . '", '
                    . 'route: "' . $this->route . '", '
                    . 'tried to extend: "' . $this->extends . '"'
                );
        }
    }

    public function extend(AnnotationInterface $annotation)
    {
        $params = get_object_vars($annotation);
        foreach ($params as $property => $value) {
            if (property_exists($this, $property) && !in_array($property, ['name', 'route'])) {
                if (!$this->$property) {
                    $this->$property = $value;
                }
            }
        }
    }

}
