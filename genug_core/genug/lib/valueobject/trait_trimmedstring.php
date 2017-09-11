<?php
declare(strict_types = 1);
namespace genug\Lib\ValueObject;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
trait trait_TrimmedString
{

    private $_isMutable = TRUE;

    private $_trimmedString;

    public function __construct(string $untrimmedString)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = FALSE;
        $this->_trimmedString = \trim($untrimmedString);
    }

    public function __toString(): string
    {
        return $this->_trimmedString;
    }
}