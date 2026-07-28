<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Logger;

use Override;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Logger implements LoggerInterface
{
    use LoggerTrait;

    public function __construct(
        private LoggerInterface $internalLogger
    ) {
    }

    #[Override]
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->internalLogger->log($level, $message, $context);
    }
}
