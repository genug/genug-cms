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
        public readonly IdInterface $id,
        public readonly GroupInterface $group,
        public readonly TitleInterface $title,
        public readonly DateInterface $date,
        public readonly ContentInterface $content
    ) {
    }

    public function equals(self $pageEntity): bool
    {
        return ($pageEntity === $this);
    }
}
