<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\DevBundle\Twig;

/**
 * Lexes a template string.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class Token extends \Twig_Token
{
    const WHITESPACE_TYPE = 12;

    static public function typeToEnglish($type, $line = -1)
    {
        switch ($type) {
            case self::WHITESPACE_TYPE:
                return 'whitespace';
        }

        return parent::typeToEnglish($type, $line);
    }

    static public function typeToString($type, $short = false, $line = -1)
    {
        switch ($type) {
            case self::WHITESPACE:
                return 'WHITESPACE_TYPE';
        }

        return parent::typeToString($type, $short, $line);
    }

}
