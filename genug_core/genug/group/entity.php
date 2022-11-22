<?php

declare(strict_types=1);

namespace genug\Group;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity
{
    public function __construct(
        public readonly IdInterface $id,
        public readonly TitleInterface $title
    ) {
    }
}
