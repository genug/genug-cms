<?php

declare(strict_types=1);

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
    if (! \defined(__NAMESPACE__ . '\CONTENT_TYPE')) {
        \define(__NAMESPACE__ . '\CONTENT_TYPE', 'text/html; charset=UTF-8');
    }

    if (! \defined(__NAMESPACE__ . '\MAIN_GROUP_ID')) {
        \define(__NAMESPACE__ . '\MAIN_GROUP_ID', 'site');
    }

    if (! \defined(__NAMESPACE__ . '\HOME_PAGE_ID')) {
        \define(__NAMESPACE__ . '\HOME_PAGE_ID', '/');
    }

    if (! \defined(__NAMESPACE__ . '\HTTP_404_PAGE_ID')) {
        \define(__NAMESPACE__ . '\HTTP_404_PAGE_ID', '/http-404');
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
                $pattern = '#^' . \preg_quote($pathBase, '#') . '#';
                $path = \preg_replace($pattern, '', $path, 1);
            }
            return $path;
        })());
    }

    // ---

    if (! \defined(__NAMESPACE__ . '\USER_DIR')) {
        \define(__NAMESPACE__ . '\USER_DIR', \dirname(\getcwd()) . '/genug_user');
    }

    if (! \defined(__NAMESPACE__ . '\CONTENT_DIR')) {
        \define(__NAMESPACE__ . '\CONTENT_DIR', namespace\USER_DIR . '/content');
    }

    if (! \defined(__NAMESPACE__ . '\VIEW_DIR')) {
        \define(__NAMESPACE__ . '\VIEW_DIR', namespace\USER_DIR . '/view');
    }

    if (! \defined(__NAMESPACE__ . '\VIEW_INDEX_FILE')) {
        \define(__NAMESPACE__ . '\VIEW_INDEX_FILE', namespace\VIEW_DIR . '/index.php');
    }
}

namespace genug
{
    \define(__NAMESPACE__ . '\CORE_DIR', __DIR__);
}

namespace genug\Persistence\FileSystem\Group
{
    const DIR = \genug\Setting\CONTENT_DIR;

    const FILENAME_EXTENSION = 'genug';

    const FILENAME = '_group.' . namespace\FILENAME_EXTENSION;
}

namespace genug\Persistence\FileSystem\Page
{
    const DIR = \genug\Setting\CONTENT_DIR;

    const FILENAME_EXTENSION = 'page';

    const HOME_PAGE_FILENAME = '_home.' . namespace\FILENAME_EXTENSION;
}
