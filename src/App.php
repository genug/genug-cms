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

use genug\Container\Container;
use genug\Group\Repository as GroupRepository;
use genug\Lib\EntityCache;
use genug\Page\Repository as PageRepository;
use genug\Router\Router;
use genug\Router\RouterError;
use genug\Setting\Setting;
use Throwable;

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
            ob_start();

            $container = new Container(appRoot: self::ROOT);

            $environment = $container->environment;
            $entityCache = new EntityCache();

            $pages = new PageRepository(
                $entityCache,
                $environment,
                $container->logger
            );
            $router = new Router(
                $container->request,
                $pages,
                $environment,
                $container->logger
            );

            // ---

            $genug = new Api(
                pages: $pages,
                requestedPage: $router->result(),
                homePage: $pages->fetch((string) $environment->homePageId()),
                groups: new GroupRepository(
                    $entityCache,
                    $environment,
                    $container->logger
                ),
                setting: new Setting(
                    $environment->homePageId(),
                    $environment->http404PageId()
                )
            );

            $viewFilePath = $environment->viewFilePath();

            header('Content-Type: ' . $environment->pageContentType());
            http_response_code(200);
            if ($genug->requestedPage->id->equals($genug->setting->notFoundPageId)) {
                http_response_code(404);
            }
            /** @psalm-suppress UnusedVariable */
            (function () use ($genug, $viewFilePath) {
                /** @psalm-suppress UnresolvableInclude */
                require_once $viewFilePath;
            })();
        } catch (RouterError $t) {
            ob_clean();
            http_response_code(404);

            echo '404 Not Found';
            Log::instance('genug_core')->error(
                'No page was found to display an "HTTP 404 Not Found" error.',
                ['throwable' => $t]
            );
        } catch (Throwable $t) {
            ob_clean();
            http_response_code(500);

            echo '500 Internal Server Error';
            Log::instance('genug_core')->alert(
                'Fatal Error.',
                ['throwable' => $t]
            );
        } finally {
            ob_end_flush();
            exit;
        }
    }
}
