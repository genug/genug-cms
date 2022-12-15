<?php

declare(strict_types=1);

namespace genug\Environment;

use Psr\Log\LoggerInterface;
use ReflectionEnum;
use genug\Group\{
    IdInterface as GroupIdInterface,
    Id as GroupId
};
use genug\Page\{
    IdInterface as PageIdInterface,
    Id as PageId
};

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

            $this->variables[$reflectionEnumBackedCase->name] = $typedValue->from($reflectionEnumBackedCase->getValue());
        }
    }

    public function isDebug(): bool
    {
        return $this->variables['GENUG_DEBUG'];
    }

    public function debugLogFilePath(): string
    {
        return $this->variables['GENUG_DEBUG_LOGFILE'];
    }

    public function mainGroupId(): GroupIdInterface
    {
        if (! ($this->variables['GENUG_MAINGROUP_ID'] instanceof GroupId)) {
            $this->variables['GENUG_MAINGROUP_ID'] = new GroupId($this->variables['GENUG_MAINGROUP_ID']);
        }
        return $this->variables['GENUG_MAINGROUP_ID'];
    }

    public function homePageId(): PageIdInterface
    {
        if (! ($this->variables['GENUG_HOMEPAGE_ID'] instanceof PageId)) {
            $this->variables['GENUG_HOMEPAGE_ID'] = new PageId($this->variables['GENUG_HOMEPAGE_ID']);
        }
        return $this->variables['GENUG_HOMEPAGE_ID'];
    }

    public function http404PageId(): PageIdInterface
    {
        if (! ($this->variables['GENUG_HTTP404PAGE_ID'] instanceof PageId)) {
            $this->variables['GENUG_HTTP404PAGE_ID'] = new PageId($this->variables['GENUG_HTTP404PAGE_ID']);
        }
        return $this->variables['GENUG_HTTP404PAGE_ID'];
    }
}
