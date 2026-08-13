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
use SplFileInfo;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
interface PageEntity extends LoggerAwareInterface
{
    public PageId $id {get; set;}
    public SplFileInfo $file {get; set;}
    public Config $config {get; set;}
    public PageRepository $pages {get; set;}

    public function __construct();

    public function init(): void;

    // TODO use Attributes and Reflection; not abstract methods
    public function get(GetPageDto $dto): Response;
}
