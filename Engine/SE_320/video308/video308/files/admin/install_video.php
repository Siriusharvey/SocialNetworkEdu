<?php

/* $Id: install_video.php 236 2009-11-13 05:29:23Z phil $ */

$plugin_name = "Video Plugin";
$plugin_version = "3.08";
$plugin_type = "video";
$plugin_desc = "This plugin lets your users upload and share videos. Uploaded videos are encoded into Flash files on-the-fly and played via an embedded Flash video player. This is a great way to encourage content creation and personal expression on your social network.";
$plugin_icon = "video_video16.gif";
$plugin_menu_title = "5500115";
$plugin_pages_main = "5500159<!>video_video16.gif<!>admin_video_utilities.php<~!~>5500085<!>video_video16.gif<!>admin_viewvideos.php<~!~>5500116<!>video_settings16.gif<!>admin_video.php<~!~>";
$plugin_pages_level = "5500045<!>admin_levels_videosettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/videos/([0-9]+)/?$ \$server_info/video.php?user=\$1&video_id=\$2 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;

if($install == "video")
{
  $video_error = $_GET['error'];
  $video_ffmpeg_path = $_GET['ffmpeg_path'];
  $video_do_find = $_GET['do_find'];
  $video_do_find_root = $_GET['do_find_root'];
  
  $video_can_find = ( strtoupper(substr(PHP_OS, 0, 3))!=="WIN" );
  
  if( $video_can_find && $video_do_find )
  {
    $video_do_find_root = ( $video_do_find_root ? escapeshellcmd(strip_tags($video_do_find_root)) : '/' );
    
    $result = null;
    exec("which ffmpeg", $result);
    if (empty($result[0])) {
      exec("find {$video_do_find_root} -name 'ffmpeg'", $result);
    }
    echo json_encode($result);
    exit();
  }
  
  elseif( $video_do_find )
  {
    echo '[]';
    exit();
  }
  
	//######## CHECK IF FFMPEG IS INSTALLED
	if( !empty($setting['setting_video_ffmpeg_path']) && (!isset($video_ffmpeg_path)))
  {
    $video_ffmpeg_path = $setting['setting_video_ffmpeg_path'];
  }
  
  if( !empty($video_ffmpeg_path) && in_array('exec', explode(',', ini_get('disable_functions'))) )
  {
    // exec is disabled on this server
    $video_error = TRUE;
    $smarty->assign('exec_disabled', TRUE);
  }
  
  // Check ffmpeg installation
  if( !empty($video_ffmpeg_path) )
  {
    $video_ffmpeg_path = escapeshellcmd(strip_tags($video_ffmpeg_path));
    
    $result = null;
    exec($video_ffmpeg_path.' -version 2>&1', $result);
    
    if( empty($result) || !isset($result[0]) || !strstr($result[0], 'FFmpeg') )
    {
      $video_error = TRUE;
      $smarty->assign('_error_message', join("<br />\n", $result));
    }
  }
  
  // show form if error
  if($video_error || !isset($video_ffmpeg_path))
  {
		$smarty->assign('_error', $video_error);
		$smarty->assign('_ffmpeg_path', $video_ffmpeg_path);
		$smarty->assign('_can_find', $video_can_find);
		
		
		$page = "admin_install_video";
		include "admin_footer.php";
		exit();
	}
  

	
	
	// exec and ffmpeg are working... install now...

	//######### INSERT ROW INTO se_plugins
  $sql = "SELECT plugin_id FROM se_plugins WHERE plugin_type='$plugin_type'";
  $resource = $database->database_query($sql);
  
	if( !$database->database_num_rows($resource) )
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

else
  {
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
  
  
  
	//######### CREATE se_videos
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_videos'";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_videos`
      (
        `video_id` int(9) unsigned NOT NULL auto_increment,
        `video_user_id` int(9) unsigned NOT NULL default '0',
        `video_datecreated` int(14) NOT NULL default '0',
        `video_title` varchar(255) default NULL,
        `video_desc` text NULL,
        `video_views` smallint(5) unsigned NOT NULL default '0',
        `video_cache_rating` float NOT NULL default '0',
        `video_cache_rating_weighted` float NOT NULL default '0',
        `video_cache_rating_total` int(3) unsigned NOT NULL default '0',
        `video_duration_in_sec` smallint(4) unsigned default NULL,
        `video_is_converted` tinyint(1) NOT NULL default '0',
        `video_privacy` int(2) default NULL,
        `video_comments` int(2) default NULL,
        `video_search` tinyint(1) unsigned default '1',
        `video_totalcomments` smallint unsigned default 0,
        `video_type` tinyint(1) NOT NULL default '0',
        `video_youtube_code` varchar(50) default NULL,
        PRIMARY KEY  (`video_id`),
        KEY `video_cache_rating` (`video_cache_rating`),
        KEY `video_views` (`video_views`),
        FULLTEXT KEY `title_and_text` (`video_title`,`video_desc`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  $sql = "SHOW COLUMNS FROM `se_videos` LIKE 'video_dateupdated'";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_videos ADD COLUMN `video_dateupdated` int(14) NOT NULL default '0'";
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `se_videos` LIKE 'video_uploaded'";
  $resource = $database->database_query($sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_videos ADD COLUMN `video_uploaded` tinyint(1) NOT NULL default '0'";
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    $sql = "UPDATE se_videos SET video_uploaded=1 WHERE 1";
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `se_videos` LIKE 'video_youtube_code'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br />  <b>Line: </b>".__LINE__."<br /><b> Query: </b>".$sql);
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("ALTER TABLE se_videos ADD COLUMN `video_youtube_code` varchar(50) default NULL");
  }
  
  
  // Add video_totalcomments
  $sql = "SHOW COLUMNS FROM `se_videos` LIKE 'video_totalcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_videos ADD COLUMN `video_totalcomments` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT video_id FROM se_videos WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_videos SET video_totalcomments=(SELECT COUNT(videocomment_id) FROM se_videocomments WHERE videocomment_video_id=video_id) WHERE video_id='{$result['video_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on video_title
  $sql = "SHOW FULL COLUMNS FROM `se_videos` LIKE 'video_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $video_title_column = $result;
  }
  
  
  // Ensure utf8 on video_desc
  $sql = "SHOW FULL COLUMNS FROM `se_videos` LIKE 'video_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $video_desc_column = $result;
  }
  
  // Drop fulltext?!?
  if( !empty($video_title_column) || !empty($video_desc_column) )
  {
    $sql = "ALTER TABLE se_videos DROP INDEX `title_and_text`";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  if( !empty($video_title_column) )
  {
    $sql = "ALTER TABLE se_videos MODIFY {$video_title_column['Field']} {$video_title_column['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !empty($video_desc_column) )
  {
    $sql = "ALTER TABLE se_videos MODIFY {$video_desc_column['Field']} {$video_desc_column['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Add fulltext
  if( !empty($video_title_column) || !empty($video_desc_column) )
  {
    $sql = "ALTER TABLE se_videos ADD FULLTEXT `title_and_text` (`video_title`, `video_desc`)";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  
	//######### CREATE se_videocomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_videocomments'";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_videocomments`
      (
        `videocomment_id` int(10) unsigned NOT NULL auto_increment,
        `videocomment_video_id` int(10) unsigned NOT NULL,
        `videocomment_authoruser_id` int(9) unsigned default NULL,
        `videocomment_date` int(14) NOT NULL default '0',
        `videocomment_body` text NULL,
        PRIMARY KEY  (`videocomment_id`),
        KEY `INDEX` (`videocomment_video_id`, `videocomment_authoruser_id`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  
  // Ensure utf8 on videocomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_videocomments` LIKE 'videocomment_body'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_videocomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  $sql = "SHOW COLUMNS FROM `se_videos` LIKE 'video_type'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br />  <b>Line: </b>".__LINE__."<br /><b> Query: </b>".$sql);
  if( !$database->database_num_rows($resource))
  {
    $database->database_query("ALTER TABLE se_videos ADD COLUMN `video_type` tinyint(1) NOT NULL default '0'");
  }

  
	//######### CREATE se_videoratings
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_videoratings'";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_videoratings` (
        `videorating_video_id` int(10) unsigned NOT NULL,
        `videorating_user_id` int(9) unsigned NOT NULL,
        `videorating_rating` tinyint(1) unsigned default NULL,
        PRIMARY KEY  (`videorating_video_id`,`videorating_user_id`),
        KEY `INDEX` (`videorating_video_id`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  
  
	//######### INSERT se_urls
  $sql = "SELECT url_id FROM se_urls WHERE url_file='video'";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Video URL', 'video', 'video.php?user=\$user&video_id=\$id1', '\$user/videos/\$id1')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  else
  {
    $sql = "UPDATE se_urls SET url_subdirectory='\$user/videos/\$id1' WHERE url_subdirectory='\$user/video/\$id1' LIMIT 1";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
	//######### INSERT se_actiontypes
  $actiontypes = array();
	if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newvideo'")) )
  {
	  $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newvideo', 'video_action_addvideo.gif', '1', '1', '5500178', '5500179', '[username],[displayname],[videoid],[videotitle]', '1')
    ");
	  $actiontypes[] = $database->database_insert_id();
  }

	if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newyoutubevideo'")) )
  {
	  $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newyoutubevideo', 'video_action_addvideo.gif', '1', '1', '5500198', '5500199', '[username],[displayname],[videoid],[videotitle]', '1')
    ");
	  $actiontypes[] = $database->database_insert_id();
  }







  
	if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='videocomment'")) )
  {
	  $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('videocomment', 'action_postcomment.gif', '1', '1', '5500180', '5500181', '[username1],[displayname1],[username2],,[comment],[id],[title]', '0')
    ");
	  $actiontypes[] = $database->database_insert_id();
	}
  
  $actiontypes = array_filter($actiontypes);
	if( !empty($actiontypes) )
  {
	  $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
	}
  
  $database->database_query("
    UPDATE
      se_languagevars
    SET
      languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> added a new video \"<a href=\"video.php?user=%1\$s&video_id=%3\$s\">%4\$s</a>\": <div class=\"recentaction_div_media\">[media]</div>'
    WHERE
      languagevar_value LIKE '%added a new video%' &&
      languagevar_value NOT LIKE '%recentaction_div_media%'
  ");
  
  
  
	//######### INSERT se_notifytypes
	if( !$database->database_num_rows($database->database_query("SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='videocomment'")) )
  {
	  $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('videocomment', '5500182', 'action_postcomment.gif', 'video.php?user=%1\$s&video_id=%2\$s', '5500183')
    ");
	}
  
  
	//######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
	if( !$database->database_num_rows($database->database_query("SELECT systememail_id FROM se_systememails WHERE systememail_name='videocomment'")) )
  {
	  $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('videocomment', '5500152', '5500153', '5500184', '5500185', '[displayname],[commenter],[link]')
    ");
	}
  
  
  
	//######### ADD COLUMNS/VALUES TO LEVELS TABLE IF VIDEO HAS NEVER BEEN INSTALLED
	if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_video_allow'")) == 0)
  {
		$sql = "
      ALTER TABLE se_levels
      ADD COLUMN `level_video_allow` tinyint(1) unsigned NOT NULL default '1',

      ADD COLUMN `level_video_privacy` varchar(100) NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}',
      ADD COLUMN `level_video_comments` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}',
      ADD COLUMN `level_video_search` tinyint(1) unsigned NOT NULL default '1',
      ADD COLUMN `level_video_maxnum` tinyint(5) unsigned NOT NULL default '100',
      ADD COLUMN `level_video_maxsize` int(10) unsigned NOT NULL default '20971520'
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}

if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_youtube_allow'")) == 0)
  {
  $sql = "
      ALTER TABLE se_levels  
      ADD COLUMN `level_youtube_allow` tinyint(1) unsigned NOT NULL default '1'";
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
 }

  
  
	//######### ADD COLUMNS/VALUES TO SETTINGS TABLE IF VIDEO HAS NEVER BEEN INSTALLED
	if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_video'")) == 0)
  {
		$sql = "
      ALTER TABLE se_settings
      ADD COLUMN `setting_permission_video` tinyint(1) unsigned NOT NULL default '1',
      ADD COLUMN `setting_video_ffmpeg_path` varchar(255) collate utf8_unicode_ci NOT NULL default '".$video_ffmpeg_path."',
      ADD COLUMN `setting_video_width` smallint(3) unsigned NOT NULL default '480',
      ADD COLUMN `setting_video_height` smallint(3) unsigned NOT NULL default '386',
      ADD COLUMN `setting_video_thumb_width` smallint(3) unsigned NOT NULL default '80',
      ADD COLUMN `setting_video_thumb_height` smallint(3) unsigned NOT NULL default '70',
      ADD COLUMN `setting_video_mimes` text collate utf8_unicode_ci,
      ADD COLUMN `setting_video_exts` text collate utf8_unicode_ci,
      ADD COLUMN `setting_video_max_jobs` tinyint(2) unsigned NOT NULL default '3',
      ADD COLUMN `setting_video_cronjob` tinyint(1) unsigned NOT NULL default '0'
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  else
  {
    $sql = "UPDATE se_settings SET setting_video_ffmpeg_path='{$video_ffmpeg_path}'";
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }

	
	//######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
	if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_videocomment'")) == 0)
  {
	  $sql = "ALTER TABLE se_usersettings ADD COLUMN `usersetting_notify_videocomment` int(1) NOT NULL default '1'";
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
 

	//######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5500001 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
	  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5500001, 1, 'Please register to view comments.', ''),
        (5500002, 1, 'This video does not exist', ''),
        (5500003, 1, 'Video', ''),
        (5500004, 1, 'To upload a video from your computer, click the \"Browse\" button, locate it on your computer, then click the \"Upload\" button.<br>You can also provide a title and description for your video, as well as specify privacy settings below.<br>Please be patient while your video uploads - do not navigate away from the page until the upload is complete.', ''),
        (5500005, 1, 'Edit video settings', ''),
        (5500006, 1, 'Please provide a title for your video.', ''),
        (5500007, 1, 'Video File', ''),
        (5500008, 1, 'You may upload files of the following types: %1\$s', ''),
        (5500009, 1, 'You may upload files with sizes up to %1\$s KB.', ''),
        (5500010, 1, 'Title', ''),
        (5500011, 1, 'Please type in a title', ''),
        (5500012, 1, 'Description', ''),
        (5500013, 1, 'Describe your video or just provide some keywords.', ''),
        (5500014, 1, 'Include this video in search/browse results?', ''),
        (5500015, 1, 'Yes, include this video in search/browse results.', ''),
        (5500016, 1, 'No, exclude this video from search/browse results.', ''),
        (5500017, 1, 'Who can watch this video?', ''),
        (5500018, 1, 'Who can comment on this video?', ''),
        (5500019, 1, 'Upload video', ''),
        (5500020, 1, 'Save changes', ''),
        (5500021, 1, 'Search Videos', ''),
        (5500022, 1, '%1\$s\'s videos', ''),
        (5500023, 1, '%1\$s comment(s)', ''),
        (5500024, 1, 'Uploaded %1\$s.', ''),
        (5500025, 1, 'My Videos', ''),
        (5500026, 1, 'Browse your videos', ''),
        (5500027, 1, 'less', ''),
        (5500028, 1, 'Video', ''),
        (5500029, 1, 'Browse Videos', ''),
        (5500030, 1, 'Browse popular videos', ''),
        (5500031, 1, 'Browse', ''),
        (5500032, 1, 'My Videos', ''),
        (5500033, 1, 'Upload new video', ''),
        (5500034, 1, 'Search', ''),
        (5500035, 1, 'Search', ''),
        (5500036, 1, 'Thanks for rating!', ''),
        (5500037, 1, 'Ratings: <b><span id=\'rating_total\'>%1\$s</span></b>', ''),
        (5500038, 1, 'Views: <b>%1\$s</b>', ''),
        (5500039, 1, 'Report video', ''),
        (5500040, 1, 'Videos from', ''),
        (5500041, 1, 'Video URL', ''),
        (5500042, 1, 'Permanent video URL:', ''),
        (5500043, 1, '%1\$s view(s), %2\$s comment(s)', ''),
        (5500044, 1, 'Video', ''),
        (5500045, 1, 'Video Settings', ''),
        (5500046, 1, 'Video Settings', ''),
        (5500047, 1, 'Allow Videos?', ''),
        (5500048, 1, 'Do you want to allow users to upload videos? If set to \"NO\", all other settings on this page will not apply. ', ''),
        (5500049, 1, 'Yes, allow users to upload videos.', ''),
        (5500050, 1, 'No, do not allow users to upload videos.', ''),
        (5500051, 1, 'Video Privacy Options', ''),
        (5500052, 1, 'Search Privacy Options', ''),
        (5500053, 1, 'If you enable this feature, users will be able to exclude their videos from search results. Otherwise, all videos will be included in search results.', ''),
        (5500054, 1, 'Yes, allow users to exclude their videos from search results. ', ''),
        (5500055, 1, 'No, force all videos to be included in search results. ', ''),
        (5500056, 1, 'Video Privacy Options', ''),
        (5500057, 1, 'Your users can choose from any of the options checked below when they decide who can see their video. If you do not check any options, everyone will be allowed to view videos.', ''),
        (5500058, 1, 'Video Comment Options', ''),
        (5500059, 1, 'Your users can choose from any of the options checked below when they decide who can post comments on their video. If you do not check any options, everyone will be allowed to post comments on media.', ''),
        (5500060, 1, 'Maximum Allowed Videos', ''),
        (5500061, 1, 'Enter the maximum number of allowed videos. The field must contain an integer between 1 and 999. ', ''),
        (5500062, 1, 'allowed videos', ''),
        (5500063, 1, 'Maximum Upload Filesize', ''),
        (5500064, 1, 'Enter the maximum filesize for uploaded files in KB.', ''),
        (5500065, 1, 'Save Changes', ''),
        (5500066, 1, 'If you have enabled videos, your users will have the option of uploading their videos to their profile. Use this page to configure your video settings.', ''),
        (5500067, 1, 'You are not logged in!', ''),
        
        (5500070, 1, '%1\$s view(s)', ''),
        (5500071, 1, 'View:', ''),
        (5500072, 1, 'Everyone\'s Videos', ''),
        (5500073, 1, 'My Friends\' Videos', ''),
        (5500074, 1, 'Show:', ''),
        (5500075, 1, 'Recently Uploaded', ''),
        (5500076, 1, '<a href=\'%1\$s\'>%2\$s</a>\'s videos', ''),
        (5500077, 1, 'Could not create encoding job', ''),
        (5500078, 1, 'Video Title', ''),
        (5500079, 1, 'Video Description', ''),
        (5500080, 1, 'Video File', ''),
        (5500081, 1, 'You have reached the maximum number of allowed videos (%1\$d). You must delete some of your old videos before you can upload a new one.', ''),
        (5500082, 1, 'To edit your video\'s title, description, and privacy settings, complete the form below and click \"Save Changes\".', ''),
        (5500083, 1, 'Edit Video', ''),
        (5500084, 1, 'Other Videos', ''),
        (5500085, 1, 'View Videos', ''),
        (5500086, 1, 'This page lists all of the videos that users have uploaded on your social network. You can use this page to monitor these videos and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific albums. Leaving the filter fields blank will show all the videos on your social network.', ''),
        (5500087, 1, 'Title', ''),
        (5500088, 1, 'Owner', ''),
        (5500089, 1, 'No videos were found.', ''),
        (5500090, 1, '%1\$d Videos Found', ''),
        (5500091, 1, 'view', ''),
        (5500092, 1, 'days', ''),
        (5500093, 1, 'about 1 month', ''),
        (5500094, 1, 'months', ''),
        (5500095, 1, 'about 1 year', ''),
        (5500096, 1, 'over', ''),
        (5500097, 1, 'years', ''),
        (5500098, 1, 'Videos', ''),
        (5500099, 1, 'Videos', ''),
        (5500100, 1, 'Comments', ''),
        (5500101, 1, 'more', ''),
        (5500102, 1, '%1\$s', ''),
        (5500103, 1, 'You currently have %1\$s video(s).', ''),
        (5500104, 1, 'Upload Video', ''),
        (5500105, 1, 'You can upload up to', ''),
        (5500106, 1, 'Add New Video', ''),
        (5500107, 1, 'Edit Video', ''),
        (5500108, 1, 'Delete Video', ''),
        (5500109, 1, 'You do not currently have any videos.', ''),
        (5500110, 1, 'Upload a video here.', ''),
        (5500111, 1, 'Back To My Videos', ''),
        (5500112, 1, 'Uploading...', ''),
        (5500113, 1, 'Do not close this window until your upload is complete.', ''),
        (5500114, 1, 'View more videos...', ''),
        (5500115, 1, 'Video Plugin Settings', ''),
        (5500116, 1, 'Global Video Settings', ''),
        (5500117, 1, 'Global Video Settings', ''),
        (5500118, 1, 'This page contains general video settings that affect your entire social network. ', ''),
        (5500119, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\'admin_general.php\'>General Settings</a> page.', ''),
        (5500120, 1, 'Yes, the public can view videos unless they are made private.', ''),
        (5500121, 1, 'No, the public cannot view videos.', ''),
        (5500122, 1, 'Path to FFMPEG', ''),
        (5500123, 1, 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)', ''),
        (5500124, 1, 'FFMPEG Mimetypes [optional]', ''),
        (5500125, 1, 'Please specify the mime types your FFMPEG installation can encode.', ''),
        (5500126, 1, '(comma separated list)', ''),
        (5500127, 1, 'Enter the file extensions that are connected to your specified mime types.', ''),
        (5500128, 1, 'Video And Thumbnail Size', ''),
        (5500129, 1, 'Enter the size of the encoded video. Note that these values must be even.', ''),
        (5500130, 1, 'Enter the size of the thumbnail. ', ''),
        (5500131, 1, 'Height', ''),
        (5500132, 1, 'Width', ''),
        (5500133, 1, 'Encoding Jobs', ''),
        (5500134, 1, 'How many jobs do you want to allow to run at the same time?', ''),
        (5500135, 1, 'jobs at the same time', ''),
        (5500136, 1, 'Cronjob', ''),
        (5500137, 1, 'You can use a cronjob to trigger the job queue handler. If you would like to use a cronjob to trigger the job queue hanlder, follow the instructions in the file video_conversion_cronjob.php', ''),
        (5500138, 1, 'I am using a cronjob to trigger the job queue handler.', ''),
        (5500139, 1, 'I want the system to trigger the job queue handler.', ''),
        (5500140, 1, 'Video Upload Results', ''),
        (5500141, 1, 'Video: %1\$s', ''),
        (5500142, 1, 'Video posted by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', ''),
        (5500143, 1, '%1\$d videos', ''),
        (5500144, 1, 'Most Popular', ''),
        (5500145, 1, 'Delete Video?', ''),
        (5500146, 1, 'Are you sure you want to delete this video?', ''),
        (5500147, 1, 'Report Abuse', ''),
        (5500148, 1, 'You do not have permission to view this video.', ''),
        (5500149, 1, 'Recently Uploaded Videos', ''),
        (5500150, 1, 'Top Rated Videos', ''),
        (5500151, 1, '%1\$s\'s video - %2\$s', ''),
        (5500152, 1, 'New Video Comment Email', ''),
        (5500153, 1, 'This is the email that gets sent to a user when a comment is posted on one of their videos.', ''),
        (5500154, 1, 'Your maximum filesize field must contain an integer greater than 1.', ''),
        (5500155, 1, 'Your maximum allowed videos field must contain an integer between 1 and 999.', ''),
        (5500156, 1, 'Highest Rated', '')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
 

	//######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5500158 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        
        (5500158, 1, 'Upload a Video', ''),
        (5500159, 1, 'Video Utilities', ''),
        (5500160, 1, 'This page contains utilities to help configure and troubleshoot the video plugin.', ''),
        (5500161, 1, 'Ffmpeg Version', ''),
        (5500162, 1, 'This will display the current installed version of ffmpeg.', ''),
        (5500163, 1, 'Supported Video Formats', ''),
        (5500164, 1, 'This will run and show the output of \"ffmpeg -formats\". Please see <a target=\"_blank\" href=\"http://ffmpeg.mplayerhq.hu/ffmpeg-doc.html\">this page</a> for more info.', ''),
        (5500165, 1, 'Log Browser', ''),
        (5500166, 1, 'If you have enabled debugging for the video plugin, you can use this to easily view the contents of the log files.', ''),
        (5500167, 1, 'Results for \"Ffmpeg Version\"', ''),
        (5500168, 1, 'Results for \"Supported Ffmpeg Formats\"', ''),
        (5500169, 1, 'Quick overview: D - Decoding supported, E - Encoding supported, V - Video Supported, A - Audio Supported', ''),
        (5500170, 1, 'Filesize: %1\$s bytes', ''),
        (5500171, 1, 'Type: %1\$s', ''),
        (5500172, 1, 'File: %1\$s', ''),
        (5500173, 1, 'Your video has uploaded successfully! It may take a few minutes to become available.', ''),
        (5500174, 1, 'Please enable debugging in include/class_video.php on line 28 to enable this feature. You must also set chmod 777 on the uploads_video/encoding/debug folder.', '')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5500175 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5500175, 1, 'Videos: %1\$d videos', ''),
        (5500176, 1, 'Video Comments: %1\$d comments', ''),
        (5500177, 1, 'Video Ratings: %1\$d ratings', '')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5500178 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5500178, 1, 'Uploading a Video', 'actiontypes'),
        (5500179, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> added a new video \"<a href=\"video.php?user=%1\$s&video_id=%3\$s\">%4\$s</a>\": <div class=\"recentaction_div_media\">[media]</div>', 'actiontypes'),
        (5500180, 1, 'Posting a Video Comment', 'actiontypes'),
        (5500181, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the video <a href=\"video.php?user=%3\$s&video_id=%6\$s\">%7\$s</a>:<div class=\"recentaction_div\">%5\$s</div><div class=\"recentaction_div_media\">[media]</div>', 'actiontypes'),
        (5500182, 1, '%1\$d New Video Comment(s): %2\$s', 'notifytypes'),
        (5500183, 1, 'When a new comment is posted one of my videos.', 'notifytypes'),
        (5500184, 1, 'New Video Comment', 'systememails'),
        (5500185, 1, 'Hello %1\$s,\n\nA new comment has been posted by %2\$s on one of your videos. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
	}
  
  
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5500186 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5500186, 1, 'Add YouTube Video', ''),
        (5500187, 1, 'Enter the URL of the YouTube video you would like to add.', ''),
        (5500188, 1, 'Add Video', ''),
        (5500189, 1, 'The URL you have entered is invalid', ''),
        (5500190, 1, 'Video URL', ''),
        (5500191, 1, 'Click here to upload video from your computer instead',''),
        (5500192, 1, 'Click here to add a YouTube video instead', ''),
        (5500193, 1, 'Do you want to allow users to post videos from YouTube?',''),
        (5500194, 1, 'Yes, allow users to post YouTube videos',''),
        (5500195, 1, 'No, do not allow users to post YouTube videos',''),
        (5500196, 1, 'Enter the web address of the YouTube video below', ''),
        (5500197, 1, 'To post a video from YouTube, go to the video\'s YouTube page and copy its web address from your browser\'s address bar into the Video URL field below. <br />You can also provide a title and description for your video, as well as specify privacy settings below.', ''),
        (5500198, 1, 'Adding A YouTube Video', 'actiontypes'),
        (5500199, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a YouTube video \"<a href=\"video.php?user=%1\$s&video_id=%3\$s\">%4\$s</a>\": <div class=\"recentaction_div_media\">[media]</div>', 'actiontypes'),
        (5500200, 1, 'Please enter a video title', ''),
        (5500201, 1, 'You have reached the maximum number of allowed videos. You must delete some of your old videos before you can upload a new one.', '')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SELECT NULL FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5500202 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
	if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5500202, 1, 'This video is being processed.', 'user_video'),
        (5500203, 1, 'This video could not be processed.', 'user_video'),
        (5500204, 1, 'This video failed to upload.', 'user_video')
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
	$sql = "UPDATE se_languagevars SET languagevar_value='Upload New Video' WHERE languagevar_id=5500106 && languagevar_value='Add New Video'";
	$database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  
  
	// try to delete /templates/admin_install_video.tpl
	@unlink(getcwd().'/../templates/admin_install_video.tpl');
 
}

?>