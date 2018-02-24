<?php
declare(strict_types = 1);
namespace genug\Lib\ValueObject;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
trait trait_Id
{

    private $_isMutable = TRUE;

    private $_id;

    public function __construct(string $id)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        if (! \preg_match(self::VALID_STRING_PATTERN, $id)) {
            throw new \InvalidArgumentException();
        }
        $this->_isMutable = FALSE;
        $this->_id = $id;
    }

    public function __toString(): string
    {
        return $this->_id;
    }
}