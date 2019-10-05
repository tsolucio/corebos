<?php
/*
 * (C) Copyright 2018 David Fernandez Gonzalez.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 */
global $current_user;

$roleid = vtlib_purify($_REQUEST['roleid']);

if (is_admin($current_user)) {
	if (!empty($roleid)) {
		RecalculateSharingRules($roleid);
	}
}
header('Location: index.php?action=RoleDetailView&module=Settings&roleid=' . urlencode($roleid));
