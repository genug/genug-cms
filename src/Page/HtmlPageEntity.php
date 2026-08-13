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

use Dom\HTMLDocument;
use genug\Http\ContentType;
use genug\Http\GenericResponce;
use genug\Http\Request;
use genug\Http\Response;
use genug\Http\Status;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class HtmlPageEntity extends PageEntity
{
    public function get(Request $request): Response
    {
        $dom = HTMLDocument::createFromFile($this->sourceFile->getRealPath());

        if (! $dom->doctype) {
            $doctype = $dom->implementation->createDocumentType('html', '', '');
            $doctype = $dom->importNode($doctype);
            $dom->insertBefore($doctype, $dom->firstChild);
        }

        if ($this->config->websiteTitle) {
            $title = $this->config->websiteTitle;

            if ($dom->title) {
                $title = "{$dom->title} {$this->config->titleDelimiter} {$title}";
            }

            $dom->title = $title;
        }

        return new GenericResponce(
            Status::OK,
            ContentType::HTML,
            $dom->saveHtml()
        );
    }
}
