<?php

declare(strict_types=1);

namespace genug\Lib;

use genug\Page\Entity as PageEntity;
use genug\Group\Entity as GroupEntity;
use WeakMap;
use LogicException;

final class EntityCache
{
    protected readonly WeakMap $weakMap;

    public function __construct()
    {
        $this->weakMap = new WeakMap();
    }

    public function attach(PageEntity|GroupEntity $entity): void
    {
        if ((bool) $this->fetchOrNull($entity::class, (string) $entity->id)) {
            throw new LogicException('Entity is already cached.');
        }
        $this->weakMap->offsetSet($entity, (string) $entity->id);
    }

    public function fetchOrNull(string $className, string $id): null|PageEntity|GroupEntity
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
}
