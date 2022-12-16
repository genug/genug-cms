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

namespace genug\Request;

use function dirname;
use function parse_url;

use const PHP_URL_PATH;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Request implements RequestInterface
{
    public function __construct(
    ) {
    }

    public function pageId(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathBase = (function (): string {
            $pathBase = dirname($_SERVER['SCRIPT_NAME']);
            if (\strlen($pathBase) === 1) {
                // $path_base is '\' (windows) OR '/' (linux) OR '.'
                $pathBase = '';
            }
            return $pathBase;
        })();
        return substr($path, strlen($pathBase));
    }
}
