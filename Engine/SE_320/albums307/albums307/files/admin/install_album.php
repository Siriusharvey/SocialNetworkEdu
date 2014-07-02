<?php

/* $Id: install_album.php 293 2010-01-21 01:16:21Z phil $ */

$plugin_name = "Photo Album Plugin";
$plugin_version = "3.07";
$plugin_type = "album";
$plugin_desc = "This plugin gives your users their own personal photo albums. These albums can be configured to store photos, videos, or any other file types you choose to allow. Users can interact by commenting on each others photos and viewing their friends' recent updates.";
$plugin_icon = "album_album16.gif";
$plugin_menu_title = "1000003";	
$plugin_pages_main = "1000004<!>album_album16.gif<!>admin_viewalbums.php<~!~>1000005<!>album_settings16.gif<!>admin_album.php<~!~>";
$plugin_pages_level = "1000006<!>admin_levels_albumsettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/albums/([0-9]+)/([0-9]+)/?$ \$server_info/album_file.php?user=\$1&album_id=\$2&media_id=\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/albums/([0-9]+)/?$ \$server_info/album.php?user=\$1&album_id=\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/albums/([0-9]+)/([^/]+)?$ \$server_info/album.php?user=\$1&album_id=\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/albums/?$ \$server_info/albums.php?user=\$1 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;




