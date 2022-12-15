<?php

declare(strict_types=1);

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
