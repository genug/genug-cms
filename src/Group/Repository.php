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

namespace genug\Group;

use ArrayIterator;
use ArrayObject;
use FilesystemIterator;
use FilterIterator;
use genug\Environment\Environment;
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

use const genug\Persistence\FileSystem\Group\FILENAME as GROUP_FILENAME;

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

    public function fetch(string $id): Entity
    {
        try {
            return $this->_fetch($id);
        } catch (Throwable $t) {
            $this->logger->alert(
                'Fetching a group from the repository fails.',
                [
                    'method' => __METHOD__,
                    'retrieved_id' => $id,
                    'throwable' => $t,
                ]
            );
            throw new EntityNotFound();
        }
    }

    public function fetchOrNull(?string $id): ?Entity
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
                'NULL will be returned after fetching a group from the repository failed.',
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
        $data = $this->readMetaData($idString);

        $title = (function () use ($data, $idString): ?Title {
            $_title = $data['title'] ?? null;
            if (null === $_title) {
                $this->logger->debug(sprintf('No title found for Group "%s".', $idString));
                return null;
            }
            if (! is_string($_title)) {
                $this->logger->warning(
                    sprintf('String expected, %s received.', gettype($_title)),
                    [
                        'group_id' => $idString,
                        'property' => 'title'
                    ]
                );
                return null;
            }
            return new Title($_title);
        })();

        $entity = new Entity(
            new Id($idString),
            $title
        );

        try {
            $this->entityCache->attach($entity);
        } catch (Throwable $t) {
            $this->logger->error(
                'Caching of the group entity fails.',
                [
                    'method' => __METHOD__,
                    'entity' => $entity,
                    'throwable' => $t,
                ]
            );
        }
        return $entity;
    }

    protected function readMetaData(string $id): array
    {
        try {
            $dirRealPath = $this->idToFilePathMap->offsetGet($id);
            $fileRealPath = (new SplFileInfo($dirRealPath . '/' . GROUP_FILENAME))->getRealPath();
            if (! $fileRealPath) {
                throw new LogicException('No metadata file found.');
            }
            $data = Yaml::parseFile($fileRealPath);
            if (! is_array($data)) {
                throw new LogicException(sprintf('Array expected, %s received.', gettype($data)));
            }
            return $data;
        } catch (Throwable $t) {
            $this->logger->notice(
                sprintf('No metadata found for Group "%s".', $id),
                [
                    'group_id' => $id,
                    'throwable' => $t,
                ]
            );
            return [];
        }
    }

    protected function createIdToFilePathMap(): ArrayObject
    {
        $idToFilePathMap = new ArrayObject();
        $directories = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator($this->environment->contentDirectory())) extends FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $id = $dir->getBasename();
            $path = $dir->getRealPath();

            $idToFilePathMap->offsetSet($id, $path);
        }

        return $idToFilePathMap;
    }
}
