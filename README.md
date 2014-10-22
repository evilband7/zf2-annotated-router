# Annotated Router for Zend Framework 2

This module provides routing annotation classes to use within controller.
The goal of this project is get rid of large routes configuration arrays in module configs.

## Usage:
###Install module via composer
```bash
composer require alex.oleshkevich/zf2-annotated-router
```

### Enable it in application.config.php
```php
return array(
    'modules' => array(
        // other modules
        'AnnotatedRouter'
    ),
    // other content
);
```

### Configuration:
This default options can be overwritted within your application:
```php
array(
    'annotated_router' => array(
        'compile_on_request' => true, // do re-read annotation on every request?
        'cache_file' => 'data/cache/router.cache.php', // cache file
        'use_cache' => true, // if true, when 'compile_on_request' is off, will load config from 'cache_file'
    ),
)
```

### Command line usage:
```bash
  cli router dump               Compiles router annotations and dump into cache file.                                                                                         
  cli router dump --complete    Compiles router annotations and dump all defined routes into cache file. 
```

### Add annotations namespace into controller uses
```php
use AnnotatedRouter\Annotation\Route;
```

### Annotate actions with @Route annotation
```php
/**
 * @Route(name="dashboard", route="/dashboard")
 */
public function indexAction()
{
    return new ViewModel();
}
```
A generated output will be:
```php
array (
    'dashboard' => array(
        'type' => 'literal',
        'options' => array(
            'route' => '/dashboard'
        ),
    )
);
```

### If you want to group controller actions under the same parent route, add @Route annotation to class definition:
```php
/**
 * @Route(name="home", route="/")
 */
class IndexController extends AbstractActionController
```
This will result in:
```php
array (
    'home' => array(
        'type' => 'literal',
        'options' => array(
            'route' => '/'
        ),
        'child_routes' => array(
            'dashboard' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/dashboard'
                ),
            )
        )
    )
);
```

### Class-level annotations can be insterten into another route:
```php
/**
 * @Route(extends="parent/route", name="home", route="/")
 */
class IndexController extends AbstractActionController
```

The output will be:
```php
array (
    'parent' => array(
        'type' => 'literal',
        'options' => array(
            'route' => '/parent'
        ),
        'child_routes' => array(
            'route' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/route'
                ),
                'child_routes' => array(
                    'home' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/home'
                        ),
                    ),
                    'child_routes' => array(
                        // routes from /home route controller actions
                    )
                )
            )
        )
    )
);
```


## Important to know
### Complete @Route definition
```php
/**
 * @Route(
 *     extends="parent-route"
 *     name="complete-definition",
 *     route="/complete-definition/:id/:method",
 *     type="segment",
 *     defaults={"controller": "nobase", "action": "complete-definition-action"},
 *     constraints={"id": "\d+", "method": "\w+"}
 * )
 */
public function someMethod() {}
// or if apply to controller
class IndexController extends AbstractActionController
```

#### 1. "extends" only applied to class-level route definition, if you try to add it to action route, that will fail with exception as it is not currently implemented.

#### 2. "extends" must contain valid and existing route
#### 3. "extends" can point to child route, eg. passing {"extends": "root/first/second"} will extend given path with routes from current class. 

#### 4. Root route annotation must contain "route" and "name"
#### 5. Child (action) routes may be empty (see full controller listing). In that case module will try to guess options.

### Complete controller listing:
```php
<?php
namespace AnnotatedRouterTest\TestController;

use AnnotatedRouter\Annotation as Router;
use Zend\Mvc\Controller\AbstractActionController;

class NoBaseController extends AbstractActionController
{
    /**
     * @Router\Route(
     *     name="complete-definition",
     *     route="/complete-definition/:id/:method",
     *     type="segment",
     *     defaults={"controller": "nobase", "action": "complete-definition-action"},
     *     constraints={"id": "\d+", "method": "\w+"}
     * )
     */
    public function action1Action()
    {}

    /**
     * @Router\Route(
     *     route="/route",
     *     type="literal",
     *     defaults={"controller": "nobase", "action": "no-route"}
     * )
     */
    public function action2Action()
    {}

    /**
     * @Router\Route(
     *     type="literal",
     *     defaults={"controller": "nobase", "action": "no-route"}
     * )
     */
    public function action3Action()
    {}

    /**
     * @Router\Route(
     *     defaults={"controller": "nobase", "action": "no-route"}
     * )
     */
    public function action4Action()
    {}

    /**
     * @Router\Route(
     *     defaults={"action": "no-route"}
     * )
     */
    public function action5Action()
    {}

    /**
     * @Router\Route
     */
    public function action6Action()
    {}
}

```


 
