<?php
/*
*    This program is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * XSLTPage Class: generate an HTML page from XSLT and XML
 */

class XSLTPage {

	private $XslObj;
	private $XmlObj;

	public function __construct($XslFile='') {

		// Initialization
		$this->XslObj = new DOMDocument('1.0', 'UTF-8');
		$this->XslObj->load($XslFile);

		$this->XmlObj = new DOMDocument('1.0', 'UTF-8');

		$this->createXmlFile();
	}

	public function createXmlFile() {

		// Creating root node
		$pageNode = $this->XmlObj->createElement('page');
		$this->XmlObj->appendChild($pageNode);

		// Creating config node
		$configNode = $this->XmlObj->createElement('config');
		$pageNode->appendChild($configNode);

	}
	
	// Render the page
	public function render() {

		// Debug only - This will show the XML document sent to XSL *before* processing.	
		//  echo $this->XmlObj->saveXML(); exit;

		$proc = new XSLTProcessor();
		$proc->importStyleSheet($this->XslObj); 
		echo $proc->transformToXml($this->XmlObj);

	}


	// Add an XML fragment to the /page node
	public function addToXml( $XMLFragment=null ) {
		
		if( !isset($XMLFragment) || $XMLFragment==null )
			return false;
		
		$tempNode = $this->XmlObj->importNode($XMLFragment, true);
		$this->XmlObj->firstChild->appendChild($tempNode);
	
		return true;
	
	}

	// Add an child node to the /page/config node
	public function addConfToXml($name='', $value='') {
	
		if( empty($name) )
			return false;

		$configNode = $this->XmlObj->getElementsByTagName("config")->item(0);
		$node = $this->XmlObj->createElement($name, $value);
		$configNode->appendChild($node);	

		return true;
	}

}
?>
