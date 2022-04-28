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

class cbBrowserPlugin extends Sabre\DAV\Browser\Plugin {

	public function generateDirectoryIndex($path) {
		global $current_user;
		$html = $this->generateHeader($path ? $path : '/', $path);

		$node = $this->server->tree->getNodeForPath($path);
		if ($node instanceof Sabre\DAV\ICollection) {
			$html .= "<section>\n";
			$html .= '<table class="nodeTable">';

			$subNodes = $this->server->getPropertiesForChildren($path, [
				'{DAV:}displayname',
				'{DAV:}resourcetype',
				'{DAV:}getcontenttype',
				'{DAV:}getcontentlength',
				'{DAV:}getlastmodified',
			]);

			foreach ($subNodes as $subPath => $subProps) {
				$subNode = $this->server->tree->getNodeForPath($subPath);
				$fullPath = $this->server->getBaseUri().Sabre\HTTP\encodePath($subPath);
				list(, $displayPath) = Sabre\Uri\split($subPath);

				$subNodes[$subPath]['subNode'] = $subNode;
				$subNodes[$subPath]['fullPath'] = $fullPath;
				$subNodes[$subPath]['displayPath'] = $displayPath;
			}
			uasort($subNodes, [$this, 'compareNodes']);

			foreach ($subNodes as $subProps) {
				$entityData = $subProps['subNode']->getData();
				$type = $this->getFileTypeSpecification($entityData['filetype']);

				$html .= '<tr>';
				$html .= '<td class="nameColumn"><a href="'.$this->escapeHTML($subProps['fullPath']).'"><span class="oi" data-glyph="'.$this->escapeHTML($type['icon'])
					.'"></span> '.$this->escapeHTML($subProps['displayPath']).'</a></td>';
				$html .= '<td class="typeColumn"><pre>'.$this->escapeHTML($type['string']).'</pre></td>';
				$html .= '<td>';
				if (isset($subProps['{DAV:}getcontentlength'])) {
					$html .= $this->escapeHTML($subProps['{DAV:}getcontentlength'].' bytes');
				}
				$html .= '</td><td>';
				if (!empty($entityData['lastmodified'])) {
					$dt = new DateTimeField($entityData['lastmodified']);
					$html .= $this->escapeHTML($dt->getDisplayDateTimeValue($current_user));
				}
				$html .= '</td><td>';
				if (isset($subProps['{DAV:}displayname'])) {
					$html .= $this->escapeHTML($subProps['{DAV:}displayname']);
				}
				$html .= '</td>';

				$buttonActions = '';
				$this->server->emit('browserButtonActions', [$subProps['fullPath'], $subProps['subNode'], &$buttonActions]);

				$html .= '<td>'.$buttonActions.'</td>';
				$html .= '</tr>';
			}

			$html .= '</table>';
		}

		$html .= '</section>';

		/* Start of generating actions */

		$output = '';
		if ($this->enablePost) {
			$this->server->emit('onHTMLActionsPanel', [$node, &$output, $path]);
		}

		if ($output && $this->pathSupportsActions($path, $node)) {
			$html .= '<section><h2>'.getTranslatedString('LBL_ACTIONS').'</h2>';
			$html .= "<div class=\"actions\">\n";
			$html .= $output;
			$html .= "</div>\n";
			$html .= "</section>\n";
		}

		$html .= $this->generateFooter();

		$this->server->httpResponse->setHeader('Content-Security-Policy', "default-src 'none'; img-src 'self'; style-src 'self'; font-src 'self';");

		return $html;
	}

	public function pathSupportsActions($path, $node) {
		return ($path && in_array(get_class($node), array('DirectoryFolder', 'DirectoryGroup', 'DirectoryRecord')));
	}

