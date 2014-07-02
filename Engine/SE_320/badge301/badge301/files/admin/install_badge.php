<?php
$plugin_name = "Badge Plugin";
$plugin_version = "3.01";
$plugin_type = "badge";
$plugin_desc = "The plugin allows each user to be assigned badges which can be displayed as a series of iconic images. Users can optionally pay to have badges on their profile. Admin can also use this to build pages for special members, award pages for members with detailed review and recognition etc.. <a href = \"http://www.socengine.ru/add/plug/194-badge-special-members-znachki.html\">Перевод 1.0 SocEngine.Ru</a>";
$plugin_icon = "badge_badge16.gif";
$plugin_menu_title = "11270115";
$plugin_pages_main = "11270133<!>badge_badge16.gif<!>admin_badges.php<~!~>11270085<!>badge_badge16.gif<!>admin_badgeassignments.php<~!~>11270116<!>badge_badge16.gif<!>admin_badge.php<~!~>";
//$plugin_pages_main = "11270037<!>badge_badge16.gif<!>admin_viewplugins.php?install=badge<~!~>11270133<!>badge_badge16.gif<!>admin_badges.php<~!~>11270085<!>badge_badge16.gif<!>admin_badgeassignments.php<~!~>11270116<!>badge_badge16.gif<!>admin_badge.php<~!~>";
$plugin_pages_level = "11270045<!>admin_levels_badgesettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*/)?badge/([0-9]+)/([^/]*)\$ \$server_info/badge.php?badge_id=\$1\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*/)?badgeassignment/([0-9]+)/([^/]*)\$ \$server_info/badgeassignment.php?badgeassignment_id=\$1\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*/)?badgeusers/([a-zA-Z]+)/([0-9]+)/([^/]*)\$ \$server_info/badgeusers.php?type=\$2&type_id=\$3\$4 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;

