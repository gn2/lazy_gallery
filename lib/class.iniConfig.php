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

/*
 * Configuration part for this file
 */
	$RootPath =  dirname( __FILE__ ).'/../';
	
	global $iniConfigConf;
	$iniConfigConf = array();
	$iniConfigConf['displayErrors'] = true;
	//$iniConfigConf['logFile'] = "error.xml";
/*
 * End of configuration part
 */


/**
 * iniConfig Class: Allow to load and modify ini files. 
 * 
 * @author Adrien WARTEL (adrien.wartel [at] gmail [dot] com)
  * Shortened version: no support for logging
 */

class iniConfig {
     
     private $iniValues = array();
     private $fileName= '';
     private $error = '';
 
     // Chargement du fichier de configuration
     public function __construct( $ini_file ) {

		try {
			if( isset( $ini_file ) && !empty( $ini_file ) ) {
       			$this->fileName = $ini_file;
				$this->open();		
			} else {
				throw new Exception( "(iniConfig->iniConfig) Filename is not set." );
			}
		} catch( Exception $e ){
			$this->setError( $e->getMessage() );
			return false;
		}
         
     }
     
	private function open() {

		try {
			if( !file_exists( $this->fileName ) ) {
				throw new Exception( "(iniConfig->open) File '.$this->fileName.' does not exist." );
			}
			if( $this->iniValues = parse_ini_file($this->fileName) ) {
				return true;
			}
			throw new Exception( "(iniConfig->open) Error while opening file: ".$this->fileName );
		} catch( Exception $e ) {
			$this->setError( $e->getMessage() );
			return false;
		}

	}
     
	/* 
	public function __destruct() {
	$this->writeConfig();
	}
	//*/
    
	// The file is updated after every change
	private function writeConfig() {
		try {
			if( $this->iniValues == array() ) {
				throw new Exception( "(iniConfig->writeConfig) File '.$this->fileName.' is not correctly loaded (or empty)." );
			}
			
			$config = ';<?php exit(\'-)\') ?>';
	   	     	$config .= "\n".'; Last update : '.date('d.m.Y H:i:s')."\n";

	       		foreach ($this->iniValues AS $key => $value) {
	        		if( ($value == 'true') || ($value == 'false') ) {
		            	$config .= "\n$key = $value";
	         	} else {
	         		$config .= "\n$key = \"$value\"";
	         	}
	        }
			if( file_put_contents($this->fileName, $config) ) {
				return true;
			}
			throw new Exception( "(iniConfig->writeConfig) Error while saving file: ".$this->fileName );
		} catch( Exception $e ) {
			$this->setError( $e->getMessage() );
			return false;
		}

     }
     
     // Modifying a value
     public function __set($key, $value) {
         $this->iniValues[$key] = trim($value);
         $this->writeConfig();
     }
     
     // Reading a value 
     public function __get($key) {
         return $this->iniValues[$key];
     }
     
	public function setError( $errorMessage = '' ) {
		
		$this->error = $errorMessage;

		global $iniConfigConf;
		/** Shortened version: no support for logging
		global $RootPath;
		include_once( $RootPath.'lib/class.logging.php' );

		$myLog = new logging( $iniConfigConf['logFile'] );
		$myLog->append( array( 'file'=>__FILE__, 'error'=>$this->error ) );
		$myLog->save();
		*/
		
		if( $iniConfigConf['displayErrors'] ) {
			echo "<strong>[iniConfigError] :: </strong>".$this->error.'<br />';
		}

	}

	public function getError() {
		
		if( $this->error != '' ) {
			return $this->error;
		} else {
			return false;
		}
		
	}
	
	public function getFileName() {
		
		return $this->fileName;
		
	}
 }
?>
