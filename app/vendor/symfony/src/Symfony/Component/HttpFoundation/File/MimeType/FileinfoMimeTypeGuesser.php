<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\File\MimeType;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * Guesses the mime type using the PECL extension FileInfo
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 */
class FileinfoMimeTypeGuesser implements MimeTypeGuesserInterface
{
    /**
     * Returns whether this guesser is supported on the current OS/PHP setup
     *
     * @return Boolean
     */
    public static function isSupported()
    {
        return function_exists('finfo_open');
    }

    /**
     * Guesses the mime type of the file with the given path
     *
     * @see MimeTypeGuesserInterface::guess()
     */
    public function guess($path)
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        if (!is_readable($path)) {
            throw new AccessDeniedException($path);
        }

        if (!self::isSupported()) {
            return null;
        }

		// Patch from https://github.com/cursedcoder/symfony/blob/efe2e23f7f911b0a2713160ff7f007ab622e4b36/src/Symfony/Component/HttpFoundation/File/MimeType/FileinfoMimeTypeGuesser.php
        if (!$finfo = new \finfo(FILEINFO_MIME)) {
            return null;
        }

        $mime = explode(";", $finfo->file($path));

        return $mime[0];
    }
}
