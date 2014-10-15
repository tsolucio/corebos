<?php
//
// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
// $Id: memory.php,v 1.15 2005/12/31 17:25:26 bigmichi1 Exp $

//
// xml_memory()
//
function xml_memory () {
    global $sysinfo;
    $mem = $sysinfo->memory();

    $_text = "  <Memory>\n"
           . "    <Free>" . htmlspecialchars($mem['ram']['t_free'], ENT_QUOTES) . "</Free>\n"
           . "    <Used>" . htmlspecialchars($mem['ram']['t_used'], ENT_QUOTES) . "</Used>\n"
           . "    <Total>" . htmlspecialchars($mem['ram']['total'], ENT_QUOTES) . "</Total>\n"
           . "    <Percent>" . htmlspecialchars($mem['ram']['percent'], ENT_QUOTES) . "</Percent>\n";
	   
    if (isset($mem['ram']['app_percent']))
      $_text .= "    <App>" . htmlspecialchars($mem['ram']['app'], ENT_QUOTES) . "</App>\n    <AppPercent>" . htmlspecialchars($mem['ram']['app_percent'], ENT_QUOTES) . "</AppPercent>\n";
    if (isset($mem['ram']['buffers_percent']))
      $_text .= "    <Buffers>" . htmlspecialchars($mem['ram']['buffers'], ENT_QUOTES) . "</Buffers>\n    <BuffersPercent>" . htmlspecialchars($mem['ram']['buffers_percent'], ENT_QUOTES) . "</BuffersPercent>\n";
    if (isset($mem['ram']['cached_percent']))
      $_text .= "    <Cached>" . htmlspecialchars($mem['ram']['cached'], ENT_QUOTES) . "</Cached>\n    <CachedPercent>" . htmlspecialchars($mem['ram']['cached_percent'], ENT_QUOTES) . "</CachedPercent>\n";
      
    $_text .= "  </Memory>\n"
           . "  <Swap>\n"
           . "    <Free>" . htmlspecialchars($mem['swap']['free'], ENT_QUOTES) . "</Free>\n"
           . "    <Used>" . htmlspecialchars($mem['swap']['used'], ENT_QUOTES) . "</Used>\n"
           . "    <Total>" . htmlspecialchars($mem['swap']['total'], ENT_QUOTES) . "</Total>\n"
           . "    <Percent>" . htmlspecialchars($mem['swap']['percent'], ENT_QUOTES) . "</Percent>\n"
           . "  </Swap>\n"
	   . "  <Swapdevices>\n";
    $i = 0;
    foreach ($mem['devswap'] as $device) {
	$_text .="    <Mount>\n"
	       . "     <MountPointID>" . htmlspecialchars($i++, ENT_QUOTES) . "</MountPointID>\n"
	       . "     <Type>Swap</Type>"
	       . "     <Device><Name>" . htmlspecialchars($device['dev'], ENT_QUOTES) . "</Name></Device>\n"
    	       . "     <Percent>" . htmlspecialchars($device['percent'], ENT_QUOTES) . "</Percent>\n"
    	       . "     <Free>" . htmlspecialchars($device['free'], ENT_QUOTES) . "</Free>\n"
    	       . "     <Used>" . htmlspecialchars($device['used'], ENT_QUOTES) . "</Used>\n"
    	       . "     <Size>" . htmlspecialchars($device['total'], ENT_QUOTES) . "</Size>\n"
    	       . "    </Mount>\n";
    }
    $_text .= "  </Swapdevices>\n";

    return $_text;
}

