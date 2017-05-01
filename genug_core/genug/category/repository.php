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

    private $_position = 0;

    private $_entities = [];

    private $_entities_fetch_cache = [];

    /**
     *
     * @todo [a] better Exception
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
            
            $file = new \SplFileInfo($dir->getRealPath() . '/' . CATEGORY_FILENAME);
            
            if (! $file->isFile()) {
                throw new \Exception(); // [a]
            }
            $instance->_attach(Entity::fromFile(new Id($dir->getBasename()), $file->getRealPath()));
        }
        
        return $instance;
    }

    private function __construct()
    {}

    /**
     *
     * @todo better Exception
     */
    public function fetch(string $id): Entity
    {
        if (! \array_key_exists($id, $this->_entities_fetch_cache)) {
            throw new \Exception();
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