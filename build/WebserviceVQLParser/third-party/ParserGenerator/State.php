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
 * The structure used to represent a state in the associative array
 * for a PHP_ParserGenerator_Config.
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.1.0
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_StateNode
{
    public $key;
    public $data;
    public $from = 0;
    public $next = 0;
}

/**
 * Each state of the generated parser's finite state machine
 * is encoded as an instance of this class
 * 
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.1.0
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_State {
    /**
     * The basis configurations for this state
     * @var PHP_ParserGenerator_Config
     */
    public $bp;
    /**
     * All configurations in this state
     * @var PHP_ParserGenerator_Config
     */
    public $cfp;
    /**
     * Sequential number for this state
     *
     * @var int
     */
    public $statenum;
    /**
     * Linked list of actions for this state.
     * @var PHP_ParserGenerator_Action
     */
    public $ap;
    /**
     * Number of terminal (token) actions
     *
     * @var int
     */
    public $nTknAct,
    /**
     * Number of non-terminal actions
     *
     * @var int
     */
    $nNtAct;
    /**
     * The offset into the $yy_action table for terminal tokens.
     *
     * @var int
     */
    public $iTknOfst,
    /**
     * The offset into the $yy_action table for non-terminals.
     *
     * @var int
     */
    $iNtOfst;
    /**
     * Default action
     *
     * @var int
     */
    public $iDflt;
    /**
     * Associative array of PHP_ParserGenerator_State objects
     *
     * @var array
     */
    public static $x3a = array();
    /**
     * Array of PHP_ParserGenerator_State objects
     *
     * @var array
     */
    public static $states = array();

    /**
     * Compare two states for sorting purposes.  The smaller state is the
     * one with the most non-terminal actions.  If they have the same number
     * of non-terminal actions, then the smaller is the one with the most
     * token actions.
     */
    static function stateResortCompare($a, $b)
    {
        $n = $b->nNtAct - $a->nNtAct;
        if ($n === 0) {
            $n = $b->nTknAct - $a->nTknAct;
        }
        return $n;
    }

    /**
     * Compare two states based on their configurations
     *
     * @param PHP_ParserGenerator_Config|0 $a
     * @param PHP_ParserGenerator_Config|0 $b
     * @return int
     */
    static function statecmp($a, $b)
    {
        for ($rc = 0; $rc == 0 && $a && $b;  $a = $a->bp, $b = $b->bp) {
            $rc = $a->rp->index - $b->rp->index;
            if ($rc === 0) {
                $rc = $a->dot - $b->dot;
            }
        }
        if ($rc == 0) {
            if ($a) {
                $rc = 1;
            }
            if ($b) {
                $rc = -1;
            }
        }
        return $rc;
    }

    /**
     * Hash a state based on its configuration
     * @return int
     */
    private static function statehash(PHP_ParserGenerator_Config $a)
    {
        $h = 0;
        while ($a) {
            $h = $h * 571 + $a->rp->index * 37 + $a->dot;
            $a = $a->bp;
        }
        return (int) $h;
    }

    /**
     * Return a pointer to data assigned to the given key.  Return NULL
     * if no such key.
     * @param PHP_ParserGenerator_Config
     * @return null|PHP_ParserGenerator_State
     */
    static function State_find(PHP_ParserGenerator_Config $key)
    {
        if (!count(self::$x3a)) {
            return 0;
        }
        $h = self::statehash($key);
        if (!isset(self::$x3a[$h])) {
            return 0;
        }
        $np = self::$x3a[$h];
        while ($np) {
            if (self::statecmp($np->key, $key) == 0) {
                break;
            }
            $np = $np->next;
        }
        return $np ? $np->data : 0;
    }

    /**
     * Insert a new record into the array.  Return TRUE if successful.
     * Prior data with the same key is NOT overwritten
     *
     * @param PHP_ParserGenerator_State $state
     * @param PHP_ParserGenerator_Config $key
     * @return unknown
     */
    static function State_insert(PHP_ParserGenerator_State $state,
                                 PHP_ParserGenerator_Config $key)
    {
        $h = self::statehash($key);
        if (isset(self::$x3a[$h])) {
            $np = self::$x3a[$h];
        } else {
            $np = 0;
        }
        while ($np) {
            if (self::statecmp($np->key, $key) == 0) {
                /* An existing entry with the same key is found. */
                /* Fail because overwrite is not allows. */
                return 0;
            }
            $np = $np->next;
        }
        /* Insert the new data */
        $np = new PHP_ParserGenerator_StateNode;
        $np->key = $key;
        $np->data = $state;
        self::$states[] = $np;
        // the original lemon code sets the from link always to itself
        // setting up a faulty double-linked list
        // however, the from links are never used, so I suspect a copy/paste
        // error from a standard algorithm that was never caught
        if (isset(self::$x3a[$h])) {
            self::$x3a[$h]->from = $np; // lemon has $np->next here
        } else {
            self::$x3a[$h] = 0; // dummy to avoid notice
        }
        $np->next = self::$x3a[$h];
        self::$x3a[$h] = $np;
        $np->from = self::$x3a[$h];
        return 1;
    }

    /**
     * Get an array indexed by state number
     *
     * @return array
     */
    static function State_arrayof()
    {
        return self::$states;
    }
}
