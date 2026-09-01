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
use SplFileInfo;
use SplObjectStorage;
use Traversable;

use function file_exists;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class PageRepository implements IteratorAggregate, Countable, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const INDEX_FILE_NAME = 'index';

    private array $pageTypeOrder = [
        PageType::PHP,
        PageType::PHTML,
        PageType::HTML,
    ];

    /**
     * @var SplObjectStorage<PageType, PageEntity> $pageEntityClasses
     */
    private SplObjectStorage $pageEntityClasses;

    public function __construct(
        private Config $config
    ) {
        $this->pageEntityClasses = new SplObjectStorage();
        $this->pageEntityClasses[PageType::PHP] = PhpPageEntity::class;
        $this->pageEntityClasses[PageType::PHTML] = PhtmlPageEntity::class;
        $this->pageEntityClasses[PageType::HTML] = HtmlPageEntity::class;
    }

    public function fetch(PageId $id): PageEntity
    {
        return $this->tryFetch($id) ?? throw new PageEntityNotFound();
    }

    public function tryFetch(PageId $id): ?PageEntity
    {
        $filePathWidthoutExtension = (string) $id;
        if ($id->equals(new PageId($this->config->homePageId))) {
            $filePathWidthoutExtension = '/' . self::INDEX_FILE_NAME;
        }

        $filePathWidthoutExtension = $this->config->persistenceContentDirectory . $filePathWidthoutExtension;

        $pageType = (function () use ($filePathWidthoutExtension): ?PageType {
            foreach ($this->pageTypeOrder as $pageType) {
                $file = $filePathWidthoutExtension . '.' . $pageType->value;

                if (file_exists($file)) {
                    return $pageType;
                }
            }
            return null;
        })();

        if (!$pageType) {
            return null;
        }

        $fileInfo = new SplFileInfo($filePathWidthoutExtension . '.' . $pageType->value);

        $pageEntity = new $this->pageEntityClasses[$pageType]();
        $pageEntity->id = $id;
        $pageEntity->file = $fileInfo;
        $pageEntity->config = $this->config;
        $pageEntity->pages = $this;
        if ($this->logger) {
            $pageEntity->setLogger($this->logger);
        }
        $pageEntity->init();

        return $pageEntity;
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
