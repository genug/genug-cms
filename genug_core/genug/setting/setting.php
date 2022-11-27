<?php

declare(strict_types=1);

namespace genug\Setting;

use genug\Page\{
    IdInterface as PageIdInterface,
    Id as PageId
};
use genug\Group\{
    IdInterface as GroupIdInterface,
    Id as GroupId
};

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
        public readonly PageIdInterface $homePageId = new PageId(HOME_PAGE_ID),
        public readonly PageIdInterface $notFoundPageId = new PageId(HTTP_404_PAGE_ID),
        public readonly GroupIdInterface $mainGroupId = new GroupId(MAIN_GROUP_ID),
    ) {
    }
}
