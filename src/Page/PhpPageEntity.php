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
use genug\Http\IsResponse;
use genug\Http\Response;
use genug\Http\Status;
use LogicException;
use Override;
use SplFileInfo;

use function http_response_code;
use function is_object;
use function ob_get_contents;
use function ob_get_level;
use function Safe\ob_end_clean;
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
            $prevHeaders = \headers_list();
            $prevResponceCode = http_response_code();
            ob_start();
            $page = (function (): HTMLDocument|PageEntity {
                // A separate context for the page code.
                $c = new class ($this->file, $this->id, new PageId($this->config->homePageId)) {
                    public function __construct(
                        private SplFileInfo $file,
                        // @phpstan-ignore property.onlyWritten
                        private PageId $id,
                        // @phpstan-ignore property.onlyWritten
                        private PageId $homeId,
                        // TODO Pages without this page
                    ) {
                    }

                    public function load(): true|PageEntity
                    {
                        $r = require $this->file->getRealPath();

                        if ($r === 1) {
                            return true;
                        }

                        if ($r instanceof PageEntity) {
                            return $r;
                        }

                        throw new LogicException("Invalid File: {$this->file->getRealPath()}.");
                    }
                };

                $r = $c->load();

                if (is_object($r)) {
                    return $r;
                }

                $content = ob_get_contents();
                if (false === $content) {
                    throw new LogicException(sprintf('Missing output puffer. Page file: %s', $this->file->getRealPath()));
                }
                return HTMLDocument::createFromString($content);
            })();

            ob_end_clean();

            if ($prevObLevel !== ob_get_level()) {
                throw new LogicException(sprintf('The output buffering level is incorrect. Page file: %s', $this->file->getRealPath()));
            }
            if ($prevHeaders !== headers_list()) {
                throw new LogicException(sprintf('Setting HTTP headers is not allowed in pages. Page file: %s', $this->file->getRealPath()));
            }
            if ($prevResponceCode !== http_response_code()) {
                throw new LogicException(sprintf('Setting HTTP status codes is not allowed in pages. Page file: %s', $this->file->getRealPath()));
            }

            $responce = new class (Status::OK, ContentType::HTML, '') implements Response {
                use IsResponse;
            };

            if (! ($page instanceof HTMLDocument)) {
                $page->id = $this->id;
                $page->file = $this->file;
                $page->config = $this->config;
                $page->pages = $this->pages;
                if ($this->logger) {
                    $page->setLogger($this->logger);
                }
                $page->init();

                $responce = $page->get($dto);
                $page = HTMLDocument::createFromString((string) $responce->body);
            }

            if (! $responce->contentType->equals(ContentType::HTML)) {
                throw new LogicException(sprintf('A page must have the content type %s. Page file: %s', ContentType::HTML->value, $this->file->getRealPath()));
            }

            $this->addDocType($page);
            $this->modifyTitle($page);
            // TODO `<a href="{id}">...</a>` -> rewrite set pathBase before id

            return $responce->withBody($page->saveHtml());
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
