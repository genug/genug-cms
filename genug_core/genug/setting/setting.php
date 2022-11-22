<?php

declare(strict_types=1);

namespace genug\Setting;

use const genug\Setting\{
    MAIN_GROUP_ID,
    HOME_PAGE_ID,
    HTTP_404_PAGE_ID,
};

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
class Setting
{
    public function __construct(
        public readonly string $homePageId = HOME_PAGE_ID,
        public readonly string $notFoundPageId = HTTP_404_PAGE_ID,
        public readonly string $mainGroupId = MAIN_GROUP_ID,
    ) {
    }
}
