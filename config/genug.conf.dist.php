<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

use genug\Config\Config;
use genug\Logger\FileLogger;
use Psr\Log\LogLevel;

use function Safe\php_sapi_name;

/*
 * IMPORTANT: This file may be overwritten during updates!
 * Instead of modifying this file, create a file named `genug.conf.php` in the same directory.
 * genug cms will then ignore `genug.conf.dist.php` and use the settings from `genug.conf.php`.
 *
 * NOTE: You can use the `GENUG_CONF_DIR` environment variable to specify a different directory for configuration files.
 */
return static function (Config $config) {
    /*
     * The site title.
     * Is appended to the page title.
     * Can be left blank.
     */
    $config->siteTitle = 'SITE TITLE';

    /*
     * The separator between the site title and the page title.
     * Displayed only if both titles are available.
     */
    $config->titleDelimiter = '|';

    /*
     * During development, PHP's built-in web server can be used via the CLI.
     * If this is the case, the configuration will be adjusted accordingly.
     * See also: https://www.php.net/manual/en/features.commandline.webserver.php
     */
    if (php_sapi_name() === 'cli-server') {
        // Retrieve all log messages during development
        $config->logger = new FileLogger('php://stderr', LogLevel::DEBUG);

        $config->host = \is_string($_SERVER['SERVER_NAME'] ?? null) ? $_SERVER['SERVER_NAME'] : '';
        $config->port = \is_string($_SERVER['SERVER_PORT'] ?? null) ? \intval($_SERVER['SERVER_PORT']) : null;
    }
};
