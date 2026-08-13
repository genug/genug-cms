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

namespace genug;

use ErrorException;
use genug\Container\Container;
use Throwable;

use function ob_end_clean;
use function ob_end_flush;
use function Safe\ob_start;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class App
{
    public const string ROOT = __DIR__ . '/..';

    public static function run(): never
    {
        try {
            try {
                ob_start();
                $container = new Container(appRoot: self::ROOT);
                $responce = $container->router->dispatch();

                http_response_code($responce->status->value);
                // FIXME The charset must be set dynamically
                header('Content-Type: ' . $responce->contentType->value . '; charset=UTF-8');
                echo $responce->body;
            } catch (Throwable $throwable) {
                try {
                    $container ??= null;
                    $container?->logger->critical(
                        'Genug cms has failed.',
                        ['exception' => $throwable]
                    );
                } catch (Throwable) {
                    // genug cms is too broken to write to its own log
                }

                throw new ErrorException('Genug cms has failed.', previous: $throwable);
            }
        } catch (Throwable $error) {
            // @phpstan-ignore theCodingMachineSafe.function
            while (@ob_end_clean());
            // @phpstan-ignore theCodingMachineSafe.function
            \ob_start();

            http_response_code(500);
            echo '500 Internal Server Error';

            throw $error;
        } finally {
            // @phpstan-ignore theCodingMachineSafe.function
            while (@ob_end_flush());
        }
        exit;
    }
}
