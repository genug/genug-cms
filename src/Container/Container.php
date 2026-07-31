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
use genug\Config\ConfigLoader;
use genug\Environment\EnvironmentConfigurated;
use genug\Environment\EnvironmentInterface;
use genug\Logger\Logger;
use genug\Request\Request;
use Psr\Log\LoggerInterface;

use function file_exists;
use function getenv;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Container
{
    public private(set) Config $config;

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

    private ConfigLoader $configLoader {
        get => $this->configLoader ??= new ConfigLoader();
    }

    public function __construct(
        private string $appRoot,
    ) {
        $this->config = (function (): Config {
            $config = new Config();
            $configDir = getenv('GENUG_CONF_DIR') ?: $this->appRoot . '/config';
            $configFile = $configDir . '/genug.conf.php';
            if (! file_exists($configFile)) {
                $configFile = $configDir . '/genug.conf.dist.php';
            }

            if (file_exists($configFile)) {
                $this->configLoader->populate($config, $configFile);
            }

            return $config;
        })();
    }
}
