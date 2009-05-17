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
 * rss2 Class: Create RSS 2.0 feeds.
 *
 * @author Adrien WARTEL (adrien.wartel [at] gmail [dot] com)
 */

class rss2 {

	private $XmlFile;
	private $XmlObj;

	private $title;
	private $link;
	private $description;

	public function __construct($XmlFile='') {

		if ( empty($XmlFile) )
			return false;

		$this->XmlFile = $XmlFile;
		$this->XmlObj = new DOMDocument('1.0', 'UTF-8');

		$this->createXmlFile();

	}

	public function createXmlFile() {

		// Creating root node <rss version="2.0"></racine>
		$rssNode = $this->XmlObj->createElement('rss');
		$rssNode->setAttribute('version', '2.0');
		$this->XmlObj->appendChild($rssNode);

		// Adding channel node
		$channelNode = $this->XmlObj->createElement('channel');
		$rssNode->appendChild($channelNode);

	}

	public function addInformations ( $infos = array() ){
		// Validating incoming parameters
		if( count($infos) != 3 ) {
			return false;
		}

		// Allocation of each parameter if correct
		foreach( $infos as $parameter=>$value ) {
			if( !$parameter || !$value ) {
				return false;
			}
			$this->{$parameter}=$value;
		}

		$channelNode = $this->getChannelNode();

		// Creating title, link and description nodes
		$titleNode = $this->XmlObj->createElement('title', $this->title);
		$linkNode = $this->XmlObj->createElement('link', $this->link);
		$descriptionNode = $this->XmlObj->createElement('description', $this->description);

		$channelNode->appendChild($titleNode);
		$channelNode->appendChild($linkNode);
		$channelNode->appendChild($descriptionNode);
	}

	public function addItem ( $item = array() ) {

		$channelNode = $this->getChannelNode();

		$itemNode = $this->XmlObj->createElement('item');
		$itemNode = $channelNode->appendChild($itemNode);

		// Creating node:  <title>
		$titleNode = $this->XmlObj->createElement('title',$item['title']);
		$titleNode = $itemNode->appendChild($titleNode);

		// Creating node:  <link>
		$linkNode = $this->XmlObj->createElement('link',$item['link']);
		$linkNode = $itemNode->appendChild($linkNode);;

		// Creating node:  <pubdate>
		$dateNode = $this->XmlObj->createELement('pubdate',$item['date']);
		$dateNode = $itemNode->appendChild($dateNode);

		// Creating node:  <description>
		$descriptionNode = $this->XmlObj->createElement('description',$item['description']);
		$descriptionNode = $itemNode->appendChild($descriptionNode);

		// Creating node:  <author>
		$authorNode = $this->XmlObj->createElement('author',$item['author']);
		$authorNode = $itemNode->appendChild($authorNode);

	}

	public function saveXmlFile() {
		$this->XmlObj->save( $this->XmlFile );
	}

	public function echoXml() {
		return $this->XmlObj->saveXML();
	}

	private function getChannelNode () {
		return $this->XmlObj->getElementsByTagName("channel")->item(0);
	}



}

?>
