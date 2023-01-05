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

use function dirname;

use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOL;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Log
{
    /** @var array<string, LoggerInterface&MonologLogger> */
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
        /* workaround psalm: ERROR: MixedArgument - Argument 2 of filter_var cannot be mixed, expecting int */
        $filter = (int) FILTER_VALIDATE_BOOL;

        $debugEnvValueString = getenv('GENUG_DEBUG', true) ?: Preset::GENUG_DEBUG->value;
        /** @var null|bool */
        $isDebugOrNullOnFailure = filter_var($debugEnvValueString, $filter, FILTER_NULL_ON_FAILURE);

        $debugLogFilePath = getenv('GENUG_DEBUG_LOGFILE', true) ?: Preset::GENUG_DEBUG_LOGFILE->value;

        $logger = new MonologLogger($name);
        $level = Level::Warning;
        if ((bool) $isDebugOrNullOnFailure) {
            $level = Level::Debug;
        }
        $logger->pushHandler(new StreamHandler('php://stderr', $level));

        if ((bool) $isDebugOrNullOnFailure) {
            $logger->pushHandler(new StreamHandler(dirname(__DIR__).'/'.$debugLogFilePath, Level::Debug));
        }
        return $logger;
    }
}
