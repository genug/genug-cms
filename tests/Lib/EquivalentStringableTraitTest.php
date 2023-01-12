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

namespace genug\Lib;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use Stringable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 *
 * @psalm-suppress MissingConstructor
 */
final class EquivalentStringableTraitTest extends TestCase
{
    public function testIsTrait(): void
    {
        $class = new ReflectionClass(EquivalentStringableTrait::class);

        $this->assertTrue($class->isTrait());
    }

    public function testHasEqualsMethod(): void
    {
        $class = new ReflectionClass(EquivalentStringableTrait::class);

        $this->assertTrue($class->hasMethod('equals'));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodIsFinal(): void
    {
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');

        $this->assertTrue($method->isFinal());
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodHasOneParameter(): void
    {
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');

        $this->assertSame(1, $method->getNumberOfParameters());
    }

    /**
     * @depends testEqualsMethodHasOneParameter
     */
    public function testEqualsMethodParameterNamedObject(): void
    {
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');
        $parameters = $method->getParameters();
        $parameter = array_shift($parameters);

        $this->assertSame('object', $parameter->getName());
    }

    /**
     * @depends testEqualsMethodHasOneParameter
     */
    public function testEqualsMethodParameterTypeIsStringable(): void
    {
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');
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
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');
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
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');
        $parameters = $method->getParameters();
        $parameter = array_shift($parameters);

        $this->assertFalse($parameter->isDefaultValueAvailable());
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodReturnTypeIsOnlyBool(): void
    {
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame('bool', $returnType->getName());
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualsMethodReturnTypeNotAllowsNull(): void
    {
        $method = new ReflectionMethod(EquivalentStringableTrait::class, 'equals');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(ReflectionType::class, $returnType);
        $this->assertFalse($returnType->allowsNull());
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualIfSameInstance(): void
    {
        $obj = $this->initConcreteObject('id_123');

        $this->assertTrue($obj->equals($obj));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testNotEqualIfSameInstanceButDifferentStringRepresentation(): void
    {
        $obj = $this->initConcreteObjectWithRandomStringRepresentation();

        $this->assertFalse($obj->equals($obj));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testEqualIfOtherInstanceHasSameStringRepresentation(): void
    {
        $id = 'id_123';
        $obj1 = $this->initConcreteObject($id);
        $obj2 = $this->initConcreteObject($id);

        $this->assertSame($obj1::class, $obj2::class);
        $this->assertNotSame($obj1, $obj2);
        $this->assertTrue($obj1->equals($obj2));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testNotEqualIfOtherInstanceHasDifferentStringRepresentation(): void
    {
        $obj1 = $this->initConcreteObject('id_123');
        $obj2 = $this->initConcreteObject('id_456');

        $this->assertSame($obj1::class, $obj2::class);
        $this->assertNotSame($obj1, $obj2);
        $this->assertFalse($obj1->equals($obj2));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testNotEqualIfInstanceOfAnotherConcreteClass(): void
    {
        $obj1 = $this->initConcreteObjectWithRandomStringRepresentation();
        $obj2 = $this->createMock(EquivalentStringableInterface::class);

        $this->assertInstanceOf(EquivalentStringableInterface::class, $obj1);
        $this->assertInstanceOf(EquivalentStringableInterface::class, $obj2);
        $this->assertNotSame($obj1::class, $obj2::class);
        $this->assertFalse($obj1->equals($obj2));
    }

    /**
     * @depends testHasEqualsMethod
     */
    public function testNotEqualIfNull(): void
    {
        $obj = $this->initConcreteObject('id_123');

        $this->assertFalse($obj->equals(null));
    }

    private function initConcreteObject(string $id): EquivalentStringableInterface
    {
        return new class ($id) implements EquivalentStringableInterface {
            use EquivalentStringableTrait;

            public function __construct(protected readonly string $id)
            {
            }

            public function __toString(): string
            {
                return $this->id;
            }
        };
    }

    private function initConcreteObjectWithRandomStringRepresentation(): EquivalentStringableInterface
    {
        return new class () implements EquivalentStringableInterface {
            use EquivalentStringableTrait;

            public function __toString(): string
            {
                return uniqid('id', true);
            }
        };
    }
}
