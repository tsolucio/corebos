<?php
/**
 * Interface Namespaces
 *
 * This interface just gives some constants to the classes which implements it.
 *
 * @category    File Formats
 * @package    	OpenDocumentPHP
 * @subpackage	util
 * @author 		Norman Markgraf (nmarkgraf(at)user.sourceforge.net)
 * @copyright 	Copyright in 2006, 2007 by The OpenDocumentPHP Team
 * @license 	http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
 * @version     Release: @package_version@
 * @link       	http://opendocumentphp.org
 * @since 		0.5.3 - 11. Jul. 2007
 */
interface Namespaces {
	/**
	 * namespace OpenDocument meta
	 */
	const META = 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0';
	/**
	 * namespace OpenDocument office
	 */
	const OFFICE = 'urn:oasis:names:tc:opendocument:xmlns:office:1.0';
	/**
	 * namespace OpenDocument manifest
	 */
	const MANIFEST = 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0';
	/**
	 * namespace OpenDocument style
	 */
	const STYLE = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';
	/**
	 * namespace OpenDocument text
	 */
	const TEXT = 'urn:oasis:names:tc:opendocument:xmlns:text:1.0';
	/**
	 * namespace OpenDocument draw
	 */
	const DRAW = 'urn:oasis:names:tc:opendocument:xmlns:drawing:1.0';
	/**
	 * namespace OpenDocument table
	 */
	const TABLE = 'urn:oasis:names:tc:opendocument:xmlns:table:1.0';
	/**
	 * namespace OpenDocument number
	 */
	const NUMBER = 'urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0';
	/**
	 * namespace OpenDocument chart
	 */
	const CHART = 'urn:oasis:names:tc:opendocument:xmlns:chart:1.0';
	/**
	 * namespace OpenDocument form
	 */
	const FORM = 'urn:oasis:names:tc:opendocument:xmlns:form:1.0';
	/**
	 * namespace OpenDocument config
	 */
	const CONFIG = 'urn:oasis:names:tc:opendocument:xmlns:config:1.0';
	/**
	 * namespace OpenDocument presentation
	 */
	const PRESENTATION = 'urn:oasis:names:tc:opendocument:xmlns:presentation:1.0';
	/**
	 * namespace OpenDocument dr3d
	 */
	const DR3D = 'urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0';
	/**
	 * namespace OpenDocument animation
	 */
	const ANIM = 'urn:oasis:names:tc:opendocument:xmlns:animation:1.0';
	/**
	 * namespace OpenDocument script
	 */
	const SCRIPT = 'urn:oasis:names:tc:opendocument:xmlns:script:1.0';
	/**
	 * namespace OpenDocument svg
	 */
	const SVG = 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0';
	/**
	 * namespace OpenDocument fo (formation objects)
	 */
	const FO = 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0';
	/**
	 * namespace OpenDocument smil
	 */
	const SMIL = 'urn:oasis:names:tc:opendocument:xmlns:smil-compatible:1.0';
	/**
	 * namespace Dublin Core
	 */
	const DC = 'http://purl.org/dc/elements/1.1/';
	/**
	 * namespace XLink
	 */
	const XLINK = 'http://www.w3.org/1999/xlink';
	/**
	 * namespace XForms
	 */
	const XFORMS = 'http://www.w3.org/2002/xforms';
	/**
	 * namespace MathML
	 */
	const MATHML = 'http://www.w3.org/1998/Math/MathML';
}
?>
