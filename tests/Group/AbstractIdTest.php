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

use genug\Lib\EquivalentStringableInterface;
use genug\Lib\EquivalentStringableTrait;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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
    public function testIsAbstract(): void
    {
        $class = new ReflectionClass(AbstractId::class);

        $this->assertTrue($class->isAbstract());
    }

    public function testImplementsStringable(): void
    {
        $class = new ReflectionClass(AbstractId::class);

        $this->assertTrue($class->implementsInterface(Stringable::class));
    }

    public function testImplementsEquivalentStringableInterface(): void
    {
        $class = new ReflectionClass(AbstractId::class);

        $this->assertTrue($class->implementsInterface(EquivalentStringableInterface::class));
    }

    public function testUsesEquivalentStringableTrait(): void
    {
        $class = new ReflectionClass(AbstractId::class);
        $traitNames = $class->getTraitNames();

        $this->assertTrue(in_array(EquivalentStringableTrait::class, $traitNames, true));
    }
}
