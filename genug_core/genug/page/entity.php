<?php
declare(strict_types = 1);
namespace genug\Page;

use genug\Category\Entity as Category;
use genug\Lib\ {
                DateTime as genugLibDateTime, 
                abstract_FrontMatterFile
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity
{

    private $_id;

    private $_category;

    private $_date;

    private $_filePath;

    private $_data;

    /**
     *
     * @todo better Exception
     */
    public static function fromFile(Id $id, Category $category, string $path): Entity
    {
        $file = new \SplFileInfo($path);
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new \InvalidArgumentException();
        }
        
        $instance = new self();
        $instance->_id = $id;
        $instance->_category = $category;
        $instance->_filePath = $file->getRealPath();
        
        return $instance;
    }

    private function __construct()
    {}

    public function id(): Id
    {
        return $this->_id;
    }

    public function category(): Category
    {
        return $this->_category;
    }

    public function title(): string
    {
        if (! \is_object($this->_data)) {
            $this->_readFile();
        }
        return $this->_data->frontMatter()['title'];
    }

    public function date(): genugLibDateTime
    {
        if (! \is_object($this->_data)) {
            $this->_readFile();
        }
        if (! \is_object($this->_date)) {
            $this->_date = new genugLibDateTime($this->_data->frontMatter()['date']);
        }
        return $this->_date;
    }

    public function content(): string
    {
        if (! \is_object($this->_data)) {
            $this->_readFile();
        }
        return $this->_data->content();
    }

    private function _readFile()
    {
        $this->_data = new class($this->_filePath) extends abstract_FrontMatterFile {

            protected function _parseFrontMatterString(string $str): array
            {
                return \parse_ini_string($str);
            }
        };
    }
}