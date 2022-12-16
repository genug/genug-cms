<?php

declare(strict_types=1);

namespace genug\Request;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface RequestInterface
{
    public function pageId(): string;
}
