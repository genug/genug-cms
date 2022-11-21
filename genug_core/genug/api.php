<?php

declare(strict_types=1);

namespace genug;

use genug\Category\ {
    Repository as CategoryRepository
};
use genug\Page\ {
    Repository as PageRepository,
    Entity as PageEntity
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Api
{
    public function __construct(
        public readonly PageRepository $pages,
        public readonly PageEntity $requestedPage,
        public readonly PageEntity $homePage,
        public readonly CategoryRepository $categories
    ) {
    }
}
