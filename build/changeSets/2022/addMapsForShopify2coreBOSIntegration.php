<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/

include_once 'modules/cbMap/cbMap.php';
include_once 'modules/Users/Users.php';

class addMapsForShopify2coreBOSIntegration extends cbupdaterWorker {

	public function applyChange() {

		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			$module_name = 'cbMap';
			vtlib_toggleModuleAccess($module_name, true);
			if ($this->isModuleInstalled($module_name)) {
				$focusnew = new cbMap();
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'getcustomers_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'Accounts';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>Accounts</originname>
					</originmodule>
					<wsconfig>
					<wsurl>https://{store_name}.myshopify.com/admin/api/{versionAPI}/{resource}.json?created_at_min=$created_at_min</wsurl>
					<wshttpmethod>GET</wshttpmethod>
					<methodname>getlistofcustomers</methodname>
					<wsresponsetime></wsresponsetime>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/json</keyvalue>
					</header>
					<header>
					<keyname>X-Shopify-Access-Token</keyname>
					<keyvalue>{access_token}</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>URLRESTFUL</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>created_at_min</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>sub_days(get_date("now"),{number_of_days})</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					</Orgfield>
					<delimiter></delimiter>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>customers</fieldname>
					<destination>
					<context>upsert_data</context>
					</destination>
					</field>
					<field>
					<fieldname>result</fieldname>
					<destination>
					<context>getlistofcustomersresponse</context>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'postproducts_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'Products';
				$focusnew->column_fields['content'] = "<map>
					<originmodule>
					<originname>Products</originname>
					</originmodule>
					<wsconfig>
					<wsurl>https://{store_name}.myshopify.com/admin/api/{versionAPI}/{resource}.json</wsurl>
					<wshttpmethod>POST</wshttpmethod>
					<methodname>postproductsshopify</methodname>
					<wsresponsetime></wsresponsetime>
					<wsuser></wsuser>
					<wspass></wspass>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/json</keyvalue>
					</header>
					<header>
					<keyname>X-Shopify-Access-Token</keyname>
					<keyvalue>{access_token}</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>JSON</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>product</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>concat('{\"title\":\"',productname,'\",\"body_html\":\"',description,'\",\"vendor\":\"',vendor_id,'\",\"product_type\":\"',productcategory,'\"}')</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					<postProcess>json_decode</postProcess>
					</Orgfield>
					<delimiter></delimiter>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>product.id</fieldname>
					<destination>
					<field>{fieldname}</field>
					</destination>
					</field>
					</Response>
					</map>";
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'getproducts_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'Products';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>Products</originname>
					</originmodule>
					<wsconfig>
					<wsurl>https://{store_name}.myshopify.com/admin/api/{versionAPI}/{resource}.json?created_at_min=$created_at_min</wsurl>
					<wshttpmethod>GET</wshttpmethod>
					<methodname>getlistofproducts</methodname>
					<wsresponsetime></wsresponsetime>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/json</keyvalue>
					</header>
					<header>
					<keyname>X-Shopify-Access-Token</keyname>
					<keyvalue>{accesstoken}</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>URLRESTFUL</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>created_at_min</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>sub_days(get_date("now"),{number_of_days})</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					</Orgfield>
					<delimiter></delimiter>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>products</fieldname>
					<destination>
					<context>upsert_data</context>
					</destination>
					</field>
					<field>
					<fieldname>product.title</fieldname>
					<destination>
					<field>productname</field>
					</destination>
					</field>
					<field>
					<fieldname>product.body_html</fieldname>
					<destination>
					<field>description</field>
					</destination>
					</field>
					<field>
					<fieldname>product.product_type</fieldname>
					<destination>
					<field>productcategory</field>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'Getchallente_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'SalesOrders';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>SalesOrders</originname>
					</originmodule>
					<wsconfig>
					<wsurl>{corebos_url}</wsurl>
					<wshttpmethod>GET</wshttpmethod>
					<methodname>getchallenge</methodname>
					<wsresponsetime/>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/json</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>URL</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>operation</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>getchallenge</OrgfieldName>
					<OrgfieldID>const</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					<field>
					<fieldname>username</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>admin</OrgfieldName>
					<OrgfieldID>const</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>result.token</fieldname>
					<destination>
					<context>cb_token</context>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'Login_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'SalesOrders';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>SalesOrders</originname>
					</originmodule>
					<wsconfig>
					<wsurl>{corebos_url}</wsurl>
					<wshttpmethod>POST</wshttpmethod>
					<methodname>login</methodname>
					<wsresponsetime/>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/x-www-form-urlencoded</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>XML</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>operation</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>login</OrgfieldName>
					<OrgfieldID>const</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					<field>
					<fieldname>username</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>admin</OrgfieldName>
					<OrgfieldID>const</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					<field>
					<fieldname>accessKey</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>hash(concat(getFromContext("cb_token"),"{accesstoken}"),"md5")</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>result.sessionName</fieldname>
					<destination>
					<context>cb_sessionid</context>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'createSO_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'cbOffers';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>cbOffers</originname>
					</originmodule>
					<wsconfig>
					<wsurl>{corebos_url}</wsurl>
					<wshttpmethod>POST</wshttpmethod>
					<methodname>MassCreate</methodname>
					<wsresponsetime/>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/x-www-form-urlencoded</keyvalue>
					</header>
					<header>
					<keyname/>
					<keyvalue/>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>JSON</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>operation</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>MassCreate</OrgfieldName>
					<OrgfieldID>const</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					<field>
					<fieldname>sessionName</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>getFromContext("cb_sessionid")</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					<field>
					<fieldname>elements</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>getFromContext("masscreatestructure")</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>result.id</fieldname>
					<destination>
					<field>description</field>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'getorders_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'SalesOrder';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>SalesOrder</originname>
					</originmodule>
					<wsconfig>
					<wsurl>https://{store_name}.myshopify.com/admin/api/{versionAPI}/{resource}.json?created_at_min=$created_at_min</wsurl>
					<wshttpmethod>GET</wshttpmethod>
					<methodname>getlistoforder</methodname>
					<wsresponsetime></wsresponsetime>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/json</keyvalue>
					</header>
					<header>
					<keyname>X-Shopify-Access-Token</keyname>
					<keyvalue>{accesstoken}</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>URLRESTFUL</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>created_at_min</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>sub_days(get_date("now"),{number_of_dayes})</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					</Orgfield>
					<delimiter></delimiter>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>orders</fieldname>
					<destination>
					<context>all_sales_order</context>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'Postorders_maincorebos';
				$focusnew->column_fields['maptype'] = 'Webservice Mapping';
				$focusnew->column_fields['targetname'] = 'SalesOrder';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>SalesOrder</originname>
					</originmodule>
					<wsconfig>
					<wsurl>https://{store_name}.myshopify.com/admin/api/{versionAPI}/{resource}.json</wsurl>
					<wshttpmethod>POST</wshttpmethod>
					<methodname>postOrders</methodname>
					<wsresponsetime/>
					<wsuser/>
					<wspass/>
					<wsheader>
					<header>
					<keyname>Content-type</keyname>
					<keyvalue>application/json</keyvalue>
					</header>
					<header>
					<keyname>X-Shopify-Access-Token</keyname>
					<keyvalue>{accesstoken}</keyvalue>
					</header>
					</wsheader>
					<wstype>REST</wstype>
					<inputtype>JSON</inputtype>
					<outputtype>JSON</outputtype>
					</wsconfig>
					<fields>
					<field>
					<fieldname>order</fieldname>
					<Orgfields>
					<Orgfield>
					<OrgfieldName>concat(\'{"line_items":"\',jsonEncode(getRelatedRecordCreateArrayConverting("Inventorydetails","ShopifyOrder",substring(id,3))),\'"}\')</OrgfieldName>
					<OrgfieldID>expression</OrgfieldID>
					<postProcess>json_decode</postProcess>
					</Orgfield>
					<delimiter/>
					</Orgfields>
					</field>
					</fields>
					<Response>
					<field>
					<fieldname>order.id</fieldname>
					<destination>
					<field>shopifyResponse</field>
					</destination>
					</field>
					</Response>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'Workflow_Shopify2SalesOrder';
				$focusnew->column_fields['maptype'] = 'Mapping';
				$focusnew->column_fields['targetname'] = 'SalesOrder';
				$focusnew->column_fields['content'] = '<map
					<originmodule>
					<originname>Shopify</originname>
					</originmodule>
					<targetmodule>
					<targetname>MassCreate</targetname>
					</targetmodule>
					<fields>
					<field>
						<fieldname>referenceId</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>id</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>element.shopifyclientid</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>customer.id</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>account_id</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>.getIDof("Accounts", "shpoify_response",shopifyclientid)</OrgfieldName>
							<OrgfieldID>expression</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>skip</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>true</OrgfieldName>
							<OrgfieldID>const</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>assigned_user_id</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>implode("",getFieldsOF($(account_id : (Accounts) assigned_user_id), "Users","user_name"))</OrgfieldName>
							<OrgfieldID>expression</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>elementType</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>SalesOrder</OrgfieldName>
							<OrgfieldID>const</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>element.discount</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>1</OrgfieldName>
							<OrgfieldID>const</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>element.discount_type</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>amount</OrgfieldName>
							<OrgfieldID>const</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>element.discount_amount</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>discount_codes.0.amount</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>element.shopifyordid</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>id</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>searchon</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>subject</OrgfieldName>
							<OrgfieldID>const</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>element.getorederresponse</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>id</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
						<field>
						<fieldname>element.pdoInformation</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>line_items</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					</fields>
					</map>';
				$focusnew->save($module_name);
				$focusnew->column_fields['assigned_user_id'] = Users::getActiveAdminID();
				$focusnew->column_fields['mapname'] = 'Workflow_shopify2InventoryDetails';
				$focusnew->column_fields['maptype'] = 'Mapping';
				$focusnew->column_fields['targetname'] = 'InventoryDetails';
				$focusnew->column_fields['content'] = '<map>
					<originmodule>
					<originname>InventoryDetails</originname>
					</originmodule>
					<targetmodule>
					<targetname>InventoryDetails</targetname>
					</targetmodule>
					<fields>
					<field>
						<fieldname>upsert_conditions</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>{number_of_condition_expressson_map}</OrgfieldName>
							<OrgfieldID>CONST</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>productid</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>0</OrgfieldName>
							<OrgfieldID>CONST</OrgfieldID>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>shopifyproductid</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>product_id</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>productname</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>name</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>account_id</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>gift_card</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>listprice</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>price</OrgfieldName>
						</Orgfield>
						</Orgfields>
					</field>
					<field>
						<fieldname>qtyinstock</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>quantity</OrgfieldName>
						</Orgfield>
						<delimiter> </delimiter>
						</Orgfields>
					</field>
					<field>
						<fieldname>quantity</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>quantity</OrgfieldName>
						</Orgfield>
						<delimiter> </delimiter>
						</Orgfields>
					</field>
					<field>
						<fieldname>title</fieldname>
						<Orgfields>
						<Orgfield>
							<OrgfieldName>description</OrgfieldName>
						</Orgfield>
						<delimiter> </delimiter>
						</Orgfields>
					</field>
					</fields>
					</map>';
				$focusnew->save($module_name);
				$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
				$this->sendMsg('A new Business Maps have been created.');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}