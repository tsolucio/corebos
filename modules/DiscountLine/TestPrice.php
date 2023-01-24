<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : Price Modification Tester
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

$accid = $ctoid = $pdoid = '';
$prices = false;
if (isset($_REQUEST['pdo'])) {
	$accid = empty($_REQUEST['acc']) ? 0 : vtlib_purify($_REQUEST['acc']);
	$ctoid = empty($_REQUEST['cto']) ? 0 : vtlib_purify($_REQUEST['cto']);
	$pdoid = empty($_REQUEST['pdo']) ? 0 : vtlib_purify($_REQUEST['pdo']);
	include_once 'modules/DiscountLine/DiscountLine.php';
	$prices = DiscountLine::getDiscount($pdoid, $accid, $ctoid, 0);
}

?>
<style type="text/css">
.gvtestlabeltext {
	font-size: medium;
	font-weight: bold;
	padding-left:10px;
	padding-right:20px;
}
#gvtestresults {
	width: 96%;
	margin: auto;
	font-size: medium;
}
</style>
<div class="slds-card slds-p-around_small slds-m-around_medium">
<form action="index.php">
<input name="action" type="hidden" value="TestPrice">
<input name="module" type="hidden" value="DiscountLine">
<table style="width:98%;border:0;" class="small">
<tbody><tr><td style="height:2px"></td></tr>
<tr>
	<td>
	<div class="slds-media__figure">
	<a class="hdrLink" href="index.php?action=ListView&module=DiscountLine">
	<span class="slds-icon_container slds-icon-standard-account" title="<?php echo getTranslatedString('DiscountLine', 'DiscountLine'); ?>">
	<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
	<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#sales_path"></use>
	</svg>
	<span class="slds-assistive-text">PRCCALC-0000002</span>
	</span>
	<?php echo getTranslatedString('DiscountLine', 'DiscountLine').'&nbsp;-&nbsp;'.getTranslatedString('Test', 'GlobalVariable');?>
	</a>
	</div>
	</td>
</tr>
<tr><td style="height:2px"></td></tr>
</tbody></table>
<table style="width:560px;border:0;">
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('Products', 'Products');?></td>
	<td><input name="pdo" id="pdo" style='width: 250px;' value="<?php echo $pdoid; ?>"></td>
</tr>
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('Accounts', 'Accounts');?></td>
	<td><input name="acc" id="acc" style='width: 250px;' value="<?php echo $accid; ?>"></td>
</tr>
<tr>
	<td class='gvtestlabeltext'><?php echo getTranslatedString('Contacts', 'Contacts');?></td>
	<td><input name="cto" id="cto" style='width: 250px;' value="<?php echo $ctoid; ?>"></td>
</tr>
<tr><td style="height:6px"></td></tr>
<tr>
	<td colspan="2"><button onclick="javascript:gvSearchVariableValue();"><?php echo getTranslatedString('Search Value', 'GlobalVariable');?></button></td>
</tr>
<tr><td style="height:6px"></td></tr>
</table>
</form>
<div name="gvtestresults" id="gvtestresults">
<?php
$valinfo = DiscountLine::getValidationInfo();
for ($info=0; $info<count($valinfo); $info++) {
	echo $valinfo[$info].'<br>';
}
if ($prices===false) {
	echo '<b>No price modification found</b>';
} else {
	echo '<b>Unit Price: '.$prices['unit price'].'</b><br>';
	echo '<b>Discount: '.$prices['discount'].'</b>';
}
?>
</div>
</div>
