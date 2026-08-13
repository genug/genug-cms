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

use genug\Lib\Equatable;
use genug\Lib\IsEquatableSimply;

enum Method implements Equatable
{
    use IsEquatableSimply;

    case GET;
    case HEAD;
    case POST;
    case PUT;
    case DELETE;
    case CONNECT;
    case OPTIONS;
    case TRACE;
    case PATCH;
    case QUERY;
}
