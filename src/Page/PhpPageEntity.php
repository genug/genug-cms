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
use genug\Http\Response;
use genug\Http\Status;
use LogicException;
use Override;

use function http_response_code;
use function is_int;
use function ob_get_level;
use function Safe\ob_end_clean;
use function Safe\ob_get_clean;
use function Safe\ob_start;
use function sprintf;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class PhpPageEntity implements PageEntity
{
    use IsPageEntity;

    #[Override]
    public function get(GetPageDto $dto): Response
    {
        try {
            $prevObLevel = ob_get_level();
            ob_start();
            $page = (function (): HTMLDocument|PageEntity {
                $prevLevel = ob_get_level();
                $prevHeaders = \headers_list();
                $prevResponceCode = http_response_code();

                $r = require $this->file->getRealPath();

                if ($prevLevel !== ob_get_level()) {
                    throw new LogicException(sprintf('The page has at least one open output buffer. Page file: %s', $this->file->getRealPath()));
                }
                if ($prevHeaders !== headers_list()) {
                    throw new LogicException(sprintf('Setting HTTP headers is not allowed in pages. Page file: %s', $this->file->getRealPath()));
                }

                if ($prevResponceCode !== http_response_code()) {
                    throw new LogicException(sprintf('Setting HTTP status codes is not allowed in pages. Page file: %s', $this->file->getRealPath()));
                }

                if (1 === $r) {
                    $dom = HTMLDocument::createFromString(ob_get_clean());

                    $this->addDocType($dom);
                    $this->modifyTitle($dom);

                    return $dom;
                }
                if (! ($r instanceof PageEntity)) {
                    throw new LogicException("Invalid File: {$this->file->getRealPath()}.");
                }

                return $r;
            })();

            if ($page instanceof HTMLDocument) {
                $status = Status::OK;
                if (is_int($i = http_response_code())) {
                    $status = Status::from($i);
                }

                return new GenericResponce($status, ContentType::HTML, $page->saveHtml());
            }

            $page->id = $this->id;
            $page->file = $this->file;
            $page->config = $this->config;
            $page->pages = $this->pages;
            if ($this->logger) {
                $page->setLogger($this->logger);
            }
            $page->init();

            $responce = $page->get($dto);

            if (! $responce->contentType->equals(ContentType::HTML)) {
                throw new LogicException(sprintf('A page must have the content type %s. Page file: %s', ContentType::HTML->value, $this->file->getRealPath()));
            }

            $dom = HTMLDocument::createFromString((string) $responce->body);

            $this->addDocType($dom);
            $this->modifyTitle($dom);

            return $responce->withBody($dom->saveHtml());
        } finally {
            // @phpstan-ignore nullCoalesce.variable
            while (ob_get_level() > ($prevObLevel ?? ob_get_level())) {
                ob_end_clean();
            }
        }
    }

    // TODO outsource
    private function addDocType(HTMLDocument $dom): void
    {
        if (! $dom->doctype) {
            $doctype = $dom->implementation->createDocumentType('html', '', '');
            $doctype = $dom->importNode($doctype);
            $dom->insertBefore($doctype, $dom->firstChild);
        }
    }

    // TODO outsource
    private function modifyTitle(HTMLDocument $dom): void
    {
        if ($this->config->siteTitle) {
            $title = $this->config->siteTitle;

            if ($dom->title) {
                $title = "{$dom->title} {$this->config->titleDelimiter} {$title}";
            }

            $dom->title = $title;
        }
    }
}
