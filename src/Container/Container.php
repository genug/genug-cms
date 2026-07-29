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

namespace genug\Container;

use Deprecated;
use genug\Config\Config;
use genug\Environment\EnvironmentConfigurated;
use genug\Environment\EnvironmentInterface;
use genug\Logger\Logger;
use genug\Request\Request;
use Psr\Log\LoggerInterface;

use function file_exists;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Container
{
    public private(set) Config $config {
        get => $this->config ??= (function (): Config {
            $configFile = $this->appRoot . '/genug.config.php';
            if (file_exists($configFile)) {
                return require_once $configFile;
            }
            return new Config();
        })();
    }

    /**
     * @deprecated Use Config instead.
     */
    public private(set) EnvironmentInterface $environment {
        #[Deprecated('Use Config instead.')]
        get => $this->environment ??= new EnvironmentConfigurated(
            $this->config,
            $this->logger
        );
    }

    public private(set) Request $request {
        get => $this->request ??= new Request($this->config->pathBase);
    }

    public private(set) LoggerInterface $logger {
        get => $this->logger ??= new Logger($this->config->logger);
    }

    private array $loggerInstances = [];

    public function __construct(
        private string $appRoot,
    ) {
    }
}
