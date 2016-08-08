<?php
/**
 * PHP_ParserGenerator, a php 5 parser generator.
 * 
 * This is a direct port of the Lemon parser generator, found at
 * {@link http://www.hwaci.com/sw/lemon/}
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   php
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @since      File available since Release 0.1.0
 */
/**
 * Each production rule in the grammar is stored in this class
 * 
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.1.0
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_Rule {
    /**
     * Left-hand side of the rule
     * @var array an array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $lhs;
    /**
     * Alias for the LHS (NULL if none)
     *
     * @var array
     */
    public $lhsalias = array();
    /**
     * Line number for the rule
     * @var int
     */
    public $ruleline;
    /**
     * Number of right-hand side symbols
     */
    public $nrhs;
    /**
     * The right-hand side symbols
     * @var array an array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $rhs;
    /**
     * Aliases for each right-hand side symbol, or null if no alis.
     * 
     * In this rule:
     * <pre>
     * foo ::= BAR(A) baz(B).
     * </pre>
     * 
     * The right-hand side aliases are A for BAR, and B for baz.
     * @var array aliases are indexed by the right-hand side symbol index.
     */
    public $rhsalias = array();
    /**
     * Line number at which code begins
     * @var int
     */
    public $line;
    /**
     * The code executed when this rule is reduced
     * 
     * <pre>
     * foo(R) ::= BAR(A) baz(B). {R = A + B;}
     * </pre>
     * 
     * In the rule above, the code is "R = A + B;"
     * @var string|0
     */
    public $code;
    /**
     * Precedence symbol for this rule
     * @var PHP_ParserGenerator_Symbol
     */
    public $precsym;
    /**
     * An index number for this rule
     * 
     * Used in both naming of reduce functions and determining which rule code
     * to use for reduce actions
     * @var int
     */
    public $index;
    /**
     * True if this rule is ever reduced
     * @var boolean
     */
    public $canReduce;
    /**
     * Next rule with the same left-hand side
     * @var PHP_ParserGenerator_Rule|0
     */
    public $nextlhs;
    /**
     * Next rule in the global list
     * @var PHP_ParserGenerator_Rule|0
     */
    public $next;
}
