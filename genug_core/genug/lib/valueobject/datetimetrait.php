<?php

declare(strict_types=1);

namespace genug\Lib\ValueObject;

use function trim;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
trait DateTimeTrait
{
    private $_isMutable = true;

    private $_obj;

    /**
     *
     * @todo better Exception
     */
    public function __construct(string $time)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        if ('' === trim($time)) {
            // prevents current time fallback
            // hint: use "now"
            throw new \InvalidArgumentException();
        }
        try {
            $this->_isMutable = false;
            $this->_obj = new \DateTime($time);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function format(string $format): string
    {
        return $this->_obj->format($format);
    }

    public function __toString(): string
    {
        return $this->_obj->format('c');
    }
}
