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
    // Informational

    case Continue = 100;

    // successful

    case OK = 200;

    // Redirection messages

    case MultipleChoices = 300;

    // client error

    case BadRequest = 400;
    case NotFound = 404;
    case MethodNotAllowed = 405;

    // Server error

    case InternalServerError = 500;

    public function description(): string
    {
        $text = match ($this) {
            self::OK => 'OK',
            default => \Safe\preg_replace('#(?:.)(?=[A-Z])#', ' ', $this->name)
        };

        return $this->value . ' ' . $text;
    }

    public function isInformational(): bool
    {
        return $this->value < 200;
    }

    public function isSuccessful(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }

    public function isRedirectionMessage(): bool
    {
        return $this->value >= 300 && $this->value < 400;
    }

    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    public function isServerError(): bool
    {
        // @phpstan-ignore smaller.alwaysTrue
        return $this->value >= 500 && $this->value < 600;
    }
}
