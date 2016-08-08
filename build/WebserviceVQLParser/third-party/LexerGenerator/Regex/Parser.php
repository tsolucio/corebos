<?php
/* Driver template for the PHP_PHP_LexerGenerator_Regex_rGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class PHP_LexerGenerator_Regex_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof PHP_LexerGenerator_Regex_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof PHP_LexerGenerator_Regex_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->_string;
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
                $x = ($value instanceof PHP_LexerGenerator_Regex_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof PHP_LexerGenerator_Regex_yyToken) {
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
class PHP_LexerGenerator_Regex_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 2 "LexerGenerator\Regex\Parser.y"

require_once 'PHP/LexerGenerator/Exception.php';
#line 102 "LexerGenerator\Regex\Parser.php"

// declare_class is output here
#line 5 "LexerGenerator\Regex\Parser.y"
class PHP_LexerGenerator_Regex_Parser#line 107 "LexerGenerator\Regex\Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 21 "LexerGenerator\Regex\Parser.y"

    private $_lex;
    private $_subpatterns;
    public $result;
    function __construct($lex)
    {
        $this->result = new PHP_LexerGenerator_ParseryyToken('');
        $this->_lex = $lex;
        $this->_subpatterns = 0;
    }

    function reset()
    {
        $this->_subpatterns = 0;
        $this->result = new PHP_LexerGenerator_ParseryyToken('');
    }
#line 129 "LexerGenerator\Regex\Parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const OPENPAREN                      =  1;
    const OPENASSERTION                  =  2;
    const BAR                            =  3;
    const MULTIPLIER                     =  4;
    const MATCHSTART                     =  5;
    const MATCHEND                       =  6;
    const BACKREFERENCE                  =  7;
    const COULDBEBACKREF                 =  8;
    const OPENCHARCLASS                  =  9;
    const CLOSECHARCLASS                 = 10;
    const NEGATE                         = 11;
    const TEXT                           = 12;
    const CONTROLCHAR                    = 13;
    const ESCAPEDBACKSLASH               = 14;
    const HYPHEN                         = 15;
    const FULLSTOP                       = 16;
    const INTERNALOPTIONS                = 17;
    const CLOSEPAREN                     = 18;
    const COLON                          = 19;
    const POSITIVELOOKAHEAD              = 20;
    const NEGATIVELOOKAHEAD              = 21;
    const POSITIVELOOKBEHIND             = 22;
    const NEGATIVELOOKBEHIND             = 23;
    const PATTERNNAME                    = 24;
    const ONCEONLY                       = 25;
    const COMMENT                        = 26;
    const RECUR                          = 27;
    const YY_NO_ACTION = 236;
    const YY_ACCEPT_ACTION = 235;
    const YY_ERROR_ACTION = 234;

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
    const YY_SZ_ACTTAB = 353;
static public $yy_action = array(
 /*     0 */   235,   47,   15,   21,  121,  133,  132,  131,  134,  135,
 /*    10 */   137,  136,  130,  129,   34,   15,   21,  121,  133,  132,
 /*    20 */   131,  134,  135,  137,  136,  130,  129,   90,   15,   21,
 /*    30 */   121,  133,  132,  131,  134,  135,  137,  136,  130,  129,
 /*    40 */    35,   15,   21,  121,  133,  132,  131,  134,  135,  137,
 /*    50 */   136,  130,  129,   39,   15,   21,  121,  133,  132,  131,
 /*    60 */   134,  135,  137,  136,  130,  129,   31,   15,   21,  121,
 /*    70 */   133,  132,  131,  134,  135,  137,  136,  130,  129,   33,
 /*    80 */    15,   21,  121,  133,  132,  131,  134,  135,  137,  136,
 /*    90 */   130,  129,   38,   15,   21,  121,  133,  132,  131,  134,
 /*   100 */   135,  137,  136,  130,  129,   36,   15,   21,  121,  133,
 /*   110 */   132,  131,  134,  135,  137,  136,  130,  129,   37,   15,
 /*   120 */    21,  121,  133,  132,  131,  134,  135,  137,  136,  130,
 /*   130 */   129,   41,   15,   21,  121,  133,  132,  131,  134,  135,
 /*   140 */   137,  136,  130,  129,   40,   15,   21,  121,  133,  132,
 /*   150 */   131,  134,  135,  137,  136,  130,  129,   42,   15,   21,
 /*   160 */   121,  133,  132,  131,  134,  135,  137,  136,  130,  129,
 /*   170 */    30,   15,   21,  121,  133,  132,  131,  134,  135,  137,
 /*   180 */   136,  130,  129,   16,   21,  121,  133,  132,  131,  134,
 /*   190 */   135,  137,  136,  130,  129,   48,   24,   20,   84,   94,
 /*   200 */    96,  101,   74,   75,   73,   72,   71,   77,   70,   60,
 /*   210 */    63,   43,   61,    9,    1,    2,    3,    4,   10,   12,
 /*   220 */    51,   57,    8,   17,  125,  126,   14,   87,  124,  123,
 /*   230 */    18,  114,  115,   45,   44,   59,  116,   46,   13,    8,
 /*   240 */    17,  127,   97,  138,   89,   78,   79,   18,  103,    7,
 /*   250 */    45,   44,   59,   85,   46,   13,    8,   17,  110,  120,
 /*   260 */   109,  107,   78,   79,   18,   32,  119,   45,   44,   59,
 /*   270 */    99,   46,   13,    8,   17,   19,   62,   65,   53,  124,
 /*   280 */   123,   18,   13,   56,   45,   44,   59,   50,   46,   26,
 /*   290 */    64,   76,   66,   67,   68,   69,   88,   66,   67,   68,
 /*   300 */     6,    1,    2,    3,    4,   62,   65,   53,  128,  113,
 /*   310 */   117,  104,  105,  112,   13,   13,   13,   13,   13,   13,
 /*   320 */    13,   13,   11,    5,   13,   28,   81,  106,  100,   58,
 /*   330 */    49,  122,   52,   54,   55,   98,   95,  118,   86,   80,
 /*   340 */    27,   29,   25,   92,  102,  108,   91,   93,  111,   82,
 /*   350 */    83,   22,   23,
    );
    static public $yy_lookahead = array(
 /*     0 */    29,   30,   31,   32,   33,   34,   35,   36,   37,   38,
 /*    10 */    39,   40,   41,   42,   30,   31,   32,   33,   34,   35,
 /*    20 */    36,   37,   38,   39,   40,   41,   42,   30,   31,   32,
 /*    30 */    33,   34,   35,   36,   37,   38,   39,   40,   41,   42,
 /*    40 */    30,   31,   32,   33,   34,   35,   36,   37,   38,   39,
 /*    50 */    40,   41,   42,   30,   31,   32,   33,   34,   35,   36,
 /*    60 */    37,   38,   39,   40,   41,   42,   30,   31,   32,   33,
 /*    70 */    34,   35,   36,   37,   38,   39,   40,   41,   42,   30,
 /*    80 */    31,   32,   33,   34,   35,   36,   37,   38,   39,   40,
 /*    90 */    41,   42,   30,   31,   32,   33,   34,   35,   36,   37,
 /*   100 */    38,   39,   40,   41,   42,   30,   31,   32,   33,   34,
 /*   110 */    35,   36,   37,   38,   39,   40,   41,   42,   30,   31,
 /*   120 */    32,   33,   34,   35,   36,   37,   38,   39,   40,   41,
 /*   130 */    42,   30,   31,   32,   33,   34,   35,   36,   37,   38,
 /*   140 */    39,   40,   41,   42,   30,   31,   32,   33,   34,   35,
 /*   150 */    36,   37,   38,   39,   40,   41,   42,   30,   31,   32,
 /*   160 */    33,   34,   35,   36,   37,   38,   39,   40,   41,   42,
 /*   170 */    30,   31,   32,   33,   34,   35,   36,   37,   38,   39,
 /*   180 */    40,   41,   42,   31,   32,   33,   34,   35,   36,   37,
 /*   190 */    38,   39,   40,   41,   42,    1,    2,   32,   33,   34,
 /*   200 */    35,   36,   37,   38,   39,   40,   41,   42,   12,   13,
 /*   210 */    14,   17,   16,   19,   20,   21,   22,   23,   24,   25,
 /*   220 */    26,   27,    1,    2,   12,   13,    5,    4,    7,    8,
 /*   230 */     9,   12,   13,   12,   13,   14,    4,   16,    3,    1,
 /*   240 */     2,   12,   13,   14,    6,    7,    8,    9,   18,   19,
 /*   250 */    12,   13,   14,   18,   16,    3,    1,    2,   12,   13,
 /*   260 */    14,    6,    7,    8,    9,   15,    4,   12,   13,   14,
 /*   270 */    18,   16,    3,    1,    2,   11,   12,   13,   14,    7,
 /*   280 */     8,    9,    3,   12,   12,   13,   14,   18,   16,   15,
 /*   290 */    10,    4,   12,   13,   14,   10,   18,   12,   13,   14,
 /*   300 */    18,   20,   21,   22,   23,   12,   13,   14,   12,   13,
 /*   310 */    14,   12,   13,   14,    3,    3,    3,    3,    3,    3,
 /*   320 */     3,    3,   36,   37,    3,   15,    4,    4,    4,   18,
 /*   330 */    18,   18,   18,   18,   18,   18,   18,    4,    4,   18,
 /*   340 */    15,   15,   15,    4,    4,    4,   18,    4,    4,    4,
 /*   350 */     4,   43,   43,
);
    const YY_SHIFT_USE_DFLT = -1;
    const YY_SHIFT_MAX = 70;
    static public $yy_shift_ofst = array(
 /*     0 */   221,  221,  221,  221,  221,  221,  221,  221,  221,  221,
 /*    10 */   221,  221,  221,  221,  272,  255,  238,  194,  264,  293,
 /*    20 */   196,  196,  280,  285,  281,  299,  229,  246,  296,  212,
 /*    30 */   252,  316,  219,  314,  313,  315,  312,  269,  235,  311,
 /*    40 */   318,  317,  321,  230,  333,  232,  262,  279,  271,  322,
 /*    50 */   287,  278,  223,  250,  345,  340,  282,  328,  341,  344,
 /*    60 */   343,  339,  327,  324,  323,  310,  325,  274,  326,  346,
 /*    70 */   334,
);
    const YY_REDUCE_USE_DFLT = -30;
    const YY_REDUCE_MAX = 19;
    static public $yy_reduce_ofst = array(
 /*     0 */   -29,  127,  114,  101,  140,   88,   10,  -16,   23,   36,
 /*    10 */    49,   75,   62,   -3,  152,  165,  165,  286,  309,  308,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 1 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 2 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 3 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 4 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 5 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 6 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 7 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 8 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 9 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 10 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 11 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 12 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 13 */ array(1, 2, 5, 7, 8, 9, 12, 13, 14, 16, ),
        /* 14 */ array(1, 2, 7, 8, 9, 12, 13, 14, 16, ),
        /* 15 */ array(1, 2, 6, 7, 8, 9, 12, 13, 14, 16, ),
        /* 16 */ array(1, 2, 6, 7, 8, 9, 12, 13, 14, 16, ),
        /* 17 */ array(1, 2, 17, 19, 20, 21, 22, 23, 24, 25, 26, 27, ),
        /* 18 */ array(11, 12, 13, 14, ),
        /* 19 */ array(12, 13, 14, ),
        /* 20 */ array(12, 13, 14, 16, ),
        /* 21 */ array(12, 13, 14, 16, ),
        /* 22 */ array(10, 12, 13, 14, ),
        /* 23 */ array(10, 12, 13, 14, ),
        /* 24 */ array(20, 21, 22, 23, ),
        /* 25 */ array(12, 13, 14, ),
        /* 26 */ array(12, 13, 14, ),
        /* 27 */ array(12, 13, 14, ),
        /* 28 */ array(12, 13, 14, ),
        /* 29 */ array(12, 13, ),
        /* 30 */ array(3, 18, ),
        /* 31 */ array(3, 18, ),
        /* 32 */ array(12, 13, ),
        /* 33 */ array(3, 18, ),
        /* 34 */ array(3, 18, ),
        /* 35 */ array(3, 18, ),
        /* 36 */ array(3, 18, ),
        /* 37 */ array(3, 18, ),
        /* 38 */ array(3, 18, ),
        /* 39 */ array(3, 18, ),
        /* 40 */ array(3, 18, ),
        /* 41 */ array(3, 18, ),
        /* 42 */ array(3, 18, ),
        /* 43 */ array(18, 19, ),
        /* 44 */ array(4, ),
        /* 45 */ array(4, ),
        /* 46 */ array(4, ),
        /* 47 */ array(3, ),
        /* 48 */ array(12, ),
        /* 49 */ array(4, ),
        /* 50 */ array(4, ),
        /* 51 */ array(18, ),
        /* 52 */ array(4, ),
        /* 53 */ array(15, ),
        /* 54 */ array(4, ),
        /* 55 */ array(4, ),
        /* 56 */ array(18, ),
        /* 57 */ array(18, ),
        /* 58 */ array(4, ),
        /* 59 */ array(4, ),
        /* 60 */ array(4, ),
        /* 61 */ array(4, ),
        /* 62 */ array(15, ),
        /* 63 */ array(4, ),
        /* 64 */ array(4, ),
        /* 65 */ array(15, ),
        /* 66 */ array(15, ),
        /* 67 */ array(15, ),
        /* 68 */ array(15, ),
        /* 69 */ array(4, ),
        /* 70 */ array(4, ),
        /* 71 */ array(),
        /* 72 */ array(),
        /* 73 */ array(),
        /* 74 */ array(),
        /* 75 */ array(),
        /* 76 */ array(),
        /* 77 */ array(),
        /* 78 */ array(),
        /* 79 */ array(),
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
        /* 121 */ array(),
        /* 122 */ array(),
        /* 123 */ array(),
        /* 124 */ array(),
        /* 125 */ array(),
        /* 126 */ array(),
        /* 127 */ array(),
        /* 128 */ array(),
        /* 129 */ array(),
        /* 130 */ array(),
        /* 131 */ array(),
        /* 132 */ array(),
        /* 133 */ array(),
        /* 134 */ array(),
        /* 135 */ array(),
        /* 136 */ array(),
        /* 137 */ array(),
        /* 138 */ array(),
);
    static public $yy_default = array(
 /*     0 */   234,  234,  234,  234,  234,  234,  234,  234,  234,  234,
 /*    10 */   234,  234,  234,  234,  234,  143,  141,  234,  234,  234,
 /*    20 */   158,  145,  234,  234,  234,  234,  234,  234,  234,  234,
 /*    30 */   234,  234,  234,  234,  234,  234,  234,  234,  234,  234,
 /*    40 */   234,  234,  234,  234,  201,  197,  199,  139,  234,  219,
 /*    50 */   221,  234,  227,  177,  218,  215,  234,  234,  229,  203,
 /*    60 */   209,  207,  175,  211,  172,  176,  188,  186,  187,  171,
 /*    70 */   205,  167,  166,  165,  163,  164,  222,  168,  169,  170,
 /*    80 */   223,  220,  217,  173,  159,  231,  206,  228,  232,  140,
 /*    90 */   144,  233,  208,  210,  160,  224,  161,  189,  225,  226,
 /*   100 */   212,  162,  216,  213,  183,  184,  174,  142,  230,  195,
 /*   110 */   196,  204,  185,  180,  181,  178,  198,  179,  202,  200,
 /*   120 */   194,  146,  214,  157,  156,  193,  190,  192,  182,  155,
 /*   130 */   154,  149,  148,  147,  150,  151,  153,  152,  191,
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
    const YYNOCODE = 45;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 139;
    const YYNRULE = 95;
    const YYERRORSYMBOL = 28;
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
    public $yyidx;                    /* Index of top element in stack */
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
  '$',             'OPENPAREN',     'OPENASSERTION',  'BAR',         
  'MULTIPLIER',    'MATCHSTART',    'MATCHEND',      'BACKREFERENCE',
  'COULDBEBACKREF',  'OPENCHARCLASS',  'CLOSECHARCLASS',  'NEGATE',      
  'TEXT',          'CONTROLCHAR',   'ESCAPEDBACKSLASH',  'HYPHEN',      
  'FULLSTOP',      'INTERNALOPTIONS',  'CLOSEPAREN',    'COLON',       
  'POSITIVELOOKAHEAD',  'NEGATIVELOOKAHEAD',  'POSITIVELOOKBEHIND',  'NEGATIVELOOKBEHIND',
  'PATTERNNAME',   'ONCEONLY',      'COMMENT',       'RECUR',       
  'error',         'start',         'pattern',       'basic_pattern',
  'basic_text',    'character_class',  'assertion',     'grouping',    
  'lookahead',     'lookbehind',    'subpattern',    'onceonly',    
  'comment',       'recur',         'conditional',   'character_class_contents',
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= pattern",
 /*   1 */ "pattern ::= MATCHSTART basic_pattern MATCHEND",
 /*   2 */ "pattern ::= MATCHSTART basic_pattern",
 /*   3 */ "pattern ::= basic_pattern MATCHEND",
 /*   4 */ "pattern ::= basic_pattern",
 /*   5 */ "pattern ::= pattern BAR pattern",
 /*   6 */ "basic_pattern ::= basic_text",
 /*   7 */ "basic_pattern ::= character_class",
 /*   8 */ "basic_pattern ::= assertion",
 /*   9 */ "basic_pattern ::= grouping",
 /*  10 */ "basic_pattern ::= lookahead",
 /*  11 */ "basic_pattern ::= lookbehind",
 /*  12 */ "basic_pattern ::= subpattern",
 /*  13 */ "basic_pattern ::= onceonly",
 /*  14 */ "basic_pattern ::= comment",
 /*  15 */ "basic_pattern ::= recur",
 /*  16 */ "basic_pattern ::= conditional",
 /*  17 */ "basic_pattern ::= BACKREFERENCE",
 /*  18 */ "basic_pattern ::= COULDBEBACKREF",
 /*  19 */ "basic_pattern ::= basic_pattern basic_text",
 /*  20 */ "basic_pattern ::= basic_pattern character_class",
 /*  21 */ "basic_pattern ::= basic_pattern assertion",
 /*  22 */ "basic_pattern ::= basic_pattern grouping",
 /*  23 */ "basic_pattern ::= basic_pattern lookahead",
 /*  24 */ "basic_pattern ::= basic_pattern lookbehind",
 /*  25 */ "basic_pattern ::= basic_pattern subpattern",
 /*  26 */ "basic_pattern ::= basic_pattern onceonly",
 /*  27 */ "basic_pattern ::= basic_pattern comment",
 /*  28 */ "basic_pattern ::= basic_pattern recur",
 /*  29 */ "basic_pattern ::= basic_pattern conditional",
 /*  30 */ "basic_pattern ::= basic_pattern BACKREFERENCE",
 /*  31 */ "basic_pattern ::= basic_pattern COULDBEBACKREF",
 /*  32 */ "character_class ::= OPENCHARCLASS character_class_contents CLOSECHARCLASS",
 /*  33 */ "character_class ::= OPENCHARCLASS NEGATE character_class_contents CLOSECHARCLASS",
 /*  34 */ "character_class ::= OPENCHARCLASS character_class_contents CLOSECHARCLASS MULTIPLIER",
 /*  35 */ "character_class ::= OPENCHARCLASS NEGATE character_class_contents CLOSECHARCLASS MULTIPLIER",
 /*  36 */ "character_class_contents ::= TEXT",
 /*  37 */ "character_class_contents ::= CONTROLCHAR",
 /*  38 */ "character_class_contents ::= ESCAPEDBACKSLASH",
 /*  39 */ "character_class_contents ::= ESCAPEDBACKSLASH HYPHEN CONTROLCHAR",
 /*  40 */ "character_class_contents ::= CONTROLCHAR HYPHEN ESCAPEDBACKSLASH",
 /*  41 */ "character_class_contents ::= CONTROLCHAR HYPHEN CONTROLCHAR",
 /*  42 */ "character_class_contents ::= ESCAPEDBACKSLASH HYPHEN TEXT",
 /*  43 */ "character_class_contents ::= CONTROLCHAR HYPHEN TEXT",
 /*  44 */ "character_class_contents ::= TEXT HYPHEN TEXT",
 /*  45 */ "character_class_contents ::= TEXT HYPHEN CONTROLCHAR",
 /*  46 */ "character_class_contents ::= TEXT HYPHEN ESCAPEDBACKSLASH",
 /*  47 */ "character_class_contents ::= character_class_contents CONTROLCHAR",
 /*  48 */ "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH",
 /*  49 */ "character_class_contents ::= character_class_contents TEXT",
 /*  50 */ "character_class_contents ::= character_class_contents CONTROLCHAR HYPHEN CONTROLCHAR",
 /*  51 */ "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH HYPHEN CONTROLCHAR",
 /*  52 */ "character_class_contents ::= character_class_contents CONTROLCHAR HYPHEN ESCAPEDBACKSLASH",
 /*  53 */ "character_class_contents ::= character_class_contents CONTROLCHAR HYPHEN TEXT",
 /*  54 */ "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH HYPHEN TEXT",
 /*  55 */ "character_class_contents ::= character_class_contents TEXT HYPHEN CONTROLCHAR",
 /*  56 */ "character_class_contents ::= character_class_contents TEXT HYPHEN ESCAPEDBACKSLASH",
 /*  57 */ "character_class_contents ::= character_class_contents TEXT HYPHEN TEXT",
 /*  58 */ "basic_text ::= TEXT",
 /*  59 */ "basic_text ::= TEXT MULTIPLIER",
 /*  60 */ "basic_text ::= FULLSTOP",
 /*  61 */ "basic_text ::= FULLSTOP MULTIPLIER",
 /*  62 */ "basic_text ::= CONTROLCHAR",
 /*  63 */ "basic_text ::= CONTROLCHAR MULTIPLIER",
 /*  64 */ "basic_text ::= ESCAPEDBACKSLASH",
 /*  65 */ "basic_text ::= ESCAPEDBACKSLASH MULTIPLIER",
 /*  66 */ "basic_text ::= basic_text TEXT",
 /*  67 */ "basic_text ::= basic_text TEXT MULTIPLIER",
 /*  68 */ "basic_text ::= basic_text FULLSTOP",
 /*  69 */ "basic_text ::= basic_text FULLSTOP MULTIPLIER",
 /*  70 */ "basic_text ::= basic_text CONTROLCHAR",
 /*  71 */ "basic_text ::= basic_text CONTROLCHAR MULTIPLIER",
 /*  72 */ "basic_text ::= basic_text ESCAPEDBACKSLASH",
 /*  73 */ "basic_text ::= basic_text ESCAPEDBACKSLASH MULTIPLIER",
 /*  74 */ "assertion ::= OPENASSERTION INTERNALOPTIONS CLOSEPAREN",
 /*  75 */ "assertion ::= OPENASSERTION INTERNALOPTIONS COLON pattern CLOSEPAREN",
 /*  76 */ "grouping ::= OPENASSERTION COLON pattern CLOSEPAREN",
 /*  77 */ "grouping ::= OPENASSERTION COLON pattern CLOSEPAREN MULTIPLIER",
 /*  78 */ "conditional ::= OPENASSERTION OPENPAREN TEXT CLOSEPAREN pattern CLOSEPAREN MULTIPLIER",
 /*  79 */ "conditional ::= OPENASSERTION OPENPAREN TEXT CLOSEPAREN pattern CLOSEPAREN",
 /*  80 */ "conditional ::= OPENASSERTION lookahead pattern CLOSEPAREN",
 /*  81 */ "conditional ::= OPENASSERTION lookahead pattern CLOSEPAREN MULTIPLIER",
 /*  82 */ "conditional ::= OPENASSERTION lookbehind pattern CLOSEPAREN",
 /*  83 */ "conditional ::= OPENASSERTION lookbehind pattern CLOSEPAREN MULTIPLIER",
 /*  84 */ "lookahead ::= OPENASSERTION POSITIVELOOKAHEAD pattern CLOSEPAREN",
 /*  85 */ "lookahead ::= OPENASSERTION NEGATIVELOOKAHEAD pattern CLOSEPAREN",
 /*  86 */ "lookbehind ::= OPENASSERTION POSITIVELOOKBEHIND pattern CLOSEPAREN",
 /*  87 */ "lookbehind ::= OPENASSERTION NEGATIVELOOKBEHIND pattern CLOSEPAREN",
 /*  88 */ "subpattern ::= OPENASSERTION PATTERNNAME pattern CLOSEPAREN",
 /*  89 */ "subpattern ::= OPENASSERTION PATTERNNAME pattern CLOSEPAREN MULTIPLIER",
 /*  90 */ "subpattern ::= OPENPAREN pattern CLOSEPAREN",
 /*  91 */ "subpattern ::= OPENPAREN pattern CLOSEPAREN MULTIPLIER",
 /*  92 */ "onceonly ::= OPENASSERTION ONCEONLY pattern CLOSEPAREN",
 /*  93 */ "comment ::= OPENASSERTION COMMENT CLOSEPAREN",
 /*  94 */ "recur ::= OPENASSERTION RECUR CLOSEPAREN",
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
     * @param PHP_LexerGenerator_Regex_yyParser
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
                        $x = new PHP_LexerGenerator_Regex_yyStackEntry;
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
                        $x = new PHP_LexerGenerator_Regex_yyStackEntry;
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
        $yytos = new PHP_LexerGenerator_Regex_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for($i = 1; $i <= $this->yyidx; $i++) {
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
  array( 'lhs' => 29, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 33, 'rhs' => 3 ),
  array( 'lhs' => 33, 'rhs' => 4 ),
  array( 'lhs' => 33, 'rhs' => 4 ),
  array( 'lhs' => 33, 'rhs' => 5 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 34, 'rhs' => 3 ),
  array( 'lhs' => 34, 'rhs' => 5 ),
  array( 'lhs' => 35, 'rhs' => 4 ),
  array( 'lhs' => 35, 'rhs' => 5 ),
  array( 'lhs' => 42, 'rhs' => 7 ),
  array( 'lhs' => 42, 'rhs' => 6 ),
  array( 'lhs' => 42, 'rhs' => 4 ),
  array( 'lhs' => 42, 'rhs' => 5 ),
  array( 'lhs' => 42, 'rhs' => 4 ),
  array( 'lhs' => 42, 'rhs' => 5 ),
  array( 'lhs' => 36, 'rhs' => 4 ),
  array( 'lhs' => 36, 'rhs' => 4 ),
  array( 'lhs' => 37, 'rhs' => 4 ),
  array( 'lhs' => 37, 'rhs' => 4 ),
  array( 'lhs' => 38, 'rhs' => 4 ),
  array( 'lhs' => 38, 'rhs' => 5 ),
  array( 'lhs' => 38, 'rhs' => 3 ),
  array( 'lhs' => 38, 'rhs' => 4 ),
  array( 'lhs' => 39, 'rhs' => 4 ),
  array( 'lhs' => 40, 'rhs' => 3 ),
  array( 'lhs' => 41, 'rhs' => 3 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        6 => 4,
        7 => 4,
        9 => 4,
        10 => 4,
        12 => 4,
        13 => 4,
        14 => 4,
        15 => 4,
        16 => 4,
        5 => 5,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 19,
        22 => 19,
        23 => 19,
        25 => 19,
        26 => 19,
        27 => 19,
        28 => 19,
        29 => 19,
        30 => 30,
        31 => 31,
        32 => 32,
        33 => 33,
        34 => 34,
        35 => 35,
        36 => 36,
        58 => 36,
        60 => 36,
        37 => 37,
        62 => 37,
        38 => 38,
        64 => 38,
        39 => 39,
        40 => 40,
        41 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        45 => 45,
        46 => 46,
        47 => 47,
        70 => 47,
        48 => 48,
        72 => 48,
        49 => 49,
        66 => 49,
        68 => 49,
        50 => 50,
        51 => 51,
        52 => 52,
        53 => 53,
        54 => 54,
        55 => 55,
        56 => 56,
        57 => 57,
        59 => 59,
        61 => 59,
        63 => 63,
        65 => 65,
        67 => 67,
        69 => 67,
        71 => 71,
        73 => 73,
        74 => 74,
        75 => 75,
        76 => 76,
        77 => 77,
        78 => 78,
        79 => 79,
        80 => 80,
        81 => 81,
        82 => 82,
        86 => 82,
        83 => 83,
        84 => 84,
        85 => 85,
        87 => 87,
        88 => 88,
        89 => 89,
        90 => 90,
        91 => 91,
        92 => 92,
        93 => 93,
        94 => 94,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 42 "LexerGenerator\Regex\Parser.y"
    function yy_r0(){
    $this->yystack[$this->yyidx + 0]->minor->string = str_replace('"', '\\"', $this->yystack[$this->yyidx + 0]->minor->string);
    $x = $this->yystack[$this->yyidx + 0]->minor->metadata;
    $x['subpatterns'] = $this->_subpatterns;
    $this->yystack[$this->yyidx + 0]->minor->metadata = $x;
    $this->_subpatterns = 0;
    $this->result = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1262 "LexerGenerator\Regex\Parser.php"
#line 51 "LexerGenerator\Regex\Parser.y"
    function yy_r1(){
    throw new PHP_LexerGenerator_Exception('Cannot include start match "' .
        $this->yystack[$this->yyidx + -2]->minor . '" or end match "' . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1268 "LexerGenerator\Regex\Parser.php"
#line 55 "LexerGenerator\Regex\Parser.y"
    function yy_r2(){
    throw new PHP_LexerGenerator_Exception('Cannot include start match "' .
        B . '"');
    }
#line 1274 "LexerGenerator\Regex\Parser.php"
#line 59 "LexerGenerator\Regex\Parser.y"
    function yy_r3(){
    throw new PHP_LexerGenerator_Exception('Cannot include end match "' . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1279 "LexerGenerator\Regex\Parser.php"
#line 62 "LexerGenerator\Regex\Parser.y"
    function yy_r4(){$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;    }
#line 1282 "LexerGenerator\Regex\Parser.php"
#line 63 "LexerGenerator\Regex\Parser.y"
    function yy_r5(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '|' . $this->yystack[$this->yyidx + 0]->minor->string, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . '|' . $this->yystack[$this->yyidx + 0]->minor['pattern']));
    }
#line 1288 "LexerGenerator\Regex\Parser.php"
#line 79 "LexerGenerator\Regex\Parser.y"
    function yy_r17(){
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception('Back-reference refers to non-existent ' .
            'sub-pattern ' . substr($this->yystack[$this->yyidx + 0]->minor, 1));
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    // adjust back-reference for containing ()
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . ($this->yystack[$this->yyidx + 0]->minor + 1), array(
        'pattern' => '\\' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1300 "LexerGenerator\Regex\Parser.php"
#line 89 "LexerGenerator\Regex\Parser.y"
    function yy_r18(){
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception($this->yystack[$this->yyidx + 0]->minor . ' will be interpreted as an invalid' .
            ' back-reference, use "\\0' . substr($this->yystack[$this->yyidx + 0]->minor, 1) . ' for octal');
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . ($this->yystack[$this->yyidx + 0]->minor + 1), array(
        'pattern' => '\\' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1311 "LexerGenerator\Regex\Parser.php"
#line 98 "LexerGenerator\Regex\Parser.y"
    function yy_r19(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . $this->yystack[$this->yyidx + 0]->minor->string, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor['pattern']));
    }
#line 1317 "LexerGenerator\Regex\Parser.php"
#line 136 "LexerGenerator\Regex\Parser.y"
    function yy_r30(){
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception('Back-reference refers to non-existent ' .
            'sub-pattern ' . substr($this->yystack[$this->yyidx + 0]->minor, 1));
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\\\' . ($this->yystack[$this->yyidx + 0]->minor + 1), array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . '\\' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1328 "LexerGenerator\Regex\Parser.php"
#line 145 "LexerGenerator\Regex\Parser.y"
    function yy_r31(){
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception($this->yystack[$this->yyidx + 0]->minor . ' will be interpreted as an invalid' .
            ' back-reference, use "\\0' . substr($this->yystack[$this->yyidx + 0]->minor, 1) . ' for octal');
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\\\' . ($this->yystack[$this->yyidx + 0]->minor + 1), array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . '\\' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1339 "LexerGenerator\Regex\Parser.php"
#line 155 "LexerGenerator\Regex\Parser.y"
    function yy_r32(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[' . $this->yystack[$this->yyidx + -1]->minor->string . ']', array(
        'pattern' => '[' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ']'));
    }
#line 1345 "LexerGenerator\Regex\Parser.php"
#line 159 "LexerGenerator\Regex\Parser.y"
    function yy_r33(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[^' . $this->yystack[$this->yyidx + -1]->minor->string . ']', array(
        'pattern' => '[^' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ']'));
    }
#line 1351 "LexerGenerator\Regex\Parser.php"
#line 163 "LexerGenerator\Regex\Parser.y"
    function yy_r34(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[' . $this->yystack[$this->yyidx + -2]->minor->string . ']' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '[' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ']' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1357 "LexerGenerator\Regex\Parser.php"
#line 167 "LexerGenerator\Regex\Parser.y"
    function yy_r35(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[^' . $this->yystack[$this->yyidx + -2]->minor->string . ']' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '[^' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ']' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1363 "LexerGenerator\Regex\Parser.php"
#line 172 "LexerGenerator\Regex\Parser.y"
    function yy_r36(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1369 "LexerGenerator\Regex\Parser.php"
#line 176 "LexerGenerator\Regex\Parser.y"
    function yy_r37(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1375 "LexerGenerator\Regex\Parser.php"
#line 180 "LexerGenerator\Regex\Parser.y"
    function yy_r38(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1381 "LexerGenerator\Regex\Parser.php"
#line 184 "LexerGenerator\Regex\Parser.y"
    function yy_r39(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1387 "LexerGenerator\Regex\Parser.php"
#line 188 "LexerGenerator\Regex\Parser.y"
    function yy_r40(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1393 "LexerGenerator\Regex\Parser.php"
#line 192 "LexerGenerator\Regex\Parser.y"
    function yy_r41(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1399 "LexerGenerator\Regex\Parser.php"
#line 196 "LexerGenerator\Regex\Parser.y"
    function yy_r42(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1405 "LexerGenerator\Regex\Parser.php"
#line 200 "LexerGenerator\Regex\Parser.y"
    function yy_r43(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1411 "LexerGenerator\Regex\Parser.php"
#line 204 "LexerGenerator\Regex\Parser.y"
    function yy_r44(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1417 "LexerGenerator\Regex\Parser.php"
#line 208 "LexerGenerator\Regex\Parser.y"
    function yy_r45(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1423 "LexerGenerator\Regex\Parser.php"
#line 212 "LexerGenerator\Regex\Parser.y"
    function yy_r46(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor . '-\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1429 "LexerGenerator\Regex\Parser.php"
#line 216 "LexerGenerator\Regex\Parser.y"
    function yy_r47(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1435 "LexerGenerator\Regex\Parser.php"
#line 220 "LexerGenerator\Regex\Parser.y"
    function yy_r48(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1441 "LexerGenerator\Regex\Parser.php"
#line 224 "LexerGenerator\Regex\Parser.y"
    function yy_r49(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1447 "LexerGenerator\Regex\Parser.php"
#line 228 "LexerGenerator\Regex\Parser.y"
    function yy_r50(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1453 "LexerGenerator\Regex\Parser.php"
#line 232 "LexerGenerator\Regex\Parser.y"
    function yy_r51(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1459 "LexerGenerator\Regex\Parser.php"
#line 236 "LexerGenerator\Regex\Parser.y"
    function yy_r52(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1465 "LexerGenerator\Regex\Parser.php"
#line 240 "LexerGenerator\Regex\Parser.y"
    function yy_r53(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\' . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1471 "LexerGenerator\Regex\Parser.php"
#line 244 "LexerGenerator\Regex\Parser.y"
    function yy_r54(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1477 "LexerGenerator\Regex\Parser.php"
#line 248 "LexerGenerator\Regex\Parser.y"
    function yy_r55(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1483 "LexerGenerator\Regex\Parser.php"
#line 252 "LexerGenerator\Regex\Parser.y"
    function yy_r56(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor . '-\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1489 "LexerGenerator\Regex\Parser.php"
#line 256 "LexerGenerator\Regex\Parser.y"
    function yy_r57(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1495 "LexerGenerator\Regex\Parser.php"
#line 265 "LexerGenerator\Regex\Parser.y"
    function yy_r59(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1501 "LexerGenerator\Regex\Parser.php"
#line 281 "LexerGenerator\Regex\Parser.y"
    function yy_r63(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1507 "LexerGenerator\Regex\Parser.php"
#line 289 "LexerGenerator\Regex\Parser.y"
    function yy_r65(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1513 "LexerGenerator\Regex\Parser.php"
#line 297 "LexerGenerator\Regex\Parser.y"
    function yy_r67(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1519 "LexerGenerator\Regex\Parser.php"
#line 313 "LexerGenerator\Regex\Parser.y"
    function yy_r71(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1525 "LexerGenerator\Regex\Parser.php"
#line 321 "LexerGenerator\Regex\Parser.y"
    function yy_r73(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '\\\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1531 "LexerGenerator\Regex\Parser.php"
#line 326 "LexerGenerator\Regex\Parser.y"
    function yy_r74(){
    throw new PHP_LexerGenerator_Exception('Error: cannot set preg options directly with "' .
        $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1537 "LexerGenerator\Regex\Parser.php"
#line 330 "LexerGenerator\Regex\Parser.y"
    function yy_r75(){
    throw new PHP_LexerGenerator_Exception('Error: cannot set preg options directly with "' .
        $this->yystack[$this->yyidx + -4]->minor . $this->yystack[$this->yyidx + -3]->minor . $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1543 "LexerGenerator\Regex\Parser.php"
#line 335 "LexerGenerator\Regex\Parser.y"
    function yy_r76(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?:' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?:' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1549 "LexerGenerator\Regex\Parser.php"
#line 339 "LexerGenerator\Regex\Parser.y"
    function yy_r77(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?:' . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(?:' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1555 "LexerGenerator\Regex\Parser.php"
#line 344 "LexerGenerator\Regex\Parser.y"
    function yy_r78(){
    if ($this->yystack[$this->yyidx + -4]->minor != 'R') {
        if (!preg_match('/[1-9][0-9]*/', $this->yystack[$this->yyidx + -4]->minor)) {
            throw new PHP_LexerGenerator_Exception('Invalid sub-pattern conditional: "(?(' . $this->yystack[$this->yyidx + -4]->minor . ')"');
        }
        if ($this->yystack[$this->yyidx + -4]->minor > $this->_subpatterns) {
            throw new PHP_LexerGenerator_Exception('sub-pattern conditional . "' . $this->yystack[$this->yyidx + -4]->minor . '" refers to non-existent sub-pattern');
        }
    }
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?(' . $this->yystack[$this->yyidx + -4]->minor . ')' . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(?(' . $this->yystack[$this->yyidx + -4]->minor . ')' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1569 "LexerGenerator\Regex\Parser.php"
#line 356 "LexerGenerator\Regex\Parser.y"
    function yy_r79(){
    if ($this->yystack[$this->yyidx + -3]->minor != 'R') {
        if (!preg_match('/[1-9][0-9]*/', $this->yystack[$this->yyidx + -3]->minor)) {
            throw new PHP_LexerGenerator_Exception('Invalid sub-pattern conditional: "(?(' . $this->yystack[$this->yyidx + -3]->minor . ')"');
        }
        if ($this->yystack[$this->yyidx + -3]->minor > $this->_subpatterns) {
            throw new PHP_LexerGenerator_Exception('sub-pattern conditional . "' . $this->yystack[$this->yyidx + -3]->minor . '" refers to non-existent sub-pattern');
        }
    }
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?(' . $this->yystack[$this->yyidx + -3]->minor . ')' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?(' . $this->yystack[$this->yyidx + -3]->minor . ')' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1583 "LexerGenerator\Regex\Parser.php"
#line 368 "LexerGenerator\Regex\Parser.y"
    function yy_r80(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?' . $this->yystack[$this->yyidx + -2]->minor->string . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1589 "LexerGenerator\Regex\Parser.php"
#line 372 "LexerGenerator\Regex\Parser.y"
    function yy_r81(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?' . $this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(?' . $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1595 "LexerGenerator\Regex\Parser.php"
#line 376 "LexerGenerator\Regex\Parser.y"
    function yy_r82(){
    throw new PHP_LexerGenerator_Exception('Look-behind assertions cannot be used: "(?<=' .
        $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')');
    }
#line 1601 "LexerGenerator\Regex\Parser.php"
#line 380 "LexerGenerator\Regex\Parser.y"
    function yy_r83(){
    throw new PHP_LexerGenerator_Exception('Look-behind assertions cannot be used: "(?<=' .
        $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')');
    }
#line 1607 "LexerGenerator\Regex\Parser.php"
#line 385 "LexerGenerator\Regex\Parser.y"
    function yy_r84(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?=' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern '=> '(?=' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1613 "LexerGenerator\Regex\Parser.php"
#line 389 "LexerGenerator\Regex\Parser.y"
    function yy_r85(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?!' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?!' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1619 "LexerGenerator\Regex\Parser.php"
#line 398 "LexerGenerator\Regex\Parser.y"
    function yy_r87(){
    throw new PHP_LexerGenerator_Exception('Look-behind assertions cannot be used: "(?<!' .
        $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')');
    }
#line 1625 "LexerGenerator\Regex\Parser.php"
#line 403 "LexerGenerator\Regex\Parser.y"
    function yy_r88(){
    throw new PHP_LexerGenerator_Exception('Cannot use named sub-patterns: "(' .
        $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')');
    }
#line 1631 "LexerGenerator\Regex\Parser.php"
#line 407 "LexerGenerator\Regex\Parser.y"
    function yy_r89(){
    throw new PHP_LexerGenerator_Exception('Cannot use named sub-patterns: "(' .
        $this->yystack[$this->yyidx + -3]->minor['pattern'] . ')');
    }
#line 1637 "LexerGenerator\Regex\Parser.php"
#line 411 "LexerGenerator\Regex\Parser.y"
    function yy_r90(){
    $this->_subpatterns++;
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1644 "LexerGenerator\Regex\Parser.php"
#line 416 "LexerGenerator\Regex\Parser.y"
    function yy_r91(){
    $this->_subpatterns++;
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(' . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1651 "LexerGenerator\Regex\Parser.php"
#line 422 "LexerGenerator\Regex\Parser.y"
    function yy_r92(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?>' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?>' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1657 "LexerGenerator\Regex\Parser.php"
#line 427 "LexerGenerator\Regex\Parser.y"
    function yy_r93(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1663 "LexerGenerator\Regex\Parser.php"
#line 432 "LexerGenerator\Regex\Parser.y"
    function yy_r94(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?R)', array(
        'pattern' => '(?R)'));
    }
#line 1669 "LexerGenerator\Regex\Parser.php"

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
        //PHP_LexerGenerator_Regex_yyStackEntry $yymsp;            /* The top of the parser's stack */
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
        for($i = $yysize; $i; $i--) {
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
                $x = new PHP_LexerGenerator_Regex_yyStackEntry;
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
#line 6 "LexerGenerator\Regex\Parser.y"

/* ?><?php */
    // we need to add auto-escaping of all stuff that needs it for result.
    // and then validate the original regex only
    echo "Syntax Error on line " . $this->_lex->line . ": token '" . 
        $this->_lex->value . "' while parsing rule:";
    foreach ($this->yystack as $entry) {
        echo $this->tokenName($entry->major) . ' ';
    }
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
#line 1797 "LexerGenerator\Regex\Parser.php"
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
     * @param int the token number
     * @param mixed the token value
     * @param mixed any extra arguments that should be passed to handlers
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
            $x = new PHP_LexerGenerator_Regex_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
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
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
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
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ){
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
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