<?php

declare(strict_types=1);

namespace genug\Page;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity
{
    private $_isMutable = true;

    private $_id;

    private $_category;

    private $_title;

    private $_date;

    private $_content;

    public function __construct(Id $id, Category $category, Title $title, Date $date, Content $content)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = false;

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

    public function date(): Date
    {
        return $this->_date;
    }

    public function content(): Content
    {
        return $this->_content;
    }
}
