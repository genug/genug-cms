<?php
declare(strict_types = 1);
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

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Generator
{

    /**
     *
     * @todo [a] error_log [notice]
     * @todo [b] error_log (and continue)
     */
    public static function generateEntities(CategoryRepository $categories, CategoryEntity $mainCategory): \Generator
    {
        foreach ($categories as $category) {
            try {
                $dir = new \SplFileInfo(PAGE_DIR . '/' . $category->id()->__toString());
                if (! $dir->isDir()) {
                    // [a]
                    continue;
                }
                
                $pageFiles = new class(new \FilesystemIterator($dir->getRealPath())) extends \FilterIterator {

                    public function accept()
                    {
                        return parent::current()->isFile() && parent::current()->getExtension() === PAGE_FILENAME_EXTENSION;
                    }
                };
                
                foreach ($pageFiles as $pageFile) {
                    try {
                        if (! $pageFile->isReadable()) {
                            throw new \Exception(); // [b]
                        }
                        
                        $_data = new class($pageFile->getRealPath()) extends abstract_FrontMatterFile {

                            protected function _parseFrontMatterString(string $str): array
                            {
                                return \parse_ini_string($str, FALSE, \INI_SCANNER_TYPED);
                            }
                        };
                        
                        $id = (function () use ($category, $mainCategory, $pageFile) {
                            $rtn = '';
                            if ($category === $mainCategory && $pageFile->getBasename() === HOMEPAGE_FILENAME) {
                                $rtn = '/';
                            } elseif ($category === $mainCategory) {
                                $rtn = '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
                            } else {
                                $rtn = '/' . $category->id()->__toString() . '/' . $pageFile->getBasename('.' . PAGE_FILENAME_EXTENSION);
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
                        
                        yield new Entity(new Id($id), $category, new Title($title), new Date($date), new Content($_data->content()));
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