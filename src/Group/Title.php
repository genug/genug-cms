<?php

declare(strict_types=1);

namespace genug\Group;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Title implements TitleInterface
{
    use \genug\Lib\ValueObject\TrimmedStringTrait;
}