if ($install == "badge")
{
  
  if (!class_exists('rc_toolkit')) {
    $message = '<p>You must install <b>Radcodes Core Library</b> prior to install this plugin. You can download it in <a href="http://www.radcodes.com/shop/">Radcodes Shop Customer Area</a> for FREE.</p>';  
    die($message);
  }    
  
 
  
  unset($_SESSION['RC_MODEL_CACHE']);
  
  //######### INSERT ROW INTO se_plugins
  $sql = "SELECT plugin_id FROM se_plugins WHERE plugin_type='$plugin_type'";
  $resource = $database->database_query($sql);
  if (! $database->database_num_rows($resource))
  {
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
        '" . str_replace("'", "\'", $plugin_desc) . "',
        '{$plugin_icon}',
        '{$plugin_menu_title}',
        '{$plugin_pages_main}',
        '{$plugin_pages_level}',
        '{$plugin_url_htaccess}'
      )
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  //######### UPDATE PLUGIN VERSION IN se_plugins
  else
  {
    $sql = "
      UPDATE
        se_plugins
      SET
        plugin_name='{$plugin_name}',
        plugin_version='{$plugin_version}',
        plugin_desc='" . str_replace("'", "\'", $plugin_desc) . "',
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
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  
  //######### CREATE se_badgecats
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_badgecats'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_badgecats`
      (
        `badgecat_id` int(10) unsigned NOT NULL auto_increment,
        `badgecat_dependency` int(10) unsigned NOT NULL default '0',
        `badgecat_title` int(10) unsigned NOT NULL default '0',
        `badgecat_order` smallint(5) unsigned NOT NULL default '0',
        `badgecat_signup` tinyint(3) unsigned NOT NULL default '0',
        PRIMARY KEY  (`badgecat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Insert default category
  $sql = "SELECT NULL FROM se_badgecats";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $badgecat_title  = SE_Language::edit(0, "Default", NULL, LANGUAGE_INDEX_FIELDS);
    $sql = "INSERT INTO se_badgecats (badgecat_title, badgecat_dependency, badgecat_order, badgecat_signup) VALUES ('$badgecat_title', 0, 1, 0)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }  
  
  //######### CREATE se_badges
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_badges'";
  $resource = $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  if (! $database->database_num_rows($resource))
  {
    $sql = "
      CREATE TABLE `se_badges`
      (
        `badge_id` int(9) unsigned NOT NULL auto_increment,
        `badge_badgecat_id` int(9) unsigned NOT NULL default '0',

        `badge_datecreated` int(14) NOT NULL default '0',
        `badge_dateupdated` int(14) NOT NULL default '0',
        `badge_title` varchar(255) default NULL,
        `badge_desc` text NULL,

        `badge_photo` varchar(255) default NULL,
        `badge_views` int(9) unsigned NOT NULL default '0',
        
        `badge_privacy` int(3) default NULL,
        `badge_comments` int(3) default NULL,
        
        `badge_level_ids`    varchar(255) default '0',
        `badge_subnet_ids`   varchar(255) default '0',
        `badge_profilecat_ids`   varchar(255) default '0',
        
        `badge_search` tinyint(1) unsigned default '1',
        `badge_link_details` tinyint(1) unsigned default '0',
        `badge_approved`  tinyint(1) unsigned default '1',
        `badge_cost` decimal(10,2) unsigned NOT NULL default '0.00',
        `badge_epayment` tinyint(1) unsigned NOT NULL default '0',        
        `badge_enabled`  tinyint(1) unsigned default '1',
        `badge_totalcomments` int(9) unsigned NOT NULL default '0',
        PRIMARY KEY  (`badge_id`),
        KEY `badge_views` (`badge_views`),
        FULLTEXT KEY `title_and_text` (`badge_title`,`badge_desc`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
    
  }
  
  //######### CREATE se_badgeassignments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_badgeassignments'";
  $resource = $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  if (! $database->database_num_rows($resource))
  {
    $sql = "
      CREATE TABLE `se_badgeassignments`
      (
        `badgeassignment_id` int(10) unsigned NOT NULL auto_increment,
        `badgeassignment_badge_id` int(10) unsigned NOT NULL,
        `badgeassignment_user_id` int(9) unsigned default NULL,
        `badgeassignment_datecreated`   INT           UNSIGNED  NOT NULL default 0,
        `badgeassignment_dateapproved`  INT           UNSIGNED  NOT NULL default 0,
        `badgeassignment_approved`  tinyint(1) unsigned NOT NULL default '0',
        `badgeassignment_epayment` tinyint(1) unsigned NOT NULL default '0',
        `badgeassignment_profile`  tinyint(1) unsigned NOT NULL default '1',        
        `badgeassignment_desc` text NULL,
        PRIMARY KEY  (`badgeassignment_id`),
        KEY `INDEX` (`badgeassignment_badge_id`, `badgeassignment_user_id`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }  
  
  
  //######### INSERT se_urls
  $sql = "SELECT url_id FROM se_urls WHERE url_file='badge'";
  $resource = $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  if (! $database->database_num_rows($resource))
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Badge URL', 'badge', 'badge.php?badge_id=\$id1', 'badge/\$id1/')
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  
  $sql = "SELECT url_id FROM se_urls WHERE url_file='badgeassignment'";
  $resource = $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  if (! $database->database_num_rows($resource))
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Badge Assignment URL', 'badgeassignment', 'badgeassignment.php?badgeassignment_id=\$id1', 'badgeassignment/\$id1/')
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  
  $sql = "SELECT url_id FROM se_urls WHERE url_file='badgeusers'";
  $resource = $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  if (! $database->database_num_rows($resource))
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Badge Users URL', 'badgeusers', 'badgeusers.php?type=\$user&type_id=\$id1', 'badgeusers/\$user/\$id1/')
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  if (! $database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newbadgeassignment'")))
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newbadgeassignment', 'badge_action_addbadge.gif', '1', '1', '11270178', '11270179', '[username],[displayname],[badgeid],[badgetitle]', '1')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  if (! $database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='badgecomment'")))
  {
    /* v3.01
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('badgecomment', 'action_postcomment.gif', '1', '1', '11270180', '11270181', '[username],[displayname],,,[comment],[id],[title]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
    */
  }
  else {
    $database->database_query("DELETE FROM se_actiontypes WHERE actiontype_name='badgecomment'");
  }
  
  $actiontypes = array_filter($actiontypes);
  if (! empty($actiontypes))
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '" . implode(",", $actiontypes) . "')");
  }

  //######### INSERT se_notifytypes
  if (! $database->database_num_rows($database->database_query("SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='badgecomment'")))
  {
    /* v3.01
    $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('badgecomment', '11270182', 'action_postcomment.gif', 'badge.php?badge_id=%2\$s', '11270183')
    ");
    */
  }
  else {
    $database->database_query("DELETE FROM se_notifytypes WHERE notifytype_name='badgecomment'");
  }
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  if (! $database->database_num_rows($database->database_query("SELECT systememail_id FROM se_systememails WHERE systememail_name='badgecomment'")))
  {
    /* v3.01
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('badgecomment', '11270152', '11270153', '11270184', '11270185', '[displayname],[commenter],[link]')
    ");
    */
  }
  else {
    $database->database_query("DELETE FROM se_systememails WHERE systememail_name='badgecomment'");
  }
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE IF BADGE HAS NEVER BEEN INSTALLED
  if ($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_badge_allow'")) == 0)
  {
    $sql = "
      ALTER TABLE se_levels
      ADD COLUMN `level_badge_allow` tinyint(3) unsigned NOT NULL default '1',
      ADD COLUMN `level_badge_edit` tinyint(3) unsigned NOT NULL default '1',
      ADD COLUMN `level_badge_delete` tinyint(3) unsigned NOT NULL default '1',
      ADD COLUMN `level_badge_maxnum` int(10) unsigned NOT NULL default '3'
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
    
    $sql = "UPDATE se_levels SET level_badge_allow='3'";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE IF BADGE HAS NEVER BEEN INSTALLED
  if ($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_badge'")) == 0)
  {
    $sql = "
      ALTER TABLE se_settings
      ADD COLUMN `setting_badge_license` varchar(255) NOT NULL default 'XXXX-XXXX-XXXX-XXXX',
      ADD COLUMN `setting_permission_badge` tinyint(1) unsigned NOT NULL default '1',
      ADD COLUMN `setting_badge_width` int(5) unsigned NOT NULL default '120',
      ADD COLUMN `setting_badge_height` int(5) unsigned NOT NULL default '120',
      ADD COLUMN `setting_badge_profile_show` varchar(64) NOT NULL default '',
      ADD COLUMN `setting_badge_menu_badge_ids` varchar(255) NOT NULL default '',
      ADD COLUMN `setting_badge_levels` text NULL,
      ADD COLUMN `setting_badge_subnets` text NULL,
      ADD COLUMN `setting_badge_profilecats` text NULL,
      ADD COLUMN `setting_badge_exts` text collate utf8_unicode_ci
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
    $sql = "UPDATE se_settings SET setting_badge_exts='jpeg,jpg,gif,png', setting_badge_profile_show='side'";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }

  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  if ($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_badgecomment'")) == 0)
  {
    $sql = "ALTER TABLE se_usersettings ADD COLUMN `usersetting_notify_badgecomment` int(1) NOT NULL default '1'";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  

  /////////////////////////////////////////////////////////////////////////////////////////
  /*
  $lang_min_id = 11270001;
  $lang_max_id = 11270999;
  
  $sql = "DELETE FROM se_languagevars WHERE languagevar_id >= $lang_min_id AND languagevar_id <= $lang_max_id";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  */
  
  /////////////////////////////////////////////////////////////////////////////////////////  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=11270001 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  if (! $database->database_num_rows($resource))
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (11270001, 1, 'Please fill out the form completely to add new badge.', ''),
        (11270002, 1, 'This badge does not exist.', ''),
        (11270003, 1, 'User does not exist.', ''),
        (11270004, 1, 'Edit Badge', ''),
        (11270005, 1, 'Please provide a title and description for your badge, as well as specify privacy settings below.', ''),
        (11270006, 1, 'Member Recognition', ''),
        (11270007, 1, 'Public?', ''),
        (11270008, 1, 'This page display badges that associated with users either thru admin assignment or user self-chosen.', ''),
        (11270009, 1, '%1\$d Badge Assignments Found', ''),
        (11270010, 1, 'Description', ''),
        (11270011, 1, 'Permission', ''),
        (11270012, 1, 'Public, users can choose or purchase this badge.', ''),
        (11270013, 1, 'Private, only admin can have access to this badge.', ''),
        (11270014, 1, 'Approved', ''),
        (11270015, 1, 'Turn on auto-approve when payment is made (if any), or chosen by user (free).', ''),
        (11270016, 1, 'ePayment', ''),
        (11270017, 1, 'Payment is required to have this badge on their profile.', ''),
        (11270018, 1, 'Cost', ''),
        (11270019, 1, 'Current Photo', ''),
        (11270020, 1, 'Upload New Photo', ''),
        (11270021, 1, 'Badge', ''),
        (11270022, 1, 'FREE', ''),
        (11270023, 1, 'Transaction', ''),
        (11270024, 1, 'added on', ''),
        (11270025, 1, 'My Badges', ''),
        (11270026, 1, '%1\$s %2\$s', ''),
        (11270027, 1, 'Browse through exclusive members who have this badge on their profile.', ''),
        (11270028, 1, 'User', ''),
        (11270029, 1, 'Browse Badges', ''),
        (11270030, 1, 'approved on', ''),
        (11270031, 1, 'pending approval', ''),
        (11270032, 1, 'payment is required', ''),
        (11270033, 1, '<b>Note:</b> you will be able to make your payment on next checkout screen.', ''),
        (11270034, 1, 'Pay Now', ''),
        (11270035, 1, 'Add New Badge', ''),
        (11270036, 1, 'Below you find more details about this member for assigned badge.', ''),
        (11270037, 1, 'Install Badge', ''),
        (11270038, 1, 'Badge has been removed from your account.', ''),
        (11270039, 1, 'You have NOT paid for this badge. It will not be shown on your profile until payment is completed.', ''),
        (11270040, 1, 'Payment ID', ''),
        (11270041, 1, 'Status', ''),
        (11270042, 1, 'Amount', ''),
        (11270043, 1, 'Reference Number', ''),
        (11270044, 1, 'Date', ''),
        (11270045, 1, 'Badge Settings', ''),
        (11270046, 1, 'Badge Settings', ''),
        (11270047, 1, 'Allow Badges?', ''),
        (11270048, 1, 'You may choose what access users in this level have to badges.', ''),
        (11270049, 1, 'Users may only view badges.', ''),
        (11270050, 1, 'Users may not use badges.', ''),
        (11270051, 1, 'User Levels', ''),
        (11270052, 1, 'All user levels', ''),
        (11270053, 1, 'Only user levels selected below.', ''),
        (11270054, 1, 'Subnetworks', ''),
        (11270055, 1, 'All subnetworks', ''),
        (11270056, 1, 'Only subnetworks selected below', ''),
        (11270057, 1, 'Profile Types', ''),
        (11270058, 1, 'All profile types', ''),
        (11270059, 1, 'Only profile types selected below', ''),
        (11270060, 1, 'Maximum Allowed Badges', ''),
        (11270061, 1, 'Enter the maximum number of allowed badges. The field must contain an integer between 1 and 999. ', ''),
        (11270062, 1, 'allowed badges', ''),
        (11270063, 1, 'Yes', ''),
        (11270064, 1, 'No', ''),
        (11270065, 1, 'Save Changes', ''),
        (11270066, 1, 'If you have enabled badges, your users will have the option of choose badges for their profile. Use this page to configure your badge settings.', ''),
        (11270067, 1, 'Show badges on profile tab.', ''),
        (11270068, 1, 'Show badges on profile sidebar.', ''),
        (11270069, 1, 'I will integrate it myself.', ''),
        (11270070, 1, 'Badge has been added to your profile.', ''),
        (11270071, 1, 'Your request for adding new badge has been submitted for admin approval.', ''),
        (11270072, 1, 'Badge has been added and in pending status. Please proceed with making payment for it now.', ''),
        (11270073, 1, 'Since', ''),
        (11270074, 1, 'Show:', ''),
        (11270075, 1, 'Recently Added', ''),
        (11270076, 1, 'Edit Badge Assignment', ''),
        (11270077, 1, 'To create a new badge, please select a category and enter title for this new badge, then hit submit. You will be able to fill out more options and details on next form.', ''),
        (11270078, 1, 'Integration Settings', ''),
        (11270079, 1, 'How would you like to integrate badges on user profile page?', ''),
        (11270080, 1, 'Badge ID', ''),
        (11270081, 1, 'You can add comments, notes, high-light, and other details for this badge assignment by filling out form below.', ''),
        (11270082, 1, 'Would you like to proceed with adding this badge?', ''),
        (11270083, 1, 'Username', ''),
        (11270084, 1, 'Assign', ''),
        (11270085, 1, 'Badge Assignments', ''),
        (11270086, 1, 'This page lists all of the badges that users have uploaded on your social network. You can use this page to monitor these badges and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific badges. Leaving the filter fields blank will show all the badges on your social network.', ''),
        (11270087, 1, 'Title', ''),
        (11270088, 1, 'Owner', ''),
        (11270089, 1, 'No badges were found.', ''),
        (11270090, 1, '%1\$d Badges Found', ''),
        (11270091, 1, '%1\$d member(s)', ''),
        (11270092, 1, 'Browse through users belong to <b>%1\$s</b> level.', ''),
        (11270093, 1, 'Browse through users belong to <b>%1\$s</b> subnetwork.', ''),
        (11270094, 1, 'Browse through users belong to <b>%1\$s</b> profile type.', ''),
        (11270095, 1, 'Other Levels', ''),
        (11270096, 1, 'Other Subnetworks', ''),
        (11270097, 1, 'Other Profile Types', ''),
        (11270098, 1, 'Badges', ''),
        (11270099, 1, 'Badges', ''),
        (11270100, 1, 'since %1\$s', ''),
        (11270101, 1, 'Date Added', ''),
        (11270102, 1, '%1\$s', ''),
        (11270103, 1, 'You currently have %1\$s badge(s) out of maximum %2\$d badge(s) per your account.', ''),
        (11270104, 1, 'Assign Badge to User', ''),
        (11270105, 1, 'No badge assignments were found.', ''),
        (11270106, 1, 'Add This Badge', ''),
        (11270107, 1, 'Edit Badge', ''),
        (11270108, 1, 'Delete Badge', ''),
        (11270109, 1, 'You do not have any badges.', ''),
        (11270110, 1, 'Browse badges for your profile now.', ''),
        (11270111, 1, 'Description', ''),
        (11270112, 1, 'Badge Assignment ID', ''),
        (11270113, 1, 'Display Setting', ''),
        (11270114, 1, 'Show this badge on my profile', ''),
        (11270115, 1, 'To assign a badge to a user, please enter the Badge ID and Username in fields below.', ''),
        (11270116, 1, 'Global Badge Settings', ''),
        (11270117, 1, '__NO BADGE__', ''),
        (11270118, 1, 'This page contains general badge settings that affect your entire social network. ', ''),
        (11270119, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\'admin_general.php\'>General Settings</a> page.', ''),
        (11270120, 1, 'Yes, the public can view badges unless they are made private.', ''),
        (11270121, 1, 'No, the public cannot view badges.', ''),
        (11270122, 1, 'Users may view and choose their own badges.', ''),
        (11270123, 1, 'Please select the badge that would be assigned to users belong to corresponding levels below', ''),
        (11270124, 1, 'Badge Photo Settings', ''),
        (11270125, 1, 'Badge Assignments', ''),
        (11270126, 1, '(comma separated list)', ''),
        (11270127, 1, 'Enter the file extensions that are connected to your badge photo upload.', ''),
        (11270128, 1, 'Please select the badge that would be assigned to users belong to corresponding subnetworks below', ''),
        (11270129, 1, 'Enter the maximum size for badge photo', ''),
        (11270130, 1, 'Please select the badge that would be assigned to users belong to corresponding profile types below', ''),
        (11270131, 1, 'Height', ''),
        (11270132, 1, 'Width', ''),
        (11270133, 1, 'Manage Badges', ''),
        (11270134, 1, 'This page allows to you manage various of badges which can be assigned to members.', ''),
        (11270135, 1, 'No badges has been found.', ''),
        (11270136, 1, 'Badge Categories', ''),
        (11270137, 1, 'Categorized badge make it easier for users to find badge that interest them. User will be required to select category that best match their badge type.', ''),
        (11270138, 1, 'Badge Assignment Settings', ''),
        (11270139, 1, 'Add Category', ''),
        (11270140, 1, 'Other Badges', ''),
        (11270141, 1, 'Can user delete assigned badge? This apply to all badges regardless of whether they add the badge manually or by admin.', ''),
        (11270142, 1, 'Yes, allow user to delete assigned badges.', ''),
        (11270143, 1, 'No, do not allow user to delete assigned badges.', ''),
        (11270144, 1, 'Most Popular', ''),
        (11270145, 1, 'Delete Badge?', ''),
        (11270146, 1, 'Are you sure you want to delete this badge and all of its associated records?', ''),
        (11270147, 1, 'Link Option', ''),
        (11270148, 1, 'Link to stand-alone <b>badge assignment page</b> (Member Recognition) with review written by admin for each user.', ''),
        (11270149, 1, 'Link to <b>badge page</b> with other users who have the same badge.', ''),
        (11270150, 1, 'Can user edit assigned badges?', ''),
        (11270151, 1, 'Yes, allow user to edit assigned badges.', ''),
        (11270152, 1, 'No, do not allow user to edit assigned badges.', ''),
        (11270153, 1, 'Your badge has been saved.', ''),
        (11270154, 1, 'Visit %1\$s\'s Profile', ''),
        (11270155, 1, 'View Other Members', ''),
        (11270156, 1, 'No badges were found matching your criteria.', ''),
        
        (11270157, 1, '%1\$s badge', ''),
        (11270158, 1, 'You are about to add the following badge to your profile:', ''),
        (11270159, 1, 'All Badges', ''),
        (11270160, 1, 'Browse Badges &gt; %1\$s', ''),
        (11270161, 1, '<b>Note:</b> Payment is required for adding this badge. You will be able to make your payment after submit this form.', ''),
        (11270162, 1, 'This badge has not been approved by admin yet.', ''),
        (11270163, 1, 'Please enter Badge ID(s) for menu items that would be shown under \"Special Members\" (lang var #11270165) drop-down on TOP-MENU header. If you use this feature, do not forget to embed the special code in header.tpl template as instructed in installation file', ''),
        (11270164, 1, '(commas seperated; example: 2,3,5,6)', ''),
        (11270165, 1, 'Special Members', ''),
        (11270166, 1, 'As an admin, you can write review, high-light, comments etc.. for member of badge below. The text would be shown on the badge assignment page (Member Recognition). This is totally optional, and only useful if the badge link option set to stand-alone badge assignment page.', ''),
        (11270167, 1, 'View Member Recognition Page', ''),
        (11270168, 1, 'view', ''),
        (11270169, 1, 'Members', ''),
        (11270170, 1, 'User already has this badge.', ''),
        (11270171, 1, 'Badge: %1\$s', ''),
        (11270172, 1, '%1\$d badges', ''),
        (11270173, 1, 'Category', ''),
        (11270174, 1, 'Sort', ''),
        
        (11270175, 1, 'Badges: %1\$d badges', ''),
        (11270176, 1, '%1\$s', ''),
        (11270177, 1, 'Latest Badge\'s Members', ''),
        
        (11270178, 1, 'Added New Badge', 'actiontypes'),
        (11270179, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> added badge <a href=\"badge.php?badge_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (11270180, 1, '&raquo; <a href=\"%1\$s\">%2\$s</a>', ''),
        (11270181, 1, 'on %1\$s', ''),
        (11270182, 1, 'No recent badge has been assigned to any members.', ''),
        
        (11270183, 1, 'Browse', ''),
        (11270184, 1, 'Include this badge on browse/search badges page.', ''),

        (11270185, 1, 'Visit %1\$d\'s Profile', '')
    ";
    $database->database_query($sql) or die($database->database_error() . " SQL: " . $sql);
  }
  
### LANGUAGE PLACEHOLDER ###
  

}
