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
use genug\Page\PageEntity;
use genug\Page\PageEntityNotFound;
use genug\Page\PageId;
use genug\Page\PageRepository;
use genug\Request\Request;
use Psr\Log\LoggerInterface;
use Throwable;

use function sprintf;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Router
{
    public function __construct(
        protected readonly Request $request,
        protected readonly PageRepository $pages,
        protected readonly Config $config,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function result(): PageEntity
    {
        try {
            try {
                // FIXME Implement an independent validator and use it here
                $id = PageId::tryFromString($this->request->pageId());
                if (null === $id) {
                    throw new PageEntityNotFound();
                }
                return $this->pages->fetch($id);
            } catch (PageEntityNotFound $t) {
                $this->logger->debug(sprintf('Requested page "%s" not found.', $this->request->pageId()), ['exception' => $t]);
                $this->logger->debug(sprintf('Fetch the http-404 page instead.'), ['http404page_id' => $this->config->http404PageId]);
                return $this->pages->fetch(new PageId($this->config->http404PageId));
            }
        } catch (Throwable $t) {
            $this->logger->error('No result.', ['exception' => $t]);
            throw new RouterError(previous: $t);
        }
    }
}
