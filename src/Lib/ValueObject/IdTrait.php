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

use BadMethodCallException;
use InvalidArgumentException;

use function preg_match;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
trait IdTrait
{
    private bool $_isMutable = true;

    private string $_id;

    public function __construct(string $id)
    {
        if (! $this->_isMutable) {
            throw new BadMethodCallException();
        }
        if (! preg_match(self::VALID_STRING_PATTERN, $id)) {
            throw new InvalidArgumentException();
        }
        $this->_isMutable = false;
        $this->_id = $id;
    }

    public function __toString(): string
    {
        return $this->_id;
    }
}
