<?php

declare(strict_types=1);

namespace genug\Page;

use ArrayIterator;
use ArrayObject;
use genug\Lib\ {
    AbstractFrontMatterFile,
    EntityCache
};
use Throwable;
use RuntimeException;

use const genug\Persistence\FileSystem\Page\ {
    DIR as PAGE_DIR,
    FILENAME_EXTENSION as PAGE_FILENAME_EXTENSION,
    HOME_PAGE_FILENAME
};
use const genug\Setting\MAIN_GROUP_ID;

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
        private readonly EntityCache $entityCache
    ) {
        $this->idToFilePathMap = self::createIdToFilePathMap();
        $this->iterator = $this->idToFilePathMap->getIterator();
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
            return $this->entityCache->fetchOrNull(Entity::class, $id) ?? $this->createAndCacheEntity($id);
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
        $pageFile = new \SplFileInfo($this->idToFilePathMap->offsetGet($idString));

        $dir = $pageFile->getPathInfo();

        $_data = new class ($pageFile->getRealPath()) extends AbstractFrontMatterFile {
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
            new Group($dir->getBasename()),
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
                    if ($dir->getBasename() === MAIN_GROUP_ID && $pageFile->getBasename() === HOME_PAGE_FILENAME) {
                        $rtn = '/';
                    } elseif ($dir->getBasename() === MAIN_GROUP_ID) {
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
