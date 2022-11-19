<?php

declare(strict_types=1);

namespace genug\Page;

use ArrayIterator;
use ArrayObject;
use genug\Lib\ {
    abstract_FrontMatterFile,
    EntityCache
};
use Throwable;
use RuntimeException;

use const genug\Persistence\FileSystem\Page\ {
    DIR as PAGE_DIR,
    FILENAME_EXTENSION as PAGE_FILENAME_EXTENSION,
    HOMEPAGE_FILENAME
};
use const genug\Setting\MAIN_CATEGORY_ID;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Repository implements \Iterator, \Countable
{
    private bool $_isMutable = true;

    private readonly ArrayObject $idToFilePathMap;
    private readonly ArrayIterator $iterator;
    private readonly EntityCache $entityCache;


    public function __construct()
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = false;

        $this->idToFilePathMap = self::createIdToFilePathMap();
        $this->iterator = $this->idToFilePathMap->getIterator();

        $this->entityCache = EntityCache::instance();
    }

    /**
     * @todo [a] log
     */
    public function fetch(string $id): Entity
    {
        if (! $this->idToFilePathMap->offsetExists($id)) {
            throw new throwable_EntityNotFound();
        }
        try {
            return $this->entityCache->fetchOrNull($id, Entity::class) ?? $this->createAndCacheEntity($id);
        } catch (Throwable $t) {
            // [a]
            throw new throwable_EntityNotFound(previous: $t);
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
        } catch (throwable_EntityNotFound $t) {
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
        $pageFile = new \SplFileInfo($this->idToFilePathMap->offsetGet($idString));

        $dir = $pageFile->getPathInfo();

        $_data = new class ($pageFile->getRealPath()) extends abstract_FrontMatterFile {
            protected function _parseFrontMatterString(string $str): array
            {
                return \parse_ini_string($str, false, \INI_SCANNER_TYPED);
            }
        };

        $title = (function () use ($_data) {
            $fm = $_data->frontMatter();
            if (! isset($fm['title'])) {
                throw new \Exception();
            }
            return $fm['title'];
        })();

        $date = (function () use ($_data) {
            $fm = $_data->frontMatter();
            if (! isset($fm['date'])) {
                throw new \Exception();
            }
            return $fm['date'];
        })();

        $entity = new Entity(
            new Id($idString),
            new Category($dir->getBasename()),
            new Title($title),
            new Date($date),
            new Content($_data->content())
        );

        $this->entityCache->attach($entity);
        return $entity;
    }

    protected static function createIdToFilePathMap(): ArrayObject
    {
        $idToFilePathMap = new ArrayObject();

        $directories = new class (new \FilesystemIterator(PAGE_DIR)) extends \FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $pageFiles = new class (new \FilesystemIterator($dir->getRealPath())) extends \FilterIterator {
                public function accept(): bool
                {
                    return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                }
            };

            foreach ($pageFiles as $pageFile) {
                $id = (function () use ($dir, $pageFile) {
                    $rtn = '';
                    if ($dir->getBasename() === MAIN_CATEGORY_ID && $pageFile->getBasename() === HOMEPAGE_FILENAME) {
                        $rtn = '/';
                    } elseif ($dir->getBasename() === MAIN_CATEGORY_ID) {
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
