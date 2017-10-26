<?php
declare(strict_types = 1);
namespace genug;

use genug\Category\ {
                Repository as CategoryRepository, 
                Generator as CategoryGenerator, 
                Entity as CategoryEntity
};
use genug\Page\ {
                throwable_EntityNotFound as throwable_PageEntityNotFound, 
                Repository as PageRepository, 
                Entity as PageEntity
};
use const genug\Persistence\FileSystem\Page\DIR as PAGE_DIR;
use const genug\Setting\ {
                MAIN_CATEGORY_ID, 
                HOMEPAGE_ID, 
                REQUESTED_PAGE_ID
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Api
{

    private static $_categories;

    private static $_pages;

    public static function categories(): CategoryRepository
    {
        if (! \is_object(self::$_categories)) {
            self::$_categories = new CategoryRepository(...CategoryGenerator::generateEntities());
        }
        return self::$_categories;
    }

    public static function mainCategory(): CategoryEntity
    {
        return self::categories()->fetch(MAIN_CATEGORY_ID);
    }

    public static function pages(): PageRepository
    {
        if (! \is_object(self::$_pages)) {
            self::$_pages = PageRepository::fromFileSystem(PAGE_DIR, self::categories(), self::mainCategory());
        }
        return self::$_pages;
    }

    public static function homepage(): PageEntity
    {
        return self::pages()->fetch(HOMEPAGE_ID);
    }

    public static function requestedPage(): PageEntity
    {
        try {
            return self::pages()->fetch(REQUESTED_PAGE_ID);
        } catch (throwable_PageEntityNotFound $t) {
            throw new throwable_RequestedPageNotFound('', 0, $t);
        }
    }
}