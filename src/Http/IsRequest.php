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

/**
 * @phpstan-require-implements Request
 */
trait IsRequest
{
    public protected(set) Method $method;
    public protected(set) string $path;

    public function __construct(
        Method $method,
        string $path,
    ) {
        $this->method = $method;
        $this->path = $path;
    }

    public function accepts(ContentType ...$contentTypes): bool
    {
        throw new Exception('Not implemented');
    }

    public function withMethod(Method $method): static
    {
        $request = clone $this;
        $request->method = $method;
        return $request;
    }
}
