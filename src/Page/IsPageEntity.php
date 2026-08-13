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
use genug\Http\HttpResponceException;
use genug\Http\Response;
use genug\Http\Status;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplFileInfo;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 * @phpstan-require-implements LoggerAwareInterface
 * @phpstan-require-implements PageEntity
 */
trait IsPageEntity
{
    use LoggerAwareTrait;

    public PageId $id;
    public SplFileInfo $file;
    public Config $config;
    public PageRepository $pages;

    public function __construct()
    {
    }

    public function init(): void
    {
    }

    public function get(GetPageDto $dto): Response
    {
        throw new HttpResponceException(Status::MethodNotAllowed);
    }
}
