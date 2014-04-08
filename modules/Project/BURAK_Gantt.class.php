<?php

/*
BURAK_Gantt is a gantt chart class written in PHP.
Copyright (C) 2007 Burak Seydioglu

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/**
 * Gantt Class
 *
 * @package Graph
 * @author Burak Seydioglu <buraks78@gmail.com>
 * @copyright Copyright &copy; 2007 Burak Seydioglu
 */
class BURAK_Gantt {
	
	/**
	* @var resource Image object
	*/
	var $im;
	/**
	* @var integer Font type
	*/
	var $font;
	/**
	* @var array Stores component color information
	*/
	var $colors = array();
	/**
	* @var integer Sequence increment
	*/
	var $inc_y;
	/**
	* @var integer Daily increment
	*/
	var $inc_x;
	/**
	* @var integer Total number of elements
	*/
	var $n;
	
	/**
	* @var array Stores gantt element information
	*/
	var $data_gantt = array();
	/**
	* @var array Stores group information
	*/
	var $data_tree = array();
	/**
	* @var array Stores element count based on type
	*/
	var $data_count = array();
	/**
	* @var array Stores holidays
	*/
	var $data_holiday = array();
	/**
	* @var array Stores weekends. Defaults to Saturday and Sunday
	*/
	var $data_weekend = array();
	/**
	* @var array Stores gantt element relation information
	*/
	var $data_rel = array();
	/**
	* @var array Stores gantt element start order
	*/
	var $data_start = array();
	
	/**
	* @var integer Stores gantt start date
	*/
	var $gantt_start;
	/**
	* @var integer Stores gantt end date
	*/
	var $gantt_end;
	/**
	* @var integer Stores min date based on start dates of gantt elements
	*/
	var $date_min;
	/**
	* @var integer Stores max based on end dates of gantt elements
	*/
	var $date_max;
	/**
	* @var integer Stores gantt width
	*/
	var $gantt_width;
	/**
	* @var integer Stores gantt height
	*/
	var $gantt_height;
	/**
	* @var array Stores component height information
	*/
	var $heights = array();


	/**
	* Class constructor
	*
	*/
	function BURAK_Gantt() {
		$this->__construct();
	}
	
	/**
	* Class constructor
	*
	*/
	function __construct(){
		if(!in_array("gd",get_loaded_extensions())){
			die("BURAK_Gantt requires the GD library.");
		}
		$this->heights["month"] = 20;
		$this->heights["day"] = 10;
		$this->heights["group"] = 3;
		$this->heights["task"] = 12;
		$this->heights["progress"] = 4;
		$this->inc_y = $this->heights["task"] + 18;
		$this->font = 1;
		$this->colors["font"] = "000000";
		$this->colors["gantt"] = "FFFFFF";
		$this->colors["month"] = "F0F0F0";
		$this->colors["day"] = "E1E1E1";
		$this->colors["day"] = "FFFFFF";
		$this->colors["weekend"] = "FAFAFA";
		$this->colors["today"] = "F7F5DC";
		$this->colors["grid"] = "E6E6E6";
		$this->colors["task"] = "C8C8C8";
		$this->colors["lag"] = "F7F5DC";
		$this->colors["progress"] = "FF3300";
		$this->colors["milestone"] = "FF3300";
		$this->colors["line"] = "969696";
		$this->colors["group"] = "323232";
		$this->colors["holiday"] = "FEE5C8";
		$this->data_weekend = array(0,6);
		$this->data_count = array("G"=>0,"T"=>0,"M"=>0);
		$this->grid = "AUTO";
	}
	
	/**
	* Adds a new group
	*
	* @param mixed $id Group_id	
	* @param integer $label Group label
	* @param mixed $gid Group id
	*/
	function addGroup($id,$label,$gid=null){
		$this->data_count["G"]++;
		$this->data_gantt[$id] = array();
		$this->data_gantt[$id]["type"] = "G";
		$this->data_gantt[$id]["label"] = $label;
		$this->data_gantt[$id]["start"] = 0;
		$this->data_gantt[$id]["end"] = 0;
		$this->data_gantt[$id]["parent"] = $gid;
		$this->data_gantt[$id]["members"] = array();
		$this->data_gantt[$id]["pos"] = array();
		$this->data_gantt[$id]["seq"] = 0;
		$this->data_gantt[$id]["count"] = 0;
		$this->data_gantt[$id]["valid"] = FALSE;
		$this->addID($id,$gid);
	}
	
