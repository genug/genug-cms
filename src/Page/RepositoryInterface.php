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

namespace genug\Page;

use Iterator;
use Countable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface RepositoryInterface extends Iterator, Countable
{
    public function fetch(string $id): Entity;

    public function fetchOrNull(string $id): ?Entity;
}
