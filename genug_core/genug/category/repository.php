<?php
declare(strict_types = 1);
namespace genug\Category;

use const genug\Persistence\FileSystem\Category\FILENAME as CATEGORY_FILENAME;

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
     * @todo [a] better Exception
     * @todo [b] error_log and continue
     */
    public static function fromFileSystem(string $path): Repository
    {
        $directories = new class(new \FilesystemIterator($path)) extends \FilterIterator {

            public function accept()
            {
                return parent::current()->isDir();
            }
        };
        
        $instance = new self();
        
        foreach ($directories as $dir) {
            try {
                $file = new \SplFileInfo($dir->getRealPath() . '/' . CATEGORY_FILENAME);
                
                if (! $file->isFile() || ! $file->isReadable()) {
                    throw new \Exception(); // [a]
                }
                $instance->_attach(Entity::fromIniFile(new Id($dir->getBasename()), $file->getRealPath()));
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

    private function _attach(Entity $entity)
    {
        if (\array_key_exists($entity->id()->__toString(), $this->_entities_fetch_cache)) {
            throw new \LogicException('ID already exists.');
        }
        $this->_entities[] = $this->_entities_fetch_cache[$entity->id()->__toString()] = $entity;
    }
}