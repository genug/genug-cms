<?php

declare(strict_types=1);

namespace genug\Category;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Id
{
    use \genug\Lib\ValueObject\trait_Id;

    public const VALID_STRING_PATTERN = '#^[a-z0-9][a-z0-9_\-]*$#';
}
