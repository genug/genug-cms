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

use genug\Environment\Environment;
use genug\Group\ {
    Repository as GroupRepository,
};
use genug\Lib\EntityCache;
use genug\Page\ {
    Repository as PageRepository,
};
use genug\Request\Request;
use genug\Router\Router;
use genug\Router\RouterError;
use genug\Setting\Setting;
use Throwable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class App
{
    public static function run(): never
    {
        try {
            ob_start();

            $environment = new Environment(
                dirname(__DIR__).'/',
                Log::instance('genug_environment')
            );
            $request = new Request();
            $entityCache = new EntityCache();

            $pages = new PageRepository(
                $entityCache,
                $environment,
                Log::instance('genug_page')
            );
            $router = new Router(
                $request,
                $pages,
                $environment,
                Log::instance('genug_router')
            );

            // ---

            $genug = new Api(
                pages: $pages,
                requestedPage: $router->result(),
                homePage: $pages->fetch((string) $environment->homePageId()),
                groups: new GroupRepository(
                    $entityCache,
                    $environment,
                    Log::instance('genug_group')
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
