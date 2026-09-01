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

use function array_unique;
use function rtrim;
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
                str_ends_with($this->request->path, self::TRAILING_SLASH) => throw new HttpPermanentRedirect(self::createRequestUriWithoutTrailingSlash($this->request)),
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

    /**
     * @todo If Request supports more than just path, then support it here as well.
     */
    private function createRequestUriWithoutTrailingSlash(Request $request): Uri
    {
        return new Uri(rtrim($this->request->path, self::TRAILING_SLASH));
    }

    private function createLocationHeader(HttpPermanentRedirect $exception): Header
    {
        $uri = $exception->location;

        if (! $uri->getHost()) {
            $path = $this->config->pathBase;
            $path .= $uri->getPath();

            $uri = $uri->withHost($this->config->host)
                ->withScheme($this->config->https ? 'https' : 'http')
                ->withPort($this->config->port)
                ->withPath($path);
        }

        return new Header()->withLocation($uri->toString());
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
