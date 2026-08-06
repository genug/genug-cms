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

use Stringable;

trait IsResponse
{
    public protected(set) Status $status;

    public protected(set) ContentType $contentType;

    public protected(set) string|Stringable $body;

    public function __construct(
        Status $status,
        ContentType $contentType,
        string|Stringable $body
    ) {
        $this->status = $status;
        $this->contentType = $contentType;
        $this->body = $body;
    }
}
