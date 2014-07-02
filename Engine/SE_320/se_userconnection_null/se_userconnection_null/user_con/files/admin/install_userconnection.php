<?php

/* $Id: install_userconnection.php 1 2009-09-07 09:36:11Z SocialEngineAddOns $ */
$plugin_name = "User Connection Plugin";
$plugin_version = "3.00";
$plugin_type = "userconnection";
$plugin_desc = "Need to Update : Coming Soon, Nulled by Socialengine.su";
$plugin_icon = "userconnection_userconnection16.gif";
$plugin_menu_title = "650002001";
$plugin_pages_main = "650002001<!>userconnection_userconnection16.gif<!>admin_userconnection.php<~!~>";
$plugin_pages_level = "";
$plugin_url_htaccess = "";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;



if ($install == "userconnection") {
	if (! file_exists ('../include/functions_userconnection.php')) {
			  die ( '<u><b>Error</b></u><br>Please copy the latest <b>functions_userconnection.php</b> in your <b>/include</b> directory.<br><a href="javascript:history.back()">&laquo; Back</a>' );
	}
	include_once("../include/functions_userconnection.php");
  if (empty($_POST ['step'])) {
	  if (!empty($_POST ['task']) && $_POST ['task'] == 'check') {	
	  	$key_lsetting = $_POST['lsettings'];
			$return_lsettings = userconnections_lsettings($key_lsetting, 'userconnection');
			if (!empty($return_lsettings)) {
			  $is_error = true;
			  $smarty->assign('error_message_lsetting', $return_lsettings);
			}	
 			if (empty($is_error)) {	
				
				// check all keys... install now...
			
				//######### INSERT ROW INTO se_plugins
			  $sql = "SELECT plugin_id FROM se_plugins WHERE plugin_type='$plugin_type'";
			  $resource = $database->database_query($sql);
			  
				if (!$database->database_num_rows($resource)) {
					
					$sql = "
			      INSERT INTO se_plugins (plugin_name,
			        plugin_version,
			        plugin_type,
			        plugin_desc,
			        plugin_icon,
			        plugin_menu_title,
			        plugin_pages_main,
			        plugin_pages_level,
			        plugin_url_htaccess
			      ) VALUES (
			        '{$plugin_name}',
			        '{$plugin_version}',
			        '{$plugin_type}',
			        '".str_replace("'", "\'", $plugin_desc)."',
			        '{$plugin_icon}',
			        '{$plugin_menu_title}',
			        '{$plugin_pages_main}',
			        '{$plugin_pages_level}',
			        '{$plugin_url_htaccess}'
			      )
			    ";
			    
			    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
			  } 
			  //######### UPDATE PLUGIN VERSION IN se_plugins
			
				else {
			    $sql = "
			      UPDATE
			        se_plugins
			      SET
			        plugin_name='{$plugin_name}',
			        plugin_version='{$plugin_version}',
			        plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
			        plugin_icon='{$plugin_icon}',
			        plugin_menu_title='{$plugin_menu_title}',
			        plugin_pages_main='{$plugin_pages_main}',
			        plugin_pages_level='{$plugin_pages_level}',
			        plugin_url_htaccess='{$plugin_url_htaccess}'
			      WHERE
			        plugin_type='{$plugin_type}'
			      LIMIT
			        1
			    ";
			    
			    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
				}
			
			 	//######### CREATE userconnection_settings
  			$sql = "SHOW TABLES FROM `$database_name` LIKE 'userconnection_settings'";
  			$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  			if (!$database->database_num_rows($resource)) {
  				$sql = "
      	 		CREATE TABLE `userconnection_settings` (
						  `userconnection_id` tinyint(2) NOT NULL AUTO_INCREMENT,
						  `level` int(11) NOT NULL DEFAULT '3',
						  `is_message` tinyint(2) NOT NULL DEFAULT '1',
						  `message` text,
						  `userconnection_position` tinyint(4) NOT NULL DEFAULT '2',
						  `userconnection_arrow` tinyint(4) NOT NULL DEFAULT '0',
						  `userconnection_degree` tinyint(4) NOT NULL DEFAULT '0',
						  `profile_page` tinyint(2) NOT NULL DEFAULT '1',
						  `user_home_page` tinyint(2) NOT NULL DEFAULT '1',
						  `license_key` varchar(255) NOT NULL,
						  PRIMARY KEY (`userconnection_id`)
						)
   				 ";
    			
  				$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    			$sql = "
			    	INSERT INTO `userconnection_settings` (
							`level` ,
							`is_message` ,
							`message`,
							`userconnection_position`,
							`userconnection_arrow`,
							`userconnection_degree`,
							`profile_page`,
							`user_home_page`
						)
						VALUES (
					 		'5', '1', 'There is no connection path to this user.','2','0','0','1','1'
						)

			   	";
			    
					$database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  			}	
				
				
					//######### CREATE A NEW ATTRIBUTE usersetting_userconnection IN se_usersettings
  		$database->database_query("ALTER TABLE `se_usersettings` ADD `usersetting_userconnection` TINYINT( 4 ) NOT NULL DEFAULT '0'");
  				
  				
  				 				
				//######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
		  	$sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=650002056 LIMIT 1";
		  	$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
		
		  	if (!$database->database_num_rows($resource)) {
		    
		  		$sql = "
		      	INSERT INTO `se_languagevars`
		      	  (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
		      	VALUES
		      	  (650002001, 1, 'User Connections Settings', ''),
		      	  (650002002, 1, 'Connection&nbsp;Path', ''),
		      	  (650002003, 1, 'This page contains the general settings for the User Connections plugin', ''),
		      	  (650002004, 1, 'No-connection Message Setting', ''),
		      	  (650002005, 1, 'Select whether or not you want to show the message if there is no connection between the profile viewer and the profile owner.', ''),
		      	  (650002006, 1, 'Yes, Show this message', ''),
		      	  (650002007, 1, 'No, Do not show message', ''),
		      	  (650002008, 1, 'User Connection Level Setting', ''),
		      	  (650002009, 1, 'Enter the maximum level till which User Connections are to be shown <br /><br />Note: Connections of levels larger than this will not be shown. If the message setting below is chosen to &quot;Yes&quot;, then that message will show in the block, otherwise the complete block will not be shown for connection levels larger than the maximum limit.<br />For optimal page performance, we recommend that a value between 2 to 5 is chosen.', ''),
		      	  (650002010, 1, 'No, do not show this block', ''),
		      	  (650002011, 1, 'Your Changes have been succcessfully saved.', ''),
		      	  (650002013, 1, 'Enter a message', ''),
		      	  (650002014, 1, 'Position of the User Connections Block', ''),
		      	  (650002015, 1, 'Select the position for the User Connections Block which will show the Connection Path between the profile owner and the profile viewer', ''),
		      	  (650002016, 1, 'Profile Tab', ''),
		      	  (650002017, 1, 'Sidebar-Levelled', ''),
		      	  (650002018, 1, 'User&nbsp;Connection&nbsp;Path', ''),
		      	  (650002019, 1, 'How you&#39;re connected to ' , ''),
		      	  (650002020, 1, 'Sidebar-Vertical', ''),
		      	  (650002021, 1, 'Color theme Settings', ''),
		      	  (650002023, 1, 'Select the color of the connection arrow indicators.', ''),
		      	  (650002024, 1, 'Select the color of the connection level indicators.', ''),
		      	  (650002025, 1, 'Green ', ''),
		      	  (650002026, 1, 'Yellow ', ''),
		      	  (650002027, 1, 'Orange ', ''),
		      	  (650002028, 1, 'Blue ', ''),
		      	  (650002029, 1, 'My Connection Settings', ''),
		      	  (650002030, 1, 'Enable or Disable your visibility in connection paths', ''),
		      	  (650002031, 1, 'Hide yourself in Connection Paths', ''),
		      	  (650002032, 1, 'Show yourself in Connection Paths', ''),
		      	  (650002033, 1, 'User Connection ', ''),
		      	  (650002034, 1, 'Sidebar-Vertical, without user photos', ''),
		      	  (650002035, 1, '3rd Degree Contacts ', ''),
		      	  (650002036, 1, 'These are the users who are your 3rd Degree Friends', ''),
		      	  (650002037, 1, 'Contacts of your contacts ', ''),
		      	  (650002038, 1, 'These are the users who are Friends of your Friends', ''),
		      	  (650002039, 1, 'My network ', ''),
		      	  (650002040, 1, 'Direct contacts ', ''),
		      	  (650002041, 1, 'Contacts of your contacts ', ''),
		      	  (650002042, 1, '3rd Level contacts', ''),
		      	  (650002043, 1, 'Click here to see preview', ''),
		      	  (650002044, 1, 'Do you want to show the &quot;My Network&quot; block on the User Homepages?<br /><br />This block indicates the number of 1st Level, 2nd Level and 3rd Level connections of a user. Further, it allows the users to browse through these connections and do various actions like sending message, adding as a friend, etc.', ''),
		      	  (650002045, 1, 'Profile Page', ''),
		      	  (650002047, 1, 'Yes, display this block on the profile pages of users.<br /><br />Note: you can choose position and other settings for this block below.', ''),
		      	  (650002048, 1, 'Yes, show this block', ''),
		      	  (650002049, 1, 'User Homepage', ''),
		      	  (650002050, 1, '2nd Level Friends', ''),
		      	  (650002051, 1, '3rd Level Friends ', ''),
		      	  (650002052, 1, 'Show the &quot;Connection Block&quot; on the profile pages of users.<br /><br />This block shows the connection path (if any) between the profile viewer and the profile owner.', ''),
		      	  (650002053, 1, ' No, do not display this block on the profile pages of users. I do not want users to see the connections between each other.', ''),
		      	  (650002054, 1, 'You do not have any 3rd Level Friends', ''),
		      	  (650002055, 1, 'User&nbsp;Connections', ''),
		      	  (650002056, 1, 'My&nbsp;Connections&nbsp;Settings', ''),
		      	  (650002057, 1, 'License Key', ''),
		          (650002058, 1, 'Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the ', ''),
		          (650002059, 1, 'Support Team of SocialEngineAddOns from the Support section of your Account Area.', ''),
		          (650002060, 1, 'Format: XXXXXX-XXXXXX-XXXXXX', ''),
		          (650002061, 1, 'My 2', ''),
		          (650002062, 1, 'nd', ''),		  
		          (650002064, 1, 'Level Friends', ''),
		          (650002065, 1, 'All your 3', ''),
		          (650002066, 1, 'rd', ''),
		          (650002067, 1, 'Level Friends', ''),
		          (650002063, 1, 'Connection Settings', ''),
		          (650002068, 1, 'You do not have any 2nd Level Friends', '')
		      	  ";
		    	$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
		  	}  
	 	  	header('Location: admin_viewplugins.php');exit();
 			}
		
 			else {
		  	// AN ERROR OCCURED SEND THE DATA BACK
		    $result = array(
		    'lsettings'        => $key_lsetting,
		    );
		    $smarty->assign('result', $result);
	    }
    }
	  if (! file_exists ( getcwd () . '/../templates/admin_install_userconnection.tpl' )) {
			  die ( '<u><b>Error</b></u><br>Please copy the latest <b>admin_install_userconnection.tpl</b> in your <b>/templates/</b> directory.<br><a href="javascript:history.back()">&laquo; Back</a>' );
		}
	  $page = "admin_install_userconnection";
		include "admin_footer.php";
		exit();
  }
}
?>
