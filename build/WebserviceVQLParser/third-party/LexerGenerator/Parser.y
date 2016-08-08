%name PHP_LexerGenerator_Parser
%declare_class {class PHP_LexerGenerator_Parser}
%include {
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
}
%syntax_error {
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
}
%include_class {
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
}

start ::= lexfile.

lexfile ::= declare rules(B). {
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
    foreach (B as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
}
lexfile ::= declare(D) PHPCODE(B) rules(C). {
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
    if (strlen(B)) {
        fwrite($this->out, B);
    }
    foreach (C as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
}
lexfile ::= PHPCODE(B) declare(D) rules(C). {
    if (strlen(B)) {
        fwrite($this->out, B);
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
    foreach (C as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
}
lexfile ::= PHPCODE(A) declare(D) PHPCODE(B) rules(C). {
    if (strlen(A)) {
        fwrite($this->out, A);
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
    if (strlen(B)) {
        fwrite($this->out, B);
    }
    foreach (C as $rule) {
        $this->outputRules($rule['rules'], $rule['statename']);
        if ($rule['code']) {
            fwrite($this->out, $rule['code']);
        }
    }
}

declare(A) ::= COMMENTSTART declarations(B) COMMENTEND. {
    A = B;
    $this->patterns = B['patterns'];
}

declarations(A) ::= processing_instructions(B) pattern_declarations(C). {
    $expected = array(
        'counter' => true,
        'input' => true,
        'token' => true,
        'value' => true,
        'line' => true,
    );
    foreach (B as $pi) {
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
    foreach (B as $pi) {
        if (isset($expected[$pi['pi']])) {
            $this->{$pi['pi']} = $pi['definition'];
            continue;
        }
        $this->error('Unknown processing instruction %' . $pi['pi'] .
            ', should be one of "' . implode(', ', array_keys($expected)) . '"');
    }
    A = array('patterns' => C, 'pis' => B);
}

processing_instructions(A) ::= PI(B) SUBPATTERN(C). {
    A = array(array('pi' => B, 'definition' => C));
}
processing_instructions(A) ::= PI(B) CODE(C). {
    A = array(array('pi' => B, 'definition' => C));
}
processing_instructions(A) ::= processing_instructions(P) PI(B) SUBPATTERN(C). {
    A = P;
    A[] = array('pi' => B, 'definition' => C);
}
processing_instructions(A) ::= processing_instructions(P) PI(B) CODE(C). {
    A = P;
    A[] = array('pi' => B, 'definition' => C);
}

pattern_declarations(A) ::= PATTERN(B) subpattern(C). {
    A = array(B => C);
}
pattern_declarations(A) ::= pattern_declarations(B) PATTERN(C) subpattern(D). {
    A = B;
    if (isset(A[C])) {
        throw new Exception('Pattern "' . C . '" is already defined as "' .
            A[C] . '", cannot redefine as "' . D->string . '"');
    }
    A[C] = D;
}

rules(A) ::= COMMENTSTART rule(B) COMMENTEND. {
    A = array(array('rules' => B, 'code' => '', 'statename' => ''));
}
rules(A) ::= COMMENTSTART PI(P) SUBPATTERN(S) rule(B) COMMENTEND. {
    if (P != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    A = array(array('rules' => B, 'code' => '', 'statename' => S));
}
rules(A) ::= COMMENTSTART rule(B) COMMENTEND PHPCODE(C). {
    A = array(array('rules' => B, 'code' => C, 'statename' => ''));
}
rules(A) ::= COMMENTSTART PI(P) SUBPATTERN(S) rule(B) COMMENTEND PHPCODE(C). {
    if (P != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    A = array(array('rules' => B, 'code' => C, 'statename' => S));
}
rules(A) ::= rules(R) COMMENTSTART rule(B) COMMENTEND. {
    A = R;
    A[] = array('rules' => B, 'code' => '', 'statename' => '');
}
rules(A) ::= rules(R) PI(P) SUBPATTERN(S) COMMENTSTART rule(B) COMMENTEND. {
    if (P != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    A = R;
    A[] = array('rules' => B, 'code' => '', 'statename' => S);
}
rules(A) ::= rules(R) COMMENTSTART rule(B) COMMENTEND PHPCODE(C). {
    A = R;
    A[] = array('rules' => B, 'code' => C, 'statename' => '');
}
rules(A) ::= rules(R) COMMENTSTART PI(P) SUBPATTERN(S) rule(B) COMMENTEND PHPCODE(C). {
    if (P != 'statename') {
        throw new Exception('Error: only %statename processing instruction ' .
            'is allowed in rule sections');
    }
    A = R;
    A[] = array('rules' => B, 'code' => C, 'statename' => S);
}

rule(A) ::= rule_subpattern(B) CODE(C). {
    if (@preg_match('/' . B[0] . '/', '')) {
        $this->error('Rule "' . B[2] . '" can match the empty string, this will break lexing');
    }
    A = array(array('pattern' => B[1], 'code' => C, 'subpatterns' => B[3]));
}
rule(A) ::= rule(R) rule_subpattern(B) CODE(C).{
    A = R;
    if (@preg_match('/' . B[0] . '/', '')) {
        $this->error('Rule "' . B[2] . '" can match the empty string, this will break lexing');
    }
    A[] = array('pattern' => B[1], 'code' => C, 'subpatterns' => B[3]);
}

rule_subpattern(A) ::= QUOTE(B). {
    A = array(
        preg_quote(B, '/'),
        str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote(B, '/')),
        '"' . str_replace('"', '\"', B) . '"', 0);
}
rule_subpattern(A) ::= SUBPATTERN(B). {
    if (!isset($this->patterns[B])) {
        $this->error('Undefined pattern "' . B . '" used in rules');
        throw new Exception('Undefined pattern "' . B . '" used in rules');
    }
    A = array($this->patterns[B]['pattern'], $this->patterns[B]->string, B, $this->patterns[B]['subpatterns']);
}
rule_subpattern(A) ::= rule_subpattern(B) QUOTE(C). {
    A = array(
        B[0] . preg_quote(C, '/'),
        B[1] . str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote(C, '/')),
        B[2] . ' "' . str_replace('"', '\"', C) . '"', B[3]);
}
rule_subpattern(A) ::= rule_subpattern(B) SUBPATTERN(C). {
    if (!isset($this->patterns[C])) {
        $this->error('Undefined pattern "' . C . '" used in rules');
        throw new Exception('Undefined pattern "' . C . '" used in rules');
    }
    A = array(B[0] . $this->patterns[C]['pattern'], B[1] . $this->patterns[C]->string,
        B[2] . ' ' . C, B[3] + $this->patterns[C]['subpatterns']);
}

subpattern(A) ::= QUOTE(B). {
    A = new PHP_LexerGenerator_ParseryyToken(str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote(B, '/')), array(
        'pattern' => preg_quote(B, '/'), 'subpatterns' => 0));
}
subpattern(A) ::= SUBPATTERN(B). {
    A = $this->_validatePattern(B);
}
subpattern(A) ::= subpattern(B) QUOTE(C). {
    A = new PHP_LexerGenerator_ParseryyToken(B->string . str_replace(array('\\', '"'), array('\\\\', '\\"'), preg_quote(C, '/')), array(
        'pattern' => B['pattern'] . preg_quote(C, '/'), 'subpatterns' => B['subpatterns']));
}
subpattern(A) ::= subpattern(B) SUBPATTERN(C). {
    $x = $this->_validatePattern(C);
    A = new PHP_LexerGenerator_ParseryyToken(B->string . $x->string, array(
        'pattern' => B['pattern'] . $x['pattern'],
        'subpatterns' => $x['subpatterns'] + B['subpatterns']));
}