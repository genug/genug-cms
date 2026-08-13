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

use genug\Config\Config;
use genug\Http\HttpException;
use genug\Http\Method;
use genug\Http\Request;
use genug\Http\Response;
use genug\Http\Status;
use genug\Page\PageId;
use genug\Page\PageRepository;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

use function sprintf;

final class PageController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Config $config,
        private PageRepository $pages
    ) {
    }

    public function __invoke(Request $request): Response
    {
        // TODO Allow other methods
        if (
            ! $request->method->equals(Method::HEAD)
            && !$request->method->equals(Method::GET)
        ) {
            $this->logger?->debug(sprintf('Not implemented: %s cannot handle method %s.', $this::class, $request->method->name));
            throw new HttpException(Status::MethodNotAllowed);
        }

        // TODO Implement $request->accepts

        // Ensure that HEAD requests are handled exactly the same way as GET requests.
        if ($request->method->equals(Method::HEAD)) {
            $request = $request->withMethod(Method::GET);
        }

        $path = $request->path;

        if (! PageId::isValide($path)) {
            $this->logger?->debug('Request path is a invalid page id.');
            $path = $this->config->http404PageId;
        }

        $pageId = new PageId($path);
        $http404PageId = new PageId($this->config->http404PageId);

        $page = $this->pages->tryFetch($pageId)
            ?? $this->pages->tryFetch($http404PageId)
            ?? throw new HttpException(Status::NotFound);

        $responce = match(true) {
            $request->method->equals(Method::GET) => $page->get($request),
            default => throw new LogicException()
        };

        if ($page->id->equals($http404PageId)) {
            return $responce->withStatus(Status::NotFound);
        }
        return $responce;
    }
}
