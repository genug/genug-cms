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

use Closure;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RuntimeException;

use function file_exists;
use function Safe\ob_end_clean;
use function Safe\ob_start;
use function sprintf;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class ConfigLoader implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function populate(object $configObject, string $configFile, mixed ...$param): void
    {
        if (! file_exists($configFile)) {
            throw new RuntimeException(sprintf('Missing config file: %s', $configFile));
        }

        ob_start();
        $func = require_once $configFile;
        ob_end_clean();

        if (! ($func instanceof Closure)) {
            throw new LogicException(sprintf('Config file must return a closure. File: %s', $configFile));
        }

        $func($configObject, ...$param);
    }
}
