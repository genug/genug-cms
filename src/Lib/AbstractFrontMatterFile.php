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

    private $_isMutable = true;

    private $_frontMatter;

    private $_frontMatterString;

    private $_content;

    final public function __construct(string $path)
    {
        if (! $this->_isMutable) {
            throw new \BadMethodCallException();
        }
        $this->_isMutable = false;

        $file = new \SplFileInfo($path);
        if (! $file->isFile() || ! $file->isReadable()) {
            throw new \InvalidArgumentException();
        }

        // ---

        $fileContent = $file->openFile()->fread($file->getSize());
        $fileContent = \str_replace(self::INVALID_EOLS, self::VALID_EOL, $fileContent);

        $matches = [];

        \preg_match('#^(?:-{3}' . self::VALID_EOL . '([\s\S]*?)' . self::VALID_EOL . '-{3}(?:' . self::VALID_EOL . '|$))?([\s\S]*)$#', $fileContent, $matches);

        $this->_frontMatterString = $matches[1];
        $this->_content = $matches[2];
    }

    final public function content(): string
    {
        return $this->_content;
    }

    final public function frontMatter(): array
    {
        if (! \is_array($this->_frontMatter)) {
            $this->_frontMatter = $this->_parseFrontMatterString($this->_frontMatterString);
        }
        return $this->_frontMatter;
    }

    abstract protected function _parseFrontMatterString(string $str): array;
}
