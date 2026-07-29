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

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Override;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use RuntimeException;
use Stringable;

use function defined;
use function file_put_contents;
use function gettype;
use function is_string;
use function preg_match;
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

    public function __construct(
        private string $logFile,
        private string $minLogLevel = LogLevel::ERROR
    ) {
    }

    #[Override]
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (! is_string($level)) {
            throw new \Psr\Log\InvalidArgumentException();
        }

        defined(LogLevel::class . '::' . strtoupper($level)) ?: throw new \Psr\Log\InvalidArgumentException();

        if ($this->isMinLogLevelReached($level)) {
            $data = sprintf(
                '[%s] %s %s',
                new DateTimeImmutable()->format(DateTimeInterface::ISO8601_EXPANDED),
                strtoupper((string) $level),
                self::interpolate((string) $message, $context)
            );

            $data .= "\n";

            if (false === file_put_contents($this->logFile, $data, FILE_APPEND | LOCK_EX)) {
                throw new RuntimeException('Cannot write to the log.');
            }
        }
    }

    /**
     * @param array<mixed> $context
     */
    private static function interpolate(string $message, array $context = array()): string
    {
        $replacePairs = [];

        foreach ($context as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $res = preg_match('#^[\w\.]+$#', $key);
            if (0 === $res) {
                continue;
            }
            $res ?: throw new RuntimeException('preg_match failed.');

            $value = match(gettype($value)) {
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

            if ($value === null) {
                continue;
            }

            $replacePairs['{' . $key . '}'] = $value;
        }

        return strtr($message, $replacePairs);
    }

    private function isMinLogLevelReached(string $level): bool
    {
        $minLogLevelWeight = self::levelWeigth($this->minLogLevel);
        $levelWeight = self::levelWeigth($level);

        return ($levelWeight >= $minLogLevelWeight);
    }

    private static function levelWeigth(string $level): int
    {
        return match($level) {
            LogLevel::EMERGENCY => 7,
            LogLevel::ALERT => 6,
            LogLevel::CRITICAL => 5,
            LogLevel::ERROR => 4,
            LogLevel::WARNING => 3,
            LogLevel::NOTICE => 2,
            LogLevel::INFO => 1,
            LogLevel::DEBUG => 0,
            default => throw new InvalidArgumentException()
        };
    }
}
