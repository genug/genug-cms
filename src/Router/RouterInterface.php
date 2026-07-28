<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Router;

use genug\Page\AbstractEntity as AbstractPageEntity;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
interface RouterInterface
{
    public function result(): AbstractPageEntity;
}
