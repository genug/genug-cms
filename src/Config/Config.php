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

namespace genug\Config;

use genug\App;
use genug\Logger\FileLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Config
{
    public function __construct(
        public LoggerInterface $logger = new FileLogger('php://stderr', LogLevel::ERROR),
        public string $pageContentType = 'text/html; charset=UTF-8',
        public string $pathBase = '',
        public string $homePageId = '/',
        public string $http404PageId = '/http-404',
        public string $viewFilePath = App::ROOT . '/genug_user/view/index.php',
        public string $persistenceContentDirectory = App::ROOT . '/genug_user/content',
        public string $persistenceGroupFilename = '_group.genug',
        public string $persistencePageFilenameExtension = 'page',
        public string $persistencePageHomePageFilename = '_home.page'
    ) {
    }
}
