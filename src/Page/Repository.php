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
use Exception;
use FilesystemIterator;
use FilterIterator;
use genug\Environment\Environment;
use genug\Lib\AbstractFrontMatterFile;
use genug\Lib\EntityCache;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Throwable;

use function count;

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

    protected readonly string $mainGroupIdString;

    public function __construct(
        private readonly EntityCache $entityCache,
        protected readonly Environment $environment,
        private readonly LoggerInterface $logger
    ) {
        $this->mainGroupIdString = (string) $this->environment->mainGroupId();
        $this->idToFilePathMap = $this->createIdToFilePathMap();
        $this->iterator = $this->idToFilePathMap->getIterator();
    }

    public function fetch(string $id): Entity
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

    public function fetchOrNull(string $id): ?Entity
    {
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

    protected function _fetch(string $id): Entity
    {
        if (! $this->idToFilePathMap->offsetExists($id)) {
            throw new InvalidArgumentException();
        }
        return $this->entityCache->fetchOrNull(Entity::class, $id) ?? $this->createAndCacheEntity($id);
    }

    public function count(): int
    {
        return count($this->idToFilePathMap);
    }

    public function current(): Entity
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
        $pageFile = new SplFileInfo($this->idToFilePathMap->offsetGet($idString));

        $dir = $pageFile->getPathInfo();

        $_data = new class ($pageFile->getRealPath()) extends AbstractFrontMatterFile {
            protected function _parseFrontMatterString(string $str): array
            {
                return Yaml::parse($str);
            }
        };

        $title = (function () use ($_data) {
            $fm = $_data->frontMatter();
            if (! isset($fm['title'])) {
                throw new Exception();
            }
            return $fm['title'];
        })();

        $date = (function () use ($_data) {
            $fm = $_data->frontMatter();
            if (! isset($fm['date'])) {
                throw new Exception();
            }
            return $fm['date'];
        })();

        $entity = new Entity(
            new Id($idString),
            new Group($dir->getBasename()),
            new Title($title),
            new Date($date),
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

        $directories = new class (new FilesystemIterator(PAGE_DIR)) extends FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $pageFiles = new class (new FilesystemIterator($dir->getRealPath())) extends FilterIterator {
                public function accept(): bool
                {
                    return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                }
            };

            foreach ($pageFiles as $pageFile) {
                $id = (function () use ($dir, $pageFile) {
                    $rtn = '';
                    if ($dir->getBasename() === $this->mainGroupIdString && $pageFile->getBasename() === HOME_PAGE_FILENAME) {
                        $rtn = '/';
                    } elseif ($dir->getBasename() === $this->mainGroupIdString) {
                        $rtn = '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                    } else {
                        $rtn = '/' . $dir->getBasename() . '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                    }
                    return $rtn;
                })();

                $idToFilePathMap->offsetSet($id, $pageFile->getRealPath());
            }
        }
        return $idToFilePathMap;
    }
}
