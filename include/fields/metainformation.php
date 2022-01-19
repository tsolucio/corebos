<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class Field_Metadata {
	const REFERENCE_TYPE = 'reference';
	const OWNER_TYPE = 'owner';
	const OWNERGROUP_TYPE = 'group';

	const QUICKCREATE_MANDATORY = 0;
	const QUICKCREATE_NOT_ENABLED = 1;
	const QUICKCREATE_ENABLED = 2;
	const QUICKCREATE_NOT_PERMITTED = 3;

	const DISPLAYTYPE_ALL = 1;
	const DISPLAYTYPE_DETAIL_AND_LIST = 2; //e.g. Created time, Modified time
	const DISPLAYTYPE_LIST = 3;//e.g. Total, Subtotal in Inventory modules
	const DISPLAYTYPE_READONLY_MODIFY_WORKFLOW = 4;
	const DISPLAYTYPE_CREATE = 5;

	const PRESENCE_ALWAYS_VISIBLE = 0;//cannot be hidden by user
	const PRESENCE_HIDDEN = 1;
	const PRESENCE_VISIBLE = 2;//can be hidden by user

	const MASSEDIT_ALWAYS_VISIBLE = 0;//cannot be hidden by user
	const MASSEDIT_VISIBLE = 1;//can be hidden by user
	const MASSEDIT_HIDDEN = 2;

	//UITYPES
	const UITYPE_TEXT = 1;
	const UITYPE_NAME = 2; // use 1 and set to mandatory
	const UITYPE_RECORD_NO = 4; //auto increment
	const UITYPE_DATE = 5;
	const UITYPE_NUMERIC = 7; //same for float and integer, typeofdata determins
	const UITYPE_EMAIL_RECIPIENT_ADDRESS = 8;
	const UITYPE_PERCENTAGE = 9;
	const UITYPE_RECORD_RELATION = 10;
	const UITYPE_PHONE = 11;
	const UITYPE_FROM_EMAIL = 12;
	const UITYPE_EMAIL = 13;
	const UITYPE_TIME = 14;
	const UITYPE_TIMESPAN = 63;
	const UITYPE_ROLE_BASED_PICKLIST = 15;
	const UITYPE_PICKLIST = 16; //non-role based picklist
	const UITYPE_PICKLIST_MODS = 1613;
	const UITYPE_PICKLIST_MODEXTS = 1614;
	const UITYPE_PICKLIST_PICKLISTS = 1615;
	const UITYPE_PICKLIST_FILTERS = 1615;
	const UITYPE_PICKLIST_ROLES = 1024;
	const UITYPE_URL = 17;
	const UITYPE_FULL_WIDTH_TEXT_AREA = 19;
	const UITYPE_HALF_WIDTH_TEXT_AREA = 21;
	const UITYPE_COUNTER = 25; //e.g. click count & access count
	const UITYPE_FOLDER_NAME = 26;
	const UITYPE_DOWNLOAD_TYPE = 27;
	const UITYPE_FILENAME = 28;
	const UITYPE_ACTIVITY_SEND_REMINDER = 30;
	const UITYPE_MULTI_SELECT = 33;
	const UITYPE_MULTI_SELECT_MODS = 3313;
	const UITYPE_MULTI_SELECT_MODEXTS = 3314;
	const UITYPE_DATE_TIME = 50;
	const UITYPE_USER_REFERENCE = 52; // modifiedby, createdby
	const UITYPE_ASSIGNED_TO_PICKLIST = 53;
	const UITYPE_SALUTATION_OR_FIRSTNAME = 55;
	const UITYPE_CHECKBOX = 56;
	const UITYPE_IMAGE = 69;
	const UITYPE_INTERNAL_TIME = 70;
	const UITYPE_CURRENCY_AMOUNT = 71;
	const UITYPE_LINEITEMS_CURRENCY_AMOUNT = 72;
	const UITYPE_ACTIVE_USERS = 77;
	const UITYPE_TAX = 83;
	const UITYPE_SKYPE = 85;
	const UITYPE_CURRENCY_CODE = 117;//picklist with currencies
	const UITYPE_LASTNAME = 255;//last name in contacts and leads
	const UITYPE_EMAIL_PARENT_RECORD = 357;

	//UITYPES for Users module
	const UITYPE_USER_ACCESS_KEY = 3;
	const UITYPE_USER_THEME = 31;
	const UITYPE_USER_PICKLIST = 32;
	const UITYPE_USER_ROLE = 98;
	const UITYPE_USER_PASSWORD = 98;
	const UITYPE_USER_REPORTS_TO = 101;
	const UITYPE_USER_EMAIL = 104;
	const UITYPE_USER_USERNAME = 106;
	const UITYPE_USER_STATUS = 115;
	const UITYPE_USER_END_HOUR = 116;
	const UITYPE_USER_IS_ADMIN = 156;

	// UITYPES GROUPINGS
	const RELATION_TYPES = array(
		self::UITYPE_RECORD_RELATION,
		self::UITYPE_USER_REFERENCE,
		self::UITYPE_ASSIGNED_TO_PICKLIST,
		self::UITYPE_USER_REPORTS_TO,
		self::UITYPE_CURRENCY_CODE,
		self::UITYPE_FOLDER_NAME,
	);
	const PICKLIST_TYPES = array(
		self::UITYPE_ROLE_BASED_PICKLIST,
		self::UITYPE_PICKLIST,
		self::UITYPE_PICKLIST_MODS,
		self::UITYPE_PICKLIST_MODEXTS,
		self::UITYPE_PICKLIST_PICKLISTS,
		self::UITYPE_PICKLIST_FILTERS,
		self::UITYPE_PICKLIST_ROLES,
		self::UITYPE_MULTI_SELECT,
		self::UITYPE_MULTI_SELECT_MODS,
		self::UITYPE_MULTI_SELECT_MODEXTS,
	);
	const MULTIPICKLIST_SEPARATOR = ' |##| ';
	const PICKLIST_EMPTY_VALUE = '#__empty__#';
	const ATTACHMENT_ENTITY = ' Attachment';
	const FAR_FAR_AWAY_FROM_NOW = 883612800; // TWENTY EIGHT YEARS
	const USER_TYPES = array(
		self::UITYPE_USER_REFERENCE,
		self::UITYPE_ASSIGNED_TO_PICKLIST,
		self::UITYPE_ACTIVE_USERS,
		self::UITYPE_USER_REPORTS_TO,
	);

	public static function isReferenceUIType($uitype) {
		return in_array($uitype, self::RELATION_TYPES);
	}

	public static function isPicklistUIType($uitype) {
		return in_array($uitype, self::PICKLIST_TYPES);
	}
}