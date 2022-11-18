<?php

declare(strict_types=1);

namespace genug\Category;

use const genug\Persistence\FileSystem\Category\ {
    DIR as CATEGORY_DIR,
    FILENAME as CATEGORY_FILENAME
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
     * @todo [a] better Exception
     * @todo [b] better Exception
     * @todo [c] error_log and continue
     */
    public static function generateEntities(): \Generator
    {
        $directories = new class (new \FilesystemIterator(CATEGORY_DIR)) extends \FilterIterator {
            public function accept(): bool
            {
                return parent::current()->isDir();
            }
        };

        foreach ($directories as $dir) {
            try {
                $file = new \SplFileInfo($dir->getRealPath() . '/' . CATEGORY_FILENAME);
                if (! $file->isFile() || ! $file->isReadable()) {
                    throw new \Exception($file->getFilename()); // [a]
                }

                $data = \parse_ini_file($file->getRealPath(), false, INI_SCANNER_TYPED);
                if (! isset($data['title'])) {
                    throw new \Exception(); // [b]
                }

                yield new Entity(new Id($dir->getBasename()), new Title($data['title']));
            } catch (\Throwable $t) {
                throw $t; // [c]
            }
        }
    }
}
