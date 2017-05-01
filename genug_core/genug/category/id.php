<?php
declare(strict_types = 1);
namespace genug\Category;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Id
{

    private $_isMutable = TRUE;

    private $_id;

    const VALID_STRING_PATTERN = '#^[a-z0-9][a-z0-9_\-]*$#';

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