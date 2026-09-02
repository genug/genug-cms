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
use genug\Page\PageId;
use Uri\WhatWg\Url;

final class HttpPermanentRedirect extends HttpResponceException
{
    public function __construct(public readonly Url|PageId $location)
    {
        $this->status = Status::PermanentRedirect;

        parent::__construct();
    }
}
