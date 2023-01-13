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

namespace genug\Environment;

use Attribute;
use genug\Log;
use Monolog\Logger;

use function is_string;

use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOL;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 * @internal
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
    public function from(Preset $envVar): string|bool
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
        if (is_string($value)) {
            return $value;
        }
        $this->logger->debug(sprintf('Environment varibale %s is not set.', $envVar->name));
        return $envVar->value;
    }

    protected function getEnvAsBool(Preset $envVar): bool
    {
        /* workaround psalm: ERROR: MixedArgument - Argument 2 of filter_var cannot be mixed, expecting int */
        $filter = (int) FILTER_VALIDATE_BOOL;

        /** @var ?bool */
        $value = filter_var($this->getEnv($envVar), $filter, FILTER_NULL_ON_FAILURE);
        if (null === $value) {
            $this->logger->warning(sprintf('%s has an invalid value.', $envVar->name));
        }
        return (bool) $value;
    }
}
