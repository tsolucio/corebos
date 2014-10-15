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
// $Id: mbinfo.php,v 1.14 2005/12/31 17:25:26 bigmichi1 Exp $

function xml_mbtemp() {
    global $text;
    global $mbinfo;

    $_text = "";
    $data = $mbinfo->temperature();

    $_text = "  <MBinfo>\n";
    if (sizeof($data) > 0) {
    $_text .= "    <Temperature>\n";
    for ($i=0, $max = sizeof($data); $i < $max; $i++) {
        $_text .= "       <Item>\n";
        $_text .= "      <Label>" . htmlspecialchars($data[$i]['label'], ENT_QUOTES) . "</Label>\n";
        $_text .= "      <Value>" . htmlspecialchars($data[$i]['value'], ENT_QUOTES) . "</Value>\n";
        $_text .= "      <Limit>" . htmlspecialchars($data[$i]['limit'], ENT_QUOTES) . "</Limit>\n";
        $_text .= "       </Item>\n";
    }
    $_text .= "    </Temperature>\n";
    }

    return $_text;  
};

function xml_mbfans() {
    global $text;
    global $mbinfo;

    $_text = "";
    $data = $mbinfo->fans();
    if (sizeof($data) > 0) {
        $_text = "    <Fans>\n";
        for ($i=0, $max = sizeof($data); $i < $max; $i++) {
            $_text .= "       <Item>\n";
            $_text .= "      <Label>" . htmlspecialchars($data[$i]['label'], ENT_QUOTES) . "</Label>\n";
            $_text .= "      <Value>" . htmlspecialchars($data[$i]['value'], ENT_QUOTES) . "</Value>\n";
            $_text .= "      <Min>" . htmlspecialchars($data[$i]['min'], ENT_QUOTES) . "</Min>\n";
            $_text .= "      <Div>" . htmlspecialchars($data[$i]['div'], ENT_QUOTES) . "</Div>\n";
            $_text .= "       </Item>\n";
        }
        $_text .= "    </Fans>\n";
    }

    return $_text;  
};

function xml_mbvoltage() {
    global $text;
    global $mbinfo;

    $_text = "";
    $data = $mbinfo->voltage();
    if (sizeof($data) > 0) {
        $_text = "    <Voltage>\n";
        for ($i=0, $max = sizeof($data); $i < $max; $i++) {
            $_text .= "       <Item>\n";
            $_text .= "      <Label>" . htmlspecialchars($data[$i]['label'], ENT_QUOTES) . "</Label>\n";
            $_text .= "      <Value>" . htmlspecialchars($data[$i]['value'], ENT_QUOTES) . "</Value>\n";
            $_text .= "      <Min>" . htmlspecialchars($data[$i]['min'], ENT_QUOTES) . "</Min>\n";
            $_text .= "      <Max>" . htmlspecialchars($data[$i]['max'], ENT_QUOTES) . "</Max>\n";
            $_text .= "       </Item>\n";
        }
        $_text .= "    </Voltage>\n";
    }
    $_text .= "  </MBinfo>\n";

    return $_text;  
};


function html_mbtemp() {
  global $text;
  global $mbinfo;

  $textdir = direction();
  $data = array();
  $scale_factor = 2;

  $_text = "  <tr>\n"
         . "    <td><font size=\"-1\"><b>" . $text['s_label'] . "</b></font></td>\n"
	 . "    <td><font size=\"-1\"><b>" . $text['s_value'] . "</b></font></td>\n"
	 . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\"><b>" . $text['s_limit'] . "</b></font></td>\n"
	 . "  </tr>\n";

  $data = $mbinfo->temperature();
  for ($i=0, $max = sizeof($data); $i < $max; $i++) {
     $_text .= "  <tr>\n"
             . "    <td align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['label'] . "</font></td>\n"
	     . "    <td align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">";
     if ($data[$i]['value'] == 0) {
       $_text .= "Unknown - Not connected?";
     } else {
       $_text .= create_bargraph($data[$i]['value'], $data[$i]['limit'], $scale_factor);
     }
     $_text .= "&nbsp;" . round($data[$i]['value']) . "&nbsp;" . $text['degree_mark'] . "</font></td>\n"
             . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['limit'] . "&nbsp;" . $text['degree_mark'] . "</font></td>\n"
	     . "  </tr>\n";
  };

  return $_text;  
};


