<?php
/*

Copyright (c) 2011 Sandeep.C.R, <sandeepcr2@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

// Version 1.31
// Dec 30 2011 

// List of Recent modifications

// Added nodeType,and htmlEncodedNodeContent keyes to the array that 
// Search function prints.

// Generate error on non-existant source nodes when copying
// Added line number in error string using debug_bactrace.

// Made output of errors the default behaviour the default mode using trigger_error
// Added a public static variable boolean variable debug, to toggle error reporting.

// Added a search function that accepts a nodename and return the nodes with that 
// name together with the actual php code that is required to access those nodes

// Added crxml::factory static method to create crxml objects on the
// fly from a raw xml string. to use with append ,appendTo, replaceWith 
// functions.
// Fixed bug in fullNode function when the called from topmost document 
// node

// Modified the behaviour of crxml node to crxml node assignment 
//		Now simply assigining a crxml node to another crxml node will 
//		replace the node inner content with source nodes inner content.
//		all the attributes of destination will be cleared and attributes
//		from source is copied to destination node.The node name of 
//		destination node remains the same.
//		If needed to append childeren of one node to another node
//		have to call $node->appendChildren($sourceNode) function.
//		If you have to compleatly replace one node with another node
// 		You have to use the $node->replaceWith($sourcenode) function.

// A small bug fix to prevent fatal error when non existant nodes are accessed.
// Added negative indexes
// Added Isset function
// Added function for getting errors(as mentioned below)
// Added error reporting for string access on non existant child nodes.
// Added Version and encoding option to the object Constructor
// Fixed Issue with old libxml versions when executing domxpath queries
// Fixed misc bugs
// changed the below format ..->{'prefix:localName'}($namespaceURI) to  ..->{'namespaceURI|localName|prefix'}
// Added format ..->{'prefix:localName'}($namespaceURI) to add nodes of type <prf:item xmlns:prf = "http://yahoo.com".....
// Added 'addNameSpaceDefNode' function to add nodes of type <prf:item xmlns:prf = "http://yahoo.com".....
// Added remove function to remove a node
// Added emptyNode function to delete all child nodes
// Fixed minor bug in fullNode.
// Made attributes to copy while using fullNode function
// Fixed minor bug in offsetunset
// Fixed a minor bug in offsetSet
// Added fullnode function to use in xml editing.
// Added support for namespaced attributes.
// XML Parser/Generator Class 

// Author: Sandeep.C.R 
// sandeepcr2@gmail.com

class crxml implements arrayAccess, iterator
{
	protected $node;
	protected $parent;
	protected $version;
	protected $encoding;

	protected $root;
	protected $offset;
	protected $nodeName;

	protected $current;
	protected $error;

	protected $nodeMode;
	public static $debug = true;

	public static function factory($xmlStr= '' ,$version='1.0',$encoding='UTF-8')
	{
		$crxml = new crxml($version,$encoding);
		if(strlen($xmlStr)>0) $crxml->loadXML($xmlStr);
		return $crxml;
	}

	public function __construct($version='1.0',$encoding='UTF-8',crxml $parent=null,crxml $root=null,$nodeName='',$offset=0)
	{
		$this->root=$root;
		$this->nodeMode= null;
		if(is_null($parent)) {
			$this->version = $version;
			$this->encoding = $encoding;
			$this->node=new DOMDocument($this->version, $this->encoding);
			$this->root=$this;
			$this->parent=null;
			$this->error = array();
		} else {
			$this->node=null;
			$this->parent=$parent;
			$this->error=&$parent->error;
		}
		$this->offset=$offset;
		$this->nodeName=$nodeName;
	}
	public function __call($name,$args) 
	{
		if(strpos($name,":")!==false) {
			list($prefix,$localName)=explode(':',$name);
			if(isset($args[0])) $nameSpaceURI = $args[0];else throw  new Exception("No URI given for namespace prefix $prefix");
			return $this->addNameSpaceDefNode($prefix,$localName,$nameSpaceURI);
		}
	}
	public function __unset($name)
	{
		if($node=$this->_getNode()) {
			if($childNodes=$this->_getChildrenForNode($node,$name)) {
				if($childNode=$childNodes->item($this->offset)) {
					$node->removeChild($childNode);
				}
			}
		}
	}
	public function __get($name)
	{
		if($this->offset>0)
		{
			$crxml=new crxml($this->version,$this->encoding,$this,$this->root,$name);
			if($this->parent->_getNode()) {
				if($node=$this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($this->offset)) {
					$childNode=$this->_getChildrenForNode($node,$name)->item(0);
					$crxml->node=$childNode;
				}
			}
			return $crxml;
		}
		else {
			$crxml=new crxml($this->version,$this->encoding,$this,$this->root,$name);
			if($this->_getNode() && $node=$this->_getChildrenForNode($this->_getNode(),$name)->item(0)) {
				$crxml->node=$node;
			}
			return $crxml;
		}
	}
	public function __set($name,$value)
	{
		if(is_null($this->node)) {
			$this->node=$this->connectToParent();
		}
		if($this->offset==0) {
			$childNodes=$this->_getChildrenForNode($this->_getNode(),$name);
			if($node=$childNodes->item(0)) {
				$this->_assignValue($node,$value);
			} else {
				$childNode=$this->_getNewElement($name);
				$this->node->appendChild($childNode);
				$this->_assignValue($childNode,$value);
			}
			return;
		} else {
			if($this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->length-1 < $this->offset) {
				while($this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->length < $this->offset) {
					$element=$this->_getNewElement($this->nodeName);
					$this->parent->_getNode()->appendChild($element);
				}
				$element=$this->_getNewElement($this->nodeName);
				$lastNode=$this->parent->_getNode()->appendChild($element);
				$element=$this->_getNewElement($name);
				$lastNode->appendChild($element);
				$this->_assignValue($element,$value);
			} else {
				$targetNode=$this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($this->offset);
				if($node=$this->_getChildrenForNode($targetNode,$name)->item(0)) {
					$this->_assignValue($node,$value);
				} else {
					$element=$this->_getNewElement($name);
					$targetNode->appendChild($element);
					$this->_assignValue($element,$value);
				}
			}

		}
	}
	public function debug($flag)
	{
		self::$debug = $flag;
	}
	public function __isset($name)
	{
	if($this->offset>0)
		{
			if($this->parent->_getNode()) {
				if($node=$this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($this->offset)) {
					if($this->_getChildrenForNode($node,$name)->length==0) return false;else return true;
				}
			}
			return false;
		}
		else {
			if($this->_getNode())  {
				if($this->_getChildrenForNode($this->_getNode(),$name)->length==0) return false; else return true;
			}
		}
		return false;
	}

	public function setError($error)
	{
		$this->error[] = $error;
		if(self::$debug) {
		if(function_exists('debug_backtrace'))
		{
			$stack=debug_backtrace(false);
			foreach($stack as $v)
			if(isset($v['file']) && substr($v['file'],-9)!='crXml.php' )
			{
				$error_line="\n<b>Called from:".$v['file'].",Line:<span style=\"color:red\">".$v['line']."</span></b>\n";
				break;
			}
		}
			trigger_error("<pre>$error{$error_line}</pre>");
		}
	}
	public function getError()
	{
		return "<pre>".join("\n",$this->error)."</pre>";
	}
	public function connectToParent()
	{
		if(is_null($this->parent) || !is_null($this->_getNode())) {
			if($this->offset>0) {
				if($this->parent) {
					return $this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($this->offset);
				}
			} else {
				return $this->node;
			}
		} else {
			$parentNode=$this->parent->connectToParent();
			if($this->_getChildrenForNode($parentNode,$this->nodeName)->length-1 < $this->offset) {
				while($this->_getChildrenForNode($parentNode,$this->nodeName)->length < $this->offset) {
					$element=$this->_getNewElement($this->nodeName);
					$parentNode->appendChild($element);
				}
			}
			if($namespaceURI = $parentNode->namespaceURI) {
				if(!$parentNode->lookupPrefix($namespaceURI)  ) {
				$element=$this->_getNewElement("$namespaceURI|{$this->nodeName}");
				} else {
				$element=$this->_getNewElement($this->nodeName);
				}
			} else {
				$element=$this->_getNewElement($this->nodeName);
			}

			$this->node=$parentNode->appendChild($element);
			return $this->node;
		}

	}
	// arrayaccess interface
	public function offsetExists($offset)
	{
		if(is_string($offset)) {
			if($node=$this->_getNode()) {
				if($node->attributes->getNamedItem($offset))
					return true;
				else
					return false;
			} else {
				return false;
			}
		} else {
			if($node=$this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($offset)) {
				return true;
			} else {
				return false;
			}
		}
	}
	public function offsetGet($offset)
	{
		if(is_string($offset)) {
			if(strpos($offset,":")!==false) {
				list($prefix,$localName)=explode(':',$offset);
				$nameSpaceURI=$this->root->_getNode()->lookupNamespaceURI($prefix);
				if($node=$this->_getNode()->attributes->getNamedItemNS ($nameSpaceURI , $localName )) {
					return $node->nodeValue;
				}
			} else {
				if($node=$this->_getNode()) {
					if($node = $node->attributes->getNamedItem($offset))
						return $node->nodeValue;
					else 
						return false;
				} else {
					return false;
				}
			}

		} else {
			$node=clone $this;
			$node->offset=$offset;
			return $node;
		}
	}
	public function offsetSet($offset,$value)
	{
		if(!is_object($value)) $value=str_replace('&','&amp;',$value);
		if(is_null($this->_getNode())) {
			$this->node=$this->connectToParent();
		}
		if(is_string($offset)) {
			$this->_getNode()->setAttribute($offset,$value);
		} else {
			if($this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->length-1 < $offset) {
				while($this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->length < $offset) {
					$element=$this->_getNewElement($this->nodeName);
					$this->parent->_getNode()->appendChild($element);
				}					  
				$element=$this->_getNewElement($this->nodeName);
				$lastNode=$this->parent->_getNode()->appendChild($element);
				$this->_assignValue($element,$value);
			} else {
				$node=$this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($offset);
				$this->_assignValue($node,$value);
			}
		}
	}
	public function offsetUnset($offset)
	{
		$deadNode=new crxml($this->version,$this->encoding);
		if(is_string($offset)) {
			$node=$this->_getNode();
			$node->removeAttribute($offset);
		} else {
			$deadNode->node=$this->parent->_getNode();
			$deadNode->root=$this->root;
			if($node=$deadNode->_getNode()) {
				if($childNodes=$deadNode->_getChildrenForNode($node,$this->nodeName)) {
					if($childNode=$childNodes->item($offset)) {
						$node->removeChild($childNode);
					}
				}
			}
		}
	}
	function nodeValue()
	{
		return (string)$this;
	}
	function __toString()
	{
		if($node=$this->_getNode()) {
			$count = 0;	
			$return='';	
			while($n = $node->childNodes->item($count)) {
				if($n->nodeType == 	XML_TEXT_NODE || $n->nodeType == XML_CDATA_SECTION_NODE) {
					if(!ctype_space($n->nodeValue)) $return = $return.$n->nodeValue;	
					} 
				$count++;	
			}
			return $return;	
		} else {

			//Error reporting on attempt to echo non existing child elements

			$parent = $this->parent;
			$child = $this;
			while(!$parent->_getNode()) {
				$child = $parent;
				$parent = $parent->parent;
			}
			$foundChildNodes = array();
			if($parent->_getNode()->childNodes->length) 	foreach(range(0,$parent->_getNode()->childNodes->length-1) as $v) {
				$childNodeName = $parent->_getNode()->childNodes->item($v)->nodeName;
				if($childNodeName !== '#text') $foundChildNodes[] = $childNodeName;
			}
			$this->setError("<pre>crxml error:No {$child->offset}-nth child '{$child->nodeName}' found for node \" {$parent->nodeName}\".The avaliable child nodes of node {$parent->nodeName} are\n".join("\n",$foundChildNodes)."</pre>");
			return '';
		}
	}
	function loadXML($xmlString)
	{
		$this->node->loadXML($xmlString);
		$this->parent=null;
	}
	function attributes()
	{
		$return = array();
		for($i=0;($attributeNode = $this->_getNode()->attributes->item($i));$i++) {
			$return[$attributeNode->nodeName] = $attributeNode->nodeValue;
			}
		return $return;
	}

	function xml()
	{
		$node=$this->_getNode();
		$this->root->_getNode()->formatOutput = true;
		return $this->root->_getNode()->saveXML($node);
	}
	function _getNode()
	{
		if($this->offset==0) {
			return $this->node;
		} else {
			if($this->parent) if($this->parent->_getNode()) {
				if($this->offset>0) 
					return  $this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName)->item($this->offset);
				else {
					$childNodes = $this->_getChildrenForNode($this->parent->_getNode(),$this->nodeName);
					$offset = $childNodes->length+$this->offset;
					return $childNodes->item($offset);
				}
			}
			
		}
		return null;
	}
	function count($name='')
	{
		if(!is_null($this->node)) {
			if($name) {
				return $this->_getChildrenForNode($this->_getNode(),$name)->length;
			}
			else {
				return $this->_getNode()->childNodes->length;
			}
		} else  return 0;
	}
	function addNameSpaceDefNode($prefix,$nodeName,$nameSpaceURI)
	{
		if(is_null($this->node)) {
			$this->node=$this->connectToParent();
		}
		$element=$this->root->_getNode()->createElementNS($nameSpaceURI,"$prefix:$nodeName",'');
		$this->node->appendChild($element);
		$fullName = "$nameSpaceURI|$nodeName";
		return $this->$fullName;
	}
	function addNameSpace($nameSpaces)
	{
		if(is_null($this->node)) {
			$this->node=$this->connectToParent();
		}
		foreach($nameSpaces as $prefix=>$nameSpaceURI) {
			$this->_getNode()->setAttributeNS('http://www.w3.org/2000/xmlns/' ,"xmlns:$prefix" , $nameSpaceURI);
		}
		return $this;
	}
	function _getNewElement($name,$value=null)
	{
		if(strpos($name,"|")!==false) {
			$fragments = explode('|',$name);
			if(count($fragments)==3) list($nameSpaceURI,$localName,$prefix) = $fragments;
			else {
				list($nameSpaceURI,$localName) = $fragments;
				$prefix = null;
			}
			if(isset($prefix)) {
				$element=$this->root->_getNode()->createElementNS($nameSpaceURI,"$prefix:$localName",'');
			} else {
				$element=$this->root->_getNode()->createElementNS($nameSpaceURI,"$localName",'');
			}
			if($value) $element->nodeValue=$value;
			return $element;
		} else if(strpos($name,":")!==false) {
			list($prefix,$localName)=explode(':',$name);
			if(!$nameSpaceURI = $this->root->_getNode()->lookupNamespaceURI($prefix)) 
				$nameSpaceURI = $this->parent->_getNode()->lookupNamespaceURI($prefix);
			$element=$this->root->_getNode()->createElementNS($nameSpaceURI,"$prefix:$localName",'');
			if($value) $element->nodeValue=$value;
			return $element;
		} else {
			if(isset($this->parent) && $this->parentNode!=null  && $parentNode = $this->parent->_getNode()) {
				if($parentNode->isDefaultNamespace($parentNode->namespaceURI)) {
					$element=$this->root->_getNode()->createElementNS($parentNode->namespaceURI,$name,'');
					if($value) $element->nodeValue=$value;
					return $element;
				}
			}
			return new DOMElement($name,$value);
		}
	}
	function _getChildrenForNode($node,$name)
	{
		if(strpos($name,"|")!==false) {
			list($nameSpaceURI,$localName)=explode('|',$name);
			$domXpath=new DOMXpath($this->root->node);
			$domXpath->registerNamespace('atom',$nameSpaceURI);
			$childNodes=@$domXpath->query("atom:$localName",$node);
		} else if(strpos($name,":")!==false) {
			list($prefix,$localName)=explode(':',$name);
			$domXpath=new DOMXpath($this->root->node);
			if($nameSpaceURI = $this->root->node->lookupNamespaceURI($prefix)) {
				$domXpath->registerNamespace($prefix,$nameSpaceURI);
			} elseif($node->prefix == $prefix){
				$domXpath->registerNamespace($prefix,$node->namespaceURI);
			} else {
				$found = false;
				if($node->childNodes->length) 	foreach(range(0,$node->childNodes->length-1) as $v) {
					$childNode = $node->childNodes->item($v);
					$childNodeName = $node->childNodes->item($v)->nodeName;
					if($childNodeName == $name ) {
						$childNodeNamespaceURI = $childNode->namespaceURI;
						$childNodePrefix = $childNode->prefix;
						$domXpath->registerNamespace($childNodePrefix,$childNodeNamespaceURI);
						$found = true;
					}
				}
				if(!$found) {
				throw new Exception("undefined namespace prefix $prefix");
				}
			}
			$childNodes=@$domXpath->query($name,$node);
		} elseif($node->isDefaultNamespace($node->namespaceURI)) {
			$domXpath=new DOMXpath($this->root->node);
			$parentNamespaceURI = $node->namespaceURI;
			$domXpath->registerNamespace('atom',$parentNamespaceURI);
			$childNodes=@$domXpath->query("atom:$name",$node);
		} else{
			$domXpath=new DOMXpath($this->root->node);
			$childNodes=@$domXpath->query($name,$node);
		}
		if($childNodes===false) $childNodes = new DOMNodeList();
		return $childNodes;
		/* deactivated because this generates unneassary notice for the following code
		$crxml = new crxml;
		$crxml->Item->{'http://imaginarynamespaceforauthors.com|Authors'}->Author->FirstName = 'Collins';
		$crxml->Item->Authors = 'Larry';
		echo $crxml->xml();
		*/
		if($childNodes->length == 0 && 0) {
			$foundChildNodes = array();
			if($node->childNodes->length) 	foreach(range(0,$node->childNodes->length-1) as $v) {
				$childNodeName = $node->childNodes->item($v)->nodeName;
				if($childNodeName !== '#text') $foundChildNodes[] = $childNodeName;
				if(strpos($childNodeName,":")!==false) 	list($prefix,$localname)=explode(':',$childNodeName);else {
					$prefix = '';
					$localname = $childNodeName;
				}
				if(strpos($name,":")!==false) 	list($tprefix,$tlocalname)=explode(':',$name);else {
					$tlocalname = $name;
					$tprefix = '';
				}

				if($localname==$tlocalname) {
					$foundprefix = $this->root->node->lookupPrefix($node->childNodes->item($v)->namespaceURI);
					$this->setError("<pre>crxml error:\nNo child '".$name."' with default or specified ($tprefix) name space found for node {$node->nodeName}.\nBut <b>found a node</b>, '".$localname."' within namespace '".$node->childNodes->item($v)->namespaceURI."(prefix:$foundprefix)' for same node.\nPlease use format \n ..->{$node->nodeName}->".((strlen($foundprefix)>0)?"{'{$foundprefix}:{$tlocalname}'} \nto access the above node\n\n":"$tlocalname \nto access the above node\\\n"));
				}
			}
		}
		return $childNodes;
	}
	public function build()
	{
		if($this->_getNode()) $this->connectToParent();
		return $this;
	}
	public function appendTo($crxmlNode)
	{
		if($this->_getNode()) {
			$fullNode = $this->fullNode();
			$fullNode->nodeMode = 'append';
			if(!$crxmlNode->_getNode()) $crxmlNode->connectToParent();
			$crxmlNode->_assignValue($crxmlNode->_getNode(),$fullNode);
		} else {
			$this->setError('Non-existant node specified as source for assignment');
		}
	}
	public function appendNode($crxmlNode)
	{
		if(!$this->_getNode()) $this->connectToParent();
		if($crxmlNode->_getNode()) {
		$crxmlNode = $crxmlNode->fullNode();
		$crxmlNode->nodeMode = 'append';
		$this->_assignValue($this->_getNode(),$crxmlNode);
		} else {
			$this->setError('Non-existant node specified as source for appendNode()');
		}
	}

	public function appendChildren($crxmlNode)
	{
		if(!$this->_getNode()) $this->connectToParent();
		$crxmlNode->nodeMode = 'append';
		$this->_assignValue($this->_getNode(),$crxmlNode);
	}
	public function replaceWith($crxmlNode)
	{
	if(!$this->_getNode()) $this->connectToParent();
	if($crxmlNode->_getNode()) {
		$fullNode = $crxmlNode->fullNode();
		$fullNode->nodeMode = 'replace';
		$this->_assignValue($this->_getNode(),$fullNode);
		}
	}
	public function _assignValue($node,$value)
	{
		if(!is_object($value)) $value = str_replace('&','&amp;',$value);
		if(is_object($value)) {
			if(get_class($value) == 'stdClass') { 
				$node->nodeValue='';
				$cDataSection=$this->root->node->createCDATASection($value->scalar);
				$node->appendChild($cDataSection);
			}
			elseif (get_class($value) == 'crxml') {
				$currentChildNode = 0;
				if($value->nodeMode === 'append') {
					//$node->appendChild($this->root->node->importNode($value->_getNode()->cloneNode(true),true));
					while($currentChildNodeElement = $value->_getNode()->childNodes->item($currentChildNode)) {
						$node->appendChild($this->root->node->importNode($value->_getNode()->childNodes->item($currentChildNode)->cloneNode(true),true));
						$currentChildNode++;
					}
				} elseif($value->nodeMode === 'replace') {
					if($value->_getNode()) {
						$node->parentNode->replaceChild($this->root->node->importNode($value->_getNode()->childNodes->item(0)->cloneNode(true),true),$node);
					} else {
						$this->setError('Non-existant node specified as source for assignment');
					}
				} else {
					//clear all attributes
					if($this->_getNode()->attributes->length>0) for($i=0;($attributeNode = $this->_getNode()->attributes->item($i));$i++) {
						$node->removeAttribute($attributeNode->nodeName);
					}
					//clear all child nodes
					while ($node->childNodes->length) $node->removeChild($node->firstChild);
					//copy child nodes 
					while($currentChildNodeElement = $value->_getNode()->childNodes->item($currentChildNode)) {
						$node->appendChild($this->root->node->importNode($value->_getNode()->childNodes->item($currentChildNode)->cloneNode(true),true));
						$currentChildNode++;
					}
					//copy attributes
					for($i=0;($attributeNode = $value->_getNode()->attributes->item($i));$i++) {
						$node->setAttribute($attributeNode->nodeName,$attributeNode->nodeValue);
					}
				}

			}
		} else {
			$node->nodeValue=$value;
		}
	}
	function remove()
	{
		if($this->parent) {
			$this->parent->_getNode()->removeChild($this->_getNode());
		}
	}
	function emptyNode()
	{
		$node = $this->_getNode();
		while ($node->childNodes->length) $node->removeChild($node->firstChild);
	}
	function fullNode()
	{
		$temp = new crxml($this->version,$this->encoding);
		if($node = $this->_getNode()) {
			if($node->nodeType==XML_DOCUMENT_NODE) return $this;
			$nodeName = $node->nodeName;
			if(strpos($nodeName,"|")!==false) {
				list($namespaceURI,$localName)=explode('|',$nodeName);
				$nodeName = $nameSpaceURI. "|" . $localName;
				$temp->$nodeName = $this;
			} else if(strpos($nodeName,":")!==false) {
				list($prefix,$localName)=explode(':',$nodeName);
				$nameSpaceURI=$this->_getNode()->namespaceURI;
				$nodeName = $prefix. ":" . $localName;
				$temp->addNameSpaceDefNode($prefix,$localName,$nameSpaceURI);
				$temp->$nodeName = $this;
			} else{
				$temp->$nodeName = $this;
			}
			for($i=0;($attributeNode = $this->_getNode()->attributes->item($i));$i++) {
				$temp->$nodeName->offsetSet($attributeNode->nodeName, $attributeNode->nodeValue);
			}
			return $temp;
		} else {
		if($this->offset > 0) $offsetPart = "[{$this->offset}]";else $offsetPart = '';
		$this->setError("Non-existant node `{$this->nodeName}{$offsetPart}` node given in parameter.");
		}
	}
	//Iterator Interface
	function current()
	{
		$childNodes=$this->_getChildrenForNode($this->_getNode(),'*');
		if($node=$childNodes->item($this->current)) {
			$return=clone $this;
			$return->node=$node;
			$return->offset=0;
			return $return;
		}
	}
	function key()
	{
		$childNodes=$this->_getChildrenForNode($this->_getNode(),'*');
		if($node=$childNodes->item($this->current)) {
			return $node->nodeName;
		} else {
			return false;
		}
	}
	function next()
	{
		$this->current++;
	}
	function rewind()
	{
		$this->current=0;
	}
	function valid()
	{
		$childNodes=$this->_getChildrenForNode($this->_getNode(),'*');
		if($node=$childNodes->item($this->current)) {
			return true;
		} else {
			return false;
		}
	}
	private function findWayToTop($node,$topNode=null)
	{
		while($node->parentNode) {
			if(!is_null($topNode)) {
				if($topNode->isSameNode($node)) break;
			}
			$offset = 0;
			$temp = $node;
			while($sibling = $temp->previousSibling) {
				if($sibling->nodeName == $node->nodeName) {
					if($sibling->namespaceURI === $node->namespaceURI) {
						$offset++;
					}
				}
				$temp = $sibling;
			}
			if($offset > 0 ) $offsetString = "[$offset]";else $offsetString = '';
			if(strlen($node->prefix)) {
			$path[] = "{'".$node->prefix.':'.$node->localName."'}$offsetString";
			} elseif(strlen($node->namespaceURI)) {
				if($prefix= $this->root->node->lookupPrefix($node->namespaceURI)) {
					$path[] = "{'$prefix:".$node->localName."'}$offsetString";
				}
				else {
					$path[] = "{'".$node->namespaceURI.'|'.$node->localName."'}$offsetString";
				}
			} else {
				$temp = $node->nodeName;
				if(preg_match("#[^a-zA-Z0-9_]#",$temp)) $temp= "{'$temp'}";
				$path[] = $temp.$offsetString;
			}
			$node->nodeName;
			$node = $node->parentNode;
		}
		return array_reverse($path);
	}

	function search($nodeName,$relative = false)
	{
		$return = array();
		if($thisNode = $node = $this->_getNode()) {
			if(strpos($nodeName,":")!==false) {
				list($prefix,$localName)=explode(':',$nodeName);
				if($nameSpaceURI = $this->root->node->lookupNamespaceURI($prefix)) {
					$nodeList = $node->getElementsByTagNameNS($nameSpaceURI,$localName);
					$nlList[] = $nodeList;
				}
			}
			else {
				$nodeList = $node->getElementsByTagName($nodeName);
				$nlList[] = $nodeList;
			}
			foreach($nlList as $v) {
				foreach($v as $node) {
					if($relative) $path = $this->findWayToTop($node,$thisNode); else $path = $this->findWayToTop($node);
					$nodeContent = $this->root->_getNode()->saveXML($node);
					$htmlEncoded = htmlentities($nodeContent);
					$return[] = array('nodeName'=>$node->nodeName,'accessStatement'=>'...->'.join('->',$path),'nodeChildrenCount'=>$node->childNodes->length,'nodeType'=>$node->nodeType,'namespaceURI'=>$node->namespaceURI,'nodeContent'=>$nodeContent,'htmlEncodedNodeContent'=>$htmlEncoded);
				}
			}
		}
		return $return;
	}
	function dump($nodeName='*',$relative=false)
	{
		echo "<pre>".print_r($this->search($nodeName,$relative),true)."</pre>";
	}
	/*
	function dump($node=null,$level = 0)
	{
	if(!$node) $node = $this->root->node;
	foreach($node->childNodes as $k=>$v) {
		echo str_repeat("&nbsp ",$level);
		echo $v->nodeName;
		if($v->nodeType == XML_TEXT_NODE || $v->nodeType == XML_CDATA_SECTION_NODE ) echo "=>".$v->nodeValue;
		echo "\n<br/>\n";
		if(isset($v->childNodes)) if($v->childNodes->length > 0) {
			$this->dump($v,$level+1);
			}
		}
	}*/


}


