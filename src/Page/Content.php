<?php

declare(strict_types=1);

namespace genug\Page;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Content implements ContentInterface
{
    use \genug\Lib\ValueObject\TrimmedStringTrait;
}