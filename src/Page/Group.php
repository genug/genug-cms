<?php

declare(strict_types=1);

namespace genug\Page;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Group implements GroupInterface
{
    use \genug\Lib\ValueObject\IdTrait;

    public const VALID_STRING_PATTERN = '#^[a-z0-9][a-z0-9_\-]*$#';

    public function equals(GroupInterface $group): bool
    {
        return ($group instanceof $this && (string) $group === (string) $this);
    }
}