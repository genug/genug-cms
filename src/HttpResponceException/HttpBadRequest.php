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

final class HttpBadRequest extends HttpResponceException
{
    public function __construct()
    {
        $this->status = Status::BadRequest;
        parent::__construct();
    }
}
