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

    public ContentType $contentType {get;}

    public string|Stringable $body {get;}
}
