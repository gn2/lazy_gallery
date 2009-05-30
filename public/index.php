<?php
/*
*	This program is free software; you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation; either version 2 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program; if not, write to the Free Software
*	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * This file renders all pages + RSS feed. 
 */

session_start();

define('ROOT_PATH', dirname( __FILE__ ).'/../' );
define('LIB_DIR', ROOT_PATH.'lib/');
define('VIEWS_DIR', ROOT_PATH.'views/');
define('ALBUMS_DIR', ROOT_PATH.'albums/');
define('CACHE_DIR', ROOT_PATH.'cache/');
define('PUBLIC_CACHE_DIR', ROOT_PATH.'public/cache/');
define('CONFIG_FILE', ROOT_PATH.'conf/config.ini.php' );

include(LIB_DIR.'class.iniConfig.php');
include(LIB_DIR.'class.rss2.php');
include(LIB_DIR.'class.XSLTPage.php');
include(LIB_DIR.'class.albums.php');
include(LIB_DIR.'class.simpleviewer.php');

$conf = new iniConfig( CONFIG_FILE );


// Rss feed requested
if( isset($_GET['rss']) && $conf->rssSupport ) {

	$RSS = new rss2('rss.xml');
	$RSS->addInformations( 
				array( 'title'=> $conf->rssTitle, 
					'link'=> $conf->rssSitePath, 
					'description'=> $conf->rssDescription
				 ) );

	$albums = new albums( ALBUMS_DIR, CACHE_DIR );
	$albumsArray = $albums->getAlbumsAsArray();

	foreach ($albumsArray as $key => $row) {
		// Add Rss item
		$RSS->addItem( 
			array(	'title'=> $row['dirname'],
				'link'=> $conf->rssSitePath.'/index.php?album='.$row['dirname'],
				'date'=> date("d.m.Y H:i:s", $row['date']),
				'author'=> $conf->rssAuthor,
				'description'=>  $row['count'].' '.$conf->picturesInYourLanguage.'.'
			) );
	}

	echo $RSS->echoXML();
	exit();
}

# If the gallery is password protected and the password has not been entered yet, then a form is displayed
if( $conf->galleryProtection && !$_SESSION['logged_in'] ) {
	$page = new XSLTPage( VIEWS_DIR.'login.xsl' );
	$page->addConfToXml( 'pagetitle', $conf->pageTitle );
	$page->addConfToXml( 'password', $conf->passwordInYourLanguage );
	$page->render();
	exit();
} 


$page = new XSLTPage( VIEWS_DIR.'main.xsl' );

$page->addConfToXml( 'pagetitle', $conf->pageTitle );
$page->addConfToXml( 'swfdir', 'simpleviewer/' );
$page->addConfToXml( 'cachedir', 'cache/' );
$page->addConfToXml( 'rsssupport', $conf->rssSupport );
$page->addConfToXml( 'pictures', $conf->picturesInYourLanguage );

// Check if directories cache, albums and public/cache are writable
if( !is_writable(ALBUMS_DIR) or !is_writable(CACHE_DIR) or !is_writable(PUBLIC_CACHE_DIR) )
  $page->addConfToXml( 'somedirnotwritable', 'true' );

// If we display only one album
if( isset($_GET['album']) && !empty($_GET['album']) && albums::is_a_valid_album(ALBUMS_DIR, $_GET['album']) ) {

	// Check if directories cache, albums and public/cache are writable. Send on error message if not.
	if( !is_writable(ALBUMS_DIR) or !is_writable(CACHE_DIR) or !is_writable(PUBLIC_CACHE_DIR) ) {
		$page->addConfToXml( 'somedirnotwritable', 'true' );
	
	} else {

		$sv = new simpleViewer( $_GET['album'], ALBUMS_DIR, CACHE_DIR, 'image.php?cache=false&album='.$_GET['album'].'&picture=', 'image.php?cache=true&album='.$_GET['album'].'&picture=');
		$sv->addInformations(array(	'title'=> $_GET['album'],
					'maxImageWidth' => $conf->maxImageWidth,
					'maxImageHeight' =>  $conf->maxImageHeight,
					'textColor' =>  $conf->textColor,
					'frameColor' =>  $conf->frameColor,
					'frameWidth' =>  $conf->frameWidth,
					'stagePadding' =>  $conf->stagePadding,
					'thumbnailColumns' =>  $conf->thumbnailColumns,
					'thumbnailRows' =>  $conf->thumbnailRows,
					'navPosition' =>  $conf->navPosition,
					'enableRightClickOpen' =>  $conf->enableRightClickOpen,
					'backgroundImagePath' =>  $conf->backgroundImagePath
				));
		$sv->process();
		$sv->saveXmlFile();
		$page->addToXml( $sv->getXml() );
	}

// Otherwise, the album list is rendered. 
} else {

	$currentPage = 1;
	if( isset($_GET['page']) and !empty($_GET['page']) and is_numeric($_GET['page']) and $_GET['page']>0 ) {
		$currentPage = $_GET['page'];
	}

	$albums = new albums( ALBUMS_DIR, CACHE_DIR );
	$albums->generateXMl($currentPage, $conf->albumsPerPage);
	$page->addToXml( $albums->getXml() );

	// Add info for page numbering
	$page->addConfToXml( 'totalPages', ceil($albums->getAlbumsCount() / $conf->albumsPerPage) );
	$page->addConfToXml( 'currentPage', $currentPage );

}

$page->render();

?>
