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
 * simpleViewer Class: generate the XML configuration file for a single SimpleViewer gallery.
 *
 * Works with SimpleViewer 1.8
 */

class simpleViewer {

	private $XmlFile="gallery.xml";
	private $XmlObj;
	private $albumName;
	private $pathToImages;
	private $pathToCache;
	private $XMLDirPath="cache/";

	// Default values for simpleviewer
	private $maxImageWidth="480";
	private $maxImageHeight="480";
	private $textColor="0x5A5A5A";
	private $frameColor="0xffffff";
	private $frameWidth="10";
	private $stagePadding="30";
	private $thumbnailColumns="4";
	private $thumbnailRows="3";
	private $navPosition="left";
	private $title="SimpleViewer Title";
	private $enableRightClickOpen="true";
	private $backgroundImagePath="";
	private $imagePath="images/";
	private $thumbPath="thumbs/";

	public function __construct($albumName='', $pathToImages='images/', $pathToCache='cache/', $urlToImages, $urlToCache ) {

		if ( empty($albumName) )
			return false;

		$this->albumName = $this->removeaccents($albumName);			
		$this->pathToImages = $pathToImages;
		$this->pathToCache = $pathToCache;
		$this->imageUrl = $urlToImages;
		$this->thumbUrl = $urlToCache;
		$this->imagePath = $pathToImages.$this->albumName.'/';
		$this->thumbPath = $pathToCache.$this->albumName.'/';

		// Create thumb directory if needed.
		if( !file_exists($pathToCache.$this->albumName) )
			mkdir($pathToCache.$this->albumName);

		// Rename album directory if name contains accents
		if( $this->imagePath != $pathToImages.$albumName.'/' )
			rename($pathToImages.$albumName.'/', $this->imagePath); 

		$this->XmlObj = new DOMDocument('1.0', 'UTF-8');

		$this->createXmlFile();
		
		return true;
	}

	public function createXmlFile() {

		// Creating root node
		$simpleviewerGalleryNode = $this->XmlObj->createElement('simpleviewerGallery');
		$this->XmlObj->appendChild($simpleviewerGalleryNode);

	}

	public function addInformations ( $infos = array() ){

		// Allocation of each parameter if correct
		foreach( $infos as $parameter=>$value ) {
			if( isset($this->{$parameter}) and isset($value) and !empty($value) ) {
				$this->{$parameter} = $value;
			}
		}

		$simpleviewerGalleryNode = $this->getSimpleviewerGalleryNode();
	
		// Adding information
		$simpleviewerGalleryNode->setAttribute('maxImageWidth' , $this->maxImageWidth );
		$simpleviewerGalleryNode->setAttribute('maxImageHeight' , $this->maxImageHeight );
		$simpleviewerGalleryNode->setAttribute('textColor' , $this->textColor );
		$simpleviewerGalleryNode->setAttribute('frameColor' , $this->frameColor );
		$simpleviewerGalleryNode->setAttribute('frameWidth' , $this->frameWidth );
		$simpleviewerGalleryNode->setAttribute('stagePadding' , $this->stagePadding );
		$simpleviewerGalleryNode->setAttribute('thumbnailColumns' , $this->thumbnailColumns );
		$simpleviewerGalleryNode->setAttribute('thumbnailRows' , $this->thumbnailRows );
		$simpleviewerGalleryNode->setAttribute('navPosition' , $this->navPosition );
		$simpleviewerGalleryNode->setAttribute('title' , $this->title );
		$simpleviewerGalleryNode->setAttribute('enableRightClickOpen' , $this->enableRightClickOpen );
		$simpleviewerGalleryNode->setAttribute('backgroundImagePath' , $this->backgroundImagePath );
		$simpleviewerGalleryNode->setAttribute('imagePath' , $this->imageUrl );
		$simpleviewerGalleryNode->setAttribute('thumbPath' , $this->thumbUrl );

	}

	private function addImage( $image ) {

		$simpleviewerGalleryNodeNode = $this->getSimpleviewerGalleryNode();
	
		// Creating <image> node
		$imageNode = $this->XmlObj->createElement('image');
		$simpleviewerGalleryNodeNode->appendChild($imageNode);

		// Creating <filename> node
		$filenameNode = $this->XmlObj->createElement('filename',$image);
		$imageNode->appendChild($filenameNode);

		// Creating <caption> node
		$captionNode = $this->XmlObj->createElement('caption', eregi_replace('(.*)\.[a-zA-Z]+$','\\1',$image));
		$imageNode->appendChild($captionNode);

	}

	public function saveXmlFile() {
		$this->XmlFile = $this->albumName.".xml";
		$this->XmlObj->save( $this->XMLDirPath.$this->XmlFile );
	}

	public function echoXml() {
		return $this->XmlObj->saveXML();
	}

	private function getSimpleviewerGalleryNode () {
		return $this->XmlObj->getElementsByTagName("simpleviewerGallery")->item(0);
	}

	public function process() {
	
		if(!empty($this->imagePath)) {
			#$dir = opendir($this->imagePath);			
			#while( false !== ($f = readdir($dir)) ) {
			$files = scandir($this->imagePath);
			
			foreach($files as $i => $f) {
			
				$system = explode('.',$f); 
				if(is_file($this->imagePath.$f) && preg_match('/jpg|jpeg|JPG|JPEG/',$system[sizeof($system) - 1])) { 
				// SimpleViewer 1.8 only support jpeg images.		

					// Rename image if name contains accents
					if( $this->imagePath.$f != $this->removeaccents($this->imagePath.$f) ) {
						rename($this->imagePath.$f, $this->removeaccents($this->imagePath.$f));
						$f = $this->removeaccents($f);
					} 

					if(!is_file($this->thumbPath.$f))
						$this->resizeImage($this->imagePath.$f, $this->thumbPath.$f, 85, 85);
					$this->addImage( $f );
				}
			}
		}
	}
	
	private function resizeImage($name,$filename,$new_w,$new_h){
		
		$system = explode('.',$name);
		if (preg_match('/jpg|jpeg|JPG|JPEG/',$system[sizeof($system) - 1])){
			$src_img = imagecreatefromjpeg($name);
		}
		if (preg_match('/png|PNG/',$system[sizeof($system) - 1])){
			$src_img = imagecreatefrompng($name);
		}
		
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
		
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	
		if (preg_match("/png/",$system[sizeof($system) - 1])) {
			imagepng($dst_img,$filename); 
		} else {
			imagejpeg($dst_img,$filename); 
		}
		
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
		
	}

	public function getXml() {
	
		$doc = new DOMDocument('1.0', 'UTF-8');
		$frag = $doc->createDocumentFragment();
		$albumNode = $doc->CreateElement( 'simpleviewer_album' );
		$albumNode ->setAttribute('album_name' , $this->albumName );
		$albumNode ->setAttribute('xml_file' , $this->XmlFile );
		$frag->appendChild( $albumNode );

		return $frag;

	}
	
	
	public function removeaccents($string) {  
		$string2 = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
			"Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
			"Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
			"Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
			"Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
			"Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
			"Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
			"Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
			"à" => "a", "á" => "a", "â" => "a", "ã" => "a",
			"ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
			"è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
			"ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
			"ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
			"ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
			"ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
			"ý" => "y", "ÿ" => "y", "'" => "");
		return strtr("$string", $string2);
	}

}

?>
