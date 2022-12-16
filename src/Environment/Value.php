<?php

declare(strict_types=1);

namespace genug\Environment;

use Attribute;
use genug\Log;
use Monolog\Logger;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final class Value
{
    protected readonly Logger $logger;

    public function __construct(
        public readonly Type $type
    ) {
        $this->logger = Log::instance('genug_environment');
    }

    /**
     * @todo validate Type::FilePath, Type::IdString
     */
    public function from(Preset $envVar): mixed
    {
        return match ($this->type) {
            Type::Bool => $this->getEnvAsBool($envVar),
            Type::String,
            Type::FilePath,
            Type::IdString => $this->getEnv($envVar),
        };
    }

    protected function getEnv(Preset $envVar): string
    {
        $value = getenv($envVar->name, true);
        if (false !== $value) {
            return $value;
        }
        $this->logger->debug(sprintf('Environment varibale %s is not set.', $envVar->name));
        return $envVar->value;
    }

    protected function getEnvAsBool(Preset $envVar): bool
    {
        $value = filter_var($this->getEnv($envVar), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if (null === $value) {
            $this->logger->warning(sprintf('%s has an invalid value.', $envVar->name));
        }
        return (bool) $value;
    }
}