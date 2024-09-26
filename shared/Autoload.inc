<?php

//saw this on stackOverflow, it may be useful later on

namespace NBA\Shared;

abstract class Autoload
{
    /**
     * Requires files based on class name.
     *
     * @param string $name Name of class, including namespace.
     *
     * @return boolean True if successful.
     */
    private static function autoloadClass(string $name)
    {
        if (substr_compare($name, 'NBA\shared', 0, 10) === 0) {
            $path = substr($name, 4);
        } else {
            // // Replace the 'NBA\' portion of the namespace with a slash and continue
            $path = preg_replace('/NBA\\\.*?\\\/', '\\', $name);
        }

        $path       = str_replace('\\', '/', $path);
        $pathEnd    = strrpos($path, '/');
        $className  = substr($path, $pathEnd);
        $path       = substr($path, 0, $pathEnd);
        $path       = strtolower(preg_replace('/((?<!^)(?<!\/))[A-Z]/', '-$0', $path));
        $className .= '.inc';

        $file = static::findFile($path.$className, 0);

        if ($file) {
            include $file;
            return true;
        } else {
            return false;
        }

    }//end autoloadClass()


    /**
     * Registers autoload function in PHP.
     *
     * @return void
     */
    public static function register()
    {
        //https://www.php.net/manual/en/function.spl-autoload-register.php
        spl_autoload_register([__CLASS__, 'autoloadClass']);

    }


}//end Autoload class
