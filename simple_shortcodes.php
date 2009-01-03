<?php
/*
Plugin Name: Simple Shortcodes
Plugin URI: http://michael-baker.com/simple-shortcodes/
Description: An easy to use mechanism for creating and using custom shortcodes.
Author: Michael Baker
Version: 0.2
Author URI: http://michael-baker.com/
Generated At: www.wp-fun.co.uk;
*/ 

/*  Copyright 2008  Michael Baker  (email : mbaker@pobox.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("SSVALUEWIDTH_DEFAULT", "45");
define("SSVALUEHEIGHT_DEFAULT","1");
define("DISPLAYMARKUP_DEFAULT","no");
define("YES_DEFAULT",          "yes");
define("NO_DEFAULT",           "no");


if (!class_exists('MB_SimpleShortcodes')) {
	class MB_SimpleShortcodes   {

		/**
		* @var array   The shortcodes.
		*/
		var $shortcode_pairs = array();

		/**
		* @var array   Options for this plugin.
		*/
		var $adminOptions = array();

		/**
		* @var string   The name the options are saved under in the database.
		*/
		var $adminOptionsName = "MB_SimpleShortcodes_options";		
		
		/**
		* @var string   The name of the database table used by the plugin
		*/  
		var $db_table_name = 'simple_shortcodes';

		/**
		* @var string   The name of the page used for configuring the plugin
		*/
		var $configurePage = "options-general.php";
		
		/**
		* @var string   The name of the file used for shortcode handler scripts
		*/
		var $handler_filename = '/MB_SS_Handler.php';

		/**
		* PHP 4 Compatible Constructor
		*/
		function MB_SimpleShortcodes(){$this->__construct();}
		
		/**
		* PHP 5 Constructor
		*/      
		function __construct(){
			global $wpdb;

			add_action("admin_menu", array(&$this,"add_admin_pages"));
			register_activation_hook(__FILE__,array(&$this,"install_on_activation"));

			$this->adminOptions = $this->getAdminOptions();
			$this->validateAdminOptions();
		
			//*****************************************************************************************
			// These lines allow the plugin to be translated into different languages
			// You will need to create the appropriate language files
			// this assumes your language files will be in the format: simple_shortcodes-locationcode.mo
			// This also assumes your text domain will be: simple_shortcodes 
			// For more info: http://codex.wordpress.org/Translating_WordPress
			//*****************************************************************************************
			$simple_shortcodes_locale = get_locale();
			$simple_shortcodes_mofile = dirname(__FILE__) . "/languages/simple_shortcodes-".$simple_shortcodes_locale.".mo";
			load_textdomain("simple_shortcodes", $simple_shortcodes_mofile);
		
			$this->db_table_name = $wpdb->prefix . "simple_shortcodes";
			
			$this->handler_filename = dirname(__FILE__).$this->handler_filename;

			// on initial activation table is not created before getShortcodes is called, so check table exists
			if ($this->table_exists($this->db_table_name)) {
				$this->shortcode_pairs = $this->getShortcodes();
			}

		}
		
		
		
		/**
		 *
		 * @param string $table_name Database table name.
		 * @return bool If table exists.
		 *
		 * modified from wp-admin/includes/upgrade.php maybe_create_table()
		 */
		function table_exists($table_name) {
			global $wpdb;
			foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
				if ($table == $table_name) {
					return true;
				}
			}
			return false;
		}
		
		
		/**
		* Returns the Handler Filename
		* @return string
		*/
		function getHandlerFilename() {
			return $this->handler_filename;
		}
		
		/**
		* Retrieves the shortcodes from the database.
		* @return array
		*/
		function getShortcodes() {
			$shortcodes = array();
			$sql = "SELECT name, value 
			FROM   ".$this->db_table_name."
			WHERE  1";
	
			$result = mysql_query($sql);
			
			if (!$result) {
				echo "Could not successfully run query ($sql) from ".$this->db_table_name.": " . mysql_error();
				exit;
			}
			
			if (mysql_num_rows($result) != 0) {
				// While a row of data exists, put that row in $row as an associative array
				// Note: If you're expecting just one row, no need to use a loop
				// Note: If you put extract($row); inside the following loop, you'll
				//       then create $userid, $fullname, and $userstatus
				while ($row = mysql_fetch_assoc($result)) {
					$shortcodes[$row["name"]] = $row["value"];
				}
			}
			
			
			mysql_free_result($result);
	
			return $shortcodes;
		}
		
		/**
		* Insert (or replace) shortcode into the database.
		* @param string $name    name of shortcode
		* @param string $value   value of shortcode
		*/
		function putShortcode($name, $value) {
			// Use REPLACE instead of INSERT incase the shortcode name is already in the database
			$sql = "REPLACE INTO ".$this->db_table_name." VALUES('".$name."','".$value."')";
			
			$result = mysql_query($sql);
	
			if (!$result) {
				echo "Could not successfully run query ($sql) on ".$this->db_table_name.": " . mysql_error();
				exit;
			}
		}
		
		/**
		* Delete shortcode from the database.
		* @param string $name    name of shortcode
		*/
		function deleteShortcode($name) {
			$sql = "DELETE FROM ".$this->db_table_name." WHERE name='".$name."'";
			
			$result = mysql_query($sql);
	
			if (!$result) {
				echo "Could not successfully run query ($sql) on ".$this->db_table_name.": " . mysql_error();
				exit;
			}
		}
		
		/**
		* Retrieves the options from the database.
		* @return array
		*/
		function getAdminOptions() {
			// setup default values 
			$adminOptions = array("SSValueWidth" => SSVALUEWIDTH_DEFAULT,
				"SSValueHeight" => SSVALUEHEIGHT_DEFAULT,
				"DisplayMarkup" => DISPLAYMARKUP_DEFAULT);
			// get saved values
			$savedOptions = get_option($this->adminOptionsName);
			// if there are any replace the defaults
			if (!empty($savedOptions)) {
				foreach ($savedOptions as $key => $option) {
					$adminOptions[$key] = $option;
				}
			}
			update_option($this->adminOptionsName, $adminOptions);
			return $adminOptions;
		}
		
		/**
		* Validate an integer admin option.  Silently modify if invalid.
		*/
		function validateInteger($key,$option,$min,$max,$default) {
			$valid = FALSE;
			if (is_numeric($option)) {
				// test for integer
				if ( (float)$option == (int)$option ) {
					if ($option >= $min) {
						if ($option <= $max) {
							$valid = TRUE;
						}
					}
				}
			}
			if (!$valid) {
				$this->adminOptions[$key] = $default;
			}
		}
		
		/**
		* Validate a yes or no admin option.  Silently modify if invalid.
		*/
		function validateYesNo($key,$option,$default) {
			$valid = FALSE;
			$new_value = $default;
			if (($option == YES_DEFAULT) or ($option == NO_DEFAULT)) {
				$valid = TRUE;
			} else {
				if (is_numeric($option)) {
					if ($option == 0) {
						$new_value = NO_DEFAULT;
					} else {
						$new_value = YES_DEFAULT;
					}
				} else {
					switch (strtolower($option)) {
					case "yes":
					case "y":
					case "true":
					case "t":
						$new_value = YES_DEFAULT;
						break;
					case "no":
					case "n":
					case "false":
					case "f":
						$new_value = NO_DEFAULT;
						break;
					}
				}
			}
			if (!$valid) {
				$this->adminOptions[$key] = $new_value;
			}
		}

		/**
		* Validate the admin options.  Silently modify if invalid.
		*/
		function validateAdminOptions(){
			foreach ($this->adminOptions as $key => $option) {
				switch ($key) {
				case "SSValueWidth":
					$this->validateInteger($key,$option,1,999,SSVALUEWIDTH_DEFAULT);
					break;
				case "SSValueHeight":
					$this->validateInteger($key,$option,1,999,SSVALUEHEIGHT_DEFAULT);
					break;
				case "DisplayMarkup":
					$this->validateYesNo($key,$option,DISPLAYMARKUP_DEFAULT);
					break;
				default:
					// invalid or no longer used option
					unset($this->adminOptions[$key]);
				}
			}
		}
		
		/**
		* Saves the admin options to the database.
		*/
		function saveAdminOptions(){
			update_option($this->adminOptionsName, $this->adminOptions);
		}
		
		function add_admin_pages(){
			add_submenu_page($this->configurePage, __("Configure Simple Shortcodes"), __("Configure Simple Shortcodes"), 10, __("Configure Simple Shortcodes"), array(&$this,"output_sub_admin_page_0"));
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function output_sub_admin_page_0(){
			if ($_SERVER['REQUEST_METHOD'] != 'POST'){
				$mb_action = $_GET["action"];
				$SS_name = "";
				$SS_value = "";
				$handlers = "";
			} else {
				$mb_action = $_POST["action"];
			}
			$handler_file_ok = TRUE;
			$handlers = "";
			switch ($mb_action) {
			case "Edit":
				$mb_name = $_GET["name"];
				$SS_name = $mb_name;
				$SS_value = $this->shortcode_pairs[$mb_name];
				break;
			case "Delete":
				$mb_name = $_GET["name"];
				$this->deleteShortcode($mb_name);
				unset($this->shortcode_pairs[$mb_name]);
				$handlers = MB_SS_get_handlers($this->shortcode_pairs);
				$handler_file_ok = MB_SS_write_handler_file($this->handler_filename, $handlers);
				break;
			case "Options":
				$this->adminOptions["SSValueWidth"]  = $_POST["SSValueWidth"];
				$this->adminOptions["SSValueHeight"] = $_POST["SSValueHeight"];
				$this->adminOptions["DisplayMarkup"] = $_POST["DisplayMarkup"];
				$this->validateAdminOptions();
				$this->saveAdminOptions();
				break;
			case "Save":
				// new or modified shortcode
				$SS_name = $_POST["SS_name"];
				$SS_value = $_POST["SS_value"];
				$this->putShortcode($SS_name, $SS_value);
				$this->shortcode_pairs[$SS_name] = $SS_value;
				$handlers = MB_SS_get_handlers($this->shortcode_pairs);
				$handler_file_ok = MB_SS_write_handler_file($this->handler_filename, $handlers);
				break;
			}
			?>
			<div class="wrap">
				<h2><?php _e("Configure Simple Shortcodes"); ?></h2>
				<?php if (!$handler_file_ok) { ?>
					<p><strong><?php _e("WARNING"); ?></strong>: 
					<?php _e("Unable to write shortcode handler file"); ?>
					<?php echo("(".$this->handler_filename.")."); ?>
					<?php _e("This may be due to file permissions."); ?>
					<?php _e("You could try changing the file permissions for"); ?>
					<?php echo(basename($this->handler_filename)); ?>
					<?php _e("to 664 or 666 which you can probably do using your ftp client."); ?>
					<?php _e("However there are potential security risks in doing so."); ?>
					<?php _e("It would be safer to make all of your changes then copy the code at the bottom of this page to a text file and upload it using ftp."); ?>
					</p>
					<hr />
				<?php } ?>
				<form action="" method="post" id="SS-conf">
				<table width="100%" summary="<?php _e('Form to add (or edit) a simple shortcode plus list of existing simple shortcodes with links to edit or delete them.') ?>">
				<thead>
				<tr>
				<th align='left'><strong>Shortcode Name</strong></th>
				<th align='left'><strong>Shortcode Value</strong></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<tr>
				<td><input type="text" name="SS_name" size="15" maxlength="255" id="SS_name" value="<?php echo $SS_name; ?>" /></td>
				<?php if ($this->adminOptions["SSValueHeight"] == 1) { ?>
					<td><input type="text" name="SS_value" size="<?php echo $this->adminOptions["SSValueWidth"]; ?>" id="SS_value" value="<?php echo $SS_value; ?>" /></td>
				<?php } else { ?>
					<td><textarea name="SS_value" cols="<?php echo $this->adminOptions["SSValueWidth"]; ?>" rows="<?php echo $this->adminOptions["SSValueHeight"]; ?>" id="SS_value" style="overflow:auto;"><?php echo htmlSpecialChars($SS_value); ?></textarea></td>
				<?php } ?>
				<td><input type="submit" name="save" id="SS_save" value="<?php _e('Save') ?>" /></td>
				<td><input type="hidden" name="action" value="Save" /></td>
				</tr>
				<?php foreach ($this->shortcode_pairs as $SS_name1 => $SS_value1) {
					if ($this->adminOptions["DisplayMarkup"] == YES_DEFAULT) {
						$SS_value1 = htmlSpecialChars($SS_value1);
					}
				?>
					<tr><td><?php echo $SS_name1; ?></td><td><?php echo $SS_value1; ?></td><td><a href="<?php echo $this->configurePage ?>?page=<?php _e('Configure%20Simple%20Shortcodes') ?>&amp;action=Edit&amp;name=<?php echo $SS_name1; ?>"><?php _e('Edit') ?></a></td><td><a href="<?php echo $this->configurePage ?>?page=<?php _e('Configure%20Simple%20Shortcodes') ?>&amp;action=Delete&amp;name=<?php echo $SS_name1; ?>"><?php _e('Delete') ?></a></td></tr>
					<tr>
				<?php } ?>
				</tbody>
				</table>
				</form>
				<hr />
				<h3>Options</h3>
				<form action="" method="post" id="SS-options">
				<table style="width:25em;" summary="<?php _e('Form to set simple shortcode options.') ?>">
				<tr>
				<td><label for="SSValueWidth"><?php _e('Shortcode Value Input Width') ?></label></td>
				<td><input type="text" size="5" maxlength="3" id="SSValueWidth" name="SSValueWidth" value="<?php echo($this->adminOptions['SSValueWidth']) ?>" ></td>
				</tr>
				<tr>
				<td><label for="SSValueHeight"><?php _e('Shortcode Value Input Height') ?></label></td>
				<td><input type="text" size="5" maxlength="3" id="SSValueHeight" name="SSValueHeight" value="<?php echo($this->adminOptions['SSValueHeight']) ?>" ></td>
				</tr>
				<tr>
				<td><label for="DisplayMarkup"><?php _e('Display HTML Markup') ?></label></td>
				<td><input type="text" size="5" maxlength="5" id="DisplayMarkup" name="DisplayMarkup" value="<?php echo($this->adminOptions['DisplayMarkup']) ?>" ></td>
				</tr>
				<tr>
				<td></td><td><input type="hidden" name="action" value="Options" /><input type="submit" name="options" id="SS_options" value="<?php _e('Save') ?>" /></td>
				</tr>
				</table>
				</form>
				<?php if (!$handler_file_ok) { ?>
					<hr />
					<p><?php _e("When you have made all of your changes, copy the following to a text file and use ftp to upload it to:"); ?>
					<br />
					<?php echo($this->handler_filename); ?>
					</p>
					<textarea cols="100" rows="20" style="overflow:auto; font-family:monospace"><?php echo($handlers) ?></textarea></pre>
				<?php } ?>
			</div>
			<?php
		} 
		
		
		
		/**
		* Creates or updates the database table, and adds a database table version number to the WordPress options.
		*/
		function install_on_activation() {
			global $wpdb;
			$plugin_db_version = "0.3";
			$installed_ver = get_option( "simple_shortcodes_db_version" );
			//only run installation if not installed or if previous version installed
			if ($installed_ver === false || $installed_ver != $plugin_db_version) {
				//*****************************************************************************************
				// Create the sql - You will need to edit this to include the columns you need
				// Using the dbdelta function to allow the table to be updated if this is an update.
				// Read the limitations of the dbdelta function here: http://codex.wordpress.org/Creating_Tables_with_Plugins
				// remember to update the version number every time you want to make a change.
				//*****************************************************************************************
				$sql = "CREATE TABLE " . $this->db_table_name . " (
				name VARCHAR(255) NOT NULL  PRIMARY KEY,
				value LONGTEXT
				);";
			
				require_once(ABSPATH . "wp-admin/upgrade-functions.php");
				dbDelta($sql);
				//add an initial shortcode
				$initial_name = "mb_ack";
				$initial_value = "This blog uses <a href='http://michael-baker.com/simple_shortcodes/'>Simple Shortcodes</a> which was initially developed for <a href='http://frickers.co.uk/'>world renowned Marine Artist Gordon Frickers</a>.";
				
				$insert = "INSERT INTO " . $this->db_table_name .
						  " (name, value) " .
						  "VALUES ('" . $wpdb->escape($initial_name) . "','" . $wpdb->escape($initial_value) . "')";
				
				$results = $wpdb->query( $insert );

				//add a database version number for future upgrade purposes
				update_option("simple_shortcodes_db_version", $plugin_db_version);
			}
		}

	}
}

