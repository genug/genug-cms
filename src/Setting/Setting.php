<?php

declare(strict_types=1);

namespace genug\Setting;

use genug\Page\IdInterface as PageIdInterface;
use genug\Group\IdInterface as GroupIdInterface;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Setting
{
    public function __construct(
        public readonly PageIdInterface $homePageId,
        public readonly PageIdInterface $notFoundPageId,
        public readonly GroupIdInterface $mainGroupId,
    ) {
    }
}
