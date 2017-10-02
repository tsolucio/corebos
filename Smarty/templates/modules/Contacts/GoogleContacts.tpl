{*<!--
/*********************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 ********************************************************************************/
-->*}
<style>{literal}
.edit-profile-block {
	width:100%;
	height:100%;
	position: absolute;
	top:0;
	left:0;
	right:0;
	bottom:0;
	background:rgba(145,145,145,0.85);
	z-index:999;
	overflow-y: auto;
}
.edit-profile-block .content {
	width:50%;
	max-width:500px;
	margin:30px auto;
	background:#fff;
	padding:20px;
	border:1px solid #919195;
	border-bottom:1px solid #666;
	
	-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

.edit-profile-block .content h2 {
	font-family: 'PT Sans', sans-serif;
    -webkit-font-smoothing: antialiased;
    font-size: 18px;
    font-weight: 700;
    line-height: 25px;
    color: #919195;
    text-transform: uppercase;
	margin-top:0;
}

.edit-profile-block .content ul {margin:0; padding:0;}
.edit-profile-block .content ul li { list-style:none;}
.close {
  float: right;
  font-size: 21px;
  font-weight: bold;
  line-height: 1;
  color: #000;
  text-shadow: 0 1px 0 #fff;
  filter: alpha(opacity=20);
  opacity: .2;
}
.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
  filter: alpha(opacity=50);
  opacity: .5;
}
button.close {
  -webkit-appearance: none;
  padding: 0;
  cursor: pointer;
  background: transparent;
  border: 0;
}
.modal-open {
  overflow: hidden;
}
.modal {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 1040;
  display: none;
  overflow: hidden;
  -webkit-overflow-scrolling: touch;
  outline: 0;
}
.modal.fade .modal-dialog {
  -webkit-transition: -webkit-transform .3s ease-out;
       -o-transition:      -o-transform .3s ease-out;
          transition:         transform .3s ease-out;
  -webkit-transform: translate(0, -25%);
      -ms-transform: translate(0, -25%);
       -o-transform: translate(0, -25%);
          transform: translate(0, -25%);
}
.modal.in .modal-dialog {
  -webkit-transform: translate(0, 0);
      -ms-transform: translate(0, 0);
       -o-transform: translate(0, 0);
          transform: translate(0, 0);
}
.modal-open .modal {
  overflow-x: hidden;
  overflow-y: auto;
}
.modal-dialog {
  position: relative;
  width: auto;
  margin: 10px;
}
.modal-content {
  position: relative;
  background-color: #fff;
  -webkit-background-clip: padding-box;
          background-clip: padding-box;
  border: 1px solid #999;
  border: 1px solid rgba(0, 0, 0, .2);
  border-radius: 6px;
  outline: 0;
  -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
          box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
}
.modal-backdrop {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background-color: #000;
}
.modal-backdrop.fade {
  filter: alpha(opacity=0);
  opacity: 0;
}
.modal-backdrop.in {
  filter: alpha(opacity=50);
  opacity: .5;
}
.modal-header {
  min-height: 16.42857143px;
  padding: 15px;
  border-bottom: 1px solid #e5e5e5;
}
.modal-header .close {
  margin-top: -2px;
}
.modal-title {
  margin: 0;
  line-height: 1.42857143;
}
.modal-body {
  position: relative;
  padding: 15px;
}
.modal-footer {
  padding: 15px;
  text-align: right;
  border-top: 1px solid #e5e5e5;
}
.modal-footer .btn + .btn {
  margin-bottom: 0;
  margin-left: 5px;
}
.modal-footer .btn-group .btn + .btn {
  margin-left: -1px;
}
.modal-footer .btn-block + .btn-block {
  margin-left: 0;
}
.modal-scrollbar-measure {
  position: absolute;
  top: -9999px;
  width: 50px;
  height: 50px;
  overflow: scroll;
}
.table {
    border-collapse: collapse !important;
  }
  .table td,
  .table th {
    background-color: #fff !important;
  }
  .table-bordered th,
  .table-bordered td {
    border: 1px solid #ddd !important;
  }
{/literal}
</style>
<form name="GoogleContacts">
	<div id="GoogleContacts" style="z-index:12;display:inline-table;width:200px;display: none;" class="layerPopup">
		<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
			<tr>
				<td width="90%" align="left" class="genHeaderSmall">{$MOD.GOOGLE_CONTACTS}&nbsp;</td>
				<td width="10%" align="right">
					<a href="javascript:fninvsh('GoogleContacts');"><img title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" /></a>
				</td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
			<tr><td class="small">
				<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
					<tr>
						<td align="left">
                                                    {if $hasToken eq true}
							<div style="height:50px;overflow-y:auto;overflow-x:hidden;" align="center">
                                                            <img src="themes/softed/images/vtbusy.gif" height="15" width="15" id="syncimage" style="vertical-align: middle;display:none"/>
                                                            <button name="SYNCH{$APP.SYNCH_NOW}" id="synchronize" value=" {$APP.SYNCH_NOW} " class="crmbutton small create" onClick="googleContactsSynch('{$MODULE}',this,'sync');" title="{$MOD.SYNC_NOW_TITLE}" style="vertical-align: middle;"> 
                                                                <span id="synchronizespan" style="display: inline-block;vertical-align: middle;">{$APP.SYNCH_NOW}</span>
                                                            </button>
							</div>
                                                    {else}
                                                        <div style="height:30px;overflow-y:auto;overflow-x:hidden;" align="center">
                                                                <input type="button" name="{$MOD.LBL_SIGN_IN_WITH_GOOGLE}" value=" {$MOD.LBL_SIGN_IN_WITH_GOOGLE} " class="crmbutton small create" onClick="googleContactsSynch('{$MODULE}',this,'signin');" title="{$MOD.SIGN_IN_TITLE}"/>&nbsp;&nbsp;
                                                        </div>
                                                    {/if}
						</td>
					</tr>
                                        <tr>
						<td align="left">
                                                    <div style="height:30px;overflow-y:auto;overflow-x:hidden;" align="center" >
                                                        <button type="button" name="{$MOD.SYNC_SETTINGS}" class="crmbutton small" onClick="googleContactsSettings('{$MODULE}',this);" title="{$MOD.SYNC_SETT_TITLE}" style="vertical-align: middle;"> 
                                                            <img src="themes/softed/images/mainSettings.PNG" height="15" width="15" style="vertical-align: middle;"/><span style="display: inline-block;vertical-align: middle;">{$MOD.SYNC_SETTINGS}</span>
                                                        </button>
                                                    </div>
						</td>
					</tr>
                                        <tr>
						<td align="left">
                                                    {if $hasToken eq true}
                                                        <div style="height:30px;overflow-y:auto;overflow-x:hidden;" align="center">
                                                                <input type="button" name="{$MOD.LOGOUT_GOOGLE}" value=" {$MOD.LOGOUT_GOOGLE} " class="crmbutton small delete" onClick="googleContactsLogOut('{$MODULE}');" title="{$MOD.LOGOUT_GOOGLE_TITLE}"/>&nbsp;&nbsp;
                                                        </div>
                                                    {/if}
						</td>
					</tr>
				</table>
			</td></tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr><td align=center class="small">
				<input type="button" name="cancel_syncsetting" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="fninvsh('GoogleContacts');" />
			</td></tr>
		</table>
	</div>
</form>

<div class="edit-profile-block"  id="GoogleContactsSettings" style="display: none;">

</div>