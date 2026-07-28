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

namespace genug\Environment;

use Deprecated;
use genug\Page\AbstractId as AbstractPageId;
use genug\Page\Id as PageId;
use Psr\Log\LoggerInterface;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class EnvironmentConfigurated implements EnvironmentInterface
{
    private array $instances = [];

    #[Deprecated('Use Config instead.')]
    public function __construct(
        protected readonly \genug\Config\Config $config,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function isDebug(): bool
    {
        return $this->config->debug;
    }

    public function debugLogFilePath(): string
    {
        return $this->config->logFilePath;
    }

    public function pageContentType(): string
    {
        return $this->config->pageContentType;
    }

    public function homePageId(): AbstractPageId
    {
        return $this->instances[__METHOD__] ??= new PageId($this->config->homePageId);
    }

    public function http404PageId(): AbstractPageId
    {
        return $this->instances[__METHOD__] ??= new PageId($this->config->http404PageId);
    }

    public function viewFilePath(): string
    {
        return $this->config->viewFilePath;
    }

    public function persistenceContentDirectory(): string
    {
        return $this->config->persistenceContentDirectory;
    }

    public function persistenceGroupFilename(): string
    {
        return $this->config->persistenceGroupFilename;
    }

    // FIXME: typo
    public function persistencePageFilenameExtension(): string
    {
        return $this->config->persistencePageFilenameExtension;
    }

    public function persistencePageHomePageFilename(): string
    {
        return $this->config->persistencePageHomePageFilename;
    }
}
