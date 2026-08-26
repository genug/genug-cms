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

/**
 * @phpstan-require-implements Response
 */
trait IsResponse
{
    public protected(set) Status $status;

    public protected(set) Header $header;

    // TODO ContentType is a Header
    public protected(set) ContentType $contentType;

    public protected(set) string|Stringable $body;

    public function __construct(
        Status $status,
        ContentType $contentType,
        string|Stringable $body,
        Header $header = new Header()
    ) {
        $this->status = $status;
        $this->contentType = $contentType;
        $this->body = $body;
        $this->header = $header;
    }

    public function withStatus(Status $status): static
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    public function withHeader(Header $header): static
    {
        $clone = clone $this;
        $clone->header = $header;
        return $clone;
    }

    public function withBody(string|Stringable $body): static
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }
}
