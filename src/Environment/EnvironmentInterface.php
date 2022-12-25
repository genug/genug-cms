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

use genug\Page\AbstractId as AbstractPageId;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
interface EnvironmentInterface
{
    public function isDebug(): bool;

    public function debugLogFilePath(): string;

    public function pageContentType(): string;

    public function homePageId(): AbstractPageId;

    public function http404PageId(): AbstractPageId;

    public function contentDirectory(): string;
}
