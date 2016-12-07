# Simple CodeIgniter 3 Template
---------------

## Directory structure

    [application]/views/
    .. _templates/
    .. .. bootstrap/
    .. .. .. _layout/
    .. .. .. .. main.php
    .. .. .. .. other_layout.php
    .. .. .. _partial/
    .. .. .. .. header.php
    .. .. .. .. footer.php
    .. .. SB-Admin-2/
    .. .. .. _layout/
    .. .. .. .. dashbord.php
    .. .. .. .. starter.php
    .. .. .. _partial/
    .. .. .. .. header.php
    .. .. .. .. footer.php
    .. .. .. .. sidebar.php
    .. .. Other-Template/
    .. .. .. _layout/
    .. .. .. ..
    .. .. .. _partial/
    .. .. .. ..


## Usage

```php
<?php

class Home extends CI_Controller {

    public function index()
    {
        $this->view->useTemplate('bootstrap');
        $this->view->setContent('panel-info', 'panel_info');
        $this->view->render();
    }
}

```

## Template

``View::useTemplate()``

``View::useTemplate(string $template_name, [optional] array $data)``

``View::useTemplate(array $data, [optional] string $layout)``

``View::useTemplate(string $template_name, [optional] array $data, [optional] string $layout, [optional] string $module)``

```php
<?php

$this->view->useTemplate();

$this->view->useTemplate('bootstrap');

$this->view->useTemplate('bootstrap', [], 'home_layout');

$this->view->useTemplate([], 'crud_layout');

$this->view->useTemplate('bootstrap_news', [], 'main', 'news');

```

## Main Content

``View::render()``

``View::render(string $view)``

[Controller]

```php
<?php

// ../application/controllers/[controller]/[method].php
// ../application/views/[controller]/[method].php
$this->view->render();

/* Sub Controller */
// ../application/controllers/[dir]/[controller]/[method].php
// ../application/views/[dir]/[controller]/[method].php
$this->view->render();

// ../application/views/welcome_message.php
$this->view->render('welcome_message');

```

[View]

```php

<?php echo $this->view->content(); ?>

```

## Partial content

``View::setContent(string $name, string $view, [optional] array $data)``

[Controller]

```php
<?php

// ../application/views/admin/dashbord.php
$this->view->setContent('partial_name', 'admin/dashbord');

$data = [];

$this->view->setContent('partial_name', 'admin/dashbord', $data);

```

[View]

```php
<?php

<?php echo $this->view->content('partial_name'); ?>
```

## Caching

``View::cache(bool $cache, [optional] string $filename)``

``View::cache(int $miliseconds, [optional] string $filename)``

[Controller]

```php
<?php

$this->view->cache(60, 'admin-dashbord');

$this->view->cache(true, 'admin/dashbord'); // default 0s

```

## Method Chaining

```php
<?php

$this->view
     ->cache(300)
     ->useTemplate()
     ->setContent('info', 'public/info')
     ->render();
  
```

## Example Config File

``application/config/view.php``

```php
<?php

$config['layouts_dir']   = '_layout';
$config['partials_dir']  = '_partial';

$config['default_template'] = 'sbadmin2';
$config['default_layout']   = 'main';

$config['templates'] = [
    'sbadmin'   => 'SB-Admin-2',
    'bootstrap' => 'bootstrap',
];

$config['cache'] = 300;
```

## Widget

### Directory structure

    [application]/widgets/
    .. MyMenu/
    .. .. Control.php
    .. .. view.php
    .. OtherWidget/
    .. .. Control.php
    .. .. view.php


### Usage

```php
<?php echo $this->view->widget('App\Widgets\MyMenu', ['title' => 'My Menu']); ?>
```

``application/widgets/MyMenu/Control.php``

```php
<?php namespace App\Widgets\MyMenu;

class Control extends Widget {

    public function view($data=[])
    {
        $this->render($data);
    }
}
    
```

``application/widgets/MyMenu/view.php``

```html

<ul>
  <li>About</li>
  <li>Register</li>
</ul>

```
