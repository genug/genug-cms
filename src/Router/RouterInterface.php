<?php

declare(strict_types=1);

namespace genug\Router;

use genug\Page\Entity as PageEntity;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface RouterInterface
{
    public function result(): PageEntity;
}
