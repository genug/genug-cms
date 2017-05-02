<?php
declare(strict_types = 1);
namespace genug\Lib;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class ImmutableData
{

    private $_stdClass;

    /**
     *
     * @todo better Exception
     */
    public static function fromJSON(string $json, int $depth = 512, int $options = 0): ImmutableData
    {
        $stdClass = \json_decode($json, FALSE, $depth, $options);

        if (is_null($stdClass) && \json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error', \json_last_error());
        }

        $instance = new self();
        $instance->_stdClass = $stdClass;

        return $instance;
    }

    private function __construct()
    {}

    public function __call($name, $arguments)
    {
        if (! empty($arguments)) {
            throw new \InvalidArgumentException();
        }
        if (! isset($this->_stdClass->$name)) {
            throw new \BadMethodCallException();
        }
        return $this->_stdClass->$name;
    }
}