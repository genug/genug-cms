<?php

declare(strict_types=1);

namespace genug\Page;

use Stringable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface IdInterface extends Stringable
{
    public function equals(self $id): bool;
}