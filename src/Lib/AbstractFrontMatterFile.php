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

namespace genug\Lib;

use BadMethodCallException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use SplFileInfo;

use function is_array;
use function preg_match;
use function str_replace;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
abstract class AbstractFrontMatterFile
{
    public const VALID_EOL = "\n";

    public const INVALID_EOLS = [
        "\r\n",
        "\r"
    ];

    private bool $_isMutable = true;

    private ?array $_frontMatter = null;

    private string $_frontMatterString;

    private string $_content;

    final public function __construct(protected readonly string $path, protected readonly LoggerInterface $logger)
    {
        if (! $this->_isMutable) {
            throw new BadMethodCallException();
        }
        $this->_isMutable = false;

        $file = new SplFileInfo($path);
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new InvalidArgumentException();
        }

        // ---

        $frontMatterString = '';
        $content = '';

        if ($file->getSize() > 0) {
            $fileContent = $file->openFile()->fread($file->getSize());
            $fileContent = str_replace(self::INVALID_EOLS, self::VALID_EOL, $fileContent);

            $matches = [];

            preg_match('#^(?:-{3}' . self::VALID_EOL . '([\s\S]*?)' . self::VALID_EOL . '-{3}(?:' . self::VALID_EOL . '|$))?([\s\S]*)$#', $fileContent, $matches);

            $frontMatterString = $matches[1];
            $content = $matches[2];
        }

        $this->_frontMatterString = $frontMatterString;
        $this->_content = $content;
    }

    final public function content(): string
    {
        return $this->_content;
    }

    final public function frontMatter(): array
    {
        if (! is_array($this->_frontMatter)) {
            $this->_frontMatter = $this->_parseFrontMatterString($this->_frontMatterString);
        }
        return $this->_frontMatter;
    }

    abstract protected function _parseFrontMatterString(string $str): array;
}
