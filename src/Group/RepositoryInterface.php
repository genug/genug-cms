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

use Countable;
use Iterator;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 *
 * @extends Iterator<string, AbstractEntity>
 */
interface RepositoryInterface extends Iterator, Countable
{
    public function fetch(string $id): AbstractEntity;

    public function fetchOrNull(?string $id): ?AbstractEntity;
}
