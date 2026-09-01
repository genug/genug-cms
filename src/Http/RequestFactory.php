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

namespace genug\Http;

use genug\Config\Config;
use genug\HttpResponceException\HttpBadRequest;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Uri\Rfc3986\Uri;

use function is_string;
use function mb_strlen;
use function mb_substr;
use function str_starts_with;
use function strtoupper;

final class RequestFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private Config $config)
    {
    }

    public function createRequestFromGlobal(): Request
    {
        return new GenericRequest(self::createMethodFromGlobal(), $this->createPathFromGlobal());
    }

    private static function createMethodFromGlobal(): Method
    {
        $serverRequestMethod = $_SERVER['REQUEST_METHOD'];
        if (! is_string($serverRequestMethod)) {
            throw new LogicException('Missing $_SERVER[\'REQUEST_METHOD\']');
        }
        $dirtyHttpMethod = strtoupper($serverRequestMethod);
        foreach (Method::cases() as $case) {
            if ($case->name === $dirtyHttpMethod) {
                return $case;
            }
        }
        throw new HttpBadRequest();
    }

    private function createPathFromGlobal(): string
    {
        $serverRequestUri = $_SERVER['REQUEST_URI'];
        if (! is_string($serverRequestUri)) {
            throw new LogicException('Missing $_SERVER[\'REQUEST_URI\']');
        }
        $uri = new Uri($serverRequestUri);
        if ($this->config->pathBase) {
            if (! str_starts_with($uri->getPath(), $this->config->pathBase)) {
                throw new LogicException('The pathBase is configured incorrectly.');
            }
            $path = mb_substr($uri->getPath(), mb_strlen($this->config->pathBase));

            $uri = $uri->withPath($path);
        }
        return $uri->getPath();
    }
}
