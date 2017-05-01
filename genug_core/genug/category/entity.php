<?php
declare(strict_types = 1);
namespace genug\Category;

use genug\Lib\ImmutableData;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity
{

    private $_id;

    private $_file;

    private $_data;

    /**
     *
     * @todo better Exception
     */
    public static function fromFile(Id $id, string $path): Entity
    {
        $file = new \SplFileInfo($path);
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new \InvalidArgumentException();
        }
        
        $instance = new self();
        $instance->_id = $id;
        
        $instance->_file = $file;
        
        return $instance;
    }

    private function __construct()
    {}

    public function id(): Id
    {
        return $this->_id;
    }

    public function title(): string
    {
        if (! \is_object($this->_data)) {
            $this->_readFile();
        }
        return $this->_data->title();
    }

    private function _readFile()
    {
        $fileContent = $this->_file->openFile()->fread($this->_file->getSize());
        
        $this->_data = ImmutableData::fromJSON($fileContent);
    }
}