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
 * This file provides authentication for the gallery
 */

session_start();

define('ROOT_PATH', dirname( __FILE__ ).'/../' );
define('LIB_DIR', ROOT_PATH.'lib/');
define('CONFIG_FILE', ROOT_PATH.'conf/config.ini.php' );

if( isset($_POST['password']) && !empty($_POST['password']) ) {
	include(LIB_DIR.'class.iniConfig.php');
	$conf = new iniConfig( CONFIG_FILE );
	if( $_POST['password'] == $conf->galleryPassword ) {
		$_SESSION['logged_in'] = true;
	} else {
		$_SESSION = Array();
	}
}

header("Location: index.php");
exit();
?>
