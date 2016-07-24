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
 * A followset propagation link indicates that the contents of one
 * configuration followset should be propagated to another whenever
 * the first changes.
 * 
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.1.0
 * @since      Class available since Release 0.1.0
 */

class PHP_ParserGenerator_PropagationLink {
    /**
     * The configuration that defines this propagation link
     * @var PHP_ParserGenerator_Config
     */
    public $cfp;
    /**
     * The next propagation link
     * @var PHP_ParserGenerator_PropagationLink|0
     */
    public $next = 0;

    /**
     * Add a propagation link to the current list
     * 
     * This prepends the configuration passed in to the first parameter
     * which is either 0 or a PHP_ParserGenerator_PropagationLink defining
     * an existing list.
     * @param PHP_ParserGenerator_PropagationLink|null
     * @param PHP_ParserGenerator_Config
     */
    static function Plink_add(&$plpp, PHP_ParserGenerator_Config $cfp)
    {
        $new = new PHP_ParserGenerator_PropagationLink;
        $new->next = $plpp;
        $plpp = $new;
        $new->cfp = $cfp;
    }

    /**
     * Transfer every propagation link on the list "from" to the list "to"
     */
    static function Plink_copy(PHP_ParserGenerator_PropagationLink &$to,
                               PHP_ParserGenerator_PropagationLink $from)
    {
        while ($from) {
            $nextpl = $from->next;
            $from->next = $to;
            $to = $from;
            $from = $nextpl;
        }
    }

    /**
     * Delete every propagation link on the list
     * @param PHP_ParserGenerator_PropagationLink|0
     */
    static function Plink_delete($plp)
    {
        while ($plp) {
            $nextpl = $plp->next;
            $plp->next = 0;
            $plp = $nextpl;
        }
    }
}

