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
// $Id: system_footer.php,v 1.46 2006/01/21 09:30:19 bigmichi1 Exp $
//
if (!defined('IN_PHPSYSINFO')) {
    die("No Hacking");
}

$direction = direction();

if (!$hide_picklist) {
  echo "<center>";

  $update_form = "<form method=\"POST\" action=\"" . $_SERVER['PHP_SELF'] . "\">\n" . "\t" . $text['template'] . ":&nbsp;\n" . "\t<select name=\"template\">\n";

  $dir = opendir(getcwd().'/modules/System/templates/');
  while (false !== ($file = readdir($dir))) {
    if ($file != 'CVS' && $file[0] != '.' && is_dir((getcwd().'/modules/System/templates/' . $file)) ){
      $filelist[] = $file;
    } 
  } 
  closedir($dir);

  asort($filelist);

  while (list ($key, $val) = each ($filelist)) {
    if ($_COOKIE['template'] == $val) {
      $update_form .= "\t\t<option value=\"$val\" SELECTED>$val</option>\n";
    } else {
      $update_form .= "\t\t<option value=\"$val\">$val</option>\n";
    } 
  } 

  $update_form .= "\t\t<option value=\"xml\">XML</option>\n";
  $update_form .= "\t\t<option value=\"wml\">WML - experimental</option>\n";
  // auto select the random template, if we're set to random
  $update_form .= "\t\t<option value=\"random\"";
  if ($_COOKIE['template']=='random') {
    $update_form .= " SELECTED";
  } 
  $update_form .= ">random</option>\n";

  $update_form .= "\t</select>\n";

  $update_form .= "\t&nbsp;&nbsp;" . $text['language'] . ":&nbsp;\n" . "\t<select name=\"lng\">\n";

  unset($filelist);

  $dir = opendir(getcwd().'/modules/System/includes/lang/');
  while (false !== ($file = readdir($dir))) {
    if ($file[0] != '.' && is_file(getcwd().'/modules/System/includes/lang/' . $file) && eregi("\.php$", $file)) {
      $filelist[] = eregi_replace('.php', '', $file);
    } 
  } 
  closedir($dir);

  asort($filelist);
  while (list ($key, $val) = each ($filelist)) {
    if ($_COOKIE['lng'] == $val) {
      $update_form .= "\t\t<option value=\"$val\" SELECTED>$val</option>\n";
    } else {
      $update_form .= "\t\t<option value=\"$val\">$val</option>\n";
    } 
  } 
	
	$update_form .= "\t\t<option value=\"browser\"";
  if ($_COOKIE['lng']=='browser') {
    $update_form .= " SELECTED";
  } 
  $update_form .= ">browser default</option>\n";
	
  $update_form .= "\t</select>\n" . "\t<input type=\"submit\" value=\"" . $text['submit'] . "\">\n" . "</form>\n";

  echo $update_form;

  echo "\n\n</center>";
} else {
  echo "\n\n<br>";
} 

echo "\n<hr>\n";

//echo "<table width=\"100%\">\n  <tr>\n";
//echo "<td align=\"" . $direction['left'] . "\"><font size=\"-1\">" . $text['created'] . '&nbsp;<a href="http://phpsysinfo.sourceforge.net" target="_blank">phpSysInfo-' . $VERSION . '</a> ' . strftime ($text['gen_time'], time()) . "</font></td>\n";
//echo "<td align=\"" . $direction['right'] . "\"><font size=\"-1\">" . round( ( array_sum( explode( " ", microtime() ) ) - $startTime ), 4 ). " sec</font></td>\n";
//echo "  </tr>\n</table>\n";

echo "\n<br>\n</body>\n</html>\n";

?>
