<?php
declare(strict_types = 1);
namespace genug;

use genug\Category\ {
                Repository as CategoryRepository, 
                Entity as CategoryEntity
};
use genug\Page\ {
                Repository as PageRepository, 
                Entity as PageEntity, 
                Id as PageId
};
use genug\Server\RequestUri;
use const genug\Persistence\FileSystem\Category\DIR as CATEGORY_DIR;
use const genug\Persistence\FileSystem\Page\DIR as PAGE_DIR;
use const genug\Setting\ {
                MAIN_CATEGORY_ID, 
                HOMEPAGE_ID
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class API
{

    private static $_categories;

    private static $_pages;

    private static $_isRequestedPageIdValid;

    private static $_requestedPageId;

    public static function categories(): CategoryRepository
    {
        if (! \is_object(self::$_categories)) {
            self::$_categories = CategoryRepository::fromFileSystem(CATEGORY_DIR);
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
        if (FALSE === self::$_isRequestedPageIdValid) {
            throw new \Exception();
        }
        if (\is_null(self::$_requestedPageId)) {
            $id = RequestUri::path();
            if (! \preg_match(PageId::VALID_STRING_PATTERN, $id)) {
                self::$_isRequestedPageIdValid = FALSE;
                throw new \Exception();
            }
            self::$_requestedPageId = $id;
        }
        
        return self::pages()->fetch(self::$_requestedPageId);
    }
}