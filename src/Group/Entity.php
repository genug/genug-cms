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

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Entity extends AbstractEntity
{
    public function __construct(
        public readonly AbstractId $id,
        public readonly ?TitleInterface $title
    ) {
    }
}
