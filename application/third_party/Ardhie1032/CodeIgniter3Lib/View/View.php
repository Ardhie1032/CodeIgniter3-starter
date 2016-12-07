<?php namespace Ardhie1032\CodeIgniter3Lib\View;

/*
MIT License

Copyright (c) 2016 Ardhie1032

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  @name        CodeIgniter Template Library
 *  @version     1.0a1
 *  @author      Ardhie1032
 *  @link        https://github.com/ardhie1032
 *  @email       <1032ardhie@gmail.com>
 *  @license     MIT License Copyright (c) 2016 Ardhie1032
 *  
 */
class View {
    
    /**
     *  All private methods
     */
    private
        $_ci,
        $_mainContent,
        $_cfg,
        $_useCache = false,
        $_filename = false,
        $_template = false,
        $_partContents = [],
        $_partials = [],
        $_content = [],
        $_def = [],
        $_data = []
    ;
    
    /**
     *  Constructor
     *  
     *  @return void
     */
    public function __construct()
    {
        $this->_ci =& get_instance();
        $this->_cfg = $this->_ci->config->load('view', true);
        
        if(!class_exists('MX_Router'))
            $this->_cfg = $this->_ci->config->config['view'];
        
        $this->_useCache = isset($this->_cfg['cache']) ? $this->_cfg['cache'] : false;
    }
    
    /**
     *  Set Partial Content
     *  (method chaining)
     *  
     *  @param    string  $name   The name of partial
     *  @return   object
     */
    public function setContent($name, $view, $data=[])
    {
        $this->_partContents[$name] = [
            'view' => $view,
            'data' => $data,
        ];

        return $this;
    }
    
    /**
     *  Cache (Full) View
     *  
     *  @return object   Method chaining
     */
    public function cache($useCache=true, $filename=false)
    {
        $this->_useCache = $useCache;
        $this->_filename = $filename;
        return $this;
    }
    
    /**
     *  @return object   Method chaining
     */
    public function useTemplate()
    {
        $args = func_get_args();
        $data = [];
        $template = $layout = $module = false;
        
        if(count($args))
        {
            if(is_array($args[0]))
            {
                $data = $args[0];
                // PHP version < 7.x => WTF
                if(isset($args[1])) $layout = $args[1];
                if(isset($args[2])) $module = $args[2];
            }
            
            if(is_string($args[0]))
            {
                $template = $args[0];
                // PHP version < 7.x => WTF
                if(isset($args[1])) $data = $args[1];
                if(isset($args[2])) $layout = $args[2];
                if(isset($args[3])) $module = $args[3];
            }
        }
        
        $this->_template = [$template, $layout, $module, 'data' => $data];
        return $this;
    }
    
    /**
     *  @param  $name  string  (optional) Default null as default content
     *  
     *  @return void
     */
    public function content($name=null)
    {
        if(empty($name))
        {
            if(isset($this->_mainContent))
            {
                list($view, $data) = $this->_mainContent;
                return $this->_view($view, $data, true);
            }
        }
        else
        {
            if(count($this->_partContents) && isset($this->_partContents[$name]))
            {
                $this->_view(
                    $this->_partContents[$name]['view'],
                    $this->_partContents[$name]['data'],
                    true
                );
            }
        }
    }
    
    /**
     *  ....
     *  
     *  @param  bool    $module   (optional) Default false
     *  @return string
     */
    private function _viewPath($module=false)
    {
        $dir = $this->_ci->router->directory;
        $class = $this->_ci->router->class;
        $class= empty($dir) ? $class : $dir . strtolower($class);
        
        if(method_exists($this->_ci->router, 'fetch_module') && !empty($this->_ci->router->fetch_module()))
        {
            $class = explode("/controllers/", $class)[1];
        }
        
        return $class . DIRECTORY_SEPARATOR . $this->_ci->router->method;
    }
    
    /**
     *  @param  string  $view  (optional)
     *  @param  array   $data  (optional)
     *  
     *  @return string
     */
    public function render($view=null, $data=[])
    {
        if($this->_template)
        {
            $file = str_replace(['/', '\\'], '_', $this->_viewPath() . ".php");
            
            if(method_exists($this->_ci->router, 'fetch_module') && !empty($this->_ci->router->fetch_module()))
            {
                $file = $this->_ci->router->fetch_module() . '_' . $file;
            }
            
            if($this->_filename) $file = $this->_filename . '.php';
            
            $viewName = $file;
            
            $file = (is_dir(APPPATH . 'cache/views/') ? APPPATH . 'cache/views/' : APPPATH . 'cache/') . $file;
            
            if($this->_useCache && is_file($file))
            {
                if((time() - filemtime($file)) <= (is_bool($this->_useCache) ? 0 : $this->_useCache))
                {
                    $this->_output($viewName);
                    return;
                }
            }
            
            if(!is_array($view) && empty($view))
            {
                $view = $this->_viewPath();
            }
            else if(is_array($view))
            {
                $data = $view;
                $view = $this->_viewPath();
            }
            
            $this->_mainContent = [$view, $data];

            ob_start();
            $this->_view(
                $this->_viewLayout($this->_template),
                $this->_template['data']
            );
            $output = $this->_simpleParser(ob_get_clean());
            file_put_contents($file, $output);
            
            $this->_output($viewName, $file);
            return;
        }
        
        // SKIP TEMPLATE ...
        if(!is_array($view) && empty($view))
        {
            return $this->_view($this->_viewPath(), $data);
        }
        else if(is_array($view))
        {
            return $this->_view($this->_viewPath(), $view);
        }
        
        return $this->_view($view, $data);
    }

