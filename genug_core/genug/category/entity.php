<?php
declare(strict_types = 1);
namespace genug\Category;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity
{

    private $_id;

    private $_filePath;

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
        
        $instance->_filePath = $file->getRealPath();
        
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
        if (! \is_array($this->_data)) {
            $this->_fetchData();
        }
        return $this->_data['title'];
    }

    private function _fetchData()
    {
        if (FALSE === $this->_data = \parse_ini_file($this->_filePath)) {
            throw new \LogicException();
        }
    }
}