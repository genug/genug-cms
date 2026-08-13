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

final class StatusResponce implements Response
{
    use IsResponse;

    public function __construct(
        Status $status,
        ContentType $contentType = ContentType::HTML
    ) {
        $this->status = $status;
        $this->contentType = $contentType;
        $this->body = $status->description();
    }
}
