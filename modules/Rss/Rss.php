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

require_once('data/CRMEntity.php');
require_once('include/database/PearDatabase.php');

require_once('vtlib/Vtiger/Feed/Parser.php');

class vtigerRSS extends CRMEntity {
	var $rsscache_time = 1200;

	/** Function to get the Rss Feeds from the Given URL
	  * This Function accepts the url string as the argument
	  * and assign the value for the class variables correspondingly
	 */
	function setRSSUrl($url)
	{
		$feed = new Vtiger_Feed_Parser();
		$success = $feed->vt_dofetch($url);
		$info = $feed->get_items();
		if(isset($info))
		{
			$this->rss_object = $info;
		}else
		{
			return false;
		}
		if($success)
		{
			$this->rss_title = $feed->get_title();
			$this->rss_link = $feed->get_link();
			$this->rss_object = $info;
			return true;
		}else
		{
			return false;
		}
	}

	/** Function to get the List of Rss feeds
	  * This Function accepts no arguments and returns the listview contents on Sucess
	  * returns "Sorry: It's not possible to reach RSS URL" if fails
	 */
	function getListViewRSSHtml()
	{
		global $default_charset;
		if(isset($this->rss_object))
		{
			$i = 0;
			foreach($this->rss_object as $key=>$item)
			{
				$stringConvert = function_exists(iconv) ? @iconv("UTF-8",$default_charset,$item->get_title()) : $item->get_title();
				$rss_title= ltrim(rtrim($stringConvert));
				
				$i = $i + 1;
				$shtml .= "<tr class='prvPrfHoverOff' onmouseover=\"this.className='prvPrfHoverOn'\" onmouseout=\"this.className='prvPrfHoverOff'\"><td><a href=\"javascript:display('".$item->get_permalink()."','feedlist_".$i."')\"; id='feedlist_".$i."' class=\"rssNews\">".$rss_title."</a></td><td>".$this->rss_title."</td></tr>";
				if($i == 10)
				{
					return $shtml;
				}
			}
			return $shtml;
		}else
		{
			$shtml = "<strong>".$mod_strings['LBL_REGRET_MSG']."</strong>";
		}
	}

	/** Function to get the List of Rss feeds in the Customized Home page
	  * This Function accepts maximum entries as arguments and returns the listview contents on Success
	  * returns "Sorry: It's not possible to reach RSS URL" if fails
	 */
	function getListViewHomeRSSHtml($maxentries)
	{
		$return_value=Array();
		$return_more=Array();
		if(isset($this->rss_object))
		{
			$y = 0;
			foreach($this->rss_object as $key=>$item)
			{
				$title =$item->get_title();
				$link = $item->get_permalink();
				
				if($y == $maxentries)
				{
					$return_more=Array("Details"=>$return_value,"More"=>$this->rss_link);
					return $return_more;
				}
				$y = $y + 1;
				$return_value[]=Array($title,$link);
			}
			$return_more=Array("Details"=>$return_value,"More"=>$this->rss_link);
			return $return_more;

		}else
		{
			return $return_more;
		}
	}

	/** Function to save the Rss Feeds
	  * This Function accepts the RssURl,Starred Status as arguments and
	  * returns true on sucess 
	  * returns false if fails
	 */
	function saveRSSUrl($url,$makestarred=0)
	{
		global $adb;

		if ($url != "")
		{
			$rsstitle = $this->rss_title;
			if($rsstitle == "")
			{
				$rsstitle = $url;
			}
			$genRssId = $adb->getUniqueID("vtiger_rss");
			$sSQL = "insert into vtiger_rss (RSSID,RSSURL,RSSTITLE,RSSTYPE,STARRED) values (?,?,?,?,?)";
			$sparams = array($genRssId, $url, $rsstitle, 0, $makestarred);
			$result = $adb->pquery($sSQL, $sparams);
			if($result)
			{
				return $genRssId;
			}else
			{
				return false;
			}
		}
	}