    /**
     *  ...
     *  
     *  @param  string    $view      (required)
     *  @param  array     $data      (optional)
     *  @param  boolean   $optional  (optional)
     *  @return string
     */
    protected function _view($view, $data=[], $optional=false)
    {
        return $this->_ci->load->view($view, $data, $optional);
    }
    
    /**
     *  ...
     *  
     *  @param  string  $view  (required)
     *  @param  string  $file  (required)
     *  @return void
     */
    protected function _output($view, $file)
    {
        $path = APPPATH . 'cache';
        $data = $this->_template['data'];
        
        if(is_dir($path . DIRECTORY_SEPARATOR . 'views'))
        {
            $this->_ci->load
                ->add_package_path($path)
                ->view($view, $data);
            $this->_ci->load->remove_package_path($path);
            return;
        }
        extract($data);
        require $file;
    }
    
    /**
     *  ....
     *  
     *  @param  $template   false   
     *  @param  $layout     false
     *  @param  $module     false
     *  
     *  @return string
     */
    protected function _viewLayout($template=false, $layout=false, $module=false)
    {
        if(is_array($template))
        {
            list($template, $layout, $module) = $template;
        }
        
        $cfg = $this->_cfg;
        
        if(!$template)
        {
            $template = $cfg['templates'][$cfg['default_template']];
        }
        else
        {
            if(isset($cfg['templates'][$template]))
            {
                $template = $cfg['templates'][$template];
            }
            else
                show_error("Template <strong>{$template}</strong> doesn't exists. Please check your view file's config.");
        }
        
        if(!$layout)
        {
            $layout = $cfg['default_layout'];
        }

        $module = !$module ? '' : $module . '/';
        return $module . "_templates/{$template}/_layout/{$layout}";
    }
    
    /**
     *  Widget
     *  
     *  @param  string  $ns  (Required) Namespace without shortname
     *  @return void
     */
    public function widget($ns, $data = [], $default = false)
    {
        $class = $ns . '\\Control';
        class_alias(__NAMESPACE__ . "\\Widget", $ns . '\\Widget');
        
        if($default)
        {
            if(class_exists($class))
                call_user_func_array([new $class, 'view'], [$data]);
            else
                return $default;
        }
        else
            call_user_func_array([new $class, 'view'], [$data]);
    }
    
    /**
     *  @return string
     */
    public function _viewFullPath()
    {
        $module = $this->_ci->router->fetch_module();
        
        list($path, $_view) = \Modules::find($this->_viewPath(), $module, "views" . DIRECTORY_SEPARATOR);
        $_view = !empty($path) ? $path . $_view : $_view;
        
        return $_view;
    }
    
    /**
     *  Simple Parser
     *  
     *  @return string
     */
    private function _simpleParser($text)
    {
        $replacer = [
            '#\{\{#'
                => '<?=',
            '#\{\%#'
                => '<?php',
            '#\}\}#'
                => '?>',
            '#\%\}#'
                => '?>',
            '#\{\!(\s+)macro(\s+)([A-Za-z0-9\_]+)(|\s+)\((.*)\) \!\}#m'
                => "<?php if(! function_exists('macro_$3')){ function macro_$3($5) { ?>",
            '#\{\!(\s+)endmacro(\s+)\!\}#m'
                => "<?php }} ?>",
        ];
        
        return preg_replace(array_keys($replacer), array_values($replacer), $text);
    }
    
    /**
     *  View Partial
     *  
     *  @param   string   $view       null
     *  @param   array    $data       an empty array
     *  @param   boolean  $freekick   false
     *  @return  string
     */
    public function partial($view=null, $data=[], $freekick=false)
    {
        if($freekick)
        {
            return $this->_view($view, $data, true);
        }
        
        $template = $this->_template[0] ?: $this->_cfg['default_template'];
        $template = $this->_cfg['templates'][$template];
        $partial = "_templates/" . $template . "/_partial/{$view}";
        return $this->_view($partial, $data, true);
    }
    
    /**
     *  Setter
     *  
     *  @param  string  $name  Name, required
     *  @param  mixed   $val   Value, required
     *  @return object
     */
    public function __set($name, $val)
    {
        //preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $name);
        $this->data($name)->set($val);
    }
    
    /**
     *  Getter
     *  
     *  @param  string  $name  required
     *  @return object
     */
    public function __get($name)
    {
        return $this->data($name);
    }
    
    /**
     *  Set name
     *  
     *  @param  string  $name  required
     *  @return object
     */
    public function data($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     *  Set Data
     *  
     *  @param  array  $data  Default an empty array
     *  @return void
     */
    public function set($data=[])
    {
        $this->_data[$this->_name] = $data;
    }
    
    /**
     *  Set Default
     */
    public function def($data=[])
    {
        $this->_def[$this->_name] = $data;
        return $this;
    }
    
    /**
     *  Get Data
     */
    public function get()
    {
        return // PHP version < 7.x => WTF
            !is_null($this->_data[$this->_name]) 
            ? $this->_def[$this->_name] 
            : !is_null($this->_def[$this->_name]) 
                ? $this->_def[$this->_name] 
                : null;
    }
    
}
