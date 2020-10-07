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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'vendor/autoload.php';
use Gaufrette\Adapter\Ftp;
use Gaufrette\Filesystem;

class FTPAdapter {

	public function __construct($data, $workflow_context) {
		$this->host = $data['ftp_host'];
		$this->port = $data['ftp_port'];
		$this->user = $data['ftp_username'];
		$this->password = $data['ftp_password'];
		$this->path = $data['ftp_path'];
		$this->context = $workflow_context['wfgenerated_file'];
	}

	public function setUp() {
		$host = $this->host;
		$port = $this->port;
		$user = $this->user;
		$password = $this->password;
		$baseDir = '/'.$this->path;

		if ($user === false || $password === false || $host === false || $baseDir === false) {
			return;
		}

		$adapter = new Ftp($baseDir, $host, array(
			'username' => $user,
			'password' => $password,
			'passive'  => true,
			'create'   => true,
			'ssl'      => true,
		));
		$this->filesystem = new Filesystem($adapter);
	}

	public function writeFile() {
		if (null === $this->filesystem) {
			return;
		}
		$this->filesystem->getAdapter();
		for ($y=0; $y < count($this->context); $y++) {
			$content = file_get_contents($this->context[$y]['path'].$this->context[$y]['name']);
			if (!empty($this->context[$y]['dest_name'])) {
				$this->context[$y]['name'] = $this->context[$y]['dest_name'];
			}
			$this->filesystem->write($this->context[$y]['name'], $content);
		}
	}
}
