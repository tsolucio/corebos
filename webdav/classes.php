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
 *************************************************************************************************
 *  Module       : WEBDAV
 *************************************************************************************************/

class stubDAVDirectory extends Sabre\DAV\Collection {

	private $myPath;

	public function __construct($myPath) {
		$this->myPath = $myPath;
	}

	public function getChildren() {
		// Loop through the directory, and create objects for each node
	}

	public function getChild($name) {
		if ($name == 'Module') {
			return new MyDirectoryGroup('ModuleMain');
		}
		if ($name == 'Dokumente') {
			return new MyDirectoryGroup('DokumenteMain');
		}

		preg_match('/(.*)\[(.*)\]/', $name, $treffer);
		switch ($treffer[2]) {
			case '1':
				return new MyDirectoryGroup('Leads');
				break;
			case '2':
				return new MyDirectoryGroup('Contacts');
				break;
			case '3':
				return new MyDirectoryGroup('Accounts');
				break;
			default:
				break;
		}

		if ($name == 'Leads' || $name == '1. Leads') {
			return new stubDAVDirectory('1. Leads');
		}
		$path = $this->myPath . '/' . $name;

		// We have to throw a NotFound exception if the file didn't exist
		// if (!file_exists($path)) throw new Sabre\DAV\Exception_NotFound('The file with name: ' . $name . ' could not be found');
		// Some added security
		if ($name[0]=='.') {
			throw new Sabre\DAV\Exception\NotFound('Access denied');
		}

		if (is_dir($path)) {
			return new stubDAVDirectory($path);
		} else {
			return new MyFile($path);
		}
	}

	public function childExists($name) {
		return true;
	}

	public function getName() {
		return $this->myPath;
	}
}

class stubDAVFile extends Sabre\DAV\File {

	private $id;
	private $filename;

	public function __construct($id, $filename) {
		$this->id = $id;
		$this->filename = $filename;
	}

	public function getName() {
		return $this->filename;
	}

	public function get() {
		return 'tzui';
	}

	public function getSize() {
		return 123456;
	}

	public function getETag() {
		return '"' . md5($this->id) . '"';
	}
}

