<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SEB
 * Date: 13/09/13
 * Time: 14:21
 * To change this template use File | Settings | File Templates.
 */

function simulacre_autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName = dirname(__DIR__).DIRECTORY_SEPARATOR;
    $fileName  .= 'src'.DIRECTORY_SEPARATOR.'classes'. DIRECTORY_SEPARATOR;
    //$namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if(file_exists($fileName)){
        include_once $fileName;
    } else {
        throw new Exception('The file '. $fileName. ' does not exits');
    }

}

spl_autoload_register('simulacre_autoload');