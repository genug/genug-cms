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

namespace genug\HttpResponceException;

use genug\Http\Status;
use Uri\Rfc3986\Uri;

final class HttpPermanentRedirect extends HttpResponceException
{
    public function __construct(public readonly Uri $location)
    {
        $this->status = Status::PermanentRedirect;

        parent::__construct();
    }
}
