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

namespace genug\Controller;

use genug\Http\Request;
use genug\Http\Response;
use genug\HttpResponceException\HttpPermanentRedirect;
use LogicException;
use Psr\Log\LoggerAwareTrait;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\Url;

use function intval;
use function is_string;
use function rtrim;

final class RemoveTrailingSlashController implements Controller
{
    use LoggerAwareTrait;

    public function handle(Request $request): Response
    {
        /*
        Because the data in the request object has been sanitized
        and does not necessarily match the raw request,
        the unmodified `$_SERVER` data is accessed directly here.
        */

        $host = is_string($_SERVER['SERVER_NAME'] ?? null) ? $_SERVER['SERVER_NAME'] : throw new LogicException();
        $port = is_string($_SERVER['SERVER_PORT'] ?? null) ? intval($_SERVER['SERVER_PORT']) : null;

        $requestUri = is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : throw new LogicException();
        $requestUri = new Uri($requestUri);
        $query = $requestUri->getQuery();

        $path = rtrim($requestUri->getPath(), '/');

        $uri = new Uri('')
        ->withScheme((! empty($_SERVER['HTTPS'] ?? null)) ? 'https' : 'http')
        ->withHost($host)
        ->withPort($port)
        ->withPath($path)
        ->withQuery($query);

        throw new HttpPermanentRedirect(new Url($uri->toString()));
    }
}
