<?php
declare(strict_types = 1);
namespace genug;

use genug\Category\ {
                Repository as CategoryRepository, 
                Entity as CategoryEntity
};
use genug\Page\ {
                Repository as PageRepository, 
                Entity as PageEntity
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
final class Api
{

    private static $_categories;

    private static $_pages;

    private static $_isPageRequestValid;

    private static $_requestedPage;

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
        try {
            if (FALSE === self::$_isPageRequestValid) {
                throw new throwable_Exception();
            }
            if (! \is_object(self::$_requestedPage)) {
                //
                // avoid self::pages()->fetch($untrustedString)
                //
                $untrustedString = RequestUri::path();
                $allPages = [];
                foreach (self::pages() as $page) {
                    $allPages[$page->id()->__toString()] = $page;
                }
                
                if (! \array_key_exists($untrustedString, $allPages)) {
                    throw new throwable_Exception();
                }
                self::$_requestedPage = $allPages[$untrustedString];
            }
            return self::$_requestedPage;
        } catch (throwable_Exception $t) {
            self::$_isPageRequestValid = FALSE;
            throw new throwable_RequestedPageNotFound('', 0, $t);
        }
    }
}