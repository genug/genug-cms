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

namespace genug\Http;

use Exception;
use Uri\Rfc3986\Uri;

/**
 * @phpstan-require-implements Request
 */
trait IsRequest
{
    public protected(set) Method $method;
    public protected(set) string $path;

    public function __construct(
        Method $method,
        Uri $uri,
    ) {
        $this->method = $method;
        $this->path = $uri->getPath();
    }

    public function accepts(ContentType ...$contentType): bool
    {
        throw new Exception('Not implemented');
    }
}
