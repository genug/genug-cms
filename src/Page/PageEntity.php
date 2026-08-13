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

namespace genug\Page;

use genug\Config\Config;
use genug\Http\Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplFileInfo;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
abstract class PageEntity implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        public readonly PageId $id,
        protected readonly SplFileInfo $sourceFile,
        protected readonly Config $config,
    ) {
    }

    // TODO use Attributes and Reflection; not abstract methods
    abstract public function get(GetPageRequest $request): Response;
}
