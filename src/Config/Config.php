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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final readonly class Config
{
    public function __construct(
        // TODO Simple FileLogger('php://stderr') implementation
        // TODO Remove Monolog dependency
        public LoggerInterface $logger = new NullLogger(),
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
