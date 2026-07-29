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

namespace genug\Environment;

use genug\Page\AbstractId as AbstractPageId;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 *
 * @deprecated Use Config instead.
 */
interface EnvironmentInterface
{
    public function pageContentType(): string;

    public function homePageId(): AbstractPageId;

    public function http404PageId(): AbstractPageId;

    public function viewFilePath(): string;

    public function persistenceContentDirectory(): string;

    public function persistenceGroupFilename(): string;

    public function persistencePageFilenameExtension(): string;

    public function persistencePageHomePageFilename(): string;
}
