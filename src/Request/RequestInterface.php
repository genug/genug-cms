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

namespace genug\Request;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
interface RequestInterface
{
    public function pageId(): string;
}
