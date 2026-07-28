<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Request;

use RuntimeException;

use function parse_url;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

use const PHP_URL_PATH;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Request implements RequestInterface
{
    public function __construct(
        private readonly string $pathBase = ''
    ) {
        if (str_ends_with($this->pathBase, '/')) {
            throw new RuntimeException(sprintf('Path base "%s" must not end with a slash.', $this->pathBase));
        }
    }

    public function pageId(): string
    {
        $_requestUri = $_SERVER['REQUEST_URI'] ?? throw new RuntimeException();
        $path = parse_url($_requestUri, PHP_URL_PATH);

        if (! str_starts_with($path, $this->pathBase)) {
            throw new RuntimeException(sprintf('Request URI "%s" does not start with path base "%s".', $path, $this->pathBase));
        }

        return substr($path, strlen($this->pathBase));
    }
}
