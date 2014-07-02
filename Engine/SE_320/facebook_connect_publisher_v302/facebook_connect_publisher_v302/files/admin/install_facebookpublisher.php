<?php
$plugin_name = "Facebook Connect / Publisher";
$plugin_version = "3.02";
$plugin_type = "facebookpublisher";
$plugin_desc = "This plugin allows publishing user stories to Facebook with links back to your site increasing traffic and user social participation.";
$plugin_icon = "openid_facebook.gif";
$plugin_menu_title = "100051141";
$plugin_pages_main = "100051141<!>openid_facebook.gif<!>admin_openidconnect_facebook.php<~!~>100051147<!>openid_facebook.gif<!>admin_openidconnect_facebook_stories.php<~!~>100051142<!>openid_facebook.gif<!>admin_openidconnect_viewusers.php<~!~>100051156<!>openid_facebook.gif<!>admin_openidconnect_facebook_help.php<~!~>";
$plugin_pages_level = "";
$plugin_url_htaccess = "";

if($install == "facebookpublisher") {

  //######### INSERT ROW INTO se_plugins
  $database->database_query("INSERT INTO se_plugins (

                  plugin_name,
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
                  '$plugin_url_htaccess')

                  ON DUPLICATE KEY UPDATE

                  plugin_version='$plugin_version',
                  plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
                  plugin_icon='$plugin_icon',
                  plugin_menu_title='$plugin_menu_title',
                  plugin_pages_main='$plugin_pages_main',
                  plugin_pages_level='$plugin_pages_level',
                  plugin_url_htaccess='$plugin_url_htaccess'

  ");


  //######### INSERT LANGUAGE VARS
  $database->database_query("INSERT IGNORE INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value, languagevar_default) VALUES (100051000, 1, 'OpenID Connect', ''),(100051001, 1, 'OpenID Connect Settings', ''),(100051002, 1, 'OpenID Connect Settings', ''),(100051003, 1, 'OpenID Connect Settings', ''),(100051004, 1, 'Your settings have been saved.', ''),(100051005, 1, 'Please enter Api Key and Secret that you have recieved - should be a string 32 characters long consisting of letters and numbers. <br> Please enter Relaying URL you have received.', ''),(100051006, 1, 'Signup Process', ''),(100051007, 1, 'Please choose how signup process will look like:<br><br>Regular signup - users will be redirected to regular signup page after filling some details.<br><br>Express Signup - users will benefit from fast and easy signup process, only minimum required fields. You can specify which profile fields will be required, the imported data fields will be assigned regardless of this selection.<br>', ''),(100051008, 1, 'Regular signup process', ''),(100051009, 1, 'Express signup', ''),(100051010, 1, 'Timezone:', ''),(100051011, 1, 'Don\'t ask, use default', ''),(100051012, 1, 'Ask', ''),(100051013, 1, 'Language:', ''),(100051014, 1, 'Don\'t ask, use default', ''),(100051015, 1, 'Ask', ''),(100051016, 1, 'Profile Category:', ''),(100051017, 1, 'Don\'t ask, use default', ''),(100051018, 1, 'Ask', ''),(100051019, 1, 'Default:', ''),(100051020, 1, 'Data Portability - Imported Fields Mapping', ''),(100051021, 1, 'You can map the user data fields imported from various social networks (like facebook/myspace) or openid providers (like google, yahoo) corresponding to data fields on your network. You can see which fields are available for each network on the following link: <a target=_blank href=\"http://www.socialenginemods.net/plugins/openid-connect/docs/fields.html\">http://www.socialenginemods.net/plugins/openid-connect/docs/fields.html</a>', ''),(100051022, 1, 'SocialEngine Field', ''),(100051023, 1, 'Imported Field', ''),(100051024, 1, 'Services', ''),(100051025, 1, 'Manage available OpenID services that will appear on the login / signup selection.', ''),(100051026, 1, 'Enabled?', ''),(100051027, 1, 'Service Name', ''),(100051028, 1, 'Login Page Hook', ''),(100051029, 1, 'Please select whether you will modify the login page (login.tpl) manually or would like to have it replaced automatically (experimental). If chosen \"Yes\", the login page template will be \"login_openidconnect.tpl\".', ''),(100051030, 1, 'Yes, Replace the login page, I have not modified it and don\'t want to modify it manually.', ''),(100051031, 1, 'No, Do not replace, I will modify it manually.', ''),(100051032, 1, 'Save', ''),(100051033, 1, 'Create Your Account', ''),(100051034, 1, 'Welcome to the social network! To create your account, please provide the following information.', ''),(100051035, 1, 'quick signup', ''),(100051036, 1, 'Account Information', ''),(100051037, 1, 'Email:', ''),(100051038, 1, 'You will receive your friends updates to this email.', ''),(100051039, 1, 'Signup', ''),(100051040, 1, 'Signup', ''),(100051041, 1, 'SIGN IN USING', ''),(100051042, 1, 'OR SIGN IN USING', ''),(100051043, 1, 'Invalid API Key.', ''),(100051044, 1, 'Invalid Secret.', ''),(100051045, 1, 'OR', ''),
  												(100051046, 1, 'Import Profile Data?', ''),
  												(100051047, 1, 'Or Signup Using', ''),
  												(100051048, 1, 'You are currently logged in as', ''),
  												(100051049, 1, 'Would you like to link your account to', ''),
  												(100051050, 1, 'Yes, link my account', ''),
  												(100051051, 1, 'In a matter of seconds you will be able to enjoy our site. This is an example of the quick signup welcome message which can be replaced by editing the language phrase #100051051 in your admin panel.', ''),
  												(100051052, 1, 'Cancel', ''),
  												(100051053, 1, 'Hello', ''),
  												(100051054, 1, 'or', ''),
  												(100051055, 1, 'View OpenID Connected Users', ''),
  												(100051056, 1, 'This page lists all of the users that exist on your social network and have signed up with or connected their accounts to OpenID networks like Facebook, MySpace, etc. For more information about a specific user, click on the \"edit\" link in its row. Click the \"login\" link to login as a specific user. Use the filter fields to find specific users based on your criteria. To view all users on your system, leave all the filter fields blank.', ''),
  												(100051057, 1, 'Network', ''),
  												(100051058, 1, 'Facebook Connect / Publisher Settings', ''),
  												(100051059, 1, 'General Facebook Connect / Publisher Settings', ''),
  												(100051060, 1, 'General Settings', ''),
  												(100051061, 1, 'Please enter Facebook Api Key and Secret that you have recieved after creating a Facebook application for your website - should be a string 32 characters long consisting of letters and numbers.', ''),
  												(100051062, 1, 'Public website name. This will be substituted in the facebook story feeds instead of the \"{*site-name*}\" variable.', ''),
  												(100051063, 1, 'Public Site Name', ''),
  												(100051064, 1, 'Autologin / SSO (Single Sign On) allow users of your website to automatically login to your site whenever they are also logged in to Facebook ( users that have connected their accounts to Facebook ). <br><br> According to Facebook internal study, about 80% of all Facebook users always stay logged in.', ''),
  												(100051065, 1, 'more', ''),
  												(100051066, 1, 'The logic is as follows:<br><br>If user is not logged to your site, is logged into facebook and has previously connected accounts a dialog will prompt the user to autologin and save this preference for next time.<br><br>Note: If user is logged into your site, is also logged into facebook but has linked his facebook account to another account on your network, nothing will be performed.', ''),
  												(100051067, 1, 'Would you like to enable autologin?', ''),
  												(100051068, 1, 'Yes, enable autologin', ''),
  												(100051069, 1, 'No, disable autologin', ''),
  												(100051070, 1, 'Logout link hook - According to Facebook TOS, users logging out of your network should also logout from Facebook. This feature will try to take over the Logout link located on the Top Bar. If you have a customized template you can disable this feature and add the link by yourself. Please consult your template designer. <br><br> Would you like to enable Logout link hook?', ''),
  												(100051071, 1, 'Yes, enable logout link hook', ''),
  												(100051072, 1, 'No, disable logout link hook', ''),
  												(100051073, 1, 'Invitation', ''),
  												(100051074, 1, 'Invitation Action text - This will be the message displayed to the inviting user encouraging him to invite his friends.', ''),
  												(100051075, 1, 'Invitation message', ''),
  												(100051076, 1, 'Available Variables: [displayname] - Inviting user name, [sitename] - Public site name, [signup-link] - Invitation signup link with referrer <br />', ''),
  												(100051077, 1, 'Feed Story Templates', ''),
  												(100051078, 1, 'Feed story templates are similar to SocialEngine \'Recent Activity Feed\' templates and specify what and how will be published in the facebook news stream.<strong>Note</strong>: You should only change the text / phrasing. Modifying variables can cause publishing errors.', ''),
  												(100051079, 1, 'For documentation on Facebook feed templates please see', ''),
  												(100051080, 1, 'Note: It is on your own responsibility to read and understand Facebook documentation, no support is provided if incorrectly modifying these template fails publishing to Facebook.', ''),
  												(100051081, 1, 'I agree and I know what I\'m doing, show me the templates', ''),
  												(100051082, 1, 'User Prompt - will be displayed in the publishing dialog', ''),
  												(100051083, 1, 'User Message - will be displayed as a suggestion in the publishing dialog', ''),
  												(100051084, 1, 'Feed Story Title', ''),
  												(100051085, 1, 'Available Variables', ''),
  												(100051086, 1, 'Feed Story Body', ''),
  												(100051087, 1, 'Action Link - Link', ''),
  												(100051088, 1, 'Action Link - Text', ''),
  												(100051089, 1, 'Template bundle id', ''),
  												(100051090, 1, 'Enabled', ''),
  												(100051091, 1, 'Story Type', ''),
  												(100051092, 1, 'Publish stories to Facebook?', ''),
  												(100051093, 1, 'You are currently not logged in to Facebook and can\'t publish stories to your Facebook friends. Would you like to Connect ?', ''),
  												(100051094, 1, 'Connect', ''),
  												(100051095, 1, 'Login via Facebook?', ''),
  												(100051096, 1, 'You are currently logged in to Facebook. Would you like to autologin to the network?', ''),
  												(100051097, 1, 'Autologin', ''),
  												(100051098, 1, 'Remember my choice', ''),
  												(100051099, 1, 'Would you like to publish this story to Facebook?', ''),
  												(100051100, 1, 'Publish', ''),
  												(100051101, 1, 'Not yet, I need to change a photo!', ''),
  												(100051102, 1, 'No, do not publish', ''),
  												(100051103, 1, 'Never ask me again', ''),
  												(100051104, 1, 'My Facebook', ''),
  												(100051105, 1, 'My Facebook Friends', ''),
  												(100051106, 1, 'Invite Friends', ''),
  												(100051107, 1, 'Settings', ''),
  												(100051108, 1, 'My Facebook', ''),
  												(100051109, 1, 'My Facebook', ''),
  												(100051110, 1, 'You are not connected to Facebook. Click on the button below to connect with your facebook friends, publish stories and see your friends\' updates.', ''),
  												(100051111, 1, 'Loading...', ''),
  												(100051112, 1, 'Please login to Facebook to see your friends.', ''),
  												(100051113, 1, 'You have', ''),
  												(100051114, 1, 'Facebook friend(s) that are already here', ''),
  												(100051115, 1, 'See all', ''),
  												(100051116, 1, 'No Facebook friends.', ''),
  												(100051117, 1, 'Invite some', ''),
  												(100051118, 1, 'Your Facebook friends that are still not here', ''),
  												(100051119, 1, 'Invite friends', ''),
  												(100051120, 1, 'Hooray! All your Facebook friends are here!', ''),
  												(100051121, 1, 'Click here', ''),
  												(100051122, 1, 'if you wish to disconnect your account from Facebook', ''),
  												(100051123, 1, 'My Facebook Friends', ''),
  												(100051124, 1, 'Here you can find your Facebook friends that are also members of this site', ''),
  												(100051125, 1, 'Invite My Facebook Friends', ''),
  												(100051126, 1, 'Invite your Facebook friends to connect with you.', ''),
  												(100051127, 1, 'Please login to Facebook to invite your friends.', ''),
  												(100051128, 1, 'My Facebook Settings', ''),
  												(100051129, 1, 'Setup your preferences', ''),
  												(100051130, 1, 'Publish Stories to Facebook', ''),
  												(100051131, 1, 'Which of the following stories would you like to be able to publish on Facebook?', ''),
  												(100051132, 1, 'Autologin', ''),
  												(100051133, 1, 'You can be automatically logged into our network if you are logged into Facebook. Would you like to be logged in automatically?', ''),
  												(100051134, 1, 'Always ask me', ''),
  												(100051135, 1, 'Yes, Log me in automatically', ''),
  												(100051136, 1, 'No, Never log me in automatically', ''),
  												(100051137, 1, 'My Facebook', ''),
  												(100051138, 1, 'Invite Facebook Friends', ''),
  												(100051139, 1, 'Connect', ''),
  												(100051140, 1, 'OpenID Users', ''),
  												(100051141, 1, 'Facebook Connect Settings', ''),
  												(100051142, 1, 'Facebook Users', ''),
  												(100051143, 1, 'Error', ''),
  												(100051144, 1, 'You have already linked your account to', ''),
  												(100051145, 1, 'Possible reason for this error', ''),
  												(100051146, 1, 'You are logged into %1\$s with another account. Please <a href=\"user_logout.php\">logout</a> of both networks and login again.', ''),
  												(100051147, 1, 'Facebook Feed Stories', ''),
  												(100051148, 1, 'Used maximum Facebook invitations?', ''),
  												(100051149, 1, 'Click here', ''),
  												(100051150, 1, 'to invite your friends using another method.', ''),
  												(100051151, 1, 'Facebook Publisher Feed Stories', ''),
  												(100051152, 1, 'Manage Facebook feed stories text and parameters. You can create new feed stories from SocialEngine Recent Activity Feed actions. Please see <a href=\"admin_openidconnect_facebook_help.php\">Help&FAQ page</a> for description on Feed Stories and various parameters.', ''),
  												(100051153, 1, 'Create New Feed Story', ''),
  												(100051154, 1, 'Story type', ''),
  												(100051155, 1, 'Add Story', ''),
  												(100051156, 1, 'Help&FAQ', '')
  												");



  $database->database_query("INSERT INTO se_plugins (

                  plugin_name,
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
                  '$plugin_url_htaccess')

                  ON DUPLICATE KEY UPDATE

                  plugin_version='$plugin_version',
                  plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
                  plugin_icon='$plugin_icon',
                  plugin_menu_title='$plugin_menu_title',
                  plugin_pages_main='$plugin_pages_main',
                  plugin_pages_level='$plugin_pages_level',
                  plugin_url_htaccess='$plugin_url_htaccess'

  ");


  //######### CREATE DATABASE STRUCTURE

  if(!function_exists('chain_sql')) {
    function chain_sql( $sql ) {
      global $database;

      $rows = explode( ';;;', $sql);
      foreach($rows as $row) {
        $row = trim($row);
        if(empty($row))
          continue;
        $database->database_query( $row );
      }

    }
  }

  chain_sql(
<<<EOC

CREATE TABLE IF NOT EXISTS `se_semods_openidfieldmap` (
  `openidfieldmap_id` int(10) unsigned NOT NULL auto_increment,
  `openidfieldmap_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `openidfieldmap_field_id` int(11) NOT NULL,
  `openidfieldmap_cat_id` int(11) NOT NULL,
  PRIMARY KEY  (`openidfieldmap_id`),
  UNIQUE KEY `openidfieldmap_name` (`openidfieldmap_name`,`openidfieldmap_field_id`)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;;;

INSERT IGNORE INTO `se_semods_openidfieldmap` (`openidfieldmap_name`, `openidfieldmap_field_id`, `openidfieldmap_cat_id`) VALUES ('first_name', 2, 1);;;
INSERT IGNORE INTO `se_semods_openidfieldmap` (`openidfieldmap_name`, `openidfieldmap_field_id`, `openidfieldmap_cat_id`) VALUES ('last_name', 3, 1);;;
INSERT IGNORE INTO `se_semods_openidfieldmap` (`openidfieldmap_name`, `openidfieldmap_field_id`, `openidfieldmap_cat_id`) VALUES ('birthday', 4, 1);;;
INSERT IGNORE INTO `se_semods_openidfieldmap` (`openidfieldmap_name`, `openidfieldmap_field_id`, `openidfieldmap_cat_id`) VALUES ('sex', 5, 1);;;


CREATE TABLE IF NOT EXISTS `se_semods_openidreqfields` (
  `openidreqfield_id` int(10) unsigned NOT NULL auto_increment,
  `openidreqfield_cat_id` int(11) NOT NULL,
  `openidreqfield_fields` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`openidreqfield_id`),
  UNIQUE KEY `openidreqfield_cat_id` (`openidreqfield_cat_id`)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;;;

INSERT IGNORE INTO `se_semods_openidreqfields` (`openidreqfield_cat_id`, `openidreqfield_fields`) VALUES (1, '2,3');;;
INSERT IGNORE INTO `se_semods_openidreqfields` (`openidreqfield_cat_id`, `openidreqfield_fields`) VALUES (2, '');;;


CREATE TABLE IF NOT EXISTS `se_semods_openidservices` (
  `openidservice_id` int(10) unsigned NOT NULL,
  `openidservice_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `openidservice_displayname` varchar(50) collate utf8_unicode_ci NOT NULL,
  `openidservice_enabled` tinyint(4) NOT NULL default '1',
  `openidservice_logo_mini` varchar(50) collate utf8_unicode_ci NOT NULL,
  `openidservice_logo_small` varchar(50) collate utf8_unicode_ci NOT NULL,
  `openidservice_logo_large` varchar(50) collate utf8_unicode_ci NOT NULL,
  `openidservice_import_profiledata` tinyint(4) NOT NULL default '1',
  `openidservice_showorder` tinyint(4) NOT NULL default '0'
) CHARSET=utf8 COLLATE=utf8_unicode_ci;;;

#upgrade
ALTER TABLE `se_semods_openidservices` ADD PRIMARY KEY ( `openidservice_id` );;;
ALTER TABLE `se_semods_openidservices` ADD UNIQUE (`openidservice_name` );;;

REPLACE INTO `se_semods_openidservices` (`openidservice_id`, `openidservice_name`, `openidservice_displayname`, `openidservice_enabled`, `openidservice_logo_mini`, `openidservice_logo_small`, `openidservice_logo_large`, `openidservice_import_profiledata`, `openidservice_showorder`) VALUES (1, 'facebook', 'Facebook', 1, 'logo_facebook_mini.gif', 'logo_facebook_small.gif', 'logo_facebook_large.gif', 1, 7);;;


CREATE TABLE IF NOT EXISTS `se_semods_usersopenid` (
  `openid_id` int(10) unsigned NOT NULL auto_increment,
  `openid_user_id` int(11) NOT NULL,
  `openid_user_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `openid_service_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`openid_id`)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;;;



CREATE TABLE IF NOT EXISTS `se_semods_openidfeedstories` (
  `feedstory_id` int(10) unsigned NOT NULL auto_increment,
  `feedstory_usermessage` varchar(255) collate utf8_unicode_ci NOT NULL,
  `feedstory_userprompt` varchar(255) collate utf8_unicode_ci NOT NULL,
  `feedstory_service_id` int(10) unsigned NOT NULL,
  `feedstory_type` varchar(50) collate utf8_unicode_ci NOT NULL,
  `feedstory_metadata` text collate utf8_unicode_ci NOT NULL,
  `feedstory_enabled` tinyint(4) NOT NULL default '1',
  `feedstory_pagecheck` varchar(50) collate utf8_unicode_ci NOT NULL,
  `feedstory_publishprompt` tinyint(4) NOT NULL default '0',
  `feedstory_compiler` varchar(50) collate utf8_unicode_ci NOT NULL,
  `feedstory_publishusing` varchar(20) collate utf8_unicode_ci NOT NULL,
  `feedstory_vars` varchar(255) collate utf8_unicode_ci NOT NULL,
  `feedstory_display` tinyint(4) NOT NULL default '1',
  `feedstory_display_user` tinyint(4) NOT NULL default '1',
  `feedstory_desc` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`feedstory_id`),
  UNIQUE KEY `feedstory_type` (`feedstory_type`),
  KEY `feedstory_service_id` (`feedstory_service_id`),
  KEY `feedstory_enabled` (`feedstory_enabled`)
) CHARSET=utf8 COLLATE=utf8_unicode_ci;;;


CREATE TABLE IF NOT EXISTS `se_semods_openidinvites` (
  `invite_id` int(9) NOT NULL auto_increment,
  `invite_user_id` int(9) NOT NULL default '0',
  `invite_date` int(14) NOT NULL default '0',
  `invite_user_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `invite_service_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`invite_id`),
  UNIQUE KEY `invite_user_id` (`invite_user_id`,`invite_user_key`,`invite_service_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;;;


# upgrade -> 3.04

ALTER TABLE `se_usersettings` ADD `usersetting_openidconnect_publishfeeds` TEXT NOT NULL ;;;
ALTER TABLE `se_usersettings` ADD `usersetting_openidconnect_publishfeeds_keys` TEXT NOT NULL ;;;
ALTER TABLE `se_usersettings` ADD `usersetting_openidconnect_autologin` tinyint(4) NOT NULL default '0' ;;;

ALTER TABLE `se_semods_usersopenid` ADD INDEX ( `openid_service_id` );;;
ALTER TABLE `se_semods_usersopenid` ADD INDEX ( `openid_user_id` );;;
ALTER TABLE `se_semods_usersopenid` ADD INDEX ( `openid_user_key` );;;





ALTER TABLE `se_semods_openidservices` ADD `openidservice_customlogo` TINYINT NOT NULL DEFAULT '0';;;

UPDATE se_semods_openidservices SET openidservice_customlogo = '1' WHERE openidservice_name = 'facebook' ;;;


# Stories

INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('Join my group!', '', 1, 'newgroup', 'a:5:{s:15:"feedstory_title";s:74:"{*actor*} created a new Group <a href="{*group-link*}">{*group-title*}</a>";s:14:"feedstory_body";s:14:"{*group-desc*}";s:19:"feedstory_link_link";s:14:"{*group-link*}";s:19:"feedstory_link_text";s:21:"Join {*group-title*}!";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_group_edit', 1, '', 'feed', '{*actor*},{*group-link*},{*group-title*},{*group-desc*},{*site-name*},{*site-link*}', 1, 1, 'Creating a Group');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'postclassified', 'a:5:{s:15:"feedstory_title";s:89:"{*actor*} created a new Classified <a href="{*classified-link*}">{*classified-title*}</a>";s:14:"feedstory_body";s:19:"{*classified-body*}";s:19:"feedstory_link_link";s:19:"{*classified-link*}";s:19:"feedstory_link_text";s:25:"View {*classified-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_classified_media', 1, '', 'feed', '{*actor*},{*classified-link*},{*classified-title*},{*classified-body*},{*site-name*},{*site-link*}', 1, 1, 'Posting a Classified Listing');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('Join me for my event', '', 1, 'newevent', 'a:5:{s:15:"feedstory_title";s:74:"{*actor*} created a new Event <a href="{*event-link*}">{*event-title*}</a>";s:14:"feedstory_body";s:32:"{*event-date*}<br>{*event-desc*}";s:19:"feedstory_link_link";s:14:"{*event-link*}";s:19:"feedstory_link_text";s:23:"RSVP to {*event-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_event_edit', 1, '', 'feed', '{*actor*},{*event-link*},{*event-title*},{*event-desc*},{*site-name*},{*site-link*}', 1, 1, 'Creating an Event');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'postblog', 'a:5:{s:15:"feedstory_title";s:66:"{*actor*} posted a new Blog <a href="{*blog-link*}">{*blog-title*}";s:14:"feedstory_body";s:13:"{*blog-body*}";s:19:"feedstory_link_link";s:13:"{*blog-link*}";s:19:"feedstory_link_text";s:19:"Read {*blog-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_blog', 0, '', 'feed', '{*actor*},{*blog-link*},{*blog-title*},{*blog-body*},{*site-name*},{*site-link*}', 1, 1, 'Posting a Blog Entry');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'newpoll', 'a:5:{s:15:"feedstory_title";s:71:"{*actor*} created a new Poll <a href="{*poll-link*}">{*poll-title*}</a>";s:14:"feedstory_body";s:13:"{*poll-desc*}";s:19:"feedstory_link_link";s:13:"{*poll-link*}";s:19:"feedstory_link_text";s:22:"Vote on {*poll-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_poll', 0, '', 'feed', '{*actor*},{*poll-link*},{*poll-title*},{*poll-desc*},{*site-name*},{*site-link*}', 1, 1, 'Creating a Poll');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'newalbum', 'a:5:{s:15:"feedstory_title";s:74:"{*actor*} created a new Album <a href="{*album-link*}">{*album-title*}</a>";s:14:"feedstory_body";s:14:"{*album-desc*}";s:19:"feedstory_link_link";s:14:"{*album-link*}";s:19:"feedstory_link_text";s:25:"Check out {*album-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_album_upload', 1, '', 'feed', '{*actor*},{*album-link*},{*album-title*},{*album-desc*},{*site-name*},{*site-link*}', 1, 1, 'Creating an Album');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'newyoutubevideo', 'a:5:{s:15:"feedstory_title";s:83:"{*actor*} uploaded a new Video <a href="{*uservideo-link*}">{*uservideo-title*}</a>";s:14:"feedstory_body";s:18:"{*uservideo-desc*}";s:19:"feedstory_link_link";s:18:"{*uservideo-link*}";s:19:"feedstory_link_text";s:25:"Watch {*uservideo-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, 'user_video', 0, '', 'feed', '{*actor*},{*uservideo-link*},{*uservideo-title*},{*uservideo-desc*},{*site-name*},{*site-link*}', 1, 1, 'Adding A YouTube Video');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'newmedia', 'a:5:{s:15:"feedstory_title";s:87:"{*actor*} uploaded new photos to the Album <a href="{*album-link*}">{*album-title*}</a>";s:14:"feedstory_body";s:14:"{*album-desc*}";s:19:"feedstory_link_link";s:14:"{*album-link*}";s:19:"feedstory_link_text";s:25:"Check out {*album-title*}";s:18:"template_bundle_id";s:1:"0";}', 1, '', 0, '', 'feed', '{*actor*},{*album-link*},{*album-title*},{*album-desc*},{*site-name*},{*site-link*}', 1, 1, 'Uploading Photos to an Album');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'signup', 'a:5:{s:15:"feedstory_title";s:75:"{*actor*} has just signed up to <a href="{*signup-link*}">{*site-name*}</a>";s:14:"feedstory_body";s:67:"<a href="{*signup-link*}">Join me</a> and make this place friendly!";s:19:"feedstory_link_link";s:15:"{*signup-link*}";s:19:"feedstory_link_text";s:24:"Join Me on {*site-name*}";s:18:"template_bundle_id";s:1:"0";}', 1, '', 0, '', 'feed', '{*actor*},{*signup-link*},{*site-name*},{*site-link*}', 1, 0, 'Signing up');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'editstatus', 'a:5:{s:15:"feedstory_title";s:12:"{*actor*} is";s:14:"feedstory_body";s:0:"";s:19:"feedstory_link_link";s:0:"";s:19:"feedstory_link_text";s:0:"";s:18:"template_bundle_id";s:1:"0";}', 1, '', 0, '', 'stream', '{*actor*},{*site-name*},{*site-link*}', 1, 1, 'Changing status.');;;
INSERT IGNORE INTO `se_semods_openidfeedstories` (`feedstory_usermessage`, `feedstory_userprompt`, `feedstory_service_id`, `feedstory_type`, `feedstory_metadata`, `feedstory_enabled`, `feedstory_pagecheck`, `feedstory_publishprompt`, `feedstory_compiler`, `feedstory_publishusing`, `feedstory_vars`, `feedstory_display`, `feedstory_display_user`, `feedstory_desc`) VALUES ('', '', 1, 'newmusic', 'a:5:{s:15:"feedstory_title";s:29:"{*actor*} uploaded a new song";s:14:"feedstory_body";s:0:"";s:19:"feedstory_link_link";s:29:"{*site-link*}browse_music.php";s:19:"feedstory_link_text";s:16:"Browse our music";s:18:"template_bundle_id";s:1:"0";}', 1, '', 0, '', 'feed', '{*actor*},{*site-name*},{*site-link*}', 1, 1, 'Adding a Song');;;



EOC
);




  /*** SHARED ELEMENTS ***/

  $openid_imported_fields = 'about_me,activities,body_type,birthday,books,children,current_location_city,current_location_zip,current_location_country,current_location_state,drinker,ethnicity,first_name,heroes,hometown_location_city,hometown_location_zip,hometown_location_country,hometown_location_state,interests,last_name,locale,looking_for,movies,music,name,political,profile_url,quotes,relationship_status,religion,sex,smoker,tv,twitter_followers_count,friends_count,twitter_favourites_count,twitter_statuses_count,website';

  //######### CREATE se_semods_settings
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_semods_settings'")) == 0) {

    $database->database_query("CREATE TABLE `se_semods_settings` (
	  `setting_openidconnect_api_key` varchar(32) NOT NULL default '',
	  `setting_openidconnect_secret` varchar(32) NOT NULL default '',
      `setting_openidconnect_signupmode` tinyint(4) NOT NULL default '0',
      `setting_openidconnect_default_profilecat` int(11) NOT NULL default '0',
      `setting_openidconnect_signupfields` text collate utf8_unicode_ci NOT NULL,
      `setting_openidconnect_rpurl` varchar(255) collate utf8_unicode_ci NOT NULL,
      `setting_openidconnect_replaceloginpage` tinyint(4) NOT NULL default '1',
      `setting_openidconnect_importedfields` text collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_facebook_api_key` varchar(32) collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_facebook_secret` varchar(32) collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_facebook_feed_actions` text collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_feed_public_site_name` varchar(255) collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_facebook_invitemessage` varchar(255) collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_facebook_inviteactiontext` varchar(255) collate utf8_unicode_ci NOT NULL,
	  `setting_openidconnect_hook_logout` tinyint(4) NOT NULL default '1',
	  `setting_openidconnect_autologin` tinyint(4) NOT NULL default '1'
      )");

    $database->database_query("INSERT INTO `se_semods_settings` (`setting_openidconnect_importedfields`,`setting_openidconnect_facebook_inviteactiontext`,`setting_openidconnect_facebook_invitemessage`,`setting_openidconnect_feed_public_site_name`) VALUES ('$openid_imported_fields','Invite your Facebook Friends','[displayname] is a member of [sitename] and would like to share that experience with you.  To register, simply click on the \'Register\' button below.<fb:req-choice url=\'[signup-link]\' label=\'Register\' />','Community')");

  } else {

  chain_sql(
<<<EOC

ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_api_key` varchar(32) NOT NULL default '';;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_secret` varchar(32) NOT NULL default '';;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_signupmode` tinyint(4) NOT NULL default '0';;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_default_profilecat` int(11) NOT NULL default '0';;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_signupfields` text collate utf8_unicode_ci NOT NULL;;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_rpurl` varchar(255) collate utf8_unicode_ci NOT NULL;;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_replaceloginpage` tinyint(4) NOT NULL default '1';;;
ALTER TABLE `se_semods_settings` ADD COLUMN `setting_openidconnect_importedfields` text collate utf8_unicode_ci NOT NULL;;;

ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_feed_public_site_name` varchar(255) collate utf8_unicode_ci NOT NULL;;;
ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_hook_logout` tinyint(4) NOT NULL default '1';;;
ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_autologin` tinyint(4) NOT NULL default '1';;;

ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_facebook_api_key` VARCHAR( 32 ) NOT NULL;;;
ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_facebook_secret` VARCHAR( 32 ) NOT NULL;;;
ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_facebook_feed_actions` TEXT NOT NULL ;;;
ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_facebook_invitemessage` varchar(255) collate utf8_unicode_ci NOT NULL;;;
ALTER TABLE `se_semods_settings` ADD `setting_openidconnect_facebook_inviteactiontext` varchar(255) collate utf8_unicode_ci NOT NULL;;;

UPDATE `se_semods_settings` SET `setting_openidconnect_importedfields` = '$openid_imported_fields';;;

UPDATE `se_semods_settings` SET `setting_openidconnect_facebook_inviteactiontext` = 'Invite your Facebook Friends';;;
UPDATE `se_semods_settings` SET `setting_openidconnect_facebook_invitemessage` = '[displayname] is a member of [sitename] and would like to share that experience with you.  To register, simply click on the \'Register\' button below.<fb:req-choice url=\'[signup-link]\' label=\'Register\' />';;;
UPDATE `se_semods_settings` SET `setting_openidconnect_feed_public_site_name` = 'Community';;;

EOC
);

  }





$smarty->clear_compiled_tpl('header_global.tpl');

header('Location: admin_openidconnect_facebook_help.php?show=1');
exit;


}


?>