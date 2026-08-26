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

namespace genug\Http;

use IteratorAggregate;
use Override;
use Traversable;

use function array_filter;
use function array_keys;
use function array_map;
use function implode;
use function mb_strtolower;

final class Header implements IteratorAggregate
{
    private array $collection = [];

    #[Override]
    public function getIterator(): Traversable
    {
        yield from array_keys($this->collection);
    }

    public function withLocation(string $uri): static
    {
        return $this->with('Location', $uri);
    }

    public function withAllow(Method ...$methods): static
    {
        $value = implode(', ', array_map((fn ($x) => $x->name), $methods));

        return $this->with('Allow', $value);
    }

    public function with(string $name, string $value, bool $replace = true): static
    {
        $clone = clone $this;

        $label = self::nameToLabel($name);

        if ($replace) {
            $clone->collection = array_filter($this->collection, fn ($v) => $v !== $label);
        }

        $clone->collection[self::toHeaderString($name, $value)] = $label;

        return $clone;
    }

    private static function nameToLabel(string $name): string
    {
        return $name |> \trim(...) |> mb_strtolower(...);
    }

    private static function toHeaderString(string $name, string $value): string
    {
        $name = trim($name);
        $value = trim($value);
        return "{$name}: {$value}";
    }
}
