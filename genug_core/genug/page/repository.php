<?php
declare(strict_types = 1);
namespace genug\Page;

use genug\Category\ {
                Repository as CategoryRepository, 
                Entity as CategoryEntity
};
use const genug\Persistence\FileSystem\Page\ {
                FILENAME_EXTENSION as PAGE_FILENAME_EXTENSION, 
                HOMEPAGE_FILENAME
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Repository implements \Iterator, \Countable
{

    private $_isMutable = TRUE;

    private $_position = 0;

    private $_entities = [];

    private $_entities_fetch_cache = [];

    /**
     *
     * @todo [a] error_log (notice)
     * @todo [b] error_log and continue
     */
    public static function fromFileSystem(string $path, CategoryRepository $categories, CategoryEntity $mainCategory): Repository
    {
        $instance = new self();
        
        foreach ($categories as $category) {
            try {
                $dir = new \SplFileInfo($path . '/' . $category->id()->__toString());
                
                if (! $dir->isDir()) {
                    // [a]
                    continue;
                }
                
                $pageFiles = new class(new \FilesystemIterator($dir->getRealPath())) extends \FilterIterator {

                    public function accept()
                    {
                        return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                    }
                };
                
                foreach ($pageFiles as $pageFile) {
                    try {
                        if ($category === $mainCategory && $pageFile->getBasename() === HOMEPAGE_FILENAME) {
                            $id = '/';
                        } elseif ($category === $mainCategory) {
                            $id = '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                        } else {
                            $id = '/' . $category->id()->__toString() . '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                        }
                        $instance->_attach(Entity::fromFileWithIniFrontMatter(new Id($id), $category, $pageFile->getRealPath()));
                    } catch (\Throwable $t) {
                        throw $t; // [b]
                    }
                }
            } catch (\Throwable $t) {
                throw $t; // [b]
            }
        }
        
        return $instance;
    }

    /**
     *
     * @todo [b] error_log and continue
     */
    public function __construct(Entity ...$entities)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = FALSE;
        
        foreach ($entities as $entity) {
            try {
                $this->_attach($entity);
            } catch (\Throwable $t) {
                throw $t; // [b]
            }
        }
    }

    private function _attach(Entity $entity)
    {
        if (\array_key_exists($entity->id()->__toString(), $this->_entities_fetch_cache)) {
            throw new \LogicException('ID already exists.');
        }
        $this->_entities[] = $this->_entities_fetch_cache[$entity->id()->__toString()] = $entity;
    }

    public function fetch(string $id): Entity
    {
        if (! \array_key_exists($id, $this->_entities_fetch_cache)) {
            throw new throwable_EntityNotFound();
        }
        return $this->_entities_fetch_cache[$id];
    }

    public function count()
    {
        return \count($this->_entities);
    }

    public function current(): Entity
    {
        return $this->_entities[$this->_position];
    }

    public function key(): string
    {
        return (string) $this->_entities[$this->_position]->id();
    }

    public function next()
    {
        ++ $this->_position;
    }

    public function rewind()
    {
        $this->_position = 0;
    }

    public function valid(): bool
    {
        return isset($this->_entities[$this->_position]);
    }
}