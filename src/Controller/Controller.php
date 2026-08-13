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

namespace genug\Controller;

use genug\Http\Request;
use genug\Http\Response;
use Psr\Log\LoggerAwareInterface;

interface Controller extends LoggerAwareInterface
{
    public function handle(Request $request): Response;
}
