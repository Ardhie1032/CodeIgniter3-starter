<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['register_namespaces'] = [
    // Do not remove!!!
    'Ardhie1032\CodeIgniter3Lib' => 'phar://'. APPPATH . '../CodeIgniter3Lib.phar',
    'App'    => APPPATH,
    // Optional
    //'Module' => APPPATH . 'modules/',
    // 'prefix' => 'path_directory',
];

$config['register_classes'] = [
    'CI_Model' => BASEPATH . 'core/Model.php',
    //'Modules'  => APPPATH . 'third_party/MX/Modules.php',
    // 'className' => 'file_name.php',
];
