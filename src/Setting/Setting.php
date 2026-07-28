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

namespace genug\Setting;

use genug\Page\AbstractId as AbstractPageId;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Setting
{
    public function __construct(
        public readonly AbstractPageId $homePageId,
        public readonly AbstractPageId $notFoundPageId
    ) {
    }
}
