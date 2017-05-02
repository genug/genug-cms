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

    private $_position = 0;

    private $_entities = [];

    private $_entities_fetch_cache = [];

    public static function fromFileSystem(string $path, CategoryRepository $categories, CategoryEntity $mainCategory): Repository
    {
        $instance = new self();
        
        foreach ($categories as $category) {
            
            $pageFiles = new class(new \FilesystemIterator($path . '/' . $category->id()->__toString())) extends \FilterIterator {

                public function accept()
                {
                    return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                }
            };
            
            foreach ($pageFiles as $pageFile) {
                if ($category === $mainCategory && $pageFile->getBasename() === HOMEPAGE_FILENAME) {
                    $id = '/';
                } elseif ($category === $mainCategory) {
                    $id = '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                } else {
                    $id = '/' . $category->id()->__toString() . '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                }
                $instance->_attach(Entity::fromFile(new Id($id), $category, $pageFile->getRealPath()));
            }
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