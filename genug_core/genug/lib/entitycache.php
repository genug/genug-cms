<?php

declare(strict_types=1);

namespace genug\Lib;

use genug\Page\Entity as PageEntity;
use genug\Category\Entity as CategoryEntity;
use WeakMap;
use LogicException;

final class EntityCache
{
    protected static self $instance;

    protected readonly WeakMap $weakMap;

    public static function instance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        $this->weakMap = new WeakMap();
    }

    public function attach(PageEntity|CategoryEntity $entity): void
    {
        if (self::inWeakMap($entity, $this->weakMap)) {
            throw new LogicException('Item is already cached.');
        }
        $this->weakMap->offsetSet($entity, (string) $entity->id);
    }

    public function fetchOrNull(string $id, string $className): null|PageEntity|CategoryEntity
    {
        foreach ($this->weakMap as $obj => $idString) {
            if (
                $idString === $id
                && $obj::class === $className
            ) {
                return $obj;
            }
        }
        return null;
    }

    protected static function inWeakMap(PageEntity|CategoryEntity $needle, WeakMap $haystack): bool
    {
        foreach ($haystack as $obj => $idString) {
            if (
                $needle->id->__toString() === $idString
                && $needle::class === $obj::class
            ) {
                return true;
            }
        }
        return false;
    }
}
