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

namespace genug\Page;

use ArrayIterator;
use ArrayObject;
use FilesystemIterator;
use FilterIterator;
use Generator;
use genug\Environment\Environment;
use genug\Lib\AbstractFrontMatterFile;
use genug\Lib\EntityCache;
use InvalidArgumentException;
use Iterator;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Throwable;

use function count;
use function gettype;
use function sprintf;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Repository implements RepositoryInterface
{
    /** @var ArrayObject<string, object{path: string, group: null|string}> */
    private readonly ArrayObject $idToInfoMap;
    /** @var ArrayIterator<string, object{path: string, group: null|string}> */
    private readonly ArrayIterator $iterator;

    public function __construct(
        private readonly EntityCache $entityCache,
        protected readonly Environment $environment,
        private readonly LoggerInterface $logger
    ) {
        $this->idToInfoMap = $this->createIdToInfoMap();
        $this->iterator = $this->idToInfoMap->getIterator();
    }

    public function fetch(string $id): Entity
    {
        try {
            return $this->_fetch($id);
        } catch (Throwable $t) {
            $this->logger->alert(
                'Fetching a page from the repository fails.',
                [
                    'method' => __METHOD__,
                    'retrieved_id' => $id,
                    'throwable' => $t,
                ]
            );
            throw new EntityNotFound();
        }
    }

    public function fetchOrNull(?string $id): ?Entity
    {
        if (null === $id) {
            $this->logger->debug(
                'Null was passed as the ID to be fetched.',
                [
                    'method' => __METHOD__
                ]
            );
            return null;
        }
        try {
            return $this->_fetch($id);
        } catch (Throwable $t) {
            $this->logger->debug(
                'NULL will be returned after fetching a page from the repository failed.',
                [
                    'method' => __METHOD__,
                    'retrieved_id' => $id,
                    'throwable' => $t,
                ]
            );
            return null;
        }
    }

    public function fetchByGroup(?string $group): Generator
    {
        foreach ($this->idToInfoMap as $id => $item) {
            if ($item->group === $group) {
                yield $this->fetch($id);
            }
        }
    }

    protected function _fetch(string $id): Entity
    {
        if (! $this->idToInfoMap->offsetExists($id)) {
            throw new InvalidArgumentException();
        }
        $cachedEntity = $this->entityCache->fetchOrNull(new Id($id));
        if (null === $cachedEntity) {
            return $this->createAndCacheEntity($id);
        }
        if ($cachedEntity::class !== Entity::class) {
            throw new LogicException();
        }
        return $cachedEntity;
    }

    public function count(): int
    {
        return count($this->idToInfoMap);
    }

    public function current(): Entity
    {
        try {
            return $this->fetch($this->iterator->key());
        } catch (EntityNotFound $t) {
            throw new RuntimeException(previous: $t);
        }
    }

    public function key(): string
    {
        return $this->iterator->key();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    protected function createAndCacheEntity(string $idString): Entity
    {
        $logger = $this->logger;

        $pageFile = new SplFileInfo($this->idToInfoMap->offsetGet($idString)->path);

        $dir = $pageFile->getPathInfo();
        if (null === $dir) {
            throw new RuntimeException();
        }

        $group = (function () use ($dir): ?Group {
            $contentDir = new SplFileInfo($this->environment->persistenceContentDirectory());
            if ($dir->getRealPath() === $contentDir->getRealPath()) {
                return null;
            }
            return new Group($dir->getBasename());
        })();

        $_data = new class ($pageFile->getRealPath(), $logger) extends AbstractFrontMatterFile {
            protected function _parseFrontMatterString(string $str): array
            {
                if (empty($str)) {
                    $this->logger->debug(
                        'No page front matter.',
                        ['file' => $this->path]
                    );
                    return [];
                }

                $data = Yaml::parse($str);
                if (! is_array($data)) {
                    $this->logger->warning(
                        'Invalid page front matter.',
                        ['file' => $this->path]
                    );
                    return [];
                }
                return $data;
            }
        };

        $title = (function () use ($_data, $idString, $logger): ?Title {
            $fm = $_data->frontMatter();
            if (! isset($fm['title'])) {
                $logger->debug(
                    sprintf('No title found for Page "%s"', $idString)
                );
                return null;
            }
            if (! is_string($fm['title'])) {
                $logger->warning(
                    sprintf('Invalid title for Page "%s". `string` expected, `%s` received.', $idString, gettype($fm['title']))
                );
                return null;
            }
            return new Title($fm['title']);
        })();

        $dateTime = (function () use ($_data, $idString, $logger): ?DateTime {
            $fm = $_data->frontMatter();
            if (! isset($fm['date'])) {
                $logger->debug(
                    sprintf('No date found for Page "%s"', $idString)
                );
                return null;
            }
            if (! is_string($fm['date'])) {
                $logger->warning(
                    sprintf('Invalid date for Page "%s". `string` expected, `%s` received.', $idString, gettype($fm['date']))
                );
                return null;
            }
            return new DateTime($fm['date']);
        })();

        $entity = new Entity(
            new Id($idString),
            $group,
            $title,
            $dateTime,
            new Content($_data->content())
        );

        try {
            $this->entityCache->attach($entity);
        } catch (Throwable $t) {
            $this->logger->error(
                'Caching of the page entity fails.',
                [
                    'method' => __METHOD__,
                    'entity' => $entity,
                    'throwable' => $t,
                ]
            );
        }
        return $entity;
    }

    /**
     * @return ArrayObject<string, object{path: string, group: null|string}>
     */
    protected function createIdToInfoMap(): ArrayObject
    {
        /** @var ArrayObject<string, object{path: string, group: null|string}> */
        $idToInfoMap = new ArrayObject();

        // pages without group

        $pageFiles = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator($this->environment->persistenceContentDirectory()), $this->environment) extends FilterIterator {
            public function __construct(
                Iterator $iterator,
                protected readonly Environment $environment
            ) {
                parent::__construct($iterator);
            }

            public function accept(): bool
            {
                return parent::current()->isFile() && parent::current()->getExtension() === $this->environment->persistencePageFilenameExtesion();
            }
        };

        foreach ($pageFiles as $pageFile) {
            $id = (function () use ($pageFile): string {
                if ($pageFile->getBasename() === $this->environment->persistencePageHomePageFilename()) {
                    return '/';
                }
                return '/' . $pageFile->getBasename('.' . $this->environment->persistencePageFilenameExtesion());
            })();

            $value = new class ($pageFile->getRealPath(), null) {
                public function __construct(
                    public readonly string $path,
                    public readonly ?string $group
                ) {
                }
            };

            $idToInfoMap->offsetSet($id, $value);
        }

        // pages with group

        $directories = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator($this->environment->persistenceContentDirectory())) extends FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            $pageFiles = new /** @extends \FilterIterator<string, \SplFileInfo, \Traversable<string, \SplFileInfo>> */ class (new FilesystemIterator($dir->getRealPath()), $this->environment) extends FilterIterator {
                public function __construct(
                    Iterator $iterator,
                    protected readonly Environment $environment
                ) {
                    parent::__construct($iterator);
                }

                public function accept(): bool
                {
                    return parent::current()->isFile() && parent::current()->getExtension() === $this->environment->persistencePageFilenameExtesion();
                }
            };

            foreach ($pageFiles as $pageFile) {
                $id = (function () use ($dir, $pageFile) {
                    return '/' . $dir->getBasename() . '/' . $pageFile->getBasename('.' . $this->environment->persistencePageFilenameExtesion());
                })();

                $value = new class ($pageFile->getRealPath(), $dir->getBasename()) {
                    public function __construct(
                        public readonly string $path,
                        public readonly ?string $group
                    ) {
                    }
                };

                $idToInfoMap->offsetSet($id, $value);
            }
        }
        return $idToInfoMap;
    }
}
