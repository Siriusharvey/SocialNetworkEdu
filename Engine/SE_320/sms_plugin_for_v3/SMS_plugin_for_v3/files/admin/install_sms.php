<?php

$plugin_name = "SMS Plugins";
$plugin_version = "3.00alpha";
$plugin_type = "sms";
$plugin_desc = "This plugin for sending sms by mobile manage address book amd sms credits";
$plugin_icon = "mobile_mobile16.png";
$plugin_menu_title = "45000001";
$plugin_pages_main = "45000002<!>mobile_mobile16.png<!>global_sms.php<~!~>45000003<!>mobile_mobile16.png<!>sms_package.php<~!~>45000004<!>mobile_mobile16.png<!>user_sms.php<~!~>45000005<!>mobile_mobile16.png<!>sms_sent.php";
$plugin_pages_level = "";
$plugin_url_htaccess = "";

if($install == "sms") {

  //######### INSERT ROW INTO se_plugins
  if($database->database_num_rows($database->database_query("SELECT plugin_id FROM se_plugins WHERE plugin_type='$plugin_type'")) == 0) {
    $database->database_query("INSERT INTO se_plugins (plugin_name,
					plugin_version,
					plugin_type,
					plugin_desc,
					plugin_icon,
					plugin_menu_title,
					plugin_pages_main,
					plugin_pages_level,
					plugin_url_htaccess
					) VALUES (
					'$plugin_name',
					'$plugin_version',
					'$plugin_type',
					'".str_replace("'", "\'", $plugin_desc)."',
					'$plugin_icon',
					'$plugin_menu_title',
					'$plugin_pages_main',
					'$plugin_pages_level',
					'$plugin_url_htaccess')");


  //######### UPDATE PLUGIN VERSION IN se_plugins
  } else {
    $database->database_query("UPDATE se_plugins SET plugin_name='$plugin_name',
					plugin_version='$plugin_version',
					plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
					plugin_icon='$plugin_icon',
					plugin_menu_title='$plugin_menu_title',
					plugin_pages_main='$plugin_pages_main',
					plugin_pages_level='$plugin_pages_level',
					plugin_url_htaccess='$plugin_url_htaccess' WHERE plugin_type='$plugin_type'");

  }

//######### CREATE se_global_sms
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_user_smssetting'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_user_smssetting` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `mobile_no` varchar(255) NOT NULL,
  `member_sms` varchar(255) NOT NULL,
  `admin_sms` varchar(255) NOT NULL,
  `rsms_credits` int(11) NOT NULL,
  `ssms_credits` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
)");
  }	


  //######### CREATE se_addressbook
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_addressbook'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_addressbook` ( 
  `id` int(64) NOT NULL auto_increment, 
  `nickname` varchar(64) NOT NULL default '', 
  `phone` varchar(64) NOT NULL default '', 
  `grup` varchar(64) NOT NULL default '', 
  `first` varchar(64) NOT NULL default '', 
  `last` varchar(64) NOT NULL default '', 
  `email` varchar(64) NOT NULL default '', 
  `address` varchar(64) NOT NULL default '', 
  `city` varchar(64) NOT NULL default '', 
  `state` varchar(64) NOT NULL default '', 
  `zip` varchar(64) NOT NULL default '', 
  `home` varchar(64) NOT NULL default '', 
  `country` varchar(64) NOT NULL default '', 
  `fax` varchar(64) NOT NULL default '', 
  `details` varchar(64) NOT NULL default '', 
  `owner` varchar(64) NOT NULL default '', 
  UNIQUE KEY `id` (`id`) 
)");
  }




  //######### CREATE se_groups
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_groups'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_groups` ( 
  `id` int(64) NOT NULL auto_increment, 
  `grup` varchar(64) NOT NULL default '', 
  `owner` varchar(64) NOT NULL default '', 
  UNIQUE KEY `id` (`id`) 
)");
  }



  //######### CREATE se_sms
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_sms'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_sms` ( 
  `id` int(128) unsigned NOT NULL auto_increment, 
  `username` varchar(128) NOT NULL default '', 
  `message` varchar(192) NOT NULL default '', 
  `date` varchar(64) NOT NULL default '', 
  `tono` varchar(128) NOT NULL default '', 
  `fromno` varchar(128) NOT NULL default '', 
  KEY `id` (`id`) 
) ");
  }



  //######### CREATE se_subscription
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_subscription'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_subscription` (
  `sno` int(11) NOT NULL auto_increment,
  `text` varchar(200) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sms_credit` varchar(255) NOT NULL,
  PRIMARY KEY  (`sno`)
)");
  }

//######### CREATE se_global_sms
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_global_sms'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_global_sms` (
  `id` int(11) NOT NULL auto_increment,
  `apiid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sms_userid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `currency_sign` varchar(10) NOT NULL,
  `currency_name` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
)");
  }

 if($database->database_num_rows($database->database_query("SELECT * FROM se_global_sms WHERE id='1'")) == 0) {
    $database->database_query("INSERT INTO `se_global_sms` (`id`, `apiid`, `password`, `sms_userid`, `email`,`currency_sign`,`currency_name`) VALUES 
(1, 'userid', 'apiid', 'password', 'email@gmail.com','$','USD')");
  }

//######### ADD COLUMNS/VALUES TO LEVELS TABLE IF BLOGS HAVE NEVER BEEN INSTALLED
if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_sms_allow'")) == 0) {
    $database->database_query("ALTER TABLE se_levels 
					ADD COLUMN `level_sms_allow` int(1) NOT NULL default '1'");
  }


  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=45000001 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES 
					('45000006', '1', 'SMS Settings', ''),
					('45000002', '1', 'Global SMS Settings', ''),
					('45000003', '1', 'SMS Package Manage', ''),
					('45000004', '1', 'User SMS Settings', ''),
					('45000005', '1', 'SMS Send', ''),
					('45000001', '1', 'SMS', 'profile')");



  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  }


}  

?>