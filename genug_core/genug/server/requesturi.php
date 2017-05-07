<?php
declare(strict_types = 1);
namespace genug\Server;

use const genug\Api\URL_PATH_BASE;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
class RequestUri
{

    private static $_path;

    public static function path(): string
    {
        if (\is_null(self::$_path)) {
            $path = \parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            if (URL_PATH_BASE !== '') {
                $pattern = '#^' . \preg_quote(URL_PATH_BASE, '#') . '#';
                
                $path = \preg_replace($pattern, '', $path, 1);
            }
            
            self::$_path = $path;
        }
        return self::$_path;
    }
}