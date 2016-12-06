<?php

$config['jquery']['js'] = [
    'dev' => '//assets.cdn/jquery/jquery-2.2.3.min.js',
    'pro' => '',
];

$config['bootstrap']['css'] = [
    'dev' => 'assets/vendor/bootstrap/css/bootstrap.min.css',
    'pro' => 'http://bootstrapcdn.com/bootstrap.min.css',
];

$config['bootstrap']['js'] = [
    'dev' => 'assets/vendor/bootstrap/js/bootstrap.min.js',
    'pro' => '',
    'attr' => [],
];

$config['font-awesome']['css'] = [
    'dev' => 'assets/vendor/font-awesome/css/font-awesome.min.css',
    'pro' => '',
];

$config['_template_']['css'] = '<link rel="stylesheet" href=":link:":attr:>'."\n";
$config['_template_']['js'] = '<script src=":link:":attr:></script>'."\n";

$config['_mode_'] = (ENVIRONMENT !== 'production') ? 'dev' : 'pro';

$config['_group_'] = [
    'bootstrap' => ['jquery', 'bootstrap'],
    'sbadmin'   => ['jquery', 'bootstrap', 'font-awesome'],
];


