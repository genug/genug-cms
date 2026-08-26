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

namespace genug\HttpResponceException;

use Exception;
use genug\Http\Status;

abstract class HttpResponceException extends Exception
{
    final public protected(set) Status $status;
}
