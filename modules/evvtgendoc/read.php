<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : evvtgendoc
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'OpenDocument.php'; // open document class

//open test.odt
$odt = new OpenDocument('MergeTest1.odt');

echo '<pre>';
//loop throught document children
$elems=$odt->getChildren();
foreach ($elems as $child) {
	//strip headings
	//if ($child instanceof OpenDocument_Heading) {
	//    $child->delete();
	//}
	var_dump($elems);
	echo "\n";
}
echo '</pre>';

//save as no_headings.pdt
//$odt->save('no_headings.odt');
echo 'saved as no-headings.odt';
?>
