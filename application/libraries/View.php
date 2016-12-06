<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Ardhie1032\CodeIgniter3Lib\View\View as Template;

class View extends Template {
    
    public function __construct()
    {
        parent::__construct();
        get_instance()->load->library('asset');
    }
}
