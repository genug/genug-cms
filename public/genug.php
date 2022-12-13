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

use const genug\Setting\ {
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

        $genug = (function () {
            $entityCache = new EntityCache();

            $pages = new PageRepository($entityCache);
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
            $groups = new GroupRepository($entityCache);
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
        throw $t;
    } catch (\Throwable $t) {
        \ob_clean();
        \http_response_code(500);

        echo '500 Internal Server Error';
        throw $t;
    } finally {
        \ob_end_flush();
    }
})();
