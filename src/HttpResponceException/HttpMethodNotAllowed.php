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

use genug\Http\Method;
use genug\Http\Status;

final class HttpMethodNotAllowed extends HttpResponceException
{
    public readonly array $allowedMethods;

    public function __construct(Method ...$allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
        $this->status = Status::MethodNotAllowed;
        parent::__construct();
    }
}
