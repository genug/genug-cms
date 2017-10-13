<?php
/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
require __DIR__ . '/genug_core/genug/bootstrap.php';

spl_autoload_register('genug\autoloader');

(function () {
    try {
        \ob_start();
        \http_response_code(200);
        try {
            genug\Api::requestedPage();
            require_once __DIR__ . '/genug_user/template/main.html.php';
        } catch (\genug\throwable_RequestedPageNotFound $t) {
            \ob_clean();
            \http_response_code(404);
            require_once __DIR__ . '/genug_user/template/error404.html.php';
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