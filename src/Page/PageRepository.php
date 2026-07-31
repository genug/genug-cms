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

use Countable;
use Exception;
use genug\Config\Config;
use IteratorAggregate;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Traversable;

use function file_exists;
use function Safe\file_get_contents;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class PageRepository implements IteratorAggregate, Countable, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Config $config
    ) {
    }

    public function fetch(PageId $id): PageEntity
    {
        return $this->tryFetch($id) ?? throw new PageEntityNotFound();
    }

    public function tryFetch(PageId $id): ?PageEntity
    {
        $fileName = $id . '.' . $this->config->persistencePageFilenameExtension;
        if ((string) $id === $this->config->homePageId) {
            $fileName = '/' . $this->config->persistencePageHomePageFilename;
        }

        $path = $this->config->persistenceContentDirectory . $fileName;

        if (! file_exists($path)) {
            return null;
        }
        return new PageEntity(
            $id,
            new PageContent(file_get_contents($path)),
        );
    }

    public function getIterator(): Traversable
    {
        // TODO Implement Iterator
        throw new Exception('Not implemented');
        // yield
    }

    public function count(): int
    {
        // TODO Implement Countable
        throw new Exception('Not implemented');
    }
}
