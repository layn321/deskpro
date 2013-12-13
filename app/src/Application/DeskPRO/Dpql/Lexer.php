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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql;

/**
 * Lexer for DPQL. To be used in conjunction with the parser.
 */
class Lexer
{
	/**
	 * Internal lexer positioning counter.
	 *
	 * @var integer
	 */
	protected $_counter = 0;

	/**
	 * String to be tokenized.
	 *
	 * @var string
	 */
	protected $_input;

	/**
	 * ID of token that is being emitted. Tokens are defined in the parser.
	 *
	 * @var integer
	 */
	public $token = null;

	/**
	 * Value for the token that is being emitted.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Line number currently being tokenized. This can be used to detect the
	 * line an error is occurring on.
	 *
	 * @var integer
	 */
	public $line = 1;

	/**
	 * List of reserved keywords. These will be emitted with tokens that match
	 * the name of the reserved word.
	 *
	 * @var array
	 */
	protected $_reserved = array(
		'DISPLAY', 'TABLE', 'BAR', 'LINE', 'PIE', 'AREA',
		'SELECT', 'FROM', 'WHERE',
		'GROUP', 'ORDER', 'SPLIT', 'BY',
		'LIMIT', 'OFFSET', 'AS', 'NULL',
		'AND', 'OR', 'NOT', 'IN', 'LIKE', 'REGEXP',
		'ASC', 'DESC',
		'INTERVAL'
	);

	/**
	 * Maps an operator string to a token name (T_OP_<value>).
	 * Operator strings must be listed in the operator regex.
	 *
	 * @var array
	 */
	protected $_operatorMap = array(
		'+' => 'PLUS',
		'-' => 'MINUS',
		'*' => 'MULTIPLY',
		'/' => 'DIVIDE',
		'=' => 'EQ',
		'!=' => 'NE',
		'<>' => 'NE',
		'>=' => 'GTEQ',
		'>' => 'GT',
		'<=' => 'LTEQ',
		'<' => 'LT',
		'AND' => 'AND',
		'&&' => 'AND',
		'OR' => 'OR',
		'||' => 'OR',
		'!' => 'BANG',
		'NOT' => 'NOT',
		'IN' => 'IN',
		'LIKE' => 'LIKE',
		'REGEXP' => 'REGEXP'
	);

	/**
	 * Sets the input and resets the lexer state.
	 *
	 * @param string $input
	 */
	public function setInput($input) {
		$this->_input = $input;
		$this->_counter = 0;
		$this->token = null;
		$this->value = null;
		$this->line = 1;
	}


    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state)
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate()
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state)
    {
        $this->_yy_state = $state;
    }



    function yylex1()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
              3 => 0,
              4 => 0,
              5 => 0,
              6 => 0,
              7 => 1,
              9 => 1,
              11 => 0,
              12 => 2,
              15 => 3,
              19 => 0,
              20 => 0,
            );
        if ($this->_counter >= strlen($this->_input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\s+)|\G(,)|\G(\\()|\G(\\))|\G(;)|\G(@)|\G(\'([^\\\\\']+|\\\\.)*\')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)/';

        do {
            if (preg_match($yy_global_pattern,$this->_input, $yymatches, null, $this->_counter)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->_input,
                        $this->_counter, 5) . '... state INITIAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->_counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->_counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->_counter >= strlen($this->_input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = array(
        1 => array(0, "\G(,)|\G(\\()|\G(\\))|\G(;)|\G(@)|\G('([^\\\\']+|\\\\.)*')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        2 => array(0, "\G(\\()|\G(\\))|\G(;)|\G(@)|\G('([^\\\\']+|\\\\.)*')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        3 => array(0, "\G(\\))|\G(;)|\G(@)|\G('([^\\\\']+|\\\\.)*')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        4 => array(0, "\G(;)|\G(@)|\G('([^\\\\']+|\\\\.)*')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        5 => array(0, "\G(@)|\G('([^\\\\']+|\\\\.)*')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        6 => array(0, "\G('([^\\\\']+|\\\\.)*')|\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        7 => array(1, "\G(-?([0-9]*\\.[0-9]+|[0-9]+))|\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        9 => array(2, "\G(%[a-zA-Z0-9_:.]+%)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        11 => array(2, "\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*\\*)|\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        12 => array(4, "\G([a-zA-Z_][a-zA-Z0-9_]*\\.([a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?\\.)*[a-zA-Z_][a-zA-Z0-9_]*(\\[[a-zA-Z0-9_]+\\])?)|\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        15 => array(7, "\G(\\+|-|\\*|\/|!=|<>|>=|<=|<|>|=|&&|\\|\\||!)|\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        19 => array(7, "\G([a-zA-Z_][a-zA-Z0-9_]*)"),
        20 => array(7, ""),
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                              $this->_input, $yymatches, null, $this->_counter)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = array();
                            }
                        }
                        $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->yylex();
                    } elseif ($r === false) {
                        $this->_counter += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->_counter >= strlen($this->_input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->_counter += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->_input[$this->_counter]);
            }
            break;
        } while (true);

    } // end function


    const INITIAL = 1;
    function yy_r1_1($yy_subpatterns)
    {
 return false;     }
    function yy_r1_2($yy_subpatterns)
    {
 $this->token = Parser::T_COMMA;     }
    function yy_r1_3($yy_subpatterns)
    {
 $this->token = Parser::T_LEFT_PAREN;     }
    function yy_r1_4($yy_subpatterns)
    {
 $this->token = Parser::T_RIGHT_PAREN;     }
    function yy_r1_5($yy_subpatterns)
    {
 $this->token = Parser::T_SEMICOLON;     }
    function yy_r1_6($yy_subpatterns)
    {
 $this->token = Parser::T_AT;     }
    function yy_r1_7($yy_subpatterns)
    {
 $this->token = Parser::T_QUOTED;     }
    function yy_r1_9($yy_subpatterns)
    {
 $this->token = Parser::T_NUMBER;     }
    function yy_r1_11($yy_subpatterns)
    {
 $this->token = Parser::T_PLACEHOLDER;     }
    function yy_r1_12($yy_subpatterns)
    {
 $this->token = Parser::T_COLUMN_STAR;     }
    function yy_r1_15($yy_subpatterns)
    {
 $this->token = Parser::T_COLUMN;     }
    function yy_r1_19($yy_subpatterns)
    {

	if (isset($this->_operatorMap[$this->value])) {
		$this->token = constant(__NAMESPACE__ . '\\Parser::T_OP_' . $this->_operatorMap[$this->value]);
	} else {
		throw new Exception("Unknown operator $this->value");
	}
    }
    function yy_r1_20($yy_subpatterns)
    {

	$upper = strtoupper($this->value);

	if (isset($this->_operatorMap[$upper])) {
		$this->token = constant(__NAMESPACE__ . '\\Parser::T_OP_' . $this->_operatorMap[$upper]);
	} else if (in_array($upper, $this->_reserved)) {
		$this->token = constant(__NAMESPACE__ . '\\Parser::T_' . $upper);
	}  else {
		$this->token = Parser::T_LITERAL;
	}
    }

}