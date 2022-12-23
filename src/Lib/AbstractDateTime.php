<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Lib;

use DateTimeImmutable;
use InvalidArgumentException;
use Stringable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
abstract class AbstractDateTime extends DateTimeImmutable implements Stringable
{
    public function __construct(string $dateTime)
    {
        if ('' === trim($dateTime)) {
            throw new InvalidArgumentException('`$dateTime` is empty or consists of white space. Use `\'now\'` to use the current date and time.');
        }
        parent::__construct($dateTime);
    }

    public function __toString(): string
    {
        return $this->format('c');
    }
}
