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

    private $_isMutable = TRUE;

    private $_id;

    private $_title;

    /**
     *
     * @todo better Exception
     */
    public static function fromIniFile(Id $id, string $path): Entity
    {
        $file = new \SplFileInfo($path);
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new \Exception();
        }
        
        $data = \parse_ini_file($file->getRealPath(), FALSE, INI_SCANNER_TYPED);
        
        if (! isset($data['title'])) {
            throw new \Exception();
        }
        
        return new self($id, new Title($data['title']));
    }

    public function __construct(Id $id, Title $title)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = FALSE;
        
        $this->_id = $id;
        $this->_title = $title;
    }

    public function id(): Id
    {
        return $this->_id;
    }

    public function title(): Title
    {
        return $this->_title;
    }
}