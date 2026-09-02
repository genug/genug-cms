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

namespace genug\Router;

use genug\Config\Config;
use genug\Controller\PageController;
use genug\Controller\RemoveTrailingSlashController;
use genug\Http\ContentType;
use genug\Http\GenericResponce;
use genug\Http\Header;
use genug\Http\Method;
use genug\Http\Request;
use genug\Http\Response;
use genug\HttpResponceException\HttpMethodNotAllowed;
use genug\HttpResponceException\HttpNotFound;
use genug\HttpResponceException\HttpPermanentRedirect;
use genug\HttpResponceException\HttpResponceException;
use genug\Page\PageId;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\Url;

use function array_unique;
use function str_ends_with;

use const SORT_REGULAR;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Router
{
    private const TRAILING_SLASH = '/';

    public function __construct(
        private Request $request,
        private Config $config,
        private PageController $pageController
    ) {
    }

    public function dispatch(): Response
    {
        try {
            $controller = match(true) {
                PageId::isValide($this->request->path) => $this->pageController,
                // TODO fileController

                /* The trailing slash check must come after the PageId check, since `/` is a valid PageId. */
                str_ends_with($this->request->path, self::TRAILING_SLASH) => new RemoveTrailingSlashController(),
                default => throw new HttpNotFound()
            };

            return $controller->handle($this->request);
        } catch (HttpResponceException $httpResponceException) {
            // TODO set content type

            $header = match($httpResponceException::class) {
                HttpMethodNotAllowed::class => $this->createMethodNotAllowedHeader($httpResponceException),
                HttpPermanentRedirect::class => $this->createLocationHeader($httpResponceException),
                default => new Header(),
            };

            return new GenericResponce(
                $httpResponceException->status,
                ContentType::HTML,
                $httpResponceException->status->description(),
                $header
            );
        }
    }

    private function createLocationHeader(HttpPermanentRedirect $exception): Header
    {
        $location = $exception->location;

        if ($location instanceof PageId) {
            $uri = new Uri('')
            ->withScheme($this->config->https ? 'https' : 'http')
            ->withHost($this->config->host)
            ->withPort($this->config->port)
            ->withPath($this->config->pathBase . $location);

            $location = new Url($uri->toString());
        }

        return new Header()->withLocation($location->toAsciiString());
    }

    private function createMethodNotAllowedHeader(HttpMethodNotAllowed $exception): Header
    {
        $allowedMethods = $exception->allowedMethods;

        if (in_array(Method::GET, $allowedMethods)) {
            $allowedMethods[] = Method::HEAD;
        } elseif (in_array(Method::HEAD, $allowedMethods)) {
            $allowedMethods[] = Method::GET;
        }

        $allowedMethods = array_unique($allowedMethods, SORT_REGULAR);

        return new Header()->withAllow(...$allowedMethods);
    }
}
