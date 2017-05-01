<?php
declare(strict_types = 1);

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

/*
 * constants
 */
namespace genug
{

    \define('genug\CORE_DIR', \dirname(__DIR__));
    
    \define('genug\CWD', \getcwd());
    
    \define('genug\URL_PATH_BASE', (function () {
        $path_base = \dirname($_SERVER['SCRIPT_NAME']);
        if (\strlen($path_base) === 1) {
            // $path_base is '\' (windows) OR '/' (linux) OR '.'
            $path_base = '';
        }
        return $path_base;
    })());
}
namespace genug\Setting
{

    const MAIN_CATEGORY_ID = 'site';
}
namespace genug\Persistence\FileSystem\Category
{

    const DIR = \genug\CWD . '/content';

    const FILENAME_EXTENSION = 'genug';

    const FILENAME = '_category.' . FILENAME_EXTENSION;
}

/*
 * autoload
 */
namespace 
{

    set_include_path(get_include_path() . PATH_SEPARATOR . genug\CORE_DIR);
    
    spl_autoload_register();
}