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

    public function __construct(
        private string $appRoot,
    ) {
    }
}
