<?php namespace Ardhie1032\CodeIgniter3Lib\Core;

class Autoloader
{
    protected $prefixes = array();
	protected $classmap = array();

    public function addNamespaces($class = [])
    {
        foreach($class as $prefix => $base_dir)
        {
            $prefix = trim($prefix, '\\') . '\\';
            $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';
            
            if (isset($this->prefixes[$prefix]) === false)
                $this->prefixes[$prefix] = array();

            array_push($this->prefixes[$prefix], $base_dir);
        }
        return $this;
    }
    
    public function register()
    {
        $this->load('loadClasses');
        $this->load('loadNsClasses');
    }

    private function load($method)
    {
		spl_autoload_register(array($this, $method), false, true);
    }

    public function loadNsClasses($class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\'))
        {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);
            $mapped_file = $this->loadMappedFileNS($prefix, $relative_class);
            if ($mapped_file) return $mapped_file;
            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    protected function loadMappedFileNS($prefix, $relative_class)
    {
        if (isset($this->prefixes[$prefix]) === false) return false;
        
        foreach ($this->prefixes[$prefix] as $base_dir)
        {
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            if ($this->requireFile($file)) return $file;
        }

        return false;
    }

    protected function requireFile($file)
    {
        if(file_exists($file))
        {
            require $file;
            return true;
        }
        return false;
    }

	public function addClasses($classmap = [])
	{
        $this->classmap = $classmap;
        return $this;
	}

	protected function loadClasses($class)
	{
		if (array_key_exists($class, $this->classmap) && $this->requireFile($this->classmap[$class]))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}
