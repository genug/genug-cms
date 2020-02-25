<?php
/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

(function () {
    try {
        \ob_start();

        (function (){
            $bootstrapFile = __DIR__ . '/genug_core/genug/bootstrap.php';
            if (! \is_readable($bootstrapFile)) {
                throw new \Error("Failed opening required '" . $bootstrapFile . "'");
            }
            require_once $bootstrapFile;
        })();

        spl_autoload_register('\genug\autoloader');

        \header('Content-Type: ' . \genug\Setting\CONTENT_TYPE);
        \http_response_code(200);

        try {
            \genug\Api::requestedPage();

            if (! \is_readable(\genug\Setting\VIEW_INDEX_FILE)) {
                throw new \Error("Failed opening required '" . \genug\Setting\VIEW_INDEX_FILE . "'");
            }
            require_once \genug\Setting\VIEW_INDEX_FILE;
        } catch (\genug\throwable_RequestedPageNotFound $t) {
            \ob_clean();
            \http_response_code(404);

            if (\is_readable(\genug\Setting\VIEW_404_FILE)) {
                require_once \genug\Setting\VIEW_404_FILE;
            } else {
                echo '404 Not Found';
            }
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