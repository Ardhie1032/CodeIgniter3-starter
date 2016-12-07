<?php namespace Ardhie1032\CodeIgniter3Lib\Core;

defined('BASEPATH') or exit('No direct script access allowed');

include 'Autoloader.php';

class Hook extends \CI_Hooks {

    public function call_hook($which = '')
    {
        if($which == 'pre_system')
        {
            $cfg =& load_class('Config', 'core');
            $cfg->load('classmap');
            $loader = new Autoloader;
            $loader->addNamespaces($cfg->item('register_namespaces'));
            $loader->addClasses($cfg->item('register_classes'));
            $loader->register();
        }
        
		if ( !$this->enabled or ! isset($this->hooks[$which]))
		{
			return false;
		}

		if (is_array($this->hooks[$which]) && ! isset($this->hooks[$which]['function']))
		{
			foreach ($this->hooks[$which] as $val)
			{
				$this->_run_hook($val);
			}
		}
		else
		{
			$this->_run_hook($this->hooks[$which]);
		}

		return true;
    }
    
}
