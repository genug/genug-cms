<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Page;

use ArrayIterator;
use ArrayObject;
use FilesystemIterator;
use FilterIterator;
use Generator;
use genug\Environment\Environment;
use genug\Lib\AbstractFrontMatterFile;
use genug\Lib\EntityCache;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Throwable;

use function count;
use function sprintf;

use const genug\Persistence\FileSystem\Page\DIR as PAGE_DIR;
use const genug\Persistence\FileSystem\Page\FILENAME_EXTENSION as PAGE_FILENAME_EXTENSION;
use const genug\Persistence\FileSystem\Page\HOME_PAGE_FILENAME;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Repository implements RepositoryInterface
{
    private readonly ArrayObject $idToFilePathMap;
    private readonly ArrayIterator $iterator;

    public function __construct(
        private readonly EntityCache $entityCache,
        protected readonly Environment $environment,
        private readonly LoggerInterface $logger
    ) {
        $this->idToFilePathMap = $this->createIdToFilePathMap();
        $this->iterator = $this->idToFilePathMap->getIterator();
    }

    public function fetch(string $id): AbstractEntity
    {
        try {
            return $this->_fetch($id);
        } catch (Throwable $t) {
            $this->logger->alert(
                'Fetching a page from the repository fails.',
                [
                    'method' => __METHOD__,
                    'retrieved_id' => $id,
                    'throwable' => $t,
                ]
            );
            throw new EntityNotFound();
        }
    }

    public function fetchOrNull(?string $id): ?AbstractEntity
    {
        if (null === $id) {
            $this->logger->debug(
                'Null was passed as the ID to be fetched.',
                [
                    'method' => __METHOD__
                ]
            );
            return null;
        }
        try {
            return $this->_fetch($id);
        } catch (Throwable $t) {
            $this->logger->debug(
                'NULL will be returned after fetching a page from the repository failed.',
                [
                    'method' => __METHOD__,
                    'retrieved_id' => $id,
                    'throwable' => $t,
                ]
            );
            return null;
        }
    }

    public function fetchByGroup(?string $group): Generator
    {
        foreach ($this as $page) {
            if ((string) $page->group === (string) $group) {
                yield $page;
            }
        }
    }

    protected function _fetch(string $id): AbstractEntity
    {
        if (! $this->idToFilePathMap->offsetExists($id)) {
            throw new InvalidArgumentException();
        }
        $cachedEntity = $this->entityCache->fetchOrNull(new Id($id));
        if (null === $cachedEntity) {
            return $this->createAndCacheEntity($id);
        }
        if ($cachedEntity::class !== Entity::class) {
            throw new LogicException();
        }
        return $cachedEntity;
    }

    public function count(): int
    {
        return count($this->idToFilePathMap);
    }

    public function current(): AbstractEntity
    {
        try {
            return $this->fetch($this->iterator->key());
        } catch (EntityNotFound $t) {
            throw new RuntimeException(previous: $t);
        }
    }

    public function key(): string
    {
        return $this->iterator->key();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    protected function createAndCacheEntity(string $idString): Entity
    {
        $logger = $this->logger;

        $pageFile = new SplFileInfo($this->idToFilePathMap->offsetGet($idString));

        $dir = $pageFile->getPathInfo();
        if (null === $dir) {
            throw new RuntimeException();
        }

        $group = (function () use ($dir): ?Group {
            if ($dir->getRealPath() === PAGE_DIR) {
                return null;
            }
            return new Group($dir->getBasename());
        })();

        $_data = new class ($pageFile->getRealPath(), $logger) extends AbstractFrontMatterFile {
            protected function _parseFrontMatterString(string $str): array
            {
                if (empty($str)) {
                    $this->logger->debug(
                        'No page front matter.',
                        ['file' => $this->path]
                    );
                    return [];
                }

                $data = Yaml::parse($str);
                if (! is_array($data)) {
                    $this->logger->warning(
                        'Invalid page front matter.',
                        ['file' => $this->path]
                    );
                    return [];
                }
                return $data;
            }
        };

        $title = (function () use ($_data, $idString, $logger): ?Title {
            $fm = $_data->frontMatter();
            if (! isset($fm['title'])) {
                $logger->debug(
                    sprintf('No title found for Page "%s"', $idString)
                );
                return null;
            }
            return new Title($fm['title']);
        })();

        $date = (function () use ($_data, $idString, $logger): ?Date {
            $fm = $_data->frontMatter();
            if (! isset($fm['date'])) {
                $logger->debug(
                    sprintf('No date found for Page "%s"', $idString)
                );
                return null;
            }
            return new Date($fm['date']);
        })();

        $entity = new Entity(
            new Id($idString),
            $group,
            $title,
            $date,
            new Content($_data->content())
        );

        try {
            $this->entityCache->attach($entity);
        } catch (Throwable $t) {
            $this->logger->error(
                'Caching of the page entity fails.',
                [
                    'method' => __METHOD__,
                    'entity' => $entity,
                    'throwable' => $t,
                ]
            );
        }
        return $entity;
    }

    protected function createIdToFilePathMap(): ArrayObject
    {
        $idToFilePathMap = new ArrayObject();

        // pages without group

        $pageFiles = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator(PAGE_DIR)) extends FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
            }
        };

        foreach ($pageFiles as $pageFile) {
            $id = (function () use ($pageFile) {
                if ($pageFile->getBasename() === HOME_PAGE_FILENAME) {
                    return '/';
                }
                return '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
            })();

            $idToFilePathMap->offsetSet($id, $pageFile->getRealPath());
        }

        // pages with group

        $directories = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator(PAGE_DIR)) extends FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $pageFiles = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator($dir->getRealPath())) extends FilterIterator {
                public function accept(): bool
                {
                    return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                }
            };

            foreach ($pageFiles as $pageFile) {
                $id = (function () use ($dir, $pageFile) {
                    return '/' . $dir->getBasename() . '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                })();

                $idToFilePathMap->offsetSet($id, $pageFile->getRealPath());
            }
        }
        return $idToFilePathMap;
    }
}
