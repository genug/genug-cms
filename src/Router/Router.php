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

use genug\Controller\PageController;
use genug\Http\HttpException;
use genug\Http\Request;
use genug\Http\Response;
use genug\Http\Status;
use genug\Http\StatusResponce;
use genug\Page\PageId;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Router
{
    public function __construct(
        private Request $request,
        private PageController $pageController
    ) {
    }

    public function dispatch(): Response
    {
        try {
            $controller = match(true) {
                PageId::isValide($this->request->path) => $this->pageController,
                // TODO fileController
                default => throw new HttpException(Status::NotFound)
            };

            return $controller->handle($this->request);
        } catch (HttpException $httpException) {
            // TODO set content type
            return new StatusResponce($httpException->status);
        }
    }
}
