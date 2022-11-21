<?php

declare(strict_types=1);

namespace genug\Page;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity
{
    public function __construct(
        public readonly Id $id,
        public readonly Group $group,
        public readonly Title $title,
        public readonly Date $date,
        public readonly Content $content
    ) {
    }
}
