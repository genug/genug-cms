<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug;

use genug\Page\PageEntity;
use genug\Page\PageRepository;
use genug\Setting\Setting;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Api
{
    public function __construct(
        public readonly PageRepository $pages,
        public readonly PageEntity $requestedPage,
        public readonly PageEntity $homePage,
        public readonly Setting $setting
    ) {
    }
}
