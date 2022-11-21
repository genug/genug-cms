<?php

declare(strict_types=1);

namespace genug\Lib;

use genug\Page\{
    Entity as PageEntity,
    Id as PageId
};
use genug\Category\{
    Entity as CategoryEntity,
    Id as CategoryId
};
use WeakMap;
use LogicException;

final class EntityAndIdCache
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
        if ((bool) $this->fetchOrNull($entity::class, (string) $entity->id)) {
            throw new LogicException('Entity is already cached.');
        }
        if ($this->isAnotherInstanceOfIdValueInWeakMap($entity)) {
            throw new LogicException('Another instance of the ID value already exists.');
        }

        $this->weakMap->offsetSet($entity, (string) $entity->id);

        if (null === $this->fetchOrNull($entity->id::class, (string) $entity->id)) {
            $this->weakMap->offsetSet($entity->id, (string) $entity->id);
        }
    }

    public function fetchOrNull(string $className, string $id): null|PageEntity|CategoryEntity|PageId|CategoryId
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

    protected function isAnotherInstanceOfIdValueInWeakMap(PageEntity|CategoryEntity $needle): bool
    {
        $IdOrNull = $this->fetchOrNull($needle->id::class, (string) $needle->id);
        return ($IdOrNull !== null && $IdOrNull !== $needle->id);
    }
}
