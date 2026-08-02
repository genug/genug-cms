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
use genug\Lib\EquatableSimpleObjectTrait;
use InvalidArgumentException;

use function sprintf;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 *
 * @phpstan-require-implements \Stringable
 * @phpstan-require-implements \genug\Lib\Equatable
 */
trait IdTrait
{
    use EquatableSimpleObjectTrait;

    public function __construct(private string $id)
    {
        static::isValide($id) ?: throw new InvalidArgumentException(sprintf('Invalide id %s', $id));
    }

    abstract public static function isValide(string $id): bool;

    // FIXME Remove as soon as an independent validator exists
    #[Deprecated('Use an independent validator')]
    public static function tryFromString(string $id): ?static
    {
        if (! static::isValide($id)) {
            return null;
        }
        return new static($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
