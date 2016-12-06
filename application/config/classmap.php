<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['register_namespaces'] = [
    'App'    => APPPATH,
    'Module' => APPPATH . 'modules/',
    // 'prefix' => 'path_directory',
];

$config['register_classes'] = [
    'Modules'  => APPPATH . 'third_party/MX/Modules.php',
    'CI_Model' => BASEPATH . 'core/Model.php',
    // 'className' => 'file_name.php',
];
