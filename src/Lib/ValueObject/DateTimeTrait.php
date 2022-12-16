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

namespace genug\Lib\ValueObject;

use DateTime;
use InvalidArgumentException;

use function trim;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
trait DateTimeTrait
{
    protected readonly DateTime $dateTime;

    public function __construct(string $dateTime)
    {
        if ('' === trim($dateTime)) {
            throw new InvalidArgumentException('`$dateTime` is empty or consists of white space. Use `\'now\'` to use the current date and time.');
        }
        $this->dateTime = new DateTime($dateTime);
    }

    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }

    public function __toString(): string
    {
        return $this->dateTime->format('c');
    }
}
