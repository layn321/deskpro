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

/* Driver template for the PHP_ParserGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class ParseyyToken implements \ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof ParseyyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof ParseyyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof ParseyyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof ParseyyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class ParseyyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here

// declare_class is output here
#line 1 "Parser.y"
class Parser#line 102 "Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 10 "Parser.y"

	/**
	 * Line number currently being parsed. This comes from the lexer.
	 *
	 * @var integer
	 */
	public $line = 1;

	/**
	 * The output of parsing. When parsing has run, this will be a statement object.
	 *
	 * @var \Application\DeskPRO\Dpql\Statement\Display|null
	 */
	protected $_result = null;

	/**
	 * Gets the result object.
	 *
	 * @return \Application\DeskPRO\Dpql\Statement\Display|null
	 */
	public function getResult()
	{
		return $this->_result;
	}

	/**
	 * Processes a quoted string, by removing the quotes and un-escaping
	 * backslashes.
	 *
	 * @param string $string Quoted string
	 *
	 * @return string String with quotes/escaping removed.
	 */
	public function processQuoted($string)
	{
		if (!strlen($string)) {
			return $string;
		}
		$firstChar = $string[0];
		if (substr($string, -1) !== $firstChar) {
			return $string; // not quoted properly
		}

		$string = substr($string, 1, -1); // strip off quotes

		$searchPos = 0;
		do {
			$searchPos = strpos($string, '\\', $searchPos);
			if ($searchPos === false) {
				break;
			}

			// strip out the back slash and step 1 forward to skip the character after (what it escaped)
			$string = substr($string, 0, $searchPos) . substr($string, $searchPos + 1);
			$searchPos++;
		} while (true);

		return $string;
	}
#line 167 "Parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const T_OP_OR                          =  1;
    const T_OP_AND                         =  2;
    const T_OP_NOT                         =  3;
    const T_OP_EQ                          =  4;
    const T_OP_NE                          =  5;
    const T_OP_GT                          =  6;
    const T_OP_GTEQ                        =  7;
    const T_OP_LT                          =  8;
    const T_OP_LTEQ                        =  9;
    const T_OP_IN                          = 10;
    const T_OP_LIKE                        = 11;
    const T_OP_REGEXP                      = 12;
    const T_OP_MINUS                       = 13;
    const T_OP_PLUS                        = 14;
    const T_OP_MULTIPLY                    = 15;
    const T_OP_DIVIDE                      = 16;
    const T_OP_U_MINUS                     = 17;
    const T_OP_BANG                        = 18;
    const T_SEMICOLON                      = 19;
    const T_DISPLAY                        = 20;
    const T_TABLE                          = 21;
    const T_BAR                            = 22;
    const T_LINE                           = 23;
    const T_PIE                            = 24;
    const T_AREA                           = 25;
    const T_COMMA                          = 26;
    const T_SELECT                         = 27;
    const T_COLUMN_STAR                    = 28;
    const T_AS                             = 29;
    const T_LITERAL                        = 30;
    const T_QUOTED                         = 31;
    const T_FROM                           = 32;
    const T_WHERE                          = 33;
    const T_SPLIT                          = 34;
    const T_BY                             = 35;
    const T_GROUP                          = 36;
    const T_ORDER                          = 37;
    const T_ASC                            = 38;
    const T_DESC                           = 39;
    const T_LIMIT                          = 40;
    const T_NUMBER                         = 41;
    const T_OFFSET                         = 42;
    const T_INTERVAL                       = 43;
    const T_LEFT_PAREN                     = 44;
    const T_RIGHT_PAREN                    = 45;
    const T_COLUMN                         = 46;
    const T_PLACEHOLDER                    = 47;
    const T_AT                             = 48;
    const T_NULL                           = 49;
    const YY_NO_ACTION = 196;
    const YY_ACCEPT_ACTION = 195;
    const YY_ERROR_ACTION = 194;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 239;
