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

use function trim;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 *
 * @phpstan-require-implements \Stringable
 */
trait TrimmedStringTrait
{
    private string $trimmedString;

    public function __construct(string $untrimmedString)
    {
        $this->trimmedString = trim($untrimmedString);
    }

    public function __toString(): string
    {
        return $this->trimmedString;
    }
}
