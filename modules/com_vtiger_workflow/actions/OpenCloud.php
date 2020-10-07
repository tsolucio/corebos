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
use Gaufrette\Adapter\OpenCloud;
use Gaufrette\Filesystem;
use OpenCloud\Rackspace;

class OpenCloudAdapter {

	public function __construct($data, $workflow_context) {
		$this->rackspace_user = $data['opencloud_username'];
		$this->rackspace_apikey = $data['opencloud_password'];
		$this->rackspace_container = $data['opencloud_projectname'];
		$this->context = $workflow_context['wfgenerated_file'];
	}

	private $objectStore;

	private $container;

	public function setUp() {
		$username = $this->rackspace_user;
		$apiKey = $this->rackspace_apikey;
		$container = $this->rackspace_container;

		if (empty($username) || empty($apiKey) || empty($container)) {
			return;
		}

		$connection = new Rackspace('https://identity.api.rackspacecloud.com/v2.0/', [
			'username' => $username,
			'apiKey' => $apiKey,
		]);

		$this->container = uniqid($container);
		$this->objectStore = $connection->objectStoreService('cloudFiles', 'IAD', 'publicURL');
		$this->objectStore->createContainer($this->container);

		$adapter = new OpenCloud($this->objectStore, $this->container);
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
