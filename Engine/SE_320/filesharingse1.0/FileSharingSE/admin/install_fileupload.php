<?php

/* $Id: install_fileupload.php 30 2009-01-20 21:34:44Z john $ */

$plugin_name = "File Upload Plugin";
$plugin_version = "1.0";
$plugin_type = "fileupload";
$plugin_desc = "<a href='http://www.socengine.ru'>SocEngine.Ru</a> This plugin allows your users to upload and download the files. As the admin, you create the categories (like \"For Pdf\", \"Audio\", \"Videos\", etc.) and your users can post relevant listings. Your users will also be able to search for other listings via a \"browse marketplace\" area, and each users' listings will appear on their profile.";
$plugin_icon = "fileupload_fileupload16.gif";
$plugin_menu_title = "7800001";
$plugin_pages_main = "7800002<!>fileupload_fileupload16.gif<!>admin_viewfiles.php<~!~>7800003<!>fileupload_settings16.gif<!>admin_file.php<~!~>";
$plugin_pages_level = "7800004<!>admin_levels_filesettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/fileuploads/([0-9]+)/?$ \$server_info/user_file_uploads.php?user=\$1&upid=\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/fileuploads/([0-9]+)/([^/]+)?$ \$server_info/user_file_uploads.php?user=\$1&upid=\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/fileuploads/?$ \$server_info/user_file_uploads.php?user=\$1 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;




