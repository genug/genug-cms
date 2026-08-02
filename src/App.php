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
use genug\Page\PageId;
use genug\Router\Router;
use genug\Router\RouterError;
use genug\Setting\Setting;
use Throwable;

use function ob_end_flush;
use function Safe\ob_end_clean;
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
                try {
                    ob_start();

                    $container = new Container(appRoot: self::ROOT);

                    $router = new Router(
                        $container->request,
                        $container->pages,
                        $container->config,
                        $container->logger
                    );

                    $genug = new Api(
                        pages: $container->pages,
                        requestedPage: $router->result(),
                        homePage: $container->pages->fetch(new PageId($container->config->homePageId)),
                        setting: new Setting(
                            new PageId($container->config->homePageId),
                            new PageId($container->config->http404PageId)
                        )
                    );

                    $viewFilePath = $container->config->viewFilePath;

                    header('Content-Type: ' . $container->config->pageContentType);
                    http_response_code(200);
                    if ($genug->requestedPage->id->equals($genug->setting->notFoundPageId)) {
                        http_response_code(404);
                    }
                    /** @psalm-suppress UnusedVariable */
                    (function () use ($genug, $viewFilePath) {
                        /** @psalm-suppress UnresolvableInclude */
                        require_once $viewFilePath;
                    })();
                } catch (RouterError $routerError) {
                    // No 404-page was found to display an "HTTP 404 Not Found" error.

                    while (ob_get_level()) {
                        ob_end_clean();
                    }

                    ob_start();

                    http_response_code(404);
                    echo '404 Not Found';
                }
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
            while (@\ob_end_clean());
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
