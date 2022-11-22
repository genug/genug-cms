<?php

declare(strict_types=1);

namespace genug\Group;

use ArrayIterator;
use ArrayObject;
use genug\Lib\EntityAndIdCache;
use Throwable;
use RuntimeException;

use const genug\Persistence\FileSystem\Group\ {
    DIR as GROUP_DIR,
    FILENAME as GROUP_FILENAME
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Repository implements RepositoryInterface
{
    private bool $_isMutable = true;

    private readonly ArrayObject $idToFilePathMap;
    private readonly ArrayIterator $iterator;
    private readonly EntityAndIdCache $entityAndIdCache;

    /**
     *
     * @todo [b] error_log and continue
     */
    public function __construct()
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = false;

        $this->idToFilePathMap = self::createIdToFilePathMap();
        $this->iterator = $this->idToFilePathMap->getIterator();

        $this->entityAndIdCache = EntityAndIdCache::instance();
    }

    /**
     * @todo [a] log
     */
    public function fetch(string $id): Entity
    {
        if (! $this->idToFilePathMap->offsetExists($id)) {
            throw new EntityNotFound();
        }
        try {
            return $this->entityAndIdCache->fetchOrNull(Entity::class, $id) ?? $this->createAndCacheEntity($id);
        } catch (Throwable $t) {
            // [a]
            throw new EntityNotFound(previous: $t);
        }
    }

    public function count(): int
    {
        return \count($this->idToFilePathMap);
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
        $file = new \SplFileInfo($this->idToFilePathMap->offsetGet($idString));
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new \Exception($file->getFilename());
        }

        $data = \parse_ini_file($file->getRealPath(), false, INI_SCANNER_TYPED);
        if (! isset($data['title'])) {
            throw new \Exception();
        }

        $entity =  new Entity(
            $this->entityAndIdCache->fetchOrNull(Id::class, $idString) ?? new Id($idString),
            new Title($data['title'])
        );

        $this->entityAndIdCache->attach($entity);
        return $entity;
    }

    protected static function createIdToFilePathMap(): ArrayObject
    {
        $idToFilePathMap = new ArrayObject();

        $directories = new class (new \FilesystemIterator(GROUP_DIR)) extends \FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $file = new \SplFileInfo($dir->getRealPath() . '/' . GROUP_FILENAME);
            if (! $file->isFile()) {
                continue;
            }
            $id = $file->getPathInfo()->getBasename();

            $idToFilePathMap->offsetSet($id, $file->getRealPath());
        }

        return $idToFilePathMap;
    }
}