//
// xml_memory()
//
function html_memory () {
    global $XPath;
    global $text;

    $textdir = direction();
    $scale_factor = 2;

    $ram = create_bargraph($XPath->getData("/phpsysinfo/Memory/Used"), $XPath->getData("/phpsysinfo/Memory/Total"), $scale_factor);
    $ram .= "&nbsp;&nbsp;" . $XPath->getData("/phpsysinfo/Memory/Percent") . "% ";

    $swap = create_bargraph($XPath->getData("/phpsysinfo/Swap/Used"), $XPath->getData("/phpsysinfo/Swap/Total"), $scale_factor);
    $swap .= "&nbsp;&nbsp;" . $XPath->getData("/phpsysinfo/Swap/Percent") . "% ";

    if ($XPath->match("/phpsysinfo/Memory/AppPercent")) {
	$app = create_bargraph($XPath->getData("/phpsysinfo/Memory/App"), $XPath->getData("/phpsysinfo/Memory/Total"), $scale_factor);
        $app .= "&nbsp;&nbsp;" . $XPath->getData("/phpsysinfo/Memory/AppPercent") . "% ";
    }
    if ($XPath->match("/phpsysinfo/Memory/BuffersPercent")) {
	$buffers = create_bargraph($XPath->getData("/phpsysinfo/Memory/Buffers"), $XPath->getData("/phpsysinfo/Memory/Total"), $scale_factor);
        $buffers .= "&nbsp;&nbsp;" . $XPath->getData("/phpsysinfo/Memory/BuffersPercent") . "% ";
    }
    if ($XPath->match("/phpsysinfo/Memory/CachedPercent")) {
	$cached = create_bargraph($XPath->getData("/phpsysinfo/Memory/Cached"), $XPath->getData("/phpsysinfo/Memory/Total"), $scale_factor);
        $cached .= "&nbsp;&nbsp;" . $XPath->getData("/phpsysinfo/Memory/CachedPercent") . "% ";
    }

    $_text = "<table cellspacing=0 cellpadding=5 border=\"0\" width=\"100%\" align=\"center\">\n"
           . "  <tr>\n"
	   . "    <td class=\"colHeader small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['type'] . "</b></font></td>\n"
           . "    <td class=\"colHeader small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['percent'] . "</b></font></td>\n"
           . "    <td class=\"colHeader small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['free'] . "</b></font></td>\n"
           . "    <td class=\"colHeader small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['used'] . "</b></font></td>\n"
           . "    <td class=\"colHeader small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['size'] . "</b></font></td>\n"
	   . "  </tr>\n"
	   
           . "  <tr>\n"
	   . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $text['phymem'] . "</font></td>\n"
           . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $ram . "</font></td>\n"
           . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Memory/Free")) . "</font></td>\n"
           . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Memory/Used")) . "</font></td>\n"
           . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Memory/Total")) . "</font></td>\n"
	   . "  </tr>\n";

    if (isset($app)) {
      $_text .= "  <tr>\n"
    	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">- " . $text['app'] . "</font></td>\n"
    	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $app . "</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">&nbsp;</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Memory/App")) . "</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">&nbsp;</font></td>\n"
	      . "  </tr>\n";
    }

    if (isset($buffers)) {
      $_text .= "  <tr>\n"
    	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">- " . $text['buffers'] . "</font></td>\n"
    	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $buffers . "</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">&nbsp;</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Memory/Buffers")) . "</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">&nbsp;</font></td>\n"
	      . "  </tr>\n";
    }

    if (isset($cached)) {
      $_text .= "  <tr>\n"
    	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">- " . $text['cached'] . "</font></td>\n"
    	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $cached . "</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">&nbsp;</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Memory/Cached")) . "</font></td>\n"
	      . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">&nbsp;</font></td>\n"
	      . "  </tr>\n";
    }

    $_text .= "  <tr>\n"
            . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $text['swap'] . "</font></td>\n"
            . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $swap . "</font></td>\n"
            . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Swap/Free")) . "</font></td>\n"
            . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Swap/Used")) . "</font></td>\n"
            . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Swap/Total")) . "</font></td>\n"
	    . "  </tr>\n";

    if (($max = sizeof($XPath->getDataParts("/phpsysinfo/Swapdevices"))) > 2) {
      for($i = 1; $i < $max; $i++) {
        $swapdev = create_bargraph($XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Used"), $XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Size"), $scale_factor);
        $swapdev .= "&nbsp;&nbsp;" . $XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Percent") . "% ";
        $_text .= "  <tr>\n"
		. "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\"> - " . $XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Device/Name") . "</font></td>\n"
                . "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $swapdev . "</font></td>\n"
                . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Free")) . "</font></td>\n"
                . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Used")) . "</font></td>\n"
                . "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Swapdevices/Mount[$i]/Size")) . "</font></td>\n"
		. "  </tr>\n";
      }
    }
    $_text .= "</table>";

    return $_text;
}

function wml_memory() {
    global $XPath;
    global $text;

    $_text = "<card id=\"memory\" title=\"" . $text['memusage'] . "\">\n"
           . "<p>" . $text['phymem'] . ":<br/>\n"
           . "- " . $text['free'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Memory/Free")) . "<br/>\n"
           . "- " . $text['used'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Memory/Used")) . "<br/>\n"
           . "- " . $text['size'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Memory/Total")) . "</p>\n";
    if ($XPath->match("/phpsysinfo/Memory/App")) {
      $_text .= "<p>" . $text['app'] . ":<br/>\n"
             . "- " . $text['used'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Memory/App")) . "</p>\n";
    }	   
    if ($XPath->match("/phpsysinfo/Memory/Cached")) {
      $_text .= "<p>" . $text['cached'] . ":<br/>\n"
             . "- " . $text['used'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Memory/Cached")) . "</p>\n";
    }	   
    if ($XPath->match("/phpsysinfo/Memory/Buffers")) {
      $_text .= "<p>" . $text['buffers'] . ":<br/>\n"
             . "- " . $text['used'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Memory/Buffers")) . "</p>\n";
    }
    $_text .= "<p><br/>" . $text['swap'] . ":<br/>\n"
            . "- " . $text['free'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Swap/Free")) . "<br/>\n"
            . "- " . $text['used'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Swap/Used")) . "<br/>\n"
            . "- " . $text['size'] . ": " . format_bytesize($XPath->getData("/phpsysinfo/Swap/Total")) . "</p>\n";
    
    $_text .= "</card>\n";
    return $_text;

}

?>