	/**
	* Adds a new task to gantt
	*
	* @param mixed $id Task id
	* @param string $start Start date
	* @param string $end End date
	* @param integer $progress Task progress 0-100
	* @param integer $label Task label
	* @param mixed $gid Group id
	*/
	function addTask($id,$start,$end,$progress,$label,$gid=null){
		$s = BURAK_Gantt::getTimestamp($start);
		$e = BURAK_Gantt::getTimestamp($end)+86400;
		if($s >= $e){
			//die("Task {$id}: Start date should be before end date");
            return;
		}
		$this->data_count["T"]++;
		$this->data_gantt[$id] = array();
		$this->data_gantt[$id]["type"] = "T";
		$this->data_gantt[$id]["label"] = $label;
		$this->data_gantt[$id]["start"] = $s;
		$this->data_gantt[$id]["end"] = $e;
		$this->data_gantt[$id]["parent"] = $gid;
		$this->data_gantt[$id]["progress"] = $progress;
		$this->data_gantt[$id]["pos"] = array();
		$this->data_gantt[$id]["seq"] = 0;
		$this->addID($id,$gid);
	}
	
	/**
	* Adds a new milestone to gantt
	*
	* @param mixed $id Task id
	* @param string $start Start date
	* @param integer $label Task label
	* @param mixed $gid Group id
	*/
	function addMilestone($id,$start,$label,$gid=null){
		$this->data_count["M"]++;
		$s = BURAK_Gantt::getTimestamp($start);
		$this->data_gantt[$id] = array();
		$this->data_gantt[$id]["type"] = "M";
		$this->data_gantt[$id]["label"] = $label;
		$this->data_gantt[$id]["start"] = $s;
		$this->data_gantt[$id]["parent"] = $gid;
		$this->data_gantt[$id]["pos"] = array();
		$this->data_gantt[$id]["seq"] = 0;
		$this->addID($id,$gid);
	}
	
	/**
	* Adds a relation
	*
	* @param mixed $parent Parent element id
	* @param mixed $child Child element id
	* @param string $type ES for end-to-start, EE for end-to-end, SS for start-to-start
	*/
	function addRelation($parent,$child,$type){
		if(!array_key_exists($parent,$this->data_tree)){
			die("{$parent} is not a valid identifier");
		}else{
			if($this->data_gantt[$parent]["type"] == "G"){
				die("Parent element can not be a group");
			}
		}
		if(!array_key_exists($child,$this->data_tree)){
			die("{$child} is not a valid identifier");
		}else{
			if($this->data_gantt[$child]["type"] == "G"){
				die("Child element can not be a group");
			}
		}
		$this->data_rel[] = array("parent"=>$parent,"child"=>$child,"type"=>$type);
	}
	
	/**
	* Adds a holiday
	*
	* @param string $date
	*/
	function addHoliday($date){
		BURAK_Gantt::validateDate($date);
		$this->data_holiday[] = $date;
	}
	
	/**
	* Sets component colors
	*
	* @param string $name Component name
	* @param string $color Color in hexadecimal format.. ie FFFFFF
	*/
	function setColor($name,$color){
		if(!preg_match("/^[0-9A-F]{6}$/i",$color)){
			die("{$color} is not an acceptable color format!");
		}
		if(!array_key_exists($name,$this->colors)){
			die("Component {$name} does not exist!");
		}
		$this->colors[$name] = $color;
	}
	
	/**
	* Sets weekend 
	*
	* paramater is an array of integers from 0 to 6. 0 for sunday, 6 for saturday  Default is array(0,6)
	*
	* @param array $days
	*/
	function setWeekend($days){
		if(!is_array($days)){
			die("Weekend days should definbed as an array");
		}
		$this->data_weekend = $days;
	}
	
	/**
	* Overrides grid type 
	*
	* @param integer $type 1 for daily, 2 for weekly, 3 for monthly
	*/
	function setGrid($type){
		switch($type){
			case 1:
			case 2:
			case 3:
			case "AUTO":
			case "auto":
				$this->grid = $type;
				break;
			default:
				die("Grid type not recognized");
				break;
		}
	}	
	
