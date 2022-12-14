<?php

declare(strict_types=1);

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

use genug\Api as GenugApi;
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
use Monolog\Handler\StreamHandler;
use Monolog\{
    Level,
    Logger,
};

use const genug\Setting\ {
    DEBUG_MODE,
    DEBUG_LOG_FILE,
    HOME_PAGE_ID,
    REQUESTED_PAGE_ID,
    HTTP_404_PAGE_ID,
    CONTENT_TYPE,
    VIEW_INDEX_FILE
};

(function () {
    try {
        \ob_start();

        require_once dirname(__DIR__) . '/vendor/autoload.php';

        require_once dirname(__DIR__) . '/src/Bootstrap.php';

        $genugLogger = (function (): Logger {
            $logger = new Logger('genug_user');
            $logger->pushHandler(new StreamHandler('php://stderr', Level::Warning));

            if (DEBUG_MODE) {
                $logger->pushHandler(new StreamHandler(DEBUG_LOG_FILE, Level::Debug));
            }
            return $logger;
        })();

        $genug = (function () use ($genugLogger) {
            $logger = $genugLogger->withName('genug_core');

            $entityCache = new EntityCache();

            $pages = new PageRepository($entityCache, $logger);
            $requestedPage = (function () use ($pages) {
                try {
                    return $pages->fetch(REQUESTED_PAGE_ID);
                } catch (PageEntityNotFound $t) {
                    try {
                        return $pages->fetch(HTTP_404_PAGE_ID);
                    } catch (PageEntityNotFound $t) {
                        throw new RequestedPageNotFound(previous: $t);
                    }
                }
            })();
            $groups = new GroupRepository($entityCache, $logger);
            $homePage = $pages->fetch(HOME_PAGE_ID);

            return new GenugApi(
                $pages,
                $requestedPage,
                $homePage,
                $groups,
                new Setting()
            );
        })();

        \header('Content-Type: ' . CONTENT_TYPE);
        \http_response_code(200);
        if ($genug->requestedPage->id->equals($genug->setting->notFoundPageId)) {
            \http_response_code(404);
        }
        require_once VIEW_INDEX_FILE;
    } catch (RequestedPageNotFound $t) {
        \ob_clean();
        \http_response_code(404);

        echo '404 Not Found';
        $genugLogger->error(
            'No page was found to display an "HTTP 404 Not Found" error.',
            ['throwable' => $t]
        );
    } catch (\Throwable $t) {
        \ob_clean();
        \http_response_code(500);

        echo '500 Internal Server Error';
        $genugLogger->alert(
            'Fatal Error.',
            ['throwable' => $t]
        );
    } finally {
        \ob_end_flush();
    }
})();
