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
 * This file renders pictures using $_GET['picture'], $_GET['album'], and $_GET['cache'] parameters. 
 */

session_start();

define('ROOT_PATH', dirname( __FILE__ ).'/../' );
define('LIB_DIR', ROOT_PATH.'lib/');
define('ALBUMS_DIR', ROOT_PATH.'albums/');
define('CACHE_DIR', ROOT_PATH.'cache/');
define('CONFIG_FILE', ROOT_PATH.'conf/config.ini.php' );

include(LIB_DIR.'class.iniConfig.php');

# Loading configuration
$conf = new iniConfig( CONFIG_FILE );

# If the gallery is password protected and no session is active,
# then probably someone is trying to access the pictures directly...
if( $conf->galleryProtection ) 
  if (!$_SESSION['logged_in'])
    exit();

# Check for required parameters
if( !isset($_GET['picture']) or empty($_GET['picture']) or  # Picture name 
 !isset($_GET['album']) or empty($_GET['album']) or	# Album name
 !isset($_GET['cache']) or empty($_GET['cache']) ) { 	# Whether we want to access the picture in cache (thumbnail)) 
  echo 'Incorrect parameters.';
  exit();
 }

# Get picture and album name, and prevent someone to explore your filesystem
$pictureName = str_replace("/", "", urldecode($_GET['picture']));
$albumName = str_replace("/", "", urldecode($_GET['album']));

# Create full path name of the picture
if( $_GET['cache'] == 'true') {
  $picture = CACHE_DIR.'/'.$albumName.'/'.$pictureName;
} else {  
  $picture = ALBUMS_DIR.'/'.$albumName.'/'.$pictureName;
  $picture = ALBUMS_DIR.'/'.$albumName.'/'.$pictureName;
}

# Check that file exists...
if( !file_exists($picture) ) {
  echo 'File doesn\'t exists.';
  exit();
}

# Set headers...
header("Cache-Control: public, must-revalidate");
header("Pragma: hack"); 
header("Content-Type: application/octet-stream");
header("Content-Length: " .(string)(filesize($picture)) );
header('Content-Disposition: attachment; filename="'.$pictureName.'"');
header("Content-Transfer-Encoding: binary\n");

# Send picture data
readfile($picture);

?>