function html_mbfans() {
  global $text;
  global $mbinfo;
  $textdir = direction();

  $_text ="<table width=\"100%\">\n";

  $_text .= "  <tr>\n"
	  . "    <td><font size=\"-1\"><b>" . $text['s_label'] . "</b></font></td>\n"
          . "    <td align=\"" . $textdir['right'] . "\"><font size=\"-1\"><b>" . $text['s_value'] . "</b></font></td>\n"
	  . "    <td align=\"" . $textdir['right'] . "\"><font size=\"-1\"><b>" . $text['s_min'] . "</b></font></td>\n"
	  . "    <td align=\"" . $textdir['right'] . "\"><font size=\"-1\"><b>" . $text['s_div'] . "</b></font></td>\n"
	  . "  </tr>\n";

  $data = $mbinfo->fans();
  $show_fans = false;

  for ($i=0, $max = sizeof($data); $i < $max; $i++) {
      $_text .= "  <tr>\n"
              . "    <td align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['label'] . "</font></td>\n"
              . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">". round($data[$i]['value']) . " " . $text['rpm_mark'] . "</font></td>\n"
              . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['min'] . " " . $text['rpm_mark'] . "</font></td>\n"
              . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . $data[$i]['div'] . "</font></td>\n"
              . "  </tr>\n";
      if (round($data[$i]['value']) > 0) { 
          $show_fans = true;
      }
  };
  $_text .= "</table>\n";

  if (!$show_fans) {
      $_text = "";
  }

  return $_text;  
};


function html_mbvoltage() {
  global $text;
  global $mbinfo;
  $textdir = direction();

  $_text = "<table width=\"100%\">\n";

  $_text .= "  <tr>\n"
          . "    <td><font size=\"-1\"><b>" . $text['s_label'] . "</b></font></td>\n"
	  . "    <td align=\"" . $textdir['right'] . "\"><font size=\"-1\"><b>" . $text['s_value'] . "</b></font></td>\n"
	  . "    <td align=\"" . $textdir['right'] . "\"><font size=\"-1\"><b>" . $text['s_min'] . "</b></font></td>\n"
	  . "    <td align=\"" . $textdir['right'] . "\"><font size=\"-1\"><b>" . $text['s_max'] . "</b></font></td>\n"
	  . "  </tr>\n";

    $data = $mbinfo->voltage();
    for ($i=0, $max = sizeof($data); $i < $max; $i++) {
            $_text .= "  <tr>\n"
                    . "    <td align=\"" . $textdir['left'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['label'] . "</font></td>\n"
                    . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['value'] . " " . $text['voltage_mark'] . "</font></td>\n"
                    . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">". $data[$i]['min'] . " " . $text['voltage_mark'] . "</font></td>\n"
                    . "    <td align=\"" . $textdir['right'] . "\" valign=\"top\"><font size=\"-1\">" . $data[$i]['max'] . " " . $text['voltage_mark'] . "</font></td>\n"
		    . "  </tr>\n";
    };

  $_text .= "</table>\n";

  return $_text;  
};

function wml_mbtemp() {
  global $text;
  global $mbinfo;
  $data = array();
  $data = $mbinfo->temperature();

  for ($i=0; $i < $max = sizeof($data); $i++) {
     $_text .= "<p>\n"
             . $data[$i]['label'] . ": ";
     if ($data[$i]['value'] == 0) {
       $_text .= "Unknown - Not connected?</p>";
     } else {
       $_text .= round($data[$i]['value']) . "&nbsp;" . str_replace("&ordm;", "", $text['degree_mark']) . "</p>\n";
     }
  };
  
  return $_text;
}

function wml_mbfans() {
  global $text;
  global $mbinfo;
  $data = array();
  $data = $mbinfo->fans();
  
  $_text = "<card id=\"fans\" title=\"" . $text['fans'] . "\">\n";
  for ($i = 0; $i <  sizeof($data); $i++) {
      $_text .= "<p>\n"
              . $data[$i]['label'] . ": " . round($data[$i]['value']) . "&nbsp;" . $text['rpm_mark'] . "</p>\n";
      }
  $_text .= "</card>\n";

  return $_text;  
}

function wml_mbvoltage() {
  global $text;
  global $mbinfo;
  $data = array();
  $data = $mbinfo->voltage();
  
  $_text = "<card id=\"volt\" title=\"" . $text['voltage'] . "\">\n";

  for ($i = 0; $i < sizeof($data); $i++) {
    $_text .= "<p><br/>\n"
            . $data[$i]['label'] . ": " . $data[$i]['value'] . "&nbsp;" . $text['voltage_mark'] . "<br/>\n"
            . "- " . $text['s_min'] . ": " . $data[$i]['min'] . " " . $text['voltage_mark'] . "<br/>\n"
            . "- " . $text['s_max'] . ": " . $data[$i]['max'] . " " . $text['voltage_mark'] . "</p>\n";
  };

  $_text .= "</card>\n";

  return $_text;  
};

?>