	/**
	* Outputs gantt
	*
	* @param string $file File name
	* @param integer $quality Image quality 0-100
	*/
	function outputGantt($file=null,$quality=90){
		$this->drawGantt();
		if(!empty($file)){
			imagejpeg($this->im,$file,$quality);
		}else{
			header("Content-type: image/jpeg");
			imagejpeg($this->im,"",$quality);
		}
		imagedestroy($this->im);
		//exit();
	}
	
	//
	//
	//
	// private functions start here
	//
	//
	//
	
	/**
	* Creates a new identifier
	* 
	* @param string $id
	* @param string $gid Group id
	*/
	function addID($id,$gid=null){
		if(array_key_exists($id,$this->data_tree)){
			die("{$id} already exists");
		}
		if(empty($gid)){
			if(empty($this->data_tree[0])){
				$this->data_tree[0] = array();
			}
			$this->data_tree[0][] = $id;
		}
		$this->data_tree[$id] = array();
		if(!empty($gid)){
			$this->addChild($id,$gid);
		}
	}
	
	/**
	* Adds a child to a parent group
	* 
	* @param string $id
	* @param string $gid Group id
	* @param boolean $pm Populate members array?
	*/
	function addChild($id,$gid,$pm=TRUE){
		if(!array_key_exists($gid,$this->data_tree)){
			die("{$gid} is not a valid group identifier");
		}else{
			if($this->data_gantt[$gid]["type"] != "G"){
				die("{$gid} is not a group");
			}
		}
		$this->data_tree[$gid][] = $id; // contains all group members recursively
		if($this->data_gantt[$id]["type"] != "G"){
			$this->data_gantt[$gid]["valid"] = TRUE;
		}
		if($pm){
			$this->data_gantt[$gid]["members"][] = $id; // contains first level members of the group
		}
		// add child to parent groups recursively
		foreach($this->data_tree as $k=>$v){
			// if $k !=0 -> ignore elements not within a group(data_tree[0])
			if(!empty($k)){
				// if group id is located in another group
				if(in_array($gid,$this->data_tree[$k])){
					if(!in_array($id,$this->data_tree[$k])){
						$this->addChild($id,$k,FALSE);
					}
				}
			}
		}
	}
	
	/**
	* Creates canvas
	*
	*/
	function createCanvas(){
		$this->n = $this->data_count["T"] + $this->data_count["M"] + ($this->data_count["G"]*2);
		$this->calGroupRange();
		$this->calRange();
		// calculate the height of the gantt image
		$this->gantt_height = ($this->n * $this->inc_y) + ($this->heights["month"]*2) + ($this->heights["day"]*2) + ($this->inc_y * 2);
		// calculate the width of the  gantt image
		$this->gantt_width = ((($this->gantt_end+1 - $this->gantt_start) / 86400) * $this->inc_x) + $this->inc_x +1;
		// create image
		$this->im = imagecreatetruecolor($this->gantt_width,$this->gantt_height);
		// create colors
		foreach($this->colors as $k=>$v){
			list($r,$g,$b) = sscanf($v,"%2x%2x%2x");
			$this->colors[$k] = imagecolorallocate($this->im,$r,$g,$b);
		}
		// set background color
		imagefill($this->im,0,0,$this->colors["gantt"]);
	}

	/**
	* Calculates the start and end dates for the gantt
	*
	* This function also extends the gantt chart depending on the grid type.
	* The extra space is needed to allow for long labels.
	*/
	function calRange(){
		// calculate min and max dates 
		foreach($this->data_gantt as $k=>$v){
			switch($v["type"]){
				case "T":
					BURAK_Gantt::compareDate($this->date_min,$v["start"],"<");
					BURAK_Gantt::compareDate($this->date_max,$v["end"],">");
					break;
				case "M":
					BURAK_Gantt::compareDate($this->date_min,$v["start"],"<");
					BURAK_Gantt::compareDate($this->date_max,($v["start"]+86400),">");
					break;
			}
		}
		// I am dedicating this to people who are too lazy to come up with an algorithm for padding to complete a week
		$s_offset = array(0=>6,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5);
		$e_offset = array(0=>0,1=>6,2=>5,3=>4,4=>3,5=>2,6=>1);
		// pad max and min dates to have enough space for labels and complete weeks
		$this->gantt_start = $this->date_min - ($s_offset[gmdate("w",$this->date_min)] * 86400);
		$this->gantt_end = $this->date_max + ($e_offset[gmdate("w",$this->date_max)] * 86400) + (86400*6);
		// if grid type is not set
		if(strtoupper($this->grid) == "AUTO"){
			// determine grid type
			$dif = ceil(($this->gantt_end - $this->gantt_start)/86400);
			if($dif <= 62){ // dif is less than  2 months
				$this->grid = 1; //daily grid
			}elseif($dif > 62 && $dif < 124){ // dif is 2 to 4 months
				$this->grid = 2; //weekly grid
			}else{ // dif is more that 4 months
				$this->grid = 3; //monthly grid
			}
		}
		switch($this->grid){
			case 1:
				$this->inc_x = 15;
				$this->gantt_end += (86400*7*2); // extend 2 weeks
				break;
			case 2:
				$this->inc_x = 8;
				$this->gantt_end += (86400*7*4); // extend 4 weeks
				break;
			case 3:
				$this->inc_x = 4;
				$this->gantt_end += (86400*7*8); // extend 8 weeks
				break;
		}
	}
	