static public $yy_action = array(
 /*     0 */    29,   29,   58,   30,   30,   30,   30,   30,   30,   68,
 /*    10 */    20,   21,    3,    3,   19,   19,   29,   29,   58,   30,
 /*    20 */    30,   30,   30,   30,   30,   68,   20,   21,    3,    3,
 /*    30 */    19,   19,   90,   83,   86,   82,   80,   99,   94,    3,
 /*    40 */     3,   19,   19,   48,   62,   91,   29,   29,   58,   30,
 /*    50 */    30,   30,   30,   30,   30,   68,   20,   21,    3,    3,
 /*    60 */    19,   19,   29,   29,   58,   30,   30,   30,   30,   30,
 /*    70 */    30,   68,   20,   21,    3,    3,   19,   19,  195,   41,
 /*    80 */    15,   39,   65,   18,   24,  115,  117,  111,  112,   48,
 /*    90 */    97,   44,   69,   29,   58,   30,   30,   30,   30,   30,
 /*   100 */    30,   68,   20,   21,    3,    3,   19,   19,   58,   30,
 /*   110 */    30,   30,   30,   30,   30,   68,   20,   21,    3,    3,
 /*   120 */    19,   19,   22,   28,   28,   11,   13,  105,   11,   13,
 /*   130 */   110,  108,   27,   43,   45,   50,   10,   22,   87,   46,
 /*   140 */    12,   12,   95,  114,   61,  113,  120,   84,   54,   71,
 /*   150 */   107,   63,   75,   70,   22,    4,    8,   77,  100,  109,
 /*   160 */   118,   17,   66,   25,   27,  106,  116,   59,  119,   22,
 /*   170 */    89,   64,   34,    7,    5,    2,   31,   92,   23,   85,
 /*   180 */    28,   71,  107,   67,   14,    6,   22,    9,   56,   53,
 /*   190 */    42,   60,  118,   78,  103,   25,   27,  106,  116,   59,
 /*   200 */   119,   22,   52,   93,   81,   98,   72,    1,   32,   47,
 /*   210 */    16,   49,   55,   71,  107,   74,   51,   36,   37,  102,
 /*   220 */   101,   57,   26,   96,  118,   38,   35,   25,   33,  106,
 /*   230 */   116,   59,  119,  104,   73,   40,   76,   79,   88,
    );
    static public $yy_lookahead = array(
 /*     0 */     1,    2,    3,    4,    5,    6,    7,    8,    9,   10,
 /*    10 */    11,   12,   13,   14,   15,   16,    1,    2,    3,    4,
 /*    20 */     5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
 /*    30 */    15,   16,   21,   22,   23,   24,   25,   38,   39,   13,
 /*    40 */    14,   15,   16,   66,   29,   68,    1,    2,    3,    4,
 /*    50 */     5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
 /*    60 */    15,   16,    1,    2,    3,    4,    5,    6,    7,    8,
 /*    70 */     9,   10,   11,   12,   13,   14,   15,   16,   51,   52,
 /*    80 */    66,   54,   10,   11,   12,   30,   31,   30,   31,   66,
 /*    90 */    45,   68,   78,    2,    3,    4,    5,    6,    7,    8,
 /*   100 */     9,   10,   11,   12,   13,   14,   15,   16,    3,    4,
 /*   110 */     5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
 /*   120 */    15,   16,    3,   26,   26,   66,   66,   41,   66,   66,
 /*   130 */    70,   72,   13,   70,   72,   66,   26,   18,   64,   64,
 /*   140 */    66,   66,   45,   45,   77,   76,   67,   67,   66,   30,
 /*   150 */    31,   73,   69,   77,    3,   35,   35,   65,   74,   30,
 /*   160 */    41,   44,   43,   44,   13,   46,   47,   48,   49,   18,
 /*   170 */    30,   41,   41,   26,   26,   26,   20,   45,   44,   28,
 /*   180 */    26,   30,   31,   71,   66,   44,    3,   35,   66,   66,
 /*   190 */    62,   77,   41,   40,   61,   44,   13,   46,   47,   48,
 /*   200 */    49,   18,   66,   66,   62,   66,   42,   27,   26,   66,
 /*   210 */    66,   66,   66,   30,   31,   36,   66,   57,   55,   53,
 /*   220 */    19,   66,   33,   66,   41,   56,   59,   44,   60,   46,
 /*   230 */    47,   48,   49,   75,   37,   58,   34,   32,   63,
);
    const YY_SHIFT_USE_DFLT = -2;
    const YY_SHIFT_MAX = 79;
    static public $yy_shift_ofst = array(
 /*     0 */   156,  151,  151,  119,  183,  183,  183,  183,  183,  183,
 /*    10 */   183,   -1,   15,   15,   61,   61,   61,  183,  183,  183,
 /*    20 */   183,  183,  183,  183,  183,  183,  183,  183,  183,  183,
 /*    30 */   183,   11,   11,  153,  164,  197,  202,  205,  189,  180,
 /*    40 */   179,  201,  182,   -2,   -2,   -2,   -2,   45,   61,   61,
 /*    50 */    61,   61,   91,  105,  105,   26,   26,   26,   72,   55,
 /*    60 */    98,   97,   57,  148,  129,  134,  130,  147,  117,  132,
 /*    70 */   154,  141,   86,  120,  152,  110,  121,  149,  131,  140,
);
    const YY_REDUCE_USE_DFLT = -24;
    const YY_REDUCE_MAX = 46;
    static public $yy_reduce_ofst = array(
 /*     0 */    27,   75,   74,   69,   62,   59,   14,   60,   23,   63,
 /*    10 */   -23,   84,   80,   79,   67,   76,  114,  118,  123,  137,
 /*    20 */   155,  146,  139,  144,   82,  143,  145,  157,  150,  136,
 /*    30 */   122,  128,  142,  133,  158,  168,  177,  169,  160,  163,
 /*    40 */   167,  166,  175,  112,   83,   78,   92,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(20, ),
        /* 1 */ array(3, 13, 18, 28, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 2 */ array(3, 13, 18, 28, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 3 */ array(3, 13, 18, 30, 31, 41, 43, 44, 46, 47, 48, 49, ),
        /* 4 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 5 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 6 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 7 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 8 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 9 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 10 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 11 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 38, 39, ),
        /* 12 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 29, ),
        /* 13 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 29, ),
        /* 14 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 15 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 16 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 17 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 18 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 19 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 20 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 21 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 22 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 23 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 24 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 25 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 26 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 27 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 28 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 29 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 30 */ array(3, 13, 18, 30, 31, 41, 44, 46, 47, 48, 49, ),
        /* 31 */ array(21, 22, 23, 24, 25, ),
        /* 32 */ array(21, 22, 23, 24, 25, ),
        /* 33 */ array(40, ),
        /* 34 */ array(42, ),
        /* 35 */ array(37, ),
        /* 36 */ array(34, ),
        /* 37 */ array(32, ),
        /* 38 */ array(33, ),
        /* 39 */ array(27, ),
        /* 40 */ array(36, ),
        /* 41 */ array(19, ),
        /* 42 */ array(26, ),
        /* 43 */ array(),
        /* 44 */ array(),
        /* 45 */ array(),
        /* 46 */ array(),
        /* 47 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 45, ),
        /* 48 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 49 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 50 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 51 */ array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 52 */ array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 53 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 54 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, ),
        /* 55 */ array(13, 14, 15, 16, ),
        /* 56 */ array(13, 14, 15, 16, ),
        /* 57 */ array(13, 14, 15, 16, ),
        /* 58 */ array(10, 11, 12, ),
        /* 59 */ array(30, 31, ),
        /* 60 */ array(26, 45, ),
        /* 61 */ array(26, 45, ),
        /* 62 */ array(30, 31, ),
        /* 63 */ array(26, ),
        /* 64 */ array(30, ),
        /* 65 */ array(44, ),
        /* 66 */ array(41, ),
        /* 67 */ array(26, ),
        /* 68 */ array(44, ),
        /* 69 */ array(45, ),
        /* 70 */ array(26, ),
        /* 71 */ array(44, ),
        /* 72 */ array(41, ),
        /* 73 */ array(35, ),
        /* 74 */ array(35, ),
        /* 75 */ array(26, ),
        /* 76 */ array(35, ),
        /* 77 */ array(26, ),
        /* 78 */ array(41, ),
        /* 79 */ array(30, ),
        /* 80 */ array(),
        /* 81 */ array(),
        /* 82 */ array(),
        /* 83 */ array(),
        /* 84 */ array(),
        /* 85 */ array(),
        /* 86 */ array(),
        /* 87 */ array(),
        /* 88 */ array(),
        /* 89 */ array(),
        /* 90 */ array(),
        /* 91 */ array(),
        /* 92 */ array(),
        /* 93 */ array(),
        /* 94 */ array(),
        /* 95 */ array(),
        /* 96 */ array(),
        /* 97 */ array(),
        /* 98 */ array(),
        /* 99 */ array(),
        /* 100 */ array(),
        /* 101 */ array(),
        /* 102 */ array(),
        /* 103 */ array(),
        /* 104 */ array(),
        /* 105 */ array(),
        /* 106 */ array(),
        /* 107 */ array(),
        /* 108 */ array(),
        /* 109 */ array(),
        /* 110 */ array(),
        /* 111 */ array(),
        /* 112 */ array(),
        /* 113 */ array(),
        /* 114 */ array(),
        /* 115 */ array(),
        /* 116 */ array(),
        /* 117 */ array(),
        /* 118 */ array(),
        /* 119 */ array(),
        /* 120 */ array(),
);
    static public $yy_default = array(
 /*     0 */   194,  194,  194,  194,  194,  194,  191,  194,  194,  194,
 /*    10 */   194,  159,  140,  140,  193,  193,  193,  194,  194,  194,
 /*    20 */   194,  194,  194,  194,  194,  194,  194,  194,  194,  194,
 /*    30 */   194,  194,  194,  163,  165,  155,  145,  194,  143,  194,
 /*    40 */   150,  123,  132,  152,  147,  161,  135,  194,  148,  142,
 /*    50 */   170,  192,  167,  173,  175,  174,  166,  172,  194,  194,
 /*    60 */   194,  194,  194,  154,  194,  194,  194,  149,  194,  194,
 /*    70 */   190,  183,  194,  194,  194,  144,  194,  133,  194,  194,
 /*    80 */   130,  131,  129,  127,  136,  137,  128,  134,  125,  141,
 /*    90 */   126,  146,  181,  171,  158,  176,  178,  180,  179,  157,
 /*   100 */   156,  122,  121,  124,  162,  164,  182,  184,  160,  169,
 /*   110 */   151,  138,  139,  168,  177,  186,  185,  187,  188,  189,
 /*   120 */   153,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 80;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 121;
    const YYNRULE = 73;
    const YYERRORSYMBOL = 50;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx = -1;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = array( 
  '$',             'OP_OR',         'OP_AND',        'OP_NOT',      
  'OP_EQ',         'OP_NE',         'OP_GT',         'OP_GTEQ',     
  'OP_LT',         'OP_LTEQ',       'OP_IN',         'OP_LIKE',     
  'OP_REGEXP',     'OP_MINUS',      'OP_PLUS',       'OP_MULTIPLY', 
  'OP_DIVIDE',     'OP_U_MINUS',    'OP_BANG',       'SEMICOLON',   
  'DISPLAY',       'TABLE',         'BAR',           'LINE',        
  'PIE',           'AREA',          'COMMA',         'SELECT',      
  'COLUMN_STAR',   'AS',            'LITERAL',       'QUOTED',      
  'FROM',          'WHERE',         'SPLIT',         'BY',          
  'GROUP',         'ORDER',         'ASC',           'DESC',        
  'LIMIT',         'NUMBER',        'OFFSET',        'INTERVAL',    
  'LEFT_PAREN',    'RIGHT_PAREN',   'COLUMN',        'PLACEHOLDER', 
  'AT',            'NULL',          'error',         'start',       
  'display_query',  'trailing_semicolon',  'display_clause',  'select_clause',
  'from_clause',   'where_clause',  'split_clause',  'group_clause',
  'order_clause',  'limit_clause',  'display_type',  'display_type_option',
  'select_field',  'select_fields_extra',  'expression',    'alias_optional',
  'split_expression',  'split_expressions_extra',  'group_expression',  'group_expressions_extra',
  'order_expression',  'comma_order_expression_opt',  'direction_opt',  'limit_offset_opt',
  'interval_expression',  'comma_expressions_opt',  'func_args',   
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= display_query trailing_semicolon",
 /*   1 */ "trailing_semicolon ::= SEMICOLON",
 /*   2 */ "trailing_semicolon ::=",
 /*   3 */ "display_query ::= display_clause select_clause from_clause where_clause split_clause group_clause order_clause limit_clause",
 /*   4 */ "display_clause ::= DISPLAY display_type display_type_option",
 /*   5 */ "display_type ::= TABLE",
 /*   6 */ "display_type ::= BAR",
 /*   7 */ "display_type ::= LINE",
 /*   8 */ "display_type ::= PIE",
 /*   9 */ "display_type ::= AREA",
 /*  10 */ "display_type_option ::= COMMA display_type",
 /*  11 */ "display_type_option ::=",
 /*  12 */ "select_clause ::= SELECT select_field select_fields_extra",
 /*  13 */ "select_fields_extra ::= select_fields_extra COMMA select_field",
 /*  14 */ "select_fields_extra ::=",
 /*  15 */ "select_field ::= expression alias_optional",
 /*  16 */ "select_field ::= COLUMN_STAR",
 /*  17 */ "alias_optional ::= AS LITERAL",
 /*  18 */ "alias_optional ::= AS QUOTED",
 /*  19 */ "alias_optional ::=",
 /*  20 */ "from_clause ::= FROM LITERAL",
 /*  21 */ "where_clause ::= WHERE expression",
 /*  22 */ "where_clause ::=",
 /*  23 */ "split_clause ::= SPLIT BY split_expression split_expressions_extra",
 /*  24 */ "split_clause ::=",
 /*  25 */ "split_expressions_extra ::= split_expressions_extra COMMA split_expression",
 /*  26 */ "split_expressions_extra ::=",
 /*  27 */ "split_expression ::= expression",
 /*  28 */ "group_clause ::= GROUP BY group_expression group_expressions_extra",
 /*  29 */ "group_clause ::=",
 /*  30 */ "group_expressions_extra ::= group_expressions_extra COMMA group_expression",
 /*  31 */ "group_expressions_extra ::=",
 /*  32 */ "group_expression ::= expression alias_optional",
 /*  33 */ "order_clause ::= ORDER BY order_expression comma_order_expression_opt",
 /*  34 */ "order_clause ::=",
 /*  35 */ "order_expression ::= expression direction_opt",
 /*  36 */ "direction_opt ::= ASC",
 /*  37 */ "direction_opt ::= DESC",
 /*  38 */ "direction_opt ::=",
 /*  39 */ "comma_order_expression_opt ::= comma_order_expression_opt COMMA order_expression",
 /*  40 */ "comma_order_expression_opt ::=",
 /*  41 */ "limit_clause ::= LIMIT NUMBER limit_offset_opt",
 /*  42 */ "limit_clause ::=",
 /*  43 */ "limit_offset_opt ::= OFFSET NUMBER",
 /*  44 */ "limit_offset_opt ::=",
 /*  45 */ "expression ::= expression OP_EQ|OP_NE|OP_GT|OP_GTEQ|OP_LT|OP_LTEQ expression",
 /*  46 */ "expression ::= expression OP_OR|OP_AND expression",
 /*  47 */ "expression ::= expression OP_MINUS|OP_PLUS interval_expression",
 /*  48 */ "interval_expression ::= INTERVAL NUMBER LITERAL",
 /*  49 */ "interval_expression ::= expression",
 /*  50 */ "expression ::= expression OP_MULTIPLY|OP_DIVIDE expression",
 /*  51 */ "expression ::= expression OP_LIKE expression",
 /*  52 */ "expression ::= expression OP_NOT OP_LIKE expression",
 /*  53 */ "expression ::= expression OP_REGEXP expression",
 /*  54 */ "expression ::= expression OP_NOT OP_REGEXP expression",
 /*  55 */ "expression ::= expression OP_IN LEFT_PAREN expression comma_expressions_opt RIGHT_PAREN",
 /*  56 */ "expression ::= expression OP_NOT OP_IN LEFT_PAREN expression comma_expressions_opt RIGHT_PAREN",
 /*  57 */ "expression ::= OP_MINUS expression",
 /*  58 */ "expression ::= OP_BANG|OP_NOT expression",
 /*  59 */ "expression ::= LEFT_PAREN expression RIGHT_PAREN",
 /*  60 */ "expression ::= LITERAL LEFT_PAREN func_args RIGHT_PAREN",
 /*  61 */ "expression ::= COLUMN",
 /*  62 */ "expression ::= LITERAL",
 /*  63 */ "expression ::= QUOTED",
 /*  64 */ "expression ::= PLACEHOLDER",
 /*  65 */ "expression ::= AT LITERAL",
 /*  66 */ "expression ::= AT QUOTED",
 /*  67 */ "expression ::= NUMBER",
 /*  68 */ "expression ::= NULL",
 /*  69 */ "func_args ::= expression comma_expressions_opt",
 /*  70 */ "func_args ::=",
 /*  71 */ "comma_expressions_opt ::= comma_expressions_opt COMMA expression",
 /*  72 */ "comma_expressions_opt ::=",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param ParseyyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new ParseyyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new ParseyyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new ParseyyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 51, 'rhs' => 2 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 0 ),
  array( 'lhs' => 52, 'rhs' => 8 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 63, 'rhs' => 2 ),
  array( 'lhs' => 63, 'rhs' => 0 ),
  array( 'lhs' => 55, 'rhs' => 3 ),
  array( 'lhs' => 65, 'rhs' => 3 ),
  array( 'lhs' => 65, 'rhs' => 0 ),
  array( 'lhs' => 64, 'rhs' => 2 ),
  array( 'lhs' => 64, 'rhs' => 1 ),
  array( 'lhs' => 67, 'rhs' => 2 ),
  array( 'lhs' => 67, 'rhs' => 2 ),
  array( 'lhs' => 67, 'rhs' => 0 ),
  array( 'lhs' => 56, 'rhs' => 2 ),
  array( 'lhs' => 57, 'rhs' => 2 ),
  array( 'lhs' => 57, 'rhs' => 0 ),
  array( 'lhs' => 58, 'rhs' => 4 ),
  array( 'lhs' => 58, 'rhs' => 0 ),
  array( 'lhs' => 69, 'rhs' => 3 ),
  array( 'lhs' => 69, 'rhs' => 0 ),
  array( 'lhs' => 68, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 4 ),
  array( 'lhs' => 59, 'rhs' => 0 ),
  array( 'lhs' => 71, 'rhs' => 3 ),
  array( 'lhs' => 71, 'rhs' => 0 ),
  array( 'lhs' => 70, 'rhs' => 2 ),
  array( 'lhs' => 60, 'rhs' => 4 ),
  array( 'lhs' => 60, 'rhs' => 0 ),
  array( 'lhs' => 72, 'rhs' => 2 ),
  array( 'lhs' => 74, 'rhs' => 1 ),
  array( 'lhs' => 74, 'rhs' => 1 ),
  array( 'lhs' => 74, 'rhs' => 0 ),
  array( 'lhs' => 73, 'rhs' => 3 ),
  array( 'lhs' => 73, 'rhs' => 0 ),
  array( 'lhs' => 61, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 0 ),
  array( 'lhs' => 75, 'rhs' => 2 ),
  array( 'lhs' => 75, 'rhs' => 0 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 76, 'rhs' => 3 ),
  array( 'lhs' => 76, 'rhs' => 1 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 4 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 4 ),
  array( 'lhs' => 66, 'rhs' => 6 ),
  array( 'lhs' => 66, 'rhs' => 7 ),
  array( 'lhs' => 66, 'rhs' => 2 ),
  array( 'lhs' => 66, 'rhs' => 2 ),
  array( 'lhs' => 66, 'rhs' => 3 ),
  array( 'lhs' => 66, 'rhs' => 4 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 66, 'rhs' => 2 ),
  array( 'lhs' => 66, 'rhs' => 2 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 66, 'rhs' => 1 ),
  array( 'lhs' => 78, 'rhs' => 2 ),
  array( 'lhs' => 78, 'rhs' => 0 ),
  array( 'lhs' => 77, 'rhs' => 3 ),
  array( 'lhs' => 77, 'rhs' => 0 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        17 => 10,
        20 => 10,
        21 => 10,
        12 => 12,
        33 => 12,
        69 => 12,
        13 => 13,
        39 => 13,
        71 => 13,
        15 => 15,
        16 => 16,
        18 => 18,
        23 => 23,
        28 => 23,
        25 => 25,
        30 => 25,
        27 => 27,
        32 => 32,
        35 => 35,
        36 => 36,
        37 => 37,
        41 => 41,
        43 => 43,
        45 => 45,
        46 => 46,
        47 => 47,
        48 => 48,
        49 => 49,
        50 => 50,
        51 => 51,
        52 => 52,
        53 => 53,
        54 => 54,
        55 => 55,
        56 => 56,
        57 => 57,
        58 => 57,
        59 => 59,
        60 => 60,
        61 => 61,
        62 => 62,
        63 => 63,
        64 => 64,
        65 => 65,
        66 => 66,
        67 => 67,
        68 => 68,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 94 "Parser.y"
    function yy_r3(){
	$res = new Statement\Display($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -6]->minor, $this->yystack[$this->yyidx + -5]->minor);

	if ($this->yystack[$this->yyidx + -4]->minor) {
		$res->setWhere($this->yystack[$this->yyidx + -4]->minor);
	}
	if ($this->yystack[$this->yyidx + -3]->minor) {
		$res->setSplitBy($this->yystack[$this->yyidx + -3]->minor);
	}
	if ($this->yystack[$this->yyidx + -2]->minor) {
		$res->setGroupBy($this->yystack[$this->yyidx + -2]->minor);
	}
	if ($this->yystack[$this->yyidx + -1]->minor) {
		$res->setOrderBy($this->yystack[$this->yyidx + -1]->minor);
	}
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$res->setLimitAmount($this->yystack[$this->yyidx + 0]->minor['limit']);
		if (isset($this->yystack[$this->yyidx + 0]->minor['offset'])) {
			$res->setLimitOffset($this->yystack[$this->yyidx + 0]->minor['offset']);
		}
	}

	$this->_result = $res;
    }
#line 1229 "Parser.php"
#line 122 "Parser.y"
    function yy_r4(){
	$this->_retvalue = array($this->yystack[$this->yyidx + -1]->minor);
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;
	}
    }
