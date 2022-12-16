<?php

declare(strict_types=1);

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

use genug\Api as GenugApi;
use genug\Environment\Environment;
use genug\RequestedPageNotFound;
use genug\Group\ {
    Repository as GroupRepository,
};
use genug\Page\ {
    EntityNotFound as PageEntityNotFound,
    Repository as PageRepository,
};
use genug\Setting\Setting;
use genug\Lib\EntityCache;
use genug\Log;
use genug\Request\Request;

use const genug\Setting\ {
    CONTENT_TYPE,
    VIEW_INDEX_FILE
};

(function () {
    try {
        \ob_start();

        require_once dirname(__DIR__) . '/vendor/autoload.php';

        require_once dirname(__DIR__) . '/src/Bootstrap.php';

        $genug = (function (): GenugApi {
            $entityCache = new EntityCache();
            $environment = new Environment(Log::instance('genug_environment'));
            $request = new Request();

            $pages = new PageRepository(
                $entityCache,
                $environment,
                Log::instance('genug_page')
            );
            $requestedPage = (function () use ($pages, $environment, $request) {
                try {
                    return $pages->fetch($request->pageId());
                } catch (PageEntityNotFound $t) {
                    try {
                        return $pages->fetch((string) $environment->http404PageId());
                    } catch (PageEntityNotFound $t) {
                        throw new RequestedPageNotFound(previous: $t);
                    }
                }
            })();
            $groups = new GroupRepository($entityCache, Log::instance('genug_group'));
            $homePage = $pages->fetch((string) $environment->homePageId());

            return new GenugApi(
                $pages,
                $requestedPage,
                $homePage,
                $groups,
                new Setting(
                    $environment->homePageId(),
                    $environment->http404PageId(),
                    $environment->mainGroupId()
                )
            );
        })();

        \header('Content-Type: ' . CONTENT_TYPE);
        \http_response_code(200);
        if ($genug->requestedPage->id->equals($genug->setting->notFoundPageId)) {
            \http_response_code(404);
        }
        (function () use ($genug) {
            require_once VIEW_INDEX_FILE;
        })();
    } catch (RequestedPageNotFound $t) {
        \ob_clean();
        \http_response_code(404);

        echo '404 Not Found';
        Log::instance('genug_core')->error(
            'No page was found to display an "HTTP 404 Not Found" error.',
            ['throwable' => $t]
        );
    } catch (\Throwable $t) {
        \ob_clean();
        \http_response_code(500);

        echo '500 Internal Server Error';
        Log::instance('genug_core')->alert(
            'Fatal Error.',
            ['throwable' => $t]
        );
    } finally {
        \ob_end_flush();
    }
})();
