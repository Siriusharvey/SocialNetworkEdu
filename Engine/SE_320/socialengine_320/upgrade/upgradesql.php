<?php

/* $Id: upgradesql.php 256 2009-11-18 02:58:25Z steve $ */

$current_database_structure_version = '3.19';

$previous_database_structure_version = @mysql_fetch_assoc(@mysql_query("SELECT setting_version FROM se_settings"));
$previous_database_structure_version = @$previous_database_structure_version['setting_version'];




//######### INSERT LICENSE KEY
mysql_query("UPDATE se_settings SET setting_key='$license'");


//######### BELOW ARE MODIFICATIONS SPECIFICALLY FOR V2.10 ##########################################################################

  //######### MODIFY STATS TABLE
  $field_info = mysql_fetch_assoc(mysql_query("SHOW COLUMNS FROM se_stats FROM `$database_name` LIKE 'stat_date'"));
  if($field_info['Key'] != "UNI") {
    mysql_query("TRUNCATE TABLE se_stats");
    mysql_query("ALTER TABLE `se_stats` ADD UNIQUE (`stat_date`)");
  }

//######### ENSURE ALL PROFILE FIELDS FOR DROPDOWN AND RADIOS ARE DEFAULT -1
$columns = mysql_query("SHOW COLUMNS FROM se_profiles");
while($column_info = mysql_fetch_assoc($columns)) {
  $field_name = $column_info['Field'];
  $field_type = $column_info['Type'];
  $field_default = $column_info['Default'];
  if($field_type == "int(2)" & $field_default != "-1") {
    mysql_query("ALTER TABLE se_profiles CHANGE $field_name $field_name $field_type NOT NULL default '-1'");
  }
}








//######### BELOW ARE MODIFICATIONS SPECIFICALLY FOR V3.00 ##########################################################################

