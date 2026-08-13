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
use InvalidArgumentException;

class HttpResponceException extends Exception
{
    final public protected(set) Status $status;

    final public function __construct(Status $status)
    {
        if ($status->isRedirectionMessage()) {
            throw new Exception('Not implemented');
        }

        if ($status->isInformational() || $status->isSuccessful()) {
            throw new InvalidArgumentException();
        }

        $this->status = $status;

        parent::__construct();
    }
}