#line 1237 "Parser.php"
#line 130 "Parser.y"
    function yy_r5(){
	$this->_retvalue = 'table';
    }
#line 1242 "Parser.php"
#line 134 "Parser.y"
    function yy_r6(){
	$this->_retvalue = 'bar';
    }
#line 1247 "Parser.php"
#line 138 "Parser.y"
    function yy_r7(){
	$this->_retvalue = 'line';
    }
#line 1252 "Parser.php"
#line 142 "Parser.y"
    function yy_r8(){
	$this->_retvalue = 'pie';
    }
#line 1257 "Parser.php"
#line 146 "Parser.y"
    function yy_r9(){
	$this->_retvalue = 'area';
    }
#line 1262 "Parser.php"
#line 151 "Parser.y"
    function yy_r10(){
	$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1267 "Parser.php"
#line 158 "Parser.y"
    function yy_r12(){
	$this->_retvalue = array($this->yystack[$this->yyidx + -1]->minor);
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue = array_merge($this->_retvalue, $this->yystack[$this->yyidx + 0]->minor);
	}
    }
#line 1275 "Parser.php"
#line 167 "Parser.y"
    function yy_r13(){
	if (!$this->yystack[$this->yyidx + -2]->minor) {
		$this->_retvalue = array();
	} else {
		$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
	}
	$this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1285 "Parser.php"
#line 180 "Parser.y"
    function yy_r15(){
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue = new Statement\Part\Alias($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
	} else {
		$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
	}
    }
#line 1294 "Parser.php"
#line 189 "Parser.y"
    function yy_r16(){
	$this->_retvalue = new Statement\Part\ColumnStar(explode('.', $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1299 "Parser.php"
#line 200 "Parser.y"
    function yy_r18(){
	$this->_retvalue = $this->processQuoted($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1304 "Parser.php"
#line 223 "Parser.y"
    function yy_r23(){
	$this->_retvalue = ($this->yystack[$this->yyidx + -1]->minor ? array($this->yystack[$this->yyidx + -1]->minor) : array());
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue = array_merge($this->_retvalue, $this->yystack[$this->yyidx + 0]->minor);
	}
    }
#line 1312 "Parser.php"
#line 234 "Parser.y"
    function yy_r25(){
	if (!$this->yystack[$this->yyidx + -2]->minor) {
		$this->_retvalue = array();
	} else {
		$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
	}

	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;
	}
    }
#line 1325 "Parser.php"
#line 250 "Parser.y"
    function yy_r27(){
	if ($this->yystack[$this->yyidx + 0]->minor instanceof Statement\Part\NullValue) {
		$this->_retvalue = false;
	} else {
		$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
	}
    }
#line 1334 "Parser.php"
#line 287 "Parser.y"
    function yy_r32(){
	if ($this->yystack[$this->yyidx + -1]->minor instanceof Statement\Part\NullValue) {
		$this->_retvalue = false;
	} else if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue = new Statement\Part\Alias($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
	} else {
		$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
	}
    }
#line 1345 "Parser.php"
#line 311 "Parser.y"
    function yy_r35(){
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue = new Statement\Part\OrderDir($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
	} else {
		$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
	}
    }
#line 1354 "Parser.php"
#line 322 "Parser.y"
    function yy_r36(){
	$this->_retvalue = 'ASC';
    }
#line 1359 "Parser.php"
#line 327 "Parser.y"
    function yy_r37(){
	$this->_retvalue = 'DESC';
    }
#line 1364 "Parser.php"
#line 349 "Parser.y"
    function yy_r41(){
	$this->_retvalue = array('limit' => intval($this->yystack[$this->yyidx + -1]->minor));
	if ($this->yystack[$this->yyidx + 0]->minor) {
		$this->_retvalue['offset'] = $this->yystack[$this->yyidx + 0]->minor;
	}
    }
#line 1372 "Parser.php"
#line 360 "Parser.y"
    function yy_r43(){
	$this->_retvalue = intval($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1377 "Parser.php"
#line 368 "Parser.y"
    function yy_r45(){
	// this line should be = @$this->yystack[$this->yyidx + -1]->minor, but due to a parser generator bug, doesn't work.
	$token = $this->yystack[$this->yyidx + -1]->major;

	$this->_retvalue = new Statement\Part\BinaryComparison($token, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1385 "Parser.php"
#line 376 "Parser.y"
    function yy_r46(){
	// this line should be = @$this->yystack[$this->yyidx + -1]->minor, but due to a parser generator bug, doesn't work.
	$token = $this->yystack[$this->yyidx + -1]->major;

	$this->_retvalue = new Statement\Part\BinaryLogical($token, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1393 "Parser.php"
#line 384 "Parser.y"
    function yy_r47(){
	// this line should be = @$this->yystack[$this->yyidx + -1]->minor, but due to a parser generator bug, doesn't work.
	$token = $this->yystack[$this->yyidx + -1]->major;
	$expression = $this->yystack[$this->yyidx + 0]->minor;

	if ($expression[0] == 'interval') {
		$this->_retvalue = new Statement\Part\BinaryInterval($token, $this->yystack[$this->yyidx + -2]->minor, $expression[1], $expression[2]);
	} else {
		$this->_retvalue = new Statement\Part\BinaryMath($token, $this->yystack[$this->yyidx + -2]->minor, $expression[1]);
	}
    }
#line 1406 "Parser.php"
#line 397 "Parser.y"
    function yy_r48(){
	$this->_retvalue = array('interval', $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1411 "Parser.php"
#line 402 "Parser.y"
    function yy_r49(){
	$this->_retvalue = array('expression', $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1416 "Parser.php"
#line 407 "Parser.y"
    function yy_r50(){
	// this line should be = @$this->yystack[$this->yyidx + -1]->minor, but due to a parser generator bug, doesn't work.
	$token = $this->yystack[$this->yyidx + -1]->major;

	$this->_retvalue = new Statement\Part\BinaryMath($token, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1424 "Parser.php"
#line 415 "Parser.y"
    function yy_r51(){
	$this->_retvalue = new Statement\Part\Like($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1429 "Parser.php"
#line 420 "Parser.y"
    function yy_r52(){
	$this->_retvalue = new Statement\Part\Like($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + 0]->minor, false);
    }
#line 1434 "Parser.php"
#line 425 "Parser.y"
    function yy_r53(){
	$this->_retvalue = new Statement\Part\RegExp($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1439 "Parser.php"
#line 430 "Parser.y"
    function yy_r54(){
	$this->_retvalue = new Statement\Part\RegExp($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + 0]->minor, false);
    }
#line 1444 "Parser.php"
#line 435 "Parser.y"
    function yy_r55(){
	$values = array($this->yystack[$this->yyidx + -2]->minor);
	if ($this->yystack[$this->yyidx + -1]->minor) {
		$values = array_merge($values, $this->yystack[$this->yyidx + -1]->minor);
	}
	$this->_retvalue = new Statement\Part\In($this->yystack[$this->yyidx + -5]->minor, $values);
    }
#line 1453 "Parser.php"
#line 444 "Parser.y"
    function yy_r56(){
	$values = array($this->yystack[$this->yyidx + -2]->minor);
	if ($this->yystack[$this->yyidx + -1]->minor) {
		$values = array_merge($values, $this->yystack[$this->yyidx + -1]->minor);
	}
	$this->_retvalue = new Statement\Part\In($this->yystack[$this->yyidx + -6]->minor, $values, false);
    }
#line 1462 "Parser.php"
#line 453 "Parser.y"
    function yy_r57(){
	// this line should be = @$this->yystack[$this->yyidx + -1]->minor, but due to a parser generator bug, doesn't work.
	$token = $this->yystack[$this->yyidx + -1]->major;

	$this->_retvalue = new Statement\Part\UnaryOperator($token, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1470 "Parser.php"
#line 469 "Parser.y"
    function yy_r59(){
	$this->_retvalue = new Statement\Part\Parentheses($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1475 "Parser.php"
#line 474 "Parser.y"
    function yy_r60(){
	if (!$this->yystack[$this->yyidx + -1]->minor) {
		$this->yystack[$this->yyidx + -1]->minor = array();
	}
	$this->_retvalue = new Statement\Part\FunctionCall($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1483 "Parser.php"
#line 482 "Parser.y"
    function yy_r61(){
	$this->_retvalue = new Statement\Part\Column(explode('.', $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1488 "Parser.php"
#line 487 "Parser.y"
    function yy_r62(){
	$this->_retvalue = new Statement\Part\String($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1493 "Parser.php"
#line 492 "Parser.y"
    function yy_r63(){
	$this->_retvalue = new Statement\Part\String($this->processQuoted($this->yystack[$this->yyidx + 0]->minor));
    }
#line 1498 "Parser.php"
#line 497 "Parser.y"
    function yy_r64(){
	$value = substr($this->yystack[$this->yyidx + 0]->minor, 1, -1);
	$this->_retvalue = new Statement\Part\Placeholder($value);
    }
#line 1504 "Parser.php"
#line 503 "Parser.y"
    function yy_r65(){
	$this->_retvalue = new Statement\Part\AliasRef($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1509 "Parser.php"
#line 508 "Parser.y"
    function yy_r66(){
	$this->_retvalue = new Statement\Part\AliasRef($this->processQuoted($this->yystack[$this->yyidx + 0]->minor));
    }
#line 1514 "Parser.php"
#line 513 "Parser.y"
    function yy_r67(){
	$this->_retvalue =  new Statement\Part\Number($this->yystack[$this->yyidx + 0]->minor + 0);
    }
#line 1519 "Parser.php"
#line 518 "Parser.y"
    function yy_r68(){
	$this->_retvalue = new Statement\Part\NullValue();
    }
#line 1524 "Parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //ParseyyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new ParseyyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 5 "Parser.y"

	throw new Exception("Error parsing DPQL statement at line $this->line");
#line 1640 "Parser.php"
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int   $yymajor      the token number
     * @param mixed $yytokenvalue the token value
     * @param mixed ...           any extra arguments that should be passed to handlers
     *
     * @return void
     */
    function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new ParseyyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(
                self::$yyTraceFILE,
                "%sInput %s\n",
                self::$yyTracePrompt,
                self::$yyTokenName[$yymajor]
            );
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL
                && !$this->yy_is_expected_token($yymajor)
            ) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(
                        self::$yyTraceFILE,
                        "%sSyntax Error!\n",
                        self::$yyTracePrompt
                    );
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ) {
                        if (self::$yyTraceFILE) {
                            fprintf(
                                self::$yyTraceFILE,
                                "%sDiscard input token %s\n",
                                self::$yyTracePrompt,
                                self::$yyTokenName[$yymajor]
                            );
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0
                            && $yymx != self::YYERRORSYMBOL
                            && ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
