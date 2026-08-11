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

namespace genug\Page;

use genug\Http\ContentType;
use genug\Http\GenericResponce;
use genug\Http\Method;
use genug\Http\Request;
use genug\Http\Response;
use genug\Http\Status;
use LogicException;

use function Safe\file_get_contents;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class HtmlPageEntity extends PageEntity
{
    public function get(Request $request): Response
    {
        if ($request->method !== Method::GET) {
            throw new LogicException();
        }

        // FIXME Implement $request->accepts
        // if (! $request->accepts(ContentType::HTML)) {
        //     return new StatusResponce(Status::BadRequest);
        // }

        // TODO use View
        $content = file_get_contents($this->sourceFile->getRealPath());

        return new GenericResponce(
            Status::OK,
            ContentType::HTML,
            $content
        );
    }
}
