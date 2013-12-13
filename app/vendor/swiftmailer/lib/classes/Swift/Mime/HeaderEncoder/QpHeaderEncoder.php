<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles Quoted Printable (Q) Header Encoding in Swift Mailer.
 *
 * @package    Swift
 * @subpackage Mime
 * @author     Chris Corbyn
 */
class Swift_Mime_HeaderEncoder_QpHeaderEncoder extends Swift_Encoder_QpEncoder implements Swift_Mime_HeaderEncoder
{
    /**
     * Creates a new QpHeaderEncoder for the given CharacterStream.
     *
     * @param Swift_CharacterStream $charStream to use for reading characters
     */
    public function __construct(Swift_CharacterStream $charStream)
    {
        parent::__construct($charStream);
    }

    protected function initSafeMap()
    {
		$map = array(32, 33, 42, 43, 45, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122);
        foreach ($map as $byte) {
            $this->_safeMap[$byte] = chr($byte);
        }
    }

    /**
     * Get the name of this encoding scheme.
     *
     * Returns the string 'Q'.
     *
     * @return string
     */
    public function getName()
    {
        return 'Q';
    }

    /**
     * Takes an unencoded string and produces a QP encoded string from it.
     *
     * @param string  $string          string to encode
     * @param integer $firstLineOffset optional
     * @param integer $maxLineLength   optional, 0 indicates the default of 76 chars
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0)
    {
        return str_replace(array(' ', '=20', "=\r\n"), array('_', '_', "\r\n"),
            parent::encodeString($string, $firstLineOffset, $maxLineLength)
        );
    }
}
