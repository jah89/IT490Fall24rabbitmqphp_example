<?php

//saw this on stackOverflow, it may be useful later on

namespace NBA\Frontend\Shared;

abstract class Autoload
{

    /**
     * Recursively goes up a path until a file is found.
     * Up to 10 times.
     *
     * @param string  $path      Relative path to find file for, with leading "/".
     * @param integer $iteration Current iteration, will stop once above 9.
     *
     * @return false|string String of file, false if not found.
     */
    private static function findFile(string $path, int $iteration)
    {
        if (php_sapi_name() === 'cli') {
            $currentDir = dirname($_SERVER['PHP_SELF']);
        } else {
            $currentDir = $_SERVER['DOCUMENT_ROOT'];
        }

        if (file_exists($currentDir.$path)) {
            return $currentDir.$path;
        } else if ($iteration > 9) {
            return false;
        } else {
            return static::findFile('/..'.$path, ++$iteration);
        }

    }//end findFile()

    
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
            $path = substr($name, 3);
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
