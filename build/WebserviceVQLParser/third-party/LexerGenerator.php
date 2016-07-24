<?php
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
 * The Lexer generation parser
 */
require_once 'PHP/LexerGenerator/Parser.php';
/**
 * Hand-written lexer for lex2php format files
 */
require_once 'PHP/LexerGenerator/Lexer.php';
/**
 * The basic home class for the lexer generator.  A lexer scans text and
 * organizes it into tokens for usage by a parser.
 * 
 * Sample Usage:
 * <code>
 * require_once 'PHP/LexerGenerator.php';
 * $lex = new PHP_LexerGenerator('/path/to/lexerfile.plex');
 * </code>
 * 
 * A file named "/path/to/lexerfile.php" will be created.
 * 
 * File format consists of a PHP file containing specially
 * formatted comments like so:
 * 
 * <code>
 * /*!lex2php
 * {@*}
 * </code>
 * 
 * The first lex2php comment must contain several declarations and define
 * all regular expressions.  Declarations (processor instructions) start with
 * a "%" symbol and must be:
 * 
 *  - %counter
 *  - %input
 *  - %token
 *  - %value
 *  - %line
 * 
 * token and counter should define the class variables used to define lexer input
 * and the index into the input.  token and value should be used to define the class
 * variables used to store the token number and its textual value.  Finally, line
 * should be used to define the class variable used to define the current line number
 * of scanning.
 * 
 * For example:
 * <code>
 * /*!lex2php
 * %counter {$this->N}
 * %input {$this->data}
 * %token {$this->token}
 * %value {$this->value}
 * %line {%this->linenumber}
 * {@*}
 * </code>
 * 
 * Patterns consist of an identifier containing upper or lower-cased letters, and
 * a descriptive match pattern.
 * 
 * Descriptive match patterns may either be regular expressions (regexes) or
 * quoted literal strings.  Here are some examples:
 * 
 * <pre>
 * pattern = "quoted literal"
 * ANOTHER = /[a-zA-Z_]+/
 * </pre>
 * 
 * Quoted strings must escape the \ and " characters with \" and \\.
 * 
 * Regex patterns must be in Perl-compatible regular expression format (preg).
 * special characters (like \t \n or \x3H) can only be used in regexes, all
 * \ will be escaped in literal strings.
 * 
 * Any sub-patterns must be defined using (?:) instead of ():
 *
 * <code>
 * /*!lex2php
 * %counter {$this->N}
 * %input {$this->data}
 * %token {$this->token}
 * %value {$this->value}
 * %line {%this->linenumber}
 * alpha = /[a-zA-Z]/
 * alphaplus = /[a-zA-Z]+/
 * number = /[0-9]/
 * numerals = /[0-9]+/
 * whitespace = /[ \t\n]+/
 * blah = "$\""
 * blahblah = /a\$/
 * GAMEEND = @(?:1\-0|0\-1|1/2\-1/2)@
 * PAWNMOVE = /P?[a-h](?:[2-7]|[18]\=(?:Q|R|B|N))|P?[a-h]x[a-h](?:[2-7]|[18]\=(?:Q|R|B|N))/
 * {@*}
 * </code>
 * 
 * All regexes must be delimited.  Any legal preg delimiter can be used (as in @ or / in
 * the example above)
 * 
 * 
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.2.0
 * @since      Class available since Release 0.1.0
 * @example    TestLexer.plex Example lexer source
 * @example    TestLexer.php  Example lexer php code
 * @example    usage.php      Example usage of PHP_LexerGenerator
 * @example    Lexer.plex     File_ChessPGN lexer source (complex)
 * @example    Lexer.php      File_ChessPGN lexer php code
 */

class PHP_LexerGenerator
{
    private $lex;
    private $parser;
    private $outfile;
    /**
     * Create a lexer file from its skeleton plex file.
     *
     * @param string $lexerfile path to the plex file
     */
    function __construct($lexerfile)
    {
        $this->lex = new PHP_LexerGenerator_Lexer(file_get_contents($lexerfile));
        $info = pathinfo($lexerfile);
        $this->outfile = $info['dirname'] . DIRECTORY_SEPARATOR .
            substr($info['basename'], 0,
            strlen($info['basename']) - strlen($info['extension'])) . 'php';
        $this->parser = new PHP_LexerGenerator_Parser($this->outfile, $this->lex);
        $this->parser->PrintTrace();
        while ($this->lex->advance($this->parser)) {
            $this->parser->doParse($this->lex->token, $this->lex->value);
        }
        $this->parser->doParse(0, 0);
    }
}
//$a = new PHP_LexerGenerator('/development/File_ChessPGN/ChessPGN/Lexer.plex');
?>