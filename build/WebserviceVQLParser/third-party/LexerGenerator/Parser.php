<?php
/* Driver template for the PHP_PHP_LexerGenerator_ParserrGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class PHP_LexerGenerator_ParseryyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof PHP_LexerGenerator_ParseryyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof PHP_LexerGenerator_ParseryyToken) {
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
                $x = ($value instanceof PHP_LexerGenerator_ParseryyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof PHP_LexerGenerator_ParseryyToken) {
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
class PHP_LexerGenerator_ParseryyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 3 "LexerGenerator\Parser.y"

/* ?><?php {//*/
/**
 * PHP_LexerGenerator, a php 5 lexer generator.
 * 
 * This lexer generator translates a file in a format similar to
 * re2c ({@link http://re2c.org}) and translates it into a PHP 5-based lexer
 *
 * PHP version 5
 *
 * LICENSE:
 * 
 * Copyright (c) 2006, Gregory Beaver <cellog@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in
 *       the documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PHP_LexerGenerator nor the names of its
 *       contributors may be used to endorse or promote products derived
 *       from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   php
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @since      File available since Release 0.1.0
 */
/**
 * For regular expression validation
 */
require_once 'PHP/LexerGenerator/Regex/Lexer.php';
require_once 'PHP/LexerGenerator/Regex/Parser.php';
require_once 'PHP/LexerGenerator/Exception.php';
/**
 * Token parser for plex files.
 * 
 * This parser converts tokens pulled from {@link PHP_LexerGenerator_Lexer}
 * into abstract patterns and rules, then creates the output file
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.2.0
 * @since      Class available since Release 0.1.0
 */
#line 166 "LexerGenerator\Parser.php"

