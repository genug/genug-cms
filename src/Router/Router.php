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

namespace genug\Router;

use Closure;
use Exception;
use genug\Config\Config;
use genug\Http\GenericRequest;
use genug\Http\HttpException;
use genug\Http\Method;
use genug\Http\Request;
use genug\Http\Response;
use genug\Http\Status;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use Uri\Rfc3986\Uri;

use function array_first;
use function in_array;
use function mb_strlen;
use function mb_substr;
use function sprintf;
use function str_starts_with;
use function strtoupper;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Router implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private array $storage = [];

    public function __construct(
        private Config $config
    ) {
    }

    public function register(string $regex, Closure $closure, Method ...$methods): void
    {
        if (! in_array(Method::HEAD, $methods) && in_array(Method::GET, $methods)) {
            $methods[] = Method::HEAD;
        }

        foreach ($methods as $method) {
            if (isset($this->storage[$method->name][$regex])) {
                throw new LogicException();
            }

            $this->storage[$method->name][$regex] = $closure;
        }
    }

    public function dispatch(): Response
    {
        $method = $this->createMethodFromGlobal();

        if (
            Method::HEAD !== $method
            && Method::GET !== $method
        ) {
            $this->logger?->debug(sprintf('Support for the HTTP %s method has not been implemented.', $method->name));
            throw new HttpException(Status::MethodNotAllowed);
        }

        $closure = $this->tryMatch(
            $method,
            $this->createUriFromGlobal()
        ) ?? throw new HttpException(Status::NotFound);

        $request = $this->detectRequestFromClosure($closure);

        return $closure($request);
    }

    private function detectRequestFromClosure(Closure $closure): Request
    {
        $this->isClosureValid($closure) ?: throw new LogicException();

        $reflectionFunction = new ReflectionFunction($closure);
        $reflectionParameter = array_first($reflectionFunction->getParameters());
        $reflectionType = $reflectionParameter->getType();
        $reflectionRequestClass = new ReflectionClass($reflectionType->getName());

        // TODO Automate the creation of Requests by the container

        if ($reflectionRequestClass->isInterface()) {
            return new GenericRequest($this->createMethodFromGlobal(), $this->createUriFromGlobal());
        }

        return match ($reflectionRequestClass->getName()) {
            GenericRequest::class => new GenericRequest($this->createMethodFromGlobal(), $this->createUriFromGlobal()),
            default => throw new LogicException()
        };
    }

    private function isClosureValid(Closure $closure): bool
    {
        try {
            $reflectionFunction = new ReflectionFunction($closure);

            // Parameter

            if (1 !== $reflectionFunction->getNumberOfParameters()) {
                throw new Exception();
            }
            $reflectionParameter = array_first($reflectionFunction->getParameters());
            $reflectionType = $reflectionParameter->getType();
            if (!($reflectionType instanceof ReflectionNamedType)) {
                throw new Exception();
            }

            $reflectionClass = new ReflectionClass($reflectionType->getName());
            if (! $reflectionClass->implementsInterface(Request::class)) {
                throw new Exception();
            }

            // ReturnType

            if (! $reflectionFunction->hasReturnType()) {
                throw new Exception();
            }

            if ($reflectionFunction->hasTentativeReturnType()) {
                throw new Exception();
            }

            $refectionReturnType = $reflectionFunction->getReturnType();

            if ($refectionReturnType->allowsNull()) {
                throw new Exception();
            }

            if (! ($refectionReturnType instanceof ReflectionNamedType)) {
                throw new Exception();
            }

            $refelctionReturnClass = new ReflectionClass($refectionReturnType->getName());
            if (! $refelctionReturnClass->implementsInterface(Response::class)) {
                throw new Exception();
            }

            return true;
        } catch (Exception $e) {
            $this->logger?->debug($e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    private function tryMatch(Method $method, Uri $uri): ?Closure
    {
        if (! isset($this->storage[$method->name])) {
            return null;
        }

        $options = $this->storage[$method->name];

        foreach ($options as $regex => $closure) {
            if (\Safe\preg_match($regex, $uri->getPath())) {
                return $closure;
            }
        }
        return null;
    }

    private function createUriFromGlobal(): Uri
    {
        $uri = new Uri($_SERVER['REQUEST_URI']);
        if ($this->config->pathBase) {
            if (! str_starts_with($uri->getPath(), $this->config->pathBase)) {
                throw new LogicException('The pathBase is configured incorrectly.');
            }
            $path = mb_substr($uri->getPath(), mb_strlen($this->config->pathBase));

            $uri = $uri->withPath($path);
        }
        return $uri;
    }

    private function createMethodFromGlobal(): Method
    {
        $dirtyHttpMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        foreach (Method::cases() as $case) {
            if ($case->name === $dirtyHttpMethod) {
                return $case;
            }
        }
        throw new HttpException(Status::MethodNotAllowed);
    }
}
