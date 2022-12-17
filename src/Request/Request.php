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

use RuntimeException;

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
        $_requestUri = $_SERVER['REQUEST_URI'] ?? throw new RuntimeException();
        $path = parse_url($_requestUri, PHP_URL_PATH);
        $pathBase = (function (): string {
            $_scriptName = $_SERVER['SCRIPT_NAME'] ?? throw new RuntimeException();
            $pathBase = dirname($_scriptName);
            if (\strlen($pathBase) === 1) {
                // $path_base is '\' (windows) OR '/' (linux) OR '.'
                $pathBase = '';
            }
            return $pathBase;
        })();
        return substr($path, strlen($pathBase));
    }
}