if($install == "fileupload")
{
  //######### GET CURRENT PLUGIN INFORMATION
  $sql = "SELECT * FROM se_plugins WHERE plugin_type='$plugin_type' LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  $plugin_info = array();
  if( $database->database_num_rows($resource) )
    $plugin_info = $database->database_fetch_assoc($resource);
  
  // Uncomment this line if you already upgraded to v3, but are having issues with everything being private
  //$plugin_info['plugin_version'] = '2.00';
  
  
  
  
  //######### INSERT ROW INTO se_plugins
  $sql = "SELECT plugin_id FROM se_plugins WHERE plugin_type='$plugin_type'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_plugins (
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
        '$plugin_url_htaccess'
      )
    ";
    
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  //######### UPDATE PLUGIN VERSION IN se_plugins
  else
  {
    $sql = "
      UPDATE
        se_plugins
      SET
        plugin_name='$plugin_name',
        plugin_version='$plugin_version',
        plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
        plugin_icon='$plugin_icon',
        plugin_menu_title='$plugin_menu_title',
        plugin_pages_main='$plugin_pages_main',
        plugin_pages_level='$plugin_pages_level',
        plugin_url_htaccess='$plugin_url_htaccess'
      WHERE
        plugin_type='$plugin_type'
    ";
    
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_fileuploadcats
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_fileuploadcats'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_fileuploadcats` (
        `fileuploadcat_id` int(11) NOT NULL auto_increment,
  	`fileuploadcat_name` varchar(255) NOT NULL,
 	 PRIMARY KEY  (`fileuploadcat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_fileupload
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_fileuploads'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_fileuploads` (
        `userupload_id` int(11) NOT NULL auto_increment,
	`userupload_userid` int(11) NOT NULL,
	`userupload_categoryid` int(11) NOT NULL,
	`userupload_title` varchar(100) collate utf8_unicode_ci NOT NULL,
	`userupload_description` text collate utf8_unicode_ci NOT NULL,
	`userupload_userfiles` varchar(255) collate utf8_unicode_ci NOT NULL,
	`userupload_filetype` varchar(255) collate utf8_unicode_ci NOT NULL,
	`userupload_filesize` varchar(200) collate utf8_unicode_ci NOT NULL,
	`userupload_userthumbs` varchar(255) collate utf8_unicode_ci NOT NULL,
	`userupload_time` datetime NOT NULL,
	`modified_at` datetime NOT NULL,
	`modify` tinyint(2) NOT NULL default '0',
	`userupload_search` tinyint(1) NOT NULL default '1',
	`userupload_privacy` tinyint(2) NOT NULL default '0',
	`fileuploads_comments` tinyint(2) NOT NULL default '0',
	`fileuploads_totalcomments` int(11) NOT NULL,
	PRIMARY KEY  (`userupload_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
 
   
  
  //######### CREATE se_fileuploadscomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_fileuploadscomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_fileuploadscomments` (
        `fileuploadscomment_id`              INT         UNSIGNED  NOT NULL auto_increment,
        `fileuploadscomment_fileupload_id`   INT         UNSIGNED  NOT NULL default 0,
        `fileuploadscomment_authoruser_id`   INT         UNSIGNED  NOT NULL default 0,
        `fileuploadscomment_date`            INT         UNSIGNED  NOT NULL default 0,
        `fileuploadscomment_body`            TEXT                      NULL,
        PRIMARY KEY  (`fileuploadscomment_id`),
        KEY `INDEX` (`fileuploadscomment_fileupload_id`,`fileuploadscomment_authoruser_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  //######### CREATE se_fileratings
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_fileratings'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_fileratings` (
       `id` int(11) NOT NULL auto_increment,
	`userupload_id` int(11) NOT NULL,
	`total_votes` int(11) NOT NULL,
	`total_value` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	PRIMARY KEY  (`id`)
	)
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_filedownloads
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_filedownloads'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_filedownloads` (
         `userfiledownload_id` int(11) NOT NULL auto_increment,
  	 `userupload_id` int(11) NOT NULL,
 	 `userfiledownload_time` datetime NOT NULL,
 	 `userfiledownload_count` int(11) NOT NULL,
 	 PRIMARY KEY  (`userfiledownload_id`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
 

//######### CREATE se_fileicons
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_fileicons'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
     CREATE TABLE IF NOT EXISTS `se_fileicons` (
  	`id` int(11) NOT NULL auto_increment,
  	`file_type` varchar(100) NOT NULL,
 	 `icon_name` varchar(100) NOT NULL,
  	`icon_alt` varchar(50) NOT NULL,
  	PRIMARY KEY  (`id`)
	)
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
 $sql="INSERT INTO `se_fileicons` (`id`, `file_type`, `icon_name`, `icon_alt`) VALUES
(1, 'application/pdf', 'pdf-icon.png', 'PDF'),
(2, 'application/zip', 'zip-icon.png', 'ZIP'),
(3, 'application/rar', 'rar-icon.png', 'RAR'),
(4, 'image/jpeg', 'pic-icon.png', 'Picture'),
(5, 'image/gif', 'pic-icon.png', 'Picture'),
(6, 'image/bmp', 'pic-icon.png', 'Picture'),
(7, 'video', 'video-icon.png', 'Video'),
(8, 'audio', 'audio-icon.png', 'Audio'),
(9, 'application/msword', 'doc-icon.png', 'Document'),
(10, 'text/xml', 'doc-icon.png', 'Document');";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
   
  
  //######### INSERT se_urls
  
  $sql = "SELECT url_id FROM se_urls WHERE url_file='fileuploads'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('File upload Listing URL', 'fileuploads', 'upload_desc.php?user=\$user&upid=\$id1', '\$user/fileuploads/\$id1/')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newfileupload'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newfileupload', 'action_newfileupload.png', '1', '1', '7800148', '7800149', '[user_id],[username],[displayname],[file_id],[file_title],[image_name],[image_alt_text]', '0')
    ");
    
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='editfile'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
	 ('editfile', 'action_editfile.gif', '1', '1', '7800150', '7800151', '[user_id],[username],[displayname],[file_id],[file_title],[image_name],[image_alt_text]', '0')
	 
    ");
    
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }
  
//######### ADD COLUMNS/VALUES TO LEVELS TABLE IF FILE UPLOAD HAS NEVER BEEN INSTALLED
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_file_upload_allow'")) == 0) {
    $database->database_query("ALTER TABLE se_levels 
					ADD COLUMN `level_file_upload_allow` int(1) NOT NULL default '1',
					ADD COLUMN `level_file_upload_maxnum` int(3) NOT NULL default '10',
					ADD COLUMN `level_file_upload_exts` text NOT NULL,
					ADD COLUMN `level_file_upload_mimes` text NOT NULL,
					ADD COLUMN `level_file_upload_storage` bigint(11) NOT NULL default '5242880',
					ADD COLUMN `level_file_upload_maxsize` bigint(11) NOT NULL default '2048000',
					ADD COLUMN `level_file_upload_width` varchar(4) NOT NULL default '500',
					ADD COLUMN `level_file_upload_height` varchar(4) NOT NULL default '500',
					ADD COLUMN `level_file_upload_style` int(1) NOT NULL default '1',
					ADD COLUMN `level_file_upload_search` int(1) NOT NULL default '1',
					ADD COLUMN `level_file_upload_privacy` varchar(100) NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}',
					ADD COLUMN `level_file_upload_comments` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
    $database->database_query("UPDATE se_levels SET level_file_upload_exts='jpg,gif,jpeg,png,bmp,mp3,mpeg,avi,mpa,mov,qt,swf', level_file_upload_mimes='image/jpeg,image/pjpeg,image/jpg,image/jpe,image/pjpg,image/x-jpeg,image/x-jpg,image/gif,image/x-gif,image/png,image/x-png,image/bmp,audio/mpeg,video/mpeg,video/x-msvideo,video/avi,video/quicktime,application/x-shockwave-flash'");
  }
  else
  {
    $columns = mysql_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_file_upload_privacy'");
    while($column_info = mysql_fetch_assoc($columns)) {
      $field_name = $column_info['Field'];
      $field_type = $column_info['Type'];
      $field_default = $column_info['Default'];
      if($field_type == "varchar(10)") {
        mysql_query("ALTER TABLE se_levels CHANGE level_file_upload_privacy level_file_upload_privacy varchar(100) NOT NULL default ''");
        mysql_query("UPDATE se_levels SET level_file_upload_privacy='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'");
        mysql_query("UPDATE se_fileuploads SET userupload_privacy='63' WHERE userupload_privacy='0'");
        mysql_query("UPDATE se_fileuploads SET userupload_privacy='31' WHERE userupload_privacy='1'");
        mysql_query("UPDATE se_fileuploads SET userupload_privacy='15' WHERE userupload_privacy='2'");
        mysql_query("UPDATE se_fileuploads SET userupload_privacy='7' WHERE userupload_privacy='3'");
        mysql_query("UPDATE se_fileuploads SET userupload_privacy='3' WHERE userupload_privacy='4'");
        mysql_query("UPDATE se_fileuploads SET userupload_privacy='1' WHERE userupload_privacy='5'");
      }
    }
    $columns = mysql_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_file_upload_comments'");
    while($column_info = mysql_fetch_assoc($columns)) {
      $field_name = $column_info['Field'];
      $field_type = $column_info['Type'];
      $field_default = $column_info['Default'];
      if($field_type == "varchar(10)") {
        mysql_query("ALTER TABLE se_levels CHANGE level_file_upload_comments level_file_upload_comments varchar(100) NOT NULL default ''");
        mysql_query("UPDATE se_levels SET level_file_upload_comments='a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
        mysql_query("UPDATE se_fileuploads SET fileuploads_comments='63' WHERE fileuploads_comments='0'");
        mysql_query("UPDATE se_fileuploads SET fileuploads_comments='31' WHERE fileuploads_comments='1'");
        mysql_query("UPDATE se_fileuploads SET fileuploads_comments='15' WHERE fileuploads_comments='2'");
        mysql_query("UPDATE se_fileuploads SET fileuploads_comments='7' WHERE fileuploads_comments='3'");
        mysql_query("UPDATE se_fileuploads SET fileuploads_comments='3' WHERE fileuploads_comments='4'");
        mysql_query("UPDATE se_fileuploads SET fileuploads_comments='1' WHERE fileuploads_comments='5'");
      }
    }
  }
    
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_file_upload_profile'")) == 0) {
    $database->database_query("ALTER TABLE se_levels 
					ADD COLUMN `level_file_upload_profile` SET('side', 'tab'),
					ADD COLUMN `level_file_upload_tag` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
    $database->database_query("UPDATE se_levels SET level_file_upload_profile='tab', level_file_upload_tag='a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
  }

   
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_fileuploads'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_settings ADD COLUMN `setting_permission_fileuploads` int(1) NOT NULL default '1'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='fileuploadscomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('fileuploadscomment', '7800005', '7800006', '7800154', '7800155', '[displayname],[commenter],[link]')
    ");
  }
  
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_fileuploadscomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_usersettings ADD COLUMN `usersetting_notify_fileuploadscomment` int(1) NOT NULL default '1'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=7800001 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (7800001, 1, 'Upload Settings', ''),
        (7800002, 1, 'View File Listings', ''),
        (7800003, 1, 'Global File Upload Settings', ''),
        (7800004, 1, 'File Upload Settings', ''),
        (7800005, 1, 'New File Upload Comment Email', ''),
        (7800006, 1, 'This is the email that gets sent to a user when a new comment is posted on one of their File Upload listings.', ''),
        (7800007, 1, 'File Uploads', '')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=7800008 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
      
        /* admin_viewfiles */
	(7800021, 1, ' Thumb Image should be less than 2MB', ''),
	(7800022, 1, 'File upload successfully', ''),
	(7800023, 1, 'File is blank or it\'s size should be less than 4MB', ''),
	(7800024, 1, 'Do you want to let users have files? If set to no, all other settings on this page will not apply.', ''),
        (7800025, 1, 'Yes, allow files.', ''),
        (7800026, 1, 'No, do not allow files.', ''),
	
	(7800027, 1, 'Your maximum filesize field must contain an integer between 1 and 4096.', ''),
 	(7800028, 1, 'Your maximum width and height fields must contain integers between 1 and 9999.', ''),
        (7800029, 1, 'Your maximum allowed albums field must contain an integer between 1 and 999.', ''),
	(7800030, 1, 'Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 4096.', ''),
	(7800031, 1, 'List the following file extensions that users are allowed to upload. Separate file extensions with commas, for example: jpg, gif, jpeg, png, bmp',''),
	(7800032, 1, 'allowed files', ''),
 	(7800033, 1, 'Enter the maximum number of allowed files. The field must contain an integer between 1 and 999.', ''),
         (7800034,1,'If you have allowed users to have file files, you can adjust their details from this page.',''),
	(7800035,1,'File Settings',''),
	(7800036,1,'Allowed Storage Space',''),	
	(7800037,1,'Maximum Filesize',''),
	(7800038, 1, 'Allowed MIME Types', ''),
	(7800039,1,'Maximum Allowed Files',''),
	(7800040, 1, 'Allowed File Types', ''),
	(7800041,1,'File Privacy Options',''),
	(7800042, 1, 'Allow Files?', ''),
	(7800043, 1, 'size:', 'upload_desc'),
	(7800044, 1, 'Category added successfully.', 'admin_viewfiles'),
	(7800045, 1, 'Category name already exist.', 'admin_viewfiles'),
	(7800046, 1, 'Please enter category name.', 'admin_viewfiles'),
	(7800047, 1, 'Some error occured while adding.', 'admin_viewfiles'),
	(7800048, 1, 'Category Delete successfully.', 'admin_viewfiles'),
        (7800049, 1, 'This page lists all of the file listings your users have posted. You can use this page to monitor these files and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific fileupload listings. Leaving the filter fields blank will show all the fileupload listings on your social network.', 'admin_viewfiles'),
        (7800050, 1, 'No listings were found.', 'admin_viewfiles'),
        (7800051, 1, '%1\$d Files Found', 'admin_viewfiles'),
        (7800052, 1, 'Title', 'admin_viewfileupload'),
        (7800053, 1, 'Owner', 'admin_viewfileupload'),
        (7800054, 1, 'view', 'admin_viewfiles, fileuploads'),
        (7800055, 1, 'Are you sure you want to delete this File ?', 'admin_viewfiles'),
        
        /* User File listing */
        (7800056, 1, '<a href=\"%2\$s\">%1\$s</a>\'s <a href=\"%3\$s\">File description</a>', 'user_desc '),
        (7800057, 1, 'You have %1\$d files', ''),
        (7800058, 1, 'Category:', 'userupload'),
        (7800059, 1, 'Back to %1\$s\'s Listings', 'user_desc'),
        
        /* fileuploads */
        (7800060, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s %3\$s</a>\'s FileListings', 'fileuploads'),
        (7800061, 1, '<b><a href=\"%2\$s\">%1\$s</a></b> has not posted any fileupload listings.', 'fileuploads'),
        (7800062, 1, 'Views: %1\$d views', 'fileuploads'),
        (7800063, 1, 'Comments: %1\$d comments', 'fileuploads'),
        
        /* profile_fileupload */
        (7800064, 1, 'Posted:', 'profile_fileupload'),
        
        /* browse_upload        Save in DB     */
        (7800065, 1, 'Upload New File', 'browse_upload'),
        (7800066, 1, 'Listing Settings', 'browse_upload'),
        (7800067, 1, 'Search My Listings', 'browse_upload'),
        (7800068, 1, 'My File UPload Listings', 'browse_upload'),
        (7800069, 1, 'File uploads has great way to search a particular file.', 'browse_upload'),
        (7800070, 1, 'No File Upload listings were found.', 'browse_upload'),
        (7800071, 1, 'You do not have any file upload listings. <a href=\"%1\$s\">Click here</a> to post one.', 'browse_upload'),
        (7800072, 1, '%1\$d views', 'browse_upload'),
        (7800073, 1, 'Download', 'browse_upload'),
        (7800074, 1, 'Rating', 'browse_upload'),
        (7800075, 1, 'Edit File', 'browse_upload'),
        (7800076, 1, 'Delete File', 'browse_upload'),
        
        /* admin_file */
        (7800077, 1, 'General File Upload Settings', 'admin_file'),
        (7800078, 1, 'This page contains general fileupload settings that affect your entire social network.', 'admin_file'),
        (7800079, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\"admin_general.php\">General Settings</a> page.', 'admin_file'),
        (7800080, 1, 'Yes, the public can view fileuploads unless they are made private.', 'admin_file'),
        (7800081, 1, 'No, the public cannot view uploaded files.', 'admin_file'),
        (7800082, 1, 'File Categories', 'admin_file'),
        (7800083, 1, 'You may want to allow your users to categorize their fileupload listings by subject, location, etc. Categorized fileupload listings make it easier for users to find and fileuploads that interest them. If you want to allow fileupload listing categories, you can create them (along with subcategories) below.<br /><br />Within each category, you can create fileupload fields. When a fileupload is created, the creator (owner) will describe the fileupload by filling in some fields with information about the fileupload. Add the fields you want to include below. Some examples of fileupload fields are \"Location\", \"Price\", \"Contact Email\", etc. Remember that a \"FileTitle\" and \"FileDescription\" field will always be available and required. Drag the icons next to the categories and fields to reorder them.', 'admin_file'),
        (7800084, 1, 'File Categories', 'admin_file'),
        
        /* user_upload_listing */
        (7800085, 1, 'Most Popular', 'user_upload_listing'),
        (7800086, 1, 'Top Download', 'user_upload_listing'),
        (7800087, 1, 'Write your new listing below, then click \"Post Listing\" to publish the listing on your files.', 'user_file_uploads'),
        (7800088, 1, 'Edit the details of this file listing below.', 'user_file_uploads'),
        (7800089, 1, 'File Title', 'user_uploads'),
        (7800090, 1, 'File Description', 'user_uploads'),
        (7800091, 1, 'File Category', 'user_uploads'),
        (7800092, 1, 'Include this file uploads in search/browse results?', 'browse_upload'),
        (7800093, 1, 'Yes, include this group in search/browse results.', 'browse_upload'),
        (7800094, 1, 'No, exclude this group from search/browse results.', 'browse_upload'),
        (7800095, 1, 'Who can see this file uploads?', 'browse_upload'),
        (7800096, 1, 'You can decide who gets to see this file upload.', 'browse_upload'),
        (7800097, 1, 'Allow Comments?', 'browse_upload'),
        (7800098, 1, 'You can decide who can post comments on this file upload.', 'browse_upload'),
        (7800099, 1, 'Post File upload', 'browse_upload'),
        (7800100, 1, 'Please enter a name for your File.', 'browse_upload'),
        (7800101, 1, 'Please select a category for this file.', 'browse_upload'),
        (7800102, 1, 'My Files', 'user_file_uploads'),
        (7800103, 1, 'Please do login to Download file', 'upload_desc'),

        /* userupload */
        
        (7800104, 1, 'Use this page to change the photos shown on this file listing.', 'userupload'),
        (7800105, 1, 'Your File listing has been posted! Do you want to add some photos?', 'userupload'),
        (7800106, 1, 'Add Photos Now', 'userupload'),
        (7800107, 1, 'Cancel', 'userupload'),
        (7800108, 1, 'Small Thumb', 'userupload'),
        (7800109, 1, 'Replace this photo with:', 'userupload'),
        (7800110, 1, 'delete File', 'userupload'),
        (7800111, 1, 'Deleting File...', 'userupload'),
        (7800112, 1, 'Add File', 'userupload'),
        (7800113, 1, 'Upload', 'userupload'),
        (7800114, 1, 'Thumbnail', 'userupload'),
        
        /* user_file_settings 
        (7800115, 1, 'Edit File Settings', 'user_file_settings'),
        (7800116, 1, 'Edit settings pertaining to your file listings.', 'user_file_settings'),
        (7800117, 1, 'Custom FileStyles', 'user_file_settings'),
        (7800118, 1, 'You can change the colors, fonts, and styles of your file listing by adding CSS code below. The contents of the text area below will be output between &lt;style&gt; tags on your file listing.', 'user_file_settings'),
        (7800119, 1, 'File Notifications', 'user_file_settings'),
        
	*/
        /* MISC */
	(7800120, 1, 'You have previously voted', 'star_rate'),
        (7800121, 1, 'Delete File ?', 'user_file_uploads'),
        (7800122, 1, 'Are you sure you want to delete this file from list?', 'user_file_uploads'),
        (7800123, 1, 'There was an error processing your request.', 'user_file_uploads'),
        
        /* browse_upload */
        (7800124, 1, 'Browse File Listings', 'browse_upload'),
        (7800125, 1, 'Back to File List', 'browse_upload'),
        (7800126, 1, 'Order:', 'browse_upload'),

        (7800127, 1, 'Top Ratings', 'browse_upload'),
        (7800128, 1, 'Recent Downloads', 'browse_upload'),
        (7800129, 1, 'Recently Created', 'browse_upload'),
        (7800130, 1, 'Recently Uploaded', 'browse_upload'),
        (7800131, 1, 'Most Viewed', 'browse_upload'),
        (7800132, 1, 'Most Commented', 'browse_upload'),
        (7800133, 1, 'All File Listings', 'browse_upload'),
        (7800134, 1, 'No Files were found matching your criteria.', 'browse_upload'),
        (7800135, 1, 'created %1\$s', 'browse_upload'),
        (7800136, 1, 'updated %1\$s', 'browse_upload'),
        
        /* search */
        (7800137, 1, 'File: %1\$s', 'search'),
        (7800138, 1, 'File uploaded by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', 'upload_desc'),
        (7800139, 1, '%1\$d files', 'search'),
        (7800143, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s %3\$s</a>\'s File', 'upload_desc'),
        (7800144, 1, '%1\$s\'s File listing - %2\$s', 'header_global'),
	(7800158, 1, 'No Uploads were found matching your criteria.', 'browse_upload'),
	(7800159, 1, '(Only JPEG and GIF images can be uploaded \r\n with size less than 2 MB.', 'userupload)'),
	(7800160, 1, '* fields are mandatory', 'userupload')	
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=7800145 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (7800145, 1, 'File Uploads: %1\$d Files', 'home'),
        (7800146, 1, 'File Comments: %1\$d comments', 'home'),
        (7800147, 1, 'Files: %1\$d files', 'home')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=7800148 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (7800148, 1, 'Posting a File Listing', 'actiontypes'),
        (7800149, 1, '<img src=\"./images/icons/%6\$s\" alt=\"%7\$s\">&nbsp;<a href=\"profile.php?user=%2\$s\">%3\$s</a> uploaded a new File : <a href=\"upload_desc.php?user=%1\$s&upid=%4\$s\">%5\$s</a>', 'actiontypes'),
        (7800150, 1, 'Update a File ', 'actiontypes'),
        (7800151, 1, '<img src=\"./images/icons/%6\$s\" alt=\"%7\$s\">&nbsp;<a href=\"profile.php?user=%2\$s\">%3\$s</a> updated a file <a href=\"upload_desc.php?user=%1\$s&upid=%4\$s\">%5\$s\</a>', 'actiontypes'),
        (7800152, 1, '%1\$d New File Comment(s): %2\$s', 'notifytypes'),
        (7800153, 1, 'When I receive a file comment.', 'notifytypes'),
        (7800154, 1, 'New file Listing Comment', 'systememails'),
        (7800155, 1, 'Hello %1\$s,\n\nA new comment has been posted on one of your file listings by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  ################ UPGRADE EXISTING CLASSIFIEDS' PRIVACY OPTIONS

if( !empty($plugin_info) && version_compare($plugin_info['plugin_version'], '1.00', '<') )
  {
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='63'  WHERE userupload_privacy='0' ") or die($database->database_error()." View Privacy Query #1");
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='31'  WHERE userupload_privacy='1' ") or die($database->database_error()." View Privacy Query #2");
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='15'  WHERE userupload_privacy='2' ") or die($database->database_error()." View Privacy Query #3");
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='7'   WHERE userupload_privacy='3' ") or die($database->database_error()." View Privacy Query #4");
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='3'   WHERE userupload_privacy='4' ") or die($database->database_error()." View Privacy Query #5");
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='1'   WHERE userupload_privacy='5' ") or die($database->database_error()." View Privacy Query #6");
    $database->database_query("UPDATE se_fileuploads SET userupload_privacy='0'   WHERE userupload_privacy='6' ") or die($database->database_error()." View Privacy Query #7");
    
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='63' WHERE fileuploads_comments='0'") or die($database->database_error()." Comment Privacy Query #1");
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='31' WHERE fileuploads_comments='1'") or die($database->database_error()." Comment Privacy Query #2");
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='15' WHERE fileuploads_comments='2'") or die($database->database_error()." Comment Privacy Query #3");
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='7'  WHERE fileuploads_comments='3'") or die($database->database_error()." Comment Privacy Query #4");
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='3'  WHERE fileuploads_comments='4'") or die($database->database_error()." Comment Privacy Query #5");
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='1'  WHERE fileuploads_comments='5'") or die($database->database_error()." Comment Privacy Query #6");
    $database->database_query("UPDATE se_fileuploads SET fileuploads_comments='0'  WHERE fileuploads_comments='6'") or die($database->database_error()." Comment Privacy Query #7");
  }

}

?>