// declare_class is output here
#line 2 "LexerGenerator\Parser.y"
class PHP_LexerGenerator_Parser#line 171 "LexerGenerator\Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 82 "LexerGenerator\Parser.y"

    private $patterns;
    private $out;
    private $lex;
    private $input;
    private $counter;
    private $token;
    private $value;
    private $line;
    private $_regexLexer;
    private $_regexParser;

    public $transTable = array(
        1 => self::PHPCODE,
        2 => self::COMMENTSTART,
        3 => self::COMMENTEND,
        4 => self::QUOTE,
        5 => self::PATTERN,
        6 => self::CODE,
        7 => self::SUBPATTERN,
        8 => self::PI,
    );

    function __construct($outfile, $lex)
    {
        $this->out = fopen($outfile, 'wb');
        if (!$this->out) {
            throw new Exception('unable to open lexer output file "' . $outfile . '"');
        }
        $this->lex = $lex;
        $this->_regexLexer = new PHP_LexerGenerator_Regex_Lexer('');
        $this->_regexParser = new PHP_LexerGenerator_Regex_Parser($this->_regexLexer);
    }

    function outputRules($rules, $statename)
    {
        static $ruleindex = 1;
        $patterns = array();
        $pattern = '/';
        $ruleMap = array();
        $tokenindex = array();
        $i = 0;
        $actualindex = 1;
        foreach ($rules as $rule) {
            $ruleMap[$i++] = $actualindex;
            $tokenindex[$actualindex] = $rule['subpatterns'];
            $actualindex += $rule['subpatterns'] + 1;
            $patterns[] = '^(' . $rule['pattern'] . ')';
        }
        $tokenindex = var_export($tokenindex, true);
        $tokenindex = explode("\n", $tokenindex);
        // indent for prettiness
        $tokenindex = implode("\n            ", $tokenindex);
        $pattern .= implode('|', $patterns);
        $pattern .= '/';
        if (!$statename) {
            $statename = $ruleindex;
        }
        fwrite($this->out, '
    function yylex' . $ruleindex . '()
    {
        $tokenMap = ' . $tokenindex . ';
        if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
            return false; // end of input
        }
        ');
        fwrite($this->out, '$yy_global_pattern = "' .
            $pattern . '";' . "\n");
        fwrite($this->out, '
        do {
            if (preg_match($yy_global_pattern, substr(' . $this->input . ', ' .
             $this->counter .
                    '), $yymatches)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, \'strlen\'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception(\'Error: lexing failed because a rule matched\' .
                        \'an empty string.  Input "\' . substr(' . $this->input . ',
                        ' . $this->counter . ', 5) . \'... state ' . $statename . '\');
                }
                next($yymatches); // skip global match
                ' . $this->token . ' = key($yymatches); // token number
                if ($tokenMap[' . $this->token . ']) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, ' . $this->token . ' + 1,
                        $tokenMap[' . $this->token . ']);
                } else {
                    $yysubmatches = array();
                }
                ' . $this->value . ' = current($yymatches); // token value
                $r = $this->{\'yy_r' . $ruleindex . '_\' . ' . $this->token . '}($yysubmatches);
                if ($r === null) {
                    ' . $this->counter . ' += strlen($this->value);
                    ' . $this->line . ' += substr_count("\n", ' . $this->value . ');
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    ' . $this->counter . ' += strlen($this->value);
                    ' . $this->line . ' += substr_count("\n", ' . $this->value . ');
                    if (' . $this->counter . ' >= strlen(' . $this->input . ')) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {');
        fwrite($this->out, '                    $yy_yymore_patterns = array(' . "\n");
        for($i = 0; count($patterns); $i++) {
            unset($patterns[$i]);
            fwrite($this->out, '        ' . $ruleMap[$i] . ' => "' .
                implode('|', $patterns) . "\",\n");
        }
        fwrite($this->out, '    );' . "\n");
        fwrite($this->out, '
                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[' . $this->token . '])) {
                            throw new Exception(\'cannot do yymore for the last token\');
                        }
                        if (preg_match($yy_yymore_patterns[' . $this->token . '],
                              substr(' . $this->input . ', ' . $this->counter . '), $yymatches)) {
                            $yymatches = array_filter($yymatches, \'strlen\'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            ' . $this->token . ' = key($yymatches); // token number
                            ' . $this->value . ' = current($yymatches); // token value
                            ' . $this->line . ' = substr_count("\n", ' . $this->value . ');
                        }
                    } while ($this->{\'yy_r' . $ruleindex . '_\' . ' . $this->token . '}() !== null);
                    // accept
                    ' . $this->counter . ' += strlen($this->value);
                    ' . $this->line . ' += substr_count("\n", ' . $this->value . ');
                    return true;
                }
            } else {
                throw new Exception(\'Unexpected input at line\' . ' . $this->line . ' .
                    \': \' . ' . $this->input . '[' . $this->counter . ']);
            }
            break;
        } while (true);
    } // end function

');
        if ($statename) {
            fwrite($this->out, '
    const ' . $statename . ' = ' . $ruleindex . ';
');
        }
        foreach ($rules as $i => $rule) {
            fwrite($this->out, '    function yy_r' . $ruleindex . '_' . $ruleMap[$i] . '($yy_subpatterns)
    {
' . $rule['code'] .
'    }
');
        }
        $ruleindex++; // for next set of rules
    }

    function error($msg)
    {
        echo 'Error on line ' . $this->lex->line . ': ' , $msg;
    }

    function _validatePattern($pattern)
    {
        $this->_regexLexer->reset($pattern);
        try {
            while ($this->_regexLexer->yylex()) {
                $this->_regexParser->doParse(
                    $this->_regexLexer->token, $this->_regexLexer->value);
            }
            $this->_regexParser->doParse(0, 0);
        } catch (PHP_LexerGenerator_Exception $e) {
            $this->error($e->getMessage());
            throw new PHP_LexerGenerator_Exception('Invalid pattern "' . $pattern . '"');
        }
        return $this->_regexParser->result;
    }
#line 357 "LexerGenerator\Parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const PHPCODE                        =  1;
    const COMMENTSTART                   =  2;
    const COMMENTEND                     =  3;
    const PI                             =  4;
    const SUBPATTERN                     =  5;
    const CODE                           =  6;
    const PATTERN                        =  7;
    const QUOTE                          =  8;
    const YY_NO_ACTION = 94;
    const YY_ACCEPT_ACTION = 93;
    const YY_ERROR_ACTION = 92;

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
    const YY_SZ_ACTTAB = 87;
static public $yy_action = array(
 /*     0 */    33,   31,   58,   58,    3,   50,   50,   57,   44,   39,
 /*    10 */    42,   58,   57,   55,   50,   42,   51,   36,   58,   58,
 /*    20 */    59,   50,   50,   38,   58,   46,   45,   50,   35,   58,
 /*    30 */    17,    2,   50,   93,   52,   16,   18,    6,   24,   19,
 /*    40 */     2,   12,   41,   53,   48,   40,   30,   60,    1,    4,
 /*    50 */    34,   10,   20,   43,   49,   32,   14,   58,    7,   20,
 /*    60 */    50,    8,   20,    9,   20,   37,   47,   11,   20,   56,
 /*    70 */    15,    5,   22,   54,   28,   23,   53,   21,   29,   25,
 /*    80 */     2,   27,    6,   13,   53,   53,   26,
    );
    static public $yy_lookahead = array(
 /*     0 */     3,    3,    5,    5,    5,    8,    8,    5,    6,    3,
 /*    10 */     8,    5,    5,    6,    8,    8,    3,    3,    5,    5,
 /*    20 */     3,    8,    8,    4,    5,    5,    6,    8,    4,    5,
 /*    30 */     1,    2,    8,   10,   11,   12,    1,    2,    4,    1,
 /*    40 */     2,    7,    5,    1,    5,    8,   16,    8,    2,    5,
 /*    50 */     4,   18,   19,    5,    6,   14,   15,    5,   18,   19,
 /*    60 */     8,   18,   19,   18,   19,    5,    1,   18,   19,    1,
 /*    70 */     7,    2,   13,    1,   17,   13,   20,   19,    4,   13,
 /*    80 */     2,   17,    2,   12,   20,   20,   13,
);
    const YY_SHIFT_USE_DFLT = -4;
    const YY_SHIFT_MAX = 39;
    static public $yy_shift_ofst = array(
 /*     0 */    35,   24,   19,   52,   52,   52,   74,   13,   -2,   -3,
 /*    10 */    14,    6,   37,   38,   34,   37,   29,   78,   80,   78,
 /*    20 */     7,    2,   46,   46,   48,   46,   46,   39,   39,   20,
 /*    30 */    63,   42,   17,   72,   60,   -1,   68,   69,   44,   65,
);
    const YY_REDUCE_USE_DFLT = -1;
    const YY_REDUCE_MAX = 19;
    static public $yy_reduce_ofst = array(
 /*     0 */    23,   49,   45,   33,   43,   40,   41,   58,   58,   58,
 /*    10 */    58,   58,   64,   59,   30,   57,   62,   73,   71,   66,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(1, 2, ),
        /* 1 */ array(4, 5, 8, ),
        /* 2 */ array(4, 5, 8, ),
        /* 3 */ array(5, 8, ),
        /* 4 */ array(5, 8, ),
        /* 5 */ array(5, 8, ),
        /* 6 */ array(4, ),
        /* 7 */ array(3, 5, 8, ),
        /* 8 */ array(3, 5, 8, ),
        /* 9 */ array(3, 5, 8, ),
        /* 10 */ array(3, 5, 8, ),
        /* 11 */ array(3, 5, 8, ),
        /* 12 */ array(5, 8, ),
        /* 13 */ array(1, 2, ),
        /* 14 */ array(4, 7, ),
        /* 15 */ array(5, 8, ),
        /* 16 */ array(1, 2, ),
        /* 17 */ array(2, ),
        /* 18 */ array(2, ),
        /* 19 */ array(2, ),
        /* 20 */ array(5, 6, 8, ),
        /* 21 */ array(5, 6, 8, ),
        /* 22 */ array(2, 4, ),
        /* 23 */ array(2, 4, ),
        /* 24 */ array(5, 6, ),
        /* 25 */ array(2, 4, ),
        /* 26 */ array(2, 4, ),
        /* 27 */ array(5, 8, ),
        /* 28 */ array(5, 8, ),
        /* 29 */ array(5, 6, ),
        /* 30 */ array(7, ),
        /* 31 */ array(1, ),
        /* 32 */ array(3, ),
        /* 33 */ array(1, ),
        /* 34 */ array(5, ),
        /* 35 */ array(5, ),
        /* 36 */ array(1, ),
        /* 37 */ array(2, ),
        /* 38 */ array(5, ),
        /* 39 */ array(1, ),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(),
        /* 43 */ array(),
        /* 44 */ array(),
        /* 45 */ array(),
        /* 46 */ array(),
        /* 47 */ array(),
        /* 48 */ array(),
        /* 49 */ array(),
        /* 50 */ array(),
        /* 51 */ array(),
        /* 52 */ array(),
        /* 53 */ array(),
        /* 54 */ array(),
        /* 55 */ array(),
        /* 56 */ array(),
        /* 57 */ array(),
        /* 58 */ array(),
        /* 59 */ array(),
        /* 60 */ array(),
);
    static public $yy_default = array(
 /*     0 */    92,   92,   92,   92,   92,   92,   92,   92,   92,   92,
 /*    10 */    92,   92,   92,   92,   92,   92,   92,   92,   92,   92,
 /*    20 */    92,   92,   64,   62,   92,   65,   63,   72,   73,   92,
 /*    30 */    67,   75,   92,   74,   92,   92,   92,   92,   92,   78,
 /*    40 */    88,   89,   86,   70,   83,   69,   68,   80,   91,   71,
 /*    50 */    84,   79,   61,   77,   76,   82,   81,   87,   85,   66,
 /*    60 */    90,
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
    const YYNOCODE = 21;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 61;
    const YYNRULE = 31;
    const YYERRORSYMBOL = 9;
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
  '$',             'PHPCODE',       'COMMENTSTART',  'COMMENTEND',  
  'PI',            'SUBPATTERN',    'CODE',          'PATTERN',     
  'QUOTE',         'error',         'start',         'lexfile',     
  'declare',       'rules',         'declarations',  'processing_instructions',
  'pattern_declarations',  'subpattern',    'rule',          'rule_subpattern',
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= lexfile",
 /*   1 */ "lexfile ::= declare rules",
 /*   2 */ "lexfile ::= declare PHPCODE rules",
 /*   3 */ "lexfile ::= PHPCODE declare rules",
 /*   4 */ "lexfile ::= PHPCODE declare PHPCODE rules",
 /*   5 */ "declare ::= COMMENTSTART declarations COMMENTEND",
 /*   6 */ "declarations ::= processing_instructions pattern_declarations",
 /*   7 */ "processing_instructions ::= PI SUBPATTERN",
 /*   8 */ "processing_instructions ::= PI CODE",
 /*   9 */ "processing_instructions ::= processing_instructions PI SUBPATTERN",
 /*  10 */ "processing_instructions ::= processing_instructions PI CODE",
 /*  11 */ "pattern_declarations ::= PATTERN subpattern",
 /*  12 */ "pattern_declarations ::= pattern_declarations PATTERN subpattern",
 /*  13 */ "rules ::= COMMENTSTART rule COMMENTEND",
 /*  14 */ "rules ::= COMMENTSTART PI SUBPATTERN rule COMMENTEND",
 /*  15 */ "rules ::= COMMENTSTART rule COMMENTEND PHPCODE",
 /*  16 */ "rules ::= COMMENTSTART PI SUBPATTERN rule COMMENTEND PHPCODE",
 /*  17 */ "rules ::= rules COMMENTSTART rule COMMENTEND",
 /*  18 */ "rules ::= rules PI SUBPATTERN COMMENTSTART rule COMMENTEND",
 /*  19 */ "rules ::= rules COMMENTSTART rule COMMENTEND PHPCODE",
 /*  20 */ "rules ::= rules COMMENTSTART PI SUBPATTERN rule COMMENTEND PHPCODE",
 /*  21 */ "rule ::= rule_subpattern CODE",
 /*  22 */ "rule ::= rule rule_subpattern CODE",
 /*  23 */ "rule_subpattern ::= QUOTE",
 /*  24 */ "rule_subpattern ::= SUBPATTERN",
 /*  25 */ "rule_subpattern ::= rule_subpattern QUOTE",
 /*  26 */ "rule_subpattern ::= rule_subpattern SUBPATTERN",
 /*  27 */ "subpattern ::= QUOTE",
 /*  28 */ "subpattern ::= SUBPATTERN",
 /*  29 */ "subpattern ::= subpattern QUOTE",
 /*  30 */ "subpattern ::= subpattern SUBPATTERN",
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
     * @param PHP_LexerGenerator_ParseryyParser
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
                        $x = new PHP_LexerGenerator_ParseryyStackEntry;
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
                        $x = new PHP_LexerGenerator_ParseryyStackEntry;
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
        $yytos = new PHP_LexerGenerator_ParseryyStackEntry;
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
  array( 'lhs' => 10, 'rhs' => 1 ),
  array( 'lhs' => 11, 'rhs' => 2 ),
  array( 'lhs' => 11, 'rhs' => 3 ),
  array( 'lhs' => 11, 'rhs' => 3 ),
  array( 'lhs' => 11, 'rhs' => 4 ),
  array( 'lhs' => 12, 'rhs' => 3 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 3 ),
  array( 'lhs' => 15, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 3 ),
  array( 'lhs' => 13, 'rhs' => 3 ),
  array( 'lhs' => 13, 'rhs' => 5 ),
  array( 'lhs' => 13, 'rhs' => 4 ),
  array( 'lhs' => 13, 'rhs' => 6 ),
  array( 'lhs' => 13, 'rhs' => 4 ),
  array( 'lhs' => 13, 'rhs' => 6 ),
  array( 'lhs' => 13, 'rhs' => 5 ),
  array( 'lhs' => 13, 'rhs' => 7 ),
  array( 'lhs' => 18, 'rhs' => 2 ),
  array( 'lhs' => 18, 'rhs' => 3 ),
  array( 'lhs' => 19, 'rhs' => 1 ),
  array( 'lhs' => 19, 'rhs' => 1 ),
  array( 'lhs' => 19, 'rhs' => 2 ),
  array( 'lhs' => 19, 'rhs' => 2 ),
  array( 'lhs' => 17, 'rhs' => 1 ),
  array( 'lhs' => 17, 'rhs' => 1 ),
  array( 'lhs' => 17, 'rhs' => 2 ),
  array( 'lhs' => 17, 'rhs' => 2 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 7,
        9 => 9,
        10 => 9,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 24,
        25 => 25,
        26 => 26,
        27 => 27,
        28 => 28,
        29 => 29,
        30 => 30,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 266 "LexerGenerator\Parser.y"
    function yy_r1(){
    fwrite($this->out, '
    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{\'yylex\' . $this->_yy_state}();
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

');
    foreach ($this->yystack[$this->yyidx + 0]->minor as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
    }
#line 1159 "LexerGenerator\Parser.php"
#line 300 "LexerGenerator\Parser.y"
    function yy_r2(){
    fwrite($this->out, '
    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{\'yylex\' . $this->_yy_state}();
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

');
    if (strlen($this->yystack[$this->yyidx + -1]->minor)) {
        fwrite($this->out, $this->yystack[$this->yyidx + -1]->minor);
    }
    foreach ($this->yystack[$this->yyidx + 0]->minor as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
    }
#line 1198 "LexerGenerator\Parser.php"
#line 337 "LexerGenerator\Parser.y"
    function yy_r3(){
    if (strlen($this->yystack[$this->yyidx + -2]->minor)) {
        fwrite($this->out, $this->yystack[$this->yyidx + -2]->minor);
    }
    fwrite($this->out, '
    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{\'yylex\' . $this->_yy_state}();
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

');
    foreach ($this->yystack[$this->yyidx + 0]->minor as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
    }
#line 1237 "LexerGenerator\Parser.php"
#line 374 "LexerGenerator\Parser.y"
    function yy_r4(){
    if (strlen($this->yystack[$this->yyidx + -3]->minor)) {
        fwrite($this->out, $this->yystack[$this->yyidx + -3]->minor);
    }
    fwrite($this->out, '
    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{\'yylex\' . $this->_yy_state}();
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

');
    if (strlen($this->yystack[$this->yyidx + -1]->minor)) {
        fwrite($this->out, $this->yystack[$this->yyidx + -1]->minor);
    }
    foreach ($this->yystack[$this->yyidx + 0]->minor as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
    }
#line 1279 "LexerGenerator\Parser.php"
#line 415 "LexerGenerator\Parser.y"
    function yy_r5(){
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    $this->patterns = $this->yystack[$this->yyidx + -1]->minor['patterns'];
    }
#line 1285 "LexerGenerator\Parser.php"
#line 420 "LexerGenerator\Parser.y"
    function yy_r6(){
    $expected = array(
        'counter' => true,
        'input' => true,
        'token' => true,
        'value' => true,
        'line' => true,
    );
    foreach ($this->yystack[$this->yyidx + -1]->minor as $pi) {
        if (isset($expected[$pi['pi']])) {
            unset($expected[$pi['pi']]);
            continue;
        }
        if (count($expected)) {
            throw new Exception('Processing Instructions "' .
                implode(', ', array_keys($expected)) . '" must be defined');
        }
    }
    $expected = array(
        'counter' => true,
        'input' => true,
        'token' => true,
        'value' => true,
        'line' => true,
    );
    foreach ($this->yystack[$this->yyidx + -1]->minor as $pi) {
        if (isset($expected[$pi['pi']])) {
            $this->{$pi['pi']} = $pi['definition'];
            continue;
        }
        $this->error('Unknown processing instruction %' . $pi['pi'] .
            ', should be one of "' . implode(', ', array_keys($expected)) . '"');
    }
    $this->_retvalue = array('patterns' => $this->yystack[$this->yyidx + 0]->minor, 'pis' => $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1322 "LexerGenerator\Parser.php"
#line 456 "LexerGenerator\Parser.y"
    function yy_r7(){
    $this->_retvalue = array(array('pi' => $this->yystack[$this->yyidx + -1]->minor, 'definition' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1327 "LexerGenerator\Parser.php"
#line 462 "LexerGenerator\Parser.y"
    function yy_r9(){
    $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue[] = array('pi' => $this->yystack[$this->yyidx + -1]->minor, 'definition' => $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1333 "LexerGenerator\Parser.php"
#line 471 "LexerGenerator\Parser.y"
    function yy_r11(){
    $this->_retvalue = array($this->yystack[$this->yyidx + -1]->minor => $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1338 "LexerGenerator\Parser.php"
#line 474 "LexerGenerator\Parser.y"
    function yy_r12(){
    $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
    if (isset($this->_retvalue[$this->yystack[$this->yyidx + -1]->minor])) {
        throw new Exception('Pattern "' . $this->yystack[$this->yyidx + -1]->minor . '" is already defined as "' .
            $this->_retvalue[$this->yystack[$this->yyidx + -1]->minor] . '", cannot redefine as "' . $this->yystack[$this->yyidx + 0]->minor->string . '"');
    }
    $this->_retvalue[$this->yystack[$this->yyidx + -1]->minor] = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1348 "LexerGenerator\Parser.php"
#line 483 "LexerGenerator\Parser.y"
    function yy_r13(){
    $this->_retvalue = array(array('rules' => $this->yystack[$this->yyidx + -1]->minor, 'code' => '', 'statename' => ''));
    }
#line 1353 "LexerGenerator\Parser.php"
#line 486 "LexerGenerator\Parser.y"
    function yy_r14(){
    if ($this->yystack[$this->yyidx + -3]->minor != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    $this->_retvalue = array(array('rules' => $this->yystack[$this->yyidx + -1]->minor, 'code' => '', 'statename' => $this->yystack[$this->yyidx + -2]->minor));
    }
#line 1362 "LexerGenerator\Parser.php"
#line 493 "LexerGenerator\Parser.y"
    function yy_r15(){
    $this->_retvalue = array(array('rules' => $this->yystack[$this->yyidx + -2]->minor, 'code' => $this->yystack[$this->yyidx + 0]->minor, 'statename' => ''));
    }
#line 1367 "LexerGenerator\Parser.php"
#line 496 "LexerGenerator\Parser.y"
    function yy_r16(){
    if ($this->yystack[$this->yyidx + -4]->minor != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    $this->_retvalue = array(array('rules' => $this->yystack[$this->yyidx + -2]->minor, 'code' => $this->yystack[$this->yyidx + 0]->minor, 'statename' => $this->yystack[$this->yyidx + -3]->minor));
    }
#line 1376 "LexerGenerator\Parser.php"
#line 503 "LexerGenerator\Parser.y"
    function yy_r17(){
    $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor;
    $this->_retvalue[] = array('rules' => $this->yystack[$this->yyidx + -1]->minor, 'code' => '', 'statename' => '');
    }
#line 1382 "LexerGenerator\Parser.php"
#line 507 "LexerGenerator\Parser.y"
    function yy_r18(){
    if ($this->yystack[$this->yyidx + -4]->minor != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    $this->_retvalue = $this->yystack[$this->yyidx + -5]->minor;
    $this->_retvalue[] = array('rules' => $this->yystack[$this->yyidx + -1]->minor, 'code' => '', 'statename' => $this->yystack[$this->yyidx + -3]->minor);
    }
#line 1392 "LexerGenerator\Parser.php"
#line 515 "LexerGenerator\Parser.y"
    function yy_r19(){
    $this->_retvalue = $this->yystack[$this->yyidx + -4]->minor;
    $this->_retvalue[] = array('rules' => $this->yystack[$this->yyidx + -2]->minor, 'code' => $this->yystack[$this->yyidx + 0]->minor, 'statename' => '');
    }
#line 1398 "LexerGenerator\Parser.php"
#line 519 "LexerGenerator\Parser.y"
    function yy_r20(){
    if ($this->yystack[$this->yyidx + -4]->minor != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    $this->_retvalue = $this->yystack[$this->yyidx + -6]->minor;
    $this->_retvalue[] = array('rules' => $this->yystack[$this->yyidx + -2]->minor, 'code' => $this->yystack[$this->yyidx + 0]->minor, 'statename' => $this->yystack[$this->yyidx + -3]->minor);
    }
#line 1408 "LexerGenerator\Parser.php"
#line 528 "LexerGenerator\Parser.y"
    function yy_r21(){
    if (@preg_match('/' . $this->yystack[$this->yyidx + -1]->minor[0] . '/', '')) {
        $this->error('Rule "' . $this->yystack[$this->yyidx + -1]->minor[2] . '" can match the empty string, this will break lexing');
    }
    $this->_retvalue = array(array('pattern' => $this->yystack[$this->yyidx + -1]->minor[1], 'code' => $this->yystack[$this->yyidx + 0]->minor, 'subpatterns' => $this->yystack[$this->yyidx + -1]->minor[3]));
    }
#line 1416 "LexerGenerator\Parser.php"
#line 534 "LexerGenerator\Parser.y"
    function yy_r22(){
    $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
    if (@preg_match('/' . $this->yystack[$this->yyidx + -1]->minor[0] . '/', '')) {
        $this->error('Rule "' . $this->yystack[$this->yyidx + -1]->minor[2] . '" can match the empty string, this will break lexing');
    }
    $this->_retvalue[] = array('pattern' => $this->yystack[$this->yyidx + -1]->minor[1], 'code' => $this->yystack[$this->yyidx + 0]->minor, 'subpatterns' => $this->yystack[$this->yyidx + -1]->minor[3]);
    }
#line 1425 "LexerGenerator\Parser.php"
#line 542 "LexerGenerator\Parser.y"
    function yy_r23(){
    $this->_retvalue = array(
        preg_quote($this->yystack[$this->yyidx + 0]->minor, '/'),
        str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote($this->yystack[$this->yyidx + 0]->minor, '/')),
        '"' . str_replace('"', '\"', $this->yystack[$this->yyidx + 0]->minor) . '"', 0);
    }
#line 1433 "LexerGenerator\Parser.php"
#line 548 "LexerGenerator\Parser.y"
    function yy_r24(){
    if (!isset($this->patterns[$this->yystack[$this->yyidx + 0]->minor])) {
        $this->error('Undefined pattern "' . $this->yystack[$this->yyidx + 0]->minor . '" used in rules');
        throw new Exception('Undefined pattern "' . $this->yystack[$this->yyidx + 0]->minor . '" used in rules');
    }
    $this->_retvalue = array($this->patterns[$this->yystack[$this->yyidx + 0]->minor]['pattern'], $this->patterns[$this->yystack[$this->yyidx + 0]->minor]->string, $this->yystack[$this->yyidx + 0]->minor, $this->patterns[$this->yystack[$this->yyidx + 0]->minor]['subpatterns']);
    }
#line 1442 "LexerGenerator\Parser.php"
#line 555 "LexerGenerator\Parser.y"
    function yy_r25(){
    $this->_retvalue = array(
        $this->yystack[$this->yyidx + -1]->minor[0] . preg_quote($this->yystack[$this->yyidx + 0]->minor, '/'),
        $this->yystack[$this->yyidx + -1]->minor[1] . str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote($this->yystack[$this->yyidx + 0]->minor, '/')),
        $this->yystack[$this->yyidx + -1]->minor[2] . ' "' . str_replace('"', '\"', $this->yystack[$this->yyidx + 0]->minor) . '"', $this->yystack[$this->yyidx + -1]->minor[3]);
    }
#line 1450 "LexerGenerator\Parser.php"
#line 561 "LexerGenerator\Parser.y"
    function yy_r26(){
    if (!isset($this->patterns[$this->yystack[$this->yyidx + 0]->minor])) {
        $this->error('Undefined pattern "' . $this->yystack[$this->yyidx + 0]->minor . '" used in rules');
        throw new Exception('Undefined pattern "' . $this->yystack[$this->yyidx + 0]->minor . '" used in rules');
    }
    $this->_retvalue = array($this->yystack[$this->yyidx + -1]->minor[0] . $this->patterns[$this->yystack[$this->yyidx + 0]->minor]['pattern'], $this->yystack[$this->yyidx + -1]->minor[1] . $this->patterns[$this->yystack[$this->yyidx + 0]->minor]->string,
        $this->yystack[$this->yyidx + -1]->minor[2] . ' ' . $this->yystack[$this->yyidx + 0]->minor, $this->yystack[$this->yyidx + -1]->minor[3] + $this->patterns[$this->yystack[$this->yyidx + 0]->minor]['subpatterns']);
    }
#line 1460 "LexerGenerator\Parser.php"
#line 570 "LexerGenerator\Parser.y"
    function yy_r27(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken(str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote($this->yystack[$this->yyidx + 0]->minor, '/')), array(
        'pattern' => preg_quote($this->yystack[$this->yyidx + 0]->minor, '/'), 'subpatterns' => 0));
    }
#line 1466 "LexerGenerator\Parser.php"
#line 574 "LexerGenerator\Parser.y"
    function yy_r28(){
    $this->_retvalue = $this->_validatePattern($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1471 "LexerGenerator\Parser.php"
#line 577 "LexerGenerator\Parser.y"
    function yy_r29(){
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote($this->yystack[$this->yyidx + 0]->minor, '/')), array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . preg_quote($this->yystack[$this->yyidx + 0]->minor, '/'), 'subpatterns' => $this->yystack[$this->yyidx + -1]->minor['subpatterns']));
    }
#line 1477 "LexerGenerator\Parser.php"
#line 581 "LexerGenerator\Parser.y"
    function yy_r30(){
    $x = $this->_validatePattern($this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . $x->string, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $x['pattern'],
        'subpatterns' => $x['subpatterns'] + $this->yystack[$this->yyidx + -1]->minor['subpatterns']));
    }
#line 1485 "LexerGenerator\Parser.php"

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
        //PHP_LexerGenerator_ParseryyStackEntry $yymsp;            /* The top of the parser's stack */
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
                $x = new PHP_LexerGenerator_ParseryyStackEntry;
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
#line 70 "LexerGenerator\Parser.y"

    echo "Syntax Error on line " . $this->lex->line . ": token '" . 
        $this->lex->value . "' while parsing rule:";
    foreach ($this->yystack as $entry) {
        echo $this->tokenName($entry->major) . ' ';
    }
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
#line 1610 "LexerGenerator\Parser.php"
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
            $x = new PHP_LexerGenerator_ParseryyStackEntry;
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