	function getCRMRssFeeds()
	{
		global $adb, $theme;

		$sSQL = "select * from vtiger_rss where rsstype=1";
		$result = $adb->pquery($sSQL, array());
		while($allrssrow = $adb->fetch_array($result))
		{
			$shtml .= "<tr>";
			if($allrssrow["starred"] == 1)
			{
				$shtml .= "<td width=\"15\">
					<img src=\"". vtiger_imageurl('onstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\"></td>";
			}else
			{
				$shtml .= "<td width=\"15\">
					<img src=\"". vtiger_imageurl('offstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\" onClick=\"makedefaultRss($allrssrow[rssid])\"></td>";
			}
			$shtml .= "<td class=\"rssTitle\"><a href=\"index.php?module=Rss&action=ListView&record=$allrssrow[rssid]
				\" class=\"rssTitle\">".$allrssrow[rsstitle]."</a></td>";
			$shtml .= "<td>&nbsp;</td></tr>";

		}
		return $shtml;
	}

	function getAllRssFeeds()
	{
		global $adb;

		$sSQL = "select * from vtiger_rss where rsstype <> 1";
		$result = $adb->pquery($sSQL, array());
		while($allrssrow = $adb->fetch_array($result)) {
			$shtml .= "<tr>";
			if($allrssrow["starred"] == 1)
			{
				$shtml .= "<td width=\"15\">
					<img src=\"". vtiger_imageurl('onstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\"></td>";
			}else
			{
				$shtml .= "<td width=\"15\">
					<img src=\"". vtiger_imageurl('offstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\" onClick=\"makedefaultRss($allrssrow[rssid])\"></td>";
			}
			$shtml .= "<td class=\"rssTitle\"><a href=\"index.php?module=Rss&action=ListView&record=$allrssrow[rssid]\" class=\"rssTitle\">".$allrssrow[rsstitle]."</a></td><td>&nbsp;</td>";
			$shtml .= "</tr>";
		}
		return $shtml;
	}

	/** Function to get the rssurl for the given id  
	  * This Function accepts the rssid as argument and returns the rssurl for that id
	 */
	function getRssUrlfromId($rssid)
	{
		global $adb;

		if($rssid != "")
		{
			$sSQL = "select * from vtiger_rss where rssid=?";
			$result = $adb->pquery($sSQL, array($rssid));
			$rssrow = $adb->fetch_array($result);

			if(count($rssrow) > 0)
			{
				$rssurl = $rssrow[rssurl];
			}
		}
		return $rssurl;
	}

	function getRSSHeadings($rssid)
	{
		global $adb;

		if($rssid != "")
		{
			$sSQL = "select * from vtiger_rss where rssid=?";
			$result = $adb->pquery($sSQL, array($rssid));
			$rssrow = $adb->fetch_array($result);

			if(count($rssrow) > 0)
			{
				$shtml = "<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"4\">
					<tr>
					<td class=\"rssPgTitle\">";
				if($rssrow[starred] == 1)
				{
					$shtml .= "<img src=\"". vtiger_imageurl('starred.gif', $theme) ."\" align=\"absmiddle\">";
				}else
				{
					$shtml .= "<img src=\"". vtiger_imageurl('unstarred.gif', $theme) ."\" align=\"absmiddle\">";
				}		
				$shtml .= "<a href=\"".$this->rss_object[link]."\">  ".$rssrow[rsstitle]."</a>
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
	function getTopStarredRSSFeeds()
	{
		global $adb;

		$sSQL = "select * from vtiger_rss where starred=1";
		$result = $adb->pquery($sSQL, array());
		$shtml .= "<img src=\"". vtiger_imageurl('rss.gif', $theme) ."\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"#\" onclick='window.open(\"index.php?module=Rss&action=Popup\",\"new\",\"width=500,height=300,resizable=1,scrollbars=1\");'>Add New Rss</a>";

		while($allrssrow = $adb->fetch_array($result))
		{
			$shtml .= "<img src=\"". vtiger_imageurl('rss.gif', $theme) ."\" border=\"0\" align=\"absmiddle\" hspace=\"2\">"; 
			$shtml .= "<a href=\"index.php?module=Rss&action=ListView&record=$allrssrow[rssid]\" class=\"rssFavLink\"> ".substr($allrssrow['rsstitle'],0,10)."...</a></img>";
		}
		return $shtml;
	}

	/** Function to get the rssfeed lists for the starred Rss feeds
	  * This Function accepts no argument and returns the rss feeds of
	  * the starred Feeds as HTML strings
	 */
	function getStarredRssHTML()
	{
		global $adb, $mod_strings;

		$sSQL = "select * from vtiger_rss where starred=1";
		$result = $adb->pquery($sSQL, array());
		while($allrssrow = $adb->fetch_array($result))
		{
			if($this->setRSSUrl($allrssrow["rssurl"]))
			{
				$rss_html = $this->getListViewRSSHtml();
			}
			$shtml .= $rss_html;
			if(isset($this->rss_object))
			{
				if(count($this->rss_object) > 10)
				{
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
		for($i=0;$i<$recordcount;$i++)
		{
			$starredhtml .= $sreturnhtml[$i].$sreturnhtml[$j];
			$j = $j + 1;
		}
		$starredhtml = "<table class='rssTable' cellspacing='0' cellpadding='0'>
						<tr>
						<th width='75%'>".$mod_strings['LBL_SUBJECT']."</th>
						<th width='25%'>".$mod_strings['LBL_SENDER']."</th>
						</tr>".$starredhtml."</table>";
		return $starredhtml;
	}

	/** Function to get the rssfeed lists for the given rssid  
	  * This Function accepts the rssid as argument and returns the rss feeds as HTML strings
	 */
	function getSelectedRssHTML($rssid)
	{
		global $adb, $mod_strings;

		$sSQL = "select * from vtiger_rss where rssid=?";
		$result = $adb->pquery($sSQL, array($rssid));
		while($allrssrow = $adb->fetch_array($result))
		{
			if($this->setRSSUrl($allrssrow["rssurl"]))
			{
				$rss_html = $this->getListViewRSSHtml();
			}
			$shtml .= $rss_html;
			if(isset($this->rss_object))
			{
				if(count($this->rss_object) > 10)
				{
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
		for($i=0;$i<$recordcount;$i++)
		{
			$starredhtml .= $sreturnhtml[$i].$sreturnhtml[$j];
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
	function getRSSCategoryHTML()
	{
		global $adb;
			$shtml .= "<tr>
						<td colspan=\"3\">
						<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\" style=\"margin:5 0 0 35\">".$this->getRssFeedsbyCategory()."</table>
						</td>
					  </tr>";
		return $shtml;
	}

	/** Function to get the Rss Feeds for the Given Category
	  * This Function accepts the RssCategory as argument
	  * and returns the html string for the Rss feeds lists
	 */
	function getRssFeedsbyCategory()
	{
		global $adb, $theme;

		$sSQL = "select * from vtiger_rss";
		$result = $adb->pquery($sSQL, array());
		while($allrssrow = $adb->fetch_array($result))
		{
			$shtml .= "<tr id='feed_".$allrssrow[rssid]."'>";
			$shtml .= "<td align='left' width=\"15\">";
			if($allrssrow["starred"] == 1)
			{
				$shtml .= "<img src=\"". vtiger_imageurl('onstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\">";
			}else
			{
				$shtml .= "<img src=\"". vtiger_imageurl('offstar.gif', $theme) ."\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\" onClick=\"makedefaultRss($allrssrow[rssid])\">";
			}
			$shtml .= "</td>";
			$shtml .= "<td class=\"rssTitle\" width=\"10%\" nowrap><a href=\"javascript:GetRssFeedList('$allrssrow[rssid]')\" class=\"rssTitle\">".$allrssrow[rsstitle]."</a></td><td>&nbsp;</td>";
			$shtml .= "</tr>";
		}
		return $shtml;
	}

	// Function to delete an entity with given Id
	function trash($module, $id) {
		global $log, $adb;
		$del_query = 'DELETE FROM vtiger_rss WHERE rssid=?';
		$adb->pquery($del_query, array($id));
	}

}

/** Function to get the rsstitle for the given rssid
 * This Function accepts the rssid as an optional argument and returns the title
 * if no id is passed it will return the tittle of the starred rss
 */
function gerRssTitle($id='')
{
	global $adb;
	if($id == '') {
		$query = 'select * from vtiger_rss where starred=1';
		$params = array();
	} else {
		$query = 'select * from vtiger_rss where rssid =?';
		$params = array($id);
	}
	$result = $adb->pquery($query, $params);
	$title = $adb->query_result($result,0,'rsstitle');
	return $title;
}

?>
