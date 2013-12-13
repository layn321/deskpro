<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
* DeskPRO
*
* @package DeskPRO
*/

namespace Application\DevBundle\Language;

class PhraseFinder
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getPhrasesFromTwigFiles($bundle = null, &$by_id, &$errors, &$prefixes)
    {
        $twig = Language::GetTwigLexer($this->container);
        $templates = Language::GetFileFinder()->getTwigFileList($bundle);
        $cache_path = DP_ROOT.'/sys/cache/twig.phrase.cache';

        if(file_exists($cache_path)) {
            $cache = json_decode(file_get_contents($cache_path), true);
        }
        else {
            $cache = array();
        }

        foreach($templates as $file) {
            if(isset($cache[$file]) && filemtime($file) == $cache[$file]['mtime']) {
                foreach($cache[$file]['prefixes'] as $prefix) {
                    list($id, $line) = $prefix;
                    $prefixes[] = array('id' => $id, 'filename' => $file, 'line' => $line);
                }

                if(isset($cache[$file]['errors'])) {
                    $errors[$file] = $cache[$file]['errors'];
                }

                foreach($cache[$file]['phrases'] as $phrase) {
                    list($id, $line) = $phrase;

                    if(!isset($by_id[$id])) {
                        $by_id[$id] = array();
                    }

                    $by_id[$id][] = array(
                        'filename' => $file,
                        'line' => $line
                    );
                }

                continue;
            }

            $raw_twig = file_get_contents($file);
            $tokens = $twig->tokenize($raw_twig);
            $state = 0;
            $cache[$file] = array('phrases' => array(), 'prefixes' => array(), 'mtime' => filemtime($file));

            while(!$tokens->isEOF()) {
                $token = $tokens->next();
                $type = $token->getType();
                $value = $token->getValue();
                $line = $token->getLine();

                switch($state) {
                    case 0:
                        if($type == \Twig_Token::NAME_TYPE && $value == 'phrase') {
                            $state++;
                        }

                        break;
                    case 1:
                        if($type == \Twig_Token::PUNCTUATION_TYPE && $value == '(') {
                            $state++;
                        }
                        else {
                            $state = 0;

                            if(!isset($errors[$file])) {
                                $cache[$file]['errors'] =
                                $errors[$file] = array();
                            }

                            $cache[$file]['errors'][] =
                            $errors[$file][] = $this->tokenWarningTwig('Unexpected token', $token, $file, $line);
                        }

                        break;
                    case 2:
                        if($type == \Twig_Token::STRING_TYPE) {
                            $state++;
                            $id = $value;
                        }
                        else {
                            $state = 0;

                            if(!isset($errors[$file])) {
                                $cache[$file]['errors'] =
                                $errors[$file] = array();
                            }

                            $cache[$file]['errors'][] =
                            $errors[$file][] = $this->tokenWarningTwig('Unexpected token', $token, $file, $line);
                        }

                        break;
                    case 3:
                        if($type != \Twig_Token::PUNCTUATION_TYPE || ($value != ')' && $value != ',')) {
                            $cache[$file]['prefixes'][] = array($id, $line);
                            $prefixes[] = array('id' => $id, 'filename' => $file, 'line' => $line);
                        }
                        else {
                            $cache[$file]['phrases'][] = array($id, $line);

                            if(!isset($by_id[$id])) {
                                $by_id[$id] = array();
                            }

                            $by_id[$id][] = array(
                                'filename' => $file,
                                'line' => $line
                            );

                            if(!isset($by_file[$file])) {
                                $by_file[$file] = array();
                            }

                            $by_file[$file][] = array(
                                'id' => $id,
                                'line' => $line
                            );
                        }

                        $state = 0;

                        break;
                }
            }
        }

        file_put_contents($cache_path, json_encode($cache));
    }

    public function getPhrasesFromPHPFiles($bundle = null, &$by_id, &$errors, &$prefixes, &$by_file)
    {
        $files = Language::GetFileFinder()->getPhpFileList($bundle);
        $cache_path = DP_ROOT.'/sys/cache/php.phrase.cache';

        if(file_exists($cache_path)) {
            $cache = json_decode(file_get_contents($cache_path), true);
        }
        else {
            $cache = array();
        }

        foreach($files as $file) {
            if(isset($cache[$file]) && filemtime($file) == $cache[$file]['mtime']) {
                foreach($cache[$file]['prefixes'] as $prefix) {
                    list($id, $line) = $prefix;
                    $prefixes[] = array('id' => $id, 'filename' => $file, 'line' => $line);
                }

                foreach($cache[$file]['phrases'] as $phrase) {
                    list($id, $line) = $phrase;

                    if(!isset($by_id[$id])) {
                        $by_id[$id] = array();
                    }

                    if(isset($cache[$file]['errors'])) {
                        $errors[$file] = $cache[$file]['errors'];
                    }

                    $by_id[$id][] = array(
                        'filename' => $file,
                        'line' => $line
                    );

                    if(!isset($by_file[$file])) {
                        $by_file[$file] = array();
                    }

                    $by_file[$file][] = array(
                        'id' => $id,
                        'line' => $line
                    );
                }

                continue;
            }

            $rawphp = file_get_contents($file);
            $tokens = token_get_all($rawphp);
            $state = 0;
            $line = 0;
            $cache[$file] = array('phrases' => array(), 'prefixes' => array(), 'mtime' => filemtime($file));

            foreach($tokens as $token) {
                if(is_array($token)) {
                    $line = $token[2];
                }

                if(!is_array($token) || $token[0] != T_WHITESPACE)
                    switch($state) {
                        case 0:
                            if(is_array($token) && ($token[0] == T_OBJECT_OPERATOR || $token[0] == T_DOUBLE_COLON)) {
                                $state++;
                            }

                            break;
                        case 1:
                            if(is_array($token) && $token[0] == T_STRING && $token[1] == 'phrase') {
                                $state++;
                            }
                            else {
                                $state = 0;
                            }

                            break;
                        case 2:
                            if($token == '(') {
                                $state++;
                            }
                            else {
                                $state = 0;

                                if(!isset($errors[$file])) {
                                    $cache[$file]['errors'] =
                                    $errors[$file] = array();
                                }

                                $cache[$file]['errors'][] =
                                $errors[$file][] = $this->tokenWarningPhp('Unexpected Token', $token, $file, $line);
                            }

                            break;
                        case 3:
                            if(is_array($token) && $token[0] == T_CONSTANT_ENCAPSED_STRING) {
                                $id = eval('return '.$token[1].';');
                                $state++;
                            }
                            else {
                                $state = 0;

                                if(!isset($errors[$file])) {
                                    $cache[$file]['errors'] =
                                    $errors[$file] = array();
                                }

                                $cache[$file]['errors'][] =
                                $errors[$file][] = $this->tokenWarningPhp('Unexpected Token', $token, $file, $line);
                            }

                            break;
                        case 4:
                            if($token != ')' && $token != ',') {
                                $cache[$file]['prefixes'][] = array($id, $line);
                                $prefixes[] = array('id' => $id, 'filename' => $file, 'line' => $line);
                            }
                            else {
                                $cache[$file]['phrases'][] = array($id, $line);

                                if(!isset($by_id[$id])) {
                                    $by_id[$id] = array();
                                }

                                $by_id[$id][] = array(
                                    'filename' => $file,
                                    'line' => $line
                                );

                                if(!isset($by_file[$file])) {
                                    $by_file[$file] = array();
                                }

                                $by_file[$file][] = array(
                                    'id' => $id,
                                    'line' => $line
                                );
                            }

                            $state = 0;

                            break;
                    }
            }
        }

        file_put_contents($cache_path, json_encode($cache));
        return array($by_id, $by_file, $prefixes);
    }

    public function tokenWarningPhp($message, $token, $file, $line)
    {
        if(is_array($token))
            $token = token_name($token[0]) .':'. $token[1];

        return "Warning: {$message} ($token) in {$file}:{$line}";
    }

    public function tokenWarningTwig($message, $token, $file, $line)
    {
        $token = \Twig_Token::TypeToString($token->getType(), true) .':'.$token->getValue();

        return "Warning: {$message} ($token) in {$file}:{$line}";
    }
}