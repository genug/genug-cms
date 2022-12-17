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
use Exception;
use FilesystemIterator;
use FilterIterator;
use genug\Lib\EntityCache;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Throwable;

use function count;

use const genug\Persistence\FileSystem\Group\DIR as GROUP_DIR;
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
        private readonly LoggerInterface $logger
    ) {
        $this->idToFilePathMap = self::createIdToFilePathMap();
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

    public function fetchOrNull(string $id): ?Entity
    {
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
        return $this->entityCache->fetchGroupOrNull($id) ?? $this->createAndCacheEntity($id);
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
        $file = new SplFileInfo($this->idToFilePathMap->offsetGet($idString));
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new Exception($file->getFilename());
        }

        $data = Yaml::parseFile($file->getRealPath());
        if (! isset($data['title'])) {
            throw new Exception();
        }

        $entity = new Entity(
            new Id($idString),
            new Title($data['title'])
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

    protected static function createIdToFilePathMap(): ArrayObject
    {
        $idToFilePathMap = new ArrayObject();
        $directories = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator(GROUP_DIR)) extends FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $file = new SplFileInfo($dir->getRealPath() . '/' . GROUP_FILENAME);
            if (! $file->isFile()) {
                continue;
            }
            $id = $file->getPathInfo()->getBasename();

            $idToFilePathMap->offsetSet($id, $file->getRealPath());
        }

        return $idToFilePathMap;
    }
}
