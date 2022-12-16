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

namespace genug\Environment;

use Attribute;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
enum Type
{
    case Bool;
    case String;
    case FilePath;
    case IdString;
}
