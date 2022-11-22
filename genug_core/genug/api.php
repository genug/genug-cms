<?php

declare(strict_types=1);

namespace genug;

use genug\Group\ {
    RepositoryInterface as GroupRepositoryInterface
};
use genug\Page\ {
    RepositoryInterface as PageRepositoryInterface,
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
        public readonly PageRepositoryInterface $pages,
        public readonly PageEntity $requestedPage,
        public readonly PageEntity $homePage,
        public readonly GroupRepositoryInterface $groups
    ) {
    }
}
