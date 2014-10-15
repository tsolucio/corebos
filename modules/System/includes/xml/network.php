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
// $Id: network.php,v 1.13 2005/12/31 17:25:26 bigmichi1 Exp $

//
// xml_network()
//
function xml_network () {
    global $sysinfo;
    $net = $sysinfo->network();

    $_text = "  <Network>\n";
    while (list($dev, $stats) = each($net)) {
        $_text .= "    <NetDevice>\n"
               .  "      <Name>" . htmlspecialchars(trim($dev), ENT_QUOTES) . "</Name>\n"
               .  "      <RxBytes>" . htmlspecialchars($stats['rx_bytes'], ENT_QUOTES) . "</RxBytes>\n"
               .  "      <TxBytes>" . htmlspecialchars($stats['tx_bytes'], ENT_QUOTES) . "</TxBytes>\n"
               .  "      <Errors>" . htmlspecialchars($stats['errs'], ENT_QUOTES) . "</Errors>\n"
               .  "      <Drops>" . htmlspecialchars($stats['drop'], ENT_QUOTES) . "</Drops>\n"
               .  "    </NetDevice>\n";
    }
    $_text .= "  </Network>\n";

    return $_text;
}

//
// html_network()
//
function html_network () {
    global $XPath;
    global $text;

    $textdir = direction();
    
    $_text = "<table border=\"0\" cellspacing=0 cellpadding=5 width=\"100%\" align=\"center\">\n"
           . "  <tr>\n"
			. "    <td width=40% class=\"colHeader small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['device'] . "</b></font></td>\n"
           . "    <td width=20% class=\"colHeader small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['received'] . "</b></font></td>\n"
           . "    <td width=20% class=\"colHeader small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['sent'] . "</b></font></td>\n"
           . "    <td width=20% class=\"colHeader small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['errors'] . "</b></font></td>\n"
	   . "  </tr>\n";
	   
    for ($i=1, $max = sizeof($XPath->getDataParts("/phpsysinfo/Network")); $i < $max; $i++) {
        if ($XPath->match("/phpsysinfo/Network/NetDevice[$i]/Name")) {
            $_text .= "  <tr>\n";
            $_text .= "    <td class=\"listTableRow small\" align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Network/NetDevice[$i]/Name") . "</font></td>\n";
            $_text .= "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Network/NetDevice[$i]/RxBytes") / 1024) . "</font></td>\n";
            $_text .= "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . format_bytesize($XPath->getData("/phpsysinfo/Network/NetDevice[$i]/TxBytes") / 1024) . "</font></td>\n";
            $_text .= "    <td class=\"listTableRow small\" align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Network/NetDevice[$i]/Errors") . '/' . $XPath->getData("/phpsysinfo/Network/NetDevice[$i]/Drops") . "</font></td>\n";
            $_text .= "  </tr>\n";
        }
    }
    $_text .= "</table>";

    return $_text;
}

function wml_network() {
    global $XPath;
    global $text;

    $_text = "<card id=\"network\" title=\"" . $text['network'] . "\">\n";
    for ($i=1, $max = sizeof($XPath->getDataParts("/phpsysinfo/Network")); $i < $max; $i++) {
        if ($XPath->match("/phpsysinfo/Network/NetDevice[$i]/Name")) {
          $_text .= "<p>" . $text['device'] . ": " . $XPath->getData("/phpsysinfo/Network/NetDevice[$i]/Name") . "<br/>"
		  . "- U: " . format_bytesize($XPath->getData("/phpsysinfo/Network/NetDevice[$i]/TxBytes") / 1024) . "<br/>"
	          . "- D: " . format_bytesize($XPath->getData("/phpsysinfo/Network/NetDevice[$i]/RxBytes") / 1024) . "<br/>"
		  . "- E: " . $XPath->getData("/phpsysinfo/Network/NetDevice[$i]/Errors") . '/' . $XPath->getData("/phpsysinfo/Network/NetDevice[$i]/Drops") . "</p>\n";
	}
    }
    $_text .= "</card>\n";
    
    return $_text;
}
?>
