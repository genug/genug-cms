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
    public Method $method {get;}
    public string $path {get;}

    public function accepts(ContentType ...$contentType): bool;
}
