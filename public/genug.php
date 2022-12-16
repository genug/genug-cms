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

use genug\Api as GenugApi;
use genug\Environment\Environment;
use genug\Group\ {
    Repository as GroupRepository,
};
use genug\Lib\EntityCache;
use genug\Log;
use genug\Page\ {
    Repository as PageRepository,
};
use genug\Request\Request;
use genug\Router\Router;
use genug\Router\RouterError;
use genug\Setting\Setting;

use const genug\Setting\CONTENT_TYPE;
use const genug\Setting\VIEW_INDEX_FILE;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
(function () {
    try {
        ob_start();

        require_once dirname(__DIR__) . '/vendor/autoload.php';

        require_once dirname(__DIR__) . '/src/Bootstrap.php';

        $environment = new Environment(Log::instance('genug_environment'));
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

        $genug = new GenugApi(
            pages: $pages,
            requestedPage: $router->result(),
            homePage: $pages->fetch((string) $environment->homePageId()),
            groups: new GroupRepository(
                $entityCache,
                Log::instance('genug_group')
            ),
            setting: new Setting(
                $environment->homePageId(),
                $environment->http404PageId(),
                $environment->mainGroupId()
            )
        );

        (function () use ($genug) {
            header('Content-Type: ' . CONTENT_TYPE);
            http_response_code(200);
            if ($genug->requestedPage->id->equals($genug->setting->notFoundPageId)) {
                http_response_code(404);
            }
            require_once VIEW_INDEX_FILE;
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
    }
})();