	/**
	* Calculates the start and end dates for groups
	*
	* Loops through all group members (including members of sub-groups)
	* and compares start and end dates.
	*/
	function calGroupRange(){
		// calculate start and end dates for groups
		foreach($this->data_gantt as $k=>$v){
			// if element is a group
			if($this->data_gantt[$k]["type"] == "G"){
				// ignore empty groups
				if($this->isValidGroup($k)){
					// retrieve member information for the group
					foreach($this->data_tree[$k] as $member){
						switch($this->data_gantt[$member]["type"]){
							case "T":
								BURAK_Gantt::compareDate($this->data_gantt[$k]["start"],$this->data_gantt[$member]["start"],"<");
								BURAK_Gantt::compareDate($this->data_gantt[$k]["end"],$this->data_gantt[$member]["end"],">");
								$this->data_gantt[$k]["count"]++;
								break;
							case "M":
								BURAK_Gantt::compareDate($this->data_gantt[$k]["start"],$this->data_gantt[$member]["start"],"<");
								BURAK_Gantt::compareDate($this->data_gantt[$k]["end"],($this->data_gantt[$member]["start"]+86400),">");
								$this->data_gantt[$k]["count"]++;
								break;
							case "G":
								// ignore empty groups
								if($this->isValidGroup($member)){
									$this->data_gantt[$k]["count"]++;
									$this->data_gantt[$k]["count"]++;
								}
								break;
						}
					}
				}
			}
		}
	}
	
