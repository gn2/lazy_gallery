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
 * albums Class: Manage albums for SimpleViewer. Scan albums dir and generate XML for XSL file. 
 *
 * @author Adrien WARTEL (adrien.wartel [at] gmail [dot] com)
 */

class albums {

	private $XmlObj;
	private $XmlFrag;
	private $XmlAlbumsNode;
	private $path2Albums;
	private $path2Cache;
	
	public function __construct($path2Albums, $path2Cache) {

		if ( empty($path2Albums) || !is_dir($path2Albums) || empty($path2Cache) || !is_dir($path2Cache))
			return false;

		$this->path2Albums = $path2Albums;
		$this->path2Cache = $path2Cache;
		
		// Initialization
		$this->XmlObj = new DOMDocument('1.0', 'UTF-8');
		$this->XmlFrag = $this->XmlObj->createDocumentFragment();
		$this->createXmlFile();
	}

	public function createXmlFile() {

		// Creating root node
		$this->XmlAlbumsNode = $this->XmlObj->createElement('albums');
		$this->XmlFrag->appendChild($this->XmlAlbumsNode);

	}
	
	private function addAlbum($albumName='', $albumCount='') {

		if(!empty($albumName)) {
		
			$albumNode = $this->XmlObj->createElement('album', $albumName);
			$thumbnail = '';

			// Fill $files with all cache files available			
			if( is_dir($this->path2Cache.'/'.$albumName) ) {

				$files = array();
				$dir = dir($this->path2Cache.'/'.$albumName);

				while (false !== ( $entry = $dir->read() ) ) {
					$system = explode('.',$entry); 
					if(is_file($this->path2Cache.'/'.$albumName.'/'.$entry) && preg_match('/jpg|jpeg|JPG|JPEG/',$system[sizeof($system) - 1])) { 
						#$files[] = $this->path2Cache.'/'.$albumName.'/'.$entry;
						$files[] = $entry;
					}
				}
				$dir->close();

				// Set a random thumbnail
				if( sizeof($files) > 0 ) {
					$thumbnail = $files[array_rand($files, 1)];
				}

			}

			$albumNode->setAttribute('thumbnail', $thumbnail );
			$albumNode->setAttribute('count', $albumCount.' ' );
			$this->XmlAlbumsNode->appendChild($albumNode);	
	
		}
	
	}
	
	public function generateXML( $page = 1, $albumsPerPage = 5) {

		if(!empty($this->path2Albums)) {

			$albums = $this->getAlbumsAsArray();

			// Filtering albums per page
			if( is_numeric($page) and is_numeric($albumsPerPage) ) {
				// If page number is too big, it is set back to 1.
				if( ($albumsPerPage * ($page-1)) > sizeof($albums) )
					$page = 1;

				$albums = array_slice($albums, ($albumsPerPage * ($page-1)), $albumsPerPage);
			}
			
			// Adding albums to XML
			foreach ($albums as $key => $row) {
			    $this->addAlbum(  $row['dirname'], $row['count'] );
			}
		}
	}

	public function is_a_valid_album( $albumPath, $albumName='' ) {
		
		# Prevent a smart ass to explore your filessytem
		$albumName = str_replace("/", "", $albumName);

		if ( !empty($albumName) && file_exists($albumPath.$albumName) )
			return true;

		return false;

	}	
		
	public function getXml() {
		
		return $this->XmlFrag;
		
	}

	public function getAlbumsAsArray() {
	
		$albums = array();
		$dir = opendir($this->path2Albums);	

		// Adding albums to array
		while ($f = readdir($dir)) {
			if(is_dir($this->path2Albums.'/'.$f) && $f[0] != '.' ) {
			
				$count = 0;
				$dir2 = opendir($this->path2Albums.'/'.$f.'/');

				// Adding the number of pictures for each album
				while ($f2 = readdir($dir2)) { 
					$system = explode('.',$f2);
					if(is_file($this->path2Albums.'/'.$f.'/'.$f2) && preg_match('/jpg|jpeg|JPG|JPEG/',$system[sizeof($system) - 1]) ) {
						$count++;
					}
				}

				$albums[] = array('dirname' => $f, 'date' => filemtime($this->path2Albums.'/'.$f.'/'), 'count' => $count );

				closedir($dir2);
			}
		}

		closedir($dir);

		// Sorting directories according to their modification date
		if( sizeof($albums) > 1 ) {
			foreach ($albums as $key => $row) {
			    $dirname[$key]  = $row['dirname'];
			    $date[$key] = $row['date'];
			}
			array_multisort($date, SORT_DESC, $dirname, SORT_ASC, $albums);
		}
		return $albums;

	
	}

	public function getAlbumsCount() {
	
		return sizeof($this->getAlbumsAsArray());
	
	}

}

?>
