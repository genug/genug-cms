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

use genug\Group\Entity as GroupEntity;
use genug\Page\Entity as PageEntity;
use LogicException;
use WeakMap;

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

    public function fetchGroupOrNull(string $id): ?GroupEntity
    {
        $entity = $this->fetchOrNull(GroupEntity::class, $id);
        if (! ($entity instanceof GroupEntity)) {
            return null;
        }
        return $entity;
    }

    public function fetchPageOrNull(string $id): ?PageEntity
    {
        $entity = $this->fetchOrNull(PageEntity::class, $id);
        if (! ($entity instanceof PageEntity)) {
            return null;
        }
        return $entity;
    }

    protected function fetchOrNull(string $className, string $id): ?object
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
