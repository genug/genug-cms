<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug;

use genug\Environment\Preset;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Log
{
    protected static array $instances = [];

    public static function instance(string $name): LoggerInterface&MonologLogger
    {
        if (! isset(static::$instances[$name])) {
            static::$instances[$name] = static::instantiateLogger($name);
        }
        return static::$instances[$name];
    }

    protected function __construct()
    {
    }

    protected static function instantiateLogger(string $name): MonologLogger
    {
        $debugEnvValueString = getenv('GENUG_DEBUG', true) ?: Preset::GENUG_DEBUG->value;
        $isDebugOrNullOnFailure = filter_var($debugEnvValueString, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        $debugLogFilePath = getenv('GENUG_DEBUG_LOGFILE', true) ?: Preset::GENUG_DEBUG_LOGFILE->value;

        $logger = new MonologLogger($name);
        $level = Level::Warning;
        if ((bool) $isDebugOrNullOnFailure) {
            $level = Level::Debug;
        }
        $logger->pushHandler(new StreamHandler('php://stderr', $level));

        if ((bool) $isDebugOrNullOnFailure) {
            $logger->pushHandler(new StreamHandler($debugLogFilePath, Level::Debug));
        }
        return $logger;
    }
}
