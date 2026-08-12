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

interface Request
{
    public protected(set) Method $method {get; set;}
    public protected(set) string $path {get; set;}

    public function accepts(ContentType ...$contentTypes): bool;

    /**
     * MUST return a new instance
     */
    public function withMethod(Method $method): static;
}