//######### CREATE se_actionmedia
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_actionmedia'")) == 0) {
  mysql_query("CREATE TABLE `se_actionmedia` (
  `actionmedia_id` int(9) NOT NULL auto_increment,
  `actionmedia_action_id` int(9) NOT NULL,
  `actionmedia_path` varchar(250) NOT NULL,
  `actionmedia_link` varchar(250) NOT NULL,
  `actionmedia_width` int(3) NOT NULL,
  `actionmedia_height` int(3) NOT NULL,
  PRIMARY KEY  (`actionmedia_id`),
  KEY `actionmedia_action_id` (`actionmedia_action_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_actionmedia<br>Error: ".mysql_error());
}




//######### MODIFY se_actions
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_actions FROM `$database_name` LIKE 'action_icon'")) == 1) {
  mysql_query("ALTER TABLE se_actions DROP action_icon");
  mysql_query("TRUNCATE TABLE se_actions");
}


//######### MODIFY se_actiontypes
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_actiontypes FROM `$database_name` LIKE 'actiontype_media'")) != 1) {
  mysql_query("DROP TABLE IF EXISTS se_actiontypes");
  mysql_query("CREATE TABLE `se_actiontypes` (
  `actiontype_id` int(9) NOT NULL auto_increment,
  `actiontype_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `actiontype_icon` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `actiontype_setting` int(1) NOT NULL default '0',
  `actiontype_enabled` int(1) NOT NULL default '0',
  `actiontype_desc` int(9) NOT NULL default '0',
  `actiontype_text` int(9) NOT NULL default '0',
  `actiontype_vars` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `actiontype_media` int(1) NOT NULL,
  PRIMARY KEY  (`actiontype_id`),
  UNIQUE KEY `actiontype_name` (`actiontype_name`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_actiontypes<br>Error: ".mysql_error());

  //######### INSERT se_actiontypes
  mysql_query("INSERT INTO `se_actiontypes` (`actiontype_id`, `actiontype_name`, `actiontype_icon`, `actiontype_setting`, `actiontype_enabled`, `actiontype_desc`, `actiontype_text`, `actiontype_vars`, `actiontype_media`) VALUES 
					(1, 'login', 'action_login.gif', 1, 0, 700008, 700001, '[username],[displayname]', 0),
					(2, 'editphoto', 'action_editphoto.gif', 1, 1, 700009, 700002, '[username],[displayname]', 1),
					(3, 'editprofile', 'action_editprofile.gif', 1, 1, 700010, 700003, '[username],[displayname]', 0),
					(4, 'profilecomment', 'action_postcomment.gif', 1, 1, 700011, 700004, '[username1],[displayname1],[username2],[displayname2],[comment]', 0),
					(5, 'addfriend', 'action_addfriend.gif', 1, 1, 700012, 700005, '[username1],[displayname1],[username2],[displayname2]', 0),
					(6, 'signup', 'action_signup.gif', 0, 1, 700013, 700006, '[username],[displayname]', 0),
					(7, 'editstatus', 'action_editstatus.gif', 1, 1, 700014, 700007, '[username],[displayname],[status]', 0)") or die("Insert: se_actiontypes<br>Error: ".mysql_error());
}


//######### CREATE se_faqcats
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_faqcats'")) == 0) {
  mysql_query("CREATE TABLE `se_faqcats` (
  `faqcat_id` int(9) NOT NULL auto_increment,
  `faqcat_order` int(5) NOT NULL default '0',
  `faqcat_title` int(9) NOT NULL default '0',
  PRIMARY KEY  (`faqcat_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_faqcats<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_faqcats
  mysql_query("INSERT INTO `se_faqcats` (`faqcat_id`, `faqcat_order`, `faqcat_title`) VALUES 
					(1, 0, 800001),
					(2, 2, 800002),
					(3, 3, 800003)") or die("Insert: se_faqcats<br>Error: ".mysql_error());
}




//######### CREATE se_faqs
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_faqs'")) == 0) {
  mysql_query("CREATE TABLE `se_faqs` (
  `faq_id` int(9) NOT NULL auto_increment,
  `faq_faqcat_id` int(9) NOT NULL default '0',
  `faq_order` int(5) NOT NULL default '0',
  `faq_subject` int(9) NOT NULL default '0',
  `faq_content` int(9) NOT NULL default '0',
  `faq_datecreated` int(14) NOT NULL default '0',
  `faq_dateupdated` int(14) NOT NULL default '0',
  `faq_views` int(9) NOT NULL default '0',
  PRIMARY KEY  (`faq_id`),
  KEY `faq_faqcat_id` (`faq_faqcat_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_faqs<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_faqs
  mysql_query("INSERT INTO `se_faqs` (`faq_id`, `faq_faqcat_id`, `faq_order`, `faq_subject`, `faq_content`, `faq_datecreated`, `faq_dateupdated`, `faq_views`) VALUES 
					(1, 1, 1, 800004, 800005, 1213374575, 1215547954, 64),
					(10, 1, 4, 800010, 800011, 1213382555, 1215547972, 22),
					(8, 1, 2, 800006, 800007, 1213382503, 1215547957, 49),
					(9, 1, 3, 800008, 800009, 1213382544, 1215547959, 35),
					(11, 1, 5, 800012, 800013, 1213382572, 1215547978, 17),
					(12, 2, 6, 800014, 800015, 1213382588, 1215547980, 27),
					(13, 2, 7, 800016, 800017, 1213382659, 1215547982, 17),
					(14, 3, 8, 800018, 800019, 1213382678, 1215547984, 22),
					(15, 3, 9, 800020, 800021, 1213382698, 1215547986, 13),
					(16, 3, 10, 800022, 800023, 1213382711, 1215547988, 14)") or die("Insert: se_faqs<br>Error: ".mysql_error());
}




//######### CREATE se_languages
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_languages'")) == 0) {
  mysql_query("CREATE TABLE `se_languages` (
  `language_id` int(9) NOT NULL auto_increment,
  `language_code` varchar(8) collate utf8_unicode_ci NOT NULL default '',
  `language_name` varchar(20) collate utf8_unicode_ci NOT NULL default '0',
  `language_autodetect_regex` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `language_setlocale` varchar(10) collate utf8_unicode_ci NOT NULL,
  `language_default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`language_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_languages<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_languages
  mysql_query("INSERT INTO `se_languages` (`language_id`, `language_code`, `language_name`, `language_autodetect_regex`, `language_setlocale`, `language_default`) VALUES (1, 'en', 'English', '/^en/i', '', 1)") or die("Insert: se_languages<br>Error: ".mysql_error());
}




//######### CREATE se_languagevars
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_languagevars'")) == 0) {
  mysql_query("CREATE TABLE `se_languagevars` (
  `languagevar_id` int(9) unsigned NOT NULL default '0',
  `languagevar_language_id` int(9) NOT NULL default '0',
  `languagevar_value` text collate utf8_unicode_ci NULL,
  `languagevar_default` text collate utf8_unicode_ci NULL,
  UNIQUE KEY `INDEX` (`languagevar_id`,`languagevar_language_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_languagevars<br>Error: ".mysql_error());

//######### INSERT DEFAULTS INTO se_languagevars
mysql_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (1, 1, 'Admin Panel', 'admin_header_global, '),
(2, 1, 'Network Management', 'admin_header'),
(3, 1, 'Summary', 'admin_header'),
(4, 1, 'View Users', 'admin_header'),
(5, 1, 'View Admins', 'admin_header'),
(6, 1, 'View Reports', 'admin_header'),
(7, 1, 'View Plugins', 'admin_header'),
(8, 1, 'User Levels', 'admin_header'),
(9, 1, 'Subnetworks', 'admin_header'),
(10, 1, 'Ad Campaigns', 'admin_header'),
(11, 1, 'Global Settings', 'admin_header'),
(12, 1, 'General Settings', 'admin_header'),
(13, 1, 'Signup Settings', 'admin_header'),
(14, 1, 'Recent Activity Feed', 'admin_header'),
(15, 1, 'HTML Templates', 'admin_header'),
(16, 1, 'Profile Fields', 'admin_header'),
(17, 1, 'Banning/Spam', 'admin_header'),
(18, 1, 'User Connections', 'admin_header'),
(19, 1, 'URL Settings', 'admin_header'),
(20, 1, 'System Emails', 'admin_header'),
(21, 1, 'Other Tools', 'admin_header'),
(22, 1, 'Invite Users', 'admin_header'),
(23, 1, 'Announcements', 'admin_header'),
(24, 1, 'Statistics', 'admin_header'),
(25, 1, 'Access Log', 'admin_header'),
(26, 1, 'Logout', 'admin_header'),
(27, 1, 'Administrator Login', ''),
(28, 1, 'Username', 'user_account, signup, admin_viewusers, admin_viewreports, admin_viewadmins, admin_login, '),
(29, 1, 'Password', 'signup, login, home, admin_login, '),
(30, 1, 'Login', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_login, '),
(31, 1, 'Your browser does not have Javascript enabled. Please enable Javascript and try again.', 'admin_login, '),
(32, 1, 'The login details you provided were invalid. Did you <a href=\'admin_lostpass.php\'>forget your password</a>?', 'admin_login, '),
(33, 1, 'Lost Password', 'lostpass, admin_lostpass, '),
(34, 1, 'If you cannot login because you have forgotten your password, please enter your email address in the field below.', 'lostpass, admin_lostpass, '),
(35, 1, 'You have been sent an email with instructions how to reset your password. If the email does not arrive within several minutes, be sure to check your spam or junk mail folders.', 'lostpass, admin_lostpass, '),
(36, 1, 'The email you have entered was not found in the database. Please try again.', 'admin_lostpass, '),
(37, 1, 'Email Address:', 'signup, lostpass, help_contact, admin_viewusers_edit, admin_lostpass, '),
(38, 1, 'Submit', 'admin_lostpass, '),
(39, 1, 'Cancel', 'user_report, user_messages_new, user_home, user_friends_manage, user_friends_block, user_account_delete, profile, lostpass_reset, lostpass, admin_viewusers_edit, admin_viewusers, admin_viewadmins, admin_subnetworks, admin_profile, admin_lostpass_reset, admin_lostpass, admin_levels, admin_language_edit, admin_language, admin_fields, admin_faq, admin_announcements, admin_ads_modify, admin_ads, '),
(40, 1, 'Reset SocialEngine Admin Password Request', 'admin_lostpass, '),
(41, 1, 'Hello,\r\n\r\nYou have requested to reset your SocialEngine admin password. If you would like to do so, please click the link below. If you did not request a password reset, simply ignore this email.\r\n\r\n[link]\r\n\r\nThank You', 'admin_lostpass, '),
(42, 1, 'Reset Password', 'lostpass_reset, admin_lostpass_reset, '),
(43, 1, 'Lost Password Reset', 'lostpass_reset, admin_lostpass_reset, '),
(44, 1, 'Your password has been reset. <a href=\'admin_login.php\'>Click here</a> to login.', 'admin_lostpass_reset, '),
(45, 1, 'Complete the form below to reset your password.', 'lostpass_reset, admin_lostpass_reset, '),
(46, 1, 'New Password', 'user_account_pass, lostpass_reset, admin_viewadmins, admin_lostpass_reset, '),
(47, 1, 'Confirm New Password', 'user_account_pass, lostpass_reset, admin_viewadmins, admin_lostpass_reset, '),
(138, 1, 'Value', 'admin_fields, '),
(50, 1, 'This link is invalid or expired. Please <a href=\'admin_lostpass.php\'>resubmit</a> your password request and follow the link sent to your email address.', 'admin_lostpass_reset, '),
(51, 1, 'Please make sure you have completed all fields.', 'signup, admin_viewadmins, '),
(52, 1, 'Username and Password fields must be alpha-numeric.', 'admin_viewadmins, admin_lostpass_reset, '),
(53, 1, 'Passwords must be at least 6 characters in length.', 'signup, admin_lostpass_reset, '),
(54, 1, 'Password and Password Confirmation fields must match.', 'admin_lostpass_reset, '),
(55, 1, 'Hello, Admin!', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(56, 1, 'Welcome to your social network control panel. Here you can manage and modify every aspect of your social network. Directly below, you will find a quick snapshot of your social network including some useful statistics.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(57, 1, 'Warning: You have not yet deleted install.php and/or installsql.php. Leaving these files on your server is a security risk!', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(58, 1, 'SocialEngine License:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(59, 1, 'Version:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(60, 1, 'Upgrade Available', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(500374, 1, '', ''),
(61, 1, 'Total Users:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(62, 1, 'Comments:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(63, 1, 'Private Messages:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(64, 1, 'User Levels:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(65, 1, 'Subnetworks:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(66, 1, 'Abuse Reports:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(67, 1, 'Friendships:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(68, 1, 'News Posts:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(69, 1, 'Views Today:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(70, 1, 'Signups Today:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(71, 1, 'Logins Today:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(72, 1, 'Admin Accounts:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(73, 1, 'User(s) Online:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(74, 1, '<h2>Getting Started</h2>If you have just setup SocialEngine and are ready to build your social network, here are some helpful suggestions:', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(75, 1, 'Create Profile Fields', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(76, 1, 'One of the most defining aspects of your social network are your profile fields. These determine what information users share about each other on their profiles. They are an essential for emphasizing your social network\'s unique theme or subject.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(77, 1, 'Edit Signup Settings', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(78, 1, 'After you\'ve created your profile fields, you will want to customize the user signup process. Here you can specify what information users have to provide, whether or not they must be invited to signup, and other important details.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(79, 1, 'Edit Default User Level', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(80, 1, 'Now let\'s decide what features users have access to and what limits we will place on their accounts. You can accomplish this by editing the default user level or creating additional user levels.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(81, 1, 'Install Plugins', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(82, 1, 'Plugins give your social network additional functionality and interactivity. If you\'ve already purchased any plugins, now would be an excellent time to install them and configure their settings.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(83, 1, 'Customize Look & Feel', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(84, 1, 'The next step is to give your new social network its own style! You can edit any of the HTML templates (including a global header template and CSS file) to add your own personal design.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(86, 1, 'This page contains a list of the last 300 login attempts. Use this page to observe suspicious login attempts to your social network.', 'admin_log, '),
(87, 1, 'ID', 'admin_viewusers, admin_viewreports, admin_viewadmins, admin_subnetworks, admin_log, admin_levels, admin_language_edit, admin_announcements, admin_ads, '),
(88, 1, 'Date', 'admin_log, admin_announcements, '),
(89, 1, 'Email', 'login, home, admin_viewusers, admin_viewadmins, admin_log, '),
(90, 1, 'Result', 'admin_log, '),
(91, 1, 'IP', 'admin_log, '),
(92, 1, 'Success', 'admin_log, '),
(93, 1, 'Failure', 'admin_log, '),
(95, 1, 'Your users will distinguish themselves through their profile page. You must give them profile fields that allow them to describe themselves in a way that is relevant to the theme of your social network. On this page, you can create profile categories, tabs, and fields.<br><br>If you want all users on your social network to have the same profile fields, you will only need to create one profile type. On the other hand, for example, you may want to have \'Musician\' profiles and \'Fan\' profiles, in which case you would create two profile types. You can create a unique set of profile tabs and fields for each profile type, meaning that Musicians and Fans will each fill out different profile fields. If you have created more than one profile type, users will select their profile type when they signup.<br><br>Profile tabs allow you to organize your profile fields into sections. Commonly used tabs are \'Personal Info\', \'Contact Info\', \'About Me\', etc., but you should create tabs that organize your fields appropriately.<br><br>Profile fields are the actual input fields into which your users will enter their information. Likewise, these should be relevant to the unique theme of your social network.<br><br>Please note that if you have additional language packs, you can translate the category, tab, and field names on the <a href=\'admin_language.php\'>Language Settings</a> page.', 'admin_profile, '),
(96, 1, 'Please ensure you have completed all the required fields.', ''),
(97, 1, 'Please ensure you have filled out the fields in the proper format.', ''),
(98, 1, 'Tabs', 'admin_profile, admin_fields, '),
(99, 1, 'Add Tab', 'admin_profile, admin_fields, '),
(100, 1, 'Fields', 'admin_profile, admin_fields, '),
(101, 1, 'Add Field', 'admin_profile, admin_fields, '),
(102, 1, 'Dependent Field', 'admin_profile, admin_fields, '),
(103, 1, 'Profile Categories', 'admin_profile, '),
(104, 1, 'Add Category', 'admin_profile, admin_faq, '),
(106, 1, 'Field Title:', 'admin_fields, '),
(105, 1, 'Are you sure you want to delete this category? NOTE: If you are deleting a main category, all subcategories and fields will be deleted as well.', 'admin_profile, admin_fields, '),
(107, 1, 'Category:', 'admin_fields, admin_faq, '),
(108, 1, 'Tab:', 'admin_fields, '),
(109, 1, 'Field Type:', 'admin_fields, '),
(110, 1, 'Text Field', 'admin_fields, '),
(111, 1, 'Multi-line Text Area', 'admin_fields, '),
(112, 1, 'Pull-down Select Box', 'admin_fields, '),
(113, 1, 'Radio Buttons', 'admin_fields, '),
(114, 1, 'Date Field', 'admin_fields, '),
(115, 1, 'Inline CSS Style:', 'admin_fields, '),
(116, 1, 'Field Description:', 'admin_fields, '),
(117, 1, 'Custom Error Message:', 'admin_fields, '),
(118, 1, 'Required:', 'admin_fields, '),
(119, 1, 'Not Required', 'admin_fields, '),
(120, 1, 'Required', 'admin_fields, '),
(121, 1, 'Search Type:', 'admin_fields, '),
(122, 1, 'Do Not Display Search Field', 'admin_fields, '),
(123, 1, 'Exact Value Search', 'admin_fields, '),
(124, 1, 'Range Search', 'admin_fields, '),
(125, 1, 'If you would like your users to be able to search based on this field, choose either an \"Exact Value Search\" or a \"Range Search\". If you select \"Exact Value Search\", results will need to match the exact search value entered by the user. If you select \"Range Search\", users will be able to input minimum and maximum search values. This is useful for numerical fields such as \"price\", \"square footage\", and \"age\". Please note that \"Range Search\" does not work for date fields.', 'admin_fields, '),
(126, 1, 'Allowed HTML Tags:', 'admin_fields, '),
(127, 1, 'By default, the user may not enter any HTML tags into this profile field. If you want to allow specific tags, you can enter them above (separated by commas). Example: <i>b, img, a, embed, font<i>', 'admin_fields, '),
(128, 1, 'Field Maxlength:', 'admin_fields, '),
(129, 1, 'Link Field To:', 'admin_fields, '),
(130, 1, 'If you want this field to link to another URL, enter the link format above. Note that this will override the \"Searchable/Linked\" setting above. Use [field_value] to represent the user\'s input for this field. Examples: <i>Regular link (if user\'s input is a URL - must begin with \"http://\"):</i> <strong>[field_value]</strong><br><i>Mailto link (if user\'s input is an email address):</i> <strong>mailto:[field_value]</strong><br><i>AIM Link (if user\'s input is an AIM screenname):</i> <strong>aim:goim?screenname=[field_value]</strong>', 'admin_fields, '),
(131, 1, 'Regex Validation:', 'admin_fields, '),
(132, 1, 'If you want to force the user to enter data in a certain format, enter the corresponding regular expression above. A preg_match() will be applied when the user enters data. This is optional - if you don\'t understand or need regular expressions, leave this blank.', 'admin_fields, '),
(133, 1, 'Options:', 'admin_fields, '),
(134, 1, 'Label', 'admin_fields, '),
(135, 1, 'Dependency?', 'admin_fields, '),
(136, 1, 'Dependent Field Label', 'admin_fields, '),
(137, 1, 'Add New Option', 'admin_fields, '),
(140, 1, 'Edit Field', 'admin_profile, admin_fields, '),
(141, 1, 'Delete Field', 'admin_profile, admin_fields, '),
(142, 1, 'Are you sure you want to delete this field?', 'admin_profile, admin_fields, '),
(144, 1, 'No dep. field', 'admin_profile, admin_fields, '),
(145, 1, 'Yes, has dep. field', 'admin_profile, admin_fields, '),
(139, 1, 'Add select box, radio button, or checkbox options by filling out the fields below. The value field should be a positive integer, and each option should have a unique value. If you would like an additional field to display when a user selects one of your options, you can create a dependent field for that option.', 'admin_fields, '),
(148, 1, 'Edit Dependent Field', 'admin_profile, admin_fields, '),
(146, 1, 'You must enter a non-negative integer for the option values.', 'admin_fields, '),
(143, 1, 'You must enter at least one option.', 'admin_fields, '),
(94, 1, 'Please enter a title for your field.', 'admin_fields, '),
(85, 1, 'You must specify a field type.', 'admin_fields, '),
(48, 1, 'Layout Settings', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(49, 1, 'Language Settings', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(147, 1, 'The layout of your social network includes hundreds of phrases of text which are stored in a language pack. SocialEngine comes with an English pack which is the default when you first install the platform. If you want to change any of these phrases on your social network, you can edit the pack below. If you want to allow users to pick from multiple languages, you can also create additional packs below. If you have multiple language packs, the pack you\'ve selected as your \"default\" will be the language that displays if a user has not selected any other language. Note: You can not delete the default language. To edit a language\'s details, click its name.', 'admin_language, '),
(149, 1, 'Language Name', 'admin_language, '),
(150, 1, 'Language Code', 'admin_language, '),
(151, 1, 'Autodetection Regex', 'admin_language, '),
(152, 1, 'Default', 'lostpass, admin_viewusers, admin_levels, admin_language, '),
(153, 1, 'Options', 'admin_viewusers, admin_viewreports, admin_viewadmins, admin_subnetworks, admin_levels, admin_language, admin_faq, admin_announcements, admin_ads, '),
(154, 1, 'edit phrases', 'admin_language, '),
(155, 1, 'delete', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers, admin_viewreports, admin_viewadmins, admin_subnetworks, admin_levels, admin_language, admin_faq, admin_announcements, admin_ads, '),
(156, 1, 'Create New Language Pack', 'admin_language, '),
(157, 1, 'Delete Language Pack', 'admin_language, '),
(158, 1, 'Create Language Pack', 'admin_language, '),
(159, 1, 'Edit Language Pack', 'admin_language, '),
(160, 1, 'Language Selection Settings', 'admin_language, '),
(161, 1, 'If you have more than one language pack, do you want to allow your <b>registered users</b> to select which one will be used while they are logged in? If you select \"Yes\", users will be able to choose their language on the signup page and the account settings page. Note that this will only apply if you have more than one language pack.', 'admin_language, '),
(162, 1, 'Yes, allow registered users to choose their own language.', 'admin_language, '),
(163, 1, 'No, do not allow registered users to save their language preference.', 'admin_language, '),
(164, 1, 'If you have more than one language pack, do you want to display a select box on your homepage so that <b>unregistered users</b> can change the language in which they view the social network? Note that this will only apply if you have more than one language pack.', 'admin_language, '),
(165, 1, 'Yes, display a select box that will allow unregistered users to change their language.', 'admin_language, '),
(166, 1, 'No, do not allow unregistered users to change the site language.', 'admin_language, '),
(167, 1, 'If you have more than one language pack, do you want the system to autodetect the language settings from your visitors\' browsers? If you select \"Yes\", the system will attempt to detect what language the user has set in their browser settings. If you have a matching language, your site will display in that language, otherwise it will display in the default language.<br><br>The system uses regexes used to detect the visitor\'s language. They will be run on the request header \"HTTP_ACCEPT_LANGUAGE\" after it has been cleaned. For example, here is a copy of your browser\'s language settings:', 'admin_language, '),
(168, 1, 'Your HTTP_ACCEPT_LANGUAGE is:', 'admin_language, '),
(169, 1, 'Your HTTP_ACCEPT_LANGUAGE after cleaning:', 'admin_language, '),
(170, 1, 'Your autodetected language with the current settings:', 'admin_language, '),
(171, 1, 'Yes, attempt to detect the visitor\'s language based on their browser settings.', 'admin_language, '),
(172, 1, 'No, do not autodetect the visitor\'s language.', 'admin_language, '),
(173, 1, 'Save Changes', 'user_home, user_editprofile_style, user_editprofile, user_account_privacy, user_account_pass, user_account, admin_viewusers_edit, admin_url, admin_templates, admin_signup, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_language, admin_general, admin_emails, admin_connections, admin_banning, admin_ads_modify, admin_activity, '),
(174, 1, 'Are you sure you want to delete this language pack?', 'admin_language, '),
(175, 1, 'Delete', 'user_account_delete, profile, admin_viewusers, admin_viewadmins, admin_subnetworks, admin_levels, admin_language, admin_faq, admin_announcements, admin_ads, '),
(176, 1, 'Please enter a name for your language.', 'admin_language, '),
(177, 1, 'Please enter your language pack\'s language name, language code (used to set content headers), setlocale code, and regex in the fields below. The setlocale code allows you to display dates in other languages and uses the PHP function <a href=\'http://www.php.net/manual/en/function.setlocale.php\'>setlocale()</a>. All of the available locale settings for your sever are provided below. If given the option, select the locale code with \"utf8\" in it, as dates may not display properly otherwise. If you leave this field blank, the default server language will be used.', 'admin_language, '),
(178, 1, 'Use this page to edit phrases of text within this language pack. Note that you can use the search box to find a specific phrase you may be looking for. If you cannot find the phrase, try just using one or two words from the phrase in the search box. When you edit a phrase, a small window will appear with a box for each language pack (if you have more than one) - you can enter all the different translations for this phrase into each respective box. After you close this popup window, the \"edit\" link for the next phrase will be automatically highlighted. If you want to edit the next phrase, you can press the \"Enter\" key on your keyboard to open the next phrase quickly. Note that if you change admin panel phrases, you may need to refresh the page to see the changes.', 'admin_language_edit, '),
(179, 1, '<b>Note: You do not have any phrases in this language pack. To add phrases, go to another language pack and edit the phrases there - you will be able to provide tranlsations for this language pack.</b>', 'admin_language_edit, '),
(180, 1, 'Partial Phrase:', 'admin_language_edit, '),
(181, 1, 'Find Phrase', 'admin_language_edit, '),
(182, 1, 'Last Page', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_language_edit, '),
(183, 1, 'Next Page', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_language_edit, '),
(184, 1, 'viewing result %1\$s of %2\$s', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_language_edit, '),
(185, 1, 'viewing results %1\$s-%2\$s of %3\$s', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_language_edit, '),
(186, 1, 'Phrase', 'admin_language_edit, '),
(187, 1, 'edit', 'profile, admin_viewusers, admin_viewadmins, admin_subnetworks, admin_levels, admin_language_edit, admin_faq, admin_announcements, admin_ads, '),
(188, 1, 'Edit Phrase', 'admin_language_edit, '),
(189, 1, 'Change your phrases in the languages below:', 'admin_language_edit, '),
(190, 1, 'This page contains general settings that affect your entire social network.', 'admin_general, '),
(191, 1, 'Your changes have been saved.', 'user_editprofile_style, user_editprofile, user_account_privacy, user_account_pass, admin_url, admin_signup, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_general, admin_emails, admin_connections, admin_banning, admin_activity, '),
(192, 1, 'Public Permission Defaults', 'admin_general, '),
(193, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here.', 'admin_general, '),
(194, 1, 'Profiles', 'admin_general, '),
(195, 1, 'Yes, the public can view profiles unless they are made private.', 'admin_general, '),
(196, 1, 'No, the public cannot view profiles.', 'admin_general, '),
(197, 1, 'Invite Page', 'admin_general, '),
(198, 1, 'Yes, the public can use the invite page.', 'admin_general, '),
(199, 1, 'No, the public cannot use the invite page.', 'admin_general, '),
(200, 1, 'Search Page', 'admin_general, '),
(201, 1, 'Yes, the public can use the search page.', 'admin_general, '),
(202, 1, 'No, the public cannot use the search page.', 'admin_general, '),
(203, 1, 'Portal Page', 'admin_general, '),
(204, 1, 'Yes, the public view use the portal page.', 'admin_general, '),
(205, 1, 'No, the public cannot view the portal page.', 'admin_general, '),
(206, 1, 'Timezone', 'user_account, signup, admin_general, '),
(207, 1, 'Please select a default timezone setting for your social network. This will be the default timezone applied to users\' accounts if they do not select a timezone during signup, or if they are not logged in.', 'admin_general, '),
(208, 1, 'Date Format', 'admin_general, '),
(209, 1, 'Select the date format you want to use on your social network. This will affect the appearance of the dates that appear on your social network pages.', 'admin_general, '),
(210, 1, 'Social networks are often the target of aggressive spam tactics. This most often comes in the form of fake user accounts and spam in comments. On this page, you can manage various anti-spam and censorship features. Note: To turn on the signup image verification feature (a popular anti-spam tool), see the <a href=\'admin_signup.php\'>Signup Settings</a> page.', 'admin_banning, '),
(211, 1, 'Ban Users by IP Address', 'admin_banning, '),
(212, 1, 'To ban users by their IP address, enter their address into the field below. Addresses should be separated by commas, like <i>123.456.789.123, 23.45.67.89</i>', 'admin_banning, '),
(213, 1, 'Ban Users by Email Address', 'admin_banning, '),
(214, 1, 'To ban users by their email address, enter their email into the field below. Emails should be separated by commas, like <i>user1@domain1.com, user2@domain2.com</i>. Note that you can ban all email addresses with a specific domain as follows: <i>*@domain.com</i>', 'admin_banning, '),
(215, 1, 'Ban Users by Username', 'admin_banning, '),
(216, 1, 'Enter the usernames that are not permitted on your social network. Usernames should be separated by commas, like <i>username1, username2</i>', 'admin_banning, '),
(217, 1, 'Censored Words on Profiles and Plugins', 'admin_banning, '),
(218, 1, 'Enter any words that you you want to censor on your users\' profiles as well as any plugins you have installed. These will be replaced with asterisks (*). Separate words by commas like <i>word1, word2</i>', 'admin_banning, '),
(219, 1, 'Require users to enter validation code when commenting?', 'admin_banning, '),
(220, 1, 'If you have selected Yes, an image containing a random sequence of 6 numbers will be shown to users on the \"write a comment\" page. Users will be required to enter these numbers into the Verification Code field in order to post their comment. This feature helps prevent users from trying to create comment spam. For this feature to work properly, your server must have the GD Libraries (2.0 or higher) installed and configured to work with PHP. If you are seeing errors, try turning this off.', 'admin_banning, '),
(221, 1, 'Yes, enable validation code for commenting.', 'admin_banning, '),
(222, 1, 'No, disable validation code for commenting.', 'admin_banning, '),
(223, 1, 'Require users to enter validation code when inviting others?', 'admin_banning, '),
(224, 1, 'If you have selected Yes, an image containing a random sequence of 6 numbers will be shown to users on the \"invite\" page. Users will be required to enter these numbers into the Verification Code field in order to send their invitation. This feature helps prevent users from trying to create comment spam. For this feature to work properly, your server must have the GD Libraries (2.0 or higher) installed and configured to work with PHP. If you are seeing errors, try turning this off.', 'admin_banning, '),
(225, 1, 'Yes, enable validation code for inviting.', 'admin_banning, '),
(226, 1, 'No, disable validation code for inviting.', 'admin_banning, '),
(227, 1, 'Facilitating associations and relationships between users is essential to building a successful social network. There are several different types of connections that can exist between users. Use this page to determine how your users will associate with each other. Note that although we refer to these relationships as \"friendships\" in this control panel, you should use a word that best fits with your social network\'s theme. For example, if you are running a business-oriented social network, you may want to change this word to \"connections.\"', 'admin_connections, '),
(228, 1, 'Who can users invite to become friends?', 'admin_connections, '),
(229, 1, 'Please select who users can invite to become their friends. Note that if you select \"nobody\", none of the other settings on this page will apply.', 'admin_connections, '),
(230, 1, 'Nobody', 'admin_connections, '),
(231, 1, 'Users cannot invite anyone to become friends.', 'admin_connections, '),
(232, 1, 'Anybody', 'admin_connections, '),
(233, 1, 'Users can invite any other user to become friends.', 'admin_connections, '),
(234, 1, 'Same Subnetwork', 'admin_connections, '),
(235, 1, 'Users can only invite other users in the same subnetwork to become friends.', 'admin_connections, '),
(236, 1, 'Friends of Friends', 'admin_connections, '),
(237, 1, 'Users can only invite their current friends\' friends to become friends.', 'admin_connections, '),
(238, 1, 'Friendship Framework', 'admin_connections, '),
(239, 1, 'Please select how you want the friendship request process to work. If you change this setting from \"Verified Friendships\" to \"Unverified Friendships\", all pending friendships will be automatically confirmed.', 'admin_connections, '),
(240, 1, 'Verified Friendships (Two-way)', 'admin_connections, '),
(241, 1, 'When UserA invites UserB to become friends, UserB is added to UserA\'s friend list and UserA is added to UserB\'s friend list after UserB confirms the friendship.', 'admin_connections, '),
(242, 1, 'Verified Friendships (One-way)', 'admin_connections, '),
(243, 1, 'When UserA invites UserB to become friends, UserB is added to UserA\'s friend list after UserB confirms the friendship.', 'admin_connections, '),
(244, 1, 'Unverified Friendships (Two-way)', 'admin_connections, '),
(245, 1, 'When UserA invites UserB to become friends, UserB is immediately listed in UserA\'s friend list, and UserA is immediately listed in UserB\'s friend list.', 'admin_connections, '),
(246, 1, 'Unverified Friendships (One-way)', 'admin_connections, '),
(247, 1, 'When UserA invites UserB to become friends, UserB is immediately listed in UserA\'s friend list.', 'admin_connections, '),
(248, 1, 'Friendship Types', 'admin_connections, '),
(249, 1, 'Add friendship types to allow your users to specify their varying degrees of friendships. Example friendship types include \"Acquaintance\", \"Co-Worker\", \"Best Friend\", \"Significant Other\", etc. If you only specify one friendship type or leave this area blank, users will not be prompted to specify a friendship type when connecting to other users.', 'admin_connections, '),
(250, 1, 'Add New Type', 'admin_connections, '),
(251, 1, 'Custom Friendship Types', 'admin_connections, '),
(252, 1, 'Allow users to specify a custom friendship type.', 'admin_connections, '),
(253, 1, 'Do not allow users to specify a custom friendship type.', 'admin_connections, '),
(254, 1, 'Friendship Explanation', 'admin_connections, '),
(255, 1, 'Allow users to provide an explanation of their friendships.', 'admin_connections, '),
(256, 1, 'Do not allow users to provide an explanation of their friendships.', 'admin_connections, '),
(257, 1, 'Your social network can have more than one administrator. This is useful if you want to have a staff of admins who maintain your social network. However, the first admin to be created (upon installation) is the \"superadmin\" and cannot be deleted. The superadmin can create and delete other admin accounts. All admin accounts on your system are listed below.', 'admin_viewadmins, '),
(258, 1, 'Name', 'help_contact, admin_viewadmins, admin_subnetworks, admin_levels_edit, admin_levels, admin_faq, admin_ads, '),
(259, 1, 'Status', 'admin_viewadmins, admin_ads, '),
(260, 1, 'Superadmin', 'admin_viewadmins, '),
(261, 1, 'Admin', 'admin_viewadmins, '),
(262, 1, 'Add Admin', 'admin_viewadmins, '),
(263, 1, 'Delete Admin', 'admin_viewadmins, '),
(264, 1, 'Are you sure you want to delete this admin?', 'admin_viewadmins, '),
(265, 1, 'Complete the form below to add/edit this admin account. Note that normal admins will not be able to delete or modify the superadmin account. If you want to change this admin\'s password, enter both the old and new passwords below - otherwise, leave them both blank.', 'admin_viewadmins, '),
(266, 1, 'Confirm Password', 'signup, '),
(267, 1, 'The Old Password field must match this admin\'s old password.', 'admin_viewadmins, '),
(268, 1, 'The username you have entered is already in use by another admin.', ''),
(269, 1, 'Old Password', 'user_account_pass, admin_viewadmins, '),
(270, 1, 'Edit Admin', 'admin_viewadmins, ')") or die("Insert: se_languagevars (1)<br>Error: ".mysql_error());
mysql_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (271, 1, 'If you want to put users into different groups with varying access to features (e.g. Bronze, Silver, and Gold membership plans), you can create multiple user groups. You must always have at least one group - your default group (which cannot be deleted). When users signup, they will be placed into the group you have designated as the default group on this page. You can change a user\'s group by editing their account from the <a href=\'admin_viewusers.php\'>View Users</a> page. If you want to give all users on your social network the same features and limits, you will only need one user level.', 'admin_levels, '),
(272, 1, 'Add User Level', 'admin_levels, '),
(273, 1, 'Users', 'admin_subnetworks, admin_levels, '),
(274, 1, 'user(s)', 'admin_levels, '),
(275, 1, 'To create a user level, complete the following form. Once it is created, you will be able to edit all the settings for this user level.', 'admin_levels, '),
(276, 1, 'Please specify a name for this user level.', 'admin_levels, '),
(277, 1, 'Description', 'admin_levels_edit, admin_levels, '),
(278, 1, 'Add Level', 'admin_levels, '),
(279, 1, 'Are you sure you want to delete this user level? Users in this level will be moved to the default user level.', 'admin_levels, '),
(280, 1, 'Delete User Level', 'admin_levels, '),
(281, 1, 'Edit User Level', 'admin_levels_edit, '),
(282, 1, 'You are currently editing this user level\'s settings. Remember, these settings only apply to the users that belong to this user level. When you\'re finished, you can edit the <a href=\'admin_levels.php\'>other levels here</a>.', 'admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, '),
(283, 1, 'To modify this user level, complete the following form.', 'admin_levels_edit, '),
(284, 1, 'Please specify a name for this user level.', 'admin_levels_edit, '),
(285, 1, 'Level Settings', 'admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, '),
(286, 1, 'User Settings', 'admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, '),
(287, 1, 'Message Settings', 'admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, '),
(288, 1, 'Editing User Level: %1\$s', 'admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, '),
(289, 1, 'This page contains various settings that affect your users\' accounts.', 'admin_levels_usersettings, '),
(290, 1, 'Photo file types can only be gif, jpg, jpeg, or png.', 'admin_levels_usersettings, '),
(291, 1, 'Photo width and height must be integers between 1 and 999', 'admin_levels_usersettings, '),
(292, 1, 'Can users block other users?', 'admin_levels_usersettings, '),
(293, 1, 'If set to \"yes\", users can block other users from sending them private messages, requesting their friendship, and viewing their profile. This helps fight spam and network abuse.', 'admin_levels_usersettings, '),
(294, 1, 'Yes, users can block other users.', 'admin_levels_usersettings, '),
(295, 1, 'No, users cannot block other users.', 'admin_levels_usersettings, '),
(296, 1, 'Privacy Options', 'admin_levels_usersettings, '),
(297, 1, 'Search Privacy Options', 'admin_levels_usersettings, admin_levels_albumsettings, '),
(298, 1, 'If you enable this feature, users will be able to exclude themselves from search results and the lists of users on the homepage (such as Recent Signups). Otherwise, all users will be included in search results.', 'admin_levels_usersettings, '),
(299, 1, 'Yes, allow users to exclude themselves from search results.', 'admin_levels_usersettings, '),
(300, 1, 'No, force all users to be included in search results.', 'admin_levels_usersettings, '),
(301, 1, 'Profile Privacy Options', 'admin_levels_usersettings, '),
(302, 1, 'Your users can choose from any of the options checked below when they decide who can see their profile. If you do not check any options, everyone will be allowed to view profiles.', 'admin_levels_usersettings, '),
(303, 1, 'Profile Comment Options', 'admin_levels_usersettings, '),
(304, 1, 'Your users can choose from any of the options checked below when they decide who can post comments on their profile. If you do not check any options, everyone will be allowed to post comments on profiles.', 'admin_levels_usersettings, '),
(305, 1, 'Allow User Photos?', 'admin_levels_usersettings, '),
(306, 1, 'If you enable this feature, users can upload a small photo icon of themselves. This will be shown next to their name/username on their profiles, in search/browse results, next to their private messages, etc.', 'admin_levels_usersettings, '),
(307, 1, 'Yes, users can upload a photo.', 'admin_levels_usersettings, '),
(308, 1, 'No, users can not upload a photo.', 'admin_levels_usersettings, '),
(309, 1, 'If you have selected \"Yes\" above, please input the maximum dimensions for the user photos. If your users upload a photo that is larger than these dimensions, the server will attempt to scale them down automatically. This feature requires that your PHP server is compiled with support for the GD Libraries.', 'admin_levels_usersettings, '),
(310, 1, 'Maximum Width:', 'admin_levels_usersettings, admin_levels_albumsettings, '),
(311, 1, '(in pixels, between 1 and 999)', 'admin_levels_usersettings, admin_levels_albumsettings, '),
(312, 1, 'Maximum Height:', 'admin_levels_usersettings, admin_levels_albumsettings, '),
(313, 1, 'What file types do you want to allow for user photos (gif, jpg, jpeg, or png)? Separate file types with commas, i.e. <i>jpg, jpeg, gif, png</i>', 'admin_levels_usersettings, '),
(314, 1, 'Allowed File Types:', 'admin_levels_usersettings, '),
(315, 1, 'Allow custom CSS in profiles?', 'admin_levels_usersettings, '),
(316, 1, 'Enable this feature if you want to allow users to customize the colors and fonts of their profiles with their own CSS styles.', 'admin_levels_usersettings, '),
(317, 1, 'Yes, users can add custom CSS styles to their profiles.', 'admin_levels_usersettings, '),
(318, 1, 'No, users cannot add custom CSS styles to their profiles.', 'admin_levels_usersettings, '),
(319, 1, 'Allow profile status messages?', 'admin_levels_usersettings, '),
(320, 1, 'Enable this feature if you want to allow users to show their \"status\" on their profile. By updating their status, users can tell others what they are up to, what\'s on their minds, etc.', 'admin_levels_usersettings, '),
(321, 1, 'Yes, allow users to have a \"status\" message.', 'admin_levels_usersettings, '),
(322, 1, 'No, users cannot have a \"status\" message.', 'admin_levels_usersettings, '),
(323, 1, 'Everyone', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, '),
(324, 1, 'All Registered Users', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, admin_activity, '),
(325, 1, 'Only My Friends and Everyone within My Subnetwork', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, admin_activity, '),
(326, 1, 'Only My Friends and Their Friends within My Subnetwork', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, '),
(327, 1, 'Only My Friends', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, admin_activity, '),
(328, 1, 'Only Me', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, '),
(329, 1, 'Nobody', 'user_account_privacy, admin_levels_usersettings, admin_levels_albumsettings, '),
(330, 1, 'Facilitating user interactivity is the key to developing a successful social network. Allowing private messages between users is an excellent way to increase interactivity. From this page, you can enable the private messaging feature and configure its settings.', 'admin_levels_messagesettings, '),
(331, 1, 'Who can users send private messages to?', 'admin_levels_messagesettings, '),
(332, 1, 'If set to \"nobody\", none of the other settings on this page will apply. Otherwise, users will have access to their private message inbox and will be able to send each other messages.', 'admin_levels_messagesettings, '),
(333, 1, 'Nobody - users cannot send private messages.', 'admin_levels_messagesettings, '),
(334, 1, 'Friends only - users can send private messages to their friends only.', 'admin_levels_messagesettings, '),
(335, 1, 'Everyone - users can send private messages to anyone.', 'admin_levels_messagesettings, '),
(336, 1, 'Inbox/Outbox Capacity', 'admin_levels_messagesettings, '),
(337, 1, 'How many total conversations will users be allowed to store in their inbox and outbox? If a user\'s inbox or outbox is full and a new conversation is started, the oldest conversation will be automatically deleted.', 'admin_levels_messagesettings, '),
(338, 1, 'conversations in inbox folder.', 'admin_levels_messagesettings, '),
(339, 1, 'conversations in outbox folder.', 'admin_levels_messagesettings, '),
(340, 1, 'Use this page to invite new users to your social network. You can specify 10 email addresses at a time. If you have specified that users may signup by invitation only, this page will email an invitation code to the email addresses you specify. Otherwise, a simple invitation email will be sent. Both these emails can be modified on your <a href=\'admin_emails.php\'>System Emails</a> page.', 'admin_invite, '),
(341, 1, 'Invitations have been sent!', 'admin_invite, '),
(342, 1, 'Email Addresses', 'admin_invite, '),
(343, 1, 'Enter email addresses (max 10), separated by commas, in the field below.', 'admin_invite, '),
(344, 1, 'Create Advertising Campaign', 'admin_ads_modify, '),
(345, 1, 'Follow this guide to design and launch a new advertising campaign.', 'admin_ads_modify, '),
(346, 1, 'Advertisement Media', 'admin_ads_modify, '),
(347, 1, 'Upload a banner image from your computer or specify your advertisement HTML code (e.g. Google Adsense). If you choose to upload an image, it must be a valid GIF, JPG, JPEG, or PNG file under 200kb.', 'admin_ads_modify, '),
(348, 1, 'Upload Banner Image', 'admin_ads_modify, '),
(349, 1, 'OR', 'signup, admin_language_edit, admin_ads_modify, '),
(350, 1, 'Insert Banner HTML', 'admin_ads_modify, '),
(351, 1, 'Insert Banner HTML Code', 'admin_ads_modify, '),
(352, 1, 'Save HTML Code & Preview', 'admin_ads_modify, '),
(353, 1, 'Please insert your banner HTML before continuing.', 'admin_ads_modify, '),
(354, 1, 'Banner Preview', 'admin_ads_modify, '),
(355, 1, 'Save Banner', 'admin_ads_modify, '),
(356, 1, 'Remove Banner', 'admin_ads_modify, '),
(357, 1, 'Edit HTML', 'admin_ads_modify, '),
(358, 1, 'Upload Banner Image', 'admin_ads_modify, '),
(359, 1, 'File:', 'admin_ads_modify, '),
(360, 1, 'Link URL:', 'admin_ads_modify, '),
(361, 1, 'Upload Banner & Preview', 'admin_ads_modify, '),
(362, 1, 'Please choose a file from your hard drive to upload.', 'admin_ads_modify, '),
(363, 1, 'The file you specified failed to upload. Please make sure this is a valid image file and the /uploads_admin/ads directory is writeable on the server.', 'admin_ads_modify, '),
(364, 1, 'Campaign Information', 'admin_ads_modify, '),
(365, 1, 'Begin by naming this campaign and determining its start date and ending terms. If you select an ending date, the campaign will end immediately when that date is reached. If you specify a certain number of total views allowed or total clicks allowed, the campaign will end when that number of views or clicks is reached. If you specify a minimum CTR (click-through ratio, which is the ratio of clicks to views) and the campaign\'s CTR goes below your limit, the campaign will end. If you decide to specify a minimum CTR limit, you should enter it as a percentage of clicks to views (e.g. 0.05%). To create a campaign with no definite end, don\'t specify an end date or any other limits and your campaign will continue until you choose to end it.', 'admin_ads_modify, '),
(366, 1, 'Note: Current date is %1\$s', 'admin_ads_modify, '),
(367, 1, 'Campaign Name:', 'admin_ads_modify, '),
(368, 1, 'Start Date:', 'admin_ads_modify, '),
(369, 1, 'End Date:', 'admin_ads_modify, '),
(370, 1, 'Don\'t end this campaign on a specific date.', 'admin_ads_modify, '),
(371, 1, 'End this campaign on a specific date.', 'admin_ads_modify, '),
(372, 1, 'Total Views Allowed:', 'admin_ads_modify, '),
(373, 1, 'Unlimited', 'admin_levels_albumsettings, admin_ads_modify, '),
(374, 1, 'Total Clicks Allowed:', 'admin_ads_modify, '),
(375, 1, 'Minimum CTR:', 'admin_ads_modify, '),
(376, 1, 'Select Placement Position', 'admin_ads_modify, '),
(377, 1, 'Where on the page do you want your banners to display? You can place your banners at the very top of the page, just above the main content area, to the left of the content area, to the right of the content area, or at the very bottom of the page. Please note that this automatic placement will NOT work if you have removed the advertisement code Smarty variables from your header.tpl and footer.tpl files. Also note that if you select a position below, the banner will show up in that position on every page of the social network. You can insert banners on a single page (or a few pages) by following the manual insertion instructions below.', 'admin_ads_modify, '),
(378, 1, 'If you want to have this advertisement display somewhere other than the site-wide positions shown above (e.g. within the content on a single page), you can insert the following code into any of your <a href=\'admin_templates.php\' target=\'_blank\'>templates</a> and it will display your advertisement once you\'ve created the campaign.', ''),
(379, 1, 'Select Audience', 'admin_ads_modify, '),
(380, 1, 'Specify which users will be shown advertisements from this campaign. To include the entire user population in this campaign, leave all of the <a href=\'admin_levels.php\' target=\'_blank\'>user levels</a> and <a href=\'admin_subnetworks.php\' target=\'_blank\'>subnetworks</a> selected. To select multiple user levels or subnetworks, use CTRL-click. Note that this advertising campaign will only be displayed to logged-in users that match both a user level <b>AND</b> a subnetwork you\'ve selected.', 'admin_ads_modify, '),
(381, 1, 'Subnetworks', ''),
(382, 1, '(signup default)', 'admin_announcements, admin_ads_modify, '),
(383, 1, 'Default Subnetwork', 'admin_announcements, admin_ads_modify, '),
(384, 1, 'Also show this advertisement to visitors that are not logged in.', 'admin_ads_modify, '),
(385, 1, 'Create New Campaign', 'admin_ads_modify, admin_ads, '),
(386, 1, 'Please upload a banner or specify your advertisement HTML for this campaign.', ''),
(387, 1, 'Please provide a name for this advertising campaign.', ''),
(388, 1, 'Please provide a complete start date for this campaign.', 'admin_ads_modify, '),
(389, 1, 'Please provide a complete end date for this campaign.', ''),
(390, 1, 'Please select an end date that is later than your start date.', 'admin_ads_modify, '),
(391, 1, 'Please provide a maximum number of views for this campaign. This must be an integer (e.g. 250000).', ''),
(392, 1, 'Please provide a maximum number of clicks for this campaign. This must be an integer (e.g. 250).', ''),
(393, 1, 'Please provide a minimum CTR limit in the form of a percentage of clicks to views (e.g. 1.50%). This value may not exceed 100%.', 'admin_ads_modify, '),
(394, 1, 'Displaying advertisements is an excellent way to monetize your social network. By creating ad campaigns, you can determine exactly where your ads will appear, how long they will be displayed, and who they will be shown to. The key to generating substantial revenue from your social network is to create targeted ad campaigns. This means that you should make an effort to show specific ads to users based on their interests or personal characteristics (e.g. their profile information). To accomplish this, ad campaigns can be created for specific <a href=\'admin_levels.php\'>user levels</a> and/or <a href=\'admin_subnetworks.php\'>subnetworks</a>.', 'admin_ads, '),
(395, 1, 'Refresh Stats', 'admin_ads, '),
(396, 1, 'Views', 'admin_ads, '),
(397, 1, 'Clicks', 'admin_ads, '),
(398, 1, 'CTR', 'admin_ads, '),
(399, 1, 'pause', 'admin_ads, '),
(400, 1, 'unpause', 'admin_ads, '),
(401, 1, 'There are currently no advertising campaigns on your social network.', 'admin_ads, '),
(402, 1, 'Paused', 'admin_ads, '),
(403, 1, 'Active', 'admin_ads, '),
(404, 1, 'Waiting For Start Date', 'admin_ads, '),
(405, 1, 'Completed', 'admin_ads, '),
(406, 1, 'Delete Ad Campaign', 'admin_ads, '),
(407, 1, 'Are you sure you want to delete this ad campaign?', 'admin_ads, '),
(408, 1, 'Edit Advertising Campaign', 'admin_ads_modify, '),
(409, 1, 'Edit this advertising campaign\'s details below.', 'admin_ads_modify, '),
(410, 1, 'The user signup process is a crucial element of your social network. You need to design a signup process that is user friendly but also gets the initial information you need from new users. On this page, you can configure your signup process.', 'admin_signup, '),
(411, 1, 'Fields on Signup Page', 'admin_signup, '),
(412, 1, 'Your users will have an opportunity to begin filling out their profile when they signup. Below, you can specify which profile fields will appear on the signup page, and which will be required. Keep in mind that a lengthly signup process may deter some users from signing up to your social network. To add or delete profile fields, visit the <a href=\'admin_profile.php\'>Profile Fields</a> page.', 'admin_signup, '),
(413, 1, 'User Photo Upload', 'admin_signup, '),
(414, 1, 'Do you want your users to be able to upload a photo of themselves upon signup?', 'admin_signup, '),
(415, 1, 'Yes, give users the option to upload a photo upon signup.', 'admin_signup, '),
(416, 1, 'No, do not allow users to upload a photo upon signup.', 'admin_signup, '),
(417, 1, 'Enable Users?', 'admin_signup, '),
(418, 1, 'If you have selected YES, users will automatically be enabled when they signup. If you select NO, you will need to manually enable users through the <a href=\'admin_viewusers.php\'>View Users</a> page. Users that are not enabled cannot login.', 'admin_signup, '),
(419, 1, 'Yes, enable users upon signup.', 'admin_signup, '),
(420, 1, 'No, do not enable users upon signup.', 'admin_signup, '),
(421, 1, 'Send Welcome Email?', 'admin_signup, '),
(422, 1, 'Send users a welcome email upon signup? If you have email verification activated, this email will be sent upon verification. You can modify this email on the <a href=\'admin_emails.php\'>System Emails</a> page.', 'admin_signup, '),
(423, 1, 'Yes, send users a welcome email.', 'admin_signup, '),
(424, 1, 'No, do not send users a welcome email.', 'admin_signup, '),
(425, 1, 'Invite Only?', 'admin_signup, '),
(426, 1, 'Do you want to turn off public signups and only allow invited users to signup? If yes, you can choose to have either admins and users invite new users, or just admins. If set to yes, an invite code will be required on the signup page.', 'admin_signup, '),
(427, 1, 'Yes, admins and users must invite new users before they can signup.', 'admin_signup, '),
(428, 1, 'Yes, admins must invite new users before they can signup.', 'admin_signup, '),
(429, 1, 'No, disable the invite only feature.', 'admin_signup, '),
(430, 1, 'Should each invite code be bound to each invited email address? If set to NO, anyone with a valid invite code can signup regardless of their email address. If set to YES, anyone with a valid invite code that matches an email address that was invited can signup.', 'admin_signup, '),
(431, 1, 'Yes, check that a user\'s email address was invited before accepting their invite code.', 'admin_signup, '),
(432, 1, 'No, anyone with an invite code can signup, regardless of their email address.', 'admin_signup, '),
(433, 1, 'How many invites do users get when they signup? (If you want to give a particular user extra invites, you can do so via the <a href=\'admin_viewusers.php\'>View Users</a> page. Please enter a number between 0 and 999 below.', 'admin_signup, '),
(434, 1, 'invites are given to each user when they signup.', 'admin_signup, '),
(435, 1, 'Show \"Invite Friends\" Page?', 'admin_signup, '),
(436, 1, 'If you have selected YES, your users will be shown a page asking them to optionally invite one or more friends to signup. The \"invite friends\" feature is different from the \"invite only\" feature because \"invite friends\" simply sends an email to the invitee instead of sending them an actual invitation code. Because of this, you probably do not want to enable both \"invite friends\" and \"invite only\" features simultaneously.', 'admin_signup, '),
(437, 1, 'Yes, show invite friends page.', 'admin_signup, '),
(438, 1, 'No, do not show invite friends page.', 'admin_signup, '),
(439, 1, 'Verify Email Address?', 'admin_signup, '),
(440, 1, 'Force users to verify their email address before they can login? If set to YES, users will be sent an email with a verification link which they must click to activate their account.', 'admin_signup, '),
(441, 1, 'Yes, verify email addresses.', 'admin_signup, '),
(442, 1, 'No, do not verify email addresses.', 'admin_signup, '),
(443, 1, 'Require Users to Enter a Verification Code?', 'admin_signup, '),
(444, 1, 'If you have selected YES, an image containing a random sequence of 6 numbers will be shown to users on the signup page. Users will be required to enter these numbers into the Verification Code field before they can continue. This feature helps prevent users from trying to automatically create accounts on your system. For this feature to work properly, your server must have the GD Libraries (2.0 or higher) installed and configured to work with PHP. If you are seeing errors or users cannot signup, try turning this off.', 'admin_signup, '),
(445, 1, 'Yes, show verification code image.', 'admin_signup, '),
(446, 1, 'No, do not show verification code image.', 'admin_signup, '),
(447, 1, 'Generate Random Passwords?', 'admin_signup, '),
(448, 1, 'If you have selected YES, a random password will be created for users when they signup. The password will be emailed to them upon the completion of the signup process. This is another method of verifying users\' email addresses.', 'admin_signup, '),
(449, 1, 'Yes, generate random passwords and email to new users.', 'admin_signup, '),
(450, 1, 'No, let users choose their own passwords.', 'admin_signup, '),
(451, 1, 'Require users to agree to your terms of service?', 'admin_signup, '),
(452, 1, 'Note: If you have selected YES, users will be forced to click a checkbox during the signup process which signifies that they have read, understand, and agree to your terms of service. Enter your terms of service text in the field below. HTML is OK.', 'admin_signup, '),
(453, 1, 'Yes, make users agree to your terms of service on signup.', 'admin_signup, '),
(454, 1, 'No, users will not be shown a terms of service checkbox on signup.', 'admin_signup, '),
(455, 1, 'There are no fields in this profile category.', 'admin_signup, '),
(456, 1, 'Some users prefer to have profile addresses (URLs) that are easier to remember, more visually appealing, and more search-engine friendly. By default, your users\' URLs will appear in the \"normal\" format as demonstrated below. If you want to give them \"subdirectory URLs\", your web server must be running Apache and have mod_rewrite installed.', 'admin_url, '),
(457, 1, 'URL Style', 'admin_url, '),
(458, 1, 'After you select a URL style and click the submit button below, you will be prompted with further instructions for enabling the URL style of your choice. Please follow these instructions carefully to ensure that your URLs are working properly.', 'admin_url, '),
(459, 1, '<b>Normal URLs</b><br>Profile URL: http://www.yourdomain.com/profile.php?user=username', 'admin_url, '),
(460, 1, '<b>Subdirectory URLs</b><br>Profile URL: http://www.yourdomain.com/username', 'admin_url, '),
(461, 1, 'Normal URLs', 'admin_url, '),
(462, 1, 'Subdirectory URLs', 'admin_url, '),
(463, 1, ' - (Need help? Review the instructions <a href=\'javascript:urlhelp();\'>here</a>.)', 'admin_url, '),
(464, 1, 'URL Settings Help', 'admin_url, '),
(465, 1, 'The system is now set to use subdirectory URLs, which require an .htaccess file in your SocialEngine root directory. Copy and paste the code in the following box into a blank text file named .htaccess, and place it into your SocialEngine root directory. This is the directory on your server in which you have installed SocialEngine.', 'admin_url, '),
(466, 1, 'Close', 'admin_url, admin_templates, admin_announcements, '),
(467, 1, 'You have complete control over the look and feel of your social network. The PHP code that powers your social network is completely separate from the HTML code used for presentation. Your HTML code is stored in the templates listed below, which can be edited directly on this page. To edit a template, simply click it\'s name.<br><br><b>About the template system:</b><br>The template system uses Smarty, which is the most advanced and renown third-party PHP templating system available. Although the templates are primarily HTML, some Smarty tags are used for various purposes. For help with the templating system, please visit the <a href=\'http://smarty.php.net\' target=\'_blank\'>Smarty</a> website. Note that many of the tags you will find in the templates are actually language variables. These are used to display bits of text that have been specified in your language pack. To change these bits of text, you must edit the language file you are using in the \"lang\" directory on your server.<br><br><b>Adding your website\'s header/footer wrapper:</b><br>The simplest way to integrate your social network into your main website is to copy your website\'s header/footer HTML and paste it into the \"Header/Footer Templates\" below. To make global changes to the CSS stylesheet, you can edit \"styles.css\".', 'admin_templates, '),
(468, 1, 'Logged-in User Pages', 'admin_templates, '),
(469, 1, 'Public Pages', 'admin_templates, '),
(470, 1, 'Header/Footer Templates', 'admin_templates, '),
(471, 1, 'Edit Template', 'admin_templates, '),
(472, 1, 'The HTML and Smarty code for this template is displayed below. After making your desired changes to the template, be sure to click the \"Save Changes\" button. For help with Smarty, see the <a href=\'http://smarty.php.net\' target=\'_blank\'>official website</a> and <a href=\'http://smarty.php.net/crashcourse.php\' target=\'_blank\'>crash course</a>.', 'admin_templates, '),
(473, 1, 'The file you specified is not a valid template file.', 'admin_templates, '),
(474, 1, 'The template you specified could not be found.', 'admin_templates, '),
(475, 1, 'The template you specified could not be read. Try setting full permissions (CHMOD 777) to this file and the templates directory.', 'admin_templates, '),
(476, 1, 'The template you specified is not writable. Try setting full permissions (CHMOD 777) to this file and the templates directory.', 'admin_templates, '),
(477, 1, 'Use this page to monitor network usage and traffic patterns. Begin by selecting one of the types of available statistics below.', 'admin_stats, '),
(478, 1, 'Quick Summary', 'admin_stats, '),
(479, 1, 'Visits/Impressions', 'admin_stats, '),
(480, 1, 'New Logins', 'admin_stats, '),
(481, 1, 'New Signups', 'admin_stats, '),
(482, 1, 'New Friendships', 'admin_stats, '),
(483, 1, 'Network Usage', 'admin_stats, '),
(484, 1, 'Other Stats', 'admin_stats, '),
(485, 1, 'Referring URLs', 'admin_stats, '),
(486, 1, 'Space Used', 'admin_stats, '),
(487, 1, 'Last Period', 'admin_stats, '),
(488, 1, 'Period:', 'admin_stats, '),
(489, 1, 'This Week (Daily)', 'admin_stats, '),
(490, 1, 'This Month (Daily)', 'admin_stats, '),
(491, 1, 'This Year (Monthly)', 'admin_stats, '),
(492, 1, 'Refresh', 'admin_stats, '),
(493, 1, 'Next Period', 'admin_stats, '),
(494, 1, 'URL', 'admin_stats, '),
(495, 1, 'These are the 100 most common referring URLs tracked from your <a href=\'../home.php\' target=\'_blank\'>home.php</a> file.<br>This indicates that most new traffic is coming from the following URLs.<br>Clearing the list periodically will give you fresh referrer data.', 'admin_stats, '),
(496, 1, 'clear now', 'admin_stats, '),
(497, 1, 'Hits', 'admin_stats, '),
(498, 1, 'The referrer URL list is currently empty.', 'admin_stats, '),
(499, 1, 'Uploaded Media:', 'admin_stats, '),
(500, 1, 'Database Size:', 'admin_stats, '),
(501, 1, 'Total Space Used (Estimated):', 'admin_stats, '),
(502, 1, 'Quick Network Statistics', 'admin_stats, '),
(503, 1, 'The following data is a quick snapshot of your social network.<br>The data does not include any items that have been deleted.', 'admin_stats, '),
(504, 1, 'Total Users:', 'admin_stats, '),
(505, 1, '%1\$d users', 'admin_stats, '),
(506, 1, '%1\$d messages', 'admin_stats, '),
(507, 1, '%1\$d comments', 'admin_stats, '),
(508, 1, 'Page Views', 'admin_stats, '),
(510, 1, 'Hello, %1\$s!', 'home, '),
(509, 1, '%1\$s\'s Profile', 'user_friends_requests_outgoing, user_friends_requests, user_friends, search_advanced, search, profile, home, '),
(660, 1, 'Remember Me', 'login, home, '),
(512, 1, 'Week of', 'admin_stats, '),
(513, 1, 'This page allows you to change the content of email messages sent by the system.', 'admin_emails, '),
(514, 1, 'From Address', 'admin_emails, '),
(515, 1, 'Enter the name and email address you want the emails from the system to come from in the fields below.', 'admin_emails, '),
(516, 1, 'From Name:', 'admin_emails, '),
(517, 1, 'From Address:', 'admin_emails, '),
(518, 1, 'Invite Code Email', 'admin_emails, '),
(519, 1, 'This is the email that gets sent if you allow users to join by invite only.', 'admin_emails, '),
(520, 1, 'Subject', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_emails, admin_announcements, '),
(521, 1, 'Message', 'user_messages_new, help_contact, admin_emails, '),
(523, 1, 'Invitation Email', 'admin_emails, '),
(524, 1, 'This is the email that gets sent when users invite their friends to join during the signup process.', 'admin_emails, '),
(526, 1, 'Verification Email', 'admin_emails, '),
(527, 1, 'This is the email that gets sent to users to verify their email addresses.', 'admin_emails, '),
(529, 1, 'New Password Email', 'admin_emails, '),
(530, 1, 'This is the email that gets sent if you have chosen to create a random password for users upon signup.', 'admin_emails, '),
(532, 1, 'Welcome Email', 'admin_emails, '),
(533, 1, 'This is the email that gets sent when a user signs up. Please note that if you have email verification enabled, the password variable is <b>not available</b>. This is due to the fact that passwords are securely encrypted upon signup and cannot be unencrypted when a user verifies their email address and the welcome email is sent.', 'admin_emails, '),
(535, 1, 'Lost Password Email', 'admin_emails, '),
(536, 1, 'This is the email that gets sent if a user forgets their password and requests to create a new one.', 'admin_emails, '),
(538, 1, 'Friend Request Email', 'admin_emails, '),
(539, 1, 'This is the email that gets sent to a user when they are added as a friend by another user.', 'admin_emails, '),
(541, 1, 'New Message Email', 'admin_emails, '),
(542, 1, 'This is the email that gets sent to a user when they receive a new message.', 'admin_emails, '),
(544, 1, 'New Profile Comment Email', 'admin_emails, '),
(545, 1, 'This is the email that gets sent to a user when a new comment is posted on their profile.', 'admin_emails, '),
(547, 1, 'The recent activity feed is an auto-updating list of actions that have recently occurred on your social network. This information is displayed (by default) on users\' \"My Home\" page. Also, each user\'s own personal activity list will be displayed on their profile page if enabled below. Please note that some of the settings here are not retroactive, meaning that changes you make here may not affect old feed items.', 'admin_activity, '),
(548, 1, 'Which actions do you want to include in the activity list?', 'admin_activity, '),
(549, 1, 'All of the possible actions that can appear in your recent activity feed are shown below. You can choose not to include them in the recent activity feed by unchecking the appropriate box, or you can alter their text. Note that some of the actions have variables that are replaced by the system (e.g. username, photo, comment). Also, note that installing new plugins may add new actions. Unchecking the designated checkbox will disable that action type, however any previously recorded actions of that type will not be deleted from the feed. You can also decide whether or not to allow users the option of disabling the activity feed type by checking or unchecking the appropriate box.<br><br><b>Note: If you are using more than one language on your social network, you can provide translations for these activity feed items on the <a href=\'admin_language.php\'>Language Settings</a> page.</b>', 'admin_activity, '),
(550, 1, 'Action Text', 'admin_activity, '),
(551, 1, 'Keyword', 'admin_activity, '),
(552, 1, 'How many actions should be stored about each user?', 'admin_activity, '),
(553, 1, 'How many recent actions do you want to store in the database for each user? A higher value will show more information about each user\'s activity, while a lower value will increase database performance. Note: If the number of actions you want to display on each user\'s profile is less than the number of actions you want to store in the database, you can edit the \"profile.tpl\" template file to limit the number of actions dispalyed.', 'admin_activity, '),
(554, 1, 'action(s) stored in the database and published on each user\'s profile page', 'admin_activity, '),
(555, 1, 'Feed Limits', 'admin_activity, '),
(556, 1, 'How many total actions do you want to display in the recent activity feed?', 'admin_activity, '),
(557, 1, 'action(s) published in the recent activity feed', 'admin_activity, '),
(558, 1, 'How long do want items to be visible in the recent activity feed? A shorter amount of time will generally result in a shorter list of actions. For small social networks, a longer time period may be more appropriate.', 'admin_activity, '),
(559, 1, 'minute(s)', 'admin_activity, '),
(560, 1, 'hour(s)', 'admin_activity, '),
(561, 1, 'day(s)', 'admin_activity, '),
(562, 1, 'week(s)', 'admin_activity, '),
(563, 1, 'month', 'admin_activity, '),
(564, 1, 'How many actions per user can be shown on the recent activity feed? It\'s important to have a nice mix of actions from various users on your social network, so here you can set a limit on the number of actions published about each user at any given time. For smaller social networks, a higher number of published actions per user might be more appropriate.', 'admin_activity, '),
(565, 1, 'action(s) published per user in the recent activity feed', 'admin_activity, '),
(566, 1, 'Should users be allowed to delete actions published about themselves?', 'admin_activity, '),
(567, 1, 'Do you want to give users the option of deleting actions published about themselves? This is generally an important freedom to give users because it helps to maintain their privacy.', 'admin_activity, '),
(568, 1, 'Yes, allow users to delete actions about themselves.', 'admin_activity, '),
(569, 1, 'No, users may not delete actions about themselves.', 'admin_activity, '),
(570, 1, 'Whose actions should users see in the recent activity list?', 'admin_activity, '),
(571, 1, 'When a user is looking at the recent activity feed, whose actions should they see? For smaller networks, it may make more sense to show recent actions from \"All Registered Users\" so the feed is quickly populated.', 'admin_activity, '),
(572, 1, 'Should users be able to prevent certain action types from being published?', 'admin_activity, '),
(573, 1, 'Do you want to allow users to prevent specific action types from being published about them? If yes, a setting will appear on your users\' account settings page allowing them to choose which action types to NOT publish in the recent activity feed.', 'admin_activity, '),
(574, 1, 'Yes, users can specify which action types will not be published about them.', 'admin_activity, '),
(575, 1, 'No, users cannot specify what actions will be published or not published about them.', 'admin_activity, '),
(576, 1, 'Include this action in the recent activity feed', 'admin_activity, '),
(577, 1, 'Display enable/disable option on user settings page', 'admin_activity, '),
(700001, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> logged in.', 'admin_activity, '),
(700002, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> updated their profile photo.<div class=\'recentaction_div_media\'>[media]</div>', 'user_home, profile, network, home, admin_viewusers_edit, admin_activity, '),
(700003, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> updated their profile.', 'user_home, profile, network, home, admin_activity, '),
(700004, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> posted a comment on <a href=\'profile.php?user=%3\$s&v=comments\'>%4\$s</a>\'s profile:<div class=\'recentaction_div\'>%5\$s</div>', 'user_home, profile, network, home, admin_activity, '),
(700005, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> and <a href=\'profile.php?user=%3\$s\'>%4\$s</a> are now friends.', 'user_home, profile, network, home, admin_viewusers_edit, admin_activity, '),
(700006, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> signed up.', 'user_home, profile, network, home, admin_viewusers_edit, admin_activity, '),
(700007, 1, '<a href=\'profile.php?user=%1\$s\'>%2\$s</a> %3\$s', 'user_home, profile, network, home, admin_activity, '),
(578, 1, 'Available Variables:', 'admin_emails, admin_activity, '),
(579, 1, 'MONTH', 'user_editprofile_style, user_editprofile_photo, user_editprofile, signup, search_advanced, admin_subnetworks, admin_signup, admin_profile, admin_fields, '),
(580, 1, 'DAY', 'user_editprofile_style, user_editprofile_photo, user_editprofile, signup, search_advanced, admin_subnetworks, admin_signup, admin_profile, admin_fields, '),
(581, 1, 'YEAR', 'user_editprofile_style, user_editprofile_photo, user_editprofile, signup, search_advanced, admin_subnetworks, admin_signup, admin_profile, admin_fields, '),
(582, 1, 'There are no language variables in this language matching your search phrase.', 'admin_language_edit, '),
(583, 1, 'You can use announcements to get a message out to all the users on your social network. You can submit announcements via email or news items.', 'admin_announcements, '),
(584, 1, 'Send Email Announcement', 'admin_announcements, '),
(585, 1, 'Your announcement will be sent as an email to all of the users on your social network. If you have many users, this process may take some time to complete.', 'admin_announcements, '),
(586, 1, 'Post News Item', 'admin_announcements, '),
(587, 1, 'Your announcement will be posted on your social network portal page. Regardless of the size of your social network, this process is instantaneous. If you have posted any news items in the past, they will be listed below. If you have included HTML in your news items, it will not be rendered below but will display properly on your portal page.', 'admin_announcements, '),
(588, 1, 'Contents', 'admin_announcements, '),
(589, 1, 'Untitled', 'admin_announcements, '),
(590, 1, 'No Date Provided', 'admin_announcements, '),
(591, 1, 'move down', 'admin_announcements, '),
(592, 1, 'Post News Item', 'admin_announcements, '),
(593, 1, 'Please complete the following form to post your news item.', 'admin_announcements, '),
(594, 1, '(date will be displayed exactly as you enter it above)', 'admin_announcements, '),
(595, 1, '(HTML OK)', 'admin_announcements, '),
(596, 1, 'Save News Item', 'admin_announcements, '),
(597, 1, 'Edit News Item', 'admin_announcements, '),
(598, 1, 'Delete News Item', 'admin_announcements, '),
(599, 1, 'Are you sure you want to delete this news announcement?', 'admin_announcements, '),
(600, 1, 'Use this form to compose an email message to be sent to every registered user on the social network. When you click the send button below, the system will begin looping through the user database and emailing the message to each user. Increasing the number of emails per page will make the process complete more quickly. However, if your server is currently under stress or you\'re simply not concerned about time, selecting a lower number of emails per page will reduce the risk of a timeout.', 'admin_announcements, '),
(601, 1, 'From', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_announcements, '),
(602, 1, 'Emails Per Page', 'admin_announcements, '),
(603, 1, 'Recipients', 'admin_announcements, '),
(604, 1, 'Select which users will receive this email announcement. By default, all <a href=\'admin_levels.php\'>user levels</a> and <a href=\'admin_subnetworks.php\'>subnetworks</a> are selected - this means that every user on your social network will receive this announcement. Use CTRL-click to select or deselect multiple user levels or subnetworks. If a user matches any user level OR subnetwork you have selected here, they will receive this announcement.', 'admin_announcements, '),
(605, 1, 'Send Announcement', 'admin_announcements, '),
(606, 1, 'Please provide a message body for this announcement.', 'admin_announcements, '),
(607, 1, 'Please select at least one user level or subnetwork that will receive this announcement.', 'admin_announcements, '),
(608, 1, 'Emailing in Progress', 'admin_announcements, '),
(609, 1, 'Your announcement is being sent to users. Do not navigate away from this page until the process is complete. Please wait...', 'admin_announcements, '),
(610, 1, 'Emailing Complete', 'admin_announcements, '),
(611, 1, 'The emailing process has been completed. All users on your social network have been sent an email with your announcement.', 'admin_announcements, '),
(612, 1, 'Your social network has the ability to organize users into \"subnetworks\" based on profile information they have in common with each other. You can use this to limit access and privacy between subnetworks, display subnetwork-specific content in your templates, or to simply organize your users.', 'admin_subnetworks, '),
(613, 1, 'Show Detailed Instructions', 'admin_subnetworks, '),
(614, 1, '<b>Important:</b> The requirement fields you select must be set to \"Required on Signup\" on the <a href=\'admin_signup.php\'>Signup Settings</a> page. If they are not set to \"Required on Signup\", they may not appear during the signup process and users will not have an opportunity to fill them out. Because they have not filled out your requirement fields, they will automatically be placed in the \"Default Subnetwork\" until they fill out the fields. If you already have users in subnetworks on your social network and you change the requirement fields or the requirements of a specific subnetwork, users will remain in the same subnetworks (based on the original requirements or differentiation fields you had set) until their profile information is updated. All users that are not sorted into a subnetwork will be placed into the \"Default Subnetwork\" until their profile information is updated and matches the requirements of a different subnetwork. When a subnetwork is deleted, users within the deleted subnetwork will be moved into the \"Default Subnetwork\".<br><br><b>Example:</b> If you wanted to create two subnetworks - one for male users and one for female users - you must create a profile field called \"Gender\" and use it as your primary requirement field below. If you want to have four subnetworks - males in California, females in California, males outside California, females outside California - you should create a profile field called \"location\" and use it as your secondary requirement field. Then, create subnetworks with the appropriate requirements so that these four subnetworks are mutually exclusive.<br><br><b>Notes:</b> If you base your subnetworks on a Birthday (Age) field (such as older/younger than 18 years old), your users will not be automatically switched from one subnetwork (younger than 18 years old) to another (older than 18). They will need to update their profile. Alternatively, if you make your primary requirement field \"Profile Category\", be aware that your secondary requirement field may not apply to all profile categories and therefore may not be visible by some users.', 'admin_subnetworks, '),
(615, 1, 'Primary Requirement Field:', 'admin_subnetworks, '),
(616, 1, 'Email Address', 'user_account, admin_subnetworks, '),
(617, 1, 'Profile Category', 'admin_viewusers_edit, admin_subnetworks, '),
(618, 1, 'Secondary Requirement Field:', 'admin_subnetworks, '),
(619, 1, 'Update', 'admin_subnetworks, '),
(620, 1, 'Add New Subnetwork', 'admin_subnetworks, '),
(621, 1, 'Requirements', 'admin_subnetworks, '),
(622, 1, 'Default Subnetwork', 'admin_subnetworks, '),
(623, 1, 'Users Not In Another Subnetwork', 'admin_subnetworks, '),
(624, 1, 'Add Subnetwork', 'admin_subnetworks, '),
(625, 1, 'To create/modify a subnetwork, complete the following form. You will need to specify who can belong to this subnetwork. You can do this by creating requirements. Note that you must specify a requirement with regards to your primary requirement field. Requirements based on the secondary requirement field are optional. The use of wildcards (*) is accepted when using the \"is equal to (==)\" and \"is not equal to (!=)\" operators. String values (such as words and phrases) will NOT be case sensitive. Please make sure that subnetwork requirements are mutually exclusive; that is, make sure users can only be placed in one subnetwork based on the requirements you provide. If the requirements overlap with another subnetwork\'s requirements, users will be randomly placed into one of the overlapping subnetworks.', 'admin_subnetworks, '),
(626, 1, 'is equal to ( == )', 'admin_subnetworks, '),
(627, 1, 'is not equal to ( != )', 'admin_subnetworks, '),
(628, 1, 'is greater than ( > )', 'admin_subnetworks, '),
(629, 1, 'is less than ( < )', 'admin_subnetworks, '),
(630, 1, 'is greater than or equal to ( >= )', 'admin_subnetworks, '),
(631, 1, 'is less than or equal to ( <= )', 'admin_subnetworks, '),
(632, 1, 'And', ''),
(633, 1, 'Please specify a name for this subnetwork.', 'admin_subnetworks, '),
(634, 1, 'You must specify a primary requirement.', 'admin_subnetworks, '),
(635, 1, 'Edit Subnetwork', 'admin_subnetworks, '),
(636, 1, 'Delete Subnetwork', 'admin_subnetworks, '),
(637, 1, 'Are you sure you want to delete this subnetwork? All users in this subnetwork will be moved to the default subnetwork.', 'admin_subnetworks, '),
(638, 1, 'The subnetwork you selected has been deleted.', 'admin_subnetworks, '),
(639, 1, 'An Error Has Occurred', 'profile, '),
(640, 1, 'You do not have permission to view this page. The user whose page you are trying to view has placed you on their blocklist.', 'profile, '),
(641, 1, 'Return', 'profile, '),
(642, 1, 'Social Network', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(643, 1, 'Search:', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(644, 1, 'Go', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(645, 1, 'Home', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(646, 1, 'Search', 'user_friends, search, profile, '),
(647, 1, 'Invite', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, ')") or die("Insert: se_languagevars (2)<br>Error: ".mysql_error());
mysql_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (648, 1, 'Help', ''),
(649, 1, 'Hello, %1\$s', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(650, 1, 'Signup', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(651, 1, 'My Account', ''),
(652, 1, 'Profile', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(653, 1, 'Friends', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(654, 1, 'Messages', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(655, 1, 'Settings', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(656, 1, 'You must be logged in to view this page. <a href=\'login.php\'>Click here</a> to login.', 'profile, '),
(657, 1, 'This page is an example of what your social network\'s portal can look like. Various statistics about your social network can be displayed, as exemplified below. This gives users a convenient way to find the newest content and interesting people on your social network. You can also use this page to display news or any other content you place into the template. You can replace this text by going to the \"Language Settings\" page in your admin panel, opening your language pack, and editing the language phrase #657.', 'home, '),
(658, 1, 'Account Login', 'login, '),
(673, 1, 'Welcome to the social network! If you already have an account, you can login below.<br>If you don\'t have an account, you can <a href=\'signup.php\'>sign up here</a>!', 'login, '),
(674, 1, '<br>If you are still waiting to receive your verification email, <a href=\'signup_verify.php?task=resend\'>click here</a> to resend it.', 'login, '),
(659, 1, 'Member Login', 'home, '),
(511, 1, 'Network Statistics', 'home, '),
(661, 1, 'Members: %1\$d members', 'home, '),
(662, 1, 'Friendships: %1\$d friends', 'home, '),
(663, 1, 'Comments: %1\$d comments', 'home, '),
(664, 1, 'Recent News', 'user_home, home, '),
(665, 1, 'People Online', 'user_home, home, '),
(666, 1, 'Newest Members', 'network, home, '),
(667, 1, 'No members have signed up yet.', 'home, '),
(668, 1, 'Popular Members', 'home, '),
(669, 1, '%1\$d friends', 'home, '),
(670, 1, 'No members have become friends yet.', 'home, '),
(671, 1, 'Members Last Logged In', 'home, '),
(672, 1, 'No members have logged in yet.', 'home, '),
(675, 1, 'Forgot password?', 'login, '),
(676, 1, 'The login details you provided were invalid. Please try again.', 'login, '),
(677, 1, 'The administrator has disabled your account.', ''),
(678, 1, 'You have not yet verified your email address. If you would like to have the email resent to you, please <a href=\'signup_verify.php?task=resend\'>click here</a>.', ''),
(679, 1, 'Create Your Account', 'signup, '),
(680, 1, 'Welcome to the social network! To create your account, please provide the following information.', 'signup, '),
(681, 1, 'Login Information', 'signup, '),
(682, 1, 'You will use your email address to login.', 'signup, '),
(683, 1, 'Enter your password again for confirmation.', 'signup, '),
(684, 1, 'Account Information', 'signup, '),
(685, 1, 'This is the name others see when they view your profile. If you decide to change your username, you must enter one that has not already been taken by another person.', 'signup, '),
(686, 1, 'This will be the name people see when they view your profile.', 'signup, '),
(687, 1, 'Language', 'signup, '),
(688, 1, 'Security Information', 'signup, '),
(689, 1, 'Invitation Code', 'signup, '),
(690, 1, 'Security Code', 'signup, '),
(691, 1, 'Enter the numbers you see in this image into the field to its left. This helps keep our site free of spam. If you have trouble reading the code, click it to generate a new one.', 'signup, '),
(692, 1, 'I have read and agree to the <a href=\'help_tos.php\' target=\'_blank\'>terms of service</a>.', 'signup, '),
(693, 1, 'Continue...', 'signup, '),
(694, 1, 'Please ensure your username is alphanumeric.', ''),
(695, 1, 'The username you selected is banned. Please choose another.', ''),
(696, 1, 'The username you selected is reserved. Please choose another.', ''),
(697, 1, 'The email address you provided is banned. Please provide another.', ''),
(698, 1, 'The email address you provided is not a valid email address.', 'help_contact, '),
(699, 1, 'The username you selected has already been taken. Please choose another.', 'signup, '),
(700, 1, 'The email address you provided has already been taken. Please provide another.', 'signup, '),
(701, 1, 'The old password you provided is incorrect. Please try again.', 'user_account_pass, '),
(702, 1, 'Please be sure you have provided the same password in both new password fields.', 'lostpass_reset, '),
(703, 1, 'Please provide a password with at least 6 letters or numbers.', 'lostpass_reset, '),
(704, 1, 'Please ensure your password is alphanumeric.', 'lostpass_reset, '),
(705, 1, 'The invite code and email address combination you have entered is invalid.', ''),
(706, 1, 'The invite code you have entered is invalid.', ''),
(707, 1, 'You must agree to the terms of service to signup.', 'signup, '),
(708, 1, 'Please make sure you have correctly entered the verification code.', 'signup, '),
(709, 1, 'Account Type', 'user_account, signup, '),
(710, 1, 'Create Your Profile', 'signup, '),
(711, 1, 'Tell us a little more about yourself. Fields marked with an asterisk (*) are required.', 'signup, '),
(800002, 1, 'Reporting Abuse', 'help, admin_faq, '),
(712, 1, 'Upload Your Photo', 'signup, '),
(713, 1, 'Upload a photo of yourself from your computer. This will be the icon other people will see on your profile.', 'user_editprofile_photo, signup, '),
(714, 1, 'Upload', 'user_editprofile_photo, signup, '),
(715, 1, 'To upload your photo, click the \"Browse\" button, locate the photo on your computer, and click the \"Upload\" button. Your photo must be less than 4 MB in size and must be one of these types:', 'user_editprofile_photo, signup, '),
(716, 1, 'Keep This Photo', 'signup, '),
(717, 1, 'Skip This Step', 'signup, '),
(718, 1, 'Upload failed. Please try again. If this problem persists, please contact the administrator for assistance.', ''),
(719, 1, 'The size of your uploaded file is greater than the maximum allowed filesize.', ''),
(720, 1, 'Your file\'s filetype is not allowed.', ''),
(721, 1, 'Your image\'s dimensions are greater than the maximum allowed width and height.', ''),
(722, 1, 'Invite Your Friends', 'signup, '),
(723, 1, 'Invite your friends to join! Enter the email addresses of your friends separated by commas in the field below.', 'signup, '),
(724, 1, 'Send Invitations To:', 'signup, '),
(725, 1, 'Enter your friends\' email addresses (up to 5) below, separated by commas.', 'signup, '),
(726, 1, 'Your Message', 'signup, '),
(727, 1, 'If you want to include a personal message in your invitations, enter it here. (optional)', 'signup, '),
(728, 1, 'Send Invitations', 'signup, invite, '),
(729, 1, 'Signup Complete!', 'signup, '),
(730, 1, 'Congratulations! You have successfully created your account.', 'signup, '),
(731, 1, 'You will be able to login after you have been approved by an administrator.', 'signup, '),
(732, 1, 'Your password has been sent to the email address you provided.', 'signup, '),
(733, 1, 'Click the button below to login.', 'signup, '),
(734, 1, 'An email has been sent to the email address you provided. Please follow the link in that email to verify your email address.', 'signup, '),
(735, 1, 'Return to Home', 'signup, '),
(736, 1, '(Age)', 'search_advanced, admin_subnetworks, '),
(737, 1, 'What\'s New?', 'user_home, home, '),
(738, 1, 'There has not been any recent activity on the social network.', 'user_home, network, '),
(739, 1, 'Profile Stats', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(740, 1, '%1\$d profile views', 'user_home, profile, '),
(741, 1, 'reset', 'user_home, '),
(742, 1, 'My Status', 'user_home, '),
(743, 1, 'Update your status.', 'user_home, profile, '),
(744, 1, 'is', 'user_home, profile, '),
(745, 1, 'update', 'user_home, profile, '),
(746, 1, 'save', 'user_home, profile, '),
(747, 1, 'cancel', 'user_home, profile, '),
(748, 1, 'The email you have entered was not found in the database. Please try again.', ''),
(749, 1, 'Send Password', 'lostpass, '),
(750, 1, 'This link is invalid or expired. Please <a href=\'lostpass.php\'>resubmit</a> your password request and follow the link sent to your email address.', ''),
(751, 1, 'Your password has been reset. <a href=\'login.php\'>Click here</a> to login.', 'lostpass_reset, '),
(752, 1, 'FAQ', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(753, 1, 'Terms of Service', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(754, 1, 'Contact Us', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(755, 1, 'Account Settings', 'user_account, '),
(756, 1, 'Change Password', 'user_account_privacy, user_account_pass, user_account_delete, user_account, '),
(757, 1, 'Delete Account', 'user_account_privacy, user_account_pass, user_account_delete, user_account, '),
(758, 1, 'If you want to change your account password, please complete the following form.', 'user_account_pass, '),
(759, 1, 'Delete Account?', 'user_account_delete, '),
(760, 1, 'Are you sure you want to delete your account? All of your account data, including any files you have uploaded, will be permanently deleted! Upon deletion of your account, you will be automatically logged out.', 'user_account_delete, '),
(761, 1, 'Delete My Account', 'user_account_delete, '),
(762, 1, 'Photo', 'user_editprofile_style, user_editprofile_photo, user_editprofile, '),
(763, 1, 'Profile Style', 'user_editprofile_style, user_editprofile_photo, user_editprofile, '),
(764, 1, 'Edit Profile: %1\$s', 'user_editprofile, '),
(765, 1, 'Please provide some information about yourself.', 'user_editprofile, '),
(766, 1, 'Changing this field may change which network you belong to.<br>You currently belong to: %1\$s', 'user_editprofile, user_account, '),
(767, 1, 'Your network has been changed from %1\$s to %2\$s.', 'user_editprofile, '),
(768, 1, 'Status', 'profile, '),
(769, 1, 'Edit My Photo', 'user_editprofile_photo, '),
(770, 1, 'Current Photo', 'user_editprofile_photo, '),
(771, 1, 'remove photo', 'user_editprofile_photo, '),
(772, 1, 'Upload Photo', 'user_editprofile_photo, '),
(773, 1, '%1\$d second(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(774, 1, '%1\$d minute(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(775, 1, '%1\$d hour(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(776, 1, '%1\$d day(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(777, 1, '%1\$d week(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(778, 1, '%1\$d month(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(779, 1, '%1\$d year(s) ago', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(780, 1, 'Inbox', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(781, 1, 'Sent Messages', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(782, 1, 'My Message Inbox', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(783, 1, 'You have %1\$s unread conversation(s) in your inbox.', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(784, 1, 'Compose New Message', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(785, 1, 'Your inbox is empty. When you receive messages in the future, they will be listed here.', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(786, 1, '%1\$s\'s Profile', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(787, 1, 'reply', 'profile, '),
(788, 1, 'Delete Selected', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, admin_viewusers, admin_viewreports, '),
(789, 1, 'Create your new message with the form below.<br>You can specify multiple recipients (up to %1\$d) - you can either select a friend from the autosuggestion area or simply type a user\'s name and press Enter.', 'user_messages_new, '),
(790, 1, 'To', 'user_messages_outbox, user_messages_new, '),
(791, 1, 'Send Message', 'user_messages_view, user_messages_new, '),
(792, 1, 'Maximum Recipients', 'admin_levels_messagesettings, '),
(793, 1, 'How many recipients can a user send a message to at one time?', 'admin_levels_messagesettings, '),
(794, 1, 'recipient(s) at a time', 'admin_levels_messagesettings, '),
(795, 1, 'Please enter a valid recipient.', ''),
(796, 1, 'You must enter a message.', 'user_messages_view, '),
(797, 1, 'My Sent Messages', 'user_messages_outbox, '),
(798, 1, 'You have %1\$d total message(s) in your outbox.', 'user_messages_outbox, '),
(799, 1, 'Your outbox is empty. When you send messages in the future, they will be listed here.', 'user_messages_outbox, '),
(800, 1, '%1\$s recipients', 'user_messages_outbox, '),
(801, 1, 'Between %1\$s and You', 'user_messages_view, '),
(802, 1, 'Reply:', 'user_messages_view, '),
(803, 1, 'Reply All:', 'user_messages_view, '),
(804, 1, 'Your message has been sent!', 'user_messages_new, '),
(805, 1, 'Back to Inbox', 'user_messages_view, '),
(806, 1, 'Back to Outbox', 'user_messages_view, '),
(807, 1, 'You do not have permission to view this page. You have been banned from the network.', ''),
(808, 1, 'If necessary, you can make changes to your account settings below.', 'user_account, '),
(809, 1, 'This is the name others see when they view your profile. If you decide to change your username, you must enter one that has not already been taken by another person.', 'user_account, '),
(810, 1, 'Note that changing your username will clear your recent activity feed.', 'user_account, '),
(811, 1, 'Recent Activity Privacy', 'user_account_privacy, '),
(812, 1, 'Which of the following things do you want to have published about you in the <a href=\'user_home.php\'>recent activity feed</a>?<br>Note that changing this setting will only affect future news feed items.', 'user_account_privacy, '),
(813, 1, 'Block List', 'user_account_privacy, '),
(814, 1, 'Adding a person to your block list makes your profile (and all of your other content) unviewable to them. Any connections you have to the blocked person will be canceled. To add someone to your block list, click the \"Add New Person\" link and enter their username. If you enter a username of someone that does not exist or has been deleted, they will not be added to your block list.', 'user_account_privacy, '),
(815, 1, 'Add New Person', 'user_account_privacy, '),
(700008, 1, 'Logging in.', ''),
(700009, 1, 'Changing profile photo.', 'user_home, user_account_privacy, '),
(700010, 1, 'Editing profile', 'user_home, user_account_privacy, '),
(700011, 1, 'Posting a profile comment.', 'user_home, user_account_privacy, '),
(700012, 1, 'Adding a friend', 'user_home, user_account_privacy, '),
(700013, 1, 'Signing Up.', 'user_home, '),
(700014, 1, 'Changing status.', 'user_home, user_account_privacy, '),
(816, 1, 'Your changes have been saved. Before your new email becomes active, you must follow the link in the email sent to you.', ''),
(817, 1, 'Your changes have been saved. Before your new email becomes active, you must follow the link in the email sent to you. Once you verify your email address, your network will be changed from %1\$s to %1\$s.', ''),
(818, 1, 'Waiting for verification for %1\$s.', 'user_account, '),
(819, 1, 'Your changes have been saved and your network has been changed from %1\$s to %2\$s.', ''),
(820, 1, 'Allow username change?', 'admin_levels_usersettings, '),
(821, 1, 'Enable this feature if you want to allow your users to be able to change their username. Note that if you have usernames disabled on the <a href=\'admin_general.php\'>General Settings</a> page, this feature is irrelevant.', 'admin_levels_usersettings, '),
(822, 1, 'Yes, allow users to change their username.', 'admin_levels_usersettings, '),
(823, 1, 'No, do not allow users to change their username.', 'admin_levels_usersettings, '),
(824, 1, 'Allow account deletion?', 'admin_levels_usersettings, '),
(825, 1, 'Enable this feature if you would like to allow your users to delete their account manually.', 'admin_levels_usersettings, '),
(826, 1, 'Yes, allow users to delete their account.', 'admin_levels_usersettings, '),
(827, 1, 'No, do not allow users to delete their account.', 'admin_levels_usersettings, '),
(828, 1, 'The profile you are looking for has been deleted or does not exist.', 'profile, '),
(829, 1, 'Write Something...', 'profile, '),
(830, 1, 'Posting...', 'profile, '),
(831, 1, 'Please enter a message.', 'profile, '),
(832, 1, 'You have entered the wrong security code.', 'profile, '),
(833, 1, 'Post Comment', 'profile, '),
(834, 1, 'message', 'profile, '),
(835, 1, 'Anonymous', 'profile, '),
(836, 1, 'View %1\$s\'s Friends', 'user_friends, '),
(837, 1, 'Remove from My Friends', 'user_friends, profile, '),
(838, 1, 'Add to My Friends', 'profile, '),
(839, 1, 'Send Message', 'user_friends_requests_outgoing, user_friends_requests, user_friends, profile, help_contact, '),
(840, 1, 'Report this Person', 'profile, '),
(841, 1, 'Unblock this Person', 'profile, '),
(842, 1, 'Block this Person', 'profile, '),
(843, 1, 'Private Profile', 'profile, '),
(844, 1, 'You do not have permission to view this profile.', 'profile, '),
(845, 1, '%1\$s is online.', 'profile, '),
(846, 1, 'Profile Views:', 'profile, '),
(847, 1, 'Friends:', 'profile, '),
(848, 1, '%1\$d friends', 'profile, '),
(849, 1, 'Updated', 'user_friends_requests_outgoing, user_friends_requests, user_friends, profile, '),
(850, 1, 'Signup Date:', 'profile, '),
(851, 1, 'Recent Activity', 'profile, admin_viewusers_edit, '),
(852, 1, '%1\$d years old', 'profile, '),
(853, 1, 'view all friends', ''),
(854, 1, 'Comments', 'profile, '),
(855, 1, 'view all comments', ''),
(856, 1, 'Enter the numbers you see to the left. If you have trouble reading the numbers, click them to generate new ones.', 'profile, invite, '),
(857, 1, 'Notify an Administrator', 'profile, '),
(858, 1, 'Please complete the following form to notify the administration of this page.', 'user_report, '),
(859, 1, 'What are you reporting?', 'user_report, '),
(860, 1, 'Spam', 'user_report, admin_viewreports, '),
(861, 1, 'Inappropriate Content', 'user_report, admin_viewreports, '),
(862, 1, 'Abuse', 'user_report, admin_viewreports, '),
(863, 1, 'Other', 'user_report, user_friends_manage, admin_viewreports, '),
(864, 1, 'Please give us a short description of the problem.', 'user_report, '),
(865, 1, 'Send Report', 'user_report, '),
(866, 1, 'Thank you for your report. We have received it and will process it as soon as possible.', 'user_report, '),
(867, 1, 'You have successfully unblocked %1\$s.', 'user_friends_block, '),
(868, 1, 'Block User', 'profile, '),
(869, 1, 'Unblock User', 'profile, '),
(870, 1, 'Are you sure you want to unblock %1\$s?', 'user_friends_block, '),
(871, 1, 'Unblock', 'user_friends_block, '),
(872, 1, 'You have successfully blocked %1\$s.', 'user_friends_block, '),
(873, 1, 'Are you sure you want to block %1\$s?', 'user_friends_block, '),
(874, 1, 'Block', 'user_friends_block, '),
(875, 1, 'Awaiting Friendship Confirmation', 'profile, '),
(876, 1, 'Add to My Friends', 'profile, '),
(877, 1, 'Are you sure you want to remove %1\$s from your friends?', 'user_friends_manage, '),
(878, 1, 'A message has been sent to this user to confirm your friendship.', 'user_friends_manage, '),
(879, 1, 'This user has been added to your friendlist.', 'user_friends_manage, '),
(880, 1, 'You are about to add %1\$s to your friends. If you add this person to your friends, they will be able to see your profile (even if it\'s viewable by friends only). Are you sure you want to add %1\$s to your friends?', 'user_friends_manage, '),
(881, 1, 'Tell us more about how you know %1\$s.', 'user_friends_manage, '),
(882, 1, 'Friend Type:', 'user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends, profile, '),
(883, 1, 'How do you know this person?', 'user_friends_manage, '),
(884, 1, 'Add Friend', 'user_friends_manage, '),
(885, 1, 'Confirm Pending Friend Request', 'profile, '),
(886, 1, 'This user\'s friendship request has been confirmed.', ''),
(887, 1, 'Confirm Friend Request', 'user_friends_requests_outgoing, user_friends_requests, user_friends_manage, profile, '),
(888, 1, 'Are you sure you want to confirm %1\$s\'s friendship request?', 'user_friends_manage, '),
(889, 1, 'Remove Friend', 'user_friends_manage, user_friends, '),
(890, 1, 'This user has been removed from your friendlist.', ''),
(892, 1, 'HTML in Comments', 'admin_general, '),
(891, 1, 'Wall-to-Wall', 'profile, '),
(893, 1, 'By default, the user may not enter any HTML tags into comments. If you want to allow specific tags, you can enter them below (separated by commas). Example: <i>b, img, a, embed, font<i>', 'admin_general, '),
(894, 1, 'Current Friends', 'user_friends_requests_outgoing, user_friends_requests, user_friends, '),
(895, 1, 'Friend Requests', 'user_friends_requests_outgoing, user_friends_requests, user_friends, '),
(896, 1, 'Outgoing Friend Requests', 'user_friends_requests_outgoing, user_friends_requests, user_friends, '),
(897, 1, 'My Friends', 'user_friends, '),
(898, 1, 'Everyone currently on your friend list is shown here. To search for a specific friend, enter a keyword in the field below.', 'user_friends, '),
(899, 1, 'Search my friends:', 'user_friends, '),
(900, 1, 'Sort by:', 'user_friends, '),
(901, 1, 'Recently Updated', 'user_friends, '),
(902, 1, 'Recently Logged-In', 'user_friends, '),
(903, 1, 'Friend Type', 'user_friends, '),
(904, 1, 'Your friend list is empty.', 'user_friends, '),
(905, 1, 'None of your friends match your search criteria.', 'user_friends, '),
(906, 1, 'Last Login:', 'user_friends_requests_outgoing, user_friends_requests, user_friends, '),
(907, 1, 'Details:', 'user_friends_requests_outgoing, user_friends_requests, user_friends, profile, '),
(908, 1, 'Edit Friendship', 'user_friends, '),
(909, 1, 'When other people request to become your friend, their requests appear here. You can approve or reject their requests.', 'user_friends_requests, '),
(910, 1, 'You do not have any friend requests at this time.', 'user_friends_requests, '),
(911, 1, 'Reject Friend Request', 'user_friends_requests, '),
(912, 1, 'Are you sure you want to reject %1\$s\'s friendship request?', 'user_friends_manage, '),
(913, 1, 'Reject Request', 'user_friends_manage, '),
(914, 1, 'You have successfully rejected this user\'s friendship request.', ''),
(915, 1, 'When you ask other people to be your friend, your pending requests will appear here until they are approved or rejected.', 'user_friends_requests_outgoing, '),
(916, 1, 'You do not have any outgoing friend requests at this time.', 'user_friends_requests_outgoing, '),
(917, 1, 'Cancel Friendship Request', 'user_friends_requests_outgoing, '),
(918, 1, 'You are waiting for a friendship confirmation from %1\$s. Are you sure you want to cancel your request for friendship with %1\$s?', 'user_friends_manage, '),
(919, 1, 'Cancel Request', 'user_friends_manage, '),
(920, 1, 'You have successfully canceled your friendship request to this user.', ''),
(921, 1, 'To edit the details of your friendship with %1\$s, complete the form below.', 'user_friends_manage, '),
(922, 1, 'Edit Friendship', 'user_friends_manage, '),
(923, 1, 'You have successfully edited the details of this friendship.', ''),
(924, 1, 'Search the social network.', 'search, '),
(925, 1, 'Search for:', 'search, '),
(926, 1, 'Advanced Search', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(927, 1, 'No results for \"<b>%1\$s</b>\" were found.', 'search, '),
(928, 1, '%1\$s seconds', 'search, '),
(929, 1, 'Currently Online', 'search, '),
(930, 1, '%1\$s\'s Friends', 'profile, '),
(931, 1, 'The following people are listed as %1\$s\'s friends.', ''),
(932, 1, '%1\$s has not yet added any friends.', ''),
(933, 1, 'Search %1\$s\'s friends:', ''),
(934, 1, 'None of %1\$s\'s friends match your search criteria.', 'profile, '),
(935, 1, 'FAQ Manager', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(936, 1, 'A frequently asked questions (FAQ) page can help you reduce your support responsibilities by allowing users to find answers to their common questions in your help area. Add any questions and answers that you feel are appropriate for your social network here. Also, be sure to organize your questions into relevant categories to help your users find answers more easily.', 'admin_faq, '),
(937, 1, 'Add Question', 'admin_faq, '),
(938, 1, 'Answer:<br>(HTML OK)', 'admin_faq, '),
(939, 1, 'Question', 'admin_faq, '),
(940, 1, 'Avg. Daily Views', 'admin_faq, '),
(941, 1, 'Created', 'admin_faq, '),
(942, 1, 'Updated', 'admin_faq, '),
(943, 1, 'move up', 'admin_faq, '),
(944, 1, 'Please provide a title for this FAQ category.', 'admin_faq, '),
(945, 1, 'Please specify a name for this category.', 'admin_faq, '),
(800001, 1, 'Your Account', 'help, admin_faq, '),
(800003, 1, 'Privacy', 'help, admin_faq, '),
(946, 1, 'Question:', 'admin_faq, '),
(947, 1, 'Please specify a question.', 'admin_faq, '),
(948, 1, 'Please provide some information about this FAQ question.', 'admin_faq, '),
(800004, 1, 'I can&#039;t login, or I forgot my username or password.', 'help, admin_faq, '),
(800005, 1, 'If you can\'t login, check to make sure that your \"caps lock\" key is off. Your username and password are CaSe SeNsItIvE. If you still cannot login, you can request to <a href=\'lostpass.php\'>reset your password</a> or <a href=\'help_contact.php\'>contact us</a>.', 'help, admin_faq, '),
(1097, 1, 'Suggestions:', 'admin_fields, '),
(949, 1, '%1\$d total views', 'admin_faq, '),
(950, 1, 'Reset the views for this question?', 'admin_faq, '),
(1012, 1, 'Are you sure you want to delete this user?', 'admin_viewusers, '),
(951, 1, 'Edit Category', 'admin_faq, '),
(954, 1, 'Edit Question', 'admin_faq, '),
(952, 1, 'Delete Category?', 'admin_faq, '),
(953, 1, 'Are you sure you want to delete this category? NOTE: All questions within this category will also be deleted!', 'admin_faq, '),
(955, 1, 'Delete Question?', 'admin_faq, '),
(800007, 1, 'If you are aboslutely sure that you want to delete your account, you can do so <a href=\'user_account_delete.php\'>here</a>. Please note that your account will be permanently deleted and irrecoverable!', 'help, admin_faq, '),
(800006, 1, 'How can I delete my account?', 'help, admin_faq, '),
(956, 1, 'Are you sure you want to delete this question?', 'admin_faq, '),
(800008, 1, 'How can I update my profile?', 'help, admin_faq, '),
(800009, 1, 'To update your profile, you must visit the <a href=\'user_editprofile.php\'>Edit Profile</a> page. You can move through the different parts of your profile by clicking the tabs at the top of the page.', 'help, admin_faq, '),
(800010, 1, 'How can I update my email address?', 'help, admin_faq, '),
(800011, 1, 'You can update your email address on the <a href=\'user_account.php\'>My Account</a> page.', 'help, admin_faq, '),
(800012, 1, 'How can I report an error or other problem with the site?', 'help, admin_faq, '),
(800013, 1, 'To report an error or problem with the site, you can contact us <a href=\'help_contact.php\'>here</a>.', 'help, admin_faq, '),
(800014, 1, 'How can I deal with someone that is bothering me?', 'help, admin_faq, '),
(800015, 1, 'If someone is bothering or harassing you, blocking them is usually the best solution. Visit the <a href=\'user_account.php\'>Account Settings</a> page to learn how to block people. If someone continues to harass you despite your efforts, you can report them <a href=\'help_contact.php\'>here</a>.', 'help, admin_faq, '),
(800016, 1, 'How can I report spam or other inappropriate content?', 'help, admin_faq, '),
(800017, 1, 'You can report spam, pornography, or any other inappropriate content <a href=\'help_contact.php\'>here</a>, or by clicking the \"Report\" link on the page containing the content you wish to report.', 'help, admin_faq, '),
(800018, 1, 'Is my information kept private?', 'help, admin_faq, '),
(800019, 1, 'Absolutely. We do not share any personally identifying information about you to any third party.', 'help, admin_faq, '),
(800020, 1, 'How can I make my profile private?', 'help, admin_faq, '),
(800021, 1, 'If the administrator has enabled it, you can make your profile private by visiting the <a href=\'user_account_privacy.php\'>Profile Privacy</a> page.', 'help, admin_faq, '),
(800022, 1, 'How can I block users from contacting me?', 'help, admin_faq, '),
(800023, 1, 'You can block people by adding their username to your blocked users list. Visit the <a href=\'user_account.php\'>Account Settings</a> page to learn more about how to block people.', 'help, admin_faq, '),
(957, 1, 'Frequently Asked Questions', 'help, '),
(958, 1, 'If you need help, the answer to your question is likely to be found on this page.', 'help, '),
(959, 1, 'Email Notifications', 'user_account, '),
(960, 1, 'Which of the following things do you want to receive email notifications for?', 'user_account, '),
(961, 1, 'When I receive a message.', 'user_account, '),
(962, 1, 'When I receive a friend request.', 'user_account, '),
(963, 1, 'When I receive a profile comment.', 'user_account, '),
(964, 1, 'Edit your profile\'s style here.', 'user_editprofile_style, '),
(965, 1, 'Profile Style', 'user_editprofile_style, '),
(966, 1, 'Add your own CSS code below to give your profile a more personalized look.<br>The contents of the text area below will be output between &lt;style&gt; tags on your profile.', 'user_editprofile_style, '),
(967, 1, 'Profile Privacy', 'user_account_privacy, '),
(968, 1, 'Who can view your profile?', 'user_account_privacy, '),
(969, 1, 'Comments Privacy', 'user_account_privacy, '),
(970, 1, 'Who can post comments on your profile?', 'user_account_privacy, '),
(971, 1, 'Search Privacy', 'user_account_privacy, '),
(972, 1, 'Do you want to be included in search results?<br>Note that this privacy setting also applies to users displayed on the homepage (such as Most Popular User).', 'user_account_privacy, '),
(973, 1, 'Yes, include my profile in search results.', 'user_account_privacy, '),
(974, 1, 'No, do not include my profile in search results.', 'user_account_privacy, '),
(750001, 1, '%1\$d Friend Request(s)', 'user_report, user_home, user_friends_requests, user_friends_manage, user_account_privacy, user_account_pass, user_account_delete, user_account, search, profile, network, '),
(850011, 1, 'Social Network - Lost Password', 'lostpass, admin_emails, '),
(850012, 1, 'Hello %1\$s,<br><br>You have requested to reset your password because you have forgotten your password. If you did not request this, please ignore it. It will expire in 24 hours.To reset your password, please click the following link:<br><br>%3\$s<br><br>Best Regards,<br>Social Network Administration', 'lostpass, admin_emails, '),
(850013, 1, '%2\$s has added you as a friend.', 'user_friends_manage, admin_emails, '),
(850014, 1, 'Hello %1\$s,<br><br>%2\$s has added you as a friend. Please click the following link to login and confirm this friendship request:<br><br>%3\$s<br><br>Best Regards,<br>Social Network Administration', 'user_friends_manage, admin_emails, '),
(850015, 1, 'You have received a new message.', 'admin_emails, '),
(850016, 1, 'Hello %1\$s,<br><br>You have just received a new message from %2\$s. Please click the following link to login and view it:<br><br>%3\$s<br><br>Best Regards,<br>Social Network Administration', 'admin_emails, '),
(750002, 1, '%1\$d New Messages', ''),
(750003, 1, '%1\$d New Profile Comment(s)', ''),
(850001, 1, 'You have received an invitation to join our social network!', 'admin_emails, '),
(850002, 1, 'Hello,<br><br>You have been invited by %1\$s to join our social network. To join, please follow the link below and enter your invite code.<br><br>%5\$s<br><br>Invite Code: %4\$s<br><br>Best Regards,<br>Social Network Administration<br><br>----------------------------------------<br>%3\$s', 'admin_emails, '),
(850003, 1, 'You have received an invitation to join our social network.', 'admin_emails, '),
(850004, 1, 'Hello,<br><br>You have been invited by %1\$s to join our social network. To join, please follow the link below:<br>%4\$s<br><br>Best Regards,<br>Social Network Administration<br><br>----------------------------------------<br>%3\$s', 'admin_emails, '),
(850005, 1, 'Social Network - Email Verification', 'signup, admin_emails, '),
(978, 1, 'Enable this feature if you want your users to choose from existing CSS samples. To add additional samples, simply insert a row into the se_stylesamples database table containing the exact CSS code that should be entered into the Profile Style textarea and, optionally, the path to a thumbnail for the template.', 'admin_levels_usersettings, '),
(976, 1, '%1\$s and %2\$d guest(s)', 'user_home, home, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(977, 1, '%1\$d guest(s)', 'user_home, home, admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(975, 1, '[ Refresh ]', 'signup, '),
(982, 1, 'Yes, users can choose from the provided sample CSS.', 'admin_levels_usersettings, '),
(983, 1, 'No, users can not choose from the provided sample CSS.', 'admin_levels_usersettings, '),
(984, 1, 'Site Online?', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, ')") or die("Insert: se_languagevars (3)<br>Error: ".mysql_error());
mysql_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (985, 1, 'Use this feature when you want to take your site offline for maintenance or upgrades. When your users attempt to access the site, a message will be displayed letting them know the site is undergoing routine maintenance and will be available again soon. If you are logged in as an administrator, you will be able to browse the site freely.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(979, 1, 'Sample Profile Layouts', 'user_editprofile_style, '),
(980, 1, 'Click on one of the sample layouts below to select it for your profile.<br><b>NOTE:</b> Choosing one of the sample layouts below will remove your current layout.', 'user_editprofile_style, '),
(981, 1, 'Are you sure you want to replace your profile style with this template?', 'user_editprofile_style, '),
(986, 1, 'Yes, the site is online.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(987, 1, 'No, the site is offline for maintenance.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(988, 1, 'The site is currently down for maintenance. Check back again shortly!', 'home, '),
(989, 1, 'Checkboxes', 'admin_fields, '),
(990, 1, 'Display Type:', 'admin_fields, '),
(991, 1, 'Displayed, Linked on Profile', 'admin_fields, '),
(992, 1, 'Displayed on Profile', 'admin_fields, '),
(993, 1, 'Not Displayed on Profile', 'admin_fields, '),
(994, 1, 'Special Attribute:', 'admin_fields, '),
(995, 1, 'Birthday (Date Field Only)', 'admin_fields, '),
(996, 1, 'This page lists all of the users that exist on your social network. For more information about a specific user, click on the \"edit\" link in its row. Click the \"login\" link to login as a specific user. Use the filter fields to find specific users based on your criteria. To view all users on your system, leave all the filter fields blank.', 'admin_viewusers, '),
(997, 1, 'User Level', 'admin_viewusers, '),
(998, 1, 'Subnetwork', 'admin_viewusers, '),
(999, 1, 'Enabled', 'admin_viewusers_edit, admin_viewusers, '),
(1000, 1, 'Yes', 'admin_viewusers, '),
(1001, 1, 'No', 'admin_viewusers, '),
(1002, 1, 'Filter', 'admin_viewusers, admin_viewreports, '),
(1003, 1, 'No users were found.', 'admin_viewusers, '),
(1004, 1, '%1\$d Users Found', 'admin_viewusers, '),
(1005, 1, 'Page:', 'admin_viewusers, admin_viewreports, '),
(1006, 1, 'Verified', 'admin_viewusers, '),
(1007, 1, 'Signup Date', 'admin_viewusers, '),
(1008, 1, 'unverified', 'admin_viewusers_edit, admin_viewusers, '),
(1009, 1, 'login', 'admin_viewusers, '),
(1010, 1, 'The site is now online.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(1011, 1, 'The site is now offline.', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
(1013, 1, 'Delete User', 'admin_viewusers, '),
(1014, 1, 'Note that if you change your Account Type, you may have to re-enter your profile information.', 'user_account, '),
(1015, 1, 'Your Email Address', 'signup_verify, '),
(1016, 1, 'Resend Verification', 'signup_verify, '),
(1072, 1, '%1\$d profiles', 'search, '),
(1018, 1, 'Phrase ID:', 'admin_language_edit, '),
(1019, 1, '%1\$s new update(s)', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1017, 1, 'Continue To Login...', 'signup_verify, '),
(1020, 1, 'View Mutual Friends', 'profile, '),
(1021, 1, 'view all', ''),
(1022, 1, 'View All Friends', 'profile, '),
(1023, 1, 'You do not have any friends in common with %1\$s.', 'profile, '),
(1024, 1, 'Mutual Friends with %1\$s', 'profile, '),
(1025, 1, 'Delete Comment', 'profile, '),
(1026, 1, 'Are you sure you want to delete this comment?', 'profile, '),
(1027, 1, '<b>Note:</b> This action has media (such as photos) associated with it. Simply include the tag <i>[media]</i> to display them.', 'admin_activity, '),
(1028, 1, 'Congratulations! You have successfully verified your email address. Click the button below to login.', 'signup_verify, '),
(1029, 1, 'First Name/1st Display Name (Text Field Only)', 'admin_fields, '),
(1030, 1, 'Last Name/2nd Display Name (Text Field Only)', 'admin_fields, '),
(1031, 1, 'This field allows you to treat certain profile fields differently. Designating a date field as \"Birthday\" will make a user\'s age display on their profile. If you would like to use the user\'s First Name or Last Name as their display name instead of their username, you can create special fields with these designations that will display instead of the user\'s username on their profile. It is advised that you only create one field per category with each special designation or unexpected results may occur.', 'admin_fields, '),
(1032, 1, 'Profile Comment Conversation', 'profile, '),
(1033, 1, 'Conversation Between %1\$s and %2\$s', ''),
(1034, 1, 'Allowed HTML Tags: %1\$s', 'profile, '),
(1073, 1, 'Please enter at least one recipient email address for your invitation.', ''),
(1035, 1, 'If you want to ask us a question directly, please submit your message with the following form.', 'help_contact, '),
(1036, 1, 'Please provide a detailed message.', ''),
(1037, 1, 'Another user has already taken this email address.', ''),
(1038, 1, 'There is no user in the system with that email address. Please <a href=\'home.php\'>click here</a> to return to the home page.', ''),
(1039, 1, 'The link you have clicked is invalid or expired. <a href=\'signup_verify.php?task=resend\'>Click here</a> to resend the verification email.', 'signup_verify, '),
(1040, 1, 'Your message has been received. We will assist you as soon as possible.', 'help_contact, '),
(1041, 1, 'Congratulations! You have successfully verified your email address and your network has been changed from %1\$s to %2\$s. Click the button below to login.', ''),
(1042, 1, 'A new verification email has been sent to the email address you provided. Please follow the link within to verify your account.', ''),
(1043, 1, 'To have the email verification resent, enter your email address in the field below. If you have reached this page in error, <a href=\'home.php\'>click here</a> to return to the home page.', 'signup_verify, '),
(1044, 1, 'The email address you have provided is already verified. If you have forgotten your password, please <a href=\'lostpass.php\'>click here</a> to have it sent to you.', 'signup_verify, '),
(1045, 1, 'Email Address Verification', 'signup_verify, '),
(1046, 1, 'Please provide your name.', ''),
(1047, 1, 'Allow users to go invisible?', 'admin_levels_usersettings, '),
(1048, 1, 'Enable this feature if you want to allow users to go \"invisible\" (not be displayed in the online users list even if they are online).', 'admin_levels_usersettings, '),
(1049, 1, 'Allow users to see who viewed their profile?', 'admin_levels_usersettings, '),
(1050, 1, 'If you enable this feature, users will be given the option of seeing which users have visited their profile.', 'admin_levels_usersettings, '),
(1051, 1, 'Yes, allow users to go invisible.', 'admin_levels_usersettings, '),
(1052, 1, 'No, do not allow users to go invisible.', 'admin_levels_usersettings, '),
(1053, 1, 'Yes, allow users to see who has viewed their profile.', 'admin_levels_usersettings, '),
(1054, 1, 'No, do not allow users to see who has viewed their profile.', 'admin_levels_usersettings, '),
(1055, 1, 'Privacy', 'user_account_privacy, user_account_pass, user_account_delete, user_account, '),
(1056, 1, 'Privacy Settings', 'user_account_privacy, '),
(1057, 1, 'Change your account\'s general privacy settings here.', 'user_account_privacy, '),
(1058, 1, 'Go Invisible', 'user_account_privacy, '),
(1059, 1, 'Do not display me in the \"Online Users\" list.', 'user_account_privacy, '),
(1060, 1, 'Show Profile Views', 'user_account_privacy, '),
(1061, 1, 'Yes, display users who viewed my profile.', 'user_account_privacy, '),
(1062, 1, 'Note: If you choose to display users who viewed your profile, other users will be able to track whether you viewed their profile. If you do not want other users to know you viewed their profile, do not enable this feature.', 'user_account_privacy, '),
(1063, 1, 'No users have viewed your profile yet.', 'user_home, '),
(1064, 1, 'Who viewed my profile?', 'user_home, '),
(1065, 1, 'Do you want to allow your users to decide which actions they want to see in their activity feed? If you enable this feature, users will be able specify actions they do and do not want to see in their recent activity feed.', 'admin_activity, '),
(1066, 1, 'Yes, allow users to specify which actions they will see in the activity feed.', 'admin_activity, '),
(1067, 1, 'No, do not allow users to specify which actions they will see in the activity feed.', 'admin_activity, '),
(1068, 1, 'Activity Feed Preferences', 'user_home, '),
(1069, 1, 'Which actions do you want to see in the recent activity feed?', 'user_home, '),
(1070, 1, 'Preferences', 'user_home, '),
(1071, 1, '[User Deleted]', 'profile, '),
(1074, 1, 'Invite Your Friends', 'invite, '),
(1075, 1, 'Invite your friends to join! Enter up to 10 email addresses of your friends separated by commas in the field below.', 'invite, '),
(1076, 1, 'You must be logged in to invite other people.', 'invite, '),
(1077, 1, 'You have <b>%1\$d</b> invites remaining.', 'invite, '),
(1078, 1, 'When they signup, they will be instantly added to your friends list.', 'invite, '),
(1079, 1, 'To:', 'invite, '),
(1080, 1, 'Separate multiple email addresses (up to 10) with commas.', 'invite, '),
(1081, 1, 'Message:', 'invite, '),
(1082, 1, 'Type your message here. (optional)', 'invite, '),
(1083, 1, 'Browsing members that match %1\$s', 'search_advanced, '),
(1084, 1, 'We found %1\$d member(s) with profiles that match %2\$s.', 'search_advanced, '),
(1085, 1, 'No people matched your search criteria.', 'search_advanced, '),
(1086, 1, 'Online', 'search_advanced, '),
(1087, 1, 'Advanced Search Members', 'search_advanced, '),
(1088, 1, 'Search through our members with your own keywords and criteria.', 'search_advanced, '),
(1089, 1, 'Search Criteria', 'search_advanced, '),
(1090, 1, 'Update Results', 'search_advanced, '),
(1091, 1, 'Sort Results By:', 'search_advanced, '),
(1092, 1, 'Last Update', 'search_advanced, '),
(1093, 1, '(DESC)', 'search_advanced, '),
(1094, 1, '(ASC)', 'search_advanced, '),
(1095, 1, 'Last Login', 'search_advanced, '),
(1096, 1, 'Last Signup', 'search_advanced, '),
(850010, 1, 'Hello %1\$s,<br><br>Thank you for joining our social network. Click the following link and enter your information below to login:<br><br>%4\$s<br><br>Email: %2\$s<br>Password: %3\$s<br><br>Best Regards,<br>Social Network Administration', 'signup_verify, signup, admin_emails, '),
(850006, 1, 'Hello %1\$s,<br><br>Thank you for joining our social network. To verify your email address and continue, please click the following link:<br>%3\$s<br><br>Best Regards,<br>Social Network Administration', 'signup, admin_emails, '),
(850007, 1, 'Social Network - Login Details', 'admin_emails, '),
(850008, 1, 'Hello %1\$s,<br><br>Thank you for joining our social network. Click the following link and enter your information below to login:<br><br>%4\$s<br><br>Email: %2\$s<br>Password: %3\$s<br><br>Best Regards,<br>Social Network Administration', 'admin_emails, '),
(850009, 1, 'Welcome to the social network!', 'signup_verify, signup, home, admin_emails, '),
(850017, 1, 'New Profile Comment', 'admin_emails, '),
(850018, 1, 'Hello %1\$s,<br><br>A new comment has been posted on your profile by %2\$s. Please click the following link to view it:<br><br>%3\$s<br><br>Best Regards,<br>Social Network Administration', 'admin_emails, '),
(1098, 1, 'Add New Suggestion', ''),
(1099, 1, 'If you would like this field to auto-suggest values to the user when they are filling out this field (such as US States), add suggestions below, separated by line breaks. Note that the user will not be restricted to these values, they will merely be suggested to the user as they are typing.', 'admin_fields, '),
(1100, 1, 'This page lists all of the reports your users have sent in regarding inappropriate content, system abuse, spam, and so forth. You can use the search field to look for reports that contain a particular word or phrase. Very old reports are periodically deleted by the system.', 'admin_viewreports, '),
(1101, 1, 'Reason', 'admin_viewreports, '),
(1102, 1, 'Details', 'admin_viewreports, '),
(1103, 1, 'No reports have been made.', 'admin_viewreports, '),
(1104, 1, '%1\$d Reports Found', 'admin_viewreports, '),
(1105, 1, 'login & view', 'admin_viewreports, '),
(1106, 1, 'details', ''),
(1107, 1, 'Any SocialEngine plugins that you have installed will appear on this page. Note that some plugins may have user level-specific settings which are available on the <a href=\'admin_levels.php\'>User Levels</a> page.', 'admin_viewplugins, '),
(1108, 1, 'There are currently no plugins installed. Visit the <a href=\'http://www.socialengine.net/\' target=\'_blank\'>SocialEngine website</a> to add plugins to your social network!', 'admin_viewplugins, '),
(1109, 1, 'Install Plugin', 'admin_viewplugins, '),
(1110, 1, 'Warning: You have not yet deleted install_%1\$s.php. Leaving this file on your server is a security risk!', 'admin_viewplugins, '),
(1111, 1, 'Install Upgrade', 'admin_viewplugins, '),
(1112, 1, 'Upgrade Available!', 'admin_viewplugins, '),
(1113, 1, 'Updated:', 'user_home, profile, '),
(1114, 1, 'The admin has not enabled any advanced search fields.', 'search_advanced, '),
(1117, 1, 'MAX', 'search_advanced, '),
(1116, 1, 'MIN', 'search_advanced, '),
(1115, 1, 'Signup today!', 'home, '),
(1118, 1, 'Uninstall', 'admin_viewplugins, '),
(1119, 1, 'Network:', 'profile, '),
(1120, 1, 'Account Type:', 'profile, '),
(1121, 1, 'Online Users Only', 'search_advanced, '),
(1122, 1, 'Users with Photos Only', 'search_advanced, '),
(1123, 1, 'Editing User: %1\$s', 'admin_viewusers_edit, '),
(1124, 1, 'To edit this users\'s account, make changes to the form below. If you want to temporarily prevent this user from logging in, you can set the user account to \"disabled\" below.', 'admin_viewusers_edit, '),
(1125, 1, 'total friends', 'admin_viewusers_edit, '),
(1126, 1, 'total logins', 'admin_viewusers_edit, '),
(1127, 1, 'total messages stored', 'admin_viewusers_edit, '),
(1128, 1, 'total comments made', 'admin_viewusers_edit, '),
(1129, 1, 'Last Login:', 'admin_viewusers_edit, '),
(1130, 1, 'Never', 'admin_viewusers_edit, '),
(1131, 1, 'resend verification email', 'admin_viewusers_edit, '),
(1132, 1, 'manually verify', 'admin_viewusers_edit, '),
(1133, 1, 'Username:', 'admin_viewusers_edit, '),
(1134, 1, 'Password:', 'admin_viewusers_edit, '),
(1135, 1, 'Only enter if you want to reset pass.', 'admin_viewusers_edit, '),
(1136, 1, 'Enabled?', 'admin_viewusers_edit, '),
(1137, 1, 'Disabled', 'admin_viewusers_edit, '),
(1138, 1, 'User Level:', 'admin_viewusers_edit, '),
(1139, 1, 'Invites Remaining:', 'admin_viewusers_edit, '),
(1140, 1, 'Email verification has been resent.', ''),
(1141, 1, 'User email has been manually verified.', 'admin_viewusers_edit, '),
(1142, 1, 'The number of invites left must be between 0 and 999.', 'admin_viewusers_edit, '),
(1143, 1, 'Your changes have been saved and this user\'s subnetwork has been changed from %1\$s to %2\$s.', ''),
(1144, 1, 'Signup IP:', 'admin_viewusers_edit, '),
(1145, 1, 'Last Recorded IP:', 'admin_viewusers_edit, '),
(1146, 1, 'Are you <b>REALLY</b> sure you want to delete your account? You account will be <i>permanently</i> deleted and you will not be able to restore any of your account data!', 'user_account_delete, '),
(1147, 1, 'Setlocale Code', 'admin_language, '),
(1148, 1, 'Enable Username?', 'admin_general, '),
(1149, 1, 'By default, usernames are used to uniquely identify your users. If you choose to disable this feature, your users will not be given the option to enter a username. Instead, their user ID will be used. Note that if you do decide to enable this feature, you should make sure to create special REQUIRED display name profile fields - otherwise the users\' IDs will be displayed. Also note that if you disable usernames after users have already signed up, their usernames will be <b>deleted</b> and any previous links to their content <b>will not work</b>, as the links will no longer use their username! Finally, <b>all recent activity and all notifications will be deleted</b> if you choose to disable usernames after previously having them enabled.', 'admin_general, '),
(1150, 1, 'Yes, users are uniquely identified by their username.', 'admin_general, '),
(1151, 1, 'No, usernames will not be used in this network.', 'admin_general, '),
(1152, 1, 'Display Name', 'admin_viewusers, '),
(1153, 1, 'Help Request: %1\$s', 'help_contact, '),
(1154, 1, 'Hello %1\$s,<br><br>You have received a support request:<br><br>Email: %2\$s<br>Name: %3\$s<br>Subject: %4\$s<br><br>%5\$s', 'help_contact, '),
(1155, 1, 'What\'s New In My Network: %1\$s', 'network, '),
(1156, 1, 'Social Network Default Meta Tag Description', 'user_report, user_messages_view, user_messages_outbox, user_messages_new, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends_manage, user_friends_block, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile_comments, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1157, 1, 'Terms of Service: %1\$s', 'help_tos, '),
(1158, 1, '%1\$s\'s Profile - %2\$s', 'profile, '),
(1159, 1, 'Type', 'admin_language_edit, '),
(1160, 1, 'Default Location(s)', 'admin_language_edit, '),
(1161, 1, 'What\'s New', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1162, 1, 'My Network', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1163, 1, 'Edit Profile Information', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1164, 1, 'Change Profile Photo', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1165, 1, 'Edit Profile Layout', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1166, 1, 'My Apps', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1167, 1, 'Compose New Message', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1168, 1, 'Message Inbox', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1169, 1, 'Message Outbox', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1170, 1, 'View My Friends', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1171, 1, 'Incoming Friend Requests', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1172, 1, 'Outgoing Friend Requests', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1173, 1, 'Account Settings', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1174, 1, 'Privacy Options', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1175, 1, 'Copyright', 'user_messages_view, user_messages_outbox, user_messages, user_home, user_friends_requests_outgoing, user_friends_requests, user_friends, user_editprofile_style, user_editprofile_photo, user_editprofile, user_album, user_account_privacy, user_account_pass, user_account_delete, user_account, signup_verify, signup, search_advanced, search, profile, network, lostpass_reset, lostpass, login, invite, home, help_tos, help_contact, help, '),
(1176, 1, 'Friends\' Birthdays', 'user_home, '),
(1177, 1, 'Recent Status Updates', 'network, '),
(1178, 1, 'No one has changed their status recently.', 'network, '),
(1179, 1, 'No users have joined this network yet.', 'network, '),
(1180, 1, 'There are no upcoming birthdays.', 'user_home, '),
(1181, 1, 'Delete this message.', 'user_messages_view, '),
(1182, 1, '(Listed By Most Recent Visitor)', 'user_home, '),
(500001, 1, 'Standard Users', ''),
(750004, 1, 'When I receive a friend request', ''),
(750005, 1, 'When I receive a message', ''),
(750006, 1, 'When I receive a profile comment', '')") or die("Insert: se_languagevars (4)<br>Error: ".mysql_error());


} else {
  mysql_query("ALTER TABLE `se_languagevars` DROP INDEX `INDEX`, ADD UNIQUE `INDEX` ( `languagevar_id` , `languagevar_language_id` )") or die("UNIQUE Index: se_languagevars<br>Error: ".mysql_error());
}





//######### MODIFY se_levels
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_message_recipients'")) )
{
  mysql_query("ALTER TABLE se_levels ADD level_message_recipients int(3) NOT NULL default '1'");
  mysql_query("ALTER TABLE se_levels CHANGE level_profile_privacy level_profile_privacy varchar(100) NOT NULL default ''");
  mysql_query("UPDATE se_levels SET level_profile_privacy='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'");
  mysql_query("ALTER TABLE se_levels CHANGE level_profile_comments level_profile_comments varchar(100) NOT NULL default ''");
  mysql_query("UPDATE se_levels SET level_profile_comments='a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_profile_style_sample'")) != 1) {
  mysql_query("ALTER TABLE se_levels ADD level_profile_style_sample int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_profile_invisible'")) != 1) {
  mysql_query("ALTER TABLE se_levels ADD level_profile_invisible int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_profile_views'")) != 1) {
  mysql_query("ALTER TABLE se_levels ADD level_profile_views int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_profile_change'")) != 1) {
  mysql_query("ALTER TABLE se_levels ADD level_profile_change int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_profile_delete'")) != 1) {
  mysql_query("ALTER TABLE se_levels ADD level_profile_delete int(1) NOT NULL default '0'");
}





//######### CREATE se_notifys
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_notifys'")) == 0) {
  mysql_query("CREATE TABLE `se_notifys` (
  `notify_id` int(9) NOT NULL auto_increment,
  `notify_user_id` int(9) NOT NULL default '0',
  `notify_notifytype_id` int(9) NOT NULL default '0',
  `notify_object_id` int(9) NOT NULL,
  `notify_urlvars` varchar(250) NOT NULL default '0',
  `notify_text` text NOT NULL,
  PRIMARY KEY  (`notify_id`),
  KEY `notify_user_id` (`notify_user_id`),
  KEY `notify_object_id` (`notify_object_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_notifys<br>Error: ".mysql_error());
}



//######### CREATE se_notifytypes
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_notifytypes'")) == 0) {
  mysql_query("CREATE TABLE `se_notifytypes` (
  `notifytype_id` int(9) NOT NULL auto_increment,
  `notifytype_icon` varchar(50) NOT NULL default '',
  `notifytype_name` varchar(50) NOT NULL default '',
  `notifytype_title` int(9) NOT NULL default '0',
  `notifytype_url` varchar(100) NOT NULL default '',
  `notifytype_desc` int(9) NOT NULL default '0',
  `notifytype_group` int(1) NOT NULL default '0',
  PRIMARY KEY  (`notifytype_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_notifytypes<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_notifytypes
  mysql_query("INSERT INTO `se_notifytypes` (`notifytype_id`, `notifytype_icon`, `notifytype_name`, `notifytype_title`, `notifytype_url`, `notifytype_desc`, `notifytype_group`) VALUES 
					(1, 'menu_friends.gif', 'friendrequest', 750004, 'user_friends_requests.php', 750001, '1'),
					(2, 'menu_messages.gif', 'message', 750005, 'user_messages.php', 750002, '1'),
					(3, 'action_postcomment.gif', 'profilecomment', 750006, 'profile.php?user=%1\$s&v=comments', 750003, '1')") or die("Insert: se_notifytypes<br>Error: ".mysql_error());
}

if(!mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_notifytypes FROM `$database_name` LIKE 'notifytype_title'"))) {
  mysql_query("ALTER TABLE se_notifytypes ADD `notifytype_title` int(9) NOT NULL default '0'");
}
if(!mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_notifytypes FROM `$database_name` LIKE 'notifytype_group'"))) {
  mysql_query("ALTER TABLE se_notifytypes ADD `notifytype_group` int(1) NOT NULL default '0'");
}



//######### CREATE se_plugins
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_plugins FROM `$database_name` LIKE 'plugin_menu_title'")) != 1) {
  mysql_query("ALTER TABLE se_plugins ADD plugin_menu_title int(9) NOT NULL default '0'");
  mysql_query("ALTER TABLE se_plugins CHANGE plugin_version plugin_version varchar(10) NOT NULL default ''");
}




//######### CREATE se_pmconvos - v2->v3.06
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_pmconvos'")) == 0)
{
  mysql_query("CREATE TABLE `se_pmconvos` (
  `pmconvo_id` int(9) NOT NULL auto_increment,
  `pmconvo_collaborators` varchar(250) NOT NULL default '',
  `pmconvo_subject` varchar(100) NOT NULL default '',
  `pmconvo_read` varchar(250) NOT NULL default '',
  `pmconvo_deleted_outbox` varchar(250) NOT NULL default '',
  `pmconvo_deleted_inbox` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`pmconvo_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_pmconvos<br>Error: ".mysql_error());

  // UPDATE PMS
  mysql_query("UPDATE se_pms SET pm_convo_id=pm_id WHERE pm_convo_id=0");
  $pms = mysql_query("SELECT * FROM se_pms GROUP BY pm_convo_id");
  while($pm_info = mysql_fetch_assoc($pms)) {
    $recipients = Array($pm_info[pm_user_id], $pm_info[pm_authoruser_id]);
    mysql_query("INSERT INTO se_pmconvos (pmconvo_id, pmconvo_collaborators, pmconvo_subject, pmconvo_read, pmconvo_deleted_inbox, pmconvo_deleted_outbox) VALUES ('$pm_info[pm_convo_id]', ',".implode(",", $recipients).",', '".str_replace("'", "\'", $pm_info[pm_subject])."', ',".implode(",", $recipients).",', ',', ',')");
  }

  mysql_query("ALTER TABLE se_pms DROP pm_user_id");
  mysql_query("ALTER TABLE se_pms DROP pm_subject");
  mysql_query("ALTER TABLE se_pms DROP pm_status");
  mysql_query("ALTER TABLE se_pms DROP pm_outbox");
  mysql_query("ALTER TABLE se_pms CHANGE pm_convo_id pm_pmconvo_id int(9) NOT NULL default '0'");

}




//######### CREATE se_pmconvoops - v3.06->v3.07+
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_pmconvoops'")) == 0)
{
  mysql_query("
    CREATE TABLE `se_pmconvoops`
    (
      `pmconvoop_id`              int(10)     unsigned  NOT NULL auto_increment,
      `pmconvoop_pmconvo_id`      int(10)     unsigned  NOT NULL default '0',
      `pmconvoop_user_id`         int(10)     unsigned  NOT NULL default '0',
      `pmconvoop_read`            tinyint(1)  unsigned  NOT NULL default '0',
      `pmconvoop_deleted_inbox`   tinyint(3)  unsigned  NOT NULL default '0',
      `pmconvoop_deleted_outbox`  tinyint(3)  unsigned  NOT NULL default '0',
      `pmconvoop_pmdate`          int(11)               NOT NULL default '0',
      PRIMARY KEY  (`pmconvoop_id`),
      UNIQUE KEY `INDEX` (`pmconvoop_pmconvo_id`,`pmconvoop_user_id`),
      KEY `total_outbox` (`pmconvoop_user_id`,`pmconvoop_deleted_outbox`,`pmconvoop_read`),
      KEY `last_pm_date` (`pmconvoop_pmdate`),
      KEY `total_inbox` (`pmconvoop_user_id`,`pmconvoop_deleted_inbox`,`pmconvoop_read`,`pmconvoop_pmdate`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8
  ");
  
  mysql_query("ALTER TABLE se_pmconvos ADD COLUMN pmconvo_recipients INT     UNSIGNED NOT NULL default 0");
  
  mysql_query("
    ALTER TABLE se_pms
    ADD INDEX `pm_pmconvo_id` (`pm_pmconvo_id`),
    ADD INDEX `list_subquery` (`pm_pmconvo_id`,`pm_authoruser_id`,`pm_id`),
    ADD FULLTEXT `SEARCH` (`pm_body`)
  ");
  
  mysql_query("
    ALTER TABLE se_pmconvos
    ADD INDEX `pmconvo_recipients` (`pmconvo_recipients`),
    ADD FULLTEXT `SEARCH` (`pmconvo_subject`)
  ");
}





//######### CREATE se_profilecats
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_profilecats'")) == 0) {
  mysql_query("CREATE TABLE `se_profilecats` (
  `profilecat_id` int(9) NOT NULL auto_increment,
  `profilecat_title` int(9) NOT NULL default '0',
  `profilecat_dependency` int(9) NOT NULL default '0',
  `profilecat_order` int(2) NOT NULL default '0',
  `profilecat_signup` int(1) NOT NULL,
  PRIMARY KEY  (`profilecat_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_profilecats<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_profilecats
  $tabs = mysql_query("SELECT * FROM se_tabs");
  $tab_title = 500002;
  mysql_query("INSERT INTO `se_profilecats` (`profilecat_id`, `profilecat_title`, `profilecat_dependency`, `profilecat_order`, `profilecat_signup`) VALUES (1, 500001, 0, 1, 1)") or die("Insert: se_profilecats<br>Error: ".mysql_error());
  while($tab_info = mysql_fetch_assoc($tabs)) {
    if($tab_info[tab_id] == 1) { 
      mysql_query("DELETE FROM se_tabs WHERE tab_id=1");
      mysql_query("INSERT INTO se_tabs (tab_name, tab_order) VALUES ('".str_replace("'", "\'", $tab_info[tab_name])."', '$tab_info[tab_order]')");
      $tab_info[tab_id] = mysql_insert_id();
      mysql_query("UPDATE se_fields SET field_tab_id=$tab_info[tab_id] WHERE field_tab_id=1");
    }
    mysql_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$tab_title', '1', '".str_replace("'", "\'", $tab_info[tab_name])."')");
    mysql_query("INSERT INTO `se_profilecats` (`profilecat_id`, `profilecat_title`, `profilecat_dependency`, `profilecat_order`, `profilecat_signup`) VALUES ($tab_info[tab_id], $tab_title, 1, $tab_info[tab_order], 1)") or die("Insert: se_profilecats<br>Error: ".mysql_error());
    $tab_title++;
  }

  mysql_query("DROP TABLE IF EXISTS se_tabs");
}



//######### CREATE se_profilefields
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_profilefields'")) == 0) {
  mysql_query("CREATE TABLE `se_profilefields` (
  `profilefield_id` int(9) NOT NULL auto_increment,
  `profilefield_profilecat_id` int(9) NOT NULL default '0',
  `profilefield_order` int(3) NOT NULL default '0',
  `profilefield_dependency` int(9) NOT NULL default '0',
  `profilefield_title` int(9) NOT NULL default '0',
  `profilefield_desc` int(9) NOT NULL default '0',
  `profilefield_error` int(9) NOT NULL default '0',
  `profilefield_type` int(1) NOT NULL default '0',
  `profilefield_signup` int(1) NOT NULL default '0',
  `profilefield_style` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_maxlength` int(3) NOT NULL default '0',
  `profilefield_link` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_options` longtext collate utf8_unicode_ci,
  `profilefield_display` int(1) NOT NULL default '1',
  `profilefield_required` int(1) NOT NULL default '0',
  `profilefield_regex` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_special` int(1) NOT NULL default '0',
  `profilefield_html` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_search` int(1) NOT NULL default '1',
  PRIMARY KEY  (`profilefield_id`),
  KEY `INDEX` (`profilefield_profilecat_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_profilefields<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_profilefields
  $fields = mysql_query("SELECT * FROM se_fields");
  if(isset($tab_title)) { $field_var = $tab_title++; } else { $field_var = 500002; }
  while($field_info = mysql_fetch_assoc($fields)) {
    $field_title = $field_var;
    mysql_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$field_title', '1', '".str_replace("'", "\'", $field_info[field_title])."')");
    $field_var++;
    $field_desc = $field_var;
    mysql_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$field_desc', '1', '".str_replace("'", "\'", $field_info[field_desc])."')");
    $field_var++;
    $field_error = $field_var;
    mysql_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$field_error', '1', '".str_replace("'", "\'", $field_info[field_error])."')");
    $field_var++;

    $field_options = Array();
    $old_field_options = explode("<~!~>", $field_info[field_options]);
    for($i=0;$i<count($old_field_options);$i++) {
      if(trim($old_field_options[$i]) != "") {

        $field_option = explode("<!>", $old_field_options[$i]);

        $option_label = $field_var;
        mysql_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$option_label', '1', '".str_replace("'", "\'", $field_option[1])."')");
        $field_var++;

        $field_options[] = Array('value'=>$field_option[0],
			'label'=>$option_label,
			'dependency'=>$field_option[2],
			'dependent_label'=>'',
			'dependent_id'=>$field_option[3]);

      }
    }
    $field_options = serialize($field_options);

    mysql_query("INSERT INTO `se_profilefields` (`profilefield_id`, `profilefield_profilecat_id`, `profilefield_order`, `profilefield_dependency`, `profilefield_title`, `profilefield_desc`, `profilefield_error`, `profilefield_type`, `profilefield_signup`, `profilefield_style`, `profilefield_maxlength`, `profilefield_link`, `profilefield_options`, `profilefield_display`, `profilefield_required`, `profilefield_regex`, `profilefield_special`, `profilefield_html`, `profilefield_search`) VALUES 
					($field_info[field_id], $field_info[field_tab_id], $field_info[field_order], $field_info[field_dependency], $field_title, $field_desc, $field_error, $field_info[field_type], $field_info[field_signup], '$field_info[field_style]', $field_info[field_maxlength], '$field_info[field_link]', '$field_options', $field_info[field_browsable], $field_info[field_required], '$field_info[field_regex]', $field_info[field_birthday], '$field_info[field_html]', 1)") or die("Insert: se_profilefields<br>Error: ".mysql_error());


  }

  mysql_query("DROP TABLE IF EXISTS se_fields");

}






//######### MODIFY se_profilestyles
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_profilestyles FROM `$database_name` LIKE 'profilestyle_stylesample_id'")) != 1) {
  mysql_query("ALTER TABLE se_profilestyles ADD profilestyle_stylesample_id int(9) NOT NULL default '0'");
}







//######### CREATE se_profilevalues
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_profilevalues'")) == 0) {

  mysql_query("ALTER TABLE se_profiles RENAME se_profilevalues");
  $columns = mysql_query("SHOW COLUMNS FROM se_profilevalues");
  while($column_info = mysql_fetch_assoc($columns)) {
    $field_name = $column_info['Field'];
    $new_field_name = str_replace("profile", "profilevalue", $column_info['Field']);
    $field_type = $column_info['Type'];
    $field_default = $column_info['Default'];
    if($new_field_name == "profilevalue_id") {
      mysql_query("ALTER TABLE se_profilevalues CHANGE $field_name $new_field_name $field_type auto_increment");
    } elseif($field_type == "int(14)") {
      mysql_query("ALTER TABLE se_profilevalues CHANGE $field_name $new_field_name date NOT NULL default '0000-00-00'");
    } else {
      mysql_query("ALTER TABLE se_profilevalues CHANGE $field_name $new_field_name $field_type");
    }
  }
}




//######### CREATE se_profileviews
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_profileviews'")) == 0) {
  mysql_query("CREATE TABLE `se_profileviews` (
  `profileview_user_id` int(1) NOT NULL,
  `profileview_views` int(9) NOT NULL,
  `profileview_viewers` text NOT NULL,
  UNIQUE KEY `profileview_user_id` (`profileview_user_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_profileviews<br>Error: ".mysql_error());
}




//######### MODIFY se_settings
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `$database_name` LIKE 'setting_online'")) != 1) {
  mysql_query("ALTER TABLE se_settings ADD setting_online tinyint(1) NOT NULL default '1'");
  mysql_query("UPDATE se_settings SET setting_subnet_field1_id = '-2' WHERE setting_subnet_field1_id = '-1'");
  mysql_query("UPDATE se_settings SET setting_subnet_field2_id = '-2' WHERE setting_subnet_field2_id = '-1'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `$database_name` LIKE 'setting_username'")) != 1) {
  mysql_query("ALTER TABLE se_settings ADD setting_username tinyint(1) NOT NULL default '1'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `$database_name` LIKE 'setting_lang_autodetect'")) != 1) {
  mysql_query("ALTER TABLE se_settings ADD setting_lang_autodetect tinyint(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `$database_name` LIKE 'setting_lang_anonymous'")) != 1) {
  mysql_query("ALTER TABLE se_settings ADD setting_lang_anonymous tinyint(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `$database_name` LIKE 'setting_comment_html'")) != 1) {
  mysql_query("ALTER TABLE se_settings ADD setting_comment_html varchar(250) NOT NULL default ''");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `$database_name` LIKE 'setting_actions_preference'")) != 1) {
  mysql_query("ALTER TABLE se_settings ADD setting_actions_preference int(1) NOT NULL default '0'");
}



//######### CREATE se_stylesamples
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_stylesamples'")) == 0) {
  mysql_query("CREATE TABLE `se_stylesamples` (
  `stylesample_id` int(9) NOT NULL auto_increment,
  `stylesample_type` varchar(20) NOT NULL default '',
  `stylesample_name` varchar(50) NOT NULL default '',
  `stylesample_thumb` varchar(50) NOT NULL default '',
  `stylesample_css` text NOT NULL,
  PRIMARY KEY  (`stylesample_id`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_stylesamples<br>Error: ".mysql_error());

//######### INSERT DEFAULTS INTO se_stylesamples
mysql_query("INSERT INTO `se_stylesamples` (`stylesample_id`, `stylesample_type`, `stylesample_name`, `stylesample_thumb`, `stylesample_css`) VALUES (1, 'profile', 'Beige', 'beige/beige_full.gif', '/* PROFILE CSS TEMPLATE  */\r\n/*      BEIGE_FULL       */\r\n/* www.solarianoir.net   */\r\n\r\n\r\n\r\n/* GLOBAL STYLES */\r\n\r\nbody {\r\n    background:#DEDBC5 url(./images/sample_styles/beige/back_shadow.png) center repeat-y;    \r\n	/* custom cursor for IE, Firefox */ \r\n	cursor: url(./images/sample_styles/beige/cursor.cur), url(./images/sample_styles/beige/cursor.gif), auto;\r\n	/* scrollbar colours */ \r\n    scrollbar-arrow-color:#43260C;\r\n    scrollbar-track-color:#E9E2CF;\r\n    scrollbar-shadow-color:#E9E2CF;\r\n    scrollbar-face-color:#E9E2CF;\r\n    scrollbar-highlight-color:#E9E2CF;\r\n    scrollbar-darkshadow-color:#ffffff;\r\n    scrollbar-3dlight-color:#ffffff;\r\n    padding-top: 0px; /* distance between top of page and content */\r\n    padding-bottom: 0px; /* distance between bottom of page and content */\r\n}\r\n\r\n/* all links to bold */\r\na {\r\n	font-weight: bold;\r\n}\r\n\r\n/* LOGGED IN Menu Links */\r\na.menu_item:link { color: #7B7A70; text-decoration: none; }\r\na.menu_item:visited { color: #7B7A70; text-decoration: none; }\r\na.menu_item:hover { color: #222222; text-decoration: none; }\r\n\r\n/* Global Links */\r\na:link { color: #6A6962; text-decoration: none; }\r\na:visited { color: #6A6962; text-decoration: none; }\r\na:hover { color: #222222; text-decoration: none; }\r\n\r\n/* Content Box */\r\ndiv.content {\r\n	background: url(./images/sample_styles/beige/back_content.png);\r\n	padding: 6px;\r\n}\r\n\r\n/* Applies to most interior content */\r\ndiv, td {\r\n	background: transparent;\r\n	font-family: Arial; /* almost global font */\r\n	font-size: 11px;\r\n	color: #6A6962;\r\n}\r\n\r\n\r\n/* Yourname\'s profile Bar */\r\ndiv.page_header {\r\n    background: url(./images/sample_styles/beige/page_header.jpg) repeat-x;\r\n    text-align: right;\r\n    line-height: 42px;\r\n    font-size: 16px;\r\n    font-weight: bold;\r\n    color: #ffffff;\r\n    background-color: #D6D3BE;\r\n    padding: 0px 0px 0px 0px;\r\n}\r\n\r\n/* Global Headers - Titles */\r\ntd.header {\r\n	padding: 4px 2px 4px 4px;\r\n	border: 1px solid #F1EED9;\r\n	border-bottom: none;\r\n	background-image: none;\r\n	background-repeat: repeat-x;\r\n	color: #6A6962;\r\n	background: url(./images/sample_styles/beige/header.png);\r\n}\r\n\r\n\r\n\r\n/* Write Something - Post Comment */\r\ntextarea {\r\n	color: #6A6962;\r\n	height:100px;\r\n	background: none; \r\n	font-size: 12px;\r\n}\r\n\r\nimg.signup_code {\r\n	background: #ffffff;\r\n	padding: 0px;\r\n	border-width: 2px;\r\n	border-color: #ffffff;\r\n}\r\n\r\n/* image verification input */\r\ninput.text, input.text_small {\r\n	border-color: #ffffff;\r\n	font-size: 11px;\r\n	font-weight: bold;\r\n	color: #ffffff;\r\n	background-color: #B5B3A1;\r\n}\r\n\r\n/* PROFILE STYLES */\r\n\r\n/* Profile box */\r\ntd.profile {\r\n	background-color: #B8B6A7;\r\n    background: transparent;\r\n	border-width: 1px; /* adjust the width for styling of content borders - 0 to remove borders */\r\n	border-color: #F1EED9;\r\n	/* padding values to vary the distance between interior borders and their content */\r\n	padding-top: 12px;\r\n	padding-right: 22px;\r\n	padding-bottom: 12px; \r\n	padding-left: 22px;\r\n}\r\n\r\n\r\ntd.profile_tab a {\r\n	background-color: #d0cdb7;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab a:hover {\r\n	background-color: #d7d4bf;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab2 a, td.profile_tab2 a:hover {\r\n	background-color: #dedbc4;\r\n	padding: 7px 10px 8px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	border-bottom: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\n#profile_tabs_profile { \r\n	border-left: 1px solid #AAAAAA;\r\n}\r\ntd.profile_tab_end {\r\n	border-bottom: 1px solid #AAAAAA;\r\n}\r\n\r\n\r\n/* Recent Activity */\r\ndiv.profile_action  {\r\n	border-bottom-width: 1px;\r\n	border-bottom-style: solid;\r\n	border-bottom-color: #CFCDBB;\r\n	font-size: 14px;\r\n\r\n}\r\n\r\ndiv.profile_action_date {\r\n	color: #6A6962;\r\n	float: right;\r\n	padding-left: 5px;\r\n}\r\n\r\n/* Profile Photo */\r\ntd.profile_photo {\r\n	padding: 4px;\r\n	background-color: #B8B6A7;\r\n    border:1px #FFFCE2 dotted;\r\n}\r\n\r\nimg.photo {\r\n	border-top: 1px;\r\n	border-color: #DEDBC5;\r\n}\r\n\r\n/* PAGE FOOTER */\r\ndiv.copyright a:link { color: #6A6962; text-decoration: none; }\r\ndiv.copyright a:visited { color: #6A6962; text-decoration: none; }\r\ndiv.copyright a:hover { color: #222222; text-decoration: none; }'),
(2, 'profile', 'Beige II', 'beige/beige2_full.gif', '/* PROFILE CSS TEMPLATE  */\r\n/*      BEIGE2_FULL       */\r\n/* www.solarianoir.net   */\r\n\r\n\r\n\r\n/* GLOBAL STYLES */\r\n\r\nbody {\r\n    background:#DEDBC5 url(./images/sample_styles/beige/back_shadow2.jpg) top center no-repeat;    \r\n	/* custom cursor for IE, Firefox */ \r\n	cursor: url(./images/sample_styles/beige/cursor.cur), url(./images/sample_styles/beige/cursor.gif), auto;\r\n	/* scrollbar colours */ \r\n    scrollbar-arrow-color:#43260C;\r\n    scrollbar-track-color:#E9E2CF;\r\n    scrollbar-shadow-color:#E9E2CF;\r\n    scrollbar-face-color:#E9E2CF;\r\n    scrollbar-highlight-color:#E9E2CF;\r\n    scrollbar-darkshadow-color:#ffffff;\r\n    scrollbar-3dlight-color:#ffffff;\r\n    padding-top: 0px; /* distance between top of page and content */\r\n    padding-bottom: 0px; /* distance between bottom of page and content */\r\n}\r\n\r\n/* all links to bold */\r\na {\r\n	font-weight: bold;\r\n}\r\n\r\n/* LOGGED IN Menu Links */\r\na.menu_item:link { color: #7B7A70; text-decoration: none; }\r\na.menu_item:visited { color: #7B7A70; text-decoration: none; }\r\na.menu_item:hover { color: #222222; text-decoration: none; }\r\n\r\n/* Global Links */\r\na:link { color: #6A6962; text-decoration: none; }\r\na:visited { color: #6A6962; text-decoration: none; }\r\na:hover { color: #222222; text-decoration: none; }\r\n\r\n/* Content Box */\r\ndiv.content {\r\n	background: url(./images/sample_styles/beige/back_content.png);\r\n	padding: 6px;\r\n}\r\n\r\n/* Applies to most interior content */\r\ndiv, td {\r\n	background: transparent;\r\n	font-family: Arial; /* almost global font */\r\n	font-size: 11px;\r\n	color: #6A6962;\r\n}\r\n\r\n\r\n/* Yourname\'s profile Bar */\r\ndiv.page_header {\r\n    background: url(./images/sample_styles/beige/page_header2.jpg) repeat-x;\r\n    text-align: right;\r\n    line-height: 42px;\r\n    font-size: 16px;\r\n    font-weight: bold;\r\n    color: #ffffff;\r\n    background-color: #D6D3BE;\r\n    padding: 0px 0px 0px 0px;\r\n}\r\n\r\n/* Global Headers - Titles */\r\ntd.header {\r\n	padding: 4px 2px 4px 4px;\r\n	border: 1px solid #F1EED9;\r\n	border-bottom: none;\r\n	background-image: none;\r\n	background-repeat: repeat-x;\r\n	color: #6A6962;\r\n	background: url(./images/sample_styles/beige/header.png);\r\n}\r\n\r\n\r\n\r\n/* Write Something - Post Comment */\r\ntextarea {\r\n	color: #6A6962;\r\n	height:100px;\r\n	background: none; \r\n	font-size: 12px;\r\n}\r\n\r\nimg.signup_code {\r\n	background: #ffffff;\r\n	padding: 0px;\r\n	border-width: 2px;\r\n	border-color: #ffffff;\r\n}\r\n\r\n/* image verification input */\r\ninput.text, input.text_small {\r\n	border-color: #ffffff;\r\n	font-size: 11px;\r\n	font-weight: bold;\r\n	color: #ffffff;\r\n	background-color: #B5B3A1;\r\n}\r\n\r\n/* PROFILE STYLES */\r\n\r\n/* Profile box */\r\ntd.profile {\r\n	background-color: #B8B6A7;\r\n    background: transparent;\r\n	border-width: 1px; /* adjust the width for styling of content borders - 0 to remove borders */\r\n	border-color: #F1EED9;\r\n	/* padding values to vary the distance between interior borders and their content */\r\n	padding-top: 12px;\r\n	padding-right: 22px;\r\n	padding-bottom: 12px; \r\n	padding-left: 22px;\r\n}\r\n\r\n\r\n\r\ntd.profile_tab a {\r\n	background-color: #d0cdb7;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab a:hover {\r\n	background-color: #d7d4bf;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab2 a, td.profile_tab2 a:hover {\r\n	background-color: #dedbc4;\r\n	padding: 7px 10px 8px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	border-bottom: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\n#profile_tabs_profile { \r\n	border-left: 1px solid #AAAAAA;\r\n}\r\ntd.profile_tab_end {\r\n	border-bottom: 1px solid #AAAAAA;\r\n}\r\n\r\n/* Recent Activity */\r\ndiv.profile_action  {\r\n	border-bottom-width: 1px;\r\n	border-bottom-style: solid;\r\n	border-bottom-color: #CFCDBB;\r\n	font-size: 14px;\r\n\r\n}\r\n\r\ndiv.profile_action_date {\r\n	color: #6A6962;\r\n	float: right;\r\n	padding-left: 5px;\r\n}\r\n\r\n/* Profile Photo */\r\ntd.profile_photo {\r\n	padding: 4px;\r\n	background-color: #B8B6A7;\r\n    border:1px #FFFCE2 dotted;\r\n}\r\n\r\nimg.photo {\r\n	border-top: 1px;\r\n	border-color: #DEDBC5;\r\n}\r\n\r\n/* PAGE FOOTER */\r\ndiv.copyright a:link { color: #6A6962; text-decoration: none; }\r\ndiv.copyright a:visited { color: #6A6962; text-decoration: none; }\r\ndiv.copyright a:hover { color: #222222; text-decoration: none; }'),
(3, 'profile', 'Black XS', 'blackxs/blackxs_full.gif', '/* PROFILE CSS TEMPLATE  */\r\n/*     BLACKXS_FULL      */\r\n/* www.solarianoir.net   */\r\n\r\n\r\n\r\n/* GLOBAL STYLES */\r\n\r\nbody {\r\n	background: #393939 url(./images/sample_styles/blackxs/background.jpg) top center no-repeat; \r\n    /* custom cursor for Firefox, IE */ \r\n	cursor: url(./images/sample_styles/blackxs/cursor.cur), url(./images/sample_styles/pinkviolet/blackxs.gif), auto;\r\n	/* scrollbar colours */ \r\n	scrollbar-face-color: #212121;\r\n	scrollbar-highlight-color: #404040;\r\n	scrollbar-shadow-color: #000000;\r\n	scrollbar-3dlight-color: #616161;\r\n	scrollbar-arrow-color:  #A1A1A1;\r\n	scrollbar-track-color: #000000;\r\n	scrollbar-darkshadow-color: #000000;\r\n    padding-top: 126px; /* distance between top of page and content */\r\n    padding-bottom: 20px; /* distance between bottom of page and content */\r\n}\r\n\r\n\r\n/* Top Menu Links */\r\na.top_menu_item:link { color: #dddddd; background: url(./images/sample_styles/blackxs/twinkle.gif); text-decoration: none; }\r\na.top_menu_item:visited { color: #dddddd; background: url(./images/sample_styles/blackxs/twinkle.gif); text-decoration: none; }\r\na.top_menu_item:hover { color: #FFFFFF; background: none; text-decoration: none; }\r\n\r\n/* LOGGED IN Menu Links */\r\na.menu_item:link { color: #dddddd; background: url(./images/sample_styles/blackxs/twinkle.gif); text-decoration: none; }\r\na.menu_item:visited { color: #dddddd; background: url(./images/sample_styles/blackxs/twinkle.gif); text-decoration: none; }\r\na.menu_item:hover { color: #ffffff; background: none; text-decoration: none; }\r\n\r\n/* Global Links */\r\na:link { color: #dddddd; text-decoration: none; }\r\na:visited { color: #dddddd; text-decoration: none; }\r\na:hover { color: #ffffff; background: url(./images/sample_styles/blackxs/twinkle.gif); text-decoration: none; }\r\n\r\n/* Override All Links */\r\na {\r\n    font-weight: bold;\r\n}\r\n\r\n\r\n/* Content Box */\r\ntd.content {\r\n	background: transparent;\r\n	padding: 6px;\r\n}\r\n\r\n/* Applies to most interior content */\r\ndiv, td {\r\n	background: transparent;\r\n	font-family: arial; /* almost global font */\r\n	font-size: 11px;\r\n	color: #dddddd;\r\n}\r\n\r\n\r\n/* TOP MENU */\r\ntd.top_menu {\r\n	background: transparent;\r\n	border: 1px solid #AAAAAA;\r\n	border-right: none;\r\n}\r\n\r\ntd.top_menu img {\r\n	display: none;\r\n}\r\n\r\ntd.top_menu2 {\r\n	background: transparent;\r\n	border: 1px solid #AAAAAA;\r\n	border-left: none;\r\n}\r\n\r\n\r\n/* USER MENU */\r\ntd.menu_user {\r\n	background: transparent;\r\n}\r\ndiv.menu_dropdown {\r\n	background: #444444;\r\n}\r\ndiv.menu_item_dropdown a:hover {\r\n	background: transparent;\r\n}\r\n\r\n\r\n/* Yourname\'s profile Bar */\r\ndiv.page_header {\r\n	font-size: 16px;\r\n	font-weight: bold;\r\n	color: #ffffff;\r\n	background: transparent;\r\n	margin-bottom: 0px;\r\n	padding-top: 6px;\r\n	padding-left: 6px;\r\n}\r\n\r\n/* Global Headers - Titles */\r\ntd.header {\r\n	padding: 4px 2px 4px 4px;\r\n	border: 1px solid #666666;\r\n	border-bottom: none;\r\n	font-weight: bold;\r\n	background: #444444;\r\n	color: #ffffff;\r\n}\r\n\r\n\r\n\r\ntextarea {\r\n	color: #eeeeee;\r\n	height:100px;\r\n	background: transparent;\r\n	font-size: 12px;\r\n}\r\n\r\nimg.signup_code {\r\n	background: #ffffff;\r\n	padding: 0px;\r\n	border-width: 2px;\r\n	border-color: #000000;\r\n}\r\n\r\n\r\n\r\n\r\n\r\n/* PROFILE STYLES */\r\n\r\n/* Profile box */\r\ntd.profile {\r\n	background: #333333;\r\n	border-width: 1px; /* adjust the width for styling of content borders - 0 to remove borders */\r\n	border-color: #777777;\r\n	/* padding values to vary the distance between interior borders and their content */\r\n	padding-top: 12px;\r\n	padding-right: 12px;\r\n	padding-bottom: 12px; \r\n	padding-left: 12px;\r\n}\r\n\r\ntd.profile_tab a, td.profile_tab a:hover {\r\n	background-color: transparent;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab a:hover {\r\n	background-color: #333333;\r\n}\r\ntd.profile_tab2 a, td.profile_tab2 a:hover {\r\n	background-color: #333333;\r\n	padding: 7px 10px 8px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	border-bottom: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\n#profile_tabs_profile { \r\n	border-left: 1px solid #AAAAAA;\r\n}\r\ntd.profile_tab_end {\r\n	border-bottom: 1px solid #AAAAAA;\r\n}\r\ndiv.profile_content {\r\n        background-color: #333333;\r\n}\r\n\r\ntd.profile_tab a { background: transparent; }\r\ntd.profile_tab a:hover { background: transparent; background: url(./images/sample_styles/blackxs/twinkle.gif); text-decoration: none; }\r\n\r\ndiv.browse_friends_result { background: transparent; }\r\n\r\ndiv.profile_comment_author { background: transparent; }\r\ndiv.profile_comment_date { background: transparent; }\r\ndiv.profile_postcomment { background: transparent; }\r\n\r\n/* Recent Activity */\r\ndiv.profile_action  {\r\n	border-bottom-width: 1px;\r\n	border-bottom-style: solid;\r\n	border-bottom-color: #444444;\r\n	font-size: 14px;\r\n}\r\n\r\n/* Profile Photo */\r\ntd.profile_photo {\r\n	padding: 0px;\r\n	padding-bottom: 8px;\r\n	background: url(./images/sample_styles/blackxs/spark.gif) no-repeat bottom;\r\n    border:0px;\r\n    border-color: #555555;\r\n    border-style: dotted;\r\n}\r\n\r\nimg.photo {\r\n	padding: 4px;\r\n    border-color: transparent;\r\n	\r\n}\r\n\r\n\r\n/* PAGE FOOTER */\r\ndiv.copyright a:link { color: #6A6962; text-decoration: none; }\r\ndiv.copyright a:visited { color: #6A6962; text-decoration: none; }\r\ndiv.copyright a:hover { color: #222222; text-decoration: none; }\r\ndiv.copyright { background: transparent; border: 1px solid #AAAAAA; }\r\n\r\n\r\n\r\n/* image verification input */\r\ninput.text, input.text_small {\r\n	border-color: #666666;\r\n	font-size: 12px;\r\n	color: #cccccc;\r\n	background-color: #444444;\r\n}\r\n\r\n'),
(4, 'profile', 'Dark Grey Basic', 'darkgrey/darkgrey_basic.gif', '/* PROFILE CSS TEMPLATE */\r\n/*   DARKGREY_BASIC     */\r\n/* www.solarianoir.net  */\r\n\r\ntextarea {\r\n  color: #FFFFFF;\r\n}\r\n\r\ndiv.copyright {\r\n  background: transparent;\r\n  border-top: 1px solid #444444;\r\n}\r\n\r\ntextarea.comment_area {\r\n	font-family: \"Lucida Sans\", verdana, arial, serif;\r\n	color: #FFFFFF; \r\n	width: 100%;\r\n	height: 70px;\r\n}\r\ntd.profile_tab a, td.profile_tab a:hover {\r\n	background-color: #333333;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab a:hover {\r\n	background-color: #444444;\r\n}\r\ntd.profile_tab2 a, td.profile_tab2 a:hover {\r\n	background-color: #222222;\r\n	padding: 7px 10px 8px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	border-bottom: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\n#profile_tabs_profile { \r\n	border-left: 1px solid #AAAAAA;\r\n}\r\ntd.profile_tab_end {\r\n	border-bottom: 1px solid #AAAAAA;\r\n}\r\n\r\ntd.top_menu {\r\n        background: #333333;\r\n	border-top: 1px solid #666666; \r\n	border-bottom: 1px solid #666666;\r\n}\r\ntd.top_menu2 {\r\n        background: #333333;\r\n	width: 15%; \r\n	text-align: right; \r\n	border-right: 1px solid #666666; \r\n	border-top: 1px solid #666666; \r\n	border-bottom: 1px solid #666666; \r\n}\r\ntd.menu_user {\r\n        background: none;\r\n	padding: 5px 10px 5px 7px; \r\n        border: none;\r\n	text-align: left;\r\n}\r\ndiv.top_menu_link_container, div.top_menu_link_container_end {\r\n	float: left;\r\n	height: 31px;\r\n	border-left: 1px solid #666666;\r\n}\r\ndiv.top_menu_link_container_end {\r\n	border-left: 1px solid #666666;\r\n}\r\n\r\n\r\n\r\n/* GLOBAL STYLES */\r\nbody {\r\n	background: #222222;\r\n	cursor: crosshair;\r\n	/* scrollbar colours */ \r\n	scrollbar-face-color: #212121;\r\n	scrollbar-highlight-color: #404040;\r\n	scrollbar-shadow-color: #000000;\r\n	scrollbar-3dlight-color: #616161;\r\n	scrollbar-arrow-color:  #A1A1A1;\r\n	scrollbar-track-color: #000000;\r\n	scrollbar-darkshadow-color: #000000;\r\n	padding-top: 16px; /* distance between top of page and content */\r\n	padding-bottom: 20px; /* distance between bottom of page and content */\r\n}\r\n\r\n/* Top Menu Links */\r\na.top_menu_item:link { color: #cccccc; text-decoration: none; }\r\na.top_menu_item:visited { color: #cccccc; text-decoration: none; }\r\na.top_menu_item:hover { color: #FFFFFF; text-decoration: none; }\r\n\r\n/* LOGGED IN Menu Links */\r\na.menu_item:link { color: #888888; text-decoration: none; }\r\na.menu_item:visited { color: #888888; text-decoration: none; }\r\na.menu_item:hover { color: #888888; text-decoration: none; }\r\n\r\n/* Global Links */\r\na:link { color: #aaaaaa; text-decoration: none; }\r\na:visited { color: #aaaaaa; text-decoration: none; }\r\na:hover { color: #ffffff; text-decoration: none; }\r\n\r\n/* Content Box */\r\ntd.content {\r\n	background-color: #333333;\r\n	/* distance between outer borders and content box */\r\n	padding: 6px;\r\n}\r\n\r\n/* Applies to most interior content */\r\ndiv, td {\r\n	background: transparent;\r\n	font-family: arial; /* almost global font */\r\n	font-size: 11px;\r\n	color: #dddddd;\r\n        line-height: 140%; /* distance between lines of text */\r\n}\r\n\r\n/* TOP Menu */\r\ntd.topbar2, td.topbar2_right {\r\n	border: 0;\r\n	background-image: none;\r\n	background-color: #222222;\r\n	background-repeat: repeat-x; \r\n	font-weight: bold; \r\n	font-size: 16px; \r\n	padding: 10px 8px 10px 8px; \r\n	color: #FFFFFF;\r\n}\r\n\r\n/* User Logged In Menu */\r\ntd.menu {\r\n	background: none; \r\n	background-color: #222222;\r\n	border-width: 1px;\r\n	border-bottom: 4px;\r\n	border-color: #777777;\r\n}\r\n\r\n/* User Logged in Menu Items */\r\ntd.menu_item {\r\n	background: #222222;\r\n	padding-top: 5px;\r\n	padding-right: 6px;\r\n	padding-bottom: 5px;\r\n	padding-left: 6px;\r\n	font-size: 11px;\r\n	font-weight: bold;\r\n	font-family: arial;\r\n}\r\n\r\n/* User Logged in Menu Shadows */\r\ntd.shadow {\r\n	background: none; \r\n}\r\n\r\ntd.shadow img {\r\n	display: none; \r\n}\r\n\r\n/* Yourname\'s profile Bar */\r\ndiv.page_header {\r\n	font-size: 16px;\r\n	font-weight: bold;\r\n	color: #ffffff;\r\n	background-color: #333333;\r\n	margin-bottom: 6px;\r\n	padding-top: 6px;\r\n        padding-bottom: 6px;\r\n	padding-left: 6px;\r\n        border-top: 1px solid #666666;\r\n}\r\n\r\n/* Global Headers - Titles */\r\ntd.header {\r\n	padding: 4px 2px 4px 4px;\r\n	border: 1px solid #666666;\r\n	border-bottom: none;\r\n	font-weight: bold;\r\n	background-image: none;\r\n	background-repeat: repeat-x;\r\n	color: #ffffff;\r\n	background: #444444;\r\n}\r\n\r\n\r\ntextarea {\r\n	color: #eeeeee;\r\n	height:100px;\r\n	background: #444444; \r\n	border: 0px;\r\n	padding: 12px;\r\n	font-size: 12px;\r\n}\r\n\r\nimg.signup_code {\r\n	background: #ffffff;\r\n	padding: 0px;\r\n	border-width: 2px;\r\n	border-color: #000000;\r\n}\r\n\r\n#dhtmltooltip {\r\n	background: #555555;\r\n	border: 1px solid #AAAAAA;\r\n}\r\n\r\n\r\n\r\n\r\n\r\n/* PROFILE STYLES */\r\n\r\n/* Profile box */\r\ntd.profile {\r\n	background-color: #333333;\r\n	border-width: 1px; /* adjust the width for styling of content borders - 0 to remove borders */\r\n	border-color: #444444;\r\n	/* padding values to vary the distance between interior borders and their content */\r\n	padding-top: 12px;\r\n	padding-right: 22px;\r\n	padding-bottom: 12px; \r\n	padding-left: 22px;\r\n}\r\n\r\n/* Recent Activity */\r\ndiv.profile_action  {\r\n	border-bottom-width: 1px;\r\n	border-bottom-style: solid;\r\n	border-bottom-color: #444444;\r\n	font-size: 14px;\r\n}\r\n\r\n/* Profile Photo */\r\ntd.profile_photo {\r\n	border: 0px solid #aaaaaa;\r\n	padding: 4px;\r\n	background-color: #555555;\r\n    border:1px;\r\n    border-color: #000000;\r\n    border-style: dotted;\r\n}\r\n\r\nimg.photo {\r\n	border-top: 1px;\r\n	border-color: #000000;\r\n}\r\n\r\n/* Menu Options (below your profile image) */\r\ntable.profile_menu {\r\n	border: 0px;\r\n} \r\n\r\ntd.profile_menu1 a {\r\n	background-color: #333333;\r\n	background-image: none;\r\n	background-repeat: repeat-y;\r\n	background-position: top right;\r\n	border-bottom: 1px solid #444444;\r\n	padding: 5px 5px 5px 7px;\r\n	font-size: 11px;\r\n	font-weight: bold;\r\n}\r\n\r\ntd.profile_menu1 a:hover {\r\n	background-color: #555555;\r\n        color: #ffffff;\r\n	font-weight: bold;\r\n	background-image: none;\r\n}\r\n\r\n/* Comments Section */\r\ndiv.profile_postcomment {\r\n	padding: 8px;\r\n	border: none;\r\n    border-bottom: 1px solid #555555;\r\n    background: #333333;	\r\n    color: #cccccc;\r\n}\r\n\r\ndiv.profile_comment_author {\r\n        background: #333333;\r\n        border-top: 1px solid #666666;\r\n}\r\ndiv.profile_comment_date {\r\n        background: #333333;\r\n        border-top: 1px solid #666666;\r\n}\r\n\r\n/* image verification input */\r\ninput.text, input.text_small {\r\n	border-color: #666666;\r\n	font-size: 12px;\r\n	color: #cccccc;\r\n	background-color: #444444;\r\n}\r\n\r\n/* Events Section */\r\ndiv.profile_event_spacer {\r\n	border-top: 2px solid #555555; \r\n 	margin: 0px 0px 0px 0px;\r\n    padding: 4px;\r\n}\r\n\r\ntd.profile_event_popup_title {\r\n	font-size: 11pt;\r\n	vertical-align: bottom;\r\n	font-weight: bold;\r\n    padding: 10px;\r\n}\r\n\r\ntd.profile_event_popup2 {\r\n	background: #ffffff;\r\n	width: 640px; \r\n	padding: 8px;\r\n}\r\n\r\ntd.profile_event_transparent {\r\n	background: #000000;\r\n	opacity: 0.5; \r\n	filter: alpha(opacity=20); \r\n	-moz-opacity: 0.2;\r\n}'),
(5, 'profile', 'Dark Grey Full', 'darkgrey/darkgrey_basic_images.gif', '/* PROFILE CSS TEMPLATE  */\r\n/* DARKGREY_BASIC_IMAGES */\r\n/* www.solarianoir.net   */\r\n\r\n\r\ntd.menu_user {\r\n  background: #444444;\r\n}\r\ntd.top_menu {\r\n  background: #444444;\r\n}\r\ntd.top_menu2 {\r\n  background: #444444;\r\n}\r\ndiv.top_menu_link, div.top_menu_link_container_end, div.top_menu_link, div.top_menu_link_container, div.top_menu_link_loggedin {\r\n  background: transparent;\r\n}\r\n\r\n\r\ntd.profile_tab a, td.profile_tab a:hover {\r\n	background-color: #333333;\r\n	padding: 7px 10px 7px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n        border-top: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\ntd.profile_tab a:hover {\r\n	background-color: #444444;\r\n}\r\ntd.profile_tab2 a, td.profile_tab2 a:hover {\r\n	background-color: #222222;\r\n	padding: 7px 10px 8px 10px;\r\n	border: 1px solid #AAAAAA; \r\n	border-left: none;\r\n	border-bottom: none;\r\n        border-top: none;\r\n	font-weight: bold; \r\n	display: block;\r\n}\r\n#profile_tabs_profile { \r\n	border-left: 1px solid #AAAAAA;\r\n}\r\ntd.profile_tab_end {\r\n	border-bottom: 1px solid #AAAAAA;\r\n}\r\n\r\n\r\n/* GLOBAL STYLES */\r\nbody {\r\n	background: #222222;\r\n	cursor: crosshair;\r\n	/* scrollbar colours */ \r\n	scrollbar-face-color: #212121;\r\n	scrollbar-highlight-color: #404040;\r\n	scrollbar-shadow-color: #000000;\r\n	scrollbar-3dlight-color: #616161;\r\n	scrollbar-arrow-color:  #A1A1A1;\r\n	scrollbar-track-color: #000000;\r\n	scrollbar-darkshadow-color: #000000;\r\n    padding-top: 16px; /* distance between top of page and content */\r\n    padding-bottom: 20px; /* distance between bottom of page and content */\r\n}\r\n\r\n/* Top Menu Links */\r\na.top_menu_item:link { color: #cccccc; text-decoration: none; }\r\na.top_menu_item:visited { color: #cccccc; text-decoration: none; }\r\na.top_menu_item:hover { color: #FFFFFF; text-decoration: none; }\r\n\r\n/* LOGGED IN Menu Links */\r\na.menu_item:link { color: #aaaaaa; text-decoration: none; }\r\na.menu_item:visited { color: #aaaaaa; text-decoration: none; }\r\na.menu_item:hover { color: #ffffff; text-decoration: none; }\r\n\r\n/* Global Links */\r\na:link { color: #aaaaaa; text-decoration: none; }\r\na:visited { color: #aaaaaa; text-decoration: none; }\r\na:hover { color: #ffffff; text-decoration: none; }\r\n\r\n/* Content Box */\r\ntd.content {\r\n	background-color: #222222;\r\n	/* distance between outer borders and content box */\r\n	padding: 10px;\r\n}\r\n\r\n/* Applies to most interior content */\r\ndiv, td {\r\n	font-family: arial; /* almost global font */\r\n	font-size: 11px;\r\n	color: #dddddd;\r\n    line-height: 140%; /* distance between lines of text */\r\n	text-align:justify;\r\n}\r\n\r\n\r\n/* TOP Menu */\r\ntd.topbar2, td.topbar2_right {\r\n	border: 0;\r\n	background-color: #222222;\r\n    background-image: url(./images/sample_styles/darkgrey/menu_bg.gif);\r\n	background-repeat: repeat-x; \r\n	font-weight: bold; \r\n	font-size: 16px; \r\n	padding: 10px 8px 10px 8px; \r\n	color: #FFFFFF;\r\n}\r\n\r\n/* User Logged In Menu */\r\ntd.menu {\r\n    background: #333333;\r\n/*    background-image: url(./images/sample_styles/darkgrey/menu2_bg.gif); 	*/\r\n    border-width: 0px;\r\n	border-bottom: 4px;\r\n	border-color: #777777;\r\n    padding-top:10px;\r\n    background-position: bottom;\r\n}\r\n\r\n/* User Logged in Menu Items */\r\ntd.menu_item {\r\n	background: #333333;\r\n/*    background-image: url(./images/sample_styles/darkgrey/menu2_bg.gif); 	*/\r\n	padding-top: 0px;\r\n	padding-right: 6px;\r\n	padding-bottom: 5px;\r\n	padding-left: 6px;\r\n	font-size: 11px;\r\n	font-weight: bold;\r\n	font-family: arial;\r\n    background-position: bottom;\r\n}\r\n\r\n/* User Logged in Menu Shadows */\r\ntd.shadow {\r\n	background: none; \r\n	visibility:hidden;\r\n}\r\n\r\ntd.shadow img {\r\n	display: none;\r\n    visibility:hidden; \r\n}\r\n\r\n/* Yourname\'s profile Bar */\r\ndiv.page_header {\r\n	font-size: 16px;\r\n	font-weight: bold;\r\n	color: #ffffff;\r\n	background-color: #333333;\r\n	margin-bottom: 10px;\r\n        padding: 8px 10px 8px 10px;\r\n        border-top: 1px solid #555555;\r\n}\r\n\r\n/* Global Headers - Titles */\r\ntd.header {\r\n	padding: 4px 2px 4px 4px;\r\n	border: 1px solid #666666;\r\n	border-bottom: none;\r\n	font-weight: bold;\r\n	background: #666666;\r\n    background-image: url(./images/sample_styles/darkgrey/header.gif);\r\n	background-repeat: repeat-x;\r\n	color: #ffffff;\r\n	\r\n}\r\n\r\n\r\n\r\n/* PROFILE STYLES */\r\n\r\n/* Profile Box */\r\ntd.profile {\r\n	background-color: #333333;\r\n	background-image: none;\r\n	background-repeat: repeat;\r\n	background-attachment: scroll;\r\n	border-width: 1px; /* adjust the width for styling of content borders */\r\n	border-color: #444444;\r\n	margin-bottom: 0px;\r\n	/* padding values to vary the distance between interior element borders and their content */\r\n	padding-top: 12px;\r\n	padding-right: 12px;\r\n	padding-bottom: 12px; \r\n	padding-left: 12px;\r\n}\r\n\r\n/* Recent Activity */\r\ndiv.profile_action  {\r\n	border-bottom-width: 1px;\r\n	border-bottom-style: solid;\r\n	border-bottom-color: #444444;\r\n	font-size: 14px;\r\n}\r\n\r\n/* Profile Photo */\r\ntd.profile_photo {\r\n	border: 0px solid #aaaaaa;\r\n	padding: 4px;\r\n	background-color: #555555;\r\n    border:1px;\r\n    border-color: #000000;\r\n    border-style: dotted;\r\n}\r\n\r\nimg.photo {\r\n	border-top: 1px;\r\n	border-color: #000000;\r\n}\r\n\r\n/* Menu Options (below your profile image) */\r\ntable.profile_menu {\r\n	border: 0px;\r\n} \r\n\r\ntd.profile_menu1 a {\r\n	background-color: #333333;\r\n	background-image: none;\r\n	background-repeat: repeat-y;\r\n	background-position: top right;\r\n	border-bottom: 1px solid #555555;\r\n	padding: 5px 5px 5px 7px;\r\n	font-size: 11px;\r\n	font-weight: bold;\r\n}\r\n\r\ntd.profile_menu1 a:hover {\r\n	background-color: #555555;\r\n    color: #ffffff;\r\n	font-weight: bold;\r\n	background-image: none;\r\n}\r\n\r\n/* Comments Section */\r\ntd.profile_viewcomments_postcomment {\r\n	padding: 1px;\r\n    color: #cccccc;\r\n	border: 0px solid #CCCCCC;\r\n	background: #333333;\r\n}\r\n\r\ntd.profile_postcomment {\r\n	padding: 18px;\r\n	border: none;\r\n    border-bottom: 1px solid #555555;\r\n    background: #333333;	\r\n    color: #cccccc;\r\n}\r\n\r\ntd.profile_comment_author {\r\n	padding: 5px 7px 5px 7px;\r\n	background: #444444;\r\n}\r\n\r\ntextarea {\r\n	color: #eeeeee;\r\n	height:100px;\r\n	background: #444444; \r\n	border: 0px;\r\n	padding: 12px;\r\n	font-size: 12px;\r\n}\r\n\r\nimg.signup_code {\r\n	background: #ffffff;\r\n	padding: 0px;\r\n	border-width: 2px;\r\n	border-color: #000000;\r\n}\r\n\r\n#dhtmltooltip {\r\n	background: #555555;\r\n	border: 1px solid #AAAAAA;\r\n}\r\n\r\n\r\n/* Events Section */\r\ndiv.profile_event_spacer {\r\n	border-top: 2px solid #555555; \r\n 	margin: 0px 0px 0px 0px;\r\n    padding: 4px;\r\n}\r\ntd.profile_event_popup_title {\r\n	font-size: 11pt;\r\n	vertical-align: bottom;\r\n	font-weight: bold;\r\n    padding: 10px;\r\n}\r\n\r\ntd.profile_event_popup2 {\r\n	background: #ffffff;\r\n	width: 640px; \r\n	padding: 8px;\r\n}\r\n\r\ntd.profile_event_transparent {\r\n	background: #000000;\r\n	opacity: 0.5; \r\n	filter: alpha(opacity=20); \r\n	-moz-opacity: 0.2;\r\n}')") or die("Insert: se_stylesamples<br>Error: ".mysql_error());
}









//######### CREATE se_systememails
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_systememails'")) == 0) {
  mysql_query("CREATE TABLE `se_systememails` (
  `systememail_id` int(9) NOT NULL auto_increment,
  `systememail_name` varchar(100) NOT NULL,
  `systememail_title` int(9) NOT NULL,
  `systememail_desc` int(9) NOT NULL,
  `systememail_subject` int(9) NOT NULL,
  `systememail_body` int(9) NOT NULL,
  `systememail_vars` varchar(250) NOT NULL,
  PRIMARY KEY  (`systememail_id`),
  UNIQUE KEY `systememail_name` (`systememail_name`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_syestememails<br>Error: ".mysql_error());

  //######### INSERT DEFAULTS INTO se_systememails
  mysql_query("INSERT INTO `se_systememails` (`systememail_id`, `systememail_name`, `systememail_title`, `systememail_desc`, `systememail_subject`, `systememail_body`, `systememail_vars`) VALUES 
					(1, 'invitecode', 518, 519, 850001, 850002, '[displayname],[email],[message],[code],[link]'),
					(2, 'invite', 523, 524, 850003, 850004, '[displayname],[email],[message],[link]'),
					(3, 'verification', 526, 527, 850005, 850006, '[displayname],[email],[link]'),
					(4, 'newpassword', 529, 530, 850007, 850008, '[displayname],[email],[password],[link]'),
					(5, 'welcome', 532, 533, 850009, 850010, '[displayname],[email],[password],[link]'),
					(6, 'lostpassword', 535, 536, 850011, 850012, '[displayname],[email],[link]'),
					(7, 'friendrequest', 538, 539, 850013, 850014, '[displayname],[friendname],[link]'),
					(8, 'message', 541, 542, 850015, 850016, '[displayname],[sender],[link]'),
					(9, 'profilecomment', 544, 545, 850017, 850018, '[displayname],[commenter],[link]')") or die("Insert: se_systememails<br>Error: ".mysql_error());
}








//######### CREATE se_users
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_profilecat_id'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_profilecat_id int(9) NOT NULL default '0'");
  mysql_query("UPDATE se_users SET user_profilecat_id=1");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_fname'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_fname varchar(100) NOT NULL default ''");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_lname'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_lname varchar(100) NOT NULL default ''");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_language_id'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_language_id int(9) NOT NULL default '0'");
  mysql_query("UPDATE se_users SET user_language_id=1");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_ip_signup'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_ip_signup varchar(15) NOT NULL default ''");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_ip_lastactive'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_ip_lastactive varchar(15) NOT NULL default ''");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_status_date'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_status_date int(14) NOT NULL");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_invisible'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_invisible int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_saveviews'")) != 1) {
  mysql_query("ALTER TABLE se_users ADD user_saveviews int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_search'")) != 1) {
  mysql_query("ALTER TABLE se_users CHANGE user_privacy_search user_search int(1) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_privacy'")) != 1) {
  mysql_query("UPDATE se_users SET user_privacy_profile='63' WHERE user_privacy_profile='0'");
  mysql_query("UPDATE se_users SET user_privacy_profile='31' WHERE user_privacy_profile='1'");
  mysql_query("UPDATE se_users SET user_privacy_profile='15' WHERE user_privacy_profile='2'");
  mysql_query("UPDATE se_users SET user_privacy_profile='7' WHERE user_privacy_profile='3'");
  mysql_query("UPDATE se_users SET user_privacy_profile='3' WHERE user_privacy_profile='4'");
  mysql_query("UPDATE se_users SET user_privacy_profile='1' WHERE user_privacy_profile='5'");
  mysql_query("UPDATE se_users SET user_privacy_profile='0' WHERE user_privacy_profile='6'");
  mysql_query("ALTER TABLE se_users CHANGE user_privacy_profile user_privacy int(2) NOT NULL default '0'");
  
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_comments'")) != 1) {
  mysql_query("UPDATE se_users SET user_privacy_comments='63' WHERE user_privacy_comments='0'");
  mysql_query("UPDATE se_users SET user_privacy_comments='31' WHERE user_privacy_comments='1'");
  mysql_query("UPDATE se_users SET user_privacy_comments='15' WHERE user_privacy_comments='2'");
  mysql_query("UPDATE se_users SET user_privacy_comments='7' WHERE user_privacy_comments='3'");
  mysql_query("UPDATE se_users SET user_privacy_comments='3' WHERE user_privacy_comments='4'");
  mysql_query("UPDATE se_users SET user_privacy_comments='1' WHERE user_privacy_comments='5'");
  mysql_query("UPDATE se_users SET user_privacy_comments='0' WHERE user_privacy_comments='6'");
  mysql_query("ALTER TABLE se_users CHANGE user_privacy_comments user_comments int(2) NOT NULL default '0'");
}






//######### CREATE se_usersettings
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_usersettings FROM `$database_name` LIKE 'usersetting_actions_display'")) != 1) {
  mysql_query("ALTER TABLE se_usersettings ADD usersetting_actions_display varchar(150) NOT NULL default ''");
  // UPDATE DISPLAYED FEED ITEMS
}







//######### CREATE se_visitors
if(mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_visitors'")) == 0) {
  mysql_query("CREATE TABLE `se_visitors` (
  `visitor_ip` varchar(50) NOT NULL default '',
  `visitor_lastactive` int(14) NOT NULL default '0',
  UNIQUE KEY `visitor_ip` (`visitor_ip`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die("Create: se_visitors<br>Error: ".mysql_error());
}







//######### BELOW ARE MODIFICATIONS SPECIFICALLY FOR V3.03 ##########################################################################


//######### MODIFY se_actions
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_actions FROM `$database_name` LIKE 'action_object_owner'")) != 1) {
  mysql_query("ALTER TABLE se_actions ADD action_object_owner varchar(10) collate utf8_unicode_ci NOT NULL default ''");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_actions FROM `$database_name` LIKE 'action_object_owner_id'")) != 1) {
  mysql_query("ALTER TABLE se_actions ADD action_object_owner_id int(9) NOT NULL default '0'");
}
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_actions FROM `$database_name` LIKE 'action_object_privacy'")) != 1) {
  mysql_query("ALTER TABLE se_actions ADD action_object_privacy int(2) NOT NULL default '0'");
}




//######### INSERT DEFAULTS INTO se_languagevars
if(mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1183 LIMIT 1")) != 1) {
  mysql_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES 
  (1183, 1, 'Having trouble uploading files? Click here to use the simple uploader.', ''),
  (1184, 1, 'File 1:', ''),
  (1185, 1, 'File 2:', ''),
  (1186, 1, 'File 3:', ''),
  (1187, 1, 'File 4:', ''),
  (1188, 1, 'File 5:', ''),
  (1189, 1, 'Upload Selected Files', ''),
  (1190, 1, 'Uploading', ''),
  (1191, 1, 'Add Files', ''),
  (1192, 1, 'Overall Progress', ''),
  (1193, 1, 'File Progress', ''),
  (1194, 1, 'Please specify a file to upload by clicking the \"Add Files\" link.', ''),
  (1195, 1, 'Upload with %1\$s/2. Time left: ~%2\$s', ''),
  (1196, 1, 'Upload complete!', ''),
  (1197, 1, 'Search Friends', ''),
  (1198, 1, 'New Updates', ''),
  (1199, 1, 'You have %1\$s new update(s):', ''),
  (1200, 1, 'Enable Plugin', ''),
  (1201, 1, 'Disable Plugin', '')") or die("Insert: se_languagevars (5)<br>Error: ".mysql_error());
}




//######### MODIFY se_plugins
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_plugins FROM `$database_name` LIKE 'plugin_disabled'")) != 1) {
  mysql_query("ALTER TABLE se_plugins ADD plugin_disabled tinyint(1) NOT NULL default '0'");
}




//######### INSERT DEFAULTS INTO se_languagevars
// Break this up for those people who don't bother to run the upgrade script and the TOS gets inserted and causes this query to fail.
if(mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1202 LIMIT 1")) != 1) {
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      (1202, 1, 'Subcategories', NULL),
      (1203, 1, 'Add Subcategory', NULL),
      (1204, 1, 'Photos of %1\$s (%2\$d)', NULL),
      (1205, 1, 'Photos of <a href=\'%1\$s\'>%2\$s</a>', NULL),
      (1206, 1, 'download this file', NULL),
      (1207, 1, 'Viewing #%1\$d of %2\$d <a href=\'%3\$s\'>photos of %4\$s</a> &nbsp;|&nbsp; <a href=\'%5\$s\'>Return to %4\$s\'s Profile</a>', NULL),
      (1208, 1, 'Last', NULL),
      (1209, 1, 'Next', NULL)
  ") or die("Insert: se_languagevars (6)<br>Error: ".mysql_error());
}

if(mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1210 LIMIT 1")) != 1) {
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      (1210, 1, 'These are the terms of service of this social network.', NULL)
  ") or die("Insert: se_languagevars (7)<br>Error: ".mysql_error());
}

if(mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1211 LIMIT 1")) != 1) {
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      (1211, 1, 'Plugin Settings', NULL),
      (1212, 1, 'Add Tag', NULL),
      (1213, 1, 'Type a tag or select a name from the list:', NULL),
      (1214, 1, ' (me)', NULL),
      (1215, 1, 'Save', NULL),
      (1216, 1, 'From <a href=\'%1\$s\'>%2\$s</a> by <a href=\'%3\$s\'>%4\$s</a>', NULL),
      (1217, 1, 'From <a href=\'%1\$s\'>%2\$s</a>', NULL),
      (1218, 1, 'In this photo: ', NULL),
      (1219, 1, 'Uploaded %1\$s', NULL),
      (1220, 1, 'Share This', NULL),
      (1221, 1, 'Report Inappropriate Content', NULL),
      (1222, 1, 'To share this photo or embed it on another web page, please copy and paste the code of your choosing:', NULL),
      (1223, 1, 'Direct Link', NULL),
      (1224, 1, 'HTML - Embedded Image', NULL),
      (1225, 1, 'HTML - Text Link', NULL),
      (1226, 1, 'UBB Code (for forums)', NULL),
      (1227, 1, 'Close Window', NULL),
      (1228, 1, 'remove tag', NULL)
  ") or die("Insert: se_languagevars (8)<br>Error: ".mysql_error());
}



//######### MODIFY se_notifytypes
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_notifytypes FROM `$database_name` LIKE 'notifytype_url'")) == 1) {
  mysql_query("ALTER TABLE se_notifytypes CHANGE notifytype_url notifytype_url varchar(100) NOT NULL default ''");
}


//######### MODIFY USERSETTINGS TABLE
$field_info = mysql_fetch_assoc(mysql_query("SHOW COLUMNS FROM se_usersettings FROM `$database_name` LIKE 'usersetting_user_id'"));
if( $field_info['Key']!="UNI" )
{
  mysql_query("DELETE t1 FROM se_usersettings AS t1, se_usersettings AS t2 WHERE t1.usersetting_user_id=t2.usersetting_user_id && t1.usersetting_id<t2.usersetting_id");
  mysql_query("ALTER TABLE `se_usersettings` ADD UNIQUE (`usersetting_user_id`)");
}



//######### MODIFY FRIENDS TABLE
$field_info = mysql_fetch_assoc(mysql_query("SHOW COLUMNS FROM se_friends FROM `$database_name` LIKE 'friend_status'"));
if( !$field_info['Key'] )
{
  mysql_query("DELETE t1 FROM se_friends AS t1, se_friends AS t2 WHERE (t1.friend_user_id1=t2.friend_user_id1 && t1.friend_user_id2=t2.friend_user_id2 && t1.friend_id<t2.friend_id)");
  mysql_query("ALTER TABLE `se_friends` DROP INDEX 'INDEX'");
  mysql_query("ALTER TABLE `se_friends` ADD UNIQUE `friend_user_id` (`friend_user_id1`,`friend_user_id2`)");
  mysql_query("ALTER TABLE `se_friends` ADD INDEX `friend_status` (`friend_status`)");
}



//######### MODIFY ACTIONS TABLE
$field_info = mysql_fetch_assoc(mysql_query("SHOW COLUMNS FROM se_actions FROM `$database_name` LIKE 'action_date'"));
if( !$field_info['Key'] )
{
  mysql_query("ALTER TABLE `se_actions` ADD INDEX `action_date` (`action_date`)");
}



//######### MODIFY USER TABLE
$field_info = mysql_fetch_assoc(mysql_query("SHOW COLUMNS FROM se_users FROM `$database_name` LIKE 'user_email'"));
if( $field_info['Key']=="MUL" )
{
  mysql_query("ALTER TABLE `se_users` DROP INDEX `UNIQUE`");
  mysql_query("ALTER TABLE `se_users` DROP INDEX `INDEX`");
  mysql_query("ALTER TABLE `se_users` ADD UNIQUE `user_username` (`user_username`), ADD UNIQUE `user_email` (`user_email`)");
}



//######### ALTER se_subnets LANGUAGIFY subnet_name
$sql = "SHOW FULL COLUMNS FROM `se_subnets` LIKE 'subnet_name'";
$resource = mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
$column_info = ( mysql_num_rows($resource) ? mysql_fetch_assoc($resource) : NULL );
 
// Fix collation, load text, drop columns
$subnet_info = array();
if( $column_info && strtoupper(substr($column_info['Type'], 0, 7))=="VARCHAR" )
{
  // Load title column
  $sql = "SELECT * FROM se_subnets";
  $resource = mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
   
  if( mysql_num_rows($resource) )
    while( $result=mysql_fetch_assoc($resource) )
      $subnet_info[] = $result;
   
  // Drop column
  $sql = "ALTER TABLE se_subnets DROP COLUMN subnet_name";
  mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
   
  unset($column_info);
}
 
// Add columns
if( !$column_info )
{
  $sql = "ALTER TABLE se_subnets ADD COLUMN subnet_name  INT UNSIGNED NOT NULL default 0";
  mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
}
 
// Update column
if( !empty($subnet_info) )
{
  // Get language var id
  $sql = "SELECT MAX(languagevar_id) AS languagevar_id_max FROM se_languagevars WHERE languagevar_id>=666001 && languagevar_id<700000";
  mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  if( mysql_num_rows($resource) )
    $result = mysql_fetch_assoc($resource);
  $languagevar_index = ( !empty($result['languagevar_id_max']) && $result['languagevar_id_max']>=666001 && $result['languagevar_id_max']<700000 ? $result['languagevar_id_max'] + 1 : 666001 );
   
   
  // Update column
  foreach( $subnet_info as $subnet_info_array )
  {
    // For v3 installations, make sure it hasn't been translated yet
    if( is_numeric($subnet_info_array['subnet_name']) && $subnet_info_array['subnet_name']>=666001 && $subnet_info_array['subnet_name']<700000 )
    {
      $subnet_name_lvid = $subnet_info_array['subnet_name'];
    }
     
    else
    {
      // Languagify
      $sql = "INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$languagevar_index', '1', '".str_replace("'", "\'", $subnet_info_array['subnet_name'])."')";
      mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
      $subnet_name_lvid = $languagevar_index;
     
      // Increment
      $languagevar_index++;
    }
     
    // Update se_subnets
    $sql = "
      UPDATE
        se_subnets
      SET
        subnet_name='{$subnet_name_lvid}'
      WHERE
        subnet_id='{$subnet_info_array['subnet_id']}'
      LIMIT
        1
    ";
     
    mysql_query($sql) or die("<b>Error: </b>".mysql_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
}




//######### INSERT DEFAULTS INTO se_languagevars
if( !mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1229 && languagevar_language_id=1 LIMIT 1")) )
{
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      (1229, 1, 'The file \"%2\$s\" exceeds the max allowed file size: %1\$s bytes', 'user_upload'),
      (1230, 1, 'You may not upload more than %1\$s file(s) at a time.', 'user_upload'),
      (1231, 1, 'Unknown Error', 'user_upload'),
      (1232, 1, 'The extension of the file \"%2\$s\" is not in the list of allowed extensions: %1\$s', 'user_upload')
  ") or die("Insert: se_languagevars (9)<br>Error: ".mysql_error());
}






// ######### 3.1.0 #########


//######### MODIFY se_settings
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_version'")) )
{
  mysql_query("ALTER TABLE se_settings ADD `setting_version` varchar(16) collate utf8_unicode_ci NOT NULL default '' AFTER setting_key");
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_cache_enabled'")) )
{
  mysql_query("ALTER TABLE se_settings ADD setting_cache_enabled TINYINT(1) UNSIGNED default 0");
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_cache_default'")) )
{
  mysql_query("ALTER TABLE se_settings ADD setting_cache_default VARCHAR(32) default 'file'");
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_cache_lifetime'")) )
{
  mysql_query("ALTER TABLE se_settings ADD setting_cache_lifetime INT(9) UNSIGNED default 120");
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_cache_file_options'")) )
{
  mysql_query("ALTER TABLE se_settings ADD setting_cache_file_options TEXT NULL");
  mysql_query("UPDATE se_settings SET setting_cache_file_options='".addslashes(serialize(array('root'=>'./cache','locking' => TRUE)))."'") or die(mysql_error());
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_cache_memcache_options'")) )
{
  mysql_query("ALTER TABLE se_settings ADD setting_cache_memcache_options TEXT NULL");
  mysql_query("UPDATE se_settings SET setting_cache_memcache_options='".addslashes(serialize(array('servers'=>array(array('host'=>'localhost', 'port'=>11211)))))."'");
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_session_options'")) )
{
  mysql_query("ALTER TABLE se_settings ADD setting_session_options TEXT NULL");
  mysql_query("UPDATE se_settings SET setting_session_options='".addslashes(serialize(array('storage'=>'none','expire'=>259200,'name'=>md5(uniqid(rand(), true)),'servers'=>array(array('host'=>'localhost', 'port'=>11211)))))."'");
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_contact_code'")) )
  mysql_query("ALTER TABLE se_settings ADD setting_contact_code tinyint(1) unsigned NOT NULL default 1");

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_login_code'")) )
  mysql_query("ALTER TABLE se_settings ADD setting_login_code tinyint(1) unsigned NOT NULL default 0");

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_login_code_failedcount'")) )
  mysql_query("ALTER TABLE se_settings ADD setting_login_code_failedcount smallint(2) unsigned NOT NULL default 0");


//######### MODIFY se_profilecats
/* THIS WILL PROBABLY BE INCLUDED IN THE FIELD SYSTEM REWRITE
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_profilecats FROM `{$database_name}` LIKE 'profilecat_displayname_method_allowed'")) != 1)
{
  mysql_query("ALTER TABLE se_profilecats ADD profilecat_displayname_method_allowed   SMALLINT NOT NULL default '15'");
  mysql_query("ALTER TABLE se_profilecats ADD profilecat_displayname_method_custom    VARCHAR(64) collate utf8_unicode_ci NOT NULL default '%1\$s %2\$s'");
  mysql_query("ALTER TABLE se_profilecats ADD profilecat_displayname_method_default   TINYINT(1) NOT NULL default '1'");
}
*/


//######### MODIFY se_usersettings
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_usersettings FROM `{$database_name}` LIKE 'usersetting_displayname_method'")) != 1)
{
  mysql_query("ALTER TABLE se_usersettings ADD usersetting_displayname_method tinyint(1) NOT NULL default 1");
}


//######### MODIFY se_users
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `{$database_name}` LIKE 'user_displayname'")) != 1)
{
  mysql_query("ALTER TABLE se_users ADD user_displayname varchar(128) collate utf8_unicode_ci default NULL AFTER user_username");
  
  // Populate displayname column
  mysql_query("UPDATE se_users SET user_displayname=CONCAT(user_fname, ' ', user_lname)");
}


//######### MODIFY se_visitors
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_visitors FROM `{$database_name}` LIKE 'visitor_browser'")) )
{
  mysql_query("DROP TABLE se_visitors");
  
  mysql_query("
    CREATE TABLE `se_visitors` (
      `visitor_ip`                int(11)                 NOT NULL default 0,
      `visitor_browser`           varchar(255)            NOT NULL default '',
      `visitor_user_id`           int(10)       unsigned  NOT NULL default 0,
      `visitor_user_username`     varchar(64)                 NULL,
      `visitor_user_displayname`  varchar(128)                NULL,
      `visitor_lastactive`        int(14)                 NOT NULL default 0,
      `visitor_invisible`         tinyint(14)             NOT NULL default 0,
      UNIQUE KEY `UNIQUE` (`visitor_ip`, `visitor_browser`(64), `visitor_user_id`),
      KEY `LASTACTIVE` (`visitor_lastactive`)
    )
    ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci
  ") or die("Create: se_visitors<br>Error: ".mysql_error());
}


//######### MODIFY se_plugins
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_plugins FROM `$database_name` LIKE 'plugin_order'")) != 1)
{
  mysql_query("ALTER TABLE se_plugins ADD plugin_order smallint(3) NOT NULL default '0'");
  mysql_query("UPDATE se_plugins SET plugin_order=plugin_id");
}


//######### MODIFY se_admins
if(mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_admins FROM `$database_name` LIKE 'admin_language_id'")) != 1)
{
  mysql_query("ALTER TABLE se_admins ADD `admin_language_id` smallint(3) NOT NULL default 1");
}


//######### MODIFY se_levels
if( mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_levels FROM `$database_name` LIKE 'level_message_recipients'")) )
{
  mysql_query("ALTER TABLE se_levels CHANGE level_message_recipients level_message_recipients int(3) NOT NULL default '1'");
  mysql_query("UPDATE se_levels SET level_message_recipients=1 WHERE level_message_recipients=0");
}




//######### INSERT DEFAULTS INTO se_languagevars
if( !mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1233 && languagevar_language_id=1 LIMIT 1")) )
{
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      /* admin_banning */
      (1233, 1, 'Require users to enter validation code when logging in?', 'admin_banning'),
      (1234, 1, 'If you have selected Yes, an image containing a random sequence of 6 numbers will be shown to users on the \"login\" page. Users will be required to enter these numbers into the Verification Code field in order to login. This feature helps prevent users from trying to spam the login form. For this feature to work properly, your server must have the GD Libraries (2.0 or higher) installed and configured to work with PHP. If you are seeing errors, try turning this off.', 'admin_banning'),
      (1235, 1, 'Yes, enable validation code for logging in.', 'admin_banning'),
      (1236, 1, 'No, disable validation code for logging in.', 'admin_banning'),
      (1237, 1, 'If \"no\" is selected in the setting directly above, a Verification Code will be displayed to the user only after a certain number of failed logins. You can set this to 0 to never display a code.', 'admin_banning'),
      (1238, 1, 'failed logins', 'admin_banning'),
      (1239, 1, 'Require users to enter validation code when using the contact form?', 'admin_banning'),
      (1240, 1, 'If you have selected Yes, an image containing a random sequence of 6 numbers will be shown to users on the \"contact\" page. Users will be required to enter these numbers into the Verification Code field in order to contact you. This feature helps prevent users from trying to spam the contact form. For this feature to work properly, your server must have the GD Libraries (2.0 or higher) installed and configured to work with PHP. If you are seeing errors, try turning this off.', 'admin_banning'),
      (1241, 1, 'Yes, enable validation code for the contact form.', 'admin_banning'),
      (1242, 1, 'No, disable validation code for the contact form.', 'admin_banning'),
      
      /* admin_header */
      (1243, 1, 'Caching', 'admin_cache, admin_header'),
      (1244, 1, 'Sessions', 'admin_header, admin_session'),
      
      /* admin_cache */
      (1245, 1, 'For very large social networks, it may be necessary to enable caching to improve performance. If there is a noticable decrease in performance on your social network, consider enabling caching below (or upgrading your hardware). Caching takes some load off the database server by storing commonly retrieved data in temporary files (file-based caching) or memcached (memory-based caching). If you are not familiar with caching, we don\'t recommend that you change any of these settings.', 'admin_header, admin_cache'),
      (1246, 1, 'Once you have set up caching, you can generate a configuration file to put in your include folder. This will allow the cache to initialize earlier and will be able to cache the site settings, which contain the cache connection info.', 'admin_cache'),
      (1247, 1, 'Click <a href=\"%1\$s\" onclick=\"%2\$s\">here</a> to generate.', 'admin_cache'),
      (1248, 1, 'Server', 'admin_cache, admin_session'),
      (1249, 1, 'General Cache Settings', 'admin_cache'),
      (1250, 1, 'Enable caching?', 'admin_cache'),
      (1251, 1, 'Enabling caching will decrease the CPU usage of your database server (resulting in a decrease of page load times). While some non-critical data may appear slightly out of date, this will usually not be noticable to users. For example, some of the general site statistics on your homepage may take longer to update. This will not be noticable if your social network is already large and populated with a lot of data.', 'admin_cache'),
      (1252, 1, 'Yes, enable caching.', 'admin_cache'),
      (1253, 1, 'No, do not enable caching.', 'admin_cache'),
      (1254, 1, 'What kind of caching do you want to enable by default?', 'admin_cache'),
      (1255, 1, 'If you have enabled caching above, please select the type of caching that you want to use. Memcache caching (if available) will use memory (RAM) which is not as abundant as disk space, however it will be faster than file-based caching when performing read/write operations.', 'admin_cache'),
      (1256, 1, 'Note to developers: If you are writing custom code, it is possible to override the type of caching used. If you choose not to do this, the system will use this default setting.', 'admin_cache'),
      (1257, 1, 'File-based', 'admin_cache, admin_session'),
      (1258, 1, 'Memcache', 'admin_cache, admin_session'),
      (1259, 1, 'Default cache lifetime.', 'admin_cache'),
      (1260, 1, 'This determines how long the system will keep cached data before reloading it from the database server. A shorter cache lifetime causes greater database server CPU usage, however the data will be more current. We recommend starting off with 60-120 seconds.', 'admin_cache'),
      (1261, 1, 'Note to developers: This will only affect places that do not specify a lifetime. To modify those, you will have to adjust the settings in that plugin or module, or change the code manually.', 'admin_cache'),
      (1262, 1, 'seconds', 'admin_cache, admin_session'),
      (1263, 1, 'File-based Cache Settings', 'admin_cache'),
      (1264, 1, 'The settings below are applicable if you have selected file-based caching above.', 'admin_cache'),
      (1265, 1, 'Successfully initialized. The cache folder exists and is writable.', 'admin_cache'),
      (1266, 1, 'The file caching was unable to initialize. The folder might not be writable - please set it to chmod 777.', 'admin_cache'),
      (1267, 1, 'Temporary directory location.', 'admin_cache'),
      (1268, 1, 'This is where the temporary files containing the cached data are stored. Folder must be writable (chmod 777). This should be a path relative to the base directory where SocialEngine is installed.', 'admin_cache'),
      (1269, 1, 'File locking.', 'admin_cache'),
      (1270, 1, 'This is used to prevent two Apache processes from trying to write to the same file at the same time. Some operating systems may not support file locking.', 'admin_cache'),
      (1271, 1, 'Enable file locking?', 'admin_cache'),
      (1272, 1, 'Memcache-based Cache Settings', 'admin_cache'),
      (1273, 1, 'The settings below are applicable if you have selected Memcache-based caching above. In this case, data is stored in memory using <a target=\"_blank\" href=\"http://www.danga.com/memcached/\">memcached</a> and its <a target=\"_blank\" href=\"http://www.php.net/memcache\">PHP extension</a>. You must set up a memcached server in order to use this option.', 'admin_cache'),
      (1274, 1, 'Successfully initialized. The Memcache extension was detected.', 'admin_cache'),
      (1275, 1, 'The Memcache extension was not detected or we were unable to connect to the memcached server.', 'admin_cache'),
      (1276, 1, 'Use compression?', 'admin_cache'),
      (1277, 1, 'Compression will decrease the amount of memory used, however will increase processor usage.', 'admin_cache'),
      (1278, 1, 'Enable compression', 'admin_cache'),
      (1279, 1, 'Memcached server configuration.', 'admin_cache'),
      (1280, 1, 'Click <a href=\"%1\$s\" onclick=\"%2\$s\">here</a> to add an additional server.', 'admin_cache, admin_session'),
      (1281, 1, 'Host', 'admin_cache, admin_session'),
      (1282, 1, 'Port', 'admin_cache, admin_session'),
      
      /* admin_language */
      (1283, 1, 'export', 'admin_language'),
      (1284, 1, 'Import Pack from File', 'admin_language'),
      
      /* admin_language_import */
      (1285, 1, 'Language Import Tool', 'admin_language_import'),
      (1286, 1, 'Here you can import a language pack from an exported file. If generated by SocialEngine, the file will contain all of the necessary info to create a new language pack.', 'admin_language_import'),
      (1287, 1, 'Updated:', 'admin_language_import'),
      (1288, 1, 'Inserted:', 'admin_language_import'),
      (1289, 1, 'Skipped:', 'admin_language_import'),
      (1290, 1, 'Failed:', 'admin_language_import'),
      (1291, 1, 'Import Options', 'admin_language_import'),
      (1292, 1, 'Please select an existing language or \"New Language\". If you select \"New Language\", the imported file will be used to create a new language pack.', 'admin_language_import'),
      (1293, 1, 'New Language', 'admin_language_import'),
      (1294, 1, 'You are about to replace an existing language pack with the one you are importing. Any new language phrases in the imported file will be added automatically. How do you want to handle changes to existing language phrases?', 'admin_language_import'),
      (1295, 1, 'Replace all phrases with those in the imported file.', 'admin_language_import'),
      (1296, 1, 'Do not replace any existing phrases, just add new ones.', 'admin_language_import'),
      (1297, 1, 'Please select a language file to import.', 'admin_language_import'),
      (1298, 1, 'Import', 'admin_language_import'),
      
      /* admin_session */
      (1299, 1, 'SocialEngine uses sessions to authenticate users and keep them logged-in. The settings below only need to be changed if your users are having trouble logging in (e.g. if your server does not allow native sessions) or if you want to improve system performance by enabling Memcache sessions. If you are not familiar with sessions, we do not recommend that you change any of these settings.', 'admin_session'),
      (1300, 1, 'General Session Settings', 'admin_session'),
      (1301, 1, 'The native method uses the current setting of <a href=\"http://www.php.net/manual/en/session.configuration.php\">session.save_handler</a>, in php.ini, which is file-based by default. <b>Note: If you change the session storage method, all of your users will be logged out.</b>', 'admin_session'),
      (1302, 1, 'Native', 'admin_session'),
      (1303, 1, 'How many seconds of inactivity is allowed before the session expires? An expired session will cause the user to be logged out and may invalidate forms that were partially filled out. This cannot be disabled, instead set it to a high value such as 259200 (3 days).', 'admin_session'),
      (1304, 1, 'File Session Settings', 'admin_session'),
      (1305, 1, 'If you have enabled file-based sessions above, please provide the path (relative to your SocialEngine base directory) to where you want to store session data. This directory must be writable (chmod 777).', 'admin_session'),
      (1306, 1, 'Memcache Session Settings', 'admin_session'),
      (1307, 1, 'Memcached supports multiple servers.', 'admin_session'),
      
      /* MISC */
      (1308, 1, 'Could not read file.', 'admin_language_import'),
      (1309, 1, 'A language code was not specified in the file.', 'admin_language_import'),
      (1310, 1, 'A language name was not specified in the file.', 'admin_language_import'),
      (1311, 1, 'Could not create new language', 'admin_language_import'),
      (1312, 1, 'Admin Interface Language', 'admin_home'),
      (1313, 1, 'If you have multiple language packs installed, you can change the language the admin interface is displayed in.', 'admin_home')
      
  ") or die("Insert: se_languagevars (10)<br>Error: ".mysql_error());
}






// ######### 3.1.2 #########


//######### MODIFY se_users
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `{$database_name}` LIKE 'user_password_method'")) )
  mysql_query("ALTER TABLE se_users ADD `user_password_method` tinyint(1) NOT NULL default 0 AFTER user_password");

mysql_query("ALTER TABLE se_users CHANGE `user_code` `user_code` varchar(255) NOT NULL default ''");


//######### MODIFY se_admins
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_admins FROM `{$database_name}` LIKE 'admin_enabled'")) )
  mysql_query("ALTER TABLE se_admins ADD `admin_enabled` tinyint(1) NOT NULL default 1 AFTER admin_email");
  
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_admins FROM `{$database_name}` LIKE 'admin_password_method'")) )
  mysql_query("ALTER TABLE se_admins ADD `admin_password_method` tinyint(1) NOT NULL default 0 AFTER admin_password");

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_admins FROM `{$database_name}` LIKE 'admin_code'")) )
{
  mysql_query("ALTER TABLE se_admins ADD `admin_code` varchar(16) NOT NULL default '' AFTER admin_password_method");
  mysql_query('UPDATE se_admins SET admin_code=\'admin123\' WHERE admin_code=\'\'');
}


//######### MODIFY se_settings
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_password_code_length'")) )
  mysql_query("ALTER TABLE se_settings ADD `setting_password_code_length` tinyint(2) NOT NULL default 16 AFTER setting_username");
  
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_password_method'")) )
  mysql_query("ALTER TABLE se_settings ADD `setting_password_method` tinyint(1) NOT NULL default 1 AFTER setting_username");



//######### INSERT DEFAULTS INTO se_languagevars
if( !mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1314 && languagevar_language_id=1 LIMIT 1")) )
{
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      (1314, 1, 'You have not yet deleted install.php and/or installsql.php. Leaving these files on your server is a security risk!', 'admin_home'),
      (1315, 1, 'You have not yet deleted upgrade.php and/or upgradesql.php. Leaving these files on your server is a security risk!', 'admin_home'),
      (1316, 1, 'More ...', 'header'),
      (1317, 1, 'More ...', 'profile'),
      (1318, 1, 'Some problems have been detected with your installation or server configuration.', 'admin_home'),
      (1319, 1, 'Click to expand.', 'admin_home'),
    (1320, 1, 'Your version file (%1\$s) does not contain the same version as your database (%2\$s). You may have not uploaded include/version.php or not run the upgrade script. In the latter case, database corruption may occur if using different file and database versions.', 'admin_home')
  ") or die("Insert: se_languagevars (11)<br>Error: ".mysql_error());
}






// ######### 3.1.4 #########


//######### MODIFY se_users
if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_users FROM `{$database_name}` LIKE 'user_hasnotifys'")) )
{
  mysql_query("ALTER TABLE se_users ADD `user_hasnotifys` tinyint(1) NOT NULL default '0'");
  mysql_query("UPDATE se_users SET user_hasnotifys=1 WHERE (SELECT TRUE FROM se_notifys WHERE notify_user_id=se_users.user_id LIMIT 1)");
}

//######### CREATE se_session_keys
/*
if( !mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_session_keys'")) )
{
  mysql_query("
    CREATE TABLE `se_session_keys` (
      `session_key_id`            varchar(32)             NOT NULL,
      `session_key_user_id`       int(9)        UNSIGNED  NOT NULL,
      `session_key_user_password` varchar(250)            NOT NULL,
      `session_key_user_email`    varchar(250)            NOT NULL,
      `session_key_count`         smallint(3)   UNSIGNED  NOT NULL,
      `session_key_ua`            text                    NOT NULL,
      `session_key_ip`            int(9)                  NOT NULL,
      `session_key_persist`       tinyint(1)              NOT NULL,
      `session_key_time`          int(9)                  NOT NULL,
      PRIMARY KEY  (`session_key_id`),
      KEY `CLEANUP`  (`session_key_time`)
    ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci
  ") or die("Create: se_session_keys<br>Error: ".mysql_error());
}
*/






// ######### 3.1.5 #########

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_stats_remote'")) )
  mysql_query("ALTER TABLE se_settings ADD `setting_stats_remote` tinyint(1) NOT NULL default 1");

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_stats_remote_last'")) )
  mysql_query("ALTER TABLE se_settings ADD `setting_stats_remote_last` int NOT NULL default 0");

if( !isset($remote_stats) ) $remote_stats = TRUE;
mysql_query("UPDATE se_settings SET setting_stats_remote='{$remote_stats}'");
mysql_query("UPDATE se_settings SET setting_stats_remote_last='0'");






// ######### 3.1.6 #########

/*
if( $previous_database_structure_version < 3.16 )
{
  mysql_query("TRUNCATE TABLE se_session_keys");

  // Regenerate the session name to prevent cookie problems from recent changes
  $session_options = @mysql_fetch_assoc(@mysql_query("SELECT setting_session_options FROM se_settings"));
  $session_options = @unserialize(@$session_options['setting_session_options']);
  if( !$session_options || !is_array($session_options) )
  {
    $session_options = addslashes(serialize(array('storage'=>'none','expire'=>259200,'name'=>md5(uniqid(rand(), true)),'servers'=>array(array('host'=>'localhost', 'port'=>11211)))));
  }

  else
  {
    $session_options['name'] = md5(uniqid(rand(), true));
    $session_options = addslashes(serialize($session_options));
  }
  mysql_query("UPDATE se_settings SET setting_session_options='{$session_options}'");
}
*/


//######### INSERT DEFAULTS INTO se_languagevars
if( !mysql_num_rows(mysql_query("SELECT NULL FROM se_languagevars WHERE languagevar_id=1321 && languagevar_language_id=1 LIMIT 1")) )
{
  mysql_query("
    INSERT INTO `se_languagevars`
      (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
    VALUES
      (1321, 1, 'Reply could not be sent because the recipient blocked you.', 'user_messages_view')
  ") or die("Insert: se_languagevars (12)<br>Error: ".mysql_error());
}






// ######### 3.1.7 #########

// Allow fail if not exists
mysql_query("DROP TABLE se_session_keys");

if( !mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_session_auth'")) )
{
  mysql_query("
    CREATE TABLE `se_session_auth` (
      `session_auth_key`      char(40)        NOT NULL,
      `session_auth_user_id`  int(9)          NOT NULL,
      `session_auth_ip`       int(9)          NOT NULL,
      `session_auth_ua`       char(32)        NOT NULL,
      `session_auth_type`     tinyint(1)      NOT NULL,
      `session_auth_time`     int(9)          NOT NULL,
      PRIMARY KEY  (`session_auth_key`),
      KEY `CLEANUP`  (`session_auth_time`)
    ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci
  ") or die("Create: se_session_auth<br />Error: ".mysql_error());
}





// ######### 3.1.8 #########

mysql_query("DROP TABLE IF EXISTS `se_visitors`");
mysql_query("
  CREATE TABLE `se_visitors` (
    `visitor_ip`                int(11)                 NOT NULL default 0,
    `visitor_browser`           char(32) character set ascii collate ascii_bin NOT NULL default '',
    `visitor_user_id`           int(10)       unsigned  NOT NULL default 0,
    `visitor_user_username`     varchar(64)                 NULL,
    `visitor_user_displayname`  varchar(128)                NULL,
    `visitor_lastactive`        int(14)                 NOT NULL default 0,
    `visitor_invisible`         tinyint(14)             NOT NULL default 0,
    UNIQUE KEY `UNIQUE` (`visitor_ip`, `visitor_browser`, `visitor_user_id`),
    KEY `LASTACTIVE` (`visitor_lastactive`)
  )
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci
") or die("Create: se_visitors(318)<br>Error: ".mysql_error());





// ######### 3.1.9 #########

if( !mysql_num_rows(mysql_query("SHOW TABLES FROM `$database_name` LIKE 'se_session_data'")) )
{
  mysql_query("
    CREATE TABLE IF NOT EXISTS `se_session_data` (
      `session_data_id` char(32) NOT NULL,
      `session_data_body` longtext NOT NULL,
      `session_data_expires` int(11) NOT NULL,
      PRIMARY KEY  (`session_data_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  ") or die("Create: se_session_data<br>Error: ".mysql_error());
}

if( !mysql_num_rows(mysql_query("SHOW COLUMNS FROM se_settings FROM `{$database_name}` LIKE 'setting_cache_xcache_options'")) )
  mysql_query("ALTER TABLE se_settings ADD `setting_cache_xcache_options` TEXT NULL");

mysql_query("ALTER TABLE `se_usersettings` CHANGE `usersetting_actions_dontpublish` `usersetting_actions_dontpublish` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
mysql_query("ALTER TABLE `se_usersettings` CHANGE `usersetting_actions_display`     `usersetting_actions_display`     TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");



// UPDATE version in database
mysql_query("UPDATE se_settings SET setting_version='{$current_database_structure_version}'");

?>