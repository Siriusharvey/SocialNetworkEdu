<?php

/* $Id: install_music.php 245 2009-11-14 03:25:55Z phil $ */

$plugin_name = "Music Plugin";
$plugin_version = "3.06";
$plugin_type = "music";
$plugin_desc = "This plugin gives each of your users the ability to upload audio files, which can then be played via a Flash player on their profiles. This is a great way to increase profile customizability and personal expression.";
$plugin_icon = "music_music16.gif";
$plugin_menu_title = "4000001";
$plugin_pages_main = "4000002<!>music_music16.gif<!>admin_viewmusic.php<~!~>";
$plugin_pages_level = "4000003<!>admin_levels_musicsettings.php<~!~>";
$plugin_url_htaccess = "";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';



if( $install=="music" )
{
  // ######### INSERT ROW INTO se_plugins
  $sql = "SELECT plugin_id FROM `$database_name`.`se_plugins` WHERE plugin_type='$plugin_type'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO
        `$database_name`.`se_plugins`
      (
        plugin_name,
        plugin_version,
        plugin_type,
        plugin_desc,
        plugin_icon,
        plugin_menu_title,
        plugin_pages_main,
        plugin_pages_level,
        plugin_url_htaccess
      )
      VALUES
      (
        '$plugin_name',
        '$plugin_version',
        '$plugin_type',
        '$plugin_desc',
        '$plugin_icon',
        '$plugin_menu_title',
        '$plugin_pages_main',
        '$plugin_pages_level',
        '$plugin_url_htaccess'
      )
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }

  // ######### UPDATE PLUGIN VERSION IN se_plugins
  else
  {
    $sql = "
      UPDATE
        `$database_name`.`se_plugins`
      SET
        plugin_name='$plugin_name',
        plugin_version='$plugin_version',
        plugin_desc='$plugin_desc',
        plugin_icon='$plugin_icon',
        plugin_menu_title='$plugin_menu_title',
        plugin_pages_main='$plugin_pages_main',
        plugin_pages_level='$plugin_pages_level',
        plugin_url_htaccess='$plugin_url_htaccess'
      WHERE
        plugin_type='$plugin_type'
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_music
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_music'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_music`
      (
        `music_id`        INT         UNSIGNED  NOT NULL auto_increment,
        `music_user_id`   INT         UNSIGNED  NOT NULL default 0,
        `music_track_num` INT         UNSIGNED  NOT NULL default 0,
        `music_date`      INT                   NOT NULL default 0,
        `music_title`     VARCHAR(64)           NOT NULL default '',
        `music_ext`       VARCHAR(8)            NOT NULL default '',
        `music_filesize`  BIGINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`music_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on music_title
  $sql = "SHOW FULL COLUMNS FROM `se_music` LIKE 'music_title'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_music MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### RENAME se_music_skins->se_xspfskins
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_music_skins'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) )
  {
    $sql = "RENAME TABLE se_music_skins TO se_xspfskins";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "
      ALTER TABLE `se_xspfskins`
      CHANGE `se_music_skins_id`      `xspfskin_id`       INT          UNSIGNED  NOT NULL auto_increment,
      CHANGE `se_music_skins_title`   `xspfskin_title`    VARCHAR(255)           NOT NULL default '',
      CHANGE `se_music_skins_height`  `xspfskin_height`   SMALLINT     UNSIGNED  NOT NULL default 0,
      CHANGE `se_music_skins_width`   `xspfskin_width`    SMALLINT     UNSIGNED  NOT NULL default 0,
      CHANGE `se_music_skins_version` `xspfskin_version`  VARCHAR(255)           NOT NULL default ''
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "
      ALTER TABLE `se_xspfskins`
      ADD COLUMN `xspfskin_desc` TEXT NULL
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_xspfskins
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_xspfskins'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_xspfskins`
      (
        `xspfskin_id`       INT          UNSIGNED  NOT NULL auto_increment,
        `xspfskin_title`    VARCHAR(255)           NOT NULL default '',
        `xspfskin_desc`     TEXT                       NULL,
        `xspfskin_height`   SMALLINT     UNSIGNED  NOT NULL default 0,
        `xspfskin_width`    SMALLINT     UNSIGNED  NOT NULL default 0,
        `xspfskin_version`  VARCHAR(255)           NOT NULL default '',
        PRIMARY KEY  (`xspfskin_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "
      INSERT INTO `se_xspfskins`
        (`xspfskin_id`, `xspfskin_title`, `xspfskin_height`, `xspfskin_width`, `xspfskin_version`)
      VALUES
        (1, 'Default', '50', '200', '2.00'),
        (2, 'Cassette', '200', '304', '2'),
        (3, 'BasicPlaylist', '170', '200', '2.00')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on xspfskin_title
  $sql = "SHOW FULL COLUMNS FROM `se_xspfskins` LIKE 'xspfskin_title'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_xspfskins MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newmusic'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newmusic', 'music_action_newmusic.gif', '1', '1', '4000108', '4000109', '[username],[displayname]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE 
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_music_allow'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
  	$sql = "
      ALTER TABLE se_levels 
  	  ADD COLUMN `level_music_allow`        TINYINT   UNSIGNED  NOT NULL default '1',
      ADD COLUMN `level_music_maxnum`       SMALLINT  UNSIGNED  NOT NULL default '5',
      ADD COLUMN `level_music_exts`         TEXT                    NULL,
      ADD COLUMN `level_music_mimes`        TEXT                    NULL,
      ADD COLUMN `level_music_storage`      BIGINT    UNSIGNED  NOT NULL default '104857600',
      ADD COLUMN `level_music_maxsize`      BIGINT    UNSIGNED  NOT NULL default '15728640',
      ADD COLUMN `level_music_allow_skins`  TINYINT   UNSIGNED  NOT NULL default '1',
      ADD COLUMN `level_xpfskin_default`    INT       UNSIGNED  NOT NULL default '3'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
  	$sql = "
      UPDATE
        se_levels
      SET
        level_music_exts='mp3,mp4',
        level_music_mimes='audio/mpeg,audio/x-wav,application/octet-stream,audio/x-mp3,audio/mpeg3,audio/x-mpeg-3,video/mpeg,video/x-mpeg,audio/x-mpeg,audio/mp3,audio/x-mpeg3,audio/mpg,audio/x-mpg,audio/x-mpegaudio,content/unknown'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_music_allow_downloads'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
  	$sql = "
      ALTER TABLE se_levels 
  	  ADD COLUMN `level_music_allow_downloads`        TINYINT   UNSIGNED  NOT NULL default 0
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### MODIFY COLUMNS/VALUES FOR LEVELS TABLE 
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_music_skin_default'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE `se_levels`
      CHANGE `level_music_skin_default` `level_xpfskin_default` INT UNSIGNED NOT NULL default '3'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_music_profile_autoplay'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_usersettings 
      ADD COLUMN `usersetting_music_profile_autoplay` TINYINT UNSIGNED NOT NULL default '1',
      ADD COLUMN `usersetting_music_site_autoplay`    TINYINT UNSIGNED NOT NULL default '1',
  		ADD COLUMN `usersetting_xspfskin_id`            INT     UNSIGNED NOT NULL default '1'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### MODIFY COLUMNS/VALUES FOR LEVELS TABLE 
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_music_skin_id'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE `se_usersettings`
      CHANGE `usersetting_music_skin_id` `usersetting_xspfskin_id` INT     UNSIGNED NOT NULL default '1'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=4000001 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (4000001, 1, 'Music Settings', 'admin_header, admin_levels_musicsettings, user_music'),
        (4000002, 1, 'View Songs', ''),
        (4000003, 1, 'Music Settings', ''),
        (4000004, 1, 'Music', '')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=4000005 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* admin_levels_musicsettings */
        (4000005, 1, 'If you have allowed users to add music to their profiles, you can adjust the details from this page.', 'admin_levels_musicsettings'),
        (4000006, 1, 'Allow Music?', 'admin_levels_musicsettings'),
        (4000007, 1, 'Do you want to allow users to upload music to their profile?', 'admin_levels_musicsettings'),
        (4000008, 1, 'Yes, allow music', 'admin_levels_musicsettings'),
        (4000009, 1, 'No, do not allow music', 'admin_levels_musicsettings'),
        (4000010, 1, 'Maximum Allowed Songs', 'admin_levels_musicsettings'),
        (4000011, 1, 'Enter the maximum number of allowed songs. The field must contain an integer between 1 and 999.', 'admin_levels_musicsettings'),
        (4000012, 1, 'allowed songs', 'admin_levels_musicsettings'),
        (4000013, 1, 'Allowed Filetypes', 'admin_levels_musicsettings'),
        (4000014, 1, 'List the following file extensions that users are allowed to upload. Separate file extensions with commas, for example: mp3, mp4', 'admin_levels_musicsettings'),
        (4000015, 1, 'Allowed MIME Types', 'admin_levels_musicsettings'),
        (4000016, 1, 'To successfully upload a file, the file must have an allowed filetype extension as well as an allowed MIME type. This prevents users from disguising malicious files with a fake extension. Separate MIME types with commas, for example: image/jpeg, image/gif, image/png, image/bmp', 'admin_levels_musicsettings'),
        (4000017, 1, 'Allowed Storage Space', 'admin_levels_musicsettings'),
        (4000018, 1, 'How much storage space should each user have to store their files?', 'admin_levels_musicsettings'),
        (4000019, 1, 'Maximum Filesize', 'admin_levels_musicsettings'),
        (4000020, 1, 'Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 204800.', 'admin_levels_musicsettings'),
        (4000021, 1, 'You are currently editing this user level\'s settings. Remember, these settings only apply to the users that belong to this user level. When you\'re finished, you can edit the <a href=\'admin_levels.php\'>other levels here</a>.', 'admin_levels_musicsettings'),
        (4000022, 1, 'The maximum filesize field must contain an integer between 1 and 204800.', 'admin_levels_musicsettings'),
        (4000023, 1, 'Your maximum allowed songs field must contain an integer between 1 and 999.', 'admin_levels_musicsettings'),
        (4000024, 1, 'Allow skins?', 'admin_levels_musicsettings'),
        (4000025, 1, 'Should the users be able to select their own skins?', 'admin_levels_musicsettings'),
        (4000026, 1, 'Yes, allow skins', 'admin_levels_musicsettings'),
        (4000027, 1, 'No, do not allow skins', 'admin_levels_musicsettings'),
        (4000028, 1, 'Default Skin', 'admin_levels_musicsettings'),
        (4000029, 1, 'preview', 'admin_levels_musicsettings'),
        (4000030, 1, '%1\$s KB', 'admin_levels_musicsettings'),
        
        /* admin_viewmusic */
        (4000031, 1, 'View User Music', 'admin_viewmusic'),
        (4000032, 1, 'This page lists all of the music that your users have posted. You can use this page to monitor the music and delete them if necessary. Entering criteria into the filter fields will help you find specific music. Leaving the filter fields blank will show all the music on your social network.', 'admin_viewmusic'),
        (4000033, 1, 'Title', 'admin_viewmusic'),
        (4000034, 1, 'Owner', 'admin_viewmusic'),
        (4000035, 1, '%1\$d total tracks', 'admin_viewmusic'),
        (4000036, 1, 'Pages', 'admin_viewmusic'),
        (4000037, 1, 'There are no music tracks', 'admin_viewmusic'),
        (4000038, 1, 'Delete Song', 'admin_viewmusic'),
        (4000039, 1, 'Are you sure you want to delete this song?', 'admin_viewmusic, user_music_delete'),
        (4000040, 1, 'preview', 'admin_viewmusic'),
        
        /* profile_music */
        (4000041, 1, '%1\$s\'s Music', 'profile_music'),
        
        /* user_music */
        (4000042, 1, 'My Playlist', 'user_music'),
        (4000044, 1, 'These are the songs you\'ve uploaded to your playlist. People will be able to listen to them on your profile.', 'user_music'),
        (4000045, 1, 'Type in the new title for the track below, then press \"Update Song\" to make the changes.', 'user_music'),
        (4000046, 1, 'Song Title', 'user_music'),
        (4000047, 1, 'Upload Songs', 'user_music, user_music_upload'),
        (4000048, 1, 'Size', 'user_music'),
        (4000049, 1, '%1\$d MB', 'admin_levels_musicsettings, user_music'),
        (4000050, 1, '%1\$d GB', 'admin_levels_musicsettings, user_music'),
        (4000051, 1, 'Delete Selected Songs', 'user_music'),
        (4000052, 1, 'You have not yet uploaded any songs.', 'user_music'),
        
        /* user_music_delete */
        (4000053, 1, 'Delete Song?', 'user_music_delete'),
        
        /* user_music_settings */
        (4000054, 1, 'Edit Music Settings', 'user_music_settings'),
        (4000055, 1, 'Configure the music player on your profile with these settings.', 'user_music_settings'),
        (4000056, 1, 'Autoplay my music on my profile?', 'user_music_settings'),
        (4000057, 1, 'Yes, make my songs auto-play when people visit my profile.', 'user_music_settings'),
        (4000058, 1, 'No, visitors to my profile must click the play button to hear my music.', 'user_music_settings'),
        (4000059, 1, 'Autoplay other people\'s music on their profiles?', 'user_music_settings'),
        (4000060, 1, 'Yes, auto-play other people\'s music when I visit their profiles.', 'user_music_settings'),
        (4000061, 1, 'No, turn auto-play off when I visit other people\'s profiles.', 'user_music_settings'),
        (4000062, 1, 'Music Player Skin', 'user_music_settings'),
        
        /* user_music_upload */
        (4000063, 1, 'Music not enabled for this user', 'user_music_upload'),
        (4000064, 1, 'The file %1\$s has been uploaded successfully.', 'user_music_upload'),
        (4000065, 1, 'The file %1\$s was unable to be uploaded, please try again later.', 'user_music_upload'),
        (4000066, 1, 'There was an unknown error, please try again later.', 'user_music_upload'),
        (4000067, 1, 'Upload New Music', 'user_music_upload'),
        (4000068, 1, 'Browse for music files on your computer and upload them to your playlist.', 'user_music_upload'),
        (4000069, 1, 'Back to My Playlist', 'user_music_upload'),
        (4000070, 1, 'You may upload files with sizes up to %1\$s MB', 'user_music_upload'),
        (4000071, 1, 'You may upload files of the following types: %1\$s.', 'user_music_upload'),
        (4000072, 1, 'You have %1\$s MB of free space remaining.', 'user_music_upload'),
        (4000073, 1, 'Click \'Add a Song\' for each song you wish to upload, up to 5 songs. Then click \'Upload Songs\' to put them on your profile.', 'user_music_upload'),
        (4000074, 1, 'Select the song you wish to upload', 'user_music_upload'),
        (4000075, 1, 'Add A Song', 'user_music_upload'),
        (4000076, 1, 'Clear List', 'user_music_upload'),
        (4000077, 1, 'Clear List', 'user_music_upload'),
        (4000078, 1, 'Overall Progress', 'user_music_upload'),
        (4000079, 1, 'File Progress', 'user_music_upload'),
        (4000080, 1, 'Upload complete', 'user_music_upload'),
        
        (4000081, 1, 'Upload token was invalid. Please make sure you have cookie enabled for your browser and session support is enabled on the server', 'user_music_upload'),
        (4000082, 1, 'Please make sure you are logged in.', 'user_music_upload'),
        (4000083, 1, 'Music is not enabled for this user.', 'user_music_upload'),
        (4000084, 1, 'This page may not be accessed from a browser window.', 'user_music_upload'),
        (4000085, 1, 'The file %1\$s has been uploaded successfully.', 'user_music_upload'),
        
        /* MISC */
        (4000086, 1, 'Untitled Song', 'user_music'),
        (4000087, 1, 'The uploaded file(s) exceed your storage limit.', 'user_music_upload'),
        (4000088, 1, 'You have uploaded %1\$d songs.', 'user_music'),
        (4000089, 1, 'You have uploaded %1\$d songs and have %2\$d songs on your playlist.', 'user_music'),
        (4000090, 1, 'confirm', 'user_music'),
        (4000091, 1, 'Music Downloads', 'admin_levels_musicsettings'),
        (4000092, 1, 'Allow users to download songs? <b>This may be illegal in your area.</b>', 'admin_levels_musicsettings'),
        (4000093, 1, 'Yes, allow users to download uploaded songs.', 'admin_levels_musicsettings'),
        (4000094, 1, 'No, do not allow users to download uploaded songs.', 'admin_levels_musicsettings'),
        
        /* browse_music */
        (4000095, 1, 'Download Song', 'browse_music'),
        (4000096, 1, 'Browse Music', 'browse_music'),
        (4000097, 1, 'View:', 'browse_music'),
        (4000098, 1, 'Everyone\'s Music', 'browse_music'),
        (4000099, 1, 'My Friends\' Music', 'browse_music'),
        (4000100, 1, 'Order:', 'browse_music'),
        (4000101, 1, 'Recently Uploaded', 'browse_music'),
        (4000102, 1, 'Highest Playlist Priority', 'browse_music'),
        (4000103, 1, 'Uploaded %1\$s by <a href=\"%2\$s\">%3\$s</a>', 'browse_music'),
        
        /* search */
        (4000104, 1, '%1\$d songs', 'search'),
        (4000105, 1, 'Song: %1\$s', 'search'),
        (4000106, 1, 'Song uploaded by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', 'search')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=4000107 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (4000107, 1, 'Music: %1\$d songs', 'home')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=4000108 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (4000108, 1, 'Adding a Song', 'actiontypes'),
        (4000109, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> added a new song.', 'actiontypes')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=4000110 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (4000110, 1, 'You have reached the max number of songs. Please delete a song before trying to upload another.', 'user_music_upload')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
}

?>