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
namespace genug\Setting
{

    if (! \defined(__NAMESPACE__ . '\MAIN_CATEGORY_ID')) {
        
        \define(__NAMESPACE__ . '\MAIN_CATEGORY_ID', 'site');
    }
    
    if (! \defined(__NAMESPACE__ . '\HOMEPAGE_ID')) {
        
        \define(__NAMESPACE__ . '\HOMEPAGE_ID', '/');
    }
    
    if (! \defined(__NAMESPACE__ . '\REQUESTED_PAGE_ID')) {
        
        \define(__NAMESPACE__ . '\REQUESTED_PAGE_ID', (function () {
            $path = \parse_url($_SERVER['REQUEST_URI'], \PHP_URL_PATH);
            $pathBase = (function () {
                $path_base = \dirname($_SERVER['SCRIPT_NAME']);
                if (\strlen($path_base) === 1) {
                    // $path_base is '\' (windows) OR '/' (linux) OR '.'
                    $path_base = '';
                }
                return $path_base;
            })();
            
            if ($pathBase !== '') {
                $pattern = '#^' . \preg_quote(\genug\Api\URL_PATH_BASE, '#') . '#';
                $path = \preg_replace($pattern, '', $path, 1);
            }
            return $path;
        })());
    }
    
    // ---
    
    if (! \defined(__NAMESPACE__ . '\USER_DIR')) {
        
        \define(__NAMESPACE__ . '\USER_DIR', \getcwd() . '/genug_user');
    }
    
    if (! \defined(__NAMESPACE__ . '\CONTENT_DIR')) {
        
        \define(__NAMESPACE__ . '\CONTENT_DIR', namespace\USER_DIR . '/content');
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

    const FILENAME = '_category.' . namespace\FILENAME_EXTENSION;
}
namespace genug\Persistence\FileSystem\Page
{

    const DIR = \genug\Setting\CONTENT_DIR;

    const FILENAME_EXTENSION = 'page';

    const HOMEPAGE_FILENAME = '_home.' . namespace\FILENAME_EXTENSION;
}

/*
 * functions
 */
namespace genug
{

    function autoloader($class)
    {
        if (\strpos($class, __NAMESPACE__) !== 0) {
            return;
        }
        
        $fileName = namespace\CORE_DIR . \DIRECTORY_SEPARATOR . \strtolower($class) . '.php';
        $isProperFileName = (function () use ($fileName) {
            $fileInfo = new \SplFileInfo($fileName);
            return ($fileInfo->isFile() && $fileInfo->isReadable());
        })();
        
        if (! $isProperFileName) {
            return;
        }
        
        include $fileName;
    }
}