	/**
	* Calculates the start and end dates for groups
	*
	* @param integer $id Group id
	* @return boolean
	*/
	function isValidGroup($id){
		if($this->data_gantt[$id]["valid"]){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	* Sorts all gantt elements by start date
	*
	* Loops through element information and populates $this->data_start .
	* $this->data_start is an array of elements sorted by start date of the element.
	* This function is required to generate a flowing gantt chart. Elements with
	* earlier start dates appear on top-left corner while ones with later dates appear 
	* on the bottom-right corner.
	*/
	function createStartOrder(){
		// milestones should be placed first if they start on the same date as a task or group
		$type = array();
		foreach($this->data_gantt as $k=>$v){
			$type[$k] = $this->data_gantt[$k]["type"];
		}
		asort($type,SORT_STRING);
		foreach($type as $k=>$v){
			$this->data_start[$k] = $this->data_gantt[$k]["start"];
		}
		asort($this->data_start,SORT_NUMERIC);
	}
	
	/**
	* Elements are ordered by their start date
	*
	* @param array $elements
	* @return array
	*/
	function getStartOrder($elements){
		$seq = array();
		foreach($this->data_start as $k=>$v){
			if(in_array($k,$elements)){
				$seq[] = $k;
			}
		}
		return $seq;
	}
	
	/**
	* Calculate sequence
	*
	* Sequnce in this class refers to the order in which
	* gantt elements appear from top to bottom
	*/
	function createSequence(){
		$i = 0;
		$seq = $this->getStartOrder($this->data_tree[0]);
		foreach($seq as $k=>$v){
			switch($this->data_gantt[$v]["type"]){
				case "G":
					// ignore empty groups
					if($this->isValidGroup($v)){
						$this->createGroupSequence($v,$i);
					}
					break;
				default:
					$i++;
					$this->data_gantt[$v]["seq"] = $i;
					break;
			}
		}
	}
	
	/**
	* Calculate group sequence
	*
	* Group sequnce refers to the order in which
	* gantt elements appear from top to bottom
	*/
	function createGroupSequence($id,&$i){
		$i++;
		$members = $this->getStartOrder($this->data_gantt[$id]["members"]);
		$this->data_gantt[$id]["seq"] = $i;
		foreach($members as $k=>$v){
			switch($this->data_gantt[$v]["type"]){
				case "G":
					// ignore empty groups
					if($this->isValidGroup($v)){
						$this->createGroupSequence($v,$i);
					}
					break;
				default:
					$i++;
					$this->data_gantt[$v]["seq"] = $i;
					break;
			}
		}
		$i++;
	}
	
	function drawGantt(){
		$this->createCanvas();
		$this->createStartOrder();
		$this->createSequence();
		$this->drawGrid();
		foreach($this->data_gantt as $k=>$v){
			switch($v["type"]){
				case "G":
					// ignore empty groups
					if($this->isValidGroup($k)){
						$this->posGroup($k);
						$this->drawGroup($k);
					}
					break;
				case "T":
					$this->posTask($k);
					$this->drawTask($k);
					break;
				case "M":
					$this->posMilestone($k);
					$this->drawMilestone($k);
					break;
			}
		}
		$this->drawConstraints();
	}
	
	function drawConstraints(){
		foreach($this->data_rel as $v){
			// get start point
			switch($this->data_gantt[$v["parent"]]["type"]){
				case "T":
					switch($v["type"]){
						case "SS":
							$cx1 = $this->data_gantt[$v["parent"]]["pos"]["x1"];
							$cy1 = $this->data_gantt[$v["parent"]]["pos"]["y1"] + $this->heights["task"];
							break;
						case "EE":
							$cx1 = $this->data_gantt[$v["parent"]]["pos"]["x2"];
							$cy1 = $this->data_gantt[$v["parent"]]["pos"]["y2"];
							break;
						case "ES":
							$cx1 = $this->data_gantt[$v["parent"]]["pos"]["x2"];
							$cy1 = $this->data_gantt[$v["parent"]]["pos"]["y2"];
							break;
					}
					break;
				case "M":
					$cx1 = $this->data_gantt[$v["parent"]]["pos"]["x3"];
					$cy1 = $this->data_gantt[$v["parent"]]["pos"]["y3"];
					break;
			}
			// get end point
			switch($this->data_gantt[$v["child"]]["type"]){
				case "T":
					switch($v["type"]){
						case "SS":
							$cx2 = $this->data_gantt[$v["child"]]["pos"]["x1"];
							$cy2 = $this->data_gantt[$v["child"]]["pos"]["y1"];
							break;
						case "EE":
							$cx2 = $this->data_gantt[$v["child"]]["pos"]["x2"];
							$cy2 = $this->data_gantt[$v["child"]]["pos"]["y2"] - $this->heights["task"];
							break;
						case "ES":
							$cx2 = $this->data_gantt[$v["child"]]["pos"]["x1"];
							$cy2 = $this->data_gantt[$v["child"]]["pos"]["y1"];
							break;
					}
					break;
				case "M":
					$cx2 = $this->data_gantt[$v["child"]]["pos"]["x2"];
					$cy2 = $this->data_gantt[$v["child"]]["pos"]["y2"];
					break;
			}
			$this->drawConstraint($cx1,$cy1,$cx2,$cy2);
		}
	}
	
	function drawGrid(){
		$i = 0;
		$s = $this->gantt_start;
		while($s <= ($this->gantt_end+1)){
			$x1 = $i * $this->inc_x;
			$y1 = $this->heights["month"];
			$x2 = $x1;
			$y2 = $y1 + $this->gantt_height;
			// print weekend columns
			if(in_array(gmdate("w",$s),$this->data_weekend)){
				imagefilledrectangle($this->im,$x1,$y1,($x1+$this->inc_x),$y2,$this->colors["weekend"]);
			}
			// print holiday columns
			if(in_array(gmdate("Y-m-d",$s),$this->data_holiday)){
				imagefilledrectangle($this->im,$x1,$y1,($x1+$this->inc_x),$y2,$this->colors["holiday"]);
			}else{
				// print today column
				if(gmdate("Ymd",$s) == date("Ymd",time())){
					imagefilledrectangle($this->im,$x1,$y1,($x1+$this->inc_x),$y2,$this->colors["today"]);
				}
			}
			// dailiy grid
			// print vertical line after each day
			imageline($this->im,$x1,$y1,$x2,$y2,$this->colors["weekend"]);
			switch($this->grid){
				case 1: // daily grid
					// top daily scale
					imagestring($this->im,$this->font,($x1+3),($y1+3),gmdate("d",$s),$this->colors["font"]);
					// bottom daily ruler if the number of data elements is bigger than 5
					if($this->n > 5){
						imagestring($this->im,$this->font,($x1+3),($this->gantt_height - $this->heights["month"] - 11),gmdate("d",$s),$this->colors["font"]);
					}
					break;
				case 2: // weekly grid
					// monday, print vertical line
					if(gmdate("w",$s) == 1){
						imageline($this->im,$x1,$y1,$x2,$y2,$this->colors["grid"]);
					}
					break;
				case 3: // monthly grid
					// first day of month, print vertical line
					if(gmdate("j",$s) == 1){
						imageline($this->im,$x1,$y1,$x2,$y2,$this->colors["grid"]);
					}
					break;
			}
			$s += 86400;
			$i++;
		}

		// need to loop again since week labels are overlapped by grid lines
		if($this->grid == 2){
			$i = 0;
			$s = $this->gantt_start;
			while($s <= ($this->gantt_end+1)){
				if(gmdate("w",$s) == 1){ // mondays
					$x1 = $i * $this->inc_x;
					$y1 = $this->heights["month"];
					$x2 = $x1;
					$y2 = $y1 + $this->gantt_height;
					imagestring($this->im,$this->font,($x1+3),($y1+3),gmdate("W",$s),$this->colors["font"]);
					// bottom weekly ruler if the number of data elements is bigger than 5
					if($this->n > 5){
						imagestring($this->im,$this->font,($x1+3),($this->gantt_height - $this->heights["month"] - 11),gmdate("W",$s),$this->colors["font"]);
					}
				}
				$s += 86400;
				$i++;
			}
		}
		
		// month labels
		$months = array();
		$s = $this->gantt_start;
		while($s <= ($this->gantt_end+1)){
			$label = gmstrftime("%b %Y",$s);
			if(!array_key_exists($label,$months)){
				$months[$label] = 1;
			}else{
				$months[$label]++;
			}
			$s += 86400;
		}
		if(!empty($months)){
			$x_current = 0;
			$y_current = 0;
			foreach($months as $k=>$v){
				$w = $this->inc_x*$v;
				imagefilledrectangle($this->im,$x_current,$y_current,($x_current+$w),($y_current+$this->heights["month"]),$this->colors["month"]);
				imagestring($this->im,2,$x_current+5,$y_current+3,$k,$this->colors["font"]);
				if($this->n > 5){
					$y_current = $this->gantt_height - $this->heights["month"] - 1;
					imagefilledrectangle($this->im,$x_current,$y_current,($x_current+$w),($y_current+$this->heights["month"]),$this->colors["month"]);
					imagestring($this->im,2,$x_current+5,$y_current+3,$k,$this->colors["font"]);
					$y_current = 0;
				}
				$x_current += $w;
			}
		}
	}
	
	function drawConstraint($x1,$y1,$x2,$y2){
		if($x1 != $x2){
			if($x1 < $x2){
				imageline($this->im,$x1,$y1,$x2,$y1,$this->colors["line"]);
				imageline($this->im,$x2,$y1,$x2,$y2,$this->colors["line"]);
			}else{
				$y_offset = floor($this->inc_y / 3);
				if($y1 < $y2){
					$p_x1 = $x1;
					$p_y1 = $y1 + $y_offset;
					$p_x2 = $x2;
					$p_y2 = $p_y1;
				}else{
					$p_x1 = $x1;
					$p_y1 = $y1 - $y_offset;
					$p_x2 = $x2;
					$p_y2 = $p_y1;
				}
				imageline($this->im,$x1,$y1,$p_x1,$p_y1,$this->colors["line"]);
				imageline($this->im,$p_x1,$p_y1,$p_x2,$p_y2,$this->colors["line"]);
				imageline($this->im,$p_x2,$p_y2,$x2,$y2,$this->colors["line"]);
			}
		}else{
			imageline($this->im,$x1,$y1,$x2,$y2,$this->colors["line"]);
		}
		if($y1 < $y2){
			$arrow_tip_x = $x2;
			$arrow_tip_y = $y2 - 1;
		}else{
			$arrow_tip_x = $x2;
			$arrow_tip_y = $y2 + 1;
		}
		$offset_x = 2;
		$offset_y = 3;
		if($y1 < $y2){
			$vertices = array(($arrow_tip_x-$offset_x),($arrow_tip_y-$offset_y),($arrow_tip_x+$offset_x),($arrow_tip_y-$offset_y),$arrow_tip_x,$arrow_tip_y);
		}else{
			$vertices = array(($arrow_tip_x-$offset_x),($arrow_tip_y+$offset_y),($arrow_tip_x+$offset_x),($arrow_tip_y+$offset_y),$arrow_tip_x,$arrow_tip_y);
		}
		imagefilledpolygon($this->im,$vertices,3,$this->colors["line"]);
	}
	
	
	function posGroup($id){
		$w = (floor(($this->data_gantt[$id]["end"] - $this->data_gantt[$id]["start"])/86400)) * $this->inc_x;
		$x1 = $this->calX($this->data_gantt[$id]["start"]);
		$y1 = $this->calY($this->data_gantt[$id]["seq"]);
		$x2 = $x1 + $w;
		$y2 = $y1 + $this->heights["group"];
		$this->data_gantt[$id]["pos"] = array("x1"=>$x1,"y1"=>$y1,"x2"=>$x2,"y2"=>$y2);
	}

	function posTask($id){
		$w = (floor(($this->data_gantt[$id]["end"] - $this->data_gantt[$id]["start"])/86400)) * $this->inc_x;
		$x1 = $this->calX($this->data_gantt[$id]["start"]);
		$y1 = $this->calY($this->data_gantt[$id]["seq"]);
		$x2 = $x1 + $w;
		$y2 = $y1 + $this->heights["task"];
		$this->data_gantt[$id]["pos"] = array("x1"=>$x1,"y1"=>$y1,"x2"=>$x2,"y2"=>$y2);
	}
	
	function posMilestone($id){
		$x = $this->calX($this->data_gantt[$id]["start"]);
		$y = $this->calY($this->data_gantt[$id]["seq"]);
		$w = 8;
// 		$x1 = $x + (($this->inc_x - $w)/2);
		$x1 = $x - ($w/2);
		$y1 = $y + ($w/2) + (($this->heights["task"]-$w)/2);
		$x2 = $x1 + ($w/2);
		$y2 = $y + (($this->heights["task"]-$w)/2);
		$x3 = $x1 + $w;
		$y3 = $y1;
		$x4 = $x2;
		$y4 = $y2 + $w;
		$this->data_gantt[$id]["pos"] = array("x1"=>$x1,"y1"=>$y1,"x2"=>$x2,"y2"=>$y2,"x3"=>$x3,"y3"=>$y3,"x4"=>$x4,"y4"=>$y4);
	}
	
	function drawGroup($id){
		$pos = $this->data_gantt[$id]["pos"];
		imagefilledrectangle($this->im,$pos["x1"],$pos["y1"],$pos["x2"],$pos["y2"],$this->colors["group"]);
		$d = $this->data_gantt[$id]["label"];
		// append details to task name
		imagestring($this->im,2,($pos["x1"]+5),($pos["y1"]-$this->heights["task"]-3),$d,$this->colors["font"]);
		// top left 
		$vertices = array($pos["x1"],$pos["y1"],$pos["x1"]+$this->heights["group"]+5,$pos["y1"],$pos["x1"],$pos["y1"]+$this->heights["group"]+5);
		imagefilledpolygon($this->im,$vertices,3,$this->colors["group"]);
		// top right
		$vertices = array($pos["x2"],$pos["y2"]-$this->heights["group"],$pos["x2"],$pos["y2"]+5,$pos["x2"]-$this->heights["group"]-5,$pos["y2"]-$this->heights["group"]);
		imagefilledpolygon($this->im,$vertices,3,$this->colors["group"]);
		$n = $this->data_gantt[$id]["count"];
		$x1 = $pos["x1"];
		$y1 = $pos["y1"] + $this->calY($n);
		$x2 = $pos["x2"];
		$y2 = $y1 + $this->heights["group"];
		imagefilledrectangle($this->im,$x1,$y1,$x2,$y2,$this->colors["group"]);
		// top left
		$vertices = array($x1,$y1-5,$x1,$y1+$this->heights["group"],$x1+$this->heights["group"]+5,$y1+$this->heights["group"]);
		imagefilledpolygon($this->im,$vertices,3,$this->colors["group"]);
		// top right
		$vertices = array($x2,$y2-$this->heights["group"]-5,$x2,$y2,$x2-$this->heights["group"]-5,$y2);
		imagefilledpolygon($this->im,$vertices,3,$this->colors["group"]);
	}
	
	function drawTask($id){
		$pos = $this->data_gantt[$id]["pos"];
		imagefilledrectangle($this->im,$pos["x1"],$pos["y1"],$pos["x2"],$pos["y2"], $this->colors["task"]);
		$d = $this->data_gantt[$id]["label"];
		imagestring($this->im,2,($pos["x1"]+5),($pos["y1"]-$this->heights["task"]-3),$d,$this->colors["font"]);
		// border
		imagerectangle($this->im,$pos["x1"],$pos["y1"],$pos["x2"],$pos["y2"],$this->colors["line"]);
		// progress
		if(!empty($this->data_gantt[$id]["progress"])){
			$w_progress = floor(($pos["x2"]-$pos["x1"]) * ($this->data_gantt[$id]["progress"]/100));
			$x1_progress = $pos["x1"];
			$y1_progress = $pos["y1"] + (($this->heights["task"] - $this->heights["progress"])/2);
			$x2_progress = $x1_progress + $w_progress;
			$y2_progress = $y1_progress + $this->heights["progress"];
			imagefilledrectangle($this->im,($x1_progress+1),$y1_progress,($x2_progress-1),$y2_progress,$this->colors["progress"]);
		}
	}
	
	function drawMilestone($id){
		$pos = $this->data_gantt[$id]["pos"];
		$vertices = array(
			$pos["x1"],
			$pos["y1"],
			$pos["x2"],
			$pos["y2"],
			$pos["x3"],
			$pos["y3"],
			$pos["x4"],
			$pos["y4"]
		);
		imagefilledpolygon($this->im,$vertices,4,$this->colors["milestone"]);
		imagestring($this->im,2,($pos["x3"]+5),($pos["y2"]-$this->heights["task"]-3),$this->data_gantt[$id]["label"],$this->colors["font"]);
	}
	
	/**
	 * Calculates the abscissa of an element
	 *
	 * @param integer $start
	 * @return integer
	*/
	function calX($start){
		return floor(($start - $this->gantt_start)/86400) * $this->inc_x;
	}
	
	/**
	 * Calculates the ordinate of an element
	 *
	 * @param integer $i Element sequence
	 * @return integer
	*/
	function calY($i){
		return ($i * $this->inc_y) + $this->heights["month"] + $this->heights["day"];
	}
	
	/**
	 * Generates UNIX timestamp from YYYY-MM-DD formatted dates
	 *
	 * Value returned is always GMT
	 *
	 * @static
	 * @param string $date
	 * @return integer
	*/
	function getTimestamp($date){
		BURAK_Gantt::validateDate($date);
		list($y,$m,$d) = sscanf($date,"%4d-%2d-%2d");
		return gmmktime(0,0,0,$m,$d,$y);
	}
	
	/**
	 * Validates date format
	 *
	 * @static
	 * @param string $date
	*/
	function validateDate($date){
		if(!preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i",$date)){
			die("{$date} is not an acceptable date format!");
		}
	}
	
	/**
	 * Compares a new date to a reference date 
	 * and if TRUE updates reference date
	 *
	 * @static
	 * @param integer $ref
	 * @param integer $new
	 * @param string $op  > or <
	*/
	function compareDate(&$ref,$new,$op){
		if(empty($ref)){
			$ref = $new;
		}else{
			switch($op){
				case ">":
					if($new > $ref){
						$ref = $new;
					}
					break;
				case "<":
					if($new < $ref){
						$ref = $new;
					}
					break;
				default:
					die("Type({$op}) not recognized");
					break;
			}
		}
	}

}

?>