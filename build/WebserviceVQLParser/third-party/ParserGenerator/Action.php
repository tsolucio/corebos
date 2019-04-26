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
 * Every shift or reduce operation is stored as one of the following objects.
 * 
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.1.0
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_Action {
    const SHIFT = 1,
    ACCEPT = 2,
    REDUCE = 3,
    ERROR = 4,
    /**
           * Was a reduce, but part of a conflict
           */
    CONFLICT = 5,
    /**
           * Was a shift.  Precedence resolved conflict
           */
    SH_RESOLVED = 6,
    /**
           * Was a reduce.  Precedence resolved conflict
           */
    RD_RESOLVED = 7,
    /**
           * Deleted by compression
           * @see PHP_ParserGenerator::CompressTables()
           */
    NOT_USED = 8;
    /**
     * The look-ahead symbol that triggers this action
     * @var PHP_ParserGenerator_Symbol
     */
    public $sp;       /* The look-ahead symbol */
    /**
     * This defines the kind of action, and must be one
     * of the class constants.
     *
     * - {@link PHP_ParserGenerator_Action::SHIFT}
     * - {@link PHP_ParserGenerator_Action::ACCEPT}
     * - {@link PHP_ParserGenerator_Action::REDUCE}
     * - {@link PHP_ParserGenerator_Action::ERROR}
     * - {@link PHP_ParserGenerator_Action::CONFLICT}
     * - {@link PHP_ParserGenerator_Action::SH_RESOLVED}
     * - {@link PHP_ParserGenerator_Action:: RD_RESOLVED}
     * - {@link PHP_ParserGenerator_Action::NOT_USED}
     */
    public $type;
    /**
     * The new state, if this is a shift,
     * the parser rule index, if this is a reduce.
     *
     * @var PHP_ParserGenerator_State|PHP_ParserGenerator_Rule
     */
    public $x;
    /**
     * The next action for this state.
     * @var PHP_ParserGenerator_Action
     */
    public $next;

    /**
     * Compare two actions
     * 
     * This is used by {@link Action_sort()} to compare actions
     */
    static function actioncmp(PHP_ParserGenerator_Action $ap1,
                              PHP_ParserGenerator_Action $ap2)
    {
        $rc = $ap1->sp->index - $ap2->sp->index;
        if ($rc === 0) {
            $rc = $ap1->type - $ap2->type;
        }
        if ($rc === 0) {
            if ($ap1->type != self::REDUCE &&
            $ap1->type != self::RD_RESOLVED &&
            $ap1->type != self::CONFLICT) {
                throw new Exception('action has not been processed: ' .
                $ap1->sp->name);
            }
            if ($ap2->type != self::REDUCE &&
            $ap2->type != self::RD_RESOLVED &&
            $ap2->type != self::CONFLICT) {
                throw new Exception('action has not been processed: ' .
                $ap2->sp->name);
            }
            $rc = $ap1->x->index - $ap2->x->index;
        }
        return $rc;
    }

    /**
     * create linked list of PHP_ParserGenerator_Actions
     *
     * @param PHP_ParserGenerator_Action|null
     * @param int one of the class constants from PHP_ParserGenerator_Action
     * @param PHP_ParserGenerator_Symbol
     * @param PHP_ParserGenerator_Symbol|PHP_ParserGenerator_Rule
     */
    static function Action_add(&$app, $type, PHP_ParserGenerator_Symbol $sp, $arg)
    {
        $new = new PHP_ParserGenerator_Action;
        $new->next = $app;
        $app = $new;
        $new->type = $type;
        $new->sp = $sp;
        $new->x = $arg;
    }

    /**
     * Sort parser actions
     * @see PHP_ParserGenerator_Data::FindActions()
     */
    static function Action_sort(PHP_ParserGenerator_Action $ap)
    {
        $ap = PHP_ParserGenerator::msort($ap, 'next', array('PHP_ParserGenerator_Action', 'actioncmp'));
        return $ap;
    }

    /**
     * Print an action to the given file descriptor.  Return FALSE if
     * nothing was actually printed.
     * @see PHP_ParserGenerator_Data::ReportOutput()
     */
    function PrintAction($fp, $indent)
    {
        $result = 1;
        switch ($this->type)
        {
            case self::SHIFT:
                fprintf($fp, "%${indent}s shift  %d", $this->sp->name, $this->x->statenum);
                break;
            case self::REDUCE:
                fprintf($fp, "%${indent}s reduce %d", $this->sp->name, $this->x->index);
                break;
            case self::ACCEPT:
                fprintf($fp, "%${indent}s accept", $this->sp->name);
                break;
            case self::ERROR:
                fprintf($fp, "%${indent}s error", $this->sp->name);
                break;
            case self::CONFLICT:
                fprintf($fp, "%${indent}s reduce %-3d ** Parsing conflict **", $this->sp->name, $this->x->index);
                break;
            case self::SH_RESOLVED:
            case self::RD_RESOLVED:
            case self::NOT_USED:
                $result = 0;
                break;
        }
        return $result;
    }
}
?>