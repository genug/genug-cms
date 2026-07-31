<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Lib\ValueObject;

use Deprecated;
use InvalidArgumentException;

use function is_object;
use function sprintf;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
trait IdTrait
{
    public function __construct(private string $id)
    {
        static::isValide($id) ?: throw new InvalidArgumentException(sprintf('Invalide id %s', $id));
    }

    abstract protected static function isValide(string $id): bool;

    // FIXME Remove as soon as an independent validator exists
    #[Deprecated('Use an independent validator')]
    public static function tryFromString(string $id): ?static
    {
        if (! static::isValide($id)) {
            return null;
        }
        return new static($id);
    }

    public function equals(mixed $other): bool
    {
        if (! is_object($other)) {
            return false;
        }

        return ($this == $other);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
