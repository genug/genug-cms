<?php

declare(strict_types=1);

namespace genug\Page;

use Stringable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface DateInterface extends Stringable
{
    public function format(string $format): string;
}
