<?php
declare(strict_types = 1);
namespace genug\Page;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Id
{
    use \genug\Lib\trait_Id;

    const VALID_STRING_PATTERN = '#^(?:\/|(?:\/[a-z0-9][a-z0-9_\-]*){1,2})$#';
}