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

namespace genug\Environment;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 * @internal
 */
enum Type
{
    case Bool;
    case String;
    case FilePath;
    case IdString;
}
