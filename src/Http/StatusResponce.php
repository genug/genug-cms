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

final class StatusResponce implements Response
{
    public private(set) Status $status;

    public private(set) ContentType $contentType;

    public private(set) string|Stringable $body;

    public function __construct(
        Status $status,
        ContentType $contentType = ContentType::HTML
    ) {
        $this->status = $status;
        $this->contentType = $contentType;
        $this->body = $status->description();
    }

    public function withStatus(Status $status): static
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }
}
