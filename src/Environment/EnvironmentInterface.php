<?php

declare(strict_types=1);

namespace genug\Environment;

use genug\Group\IdInterface as GroupIdInterface;
use genug\Page\IdInterface as PageIdInterface;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface EnvironmentInterface
{
    public function isDebug(): bool;

    public function debugLogFilePath(): string;

    public function mainGroupId(): GroupIdInterface;

    public function homePageId(): PageIdInterface;

    public function http404PageId(): PageIdInterface;
}
