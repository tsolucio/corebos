<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : Dashboard Charts
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

class DashboardCharts {

	static public function getChartHTML($labels, $values, $graph_title, $target_values,$html_imagename, $width, $height, $left, $right, $top, $bottom, $graph_type) {
		$lbls = implode(',',$labels);
		$vals = str_replace('::',',',$values);
		$lnks = array();
		$cnt=0;
		foreach ($target_values as $value) {
			$lnks[] = $cnt.':'.$value;
			$cnt++;
		}
		$lnks = implode(',',$lnks);
		$bcolor = array();
		for ($cnt=1;$cnt<count($labels);$cnt++) {
			$bcolor[] = 'getRandomColor()';
		}
		$bcolor = implode(',',$bcolor);
		$sHTML = <<<EOF
<canvas id="$html_imagename" style="width:{$width}px;height:{$height}px;margin:auto;padding:10px;"></canvas>
<script type="text/javascript">
window.doChart{$html_imagename} = function(charttype) {
	let stuffchart = document.getElementById('{$html_imagename}');
	let stuffcontext = stuffchart.getContext('2d');
	let chartDataObject = {
		labels: [{$lbls}],
		datasets: [{
			data: [ $vals ],
			backgroundColor: [ $bcolor ]
		}]
	};
	window.schart{$html_imagename} = new Chart(stuffchart,{
		type: '{$graph_type}',
		data: chartDataObject,
		options: {
			responsive: true,
			legend: {
				position: "right",
				display: ('{$graph_type}'=='pie'),
				labels: {
					fontSize: 11,
					boxWidth: 18
				}
			}
		}
	});
	stuffchart.addEventListener('click',function(evt) {
		let activePoint = schart{$html_imagename}.getElementAtEvent(evt);
		let clickzone = { $lnks };
		let a = document.createElement("a");
		a.target = "_blank";
		a.href = clickzone[activePoint[0]._index];
		document.body.appendChild(a);
		a.click();
	});
}
doChart{$html_imagename}('{$graph_type}');
</script>
EOF;
		return $sHTML;
	}

	static public function convertToArray($values,$translate=false,$withquotes=false) {
		$vals = explode('::',$values);
		if ($translate) {
			$vals = array_map(function($v) {
				return getTranslatedString($v,$v);
			}, $vals);
		}
		$ud = $urldecode;
		if ($withquotes) {
			$vals = array_map(function($v) {
				return '"'.urldecode($v).'"';
			}, $vals);
		}
		return $vals;
	}

}