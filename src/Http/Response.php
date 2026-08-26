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

interface Response
{
    public Status $status {get;}

    // TODO ContentType is a header
    public ContentType $contentType {get;}

    public Header $header {get;}

    public string|Stringable $body {get;}

    /**
     * MUST return a new instance
     */
    public function withStatus(Status $status): static;

    /**
     * MUST return a new instance
     */
    public function withHeader(Header $header): static;

    /**
     * MUST return a new instance
     */
    public function withBody(string|Stringable $body): static;
}
