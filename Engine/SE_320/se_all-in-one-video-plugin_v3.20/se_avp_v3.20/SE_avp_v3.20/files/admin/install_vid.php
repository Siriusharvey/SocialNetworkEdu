<?php
$plugin_name = "All-in-one Video Plugin";
$plugin_version = "3.20";
$plugin_type = "vid";
$plugin_desc = "This plugin gives your users possibility to upload videos or add them from different video sharing websites and view them in their profiles.";
$plugin_icon = "vid_vid16.gif";
$plugin_menu_title = "13500006";	
$plugin_pages_main = "13500004<!>vid_vid16.gif<!>admin_viewvids.php<~!~>13500005<!>vid_vid16.gif<!>admin_vid.php<~!~>";
$plugin_pages_level = "13500021<!>admin_levels_vidsettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/vid/([0-9]+)/?$ \$server_info/vid.php?user=\$1&video_id=\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/vids/([0-9]+)/?$ \$server_info/vids.php?user=\$1&p=\$2 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';

if($install == "vid") {

 $is = false;

 if(!in_array('exec', explode(',', ini_get('disable_functions')))) {
        $queryf = $database->database_query("SELECT setting_vid_ffmpeg_path, setting_vid_flvtool2_path FROM se_vidsettings LIMIT 1");
        if ($database->database_num_rows($queryf) > 0) {
                $testf = $database->database_fetch_assoc($queryf);
                $flvtool2_path = $testf[setting_vid_flvtool2_path];
                $ffmpeg_path = $testf[setting_vid_ffmpeg_path];
        } else {
                $flvtool2_path = exec("which flvtool2");
                $ffmpeg_path = exec("which ffmpeg");
        }
 }

 if($flvtool2_path != '' AND $ffmpeg_path != '') {
    $is = false;
		$result = null;
		exec($ffmpeg_path.' -version', $result);
		
		if(empty($result) || !isset($result[0]) || !strstr($result[0], 'FFmpeg')) {
                        $is = false;
		} else {
                        $is = true;
    }
		
		$result2 = null;
		exec($flvtool2_path.' -H', $result2);
		
		if(empty($result2) || !isset($result2[0]) || !strstr($result2[0], 'FLVTool2')) {
                        $is = false;
		} else {
                        $is = true;
    }
 }

 if(isset($_GET['do']) AND $_GET['do'] == 1) {
   if(isset($_GET['ffmpeg_path']) AND isset($_GET['flvtool2_path'])) {
    $ffmpeg_path = escapeshellcmd(strip_tags($_GET['ffmpeg_path']));
    $flvtool2_path = escapeshellcmd(strip_tags($_GET['flvtool2_path']));

    $is = false;
		$result = null;
		exec($ffmpeg_path.' -version', $result);
		
		if(empty($result) || !isset($result[0]) || !strstr($result[0], 'FFmpeg')) {
                        $is = false;
		} else {
                        $is = true;
    }
		
		$result = null;
		exec($flvtool2_path.' -H', $result2);
		
		if(empty($result2) || !isset($result2[0]) || !strstr($result2[0], 'FLVTool2')) {
                        $is = false;
		} else {
                        $is = true;
    }
   } else {
     $is = false;
     header("Location: admin_install_vid.php");
     exit;
   }
 }

 if(isset($_GET['do']) AND $_GET['do'] == 2) {
     $is = true;
     $ffmpeg_path = escapeshellcmd(strip_tags($_GET['ffmpeg_path']));
     $flvtool2_path = escapeshellcmd(strip_tags($_GET['flvtool2_path']));
 }

 if($is === true) {
 
   //######### SAVE IMAGES FROM YOUTUBE V3.10
  if($database->database_num_rows($database->database_query("SELECT plugin_id FROM se_plugins WHERE plugin_version='$plugin_version'")) == 0) {
  
        $query = $database->database_query("SELECT vid_id, vid_user_id, vid_location FROM se_vids WHERE vid_location like \"%,http%\"");
        $user_ids = array();
 
        while ($test = $database->database_fetch_assoc($query)) {
              $vid_location = explode(",", $test[vid_location]);
              $contents = file_get_contents($vid_location[1]);
              if ($contents) {
                    // CHECK THAT UPLOAD DIRECTORY EXISTS, IF NOT THEN CREATE
                    $thumbnail_output_dir = '.'.$video->video_dir($test[vid_user_id]);
                    
                    if (!in_array($test[vid_user_id], $user_ids)) {
                          $video_path_array = explode("/", $thumbnail_output_dir);
                          array_pop($video_path_array);
                          array_pop($video_path_array);
                          $subdir = implode("/", $video_path_array)."/";

                          if(!is_dir($subdir))
                          { 
                                 mkdir($subdir, 0777); 
                                 chmod($subdir, 0777); 
                                 $handle = fopen($subdir."index.php", 'x+');
                                 fclose($handle);
                          }

                          if(!is_dir($thumbnail_output_dir))
                          {
                                 mkdir($thumbnail_output_dir, 0777);
                                 chmod($thumbnail_output_dir, 0777);
                                 $handle = fopen($thumbnail_output_dir."/index.php", 'x+');
                                 fclose($handle);
                          }
                    }
                    
                    $path = $thumbnail_output_dir.$test[vid_id].'.jpg';
                    $handle = fopen($path, 'w') or die("can't open file");
                    fwrite($handle, $contents);
                    fclose($handle);
                    
                    $user_ids[] = $test[vid_user_id];
                    
                    $final_location = $vid_location[0].',youtube';
                    $database->database_query("UPDATE se_vids SET vid_location='$final_location' WHERE vid_id='$test[vid_id]'");
              } else {
                    continue;
              }
        }
  }

  //######### DELETE WHERE vid_is_converted = '2' V3.11 rev 2
  $query_del = $database->database_query("SELECT vid_id, vid_user_id FROM se_vids WHERE vid_is_converted='2'");
  while ($to_be_deleted = $database->database_fetch_assoc($query_del)) {
        $video->delete_video($to_be_deleted[vid_id], $to_be_deleted[vid_user_id], TRUE);
  }
 
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

  //######### CREATE DATABASE STRUCTURE
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_vids'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_vids` (
  `vid_id` int(10) unsigned NOT NULL auto_increment,
  `vid_user_id` int(7) unsigned NOT NULL default '0',
  `vid_datecreated` int(15) NOT NULL default '0',
  `vid_title` varchar(100) collate utf8_unicode_ci default NULL,
  `vid_desc` text collate utf8_unicode_ci,
  `vid_cat` int(10) NOT NULL default '0',
  `vid_tags` text collate utf8_unicode_ci NOT NULL,
  `vid_location` varchar(500) collate utf8_unicode_ci NOT NULL,
  `vid_views` int(10) unsigned NOT NULL default '0',
  `vid_is_converted` tinyint(1) NOT NULL default '0',
  `vid_comments` int(2) default NULL,
  `vid_privacy` int(2) default NULL,
  `vid_search` tinyint(1) unsigned default '1',
  `vid_rating_value` float NOT NULL default '0',
  `vid_rating_raters` text collate utf8_unicode_ci,
  `vid_rating_raters_num` int(9) NOT NULL default '0',
  PRIMARY KEY  (`vid_id`),
  FULLTEXT KEY `vid_title` (`vid_title`,`vid_tags`)
      ) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }

  //######### CREATE TABLE FOR FAVORITE VIDEOS V3.11 rev 2
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_vidfavs'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_vidfavs` (
  `vidfav_user_id` int(7) unsigned NOT NULL default '0',
  `vidfav_ids` text NOT NULL
    ) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  } 

  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_vidcomments'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_vidcomments` (
  `vidcomment_id` int(9) NOT NULL auto_increment,
  `vidcomment_vid_id` int(9) NOT NULL default '0',
  `vidcomment_authoruser_id` int(9) NOT NULL default '0',
  `vidcomment_date` int(14) NOT NULL default '0',
  `vidcomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`vidcomment_id`),
  KEY `vidcomment_user_id` (`vidcomment_vid_id`,`vidcomment_authoruser_id`)
    ) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }
  
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_vidsettings'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_vidsettings` (
  `setting_permission_vid` int(1) NOT NULL default '3',
  `setting_vid_skin` varchar(100) collate utf8_unicode_ci NOT NULL default 'default',
  `setting_vid_ffmpeg_path` varchar(255) collate utf8_unicode_ci default NULL,
  `setting_vid_flvtool2_path` varchar(255) collate utf8_unicode_ci default NULL,
  `vid_width` smallint(3) unsigned NOT NULL default '320',
  `vid_height` smallint(3) unsigned NOT NULL default '320',
  `vid_thumb_width` smallint(3) unsigned NOT NULL default '120',
  `vid_thumb_height` smallint(3) unsigned NOT NULL default '105',
  `setting_vid_mimes` text collate utf8_unicode_ci,
  `setting_vid_exts` text collate utf8_unicode_ci,
  `setting_yt_streaming` tinyint(1) NOT NULL default '0'
     ) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }
  
  //######### ALTER se_vidsettings V3.10
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.se_vidsettings LIKE 'vid_logo'")) == 0) {
    $database->database_query("ALTER TABLE se_vidsettings ADD COLUMN `vid_logo` tinyint(1) NOT NULL default '1', ADD COLUMN `vid_prov_disable` varchar(255) collate utf8_unicode_ci default NULL");
  }

  if($database->database_num_rows($database->database_query("SELECT * FROM se_vidsettings WHERE setting_permission_vid=1")) == 0) {
    $database->database_query("INSERT INTO se_vidsettings (`setting_permission_vid`, `setting_vid_skin`, `setting_vid_ffmpeg_path`, `setting_vid_flvtool2_path`, `vid_width`, `vid_height`, `vid_thumb_width`, `vid_thumb_height`, `setting_vid_mimes`, `setting_vid_exts`, `setting_yt_streaming`, `vid_logo`, `vid_prov_disable`) VALUES
(1, 'default', '$ffmpeg_path', '$flvtool2_path', 320, 320, 80, 70, '', '', 0, 1, '')");
  }
  
  if($database->database_num_rows($database->database_query("SELECT vid_logo FROM se_vidsettings")) == 0) {
    $database->database_query("UPDATE se_vidsettings SET vid_logo='1'");
    $database->database_query("UPDATE se_vidsettings SET vid_prov_disable=NULL");
  }

  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_vidtags'")) == 0) {
    $database->database_query("CREATE TABLE IF NOT EXISTS `se_vidtags` (
  `tag` varchar(50) collate utf8_unicode_ci NOT NULL,
  `value` int(9) NOT NULL
    ) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }
  
    //######### CREATE se_vidcats
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_vidcats'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_vidcats`
      (
  `vidcat_id` int(10) unsigned NOT NULL auto_increment,
  `vidcat_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `vidcat_languagevar_id` int(10) unsigned NOT NULL default '0',
  `vidcat_parentcat_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`vidcat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  //######### INSERT se_vid_cats
  if($database->database_num_rows($database->database_query("SELECT vidcat_title FROM se_vidcats WHERE vidcat_id=0")) == 0) {
    $database->database_query("INSERT INTO se_vidcats (vidcat_id, vidcat_title, vidcat_languagevar_id, vidcat_parentcat_id) VALUES (1, 'All Videos', 13500017, 0)");
  }
  

  //######### INSERT se_urls
  if($database->database_num_rows($database->database_query("SELECT url_id FROM se_urls WHERE url_file='vid_file'")) == 0) {
    $database->database_query("INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Video URL', 'vid_file', 'vid.php?user=\$user&video_id=\$id1', '\$user/vid/\$id1'),
('Videos URL', 'vids', 'vids.php?user=\$user&p=\$id1', '\$user/vids/\$id1')");
  }

  //######### INSERT se_actiontypes
  if($database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newvid'")) == 0) {
        $database->database_query("INSERT INTO se_actiontypes (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media) VALUES ('vidcomment', 'action_postcomment.gif', 1, 1, 13500022, 13500020, '[username1],[displayname1],[username2],[comment],[id],[title]', 0),
    ('newvid', 'vid_vid16.gif', 1, 1, 13500001, 13500000, '[username],[displayname],[id],[title]', 1)");
        $actiontypes[] = $database->database_insert_id();  
  }
  if(count($actiontypes) != 0) {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode( ",", $actiontypes)."')");
  }
  

  //######### ALTER se_levels V3.10
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.se_levels LIKE 'level_vid_yt_unlimited'")) == 1) {
    $database->database_query("ALTER TABLE se_levels DROP COLUMN level_vid_yt_unlimited");
  }
  
  //######### ALTER se_levels V3.00
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.se_levels LIKE 'level_vid_allow'")) == 0) {
    $database->database_query("ALTER TABLE se_levels ADD COLUMN `level_vid_allow` tinyint(1) unsigned NOT NULL default '3',
  ADD COLUMN `level_vid_maxnum` tinyint(5) unsigned NOT NULL default '100',
  ADD COLUMN `level_vid_maxsize` int(10) unsigned NOT NULL default '20971520'");
  }
  
  //######### ALTER se_levels V3.00
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.se_levels LIKE 'level_vid_prov'")) == 0) {
    $database->database_query("ALTER TABLE se_levels ADD COLUMN `level_vid_prov` varchar(500) collate utf8_unicode_ci NOT NULL default ',youtube,break,bliptv,metacafe,google',
  ADD COLUMN `level_vid_prov_maxnum` tinyint(5) unsigned NOT NULL default '100',
  ADD COLUMN `level_vid_privacy` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}',
  ADD COLUMN `level_vid_comments` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}',
  ADD COLUMN `level_vid_search` tinyint(1) unsigned NOT NULL default '1'");
  }
  
  //######### ALTER se_levels ADD level_vid_allow, level_vid_maxnum, level_vid_maxsize AND level_vid_yt_unlimited COLUMNS
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.se_levels LIKE 'level_vid_allow'")) == 0) {
    $database->database_query("ALTER TABLE se_levels ADD COLUMN `level_vid_allow` tinyint(1) unsigned NOT NULL default '3', ADD COLUMN `level_vid_maxnum` tinyint(5) unsigned NOT NULL default '100', ADD COLUMN `level_vid_maxsize` int(10) unsigned NOT NULL default '20971520', ADD COLUMN `level_vid_yt_unlimited` tinyint(1) NOT NULL default '1'");
  }

  //######### ALTER se_vidsettings ADD setting_vid_embed COLUMN
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.se_vidsettings LIKE 'setting_vid_embed'")) == 0) {
    $database->database_query("ALTER TABLE se_vidsettings ADD COLUMN `setting_vid_embed` tinyint(1) unsigned NOT NULL default '0'");
  }

  //######### INSERT LANGUAGE VARS V3.00       
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500000 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES 
				(13500000, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> added a new video \"<a href=\"vid.php?user=%1\$s&video_id=%3\$s\">%4\$s</a>\":<div class=''recentaction_div_media''>[media]</div>', ''),
        (13500001, 1, 'Adding a Video', ''),
        (13500002, 1, 'Delete Video?', ''),
        (13500003, 1, 'Are you sure you want to delete this video?', ''),
        (13500004, 1, 'View Videos', ''),
        (13500005, 1, 'Global Video Settings', ''),
        (13500006, 1, 'Video Plugin Settings', ''),
        (13500007, 1, 'Videos', ''),
        (13500008, 1, 'This page contains general video settings that affect your entire social network.', ''),
        (13500009, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. Important: your users will be able to make their videos private even though you have made them publically viewable here if you have given them the option.', ''),
        (13500010, 1, 'Yes, the public can view videos.', ''),
        (13500011, 1, 'No, the public cannot view videos.', ''),
        (13500012, 1, 'Video Player Settings', ''),
        (13500013, 1, 'You do not have any videos.', ''),
        (13500014, 1, 'Add a new video now.', ''),
        (13500015, 1, 'The video you are looking for has been deleted or does not exist.', ''),
        (13500016, 1, 'Videos', ''),
        (13500017, 1, 'All Videos', ''),
        (13500018, 1, 'Path to FFMPEG & FLVTool2', ''),
        (13500019, 1, 'Please enter the full path to your FFMPEG installation.', ''),
        (13500020, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the video <a href=\"vid.php?user=%3\$s&video_id=%6\$s\">%7\$s</a>:<div class=\"recentaction_div\">%5\$s</div><div class=\"recentaction_div_media\">[media]</div>', ''),
        (13500021, 1, 'Video Settings', ''),
        (13500022, 1, 'Posting a Video Comment', ''),
        (13500023, 1, 'To edit your video''s title, description, and tags, complete the form below and click \"Save Changes\".', ''),
        (13500024, 1, 'Video Title', ''),
        (13500025, 1, 'Video Description', ''),
        (13500026, 1, 'Video Tags', ''),
        (13500027, 1, 'Edit Video', ''),
        (13500028, 1, '%1\$s view(s)', ''),
        (13500029, 1, 'Video And Thumbnail Size', ''),
        (13500030, 1, 'Enter the size of the encoded video. Note that these values must be even.', ''),
        (13500031, 1, 'Width', ''),
        (13500032, 1, 'Height', ''),
        (13500033, 1, 'Enter the size of the thumbnail (browse_vids).', ''),
        (13500034, 1, 'Video Player Skin', ''),
        (13500035, 1, 'Browse Videos', ''),
        (13500036, 1, 'Please enter the full path to your FLVTool2 installation.', ''),
        (13500037, 1, 'Allow Videos?', ''),
        (13500038, 1, 'Do you want to allow users to upload/add videos?', ''),
        (13500039, 1, 'Yes, allow users only to upload videos.', ''),
        (13500040, 1, 'No, do not allow users to upload nor add videos.', ''),
        (13500041, 1, 'Video Uploads', ''),
        (13500042, 1, 'Enter the maximum number of videos that can be uploaded. The field must contain an integer between 1 and 999.', ''),
        (13500043, 1, 'allowed videos', ''),
        (13500044, 1, 'Maximum Upload Filesize', ''),
        (13500045, 1, 'Enter the maximum filesize per video in KB.', ''),
        (13500046, 1, 'If you have enabled videos, your users will have the option of uploading videos or adding them from different video providers'' websites to their profile. Use this page to configure your video settings.', ''),
        (13500047, 1, 'Save Changes', ''),
        (13500048, 1, 'Do you want to allow users to add videos from Youtube?', ''),
        (13500049, 1, 'Yes, allow users to add videos from different video providers'' websites.', ''),
        (13500050, 1, 'No, do not allow users to add videos from Youtube.', ''),
        (13500051, 1, 'Click here to add a video from the video sharing website instead', ''),
        (13500052, 1, 'Your maximum allowed videos field must contain an integer between 1 and 999.', ''),
        (13500053, 1, 'Your maximum allowed Youtube videos field must contain an integer between 1 and 999.', ''),
        (13500054, 1, 'Your maximum filesize field must contain an integer greater than 1.', ''),
        (13500055, 1, '<a href=\"%1\$s\">%2\$s</a>''s videos', ''),
        (13500056, 1, 'More from: %1\$s', ''),
        (13500057, 1, 'Tag Cloud [ + ]', ''),
        (13500058, 1, 'Tag Cloud [ - ]', ''),
        (13500059, 1, 'You are not allowed to upload or add videos. Either you have exceeded the maximum number of allowed videos or you do not have permission to use this feature.', ''),
        (13500060, 1, 'Search:', ''),
        (13500061, 1, 'Search', ''),
        (13500062, 1, 'Category:', ''),
        (13500063, 1, 'Show:', ''),
        (13500064, 1, 'Most Viewed', ''),
        (13500065, 1, 'Top Rated', ''),
        (13500066, 1, 'Recently Added', ''),
        (13500067, 1, 'DESC', ''),
        (13500068, 1, 'ASC', ''),
        (13500069, 1, 'order.', ''),
        (13500070, 1, 'Information', ''),
        (13500071, 1, 'Added:', ''),
        (13500072, 1, 'more info', ''),
        (13500073, 1, 'less info', ''),
        (13500074, 1, 'Category:', ''),
        (13500075, 1, 'Tags:', ''),
        (13500076, 1, 'Related Videos', ''),
        (13500077, 1, 'My Videos', ''),
        (13500078, 1, 'You have uploaded %1\$s video(s).<br />You can still upload %2\$s video(s).', ''),
        (13500079, 1, 'Upload New Video', ''),
        (13500080, 1, 'Rating:', ''),
        (13500081, 1, 'View Video', ''),
        (13500082, 1, 'Delete Video', ''),
        (13500083, 1, 'Edit Video', ''),
        (13500084, 1, 'Add Video', ''),
        (13500085, 1, 'Please give us some information about your new video.', ''),
        (13500086, 1, 'Video Category', ''),
        (13500087, 1, 'File', ''),
        (13500088, 1, 'Video URL', ''),
        (13500089, 1, 'Add', ''),
        (13500090, 1, 'Cancel', ''),
        (13500091, 1, 'Add a video from the video sharing website', ''),
        (13500092, 1, 'view all', ''),
        (13500093, 1, 'Title', ''),
        (13500094, 1, 'Owner', ''),
        (13500095, 1, 'From:', ''),
        (13500096, 1, 'FFMPEG Mimetypes [optional]', ''),
        (13500097, 1, 'Please enter the URL to your SocialEngine community without \"www\" and trailing slash (ie. http://your.domain.ext/community).', ''),
        (13500098, 1, 'Please specify the mime types your FFMPEG installation can encode.\r\n(comma separated list)', ''),
        (13500099, 1, 'Enter the file extensions that are connected to your specified mime types.\r\n(comma separated list)', ''),
        (13500100, 1, 'To add a video from %1\$s go to the video''s page and copy its web address from your browser''s address bar into the Video URL input below. <b>You must also provide a title, description and tags for your video</b> and choose a category that fits best for it. If you would like to get data from a video sharing website, you can press \"Get data\" button (appears after typing the URL of the video). After this click the \"Add\" button.', ''),
        (13500101, 1, 'NB: You can upload files with sizes up to %1\$s KB.', ''),
        (13500102, 1, 'Allowed file types are %1\$s.', ''),
        (13500103, 1, 'View Videos', ''),
        (13500104, 1, 'This page lists all of the videos that users have uploaded on your social network. You can use this page to monitor these videos and delete offensive material if necessary.', ''),
        (13500105, 1, 'Video Sharing Websites', ''),
        (13500106, 1, 'Wherefrom your users can add videos?', ''),
        (13500107, 1, 'Youtube', ''),
        (13500108, 1, 'You have uploaded %1\$s video(s) and added %2\$s from different video sharing websites.<br />You can still upload %3\$s video(s) and add %4\$s from %5\$s.', ''),
        (13500109, 1, 'The video was not added. Embedding disabled by the user in Youtube.', ''),
        (13500110, 1, 'The video you tried to add from Youtube was already in our database.', ''),
        (13500111, 1, 'No videos were found from this location. Please check the URL and try again.', ''),
        (13500112, 1, 'The video you tried to upload was disallowed type.', ''),
        (13500113, 1, 'The video you tried to upload had forbidden mime-type.', ''),
        (13500114, 1, 'The video exceeded the maximum allowed size.', ''),
        (13500115, 1, 'tags only', ''),
        (13500116, 1, 'Videos: %1\$d videos', ''),
        (13500117, 1, 'Yes, allow users only to add videos from different video sharing websites.', ''),
        (13500118, 1, 'Yes, allow both.', ''),
        (13500119, 1, 'You have added %1\$s video(s) from different video sharing websites.<br />You can still add %2\$s video(s) from %3\$s.', ''),
        (13500120, 1, 'You have added %1\$s video(s) from Youtube.<br />You can still add unlimited of amount video(s).', NULL),
        (13500121, 1, 'You have added/uploaded %1\$s video(s).<br /> You can still add/upload add %2\$s video(s).', ''),
        (13500122, 1, 'Please fill all the fields and try again.', ''),
        (13500123, 1, 'The video you uploaded will be process by our server and will be available soon. Meanwhile you can continue browsing our website.', ''),
        (13500124, 1, 'Video posted by <a href=''%1\$s''>%2\$s</a><br>%3\$s', ''),
        (13500125, 1, 'Video: %1\$s', ''),
        (13500126, 1, '%1\$s videos', ''),
        (13500127, 1, 'Get data', ''),
        (13500128, 1, 'All the fields are required. Please fill them and try again.', ''),
        (13500129, 1, 'Report Inappropriate Content', '')") or die("Insert Into se_languagevars: ".mysql_error());
  } else {
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> added a new video \"<a href=\"vid.php?user=%1\$s&video_id=%3\$s\">%4\$s</a>\":<div class=''recentaction_div_media''>[media]</div>' WHERE languagevar_id=13500000");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. Important: your users will be able to make their videos private even though you have made them publically viewable here if you have given them the option.' WHERE languagevar_id=13500009");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the video <a href=\"vid.php?user=%3\$s&video_id=%6\$s\">%7\$s</a>:<div class=\"recentaction_div\">%5\$s</div><div class=\"recentaction_div_media\">[media]</div>' WHERE languagevar_id=13500020");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='No, do not allow users to upload nor add videos.' WHERE languagevar_id=13500040");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Video Uploads' WHERE languagevar_id=13500041");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Enter the maximum number of videos that can be uploaded. The field must contain an integer between 1 and 999.' WHERE languagevar_id=13500042");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Enter the maximum filesize per video in KB.' WHERE languagevar_id=13500045");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='If you have enabled videos, your users will have the option of uploading videos or adding them from different video providers'' websites to their profile. Use this page to configure your video settings.' WHERE languagevar_id=13500046");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Yes, allow users to add videos from different video providers'' websites.' WHERE languagevar_id=13500049");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Click here to add a video from the video sharing website instead' WHERE languagevar_id=13500051");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='<a href=\"%1\$s\">%2\$s</a>''s videos' WHERE languagevar_id=13500055");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='You are not allowed to upload or add videos. Either you have exceeded the maximum number of allowed videos or you do not have permission to use this feature.' WHERE languagevar_id=13500059");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Upload New Video' WHERE languagevar_id=13500079");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Add Video' WHERE languagevar_id=13500084");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Video URL' WHERE languagevar_id=13500088");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Add' WHERE languagevar_id=13500089");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Add a video from the video sharing website' WHERE languagevar_id=13500091");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='To add a video from %1\$s go to the video''s page and copy its web address from your browser''s address bar into the Video URL input below. <b>You must also provide a title, description and tags for your video</b> and choose a category that fits best for it. If you would like to get data from a video sharing website, you can press \"Get data\" button (appears after typing the URL of the video). After this click the \"Add\" button.' WHERE languagevar_id=13500100");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='NB: You can upload files with sizes up to %1\$s KB.' WHERE languagevar_id=13500101");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Video Sharing Websites' WHERE languagevar_id=13500105");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Wherefrom your users can add videos?' WHERE languagevar_id=13500106");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Youtube' WHERE languagevar_id=13500107");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='You have uploaded %1\$s video(s) and added %2\$s from different video sharing websites.<br />You can still upload %3\$s video(s) and add %4\$s from %5\$s.' WHERE languagevar_id=13500108");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='The video you tried to add was already in our database.' WHERE languagevar_id=13500110");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Yes, allow users only to add videos from different video sharing websites.' WHERE languagevar_id=13500117");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='You have added %1\$s video(s) from different video sharing websites.<br />You can still add %2\$s video(s) from %3\$s.' WHERE languagevar_id=13500119");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='The video you uploaded will be process by our server and will be available soon. Meanwhile you can continue browsing our website.' WHERE languagevar_id=13500123");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Video posted by <a href=''%1\$s''>%2\$s</a><br>%3\$s' WHERE languagevar_id=13500124");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Video: %1\$s' WHERE languagevar_id=13500125");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='%1\$s videos' WHERE languagevar_id=13500126");
        $database->database_query("UPDATE `se_languagevars` SET languagevar_value='Get data' WHERE languagevar_id=13500127");
  }
    
  //######### INSERT LANGUAGE VARS V3.10        
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500130 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (13500130, 1, 'Upload', ''),
        (13500131, 1, 'To upload a video from your computer, click the \"Browse\" button and locate it on your computer. <b>You must also provide a title, description and tags for your video</b> and choose a category that fits best for it. After this click the \"Upload\" button. Please be patient while your video uploads - do not navigate away from the page until the upload is complete.', ''),
        (13500132, 1, 'Click here to add a video from a video sharing website instead', ''),
        (13500133, 1, 'Click here to upload video from your computer instead', ''),
        (13500134, 1, 'Upload Video', ''),
        (13500135, 1, 'examples:', ''),
        (13500136, 1, 'Video Privacy Options', ''),
        (13500137, 1, 'If you enable this feature, users will be able to exclude their videos from search results. Otherwise, all videos will be included in search results.', ''),
        (13500138, 1, 'Enter the maximum number of videos that can be added from different video sharing websites. The field must contain an integer between 1 and 999.', ''),
        (13500139, 1, 'Search Privacy Options', ''),
        (13500140, 1, 'Yes, allow users to exclude their videos from search results. ', ''),
        (13500141, 1, 'No, force all videos to be included in search results. ', ''),
        (13500142, 1, 'Video Privacy Options', ''),
        (13500143, 1, 'Your users can choose from any of the options checked below when they decide who can see their video. If you do not check any options, everyone will be allowed to view videos.', ''),
        (13500144, 1, 'Video Comment Options', ''),
        (13500145, 1, 'Your users can choose from any of the options checked below when they decide who can post comments on their video. If you do not check any options, everyone will be allowed to post comments on media.', ''),
        (13500146, 1, 'Include this video in search/browse results?', ''),
        (13500147, 1, 'Yes, include this video in search/browse results.', ''),
        (13500148, 1, 'No, exclude this video from search/browse results.', ''),
        (13500149, 1, 'Who can watch this video?', ''),
        (13500150, 1, 'Who can comment on this video?', ''),
        (13500151, 1, 'Video conversion failed', ''),
        (13500152, 1, 'Conversion of the video you just uploaded failed and file was deleted. Please try different video file format.', ''),
        (13500153, 1, 'Stream videos directly from Youtube.', NULL),
        (13500154, 1, 'Stream videos by using JW Player''s Youtube API.', NULL),
        (13500155, 1, 'Youtube Video Streaming', ''),
        (13500156, 1, 'Visual Settings', ''),
        (13500157, 1, 'Show logos', ''),
        (13500158, 1, 'Hide logos', ''),
        (13500159, 1, 'Select whether you want to show video sharing websites'' logos or not.', ''),
        (13500160, 1, 'Provided by:', ''),
        (13500161, 1, '%1\$s video(s) tagged with %2\$s', ''),
        (13500162, 1, 'If you have problems with any of the video sharing websites listed below, disable them by checking the corresponding check box. Please report any issues to us immediately at info@jaatava-plugins.com. Important: Selection of any video sharing websites will overwrite your video settings on user levels.', ''),
        (13500163, 1, 'We currently have problems with the video sharing website that provides this video. Please try again later.', ''),
        (13500164, 1, 'Having trouble uploading files? Click here to use the simple uploader.', ''),
        (13500165, 1, 'You are not allowed to add videos from this provider.', ''),
        (13500166, 1, 'Import a video from the following providers:', ''),
        (13500167, 1, 'More coming soon..', ''),
        (13500168, 1, 'Use the form below to import videos from the providers available.', ''),
        (13500169, 1, 'Use the form below to upload videos from your computer.', ''),
        (13500170, 1, 'Browse', ''),
        (13500171, 1, 'You must select at least one video sharing website. To disallow all the video sharing websites, you must select either \"Yes, allow users only to upload videos.\" or \"No, do not allow users to upload nor add videos.\" option above.', ''),
        (13500172, 1, 'We currently have problems with every video sharing website that you are allowed to use. Please try again later.', ''),
        (13500173, 1, 'temporarily unavailable', '')") or die("Insert Into se_languagevars: ".mysql_error());
  }
  
  //######### INSERT LANGUAGE VARS V3.10 rev 1
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500174 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (13500174, 1, 'The video was not added because our server was not able to catch image from the video sharing website.', '')") or die("Insert Into se_languagevars: ".mysql_error());
  }
  
  //######### INSERT LANGUAGE VARS V3.11
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500175 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (13500175, 1, 'Encoding for the video \"%1\$s\" failed and the video was deleted. Please try different file format.', ''),
        (13500176, 1, 'OK', '')") or die("Insert Into se_languagevars: ".mysql_error());
  }

  //######### INSERT LANGUAGE VARS V3.11 rev 1
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500177 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (13500177, 1, 'Would you like to use video embedding instead of direct streaming? This is the best way to secure videos'' functionality - it is possible that one day one of the video providers blocks direct streaming.', ''), (13500178, 1, 'Yes, use video embedding.', ''), (13500179, 1, 'No, use direct video streaming.', '')") or die("Insert Into se_languagevars: ".mysql_error());
  }

  //######### INSERT LANGUAGE VARS V3.11 rev 2
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500180 LIMIT 1")) == 0) {
    $database->database_query("INSERT INTO `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES (13500180, 1, 'There was an error while sending data.', ''),
     (13500181, 1, 'The video is already in your Favorites.', ''), 
     (13500182, 1, 'Remove', ''), 
     (13500183, 1, 'The video was added to your Favorites.', ''), 
     (13500184, 1, 'The video does not exists.', ''), 
     (13500185, 1, '%1\$s <a href=\"login.php\">Login</a> or <a href=\"signup.php\">Signup</a> now!', ''), 
     (13500186, 1, 'Want to report inappropriate content?', ''), 
     (13500187, 1, 'Want to add to Favorites?', ''), 
     (13500188, 1, 'The video was removed from your Favorites.', ''), 
     (13500189, 1, 'The video is not in your Favorites.', ''), 
     (13500190, 1, 'URL', ''), 
     (13500191, 1, 'Embed', ''), 
     (13500192, 1, 'BBCode', ''), 
     (13500193, 1, 'You can not exceed the maximum number of uploaded videos.', ''), 
     (13500194, 1, 'Favorite Videos', ''), 
     (13500195, 1, 'Remove Video', ''), 
     (13500196, 1, 'Remove Video?', ''), 
     (13500197, 1, 'Are you sure you want to remove this video from Favorites?', ''), 
     (13500198, 1, 'Favorite', ''), 
     (13500199, 1, 'Share', ''), 
     (13500200, 1, 'Report', ''), 
     (13500201, 1, 'remove', ''),
     (13500202, 1, 'Add a Video', ''),
     (13500203, 1, 'My Videos (%1\$s)', ''),
     (13500204, 1, 'My Favorites (%1\$s)', ''),
     (13500205, 1, 'You do not have permission to view this video.', '')") or die("Insert Into se_languagevars: ".mysql_error());
  }

  //######### UPDATE LANGUAGE VAR V3.11 rev 2
  if($database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_id=13500110 LIMIT 1")) == 1) {
    $database->database_query("UPDATE `se_languagevars` SET languagevar_value = 'The video you tried to add from the video sharing website was already in our database.' WHERE languagevar_id='13500110'") or die("Insert Into se_languagevars: ".mysql_error());
  }

  //######### UPDATE COMMENT/FEED ISSUE V3.10        
  $database->database_query("UPDATE se_actiontypes SET actiontype_vars='[username1],[displayname1],[username2],[displayname2],[comment],[id],[title],[owner]' WHERE actiontype_name='vidcomment'");
  $database->database_query("UPDATE se_languagevars SET languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the video <a href=\"vid.php?user=%3\$s&video_id=%6\$s\">%7\$s</a>:<div class=\"recentaction_div\">%5\$s</div>' WHERE languagevar_id='13500020'");
  $database->database_query("UPDATE se_actiontypes SET actiontype_media='1' WHERE actiontype_name='newvid'");
  
  //######### UPDATE SEARCH ISSUE
  if($database->database_num_rows($database->database_query("SELECT vid_id FROM se_vids WHERE vid_search='63' LIMIT 1")) == 1) {
    $database->database_query("UPDATE se_vids SET vid_search='1'");
  }
  
  // DELETE OLD AND FAILED ENCODING JOBS AFTER 1h30min
  $current_encoder_time = time()-5400;
  $vid_delete_failed = "SELECT vid_id, vid_user_id FROM se_vids WHERE vid_is_converted='2' AND vid_datecreated < ".$current_encoder_time;
  while ($vdf = $database->database_fetch_assoc($database->database_query($vid_delete_failed))) {
    $video->delete_video($vdf[vid_id], $vdf[vid_user_id], TRUE);
  } 
  
 } else {
  header("Location: admin_install_vid.php");
  exit;
 }
}
?>