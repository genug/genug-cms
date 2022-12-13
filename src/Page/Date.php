<?php

declare(strict_types=1);

namespace genug\Page;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Date implements DateInterface
{
    use \genug\Lib\ValueObject\DateTimeTrait;
}
