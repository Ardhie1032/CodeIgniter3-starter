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
 *  @name        CodeIgniter Assets Library
 *  @version     1.0a1
 *  @author      Ardhie1032 <1032ardhie@gmail.com>
 *  @link        https://github.com/ardhie1032
 *  @license     MIT License Copyright (c) 2016 Ardhie1032
 *  
 */
class Asset {
    
    private $CI;
    
    private $mode = 'dev';
    
    private $_meta = [];
    
    /**
     *  Constructor
     *  
     *  @param   array   $config    (empty array)
     *  @return  void
     */
    public function __construct($config=[])
    {
        $this->CI =& get_instance();
        $config = $this->CI->config->load("assets", true);

        if(!class_exists('MX_Router'))
            $config = $this->CI->config->config['assets'];
        
        $this->template = $config['_template_'];
        unset($config['_template_']);
        $this->assets = $config;
        $this->mode = $config['_mode_'];
    }
    
    /**
     *  
     *  @param   string   $asset   (required)
     *  @param   string   $group   (empty string)
     */
    public function render($asset, $group='')
    {
        $assets = [];
        foreach($this->assets['_group_'][$group] as $gp)
        {
            $assets[$gp] = $this->assets[$gp];
        }
        
        $html = '';
        foreach($assets as $key => $val)
        {
            if(in_array($key, $this->assets['_group_'][$group]))
            {
                if(isset($val[$asset][$this->mode]))
                {
                    $attr = isset($val[$asset]['attr']) ? $val[$asset]['attr'] : [];

                    // Baby slowdown ....
                    $html .= str_replace(
                        [":link:", ":attr:"],
                        [$this->triggerLink($val[$asset][$this->mode]), $this->_attrBuilder($attr)],
                        $this->triggerTemplate($this->template[$asset], $val[$asset], $attr, $this->_attrBuilder($attr))
                    );
                }
            }
        }
        
        return $html;
    }
    
    /**
     *  
     */
    public function update($name, $asset, $data=[])
    {
        $this->assets[$name][$asset] = $data;
        return $this;
    }
    
    /**
     *  
     */
    public function remove($name)
    {
        unset($this->assets[$name]);
        return $this;
    }
    
    /**
     *  @param  string  $url
     */
    public function triggerLink($url)
    {
        /**
         *  @author      Jens Segers
         *  @link        http://www.jenssegers.be
         */
        if (!stristr($url, 'http://') && !stristr($url, 'https://') && substr($url, 0, 2) != '//') {
            $url = $this->CI->config->item('base_url') . $url;
        }
        
        return htmlspecialchars(strip_tags($url));
    }
    
    /**
     *  
     *  @return string
     */
    public function triggerTemplate($tpl, $asset, $attr=[], $attrStr='')
    {
        if(is_object($tpl))
            return $tpl($asset, $attr, $attrStr);
        else
            return $tpl;
    }
    
    /**
     *  Set environment
     *  
     *  @param   string  $mode  (required)
     *  @return  object
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     *  Set/Add Meta
     *  (method chaining)
     *  
     *  @param   string    $name   (required)
     *  @param   string    $name   (empty string)
     *  @param   array     $name   (empty array)
     *  @return object
     */
    public function meta($name, $content='', $attr=[])
    {
        if(is_array($name))
        {
            $this->_meta[] = $name;
        }
        else
        {
            $this->_meta[] = array_merge([
                'name' => $name,
                'content' => $content,
            ], $attr);
        }
        
        return $this;
    }
    
    /**
     *  
     *  @param   array    $attr
     *  @return  string    
     */
    private function _attrBuilder($attrs=[])
    {
        $singleAttr = [];
        $attr = [];
        
        foreach($attrs as $name => $val)
        {
            if(is_numeric($name))
                $singleAttr[] = $val;
            else
                $attr[] = htmlentities(strip_tags($name)).'="'.htmlspecialchars(strip_tags($val)).'"';
        }
        
        return (!empty($attr) ? " " . implode(" ", $attr) : "")
            . (!empty($singleAttr) ? " " . implode(" ", $singleAttr) : "");
    }
    
    /**
     *  
     *  @return string
     */
    public function getMeta()
    {
        $tag = '';
        foreach($this->_meta as $key => $attr)
        {
            if(is_numeric($key))
                $tag .= '<meta'.$this->_attrBuilder($attr).">\n";
        }
        
        return $tag;
    }
    
}

/*
$config['bootstrap']['css'] = [
    'dev' => '/assets/vendor/bootstrap/bootstrap.min.css',
    'pro' => 'http://bootstrapcdn.com/bootstrap.min.css',
];

$config['bootstrap']['js'] = [
    'dev' => '/assets/vendor/bootstrap/bootstrap.min.js',
    'pro' => '',
    'attr' => ["other_attribute" => "..."],
];

$config['jquery']['js'] = [
    'dev' => '/assets/vendor/jquery/jquery.min.js',
    'pro' => '',
];

$config['_template_']['css'] = '<link rel="stylesheet" type="text/css" href=":link:":attr:>'."\n";

$config['_template_']['js'] = '<script src=":link:":attr:></script>'."\n";

// OR ----
$config['_template_']['js'] = function($data, $attr, $str) {
    return "<script src=\"$data[dev]\"$str></script>\n";
};

$asset = new Asset($config);

$asset->meta('author', 'Ardhie1032');
$asset->meta('author', 'Ardhie1032', ['name' => 'value']);
$asset->meta([
    'name' => 'description',
    'content' => 'My Web',
    'disabled',
    'cool'
]);

echo $asset->render('js');

echo $asset->getMeta();


*/