//instantiate the class
if (class_exists('MB_SimpleShortcodes')) {
	$MB_SimpleShortcodes = new MB_SimpleShortcodes();
}

function MB_SS_get_handlers($shortcode_pairs) {
$handlers = "<?php

/****************************************************
 * WARNING                                          *
 *                                                  *
 * This file is written by simple_shortcodes.php    *
 *                                                  *
 * DO NOT EDIT THIS FILE - your edits will be lost! *
 *                                                  *
 ***************************************************/

/*
* Register the shortcodes
*/
if ( function_exists( 'add_shortcode' ) ) {
";
foreach ($shortcode_pairs as $SS_name => $SS_value) {
	$handlers = $handlers."    add_shortcode('".$SS_name."', '".$SS_name."_shortcode');
";
}
$handlers = $handlers."}

";
foreach ($shortcode_pairs as $SS_name => $SS_value) {
	$handlers = $handlers."/**
* ".$SS_name."_shortcode - produces and returns the content to replace the shortcode tag
*
* @param array \$atts       An array of attributes passed from the shortcode [not used]
* @param string \$content   If the shortcode wraps round some html, this is passed [not used]
*/
function ".$SS_name."_shortcode( \$atts , \$content = null) {
	//return the content.
	return '".addslashes($SS_value)."';
}

";
}
$handlers = $handlers."?>";

return $handlers;
}

function MB_SS_write_handler_file($handler_filename, $handlers) {

	if (!is_writable($handler_filename)) {
		if ($handle = fopen($handler_filename, 'r')) {
			fclose($handle);
			// file exists but is not writeable
			return FALSE;
		} else {
		// file does not exist so we don't know if its writable
		// have to try writing but this may generate a warning
		}
	}
		
	if (!$handle = fopen($handler_filename, 'w')) {
		// Cannot open file
		return FALSE;
	}
	
	if (fwrite($handle, $handlers) === FALSE) {
	   fclose($handle);
	   // Cannot write to file
	   return FALSE;
	}
	
	fclose($handle);

	return TRUE;
}

$handler_filename = $MB_SimpleShortcodes->getHandlerFilename();

if (file_exists($handler_filename)) {
	require_once($handler_filename);
}


?>