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

use genug\Lib\EquivalentStringableInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 *
 * @psalm-suppress MissingConstructor
 */
final class EquivalentStringableInterfaceTest extends TestCase
{
    public function testIsInterface(): void
    {
        $class = new ReflectionClass(EquivalentStringableInterface::class);

        $this->assertTrue($class->isInterface());
    }

    /**
     * @depends testIsInterface
     */
    public function testExtendsStringable(): void
    {
        $class = new ReflectionClass(EquivalentStringableInterface::class);

        $this->assertTrue($class->implementsInterface(\Stringable::class));
    }

    public function testHasEqualsMethod(): void
    {
        $class = new ReflectionClass(EquivalentStringableInterface::class);

        $this->assertTrue($class->hasMethod('equals'));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodHasOneParameter(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');

        $this->assertSame(1, $method->getNumberOfParameters());
    }

    /**
     * @depends testEqualsMethodHasOneParameter
     */
    public function testEqualsMethodParameterNamedObject(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');
        $parameters = $method->getParameters();
        $parameter = array_shift($parameters);

        $this->assertSame('object', $parameter->getName());
    }

    /**
     * @depends testEqualsMethodHasOneParameter
     */
    public function testEqualsMethodParameterTypeIsStringable(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');
        $parameters = $method->getParameters();
        $parameter = array_shift($parameters);
        $type = $parameter->getType();

        $this->assertTrue($parameter->hasType());
        $this->assertInstanceOf(ReflectionNamedType::class, $type);
        $this->assertSame(Stringable::class, $type->getName());
    }

    /**
     * @depends testEqualsMethodHasOneParameter
     */
    public function testEqualsMethodParameterTypeAllowsNull(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');
        $parameters = $method->getParameters();
        $parameter = array_shift($parameters);
        $type = $parameter->getType();

        $this->assertTrue($type?->allowsNull());
    }

    /**
     * @depends testEqualsMethodHasOneParameter
     */
    public function testEqualsMethodParameterHasNoDefaultValue(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');
        $parameters = $method->getParameters();
        $parameter = array_shift($parameters);

        $this->assertFalse($parameter->isDefaultValueAvailable());
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodReturnTypeIsOnlyBool(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame('bool', $returnType->getName());
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodReturnTypeNotAllowsNull(): void
    {
        $method = new ReflectionMethod(EquivalentStringableInterface::class, 'equals');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(ReflectionType::class, $returnType);
        $this->assertFalse($returnType->allowsNull());
    }
}
