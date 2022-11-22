<?php

declare(strict_types=1);

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
}
