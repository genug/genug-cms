<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug;

use genug\Group\ {
    RepositoryInterface as GroupRepositoryInterface
};
use genug\Page\ {
    RepositoryInterface as PageRepositoryInterface,
    Entity as PageEntity
};
use genug\Setting\Setting;

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
        public readonly GroupRepositoryInterface $groups,
        public readonly Setting $setting
    ) {
    }
}