if($install == "album")
{
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
  }
  
  //######### UPDATE PLUGIN VERSION IN se_plugins
  else
  {
    $database->database_query("UPDATE se_plugins SET plugin_name='$plugin_name',
					plugin_version='$plugin_version',
					plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
					plugin_icon='$plugin_icon',
					plugin_menu_title='$plugin_menu_title',
					plugin_pages_main='$plugin_pages_main',
					plugin_pages_level='$plugin_pages_level',
					plugin_url_htaccess='$plugin_url_htaccess' WHERE plugin_type='$plugin_type'");
  }



  //######### CREATE se_albums
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_albums'")) == 0) {
    $database->database_query("
    CREATE TABLE `se_albums` (
      `album_id`          INT           UNSIGNED  NOT NULL auto_increment,
      `album_user_id`     INT           UNSIGNED  NOT NULL default '0',
      `album_datecreated` INT(14)                 NOT NULL default '0',
      `album_dateupdated` INT(14)                 NOT NULL default '0',
      `album_title`       VARCHAR(64)             NOT NULL default '',
      `album_desc`        TEXT                        NULL,
      `album_search`      TINYINT(1)    UNSIGNED  NOT NULL default '0',
      `album_privacy`     TINYINT(2)    UNSIGNED  NOT NULL default '0',
      `album_comments`    TINYINT(2)    UNSIGNED  NOT NULL default '0',
      `album_cover`       INT                     NOT NULL default '0',
      `album_views`       INT           UNSIGNED  NOT NULL default '0',
      `album_totalfiles`  SMALLINT      UNSIGNED  NOT NULL default '0',
      `album_totalspace`  BIGINT        UNSIGNED  NOT NULL default '0',
      PRIMARY KEY  (`album_id`),
      KEY `INDEX` (`album_user_id`)
    ) ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }
  
  
  // ADD ORDER COLUMN
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_albums` LIKE 'album_order'")) == 0) {
    $database->database_query("
      ALTER TABLE se_albums 
      ADD COLUMN `album_order` int(1) NOT NULL default '0',
      ADD COLUMN `album_tag` int(2) NOT NULL default '0'");
    $database->database_query("UPDATE se_albums SET album_order=album_id, album_tag=3");
  }
  
  
  // Add album_totalfiles 
  $sql = "SHOW COLUMNS FROM `se_albums` LIKE 'album_totalfiles'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $totalfiles_exists = (bool) $database->database_num_rows($resource);
  
  if( !$totalfiles_exists )
  {
    $sql = "ALTER TABLE se_albums ADD COLUMN `album_totalfiles` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Populate album_totalfiles
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_media'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) && (!$totalfiles_exists || $plugin_reindex_totals) )
  {
    $sql = "SELECT album_id FROM se_albums WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_albums SET album_totalfiles=(SELECT COUNT(media_id) FROM se_media WHERE media_album_id=album_id) WHERE album_id='{$result['album_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Add album_totalspace
  $sql = "SHOW COLUMNS FROM `se_albums` LIKE 'album_totalspace'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $totalspace_exists = (bool) $database->database_num_rows($resource);
  
  if( !$totalspace_exists )
  {
    $sql = "ALTER TABLE se_albums ADD COLUMN `album_totalspace` BIGINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Populate album_totalspace
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_media'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) && (!$totalspace_exists || $plugin_reindex_totals) )
  {
    $sql = "SELECT album_id FROM se_albums WHERE (SELECT COUNT(media_id) FROM se_media WHERE media_album_id=album_id)>0";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_albums SET album_totalspace=(SELECT SUM(media_filesize) FROM se_media WHERE media_album_id=album_id) WHERE album_id='{$result['album_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on album_title
  $sql = "SHOW FULL COLUMNS FROM `se_albums` LIKE 'album_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_albums MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on album_desc
  $sql = "SHOW FULL COLUMNS FROM `se_albums` LIKE 'album_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_albums MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_media
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_media'")) == 0) {
    $database->database_query("CREATE TABLE `se_media` (
      `media_id`        INT         UNSIGNED  NOT NULL auto_increment,
      `media_album_id`  INT         UNSIGNED  NOT NULL default '0',
      `media_date`      INT(14)               NOT NULL default '0',
      `media_title`     VARCHAR(64)           NOT NULL default '',
      `media_desc`      TEXT                      NULL,
      `media_ext`       VARCHAR(8)            NOT NULL default '',
      `media_filesize`  BIGINT      UNSIGNED  NOT NULL default '0',
      PRIMARY KEY  (`media_id`),
      KEY `INDEX` (`media_album_id`)
    )
    ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }
  
  
  // ADD ORDER COLUMN
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_media` LIKE 'media_order'")) == 0) {
    $database->database_query("
      ALTER TABLE se_media 
      ADD COLUMN `media_order` int(1) NOT NULL default '0'");
    $database->database_query("UPDATE se_media SET media_order=media_id");
  }
  
  
  // Add media_totalcomments
  $sql = "SHOW COLUMNS FROM `se_media` LIKE 'media_totalcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_media ADD COLUMN `media_totalcomments` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT media_id FROM se_media WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_media SET media_totalcomments=(SELECT COUNT(mediacomment_id) FROM se_mediacomments WHERE mediacomment_media_id=media_id) WHERE media_id='{$result['media_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on media_title
  $sql = "SHOW FULL COLUMNS FROM `se_media` LIKE 'media_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_media MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on media_desc
  $sql = "SHOW FULL COLUMNS FROM `se_media` LIKE 'media_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_media MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on media_ext
  $sql = "SHOW FULL COLUMNS FROM `se_media` LIKE 'media_ext'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_media MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_mediacomments
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_mediacomments'")) == 0) {
    $database->database_query("CREATE TABLE `se_mediacomments` (
      `mediacomment_id`             INT     UNSIGNED  NOT NULL auto_increment,
      `mediacomment_media_id`       INT     UNSIGNED  NOT NULL default '0',
      `mediacomment_authoruser_id`  INT     UNSIGNED  NOT NULL default '0',
      `mediacomment_date`           INT(14)           NOT NULL default '0',
      `mediacomment_body`           TEXT              NULL,
      PRIMARY KEY  (`mediacomment_id`),
      KEY `INDEX` (`mediacomment_media_id`,`mediacomment_authoruser_id`)
    )
    ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}");
  }
  
  
  // Ensure utf8 on mediacomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_mediacomments` LIKE 'mediacomment_body'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_mediacomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_mediatags
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_mediatags'")) == 0) {
    $database->database_query("
      CREATE TABLE IF NOT EXISTS `se_mediatags` (
        `mediatag_id`       INT           UNSIGNED  NOT NULL auto_increment,
        `mediatag_media_id` INT           UNSIGNED  NOT NULL default '0',
        `mediatag_user_id`  INT           UNSIGNED  NOT NULL default '0',
        `mediatag_x`        INT                     NOT NULL default '0',
        `mediatag_y`        INT                     NOT NULL default '0',
        `mediatag_height`   SMALLINT      UNSIGNED  NOT NULL default '0',
        `mediatag_width`    SMALLINT      UNSIGNED  NOT NULL default '0',
        `mediatag_text`     VARCHAR(255)            NOT NULL default '',
        PRIMARY KEY  (`mediatag_id`),
        KEY `INDEX` (`mediatag_media_id`,`mediatag_user_id`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }

  // ADD ORDER COLUMN
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_mediatags` LIKE 'mediatag_date'")) == 0) {
    $database->database_query("
      ALTER TABLE se_mediatags
      ADD COLUMN `mediatag_date` int(14) NOT NULL default '0'");
    $database->database_query("UPDATE se_mediatag SET mediatag_date=mediatag_id");
  }
  
  
  // Ensure utf8 on mediatag_text
  $sql = "SHOW FULL COLUMNS FROM `se_mediatags` LIKE 'mediatag_text'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_mediatags MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_albumstyles
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_albumstyles'")) == 0) {
    $database->database_query("CREATE TABLE `se_albumstyles` (
    `albumstyle_id` int(9) NOT NULL auto_increment,
    `albumstyle_user_id` int(9) NOT NULL default '0',
    `albumstyle_css` text NULL,
    PRIMARY KEY  (`albumstyle_id`),
    KEY `INDEX` (`albumstyle_user_id`)
    ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci");
  }
  
  
  // Ensure utf8 on albumstyle_css
  $sql = "SHOW FULL COLUMNS FROM `se_albumstyles` LIKE 'albumstyle_css'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_albumstyles MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### INSERT se_urls
  if($database->database_num_rows($database->database_query("SELECT url_id FROM se_urls WHERE url_file='albums'")) == 0) {
    $database->database_query("INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Album List URL', 'albums', 'albums.php?user=\$user', '\$user/albums/')");
  }
  if($database->database_num_rows($database->database_query("SELECT url_id FROM se_urls WHERE url_file='album'")) == 0) {
    $database->database_query("INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Album URL', 'album', 'album.php?user=\$user&album_id=\$id1', '\$user/albums/\$id1')");
  }
  if($database->database_num_rows($database->database_query("SELECT url_id FROM se_urls WHERE url_file='album_file'")) == 0) {
    $database->database_query("INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Photo URL', 'album_file', 'album_file.php?user=\$user&album_id=\$id1&media_id=\$id2', '\$user/albums/\$id1/\$id2')");
  }
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newalbum'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newalbum', 'album_action_newalbum.gif', '1', '1', '1000178', '1000179', '[username],[displayname],[id],[title]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newmedia'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newmedia', 'album_action_newmedia.gif', '1', '1', '1000180', '1000181', '[username],[displayname],[id],[title]', '1')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='mediacomment'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('mediacomment', 'action_postcomment.gif', '1', '1', '1000182', '1000183', '[username1],[displayname1],[username2],[displayname2],[comment],[mediaid]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newtag'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newtag', 'album_action_newtag.gif', '1', '1', '1000184', '1000185', '[username],[displayname]', '1')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }
  
  
  
  //######### INSERT se_notifytypes
  if( !$database->database_num_rows($database->database_query("SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='mediacomment'")) )
  {
    $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('mediacomment', '1000186', 'action_postcomment.gif', 'album_file.php?user=%1\$s&media_id=%2\$s', '1000187')
    ");
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='newtag'")) )
  {
    $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('newtag', '1000188', 'album_action_newtag.gif', 'profile_photos_file.php?user=%1\$s&type=%2\$s&media_id=%3\$s', '1000189')
    ");
  }
  else
  {
    // -_-
    $database->database_query("UPDATE se_notifytypes SET notifytype_url='profile_photos_file.php?user=%1\$s&type=%2\$s&media_id=%3\$s' WHERE notifytype_name='newtag'");
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='mediatag'")) )
  {
    $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('mediatag', '1000190', 'album_action_newtag.gif', 'album_file.php?user=%1\$s&media_id=%2\$s', '1000191')
    ");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE IF ALBUM HAS NEVER BEEN INSTALLED
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_album_allow'")) == 0) {
    $database->database_query("ALTER TABLE se_levels 
					ADD COLUMN `level_album_allow` int(1) NOT NULL default '1',
					ADD COLUMN `level_album_maxnum` int(3) NOT NULL default '10',
					ADD COLUMN `level_album_exts` text NOT NULL,
					ADD COLUMN `level_album_mimes` text NOT NULL,
					ADD COLUMN `level_album_storage` bigint(11) NOT NULL default '5242880',
					ADD COLUMN `level_album_maxsize` bigint(11) NOT NULL default '2048000',
					ADD COLUMN `level_album_width` varchar(4) NOT NULL default '500',
					ADD COLUMN `level_album_height` varchar(4) NOT NULL default '500',
					ADD COLUMN `level_album_style` int(1) NOT NULL default '1',
					ADD COLUMN `level_album_search` int(1) NOT NULL default '1',
					ADD COLUMN `level_album_privacy` varchar(100) NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}',
					ADD COLUMN `level_album_comments` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
    $database->database_query("UPDATE se_levels SET level_album_exts='jpg,gif,jpeg,png,bmp,mp3,mpeg,avi,mpa,mov,qt,swf', level_album_mimes='image/jpeg,image/pjpeg,image/jpg,image/jpe,image/pjpg,image/x-jpeg,image/x-jpg,image/gif,image/x-gif,image/png,image/x-png,image/bmp,audio/mpeg,video/mpeg,video/x-msvideo,video/avi,video/quicktime,application/x-shockwave-flash'");
  }
  else
  {
    $columns = mysql_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_album_privacy'");
    while($column_info = mysql_fetch_assoc($columns)) {
      $field_name = $column_info['Field'];
      $field_type = $column_info['Type'];
      $field_default = $column_info['Default'];
      if($field_type == "varchar(10)") {
        mysql_query("ALTER TABLE se_levels CHANGE level_album_privacy level_album_privacy varchar(100) NOT NULL default ''");
        mysql_query("UPDATE se_levels SET level_album_privacy='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'");
        mysql_query("UPDATE se_albums SET album_privacy='63' WHERE album_privacy='0'");
        mysql_query("UPDATE se_albums SET album_privacy='31' WHERE album_privacy='1'");
        mysql_query("UPDATE se_albums SET album_privacy='15' WHERE album_privacy='2'");
        mysql_query("UPDATE se_albums SET album_privacy='7' WHERE album_privacy='3'");
        mysql_query("UPDATE se_albums SET album_privacy='3' WHERE album_privacy='4'");
        mysql_query("UPDATE se_albums SET album_privacy='1' WHERE album_privacy='5'");
      }
    }
    $columns = mysql_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_album_comments'");
    while($column_info = mysql_fetch_assoc($columns)) {
      $field_name = $column_info['Field'];
      $field_type = $column_info['Type'];
      $field_default = $column_info['Default'];
      if($field_type == "varchar(10)") {
        mysql_query("ALTER TABLE se_levels CHANGE level_album_comments level_album_comments varchar(100) NOT NULL default ''");
        mysql_query("UPDATE se_levels SET level_album_comments='a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
        mysql_query("UPDATE se_albums SET album_comments='63' WHERE album_comments='0'");
        mysql_query("UPDATE se_albums SET album_comments='31' WHERE album_comments='1'");
        mysql_query("UPDATE se_albums SET album_comments='15' WHERE album_comments='2'");
        mysql_query("UPDATE se_albums SET album_comments='7' WHERE album_comments='3'");
        mysql_query("UPDATE se_albums SET album_comments='3' WHERE album_comments='4'");
        mysql_query("UPDATE se_albums SET album_comments='1' WHERE album_comments='5'");
      }
    }
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_album_profile'")) == 0) {
    $database->database_query("ALTER TABLE se_levels 
					ADD COLUMN `level_album_profile` SET('side', 'tab'),
					ADD COLUMN `level_album_tag` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
    $database->database_query("UPDATE se_levels SET level_album_profile='tab', level_album_tag='a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_album'")) == 0) {
    $database->database_query("ALTER TABLE se_settings 
					ADD COLUMN `setting_permission_album` int(1) NOT NULL default '1'");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  if( !$database->database_num_rows($database->database_query("SELECT systememail_id FROM se_systememails WHERE systememail_name='mediacomment'")) )
  {
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('mediacomment', '1000001', '1000002', '1000192', '1000193', '[displayname],[commenter],[link]')
    ");
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT systememail_id FROM se_systememails WHERE systememail_name='newtag'")) )
  {
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('newtag', '1000151', '1000152', '1000194', '1000195', '[displayname],[link]')
    ");
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT systememail_id FROM se_systememails WHERE systememail_name='mediatag'")) )
  {
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('mediatag', '1000153', '1000154', '1000196', '1000197', '[displayname],[tagger],[link]')
    ");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_mediacomment'")) == 0) {
    $database->database_query("ALTER TABLE se_usersettings 
					ADD COLUMN `usersetting_notify_mediacomment` int(1) NOT NULL default '1'");
  }
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_newtag'")) == 0) {
    $database->database_query("ALTER TABLE se_usersettings 
					ADD COLUMN `usersetting_notify_newtag` int(1) NOT NULL default '1',
					ADD COLUMN `usersetting_notify_mediatag` int(1) NOT NULL default '1'");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO USER TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_users` LIKE 'user_profile_album'")) == 0) {
    $database->database_query("ALTER TABLE se_users 
					ADD COLUMN `user_profile_album` ENUM('tab', 'side') NOT NULL default 'tab'");
  }
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  if( !$database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1000001 LIMIT 1")) )
  {
    $database->database_query("
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1000001, 1, 'New Media Comment Email', ''),
        (1000002, 1, 'This is the email that gets sent to a user when a new comment is posted on one of their photos.', ''),
        (1000003, 1, 'Album Plugin Settings', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
        (1000004, 1, 'View Photo Albums', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
        (1000005, 1, 'Global Album Settings', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
        (1000006, 1, 'Album Settings', 'admin_viewusers_edit, admin_viewusers, admin_viewreports, admin_viewplugins, admin_viewadmins, admin_url, admin_templates, admin_subnetworks, admin_stats, admin_signup, admin_profile, admin_lostpass_reset, admin_lostpass, admin_login, admin_log, admin_levels_usersettings, admin_levels_messagesettings, admin_levels_edit, admin_levels_albumsettings, admin_levels, admin_language_edit, admin_language, admin_invite, admin_home, admin_general, admin_fields, admin_faq, admin_emails, admin_connections, admin_banning, admin_announcements, admin_ads_modify, admin_ads, admin_activity, '),
        (1000007, 1, 'Photos', 'header, ')
    ") or die("Insert Into se_languagevars: ".mysql_error());
  }

  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  if( !$database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1000008 LIMIT 1")) )
  {
    $database->database_query("
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1000008, 1, 'This page contains general album settings that affect your entire social network.', ''),
        (1000009, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\'admin_general.php\'>General Settings</a> page.', ''),
        (1000010, 1, 'Yes, the public can view albums unless they are made private.', ''),
        (1000011, 1, 'No, the public cannot view albums.', ''),
        (1000012, 1, 'Your maximum filesize field must contain an integer between 1 and 204800.', ''),
        (1000013, 1, 'Your maximum width and height fields must contain integers between 1 and 9999.', ''),
        (1000014, 1, 'Your maximum allowed albums field must contain an integer between 1 and 999.', ''),
        (1000015, 1, 'If you have allowed users to have file albums, you can adjust their details from this page.', ''),
        (1000016, 1, 'Allow Albums?', ''),
        (1000017, 1, 'Do you want to let users have albums? If set to no, all other settings on this page will not apply.', ''),
        (1000018, 1, 'Yes, allow albums.', ''),
        (1000019, 1, 'No, do not allow albums.', ''),
        (1000020, 1, 'Album Privacy Options', ''),
        (1000021, 1, 'If you enable this feature, users will be able to exclude their albums from search results. Otherwise, all albums will be included in search results.', ''),
        (1000022, 1, 'Yes, allow users to exclude their albums from search results.', ''),
        (1000023, 1, 'No, force all albums to be included in search results.', ''),
        (1000024, 1, 'Album Privacy Options', ''),
        (1000025, 1, 'Your users can choose from any of the options checked below when they decide who can see their albums. If you do not check any options, everyone will be allowed to view albums.', ''),
        (1000026, 1, 'Media Comment Options', ''),
        (1000027, 1, 'Your users can choose from any of the options checked below when they decide who can post comments on their media. If you do not check any options, everyone will be allowed to post comments on media.', ''),
        (1000028, 1, 'Maximum Allowed Albums', ''),
        (1000029, 1, 'Enter the maximum number of allowed albums. The field must contain an integer between 1 and 999.', ''),
        (1000030, 1, 'allowed albums', ''),
        (1000031, 1, 'Allowed File Types', ''),
        (1000032, 1, 'List the following file extensions that users are allowed to upload. Separate file extensions with commas, for example: jpg, gif, jpeg, png, bmp', ''),
        (1000033, 1, 'Allowed MIME Types', ''),
        (1000034, 1, 'To successfully upload a file, the file must have an allowed filetype extension as well as an allowed MIME type. This prevents users from disguising malicious files with a fake extension. Separate MIME types with commas, for example: image/jpeg, image/gif, image/png, image/bmp', ''),
        (1000035, 1, 'Allowed Storage Space', ''),
        (1000036, 1, 'How much storage space should each user have to store their files?', ''),
        (1000037, 1, 'unlimited', ''),
        (1000038, 1, 'Maximum Filesize', ''),
        (1000039, 1, 'Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 204800.', ''),
        (1000040, 1, 'Enter the maximum width and height (in pixels) for images uploaded to albums. Images with dimensions outside this range will be sized down accordingly if your server has the GD Libraries installed. Note that unusual image types like TIFF, RAW, and others may not be resized.', ''),
        (1000041, 1, 'Allow Custom CSS Styles?', ''),
        (1000042, 1, 'If you enable this feature, your users will be able to customize the colors and fonts of their albums by altering their CSS styles.', ''),
        (1000043, 1, 'Yes, enable custom CSS styles.', ''),
        (1000044, 1, 'No, disable custom CSS styles.', ''),
        (1000045, 1, 'This page lists all of the file albums that users have created on your social network. Depending on the settings you have specified in this control panel, users can create albums and use them to upload, organize, and share photos, music, videos, and other files. You can use this page to monitor these albums and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific albums. Leaving the filter fields blank will show all the albums on your social network.', ''),
        (1000046, 1, 'Title', ''),
        (1000047, 1, 'Owner', ''),
        (1000048, 1, 'No albums were found.', ''),
        (1000049, 1, '%1\$d Albums Found', ''),
        (1000050, 1, 'Files', ''),
        (1000051, 1, 'Space Used', ''),
        (1000052, 1, 'view', ''),
        (1000053, 1, 'Are you sure you want to delete this album? Warning: All images within this album will also be deleted.', ''),
        (1000054, 1, 'Delete Album?', ''),
        (1000055, 1, 'My Albums', ''),
        (1000056, 1, 'Album Settings', ''),
        (1000057, 1, 'You have %1\$d albums and %2\$d photos.', ''),
        (1000058, 1, 'You have %1\$s MB of free space remaining.', ''),
        (1000059, 1, 'Create New Album', ''),
        (1000060, 1, 'My Albums Link:', ''),
        (1000061, 1, 'Created:', ''),
        (1000062, 1, 'Last Update:', ''),
        (1000063, 1, 'Files:', ''),
        (1000064, 1, '%1\$s photos (%2\$s MB)', ''),
        (1000065, 1, 'Views:', ''),
        (1000066, 1, '%1\$d views', ''),
        (1000067, 1, 'Viewable By:', ''),
        (1000068, 1, 'View Album', ''),
        (1000069, 1, 'Edit Album', ''),
        (1000070, 1, 'Delete Album', ''),
        (1000071, 1, 'You do not have any albums.', ''),
        (1000072, 1, 'Create an album to begin uploading files.', ''),
        (1000073, 1, 'Please enter a name for this album.', ''),
        (1000074, 1, 'Please give us some information about your new album.', ''),
        (1000075, 1, 'You have reached the maximum number of allowed albums (%1\$d). You must delete some of your old albums before you can create a new one.', ''),
        (1000076, 1, 'Return to My Albums', ''),
        (1000077, 1, 'Album Name:', ''),
        (1000078, 1, 'Album Description:', ''),
        (1000079, 1, 'Include this album in search/browse results?', ''),
        (1000080, 1, 'Yes, include this album in search/browse results.', ''),
        (1000081, 1, 'No, exclude this album from search/browse results.', ''),
        (1000082, 1, 'Who can see this album?', ''),
        (1000083, 1, 'Who can comment in this album?', ''),
        (1000084, 1, 'Create Album', ''),
        (1000085, 1, 'You do not have enough free space to upload %1\$s.', ''),
        (1000086, 1, '%1\$s was uploaded successfully.', ''),
        (1000087, 1, 'Upload Photos:', ''),
        (1000088, 1, 'To upload photos from your computer, click the \"Browse\" button, locate them on your computer, then click the \"Upload\" button.', ''),
        (1000089, 1, 'Back to Photos', ''),
        (1000090, 1, 'You may upload files of the following types: %1\$s', ''),
        (1000091, 1, 'You may upload files with sizes up to %1\$s KB.', ''),
        (1000092, 1, 'Your album has been created. You can begin uploading photos below.', ''),
        (1000093, 1, 'Edit Album Details', ''),
        (1000094, 1, 'Use this page to change the album name, description, or privacy level.', ''),
        (1000095, 1, 'Edit Photos:', ''),
        (1000096, 1, 'All photos inside this album are listed below.<br>This album contains <b>%1\$s files</b> and has been viewed <b>%2\$s times</b>.', ''),
        (1000097, 1, 'Back to Albums', ''),
        (1000098, 1, 'Add New Photos', ''),
        (1000099, 1, 'Edit Album Info', ''),
        (1000100, 1, 'There are no files in this album.', ''),
        (1000101, 1, 'Click here to begin adding files.', ''),
        (1000102, 1, 'Caption', ''),
        (1000103, 1, 'Delete Photo', ''),
        (1000104, 1, 'Album Cover', ''),
        (1000105, 1, 'Move To:', ''),
        (1000106, 1, 'Profile Position', ''),
        (1000107, 1, 'Your users can choose from any of the options checked below when they decide where they want their albums to appear on their profile. ', ''),
        (1000108, 1, 'Display Albums in Tab', ''),
        (1000109, 1, 'Display Albums in Side Gutter', ''),
        (1000110, 1, 'Where do you want your albums to display on your profile?', ''),
        (1000111, 1, 'Edit album settings such as your album\'s style.', ''),
        (1000112, 1, 'My Albums\' Style', ''),
        (1000113, 1, 'You can change the colors, fonts, and styles of your albums by adding CSS code below. The contents of the text area below will be output between &lt;style&gt; tags on your album.', ''),
        (1000114, 1, 'Move Up', ''),
        (1000115, 1, 'Rotate Left', ''),
        (1000116, 1, 'Rotate Right', ''),
        (1000118, 1, '%1\$d albums/photos', ''),
        (1000119, 1, 'Photo: %1\$s', ''),
        (1000120, 1, 'Album: %1\$s', ''),
        (1000121, 1, 'Media posted by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', ''),
        (1000122, 1, 'Album created by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', ''),
        (1000123, 1, 'Albums', ''),
        (1000124, 1, 'updated %1\$s by <a href=\'%2\$s\'>%3\$s</a>', ''),
        (1000125, 1, 'You do not have permission to view this file.', ''),
        (1000126, 1, 'Uploaded', ''),
        (1000127, 1, 'Browse Photo Albums', ''),
        (1000128, 1, 'View:', ''),
        (1000129, 1, 'Everyone\'s Albums', ''),
        (1000130, 1, 'My Friend\'s Albums', ''),
        (1000131, 1, 'Show:', ''),
        (1000132, 1, 'Recently Updated', ''),
        (1000133, 1, 'Recently Created', ''),
        (1000134, 1, 'Photo Tagging Options', ''),
        (1000135, 1, 'Your users can choose from any of the options checked below when they decide who can tag their photos. If you do not check any options, everyone will be allowed to tag photos.', ''),
        (1000136, 1, 'Who can tag photos in this album?', ''),
        (1000137, 1, 'Photos of %1\$s (%2\$d)', ''),
        (1000138, 1, '<a href=\'%1\$s\'>%2\$s</a>\'s albums', ''),
        (1000139, 1, '%1\$s does not have any albums.', ''),
        (1000140, 1, 'Updated %1\$s', ''),
        (1000141, 1, '<a href=\'%1\$s\'>%2\$s</a>\'s <a href=\'%3\$s\'>albums</a>', ''),
        (1000142, 1, 'download audio', ''),
        (1000143, 1, 'download video', ''),
        (1000144, 1, 'download this file', ''),
        (1000145, 1, 'Viewing #%1\$d of %2\$d in <a href=\'%3\$s\'>%4\$s</a>', ''),
        (1000146, 1, 'Last', ''),
        (1000147, 1, 'Next', ''),
        (1000148, 1, 'Report Inappropriate Content', ''),
        (1000149, 1, 'Photos of <a href=\'%1\$s\'>%2\$s</a>', ''),
        (1000150, 1, 'Viewing #%1\$d of %2\$d <a href=\'%3\$s\'>photos of %4\$s</a> &nbsp;|&nbsp; <a href=\'%5\$s\'>Return to %4\$s\'s Profile</a>', ''),
        (1000151, 1, 'Notification of Being Tagged', ''),
        (1000152, 1, 'This is the email that gets sent to a user when someone tags them in a photo.', ''),
        (1000153, 1, 'New Photo Tag Email', ''),
        (1000154, 1, 'This is the email that gets sent to a user when someone tags one of their photos.', ''),
        (1000155, 1, '%1\$s\'s album: %2\$s', ''),
        (1000156, 1, '%1\$s', ''),
        (1000157, 1, 'From %1\$s by <a href=\'%2\$s\'>%3\$s</a>', ''),
        (1000158, 1, '%1\$s\'s photo - %2\$s', ''),
        (1000159, 1, '%1\$s', ''),
        (1000160, 1, '%1\$s\'s albums', ''),
        (1000161, 1, '%1\$s\'s albums', ''),
        (1000162, 1, 'In this photo: ', ''),
        (1000163, 1, 'Add Tag', ''),
        (1000164, 1, 'Share This', ''),
        (1000165, 1, 'To share this photo or embed it on another web page, please copy and paste the code of your choosing:', ''),
        (1000166, 1, 'Direct Link', ''),
        (1000167, 1, 'HTML - Embedded Image', ''),
        (1000168, 1, 'HTML - Text Link', ''),
        (1000169, 1, 'UBB Code (for forums)', ''),
        (1000170, 1, 'Close Window', ''),
        (1000171, 1, 'Photo Albums', ''),
        (1000172, 1, '%1\$d photos', ''),
        (1000173, 1, 'remove tag', '')
    ") or die("Insert Into se_languagevars: ".mysql_error());
  }

  if( !$database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1000174 LIMIT 1")) )
  {
    $database->database_query("
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1000174, 1, 'Albums: %1\$d albums', ''),
        (1000175, 1, 'Media: %1\$d media', ''),
        (1000176, 1, 'Media Comments: %1\$d media comments', ''),
        (1000177, 1, 'Media Tags: %1\$d media tags', '')
    ") or die("Insert Into se_languagevars: ".mysql_error());
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1000178 LIMIT 1")) )
  {
    $database->database_query("
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1000178, 1, 'Creating an Album', 'actiontypes'),
        (1000179, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> created a new album: <a href=\"album.php?user=%1\$s&album_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (1000180, 1, 'Uploading Photos to an Album', 'actiontypes'),
        (1000181, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> uploaded new photos to their album: <a href=\"album.php?user=%1\$s&album_id=%3\$s\">%4\$s</a><div class=\"recentaction_div_media\">[media]</div>', 'actiontypes'),
        (1000182, 1, 'Posting a Photo Comment', 'actiontypes'),
        (1000183, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on <a href=\"profile.php?user=%3\$s\">%4\$s</a>\'s <a href=\"album_file.php?user=%3\$s&media_id=%6\$s\">photo</a>:<div class=\"recentaction_div\">%5\$s</div>', 'actiontypes'),
        (1000184, 1, 'Getting Tagged in a Photo', 'actions'),
        (1000185, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> was tagged in these photos:<div class=\"recentaction_div_media\">[media]</div>', 'actions'),
        (1000186, 1, '%1\$d New Photo Comment(s): %2\$s', 'notifytypes'),
        (1000187, 1, 'When I receive a new photo comment.', 'notifytypes'),
        (1000188, 1, '%1\$d New Photo(s) Tagged of You: %2\$s', 'notifytypes'),
        (1000189, 1, 'When I am tagged in a photo.', 'notifytypes'),
        (1000190, 1, '%1\$d New Tag(s) on Your Photo: %2\$s', 'notifytypes'),
        (1000191, 1, 'When someone tags a photo I own.', 'notifytypes'),
        (1000192, 1, 'New Media Comment', 'systememails'),
        (1000193, 1, 'Hello %1\$s,\n\nA new comment has been posted on one of your photos by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (1000194, 1, 'You have Been Tagged in a Photo!', 'systememails'),
        (1000195, 1, 'Hello %1\$s,\n\nYou have been tagged in a photo. Please click the following link to view it:\n\n%2\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (1000196, 1, 'New Photo Tag', 'systememails'),
        (1000197, 1, 'Hello %1\$s,\n\nA new tag has been posted on one of your photos by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails')
    ") or die("Insert Into se_languagevars: ".mysql_error());
  }
}  

?>