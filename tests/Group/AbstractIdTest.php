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

namespace genug\Group;

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
final class AbstractIdTest extends TestCase
{
    public function testClassIsAbstract(): void
    {
        $class = new ReflectionClass(AbstractId::class);

        $this->assertTrue($class->isAbstract());
    }

    public function testClassImplementsStringable(): void
    {
        $class = new ReflectionClass(AbstractId::class);

        $this->assertTrue($class->implementsInterface(Stringable::class));
    }

    public function testEqualsMethodIsFinal(): void
    {
        $method = new ReflectionMethod(AbstractId::class, 'equals');

        $this->assertTrue($method->isFinal());
    }

    public function testEqualsMethodReturnTypeIsOnlyBool(): void
    {
        $method = new ReflectionMethod(AbstractId::class, 'equals');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame('bool', $returnType->getName());
    }

    public function testEqualsMethodReturnTypeNotAllowsNull(): void
    {
        $method = new ReflectionMethod(AbstractId::class, 'equals');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(ReflectionType::class, $returnType);
        $this->assertFalse($returnType->allowsNull());
    }

    public function testEqualIfSameInstance(): void
    {
        $obj = $this->initConcreteObject('id_123');

        $this->assertTrue($obj->equals($obj));
    }

    public function testNotEqualIfSameInstanceButDifferentStringRepresentation(): void
    {
        $obj = $this->initConcreteObjectWithRandomStringRepresentation();

        $this->assertFalse($obj->equals($obj));
    }

    public function testEqualIfOtherInstanceHasSameStringRepresentation(): void
    {
        $id = 'id_123';
        $obj1 = $this->initConcreteObject($id);
        $obj2 = $this->initConcreteObject($id);

        $this->assertSame($obj1::class, $obj2::class);
        $this->assertNotSame($obj1, $obj2);
        $this->assertTrue($obj1->equals($obj2));
    }

    public function testNotEqualIfOtherInstanceHasDifferentStringRepresentation(): void
    {
        $obj1 = $this->initConcreteObject('id_123');
        $obj2 = $this->initConcreteObject('id_456');

        $this->assertSame($obj1::class, $obj2::class);
        $this->assertNotSame($obj1, $obj2);
        $this->assertFalse($obj1->equals($obj2));
    }

    public function testNotEqualIfInstanceOfAnotherConcreteClass(): void
    {
        $obj1 = $this->initConcreteObjectWithRandomStringRepresentation();
        $obj2 = $this->getMockForAbstractClass(AbstractId::class);

        $this->assertInstanceOf(AbstractId::class, $obj1);
        $this->assertInstanceOf(AbstractId::class, $obj2);
        $this->assertNotSame($obj1::class, $obj2::class);
        $this->assertFalse($obj1->equals($obj2));
    }

    private function initConcreteObject(string $id): AbstractId
    {
        return new class ($id) extends AbstractId {
            public function __construct(protected readonly string $id)
            {
            }

            public function __toString(): string
            {
                return $this->id;
            }
        };
    }

    private function initConcreteObjectWithRandomStringRepresentation(): AbstractId
    {
        return new class () extends AbstractId {
            public function __toString(): string
            {
                return uniqid('id', true);
            }
        };
    }
}