	public function generateHeader($title, $path = null) {
		$vars = [
			'title' => $this->escapeHTML($title),
			'favicon' => $this->escapeHTML($this->getAssetUrl('favicon.ico')),
			'style' => $this->escapeHTML($this->getAssetUrl('sabredav.css')),
			'iconstyle' => $this->escapeHTML($this->getAssetUrl('openiconic/open-iconic.css')),
			'logo' => $this->escapeHTML($this->getAssetUrl('sabredav.png')),
			'baseUrl' => $this->server->getBaseUri(),
		];

		$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>$vars[title]</title>
<link rel="shortcut icon" href="$vars[favicon]"   type="image/vnd.microsoft.icon" />
<link rel="stylesheet"    href="$vars[style]"     type="text/css" />
<link rel="stylesheet"    href="$vars[iconstyle]" type="text/css" />

</head>
<body>
<header>
	<div class="logo">
		<a href="$vars[baseUrl]"><img src="$vars[logo]" alt="sabre/dav" /> $vars[title]</a>
	</div>
</header>

<nav>
HTML;

		// If the path is empty, there's no parent.
		if ($path) {
			list($parentUri) = Sabre\Uri\split($path);
			$fullPath = $this->server->getBaseUri().Sabre\HTTP\encodePath($parentUri);
			$html .= '<a href="'.$fullPath.'" class="btn">⇤ '.getTranslatedString('GoToParent', 'Settings').'</a>';
		} else {
			$html .= '<span class="btn disabled">⇤ '.getTranslatedString('GoToParent', 'Settings').'</span>';
		}
		$html .= '</nav>';
		return $html;
	}

	public function generateFooter() {
		return '';
	}

	public function htmlActionsPanel(Sabre\DAV\INode $node, &$output, $path) {
		if (!$node instanceof Sabre\DAV\ICollection) {
			return;
		}

		// We also know fairly certain that if an object is a non-extended
		// SimpleCollection, we won't need to show the panel either.
		if ('Sabre\\DAV\\SimpleCollection' === get_class($node)) {
			return;
		}

		if (get_class($node)=='DirectoryGroup') {
			$dg = $node->getData();
			if ($dg['mode']=='folder') {
				$output .= '<form method="post" action=""><h3>'
				.getTranslatedString('Create_New_Folder', 'Reports').'</h3>
				<input type="hidden" name="sabreAction" value="mkcol" /><label>'
				.getTranslatedString('Name').':</label> <input type="text" name="name" /><br />
				<input type="submit" value="'.getTranslatedString('LBL_CREATE').'" />
				</form>';
			}
		} else {
			if (get_class($node)=='DirectoryFolder') {
				$dg = $node->getData();
				if (isset($dg['module']) && $dg['module'] == 'Documents') {
					$output .= '<form method="post" action=""><h3>'
					.getTranslatedString('Create_New_Folder', 'Reports').'</h3>
					<input type="hidden" name="sabreAction" value="mkcol" /><label>'
					.getTranslatedString('Name').':</label> <input type="text" name="name" /><br />
					<input type="submit" value="'.getTranslatedString('LBL_CREATE').'" />
					</form>';					
				}
			}
			$output .= '<form method="post" action="" enctype="multipart/form-data"><h3>'
				.getTranslatedString('LBL_UPLOAD', 'Products').'</h3>
				<input type="hidden" name="sabreAction" value="put" /><label>'
				.getTranslatedString('Name').' '.getTranslatedString('LBL_OPTIONAL', 'Settings').':</label> <input type="text" name="name" /><br /><label>'
				.getTranslatedString('File').':</label> <input type="file" name="file" /><br />
				<input type="submit" value="'.getTranslatedString('LBL_UPLOAD', 'Settings').'" />
				</form>';
		}
	}

	public function getFileTypeSpecification($fileType) {
		if (strpos($fileType, ';')) {
			list($fileType, $void) = explode(';', $fileType);
		}
		$map = array(
			'node/directory' => ['Directory', 'folder'],
			'node/group' => ['LBL_GROUP', 'grid-three-up'],
			'node/letter' => ['LBL_GROUP', 'grid-two-up'],
			'node/record' => ['record', 'loop-square'],
			'image/png' => ['Image', 'image'],
			'application/pdf' => ['PDF', 'puzzle-piece'],
			'application/zip' => ['ZIP', 'ellipses'],
			'text/plain' => ['text', 'text'],
			'text/html' => ['HTML', 'browser'],
			'text/rtf' => ['text', 'document'],
			'application/vnd.oasis.opendocument.text' => ['text', 'document'],
			'application/vnd.oasis.opendocument.spreadsheet' => ['spreadsheet', 'spreadsheet'],
			'application/vnd.oasis.opendocument.presentation' => ['presentation', 'browser'],
			'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['text', 'document'],
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['presentation', 'browser'],
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['spreadsheet', 'spreadsheet'],
		);
		if (isset($map[$fileType])) {
			$type = [
				'string' => getTranslatedString($map[$fileType][0], 'Settings'),
				'icon' => $map[$fileType][1],
			];
		} else {
			$type = [
				'string' => 'Unknown',
				'icon' => 'file',
			];
		}
		return $type;
	}
}
