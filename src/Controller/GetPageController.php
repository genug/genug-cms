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
use genug\Http\ContentType;
use genug\Http\GenericResponce;
use genug\Http\Method;
use genug\Http\Request;
use genug\Http\Response;
use genug\Http\Status;
use genug\Http\StatusResponce;
use genug\Page\PageId;
use genug\Page\PageRepository;
use LogicException;

final class GetPageController
{
    public function __construct(
        private Config $config,
        private PageRepository $pages
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if ($request->method !== Method::GET) {
            throw new LogicException();
        }

        // FIXME Implement $request->accepts
        // if (! $request->accepts(ContentType::HTML)) {
        //     return new StatusResponce(Status::BadRequest);
        // }

        if (! PageId::isValide($request->path)) {
            return new StatusResponce(Status::NotFound);
        }

        $http404PageId = new PageId($this->config->http404PageId);
        $status = Status::OK;
        $page = $this->pages->tryFetch(new PageId($request->path)) ?? $this->pages->fetch($http404PageId);

        if ($page->id->equals($http404PageId)) {
            $status = Status::NotFound;
        }

        return new GenericResponce(
            $status,
            ContentType::HTML,
            // TODO use View
            $page->content
        );
    }
}
