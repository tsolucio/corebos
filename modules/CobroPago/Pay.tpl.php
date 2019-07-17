<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : CobroPago
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
?>
<div style="margin:2em;text-align:center">
<p>
<?php echo vtlib_purify($message);?>
</p>
	<?php if ($response !== null && $response->isRedirect()) {?>
	<form id="payment_form" method="POST" action="<?php echo $response->getRedirectUrl(); ?>">
		<?php foreach ($response->getRedirectData() as $key => $value) {?>
			<input type="hidden" name="<?php echo $key;?>" value="<?php echo $value;?>">
		<?php }?>
	</form>
	<script type="text/javascript">
		document.getElementById('payment_form').submit();
	</script>
	<?php }?>
</div>
