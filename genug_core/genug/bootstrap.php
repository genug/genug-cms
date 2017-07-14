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
namespace genug\Api
{

    \define(__NAMESPACE__ . '\URL_PATH_BASE', (function () {
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

    if (! \defined(__NAMESPACE__ . '\MAIN_CATEGORY_ID')) {
        
        \define(__NAMESPACE__ . '\MAIN_CATEGORY_ID', 'site');
    }
    
    if (! \defined(__NAMESPACE__ . '\HOMEPAGE_ID')) {
        
        \define(__NAMESPACE__ . '\HOMEPAGE_ID', '/');
    }
    
    // ---
    
    if (! \defined(__NAMESPACE__ . '\USER_DIR')) {
        
        \define(__NAMESPACE__ . '\USER_DIR', \getcwd() . '/genug_user');
    }
    
    if (! \defined(__NAMESPACE__ . '\CONTENT_DIR')) {
        
        \define(__NAMESPACE__ . '\CONTENT_DIR', USER_DIR . '/content');
    }
    
    if (! \defined(__NAMESPACE__ . '\TEMPLATE_DIR')) {
        
        \define(__NAMESPACE__ . '\TEMPLATE_DIR', USER_DIR . '/template');
    }
}
namespace genug
{

    \define(__NAMESPACE__ . '\CORE_DIR', \dirname(__DIR__));
}
namespace genug\Persistence\FileSystem\Category
{

    const DIR = \genug\Setting\CONTENT_DIR;

    const FILENAME_EXTENSION = 'genug';

    const FILENAME = '_category.' . FILENAME_EXTENSION;
}
namespace genug\Persistence\FileSystem\Page
{

    const DIR = \genug\Setting\CONTENT_DIR;

    const FILENAME_EXTENSION = 'page';

    const HOMEPAGE_FILENAME = '_home.' . FILENAME_EXTENSION;
}

/*
 * autoload
 */
namespace 
{

    set_include_path(get_include_path() . PATH_SEPARATOR . genug\CORE_DIR);
    
    spl_autoload_register();
}