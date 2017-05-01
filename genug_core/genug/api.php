<?php
declare(strict_types = 1);
namespace genug;

use genug\Category\ {
                Repository as CategoryRepository, 
                Entity as CategoryEntity
};
use const genug\Persistence\FileSystem\Category\DIR as CATEGORY_DIR;
use const genug\Setting\MAIN_CATEGORY_ID;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class API
{

    private static $_categories;

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
}