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

    private $_title;

    private $_date;

    private $_content;

    /**
     *
     * @todo better Exception
     */
    public static function fromFileWithIniFrontMatter(Id $id, Category $category, string $path): Entity
    {
        $file = new \SplFileInfo($path);
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new \Exception();
        }
        
        $data = new class($file->getRealPath()) extends abstract_FrontMatterFile {

            protected function _parseFrontMatterString(string $str): array
            {
                return \parse_ini_string($str);
            }
        };
        
        $fm = $data->frontMatter();
        
        if (! isset($fm['title'], $fm['date'])) {
            throw new \Exception();
        }
        
        return new self($id, $category, new Title($fm['title']), new genugLibDateTime($fm['date']), new Content($data->content()));
    }

    private function __construct(Id $id, Category $category, Title $title, genugLibDateTime $date, Content $content)
    {
        $this->_id = $id;
        $this->_category = $category;
        $this->_title = $title;
        $this->_date = $date;
        $this->_content = $content;
    }

    public function id(): Id
    {
        return $this->_id;
    }

    public function category(): Category
    {
        return $this->_category;
    }

    public function title(): Title
    {
        return $this->_title;
    }

    public function date(): genugLibDateTime
    {
        return $this->_date;
    }

    public function content(): Content
    {
        return $this->_content;
    }
}