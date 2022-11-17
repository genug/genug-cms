<?php
/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

(function () {
    try {
        \ob_start();

        require_once __DIR__ . '/genug_core/genug/bootstrap.php';

        spl_autoload_register('\genug\autoloader');

        try {
            \header('Content-Type: ' . \genug\Setting\CONTENT_TYPE);
            \http_response_code(200);

            \genug\Api::requestedPage();

            if (\genug\Api::requestedPage()->id()->__toString() === \genug\Setting\HTTP_404_PAGE_ID) {
                \http_response_code(404);
            }

            require_once \genug\Setting\VIEW_INDEX_FILE;
        } catch (\genug\throwable_RequestedPageNotFound $t) {
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