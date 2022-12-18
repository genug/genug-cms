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

use genug\Group\AbstractEntity as AbstractGroupEntity;
use genug\Group\AbstractId as AbstractGroupId;
use genug\Page\AbstractEntity as AbstractPageEntity;
use genug\Page\AbstractId as AbstractPageId;
use LogicException;
use WeakMap;

final class EntityCache
{
    /** @var WeakMap<AbstractPageEntity|AbstractGroupEntity, AbstractPageId|AbstractGroupId> */
    protected readonly WeakMap $weakMap;

    public function __construct()
    {
        /** @var WeakMap<AbstractPageEntity|AbstractGroupEntity, AbstractPageId|AbstractGroupId> */
        $this->weakMap = new WeakMap();
    }

    public function attach(AbstractPageEntity|AbstractGroupEntity $entity): void
    {
        if ((bool) $this->fetchOrNull($entity->id)) {
            throw new LogicException('Entity is already cached.');
        }
        $this->weakMap->offsetSet($entity, $entity->id);
    }

    public function fetchOrNull(AbstractPageId|AbstractGroupId $id): null|AbstractPageEntity|AbstractGroupEntity
    {
        foreach ($this->weakMap as $cObj => $cId) {
            if ($id->equals($cId)) {
                return $cObj;
            }
        }
        return null;
    }
}
