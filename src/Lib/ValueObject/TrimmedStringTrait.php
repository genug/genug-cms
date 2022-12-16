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

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
trait TrimmedStringTrait
{
    private $_isMutable = true;

    private $_trimmedString;

    public function __construct(string $untrimmedString)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = false;
        $this->_trimmedString = \trim($untrimmedString);
    }

    public function __toString(): string
    {
        return $this->_trimmedString;
    }
}
