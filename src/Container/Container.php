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

use genug\Config\Config;
use genug\Config\ConfigLoader;
use genug\Logger\Logger;
use genug\Page\PageRepository;
use genug\Router\Router;
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

    public private(set) Router $router {
        get => $this->router ?? (function (): Router {
            $r = new Router($this->config);
            $r->setLogger($this->logger);
            return $r;
        })();
    }

    public private(set) LoggerInterface $logger {
        get => $this->logger ??= new Logger($this->config->logger);
    }

    public private(set) PageRepository $pages {
        get => $this->pages ??= (function () {
            $pages = new PageRepository($this->config);
            $pages->setLogger($this->config->logger);
            return $pages;
        })();
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
