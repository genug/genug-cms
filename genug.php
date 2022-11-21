<?php

declare(strict_types=1);

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

use genug\Api as GenugApi;
use genug\throwable_RequestedPageNotFound;
use genug\Category\ {
    Repository as CategoryRepository,
};
use genug\Page\ {
    throwable_EntityNotFound as throwable_PageEntityNotFound,
    Repository as PageRepository,
};

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

        require_once __DIR__ . '/genug_core/genug/bootstrap.php';

        spl_autoload_register('\genug\autoloader');

        try {
            $genug = (function () {
                $pages = new PageRepository();
                $requestedPage = (function () use ($pages) {
                    try {
                        return $pages->fetch(REQUESTED_PAGE_ID);
                    } catch (throwable_PageEntityNotFound $t) {
                        try {
                            return $pages->fetch(HTTP_404_PAGE_ID);
                        } catch (throwable_PageEntityNotFound $t) {
                            throw new throwable_RequestedPageNotFound(previous: $t);
                        }
                    }
                })();
                $categories = new CategoryRepository();
                $homePage = $pages->fetch(HOME_PAGE_ID);
                
                return new GenugApi(
                    $pages,
                    $requestedPage,
                    $homePage,
                    $categories
                );
            })();

            \header('Content-Type: ' . CONTENT_TYPE);
            \http_response_code(200);
            if ($genug->requestedPage->id->__toString() === HTTP_404_PAGE_ID) {
                \http_response_code(404);
            }
            require_once VIEW_INDEX_FILE;
        } catch (throwable_RequestedPageNotFound $t) {
            \ob_clean();
            \http_response_code(404);

            echo '404 Not Found';
        }
    } catch (\Throwable $t) {
        \ob_clean();
        \http_response_code(500);

        echo '500 Internal Server Error';
        throw $t;
    } finally {
        \ob_end_flush();
    }
})();
