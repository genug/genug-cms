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
use genug\Page\Id as PageId;
use Psr\Log\LoggerInterface;
use ReflectionEnum;

use function sprintf;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Environment implements EnvironmentInterface
{
    protected array $variables = [];

    public function __construct(
        protected readonly string $baseDir,
        protected readonly LoggerInterface $logger
    ) {
        foreach ((new ReflectionEnum(Preset::class))->getCases() as $reflectionEnumBackedCase) {
            $attributes = $reflectionEnumBackedCase->getAttributes(Value::class);
            if (! $attributes) {
                $logger->error(sprintf('Missing Attribute %s on %s::%s', Value::class, $reflectionEnumBackedCase->class, $reflectionEnumBackedCase->name));
                continue;
            }
            $attribute = $attributes[0];
            $typedValue = $attribute->newInstance();

            /** @var Preset */
            $preset = $reflectionEnumBackedCase->getValue();
            $this->variables[$reflectionEnumBackedCase->name] = $typedValue->from($preset);
        }
    }

    public function isDebug(): bool
    {
        return $this->variables['GENUG_DEBUG'];
    }

    public function debugLogFilePath(): string
    {
        return $this->baseDir.$this->variables['GENUG_DEBUG_LOGFILE'];
    }

    public function pageContentType(): string
    {
        return $this->variables['GENUG_CONTENT_TYPE'];
    }

    public function homePageId(): AbstractPageId
    {
        if (! ($this->variables['GENUG_HOMEPAGE_ID'] instanceof AbstractPageId)) {
            $this->variables['GENUG_HOMEPAGE_ID'] = new PageId($this->variables['GENUG_HOMEPAGE_ID']);
        }
        return $this->variables['GENUG_HOMEPAGE_ID'];
    }

    public function http404PageId(): AbstractPageId
    {
        if (! ($this->variables['GENUG_HTTP404PAGE_ID'] instanceof AbstractPageId)) {
            $this->variables['GENUG_HTTP404PAGE_ID'] = new PageId($this->variables['GENUG_HTTP404PAGE_ID']);
        }
        return $this->variables['GENUG_HTTP404PAGE_ID'];
    }

    public function viewFilePath(): string
    {
        return $this->baseDir.$this->variables['GENUG_VIEW_FILE'];
    }

    public function persistenceContentDirectory(): string
    {
        return $this->baseDir.$this->variables['GENUG_PERSISTENCE_CONTENT_DIR'];
    }

    public function persistenceGroupFilename(): string
    {
        return $this->variables['GENUG_PERSISTENCE_GROUP_FILENAME'];
    }

    public function persistencePageFilenameExtesion(): string
    {
        return $this->variables['GENUG_PERSISTENCE_PAGE_FILENAMEEXTENSION'];
    }

    public function persistencePageHomePageFilename(): string
    {
        return $this->variables['GENUG_PERSISTENCE_PAGE_HOMEPAGE_FILENAME'];
    }
}
