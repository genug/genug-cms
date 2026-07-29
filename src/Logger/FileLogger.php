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

use DateTimeInterface;
use Override;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Safe\DateTimeImmutable;
use Stringable;

use function array_search;
use function gettype;
use function in_array;
use function is_string;
use function Safe\file_put_contents;
use function Safe\preg_match;
use function sprintf;
use function strtoupper;

use const FILE_APPEND;
use const LOCK_EX;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class FileLogger implements LoggerInterface
{
    use LoggerTrait;

    private const LEVELS = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY,
    ];

    public function __construct(
        private string $logFile,
        private string $minLogLevel = LogLevel::ERROR
    ) {
    }

    #[Override]
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (! in_array($level, self::LEVELS, true)) {
            throw new \Psr\Log\InvalidArgumentException();
        }

        if ($this->isMinLogLevelReached($level)) {
            $data = sprintf(
                '[%s] %s %s',
                new DateTimeImmutable()->format(DateTimeInterface::ISO8601_EXPANDED),
                strtoupper((string) $level),
                strtr((string) $message, self::createReplacePairs($context))
            );

            $data .= "\n";

            file_put_contents($this->logFile, $data, FILE_APPEND | LOCK_EX);
        }
    }

    private function isMinLogLevelReached(string $level): bool
    {
        return (array_search($level, self::LEVELS) >= array_search($this->minLogLevel, self::LEVELS));
    }

    /**
     * @param array<mixed,mixed> $array
     * @return array<string,string>
     */
    private static function createReplacePairs(array $array): array
    {
        $replacePairs = [];

        foreach ($array as $key => $value) {
            if (! self::isValiedPlaceholder($key)) {
                continue;
            }

            $value = self::convertValue($value);

            if ($value === null) {
                continue;
            }

            $replacePairs['{' . $key . '}'] = $value;
        }

        return $replacePairs;
    }

    private static function isValiedPlaceholder(mixed $placeholder): bool
    {
        if (! is_string($placeholder)) {
            return false;
        }

        return (bool) preg_match('#^[\w\.]+$#', $placeholder);
    }

    private static function convertValue(mixed $value): ?string
    {
        return match(gettype($value)) {
            'boolean' => $value ? 'TRUE' : 'FALSE',
            'integer',
            'double',
            'string' => (string) $value,
            'object' => (function () use ($value) {
                if ($value instanceof DateTimeInterface) {
                    return $value->format(DateTimeInterface::ISO8601_EXPANDED);
                }
                if ($value instanceof Stringable) {
                    return (string) $value;
                }
                return $value::class;
            })(),
            default => null
        };
    }
}
