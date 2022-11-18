<?php

declare(strict_types=1);

namespace genug\Page;

use genug\Category\ {
    Repository as CategoryRepository,
    Entity as CategoryEntity
};
use genug\Lib\abstract_FrontMatterFile;

use const genug\Persistence\FileSystem\Page\ {
    DIR as PAGE_DIR,
    FILENAME_EXTENSION as PAGE_FILENAME_EXTENSION,
    HOMEPAGE_FILENAME
};
use const genug\Setting\MAIN_CATEGORY_ID;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Generator
{
    /**
     *
     * @todo [b] error_log (and continue)
     */
    public static function generateEntities(): \Generator
    {
        $directories = new class (new \FilesystemIterator(PAGE_DIR)) extends \FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            try {
                $pageFiles = new class (new \FilesystemIterator($dir->getRealPath())) extends \FilterIterator {
                    public function accept(): bool
                    {
                        return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                    }
                };

                foreach ($pageFiles as $pageFile) {
                    try {
                        if (! $pageFile->isReadable()) {
                            throw new \Exception(); // [b]
                        }

                        $_data = new class ($pageFile->getRealPath()) extends abstract_FrontMatterFile {
                            protected function _parseFrontMatterString(string $str): array
                            {
                                return \parse_ini_string($str, false, \INI_SCANNER_TYPED);
                            }
                        };

                        $id = (function () use ($dir, $pageFile) {
                            $rtn = '';
                            if ($dir->getBasename() === MAIN_CATEGORY_ID && $pageFile->getBasename() === HOMEPAGE_FILENAME) {
                                $rtn = '/';
                            } elseif ($dir->getBasename() === MAIN_CATEGORY_ID) {
                                $rtn = '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                            } else {
                                $rtn = '/' . $dir->getBasename() . '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                            }
                            return $rtn;
                        })();

                        $title = (function () use ($_data) {
                            $fm = $_data->frontMatter();
                            if (! isset($fm['title'])) {
                                throw new \Exception();
                            }
                            return $fm['title'];
                        })();

                        $date = (function () use ($_data) {
                            $fm = $_data->frontMatter();
                            if (! isset($fm['date'])) {
                                throw new \Exception();
                            }
                            return $fm['date'];
                        })();

                        yield new Entity(new Id($id), new Category($dir->getBasename()), new Title($title), new Date($date), new Content($_data->content()));
                    } catch (\Throwable $t) {
                        throw $t; // [b]
                    }
                }
            } catch (\Throwable $t) {
                throw $t; // [b]
            }
        }
    }
}
