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

enum Status: int
{
    // successful

    case OK = 200;

    // client error

    case BadRequest = 400;
    case NotFound = 404;

    public function description(): string
    {
        $text = match ($this) {
            self::OK => 'OK',
            default => \Safe\preg_replace('#(?:.)(?=[A-Z])#', ' ', $this->name)
        };

        return $this->value . ' ' . $text;
    }
}
