<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $app_strings,$mod_strings, $theme;

require_once 'data/CRMEntity.php';
require_once 'include/database/PearDatabase.php';

require_once 'vtlib/Vtiger/Feed/Parser.php';

class vtigerRSS extends CRMEntity {
	public $rsscache_time = 1200;

	/** Function to get the Rss Feeds from the Given URL
	  * This Function accepts the url string as the argument
	  * and assign the value for the class variables correspondingly
	 */
	public function setRSSUrl($url) {
		$feed = new Vtiger_Feed_Parser();
		$success = $feed->vt_dofetch($url);
		$info = $feed->get_items();
		if (isset($info)) {
			$this->rss_object = $info;
		} else {
			return false;
		}
		if ($success) {
			$this->rss_title = $feed->get_title();
			$this->rss_link = $feed->get_link();
			$this->rss_object = $info;
			return true;
		} else {
			return false;
		}
	}

	/** Function to get the List of Rss feeds
	  * This Function accepts no arguments and returns the listview contents on Sucess
	  * returns "Sorry: It's not possible to reach RSS URL" if fails
	 */
	public function getListViewRSSHtml() {
		global $default_charset;
		if (isset($this->rss_object)) {
			$i = 0;
			$shtml = '';
			foreach ($this->rss_object as $item) {
				$stringConvert = function_exists('iconv') ? @iconv('UTF-8', $default_charset, $item->get_title()) : $item->get_title();
				$rss_title= ltrim(rtrim($stringConvert));
				$i = $i + 1;
				$shtml .= "<tr class='prvPrfHoverOff' onmouseover=\"this.className='prvPrfHoverOn'\" onmouseout=\"this.className='prvPrfHoverOff'\">";
				$shtml .= "<td><a href=\"javascript:display('".$item->get_permalink()."','feedlist_".$i."')\"; id='feedlist_".$i."' class=\"rssNews\">";
				$shtml .= $rss_title."</a></td><td>".$this->rss_title."</td></tr>";
				if ($i == 10) {
					return $shtml;
				}
			}
		} else {
			$shtml = '<strong>'.getTranslatedString('LBL_REGRET_MSG', 'Rss').'</strong>';
		}
		return $shtml;
	}

	/** Function to get the List of Rss feeds in the Customized Home page
	  * This Function accepts maximum entries as arguments and returns the listview contents on Success
	  * returns "Sorry: It's not possible to reach RSS URL" if fails
	 */
	public function getListViewHomeRSSHtml($maxentries) {
		$return_value=array();
		if (isset($this->rss_object)) {
			$y = 0;
			foreach ($this->rss_object as $item) {
				$title =$item->get_title();
				$link = $item->get_permalink();
				if ($y == $maxentries) {
					return array(
						'Details'=>$return_value,
						'More'=>$this->rss_link,
					);
				}
				$y = $y + 1;
				$return_value[]=array($title, $link);
			}
			return array(
				'Details'=>$return_value,
				'More'=>$this->rss_link,
			);
		} else {
			return array();
		}
	}

	/** Function to save the Rss Feeds
	  * This Function accepts the RssURl,Starred Status as arguments and
	  * returns true on sucess
	  * returns false if fails
	 */
	public function saveRSSUrl($url, $makestarred = 0) {
		global $adb;
		if ($url != '') {
			$rsstitle = $this->rss_title;
			if ($rsstitle == '') {
				$rsstitle = $url;
			}
			$genRssId = $adb->getUniqueID("vtiger_rss");
			$sparams = array($genRssId, $url, $rsstitle, 0, $makestarred);
			$result = $adb->pquery('insert into vtiger_rss (RSSID,RSSURL,RSSTITLE,RSSTYPE,STARRED) values (?,?,?,?,?)', $sparams);
			if ($result) {
				return $genRssId;
			} else {
				return false;
			}
		}
	}

