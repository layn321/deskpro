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

class Language
{
    public static $BUNDLES = array('AgentBundle', 'AdminBundle', 'InstallBundle', 'UserBundle', 'ReportBundle', 'BillingBundle', 'DeskPRO');
    public static $BUNDLES_MAP = array(
                'AgentBundle' => 'agent',
                'ReportBundle' => 'agent',
                'AdminBundle' => 'admin',
                'BillingBundle' => 'admin',
                'UserBundle' => 'user',
                'DeskPRO' => 'agent'
            );
    public static $PACKAGES = array('agent', 'user', 'admin');

    public static function GetFileFinder()
    {
        return new FileFinder();
    }

    public static function GetPhraseFinder($container)
    {
        return new PhraseFinder($container);
    }

    public static function GetTwigPreservingLexer($container, $options = array())
    {
        return new PreservingLexer($container->get('twig'), $options);
    }

    public static function GetTwigLexer($container, $options = array())
    {
        return new \Twig_Lexer($container->get('twig'), $options);
    }

    public static function DumpTokensPHP($tokens)
    {
        foreach($tokens as $i=>$token) {
            if(is_array($token)) {
                $tokens[$i][] = token_name($token[0]);
            }
        }

        print_r($tokens);
    }

    public static function DumpTokensTwig($tokens)
    {
        $basic_tokens = array();

        while(!$tokens->isEOF()) {
            $token = $tokens->next();
            $basic_tokens[] = array(
                'type' => \Twig_Token::TypeToString($token->getType(), true),
                'value' => $token->getValue(),
            );
        }

        print_r($basic_tokens);
    }
}