	public function getCRMRssFeeds() {
		global $adb, $theme;
		$result = $adb->pquery('select * from vtiger_rss where rsstype=1', array());
		while ($allrssrow = $adb->fetch_array($result)) {
			$shtml .= '<tr>';
			if ($allrssrow["starred"] == 1) {
				$shtml .= '<td width="15"><img src="'. vtiger_imageurl('onstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" ";
				$shtml .= 'id="star-'.$allrssrow['rssid'].'"></td>';
			} else {
				$shtml .= '<td width="15"><img src="'. vtiger_imageurl('offstar.gif', $theme);
				$shtml .= "\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-";
				$shtml .= $allrssrow['rssid']. '" onClick="makedefaultRss('.$allrssrow['rssid'].')"></td>';
			}
			$shtml .= '<td class="rssTitle"><a href="index.php?module=Rss&action=ListView&record='.$allrssrow['rssid'];
			$shtml .= '" class="rssTitle">'.$allrssrow['rsstitle'].'</a></td><td>&nbsp;</td></tr>';
		}
		return $shtml;
	}

	public function getAllRssFeeds() {
		global $adb, $theme;

		$result = $adb->pquery('select * from vtiger_rss where rsstype <> 1', array());
		$shtml = '';
		while ($allrssrow = $adb->fetch_array($result)) {
			$shtml .= "<tr>";
			if ($allrssrow["starred"] == 1) {
				$shtml .= '<td width="15"><img src="'. vtiger_imageurl('onstar.gif', $theme);
				$shtml .= "\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-".$allrssrow['rssid']."\"></td>";
			} else {
				$shtml .= '<td width="15"><img src="'. vtiger_imageurl('offstar.gif', $theme);
				$shtml .= "\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-".$allrssrow['rssid'];
				$shtml .= "\" onClick=\"makedefaultRss(".$allrssrow['rssid'].")\"></td>";
			}
			$shtml .= "<td class=\"rssTitle\"><a href=\"index.php?module=Rss&action=ListView&record=".$allrssrow['rssid']."\" class=\"rssTitle\">";
			$shtml .= $allrssrow['rsstitle']."</a></td><td>&nbsp;</td>";
			$shtml .= '</tr>';
		}
		return $shtml;
	}

	/** Function to get the rssurl for the given id
	  * This Function accepts the rssid as argument and returns the rssurl for that id
	 */
	public function getRssUrlfromId($rssid) {
		global $adb;

		if ($rssid != '') {
			$result = $adb->pquery('select * from vtiger_rss where rssid=?', array($rssid));
			$rssrow = $adb->fetch_array($result);
			if (count($rssrow) > 0) {
				$rssurl = $rssrow['rssurl'];
			}
		}
		return $rssurl;
	}

	public function getRSSHeadings($rssid) {
		global $adb, $theme;

		if ($rssid != '') {
			$result = $adb->pquery('select * from vtiger_rss where rssid=?', array($rssid));
			$rssrow = $adb->fetch_array($result);

			if (count($rssrow) > 0) {
				$shtml = "<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"4\">
					<tr>
					<td class=\"rssPgTitle\">";
				if ($rssrow['starred'] == 1) {
					$shtml .= "<img src=\"". vtiger_imageurl('starred.gif', $theme) ."\" align=\"absmiddle\">";
				} else {
					$shtml .= "<img src=\"". vtiger_imageurl('unstarred.gif', $theme) ."\" align=\"absmiddle\">";
				}
				$shtml .= "<a href=\"".$this->rss_object[link]."\"> ".$rssrow['rsstitle']."</a>
					</td>
					</tr>
					</table>";
			}
		}
		return $shtml;
	}

	/** Function to get the StarredRSSFeeds lists
	  * This Function accepts no argument and returns the rss feeds of
	  * the starred Feeds as HTML strings
	 */
	public function getTopStarredRSSFeeds() {
		global $adb, $theme;

		$result = $adb->pquery('select * from vtiger_rss where starred=1', array());
		$shtml = '<img src="'. vtiger_imageurl('rss.gif', $theme). '" border="0" align="absmiddle" hspace="2">';
		$shtml .= '<a href="#" onclick='."'window.open(\"index.php?module=Rss&action=Popup\",\"new\",\"width=500,height=300,resizable=1,scrollbars=1\");'>";
		$shtml .= getTranslatedString('LBL_ADD_RSS_FEED', 'Rss').'</a>';

		while ($allrssrow = $adb->fetch_array($result)) {
			$shtml .= "<img src=\"". vtiger_imageurl('rss.gif', $theme) ."\" border=\"0\" align=\"absmiddle\" hspace=\"2\">";
			$shtml .= '<a href="index.php?module=Rss&action=ListView&record='.$allrssrow['rssid'].'" class="rssFavLink"> ';
			$shtml .= substr($allrssrow['rsstitle'], 0, 10).'...</a></img>';
		}
		return $shtml;
	}

	/** Function to get the rssfeed lists for the starred Rss feeds
	  * This Function accepts no argument and returns the rss feeds of
	  * the starred Feeds as HTML strings
	 */
	public function getStarredRssHTML() {
		global $adb, $mod_strings;
		$result = $adb->pquery('select * from vtiger_rss where starred=1', array());
		$sreturnhtml = array();
		$shtml = '';
		while ($allrssrow = $adb->fetch_array($result)) {
			if ($this->setRSSUrl($allrssrow["rssurl"])) {
				$rss_html = $this->getListViewRSSHtml();
			}
			$shtml .= $rss_html;
			if (isset($this->rss_object)) {
				if (count($this->rss_object) > 10) {
					$shtml .= "<tr><td colspan='3' align=\"right\">
						<a target=\"_BLANK\" href=\"$this->rss_link\">".$mod_strings['LBL_MORE']."</a>
						</td></tr>";
				}
			}
			$sreturnhtml[] = $shtml;
			$shtml = "";
		}

		$recordcount = round((count($sreturnhtml))/2);
		$j = $recordcount;
		$starredhtml = '';
		for ($i=0; $i<$recordcount; $i++) {
			$starredhtml .= $sreturnhtml[$i].(isset($sreturnhtml[$j]) ? $sreturnhtml[$j] : '');
			$j = $j + 1;
		}
		return "<table class='rssTable' cellspacing='0' cellpadding='0'>
				<tr>
				<th width='75%'>".$mod_strings['LBL_SUBJECT']."</th>
				<th width='25%'>".$mod_strings['LBL_SENDER']."</th>
				</tr>".$starredhtml."</table>";
	}

	/** Function to get the rssfeed lists for the given rssid
	  * This Function accepts the rssid as argument and returns the rss feeds as HTML strings
	 */
	public function getSelectedRssHTML($rssid) {
		global $adb, $mod_strings;

		$result = $adb->pquery('select * from vtiger_rss where rssid=?', array($rssid));
		$sreturnhtml = array();
		while ($allrssrow = $adb->fetch_array($result)) {
			$shtml = '';
			if ($this->setRSSUrl($allrssrow["rssurl"])) {
				$rss_html = $this->getListViewRSSHtml();
			}
			$shtml .= $rss_html;
			if (isset($this->rss_object)) {
				if (count($this->rss_object) > 10) {
					$shtml .= "<tr><td colspan='3' align=\"right\">
							<a target=\"_BLANK\" href=\"$this->rss_link\">".$mod_strings['LBL_MORE']."</a>
							</td></tr>";
				}
			}
			$sreturnhtml[] = $shtml;
		}

		$recordcount = round((count($sreturnhtml))/2);
		$j = $recordcount;
		$starredhtml = '';
		for ($i=0; $i<$recordcount; $i++) {
			$starredhtml .= $sreturnhtml[$i].(isset($sreturnhtml[$j]) ? $sreturnhtml[$j] : '');
			$j = $j + 1;
		}
		$starredhtml = "<table class='rssTable' cellspacing='0' cellpadding='0'>
						<tr>
						<th width='75%'>".$mod_strings['LBL_SUBJECT']."</th>
						<th width='25%'>".$mod_strings['LBL_SENDER']."</th>
						</tr>".$starredhtml."</table>";
		return $starredhtml;
	}

	/** Function to get the Rss Feeds by Category
	  * This Function accepts the RssCategory as argument
	  * and returns the html string for the Rss feeds lists
	 */
	public function getRSSCategoryHTML() {
		return "<tr>
					<td colspan=\"3\">
					<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\" style=\"margin:5 0 0 35\">".$this->getRssFeedsbyCategory()."</table>
					</td>
				</tr>";
	}

	/** Function to get the Rss Feeds for the Given Category
	  * This Function accepts the RssCategory as argument
	  * and returns the html string for the Rss feeds lists
	 */
	public function getRssFeedsbyCategory() {
		global $adb, $theme;
		$result = $adb->pquery('select * from vtiger_rss', array());
		$shtml = '';
		while ($allrssrow = $adb->fetch_array($result)) {
			$shtml .= "<tr id='feed_".$allrssrow['rssid']."'>";
			$shtml .= "<td align='left' width=\"15\">";
			if ($allrssrow["starred"] == 1) {
				$shtml .= "<img src=\"". vtiger_imageurl('onstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-";
				$shtml .= $allrssrow['rssid']."\">";
			} else {
				$shtml .= "<img src=\"". vtiger_imageurl('offstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-";
				$shtml .= $allrssrow['rssid']."\" onClick=\"makedefaultRss(".$allrssrow['rssid'].")\">";
			}
			$shtml .= '</td>';
			$shtml .= "<td class=\"rssTitle\" width=\"10%\" nowrap><a href=\"javascript:GetRssFeedList('".$allrssrow['rssid']."')\" class=\"rssTitle\">";
			$shtml .= $allrssrow['rsstitle']."</a></td><td>&nbsp;</td>";
			$shtml .= '</tr>';
		}
		return $shtml;
	}

	// Function to delete an entity with given Id
	public function trash($module, $id) {
		global $adb;
		$adb->pquery('DELETE FROM vtiger_rss WHERE rssid=?', array($id));
	}
}

/** Function to get the rsstitle for the given rssid
 * This Function accepts the rssid as an optional argument and returns the title
 * if no id is passed it will return the tittle of the starred rss
 */
function gerRssTitle($id = '') {
	global $adb;
	if ($id == '') {
		$query = 'select * from vtiger_rss where starred=1';
		$params = array();
	} else {
		$query = 'select * from vtiger_rss where rssid =?';
		$params = array($id);
	}
	$result = $adb->pquery($query, $params);
	$title = $adb->query_result($result, 0, 'rsstitle');
	return $title;
}
?>
