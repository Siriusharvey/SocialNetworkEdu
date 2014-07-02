<?php

/* $Id: install_event.php 243 2009-11-14 02:58:23Z phil $ */

$plugin_name = "Event Plugin";
$plugin_version = "3.06";
$plugin_type = "event";
$plugin_desc = "This plugin lets your users create events. Users can create private or public events, invite their friends, RSVP, browse each other's calendars, and much more. Events encourage users to expand their personal networks by establishing new connections.";
$plugin_icon = "event_event16.gif";
$plugin_menu_title = "3000001";
$plugin_pages_main = "3000002<!>event_event16.gif<!>admin_viewevents.php<~!~>3000003<!>event_settings16.gif<!>admin_event.php<~!~>";
$plugin_pages_level = "3000004<!>admin_levels_eventsettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*/)?event/([0-9]+)/([^/]*)\$ \$server_info/event.php?event_id=\$1\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*/)?event/([0-9]+)/album/([0-9]+)/([^/]*)\$ \$server_info/event_album_file.php?event_id=\$2&eventmedia_id=\$3\$4 [L]
";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;




if( $install=="event" )
{
  //######### GET CURRENT PLUGIN INFORMATION
  $sql = "SELECT * FROM se_plugins WHERE plugin_type='{$plugin_type}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  $plugin_info = array();
  if( $database->database_num_rows($resource) )
    $plugin_info = $database->database_fetch_assoc($resource);
  
  // Uncomment this line if you already upgraded to v3, but are having issues with everything being private
  //$plugin_info['plugin_version'] = '2.00';
  
  
  
  
  //######### INSERT ROW INTO se_plugins
  $sql = "SELECT NULL FROM se_plugins WHERE plugin_type='$plugin_type'";
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



  //######### CREATE se_eventalbums
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventalbums'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventalbums`
      (
        `eventalbum_id`           INT         UNSIGNED  NOT NULL auto_increment,
        `eventalbum_event_id`     INT         UNSIGNED  NOT NULL default 0,
        `eventalbum_datecreated`  INT         UNSIGNED  NOT NULL default 0,
        `eventalbum_dateupdated`  INT         UNSIGNED  NOT NULL default 0,
        `eventalbum_title`        VARCHAR(64)           NOT NULL default '',
        `eventalbum_desc`         TEXT                      NULL,
        `eventalbum_search`       TINYINT     UNSIGNED  NOT NULL default 0,
        `eventalbum_privacy`      TINYINT     UNSIGNED  NOT NULL default 0,
        `eventalbum_comments`     TINYINT     UNSIGNED  NOT NULL default 0,
        `eventalbum_cover`        INT         UNSIGNED  NOT NULL default 0,
        `eventalbum_views`        INT         UNSIGNED  NOT NULL default 0,
        `eventalbum_tag`          TINYINT     UNSIGNED  NOT NULL default 0,
        `eventalbum_totalfiles`   SMALLINT    UNSIGNED  NOT NULL default 0,
        `eventalbum_totalspace`   BIGINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`eventalbum_id`),
        KEY `INDEX` (`eventalbum_event_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add column eventalbum_tag
  $sql = "SHOW COLUMNS FROM `se_eventalbums` LIKE 'eventalbum_tag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_eventalbums ADD COLUMN `eventalbum_tag` TINYINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add eventalbum_totalfiles
  $sql = "SHOW COLUMNS FROM `se_eventalbums` LIKE 'eventalbum_totalfiles'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $totalfiles_exists = (bool) $database->database_num_rows($resource);
  
  if( !$totalfiles_exists )
  {
    $sql = "ALTER TABLE se_eventalbums ADD COLUMN `eventalbum_totalfiles` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Populate eventalbum_totalfiles
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventmedia'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) && (!$totalfiles_exists || $plugin_reindex_totals) )
  {
    $sql = "SELECT eventalbum_id FROM se_eventalbums WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_eventalbums SET eventalbum_totalfiles=(SELECT COUNT(eventmedia_id) FROM se_eventmedia WHERE eventmedia_eventalbum_id=eventalbum_id) WHERE eventalbum_id='{$result['eventalbum_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Add eventalbum_totalspace
  $sql = "SHOW COLUMNS FROM `se_eventalbums` LIKE 'eventalbum_totalspace'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $totalspace_exists = (bool) $database->database_num_rows($resource);
  
  if( !$totalspace_exists )
  {
    $sql = "ALTER TABLE se_eventalbums ADD COLUMN `eventalbum_totalspace` BIGINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Populate eventalbum_totalspace
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventmedia'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) && (!$totalspace_exists || $plugin_reindex_totals) )
  {
    $sql = "SELECT eventalbum_id FROM se_eventalbums WHERE (SELECT COUNT(eventmedia_id) FROM se_eventmedia WHERE eventmedia_eventalbum_id=eventalbum_id)>0";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_eventalbums SET eventalbum_totalspace=(SELECT SUM(eventmedia_filesize) FROM se_eventmedia WHERE eventmedia_eventalbum_id=eventalbum_id) WHERE eventalbum_id='{$result['eventalbum_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on eventalbum_title
  $sql = "SHOW FULL COLUMNS FROM `se_eventalbums` LIKE 'eventalbum_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventalbums MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on eventalbum_desc
  $sql = "SHOW FULL COLUMNS FROM `se_eventalbums` LIKE 'eventalbum_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventalbums MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_eventcats
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventcats'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventcats`
      (
        `eventcat_id`         INT           UNSIGNED  NOT NULL auto_increment,
        `eventcat_dependency` INT           UNSIGNED  NOT NULL default 0,
        `eventcat_title`      VARCHAR(128)                NULL,
        PRIMARY KEY  (`eventcat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### ALTER se_eventcats LANGUAGIFY eventcat_title
  $sql = "SHOW FULL COLUMNS FROM `se_eventcats` LIKE 'eventcat_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  // Fix collation, load data, drop column
  $eventcat_info = array();
  if( $column_info && strtoupper(substr($column_info['Type'], 0, 7))=="VARCHAR" )
  {
    // Fix collation
    if( $column_info['Collation']!=$plugin_db_collation )
    {
      $sql = "ALTER TABLE se_eventcats MODIFY {$column_info['Field']} {$column_info['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    // Languagify title column
    $sql = "SELECT * FROM se_eventcats";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    if( $database->database_num_rows($resource) )
      while( $result=$database->database_fetch_assoc($resource) )
        $eventcat_info[] = $result;
    
    // Drop column
    $sql = "ALTER TABLE se_eventcats DROP COLUMN eventcat_title";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    unset($column_info);
  }
  
  // Add column
  if( !$column_info )
  {
    $sql = "ALTER TABLE se_eventcats ADD COLUMN eventcat_title INT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Update column
  if( !empty($eventcat_info) )
  {
    // Update title
    foreach( $eventcat_info as $eventcat_info_array )
    {
      $eventcat_title_lvid = SE_Language::edit(0, $eventcat_info_array['eventcat_title'], NULL, LANGUAGE_INDEX_FIELDS);
      
      $sql = "
        UPDATE
          se_eventcats
        SET
          eventcat_title='{$eventcat_title_lvid}'
        WHERE
          eventcat_id='{$eventcat_info_array['eventcat_id']}'
        LIMIT
          1
      ";
      
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  
  //######### ALTER se_eventcats ADD COLUMNS
  $sql = "SHOW COLUMNS FROM `se_eventcats` LIKE 'eventcat_order'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_eventcats
      ADD COLUMN eventcat_order  SMALLINT  UNSIGNED  NOT NULL default 0,
      ADD COLUMN eventcat_signup TINYINT   UNSIGNED  NOT NULL default 0
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Insert default category
  $sql = "SELECT NULL FROM se_eventcats";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $eventcat_title  = SE_Language::edit(0, "Default", NULL, LANGUAGE_INDEX_FIELDS);
    $sql = "INSERT INTO se_eventcats (eventcat_title, eventcat_dependency, eventcat_order, eventcat_signup) VALUES ('$eventcat_title', 0, 1, 0)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }




  //######### CREATE se_eventcomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventcomments` (
        `eventcomment_id`             INT         UNSIGNED  NOT NULL auto_increment,
        `eventcomment_event_id`       INT         UNSIGNED  NOT NULL default 0,
        `eventcomment_authoruser_id`  INT         UNSIGNED  NOT NULL default 0,
        `eventcomment_date`           INT         UNSIGNED  NOT NULL default 0,
        `eventcomment_body`           TEXT                      NULL,
        PRIMARY KEY  (`eventcomment_id`),
        KEY `INDEX` (`eventcomment_event_id`,`eventcomment_authoruser_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on eventcomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_eventcomments` LIKE 'eventcomment_body'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventcomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_eventfields
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventfields'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventfields`
      (
        `eventfield_id`                INT           UNSIGNED  NOT NULL auto_increment,
        `eventfield_eventcat_id`       INT           UNSIGNED  NOT NULL default 0,
        `eventfield_order`             SMALLINT      UNSIGNED  NOT NULL default 0,
        `eventfield_dependency`        INT           UNSIGNED  NOT NULL default 0,
        `eventfield_title`             INT           UNSIGNED  NOT NULL default 0,
        `eventfield_desc`              INT           UNSIGNED  NOT NULL default 0,
        `eventfield_error`             INT           UNSIGNED  NOT NULL default 0,
        `eventfield_type`              TINYINT       UNSIGNED  NOT NULL default 0,
        `eventfield_style`             VARCHAR(255)                NULL,
        `eventfield_maxlength`         SMALLINT      UNSIGNED  NOT NULL default 0,
        `eventfield_link`              VARCHAR(255)                NULL,
        `eventfield_options`           LONGTEXT                    NULL,
        `eventfield_required`          TINYINT       UNSIGNED  NOT NULL default 0,
        `eventfield_regex`             VARCHAR(255)                NULL,
        `eventfield_html`              VARCHAR(255)                NULL,
        `eventfield_search`            TINYINT       UNSIGNED  NOT NULL default 0,
        `eventfield_signup`            TINYINT       UNSIGNED  NOT NULL default 0,
        `eventfield_display`           TINYINT       UNSIGNED  NOT NULL default 0,
        `eventfield_special`           TINYINT       UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`eventfield_id`),
        KEY `INDEX` (`eventfield_eventcat_id`,`eventfield_dependency`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_eventmedia
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventmedia'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventmedia`
      (
        `eventmedia_id`             INT           UNSIGNED  NOT NULL auto_increment,
        `eventmedia_eventalbum_id`  INT           UNSIGNED  NOT NULL default 0,
        `eventmedia_user_id`        INT           UNSIGNED  NOT NULL default 0,
        `eventmedia_date`           INT           UNSIGNED  NOT NULL default 0,
        `eventmedia_title`          VARCHAR(50)                 NULL,
        `eventmedia_desc`           TEXT                        NULL,
        `eventmedia_ext`            VARCHAR(8)                  NULL,
        `eventmedia_filesize`       INT           UNSIGNED  NOT NULL default 0,
        `eventmedia_totalcomments`  SMALLINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`eventmedia_id`),
        KEY `INDEX` (`eventmedia_eventalbum_id`),
        KEY `USER` (`eventmedia_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add 
  $sql = "SHOW COLUMNS FROM `se_eventmedia` LIKE 'eventmedia_user_id'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_eventmedia ADD COLUMN `eventmedia_user_id` INT UNSIGNED  NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "ALTER TABLE se_eventmedia ADD INDEX `USER` (`eventmedia_user_id`)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add eventmedia_totalcomments
  $sql = "SHOW COLUMNS FROM `se_eventmedia` LIKE 'eventmedia_totalcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_eventmedia ADD COLUMN `eventmedia_totalcomments` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT eventmedia_id FROM se_eventmedia WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_eventmedia SET eventmedia_totalcomments=(SELECT COUNT(eventmediacomment_id) FROM se_eventmediacomments WHERE eventmediacomment_eventmedia_id=eventmedia_id) WHERE eventmedia_id='{$result['eventmedia_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on eventmedia_desc
  $sql = "SHOW FULL COLUMNS FROM `se_eventmedia` LIKE 'eventmedia_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventmedia MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on eventmedia_title
  $sql = "SHOW FULL COLUMNS FROM `se_eventmedia` LIKE 'eventmedia_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventmedia MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on eventmedia_ext
  $sql = "SHOW FULL COLUMNS FROM `se_eventmedia` LIKE 'eventmedia_ext'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventmedia MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_eventmediatags
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventmediatags'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventmediatags` (
        `eventmediatag_id`            INT           UNSIGNED  NOT NULL auto_increment,
        `eventmediatag_eventmedia_id` INT           UNSIGNED  NOT NULL default 0,
        `eventmediatag_user_id`       INT           UNSIGNED  NOT NULL default 0,
        `eventmediatag_x`             INT           UNSIGNED  NOT NULL default 0,
        `eventmediatag_y`             INT           UNSIGNED  NOT NULL default 0,
        `eventmediatag_height`        INT           UNSIGNED  NOT NULL default 0,
        `eventmediatag_width`         INT           UNSIGNED  NOT NULL default 0,
        `eventmediatag_text`          VARCHAR(255)            NOT NULL default '',
        `eventmediatag_date`          BIGINT                  NOT NULL default 0,
        PRIMARY KEY  (`eventmediatag_id`),
        KEY `INDEX` (`eventmediatag_eventmedia_id`,`eventmediatag_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_eventmediacomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventmediacomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventmediacomments`
      (
        `eventmediacomment_id`            INT   UNSIGNED  NOT NULL auto_increment,
        `eventmediacomment_eventmedia_id` INT   UNSIGNED  NOT NULL default 0,
        `eventmediacomment_authoruser_id` INT   UNSIGNED  NOT NULL default 0,
        `eventmediacomment_date`          INT   UNSIGNED  NOT NULL default 0,
        `eventmediacomment_body`          TEXT                NULL,
        PRIMARY KEY  (`eventmediacomment_id`),
        KEY `INDEX` (`eventmediacomment_eventmedia_id`,`eventmediacomment_authoruser_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on eventmediacomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_eventmediacomments` LIKE 'eventmediacomment_body'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventmediacomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }




  //######### CREATE se_eventmembers
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventmembers'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventmembers`
      (
        `eventmember_id`        INT         UNSIGNED  NOT NULL auto_increment,
        `eventmember_user_id`   INT         UNSIGNED  NOT NULL default 0,
        `eventmember_event_id`  INT         UNSIGNED  NOT NULL default 0,
        `eventmember_status`    TINYINT     UNSIGNED  NOT NULL default 0,
        `eventmember_approved`  TINYINT     UNSIGNED  NOT NULL default 0,
        `eventmember_rank`      TINYINT     UNSIGNED  NOT NULL default 0,
        `eventmember_title`     VARCHAR(64)           NOT NULL default '',
        `eventmember_rsvp`      TINYINT     UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`eventmember_id`),
        KEY `INDEX` (`eventmember_user_id`,`eventmember_event_id`),
        KEY `STATUS` (`eventmember_status`,`eventmember_approved`, `eventmember_rsvp`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add columns
  $sql = "SHOW COLUMNS FROM `se_eventmembers` LIKE 'eventmember_status'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $eventMembersHasStatus = (bool) $database->database_num_rows($resource);
  
  $sql = "SHOW COLUMNS FROM `se_eventmembers` LIKE 'eventmember_rsvp'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $eventMembersHasRSVP = (bool) $database->database_num_rows($resource);
  
  $sql = "SHOW COLUMNS FROM `se_eventmembers` LIKE 'eventmember_approved'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $eventMembersHasApproved = (bool) $database->database_num_rows($resource);
  
  if( !$eventMembersHasApproved )
  {
    $sql = "
      ALTER TABLE se_eventmembers
      ADD COLUMN `eventmember_approved`  TINYINT     UNSIGNED  NOT NULL default 0,
      ADD COLUMN `eventmember_rank`      TINYINT     UNSIGNED  NOT NULL default 0,
      ADD COLUMN `eventmember_title`     VARCHAR(64)           NOT NULL default ''
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( $eventMembersHasStatus && !$eventMembersHasRSVP )
  {
    $sql = "ALTER TABLE se_eventmembers CHANGE COLUMN `eventmember_status` eventmember_rsvp TINYINT     UNSIGNED  NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "ALTER TABLE se_eventmembers ADD COLUMN `eventmember_status`    TINYINT     UNSIGNED  NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_eventmembers SET eventmember_approved=1, eventmember_status=1 WHERE eventmember_rsvp!=-1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_eventmembers SET eventmember_approved=0, eventmember_status=1, eventmember_rsvp=0 WHERE eventmember_rsvp=-1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "
      UPDATE
        se_eventmembers
      LEFT JOIN
        se_events
        ON se_events.event_id=se_eventmembers.eventmember_event_id
      SET
        se_eventmembers.eventmember_rank=IF(se_eventmembers.eventmember_user_id=se_events.event_user_id, 3, IF(se_eventmembers.eventmember_approved!=0, 1, 0))
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $eventMembersHasRSVP = TRUE;
  }
  
  if( !$eventMembersHasStatus )
  {
    $sql = "ALTER TABLE se_eventmembers ADD COLUMN `eventmember_status`    TINYINT     UNSIGNED  NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_eventmembers SET eventmember_status=1 WHERE 1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $eventMembersHasStatus = TRUE;
  }
  
  if( !$eventMembersHasRSVP )
  {
    $sql = "ALTER TABLE se_eventmembers ADD COLUMN `eventmember_rsvp`    TINYINT     UNSIGNED  NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $eventMembersHasRSVP = TRUE;
  }
  
  
  // Ensure utf8 on eventmember_title
  $sql = "SHOW FULL COLUMNS FROM `se_eventmembers` LIKE 'eventmember_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventmembers MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }




  //######### CREATE se_events
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_events'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_events`
      (
        `event_id`            INT           UNSIGNED  NOT NULL auto_increment,
        `event_user_id`       INT           UNSIGNED  NOT NULL default 0,
        `event_eventcat_id`   INT           UNSIGNED  NOT NULL default 0,
        `event_datecreated`   INT           UNSIGNED  NOT NULL default 0,
        `event_dateupdated`   INT           UNSIGNED  NOT NULL default 0,
        `event_views`         INT           UNSIGNED  NOT NULL default 0,
        `event_title`         VARCHAR(128)                NULL,
        `event_desc`          TEXT                        NULL,
        `event_date_start`    BIGINT        UNSIGNED  NOT NULL default 0,
        `event_date_end`      BIGINT        UNSIGNED  NOT NULL default 0,
        `event_host`          VARCHAR(255)                NULL,
        `event_location`      TEXT                        NULL,
        `event_photo`         VARCHAR(16)                 NULL,
        `event_search`        TINYINT       UNSIGNED  NOT NULL default 0,
        `event_privacy`       TINYINT       UNSIGNED  NOT NULL default 0,
        `event_comments`      TINYINT       UNSIGNED  NOT NULL default 0,
        `event_inviteonly`    TINYINT       UNSIGNED  NOT NULL default 0,
        `event_upload`        TINYINT       UNSIGNED  NOT NULL default 0,
        `event_tag`           TINYINT       UNSIGNED  NOT NULL default 0,
        `event_invite`        TINYINT       UNSIGNED  NOT NULL default 0,
        `event_totalcomments` SMALLINT      UNSIGNED  NOT NULL default 0,
        `event_totalmembers`  SMALLINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`event_id`),
        KEY `INDEX` (`event_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `se_events` LIKE 'event_datecreated'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_events
      ADD COLUMN `event_datecreated`  INT           UNSIGNED  NOT NULL default 0
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `se_events` LIKE 'event_upload'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_events
      ADD COLUMN `event_upload`       TINYINT       UNSIGNED  NOT NULL default 0,
      ADD COLUMN `event_tag`          TINYINT       UNSIGNED  NOT NULL default 0,
      ADD COLUMN `event_invite`       TINYINT       UNSIGNED  NOT NULL default 0
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add event_title_cleaned
  $sql = "SHOW COLUMNS FROM `se_events` LIKE 'event_title_cleaned'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_events ADD COLUMN `event_title_cleaned` VARCHAR(128) NULL";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add event_totalcomments
  $sql = "SHOW COLUMNS FROM `se_events` LIKE 'event_totalcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_events ADD COLUMN `event_totalcomments` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT event_id FROM se_events WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_events SET event_totalcomments=(SELECT COUNT(eventcomment_id) FROM se_eventcomments WHERE eventcomment_event_id='{$result['event_id']}') WHERE event_id='{$result['event_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Add event_totalmembers
  $sql = "SHOW COLUMNS FROM `se_events` LIKE 'event_totalmembers'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_events ADD COLUMN `event_totalmembers` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT event_id FROM se_events WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_events SET event_totalmembers=(SELECT COUNT(eventmember_id) FROM se_eventmembers WHERE eventmember_event_id='{$result['event_id']}' && eventmember_approved=1 && eventmember_status=1) WHERE event_id='{$result['event_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on event_title_cleaned
  $sql = "SHOW FULL COLUMNS FROM `se_events` LIKE 'event_title_cleaned'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_events MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on event_title
  $sql = "SHOW FULL COLUMNS FROM `se_events` LIKE 'event_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_events MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on event_desc
  $sql = "SHOW FULL COLUMNS FROM `se_events` LIKE 'event_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_events MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on event_host
  $sql = "SHOW FULL COLUMNS FROM `se_events` LIKE 'event_host'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_events MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on event_location
  $sql = "SHOW FULL COLUMNS FROM `se_events` LIKE 'event_location'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_events MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on event_photo
  $sql = "SHOW FULL COLUMNS FROM `se_events` LIKE 'event_photo'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_events MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_eventstyles
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventstyles'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventstyles`
      (
        `eventstyle_id`       INT           UNSIGNED  NOT NULL auto_increment,
        `eventstyle_event_id` INT           UNSIGNED  NOT NULL default 0,
        `eventstyle_css`      TEXT                        NULL,
        PRIMARY KEY  (`eventstyle_id`),
        KEY `INDEX` (`eventstyle_event_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on eventstyle_css
  $sql = "SHOW FULL COLUMNS FROM `se_eventstyles` LIKE 'eventstyle_css'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_eventstyles MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_eventvalues
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_eventvalues'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_eventvalues` (
        `eventvalue_id`         INT           UNSIGNED  NOT NULL auto_increment,
        `eventvalue_event_id`   INT           UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`eventvalue_id`),
        KEY `INDEX` (`eventvalue_event_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### INSERT se_urls
  $sql = "SELECT url_id FROM se_urls WHERE url_file='event'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Event URL', 'event', 'event.php?event_id=\$id1', '\event/\$id1/')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT url_id FROM se_urls WHERE url_file='event_media'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Event Media URL', 'event_media', 'event_album_file.php?event_id=\$id1&eventmedia_id=\$id2', '\event/\$id1/album/\$id2/')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Fix bogus url
  $sql = "UPDATE se_urls SET url_regular='event_album_file.php?event_id=\$id1&eventmedia_id=\$id2' WHERE url_regular='event.php?event_id=\$id1&eventmedia_id=\$id2'";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newevent'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newevent', 'event_action_newevent.gif', '1', '1', '3000296', '3000297', '[username],[displayname],[id],[title]', '0')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='joinevent'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('joinevent', 'event_action_joinevent.gif', '1', '1', '3000298', '3000299', '[username],[displayname],[id],[title]', '0')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='leaveevent'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('leaveevent', 'event_action_leaveevent.gif', '1', '1', '3000300', '3000301', '[username],[displayname],[id],[title]', '0')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='attendevent'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('attendevent', 'event_action_attendevent.gif', '1', '1', '3000302', '3000303', '[username],[displayname],[id],[title]', '0')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='eventcomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('eventcomment', 'action_postcomment.gif', '1', '1', '3000304', '3000305', '[username],[displayname],,,[comment],[id],[title]', '0')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='eventmediacomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('eventmediacomment', 'action_postcomment.gif', '1', '1', '3000306', '3000307', '[username],[displayname],,,[comment],[id],[title],[parentid]', '0')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='neweventmedia'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('neweventmedia', 'event_action_newmedia.gif', '1', '1', '3000308', '3000309', '[username],[displayname],[id],[title]', '1')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='neweventtag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('neweventtag', 'event_action_neweventtag.gif', '1', '1', '3000310', '3000311', '[username],[displayname]', '1')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $sql = "UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Update old action types for 3.04
  $sql = "
    UPDATE se_languagevars
    SET languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the event %7\$s\'s <a href=\"event_album_file.php?event_id=%8\$s&eventmedia_id=%6\$s\">photo</a>:<div class=\"recentaction_div\">%5\$s</div>'
    WHERE languagevar_value LIKE '%posted a comment on the event %event_album_file.php?eventmedia_id=%6\$s\">photo%'
    LIMIT 1
  ";
  
  $database->database_query($sql) or die($database->database_error()." SQL was: ".$sql);
  
  $sql = "
    UPDATE se_actiontypes
    SET actiontype_vars='[username],[displayname],,,[comment],[id],[title],[parentid]'
    WHERE actiontype_name='eventmediacomment' && actiontype_vars='[username],[displayname],,,[comment],[id],[title]'
    LIMIT 1
  ";
  
  $database->database_query($sql) or die($database->database_error()." SQL was: ".$sql);
  
  
  
  
  //######### INSERT se_notifytypes
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='eventcomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('eventcomment', '3000312', 'action_postcomment.gif', 'event.php?event_id=%2\$s', '3000313')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='eventmediacomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('eventmediacomment', '3000314', 'action_postcomment.gif', 'event_album_file.php?event_id=%3\$s&eventmedia_id=%2\$s', '3000315')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='eventinvite'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title, notifytype_group)
      VALUES
        ('eventinvite', '3000316', 'event_action_attendevent.gif', 'event.php?event_id=%2\$s', '3000317', 0)
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='eventmemberrequest'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title, notifytype_group)
      VALUES
        ('eventmemberrequest', '3000318', 'event_action_attendevent.gif', 'event.php?event_id=%2\$s', '3000319', 1)
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='neweventtag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('neweventtag', '3000320', 'event_action_neweventtag.gif', 'profile_photos_file.php?user=%1\$s&type=%2\$s&media_id=%3\$s', '3000321')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='eventmediatag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('eventmediatag', '3000322', 'event_action_neweventtag.gif', 'event_album_file.php?event_id=%3\$s&eventmedia_id=%2\$s', '3000323')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Update notify type url for 3.04
  $sql = "
    UPDATE
      se_notifytypes
    SET
      notifytype_url='event_album_file.php?event_id=%3\$s&eventmedia_id=%2\$s'
    WHERE
      notifytype_name='eventmediacomment' &&
      notifytype_url='event_album_file.php?eventmedia_id=%2\$s'
    LIMIT
      1
  ";
  
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);  
  
  
  $sql = "
    UPDATE
      se_notifytypes
    SET
      notifytype_url='user_event_edit_members.php?event_id=%2\$s&v=-2'
    WHERE
      notifytype_name='eventmemberrequest' &&
      notifytype_url='event.php?event_id=%2\$s'
    LIMIT
      1
  ";
  
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);  
  
  
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_allow'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( !$column_info )
  {
    $sql = "
      ALTER TABLE se_levels 
      ADD COLUMN `level_event_allow`          TINYINT     UNSIGNED  NOT NULL default 7,
      ADD COLUMN `level_event_photo`          TINYINT     UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_event_photo_width`    VARCHAR(3)            NOT NULL default '200',
      ADD COLUMN `level_event_photo_height`   VARCHAR(3)            NOT NULL default '200',
      ADD COLUMN `level_event_photo_exts`     VARCHAR(50)           NOT NULL default 'jpeg,jpg,gif,png',
      ADD COLUMN `level_event_inviteonly`     TINYINT     UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_event_style`          TINYINT     UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_event_album_exts`     TEXT                      NULL,
      ADD COLUMN `level_event_album_mimes`    TEXT                      NULL,
      ADD COLUMN `level_event_album_storage`  BIGINT                NOT NULL default '5242880',
      ADD COLUMN `level_event_album_maxsize`  BIGINT                NOT NULL default '2048000',
      ADD COLUMN `level_event_album_width`    VARCHAR(4)            NOT NULL default '500',
      ADD COLUMN `level_event_album_height`   VARCHAR(4)            NOT NULL default '500',
      ADD COLUMN `level_event_search`         TINYINT     UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_event_privacy`        VARCHAR(128)          NOT NULL default 'a:6:{i:0;s:1:\"3\";i:1;s:1:\"7\";i:2;s:2:\"15\";i:3;s:2:\"31\";i:4;s:2:\"63\";i:5;s:3:\"127\";}',
      ADD COLUMN `level_event_comments`       VARCHAR(128)          NOT NULL default 'a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}',
      ADD COLUMN `level_event_html`           TEXT                      NULL,
      ADD COLUMN `level_event_backdate`       TINYINT(1)  UNSIGNED  NOT NULL default 0
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    
    $sql = "
      UPDATE se_levels
      SET level_event_album_exts='jpg,gif,jpeg,png,bmp,mp3,mpeg,avi,mpa,mov,qt,swf',
      level_event_album_mimes='image/jpeg,image/pjpeg,image/jpg,image/jpe,image/pjpg,image/x-jpeg,image/x-jpg,image/gif,image/x-gif,image/png,image/x-png,image/bmp,audio/mpeg,video/mpeg,video/x-msvideo,video/avi,video/quicktime,application/x-shockwave-flash',
      level_event_html='br,hr,img,a,ul,ol,li'
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  if( $column_info && strtoupper($column_info['Default'])!="7" )
  {
    $sql = "ALTER TABLE se_levels CHANGE `level_event_allow` `level_event_allow` TINYINT UNSIGNED NOT NULL default 7";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_levels SET level_event_allow=7 WHERE level_event_allow=1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_privacy'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $column_info && strtoupper($column_info['Type'])!=="VARCHAR(128)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE {$column_info['Field']} {$column_info['Field']} VARCHAR(128) NOT NULL default 'a:6:{i:0;s:1:\"3\";i:1;s:1:\"7\";i:2;s:2:\"15\";i:3;s:2:\"31\";i:4;s:2:\"63\";i:5;s:3:\"127\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT level_id, {$column_info['Field']} FROM se_levels WHERE 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    while( $level_info = $database->database_fetch_assoc($resource) )
    {
      if( unserialize($level_info[$column_info['Field']]) ) continue;
      
      $sql = "UPDATE se_levels SET {$column_info['Field']}='a:6:{i:0;s:1:\"3\";i:1;s:1:\"7\";i:2;s:2:\"15\";i:3;s:2:\"31\";i:4;s:2:\"63\";i:5;s:3:\"127\";}' WHERE level_id='{$level_info['level_id']}' LIMIT 1";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_comments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $column_info && strtoupper($column_info['Type'])!=="VARCHAR(128)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE {$column_info['Field']} {$column_info['Field']} VARCHAR(128) NOT NULL default 'a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT level_id, {$column_info['Field']} FROM se_levels WHERE 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    while( $level_info = $database->database_fetch_assoc($resource) )
    {
      if( unserialize($level_info[$column_info['Field']]) ) continue;
      
      $sql = "UPDATE se_levels SET {$column_info['Field']}='a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}' WHERE level_id='{$level_info['level_id']}' LIMIT 1";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_upload'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( !$column_info )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_event_upload`   VARCHAR(128)  NOT NULL default 'a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  elseif( $column_info && strtoupper($column_info['Type'])!=="VARCHAR(128)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE {$column_info['Field']} {$column_info['Field']} VARCHAR(128) NOT NULL default 'a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT level_id, {$column_info['Field']} FROM se_levels WHERE 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    while( $level_info = $database->database_fetch_assoc($resource) )
    {
      if( unserialize($level_info[$column_info['Field']]) ) continue;
      
      $sql = "UPDATE se_levels SET {$column_info['Field']}='a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}' WHERE level_id='{$level_info['level_id']}' LIMIT 1";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_tag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( !$column_info )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_event_tag`      VARCHAR(128)  NOT NULL default 'a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  elseif( $column_info && strtoupper($column_info['Type'])!=="VARCHAR(128)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE {$column_info['Field']} {$column_info['Field']} VARCHAR(128) NOT NULL default 'a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT level_id, {$column_info['Field']} FROM se_levels WHERE 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    while( $level_info = $database->database_fetch_assoc($resource) )
    {
      if( unserialize($level_info[$column_info['Field']]) ) continue;
      
      $sql = "UPDATE se_levels SET {$column_info['Field']}='a:8:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";i:7;s:3:\"127\";}' WHERE level_id='{$level_info['level_id']}' LIMIT 1";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_html'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_event_html` TEXT NULL";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_levels SET level_event_html='br,hr,img,a,ul,ol,li'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_event_backdate'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_event_backdate` TINYINT(1) UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_event'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_settings ADD COLUMN `setting_permission_event` TINYINT NOT NULL default 1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='eventinvite'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('eventinvite', '3000005', '3000006', '3000324', '3000325', '[displayname],[eventname],[link]')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='eventcomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('eventcomment', '3000008', '3000009', '3000326', '3000327', '[displayname],[commenter],[link]')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='eventmediacomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('eventmediacomment', '3000010', '3000011', '3000328', '3000329', '[displayname],[commenter],[link]')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='eventmemberrequest'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('eventmemberrequest', '3000012', '3000013', '3000330', '3000331', '[displayname],[requester],[eventname],[link]')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='neweventtag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('neweventtag', '3000253', '3000254', '3000332', '3000333', '[displayname],[link]')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='eventmediatag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('eventmediatag', '3000255', '3000256', '3000334', '3000335', '[displayname],[tagger],[link]')
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_eventinvite'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_usersettings 
      ADD COLUMN `usersetting_notify_eventinvite`         TINYINT UNSIGNED NOT NULL default 1,
      ADD COLUMN `usersetting_notify_eventcomment`        TINYINT UNSIGNED NOT NULL default 1,
      ADD COLUMN `usersetting_notify_eventmediacomment`   TINYINT UNSIGNED NOT NULL default 1,
      ADD COLUMN `usersetting_notify_eventmemberrequest`  TINYINT UNSIGNED NOT NULL default 1
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_neweventtag'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_usersettings 
      ADD COLUMN `usersetting_notify_neweventtag`           TINYINT UNSIGNED NOT NULL default 1,
      ADD COLUMN `usersetting_notify_eventmediatag`         TINYINT UNSIGNED NOT NULL default 1
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=3000001 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (3000001, 1, 'Event Settings', ''),
        (3000002, 1, 'View Events', ''),
        (3000003, 1, 'Global Event Settings', ''),
        (3000004, 1, 'Event Settings', ''),
        (3000005, 1, 'New Event Invitation Email', ''),
        (3000006, 1, 'This is the email that gets sent to a user when they are invited to an event.', ''),
        (3000007, 1, 'Events', ''),
        (3000008, 1, 'New Event Comment Email', ''),
        (3000009, 1, 'This is the email that gets sent to a user when a comment is posted on an event they created.', ''),
        (3000010, 1, 'New Event Photo Comment Email', ''),
        (3000011, 1, 'This is the email that gets sent to a user when a comment is posted on a photo for an event they created.', ''),
        (3000012, 1, 'New Event Invitation Request Email', ''),
        (3000013, 1, 'This is the email that gets sent to a user when someone requests an invitation to an event they created.', ''),
        (3000014, 1, 'Only Invited Users, Their Friends, and Their Friends\' Friends', ''),
        (3000015, 1, 'Only Invited Users and Their Friends', ''),
        (3000016, 1, 'Only Invited Users and Event Creator\'s Friends', ''),
        (3000017, 1, 'Only Invited Users', ''),
        (3000018, 1, 'Only Event Creator', '')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }


  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=3000019 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* admin_event */
        (3000019, 1, 'General Event Settings', 'admin_event'),
        (3000020, 1, 'This page contains general event settings that affect your entire social network.', 'admin_event'),
        (3000021, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\"admin_general.php\">General Settings</a> page.', 'admin_event'),
        (3000022, 1, 'Yes, the public can view events unless they are made private.', 'admin_event'),
        (3000023, 1, 'No, the public cannot view events.', 'admin_event'),
        (3000024, 1, 'Event Categories/Fields', 'admin_event'),
        (3000025, 1, 'You may want to allow your users to categorize their events by subject, location, etc. If you want to allow event categories, you can create them (along with subcategories) below.<br /><br />Within each category, you can create fields. When an event is created, the creator (owner) will describe the event by filling in some fields with information about the event. Add the fields you want to include below. Remember that a \"Event Title\" and \"Event Description\" field will always be available and required. Drag the icons next to the categories and fields to reorder them.', 'admin_event'),
        (3000026, 1, 'Event Categories', 'admin_event'),
        
        /* admin_levels_eventsettings */
        (3000027, 1, 'Photo width and height must be integers between 1 and 999.', 'admin_levels_eventsettings'),
        (3000028, 1, 'Your maximum filesize field must contain an integer between 1 and 204800.', 'admin_levels_eventsettings'),
        (3000029, 1, 'Your maximum width and height fields must contain integers between 1 and 9999.', 'admin_levels_eventsettings'),
        (3000030, 1, 'If you have enabled events, your users will have the option of creating events and inviting users. Use this page to configure your event settings.', 'admin_levels_eventsettings'),
        (3000031, 1, 'Allow Events?', 'admin_levels_eventsettings'),
        
        (3000038, 1, 'Allow Event Photos?', 'admin_levels_eventsettings'),
        (3000039, 1, 'If you enable this feature, users will be able to upload a small photo icon when creating or editing an event. This can be displayed next to the event name on users\' profiles, in search/browse results, etc.', 'admin_levels_eventsettings'),
        (3000040, 1, 'Yes, users can upload a photo icon when they create/edit an event.', 'admin_levels_eventsettings'),
        (3000041, 1, 'No, users can not upload a photo icon when they create/edit an event.', 'admin_levels_eventsettings'),
        (3000042, 1, 'If you have selected yes above, please input the maximum dimensions for the event photos. If your users upload a photo that is larger than these dimensions, the server will attempt to scale them down automatically. This feature requires that your PHP server is compiled with support for the GD Libraries.', 'admin_levels_eventsettings'),
        (3000043, 1, 'Maximum Width:', 'admin_levels_eventsettings'),
        (3000044, 1, 'Maximum Height:', 'admin_levels_eventsettings'),
        (3000045, 1, '(in pixels, between %1\$d and %1\$d)', 'admin_levels_eventsettings'),
        (3000046, 1, 'What file types do you want to allow for event photos? Separate file types with commas, i.e. jpg, jpeg, gif, png', 'admin_levels_eventsettings'),
        (3000047, 1, 'Allowed File Types:', 'admin_levels_eventsettings'),
        (3000048, 1, 'Event Privacy Options', 'admin_levels_eventsettings'),
        (3000049, 1, '<b>Search Privacy Options</b><br>If you enable this feature, event leaders will be able to exclude their event from search results. Otherwise, all events will be included in search results.', 'admin_levels_eventsettings'),
        (3000050, 1, 'Yes, allow event leaders to exclude their events from search results.', 'admin_levels_eventsettings'),
        (3000051, 1, 'No, force all events to be included in search results.', 'admin_levels_eventsettings'),
        (3000052, 1, '<b>Overall Event Privacy</b><br>Event Creators can choose from any of the options checked below when they decide who can view their events. If you do not check any options, everyone will be allowed to view events.', 'admin_levels_eventsettings'),
        (3000053, 1, '<b>Event Comment Options</b><br>Event Creators can choose from any of the options checked below when they decide who can post comments on their events. If you do not check any options, everyone will be allowed to post comments on events.', 'admin_levels_eventsettings'),
        (3000054, 1, 'Allow Invite-Only Events?', 'admin_levels_eventsettings'),
        (3000055, 1, 'Do you want to give event creators the ability to create invite-only events? If set to yes, event creators will be able to set events to \"invite-only\". This means only invited users will be able to RSVP to the event and un-invited users will need to request an invitation.', 'admin_levels_eventsettings'),
        (3000056, 1, 'Yes, optionally allow invite-only events.', 'admin_levels_eventsettings'),
        (3000057, 1, 'No, do not allow invite-only events.', 'admin_levels_eventsettings'),
        (3000058, 1, 'Allow custom CSS styles?', 'admin_levels_eventsettings'),
        (3000059, 1, 'If you enable this feature, your users will be able to customize the colors and fonts of their events by altering their CSS styles.', 'admin_levels_eventsettings'),
        (3000060, 1, 'Yes, allow custom CSS.', 'admin_levels_eventsettings'),
        (3000061, 1, 'No, do not allow custom CSS.', 'admin_levels_eventsettings'),
        (3000062, 1, 'Event File Settings', 'admin_levels_eventsettings'),
        (3000063, 1, 'List the following file extensions that users are allowed to upload. Separate file extensions with commas, for example: jpg, gif, jpeg, png, bmp', 'admin_levels_eventsettings'),
        (3000064, 1, 'To successfully upload a file, the file must have an allowed filetype extension as well as an allowed MIME type. This prevents users from disguising malicious files with a fake extension. Separate MIME types with commas, for example: image/jpeg, image/gif, image/png, image/bmp', 'admin_levels_eventsettings'),
        (3000065, 1, 'How much storage space should each event have to store its files?', 'admin_levels_eventsettings'),
        (3000066, 1, 'Unlimited', 'admin_levels_eventsettings'),
        (3000067, 1, 'Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 204800.', 'admin_levels_eventsettings'),
        (3000068, 1, 'Enter the maximum width and height (in pixels) for images uploaded to events. Images with dimensions outside this range will be sized down accordingly if your server has the GD Libraries installed. Note that unusual image types like BMP, TIFF, RAW, and others may not be resized.', 'admin_levels_eventsettings'),
        (3000069, 1, '%1\$s B', 'admin_levels_eventsettings'),
        (3000070, 1, '%1\$s KB', 'admin_levels_eventsettings'),
        (3000071, 1, '%1\$s MB', 'admin_levels_eventsettings'),
        (3000072, 1, '%1\$s GB', 'admin_levels_eventsettings'),
        
        /* admin_viewevents */
        (3000073, 1, 'This page lists all of the events that users have created on your social network. You can use this page to monitor these events and delete offensive or unwanted material if necessary. Entering criteria into the filter fields will help you find specific events. Leaving the filter fields blank will show all the events on your social network.', 'admin_viewevents'),
        (3000074, 1, 'Title', 'admin_viewevents'),
        (3000075, 1, 'Creator', 'admin_viewevents'),
        (3000076, 1, '%1\$d Events Found', 'admin_viewevents'),
        (3000077, 1, 'view', 'admin_viewevents'),
        (3000078, 1, 'Are you sure you want to delete this event?', 'admin_viewevents'),
        (3000079, 1, 'No events were found.', 'admin_viewevents'),
        
        /* rsvp */
        (3000080, 1, 'Awaiting Approval', 'event, user_event'),
        (3000081, 1, 'Awaiting Response', 'event, user_event'),
        (3000082, 1, 'Attending', 'event, user_event'),
        (3000083, 1, 'Maybe Attending', 'event, user_event'),
        (3000084, 1, 'Not Attending', 'event, user_event'),
        (3000085, 1, 'I\'ll be late', 'event, user_event'),
        
        /* user_event */
        (3000086, 1, 'My Events', 'user_event'),
        (3000087, 1, 'Below are all of the events that you\'ve created or been invited to.<br>To search for upcoming events to join, visit the <a href=\"%1\$s\">Browse Events page</a>', 'user_event'),
        (3000088, 1, 'Create New Event', 'user_event, user_event_add'),
        (3000089, 1, 'Search My Events', 'user_event'),
        (3000090, 1, 'View:', 'browse_events, user_event'),
        (3000091, 1, 'List', 'user_event'),
        (3000092, 1, 'By Month', 'user_event'),
        (3000093, 1, 'Delete Event?', 'event, user_event'),
        (3000094, 1, 'Are you sure you want to delete this event?', 'user_event'),
        (3000095, 1, 'Leave Event?', 'user_event'),
        (3000096, 1, 'Are you sure you want to leave this event?', 'user_event'),
        (3000097, 1, 'RSVP to Event', 'event, user_event'),
        (3000098, 1, 'To RSVP to this event, please select the appropriate option below.', 'event, user_event'),
        (3000099, 1, 'Yes, I will be attending this event.', 'event, user_event'),
        (3000100, 1, 'I may attend this event.', 'event, user_event'),
        (3000101, 1, 'No, I will not be attending this event.', 'event, user_event'),
        (3000102, 1, 'You do not have any events. Click <a href=\"%1\$s\">here</a> to create one', 'user_event'),
        (3000103, 1, 'No events matched your search criteria.', 'user_event'),
        (3000104, 1, 'Category:', 'user_event'),
        (3000105, 1, 'When:', 'user_event'),
        
        /* user_event_add */
        (3000108, 1, 'Please give us some information about your new event. After you have created your event, you can begin inviting other users to attend.', 'user_event_add'),
        (3000109, 1, 'Back to My Events', 'user_event_add, user_event_edit'),
        (3000110, 1, 'Event Name', 'user_event_add, user_event_edit'),
        (3000111, 1, 'Event Description', 'user_event_add, user_event_edit'),
        (3000112, 1, 'Start Time', 'user_event_add, user_event_edit'),
        (3000113, 1, 'End Time', 'user_event_add, user_event_edit'),
        (3000114, 1, '(hh:mm am/pm, hh:mm)', 'user_event_add, user_event_edit'),
        (3000115, 1, 'Host', 'user_event_add, user_event_edit'),
        (3000116, 1, 'Location', 'user_event_add, user_event_edit'),
        (3000117, 1, 'Invite Only?', 'user_event_add, user_event_edit_settings'),
        (3000118, 1, 'Is an invitation required before users can join this event?', 'user_event_add, user_event_edit_settings'),
        (3000119, 1, 'Yes, users may RSVP without invitation.', 'user_event_add, user_event_edit_settings'),
        (3000120, 1, 'No, users may not RSVP unless they were invited.', 'user_event_add, user_event_edit_settings'),
        (3000121, 1, 'Search Results', 'user_event_add, user_event_edit_settings'),
        (3000122, 1, 'Include this event in search/browse results?', 'user_event_add, user_event_edit_settings'),
        (3000123, 1, 'Yes, include this event in search/browse results.', 'user_event_add, user_event_edit_settings'),
        (3000124, 1, 'No, exclude this event from search/browse results.', 'user_event_add, user_event_edit_settings'),
        (3000125, 1, 'Allow Viewing?', 'user_event_add, user_event_edit_settings'),
        (3000126, 1, 'You can decide who can see this event.', 'user_event_add, user_event_edit_settings'),
        (3000127, 1, 'Allow Comments?', 'user_event_add, user_event_edit_settings'),
        (3000128, 1, 'You can decide who can post comments in this event.', 'user_event_add, user_event_edit_settings'),
        (3000129, 1, 'Allow Uploads?', 'user_event_add, user_event_edit_settings'),
        (3000130, 1, 'You can decide who can upload photos to this event.', 'user_event_add, user_event_edit_settings'),
        (3000131, 1, 'Allow Photo Tagging?', 'user_event_add, user_event_edit_settings'),
        (3000132, 1, 'You can decide who can tag photos within this event.', 'user_event_add, user_event_edit_settings'),
        (3000133, 1, 'Add Event', 'user_event_add'),
        (3000134, 1, 'Event Category', 'event, user_event_add, user_event_edit'),
        
        /* user_event_edit */
        (3000135, 1, 'Edit Event: <a href=\"%1\$s\">%2\$s</a>', 'user_event_edit'),
        (3000136, 1, 'All of this event\'s details are displayed and can be changed below.', 'user_event_edit'),
        (3000137, 1, 'Event Details', 'event, user_event_edit'),
        (3000138, 1, 'Guests', 'event, user_event_edit, user_event_edit_members, user_edit_event_settings'),
        (3000139, 1, 'Your event was successfully created! You can add a photo and edit the event details below.', 'user_event_edit'),
        (3000140, 1, 'Images must be less than %1\$s in size with one of the following extensions: %2\$s', 'user_event_edit'),
        
        /* user_event_edit_members */
        (3000141, 1, 'Browse Guests: <a href=\"%1\$s\">%2\$s</a>', 'user_event_edit_members'),
        (3000142, 1, 'Use this page to list or search for event guests.', 'user_event_edit_members'),
        (3000143, 1, 'All Guests', 'user_event_edit_members'),
        (3000144, 1, 'View:', 'user_event_edit_members'),
        (3000145, 1, 'Invite New Guests', 'user_event_edit_members'),
        (3000146, 1, 'No guests were found matching your criteria.', 'user_event_edit_members'),
        (3000147, 1, 'Current Response:', 'user_event_edit_members'),
        (3000148, 1, 'Last Update:', 'user_event_edit_members'),
        (3000149, 1, 'Accept Request', 'user_event_edit_members'),
        (3000150, 1, 'Reject Request', 'user_event_edit_members'),
        (3000151, 1, 'Remove Guest', 'user_event_edit_members'),
        (3000152, 1, 'Event Leader', 'event, user_event_edit_members'),
        (3000153, 1, 'There was an error processing your request.', 'user_event, user_event_edit_members'),
        (3000154, 1, 'Delete Guest?', 'user_event_edit_members'),
        (3000155, 1, 'Are you sure you want to delete this guest from your event?', 'user_event_edit_members'),
        
        /* user_event_edit_settings */
        (3000156, 1, 'Event Settings: <a href=\"%1\$s\">%2\$s</a>', 'user_event_edit_settings'),
        (3000157, 1, 'Edit event settings, such as your event\'s style.', 'user_event_edit_settings'),
        (3000158, 1, 'Event Style', 'user_event_edit_settings'),
        (3000159, 1, 'You can change the colors, fonts, and styles of your event by adding CSS code below. The contents of the text area below will be output between &lt;style&gt; tags on your event.', 'user_event_edit_settings'),
        
        /* event */
        (3000160, 1, 'Event Guests', 'event'),
        (3000161, 1, 'Search Guests', 'event'),
        (3000162, 1, 'None of the event guests match your search criteria.', 'event'),
        (3000163, 1, 'Member', 'event'),
        (3000164, 1, 'Photos', 'event, event_album_file'),
        (3000165, 1, 'Add New Photos', 'event'),
        (3000166, 1, 'No photos have been added to this event yet.', 'event'),
        (3000167, 1, 'Request an Invitation', 'event'),
        (3000168, 1, 'Attend this Event', 'event'),
        (3000169, 1, 'Delete Event', 'event'),
        (3000170, 1, 'Cancel Request', 'event, user_event'),
        
        (3000172, 1, 'Report this Event', 'event'),
        (3000173, 1, 'Private Event', 'event'),
        (3000174, 1, 'Event Information', 'event'),
        (3000175, 1, 'Event Date/Time', 'event'),
        (3000176, 1, 'Category', 'event'),
        
        /* user_event_upload */
        (3000177, 1, 'To upload photos from your computer to this event, click the \"Browse\" button, locate them on your computer, then click the \"Upload\" button.', 'user_event_upload'),
        (3000178, 1, 'This event has %1\$s MB of free space remaining.', 'user_event_upload'),
        (3000179, 1, 'You may upload files with sizes up to %1\$s KB and with the following extensions: %2\$s', 'user_event_upload'),
        
        /* event_album_file */
        (3000180, 1, 'Download', 'event_album_file'),
        (3000181, 1, 'Viewing #%1\$d of %2\$d in <a href=\"%3\$s\">%4\$s</a>', 'event_album_file'),
        (3000182, 1, 'Last', 'event_album_file'),
        (3000183, 1, 'Next', 'event_album_file'),
        (3000184, 1, 'In this photo:', 'event_album_file'),
        (3000185, 1, 'Add Tag', 'event_album_file'),
        (3000186, 1, 'Share This', 'event_album_file'),
        (3000187, 1, 'Uploaded %1\$s by <a href=\"%2\$s\">%3\$s</a>', 'event_album_file'),
        (3000188, 1, 'Uploaded %1\$s', 'event_album_file'),
        (3000189, 1, 'Edit Photo Details', 'event_album_file'),
        (3000190, 1, 'Delete Photo', 'event_album_file'),
        (3000191, 1, 'Are you sure you want to delete this photo?', 'event_album_file'),
        (3000192, 1, 'Enter a title and description for this photo in the fields below.', 'event_album_file'),
        (3000193, 1, 'Title: ', 'event_album_file'),
        (3000194, 1, 'Description:', 'event_album_file'),
        (3000195, 1, 'To share this photo or embed it on another web page, please copy and paste the code of your choosing:', 'event_album_file'),
        (3000196, 1, 'Direct Link', 'event_album_file'),
        (3000197, 1, 'HTML - Embedded Image', 'event_album_file'),
        (3000198, 1, 'HTML - Text Link', 'event_album_file'),
        (3000199, 1, 'UBB Code (for forums)', 'event_album_file'),
        (3000200, 1, 'Close Window', 'event_album_file'),
        
        /* MISC */
        (3000201, 1, 'What\'s New', 'event'),
        (3000202, 1, '%1\$s from %2\$s to %3\$s', 'event, user_event'),
        (3000203, 1, '%1\$s at %2\$s', 'event, user_event'),
        (3000204, 1, '%1\$s to %2\$s', 'event, user_event'),
        
        /* browse_events */
        (3000205, 1, 'Browse Events', 'browse_events'),
        (3000206, 1, 'Everyone\'s Events', 'browse_events'),
        (3000207, 1, 'My Friends\' Events', 'browse_events'),
        (3000208, 1, 'Order:', 'browse_events'),
        (3000209, 1, 'Most Popular', 'browse_events'),
        (3000210, 1, 'Upcoming Events', 'browse_events'),
        (3000211, 1, 'Starting Date', 'browse_events'),
        (3000212, 1, 'Ending Date', 'browse_events'),
        (3000213, 1, 'All Events', 'browse_events'),
        (3000214, 1, 'No events were found matching your criteria', 'browse_events'),
        (3000215, 1, '%1\$d guests', 'browse_events'),
        (3000216, 1, 'led by <a href=\"%2\$s\">%1\$s</a>', 'browse_events'),
        (3000217, 1, 'updated %1\$s', 'browse_events'),
        
        /* MISC */
        (3000218, 1, 'Search my events for:', 'user_event'),
        (3000219, 1, 'Leave Event', 'event, user_event'),
        (3000220, 1, 'Are you sure you want to leave this event?', 'event, user_event'),
        (3000221, 1, 'Are you sure you want to cancel the request for invitiation to this event?', 'event, user_event'),
        (3000222, 1, 'Invited Users', 'user_event_edit_members'),
        (3000223, 1, 'Cancel Invite', 'user_event_edit_members'),
        (3000224, 1, 'Are you sure you want to cancel your invitiation to this user?', 'user_event_edit_members'),
        (3000225, 1, 'Invite Guests', 'event, user_event_edit_members'),
        (3000226, 1, 'To invite a friend to join this event, check the box next to their name below. Remember that even if this event is set to be viewable by \"members only\", people that you invite will be able to view the event as though they are members.', 'event, user_event_edit_members'),
        (3000227, 1, 'You have no friends that are available to join this event.', 'event, user_event_edit_members'),
        (3000228, 1, 'Select All', 'event, user_event_edit_members'),
        (3000229, 1, '%1\$d invite(s) were successfully sent.', 'event, user_event_edit_members'),
        (3000230, 1, 'Monday', 'event_calendar'),
        (3000231, 1, 'Tuesday', 'event_calendar'),
        (3000232, 1, 'Wednesday', 'event_calendar'),
        (3000233, 1, 'Thursday', 'event_calendar'),
        (3000234, 1, 'Friday', 'event_calendar'),
        (3000235, 1, 'Saturday', 'event_calendar'),
        (3000236, 1, 'Sunday', 'event_calendar'),
        (3000237, 1, 'M', 'event_calendar'),
        (3000238, 1, 'T', 'event_calendar'),
        (3000239, 1, 'W', 'event_calendar'),
        (3000240, 1, 'T', 'event_calendar'),
        (3000241, 1, 'F', 'event_calendar'),
        (3000242, 1, 'S', 'event_calendar'),
        (3000243, 1, 'S', 'event_calendar'),
        (3000244, 1, 'Calendar', 'event, event_calendar'),
        (3000245, 1, 'Edit Event', 'event, user_event'),
        (3000246, 1, 'You must enter a title for your event.', 'user_event_add, user_event_edit'),
        (3000247, 1, 'You must select a category for your event.', 'user_event_add, user_event_edit'),
        (3000248, 1, 'You do not have permission to perform that operation.', 'user_event_add, user_event_edit'),
        (3000249, 1, 'If you have selected an end date, please ensure it is after the start date.', 'user_event_add, user_event_edit'),
        (3000250, 1, 'You must enter a date in the future.', 'user_event_add, user_event_edit'),
        (3000251, 1, 'Target could not be found, or may have already had this operation performed.', 'user_event_add, user_event_edit'),
        (3000252, 1, 'Event Photo', 'user_event_edit'),
        (3000253, 1, 'Notification of Being Tagged in an Event Photo', NULL),
        (3000254, 1, 'This is the email that gets sent to a user when someone tags them in an event photo.', NULL),
        (3000255, 1, 'New Event Photo Tag Email', NULL),
        (3000256, 1, 'This is the email that gets sent to a user when someone tags one of the photos in an event they lead.', NULL),
        (3000257, 1, '<b>Event Album Upload Options</b><br />Event leaders can choose from any of the options checked below when they decide who can upload photos to their event. If you do not check any options, everyone will be allowed to upload photos to events.', 'admin_levels_eventsettings'),
        (3000258, 1, '<b>Event Album Tag Options</b><br />Event leaders can choose from any of the options checked below when they decide who can tag photos in their event. If you do not check any options, everyone will be allowed to tag photos in events.', 'admin_levels_eventsettings'),
        (3000259, 1, 'You may choose what access users in this level have to events.', 'admin_levels_eventsettings'),
        (3000260, 1, 'Users may view, join, and create events.', 'admin_levels_eventsettings'),
        (3000261, 1, 'Users may view and join events.', 'admin_levels_eventsettings'),
        (3000262, 1, 'Users may only view events.', 'admin_levels_eventsettings'),
        (3000263, 1, 'Users may not use events.', 'admin_levels_eventsettings'),
        
        (3000265, 1, 'Yes, allow event members to invite their friends to join.', 'user_event_add, user_event_edit_settings'),
        (3000266, 1, 'No, only the event leader may invite users to join.', 'user_event_add, user_event_edit_settings'),
        (3000267, 1, 'Member Invite', 'user_event_add, user_event_edit_settings'),
        (3000268, 1, 'Allow event members to invite users?', 'user_event_add, user_event_edit_settings'),
        (3000269, 1, 'Event Officers', 'event'),
        (3000270, 1, 'leader', 'event'),
        
        (3000272, 1, '%1\$s', NULL),
        (3000273, 1, '%1\$s: %2\$s', NULL),
        (3000274, 1, 'Browse Events', NULL),
        (3000275, 1, 'Browse the events on our social network.', NULL),
        (3000276, 1, '%1\$s\'s events on %2\$s', 'event_calendar'),
        (3000277, 1, 'Your status:', 'user_event'),
        (3000278, 1, 'My RSVP', 'event'),
        (3000279, 1, 'Your RSVP has been saved.', 'event'),
        (3000280, 1, 'Event Type', 'event')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=3000281 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* search */
        (3000281, 1, '%1\$d events', 'search'),
        (3000282, 1, 'Event: %1\$s', 'search'),
        (3000283, 1, 'Event Media: %1\$s', 'search'),
        (3000284, 1, '%1\$s', 'search'),
        (3000285, 1, 'Media posted in <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', 'search'),
        (3000286, 1, 'th', 'user_event_add, user_event_edit'),
        (3000287, 1, 'nd', 'user_event_add, user_event_edit'),
        (3000288, 1, 'rd', 'user_event_add, user_event_edit'),
        (3000289, 1, 'th', 'user_event_add, user_event_edit'),
        (3000290, 1, '', 'user_event_add, user_event_edit')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=3000291 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (3000291, 1, 'Events: %1\$d events', 'home'),
        (3000292, 1, 'Event Comments: %1\$d comments', 'home'),
        (3000293, 1, 'Event Media: %1\$d media', 'home'),
        (3000294, 1, 'Event Members: %1\$d memberships', 'home')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=3000296 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (3000296, 1, 'Creating an Event', 'actiontypes'),
        (3000297, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> created a new event: <a href=\"event.php?event_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (3000298, 1, 'Joining an Event', 'actiontypes'),
        (3000299, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> joined an event: <a href=\"event.php?event_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (3000300, 1, 'Leaving an Event', 'actiontypes'),
        (3000301, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> left an event: <a href=\"event.php?event_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (3000302, 1, 'Attending an Event', 'actiontypes'),
        (3000303, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> is attending an event: <a href=\"event.php?event_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (3000304, 1, 'Posting an Event Comment', 'actiontypes'),
        (3000305, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the event <a href=\"event.php?event_id=%6\$s\">%7\$s</a>:<div class=\"recentaction_div\">%5\$s</div>', 'actiontypes'),
        (3000306, 1, 'Posting an Event Photo Comment', 'actiontypes'),
        (3000307, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on the event %7\$s\'s <a href=\"event_album_file.php?event_id=%8\$s&eventmedia_id=%6\$s\">photo</a>:<div class=\"recentaction_div\">%5\$s</div>', 'actiontypes'),
        (3000308, 1, 'Posting a Photo on an Event', 'actiontypes'),
        (3000309, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> uploaded new photos to the event <a href=\"event.php?event_id=%3\$s\">%4\$s</a><div class=\"recentaction_div_media\">[media]</div>', 'actiontypes'),
        (3000310, 1, 'Getting Tagged in an Event Photo', 'actiontypes'),
        (3000311, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> was tagged in these photos:<div class=\"recentaction_div_media\">[media]</div>', 'actiontypes'),
        (3000312, 1, '%1\$d New Event Comment(s): %2\$s', 'notifytypes'),
        (3000313, 1, 'When a new comment is posted on an event I created.', 'notifytypes'),
        (3000314, 1, '%1\$d New Event Photo Comment(s): %2\$s', 'notifytypes'),
        (3000315, 1, 'When a new comment is posted on a photo in an event I created.', 'notifytypes'),
        (3000316, 1, '%1\$d New Event Invitation(s)', 'notifytypes'),
        (3000317, 1, 'When I receive an event invitation.', 'notifytypes'),
        (3000318, 1, '%1\$d New Event Invite Request(s)', 'notifytypes'),
        (3000319, 1, 'When I receive an invitation request for an event I created.', 'notifytypes'),
        (3000320, 1, '%1\$d New Photo(s) Tagged of You: %2\$s', 'notifytypes'),
        (3000321, 1, 'When I am tagged in an event photo.', 'notifytypes'),
        (3000322, 1, '%1\$d New Tag(s) on Your Event\'s Photo: %2\$s', 'notifytypes'),
        (3000323, 1, 'When someone tags a photo in an event I lead.', 'notifytypes'),
        (3000324, 1, 'You have been invited to attend %2\$s.', 'systememails'),
        (3000325, 1, 'Hello %1\$s,\n\nYou have been invited to attend an event: %2\$s. Please click the following link to login:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (3000326, 1, 'New Event Comment', 'systememails'),
        (3000327, 1, 'Hello %1\$s,\n\nA new comment has been posted by %2\$s about an event you created. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (3000328, 1, 'New Event Photo Comment', 'systememails'),
        (3000329, 1, 'Hello %1\$s,\n\nA new comment has been posted by %2\$s on a photo for an event you created. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (3000330, 1, 'New Event Invitation Request', 'systememails'),
        (3000331, 1, 'Hello %1\$s,\n\n%2\$s has requested an invitation to your event \"%3\$s\". Please click the following link to login and send them an invitation:\n\n%4\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (3000332, 1, 'You have Been Tagged in an Event Photo!', 'systememails'),
        (3000333, 1, 'Hello %1\$s,\n\nYou have been tagged in an event photo. Please click the following link to view it:\n\n%2\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (3000334, 1, 'New Event Photo Tag', 'systememails'),
        (3000335, 1, 'Hello %1\$s,\n\nA new tag has been posted on one of the photos in an event you lead by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=3000336 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (3000336, 1, 'Allow Event Back-dating?', 'admin_levels_eventsettings'),
        (3000337, 1, 'If you enable this feature, users in this levels will be able to create events with start and end dates in the past.', 'admin_levels_eventsettings'),
        (3000338, 1, 'Yes, allow event back-dating.', 'admin_levels_eventsettings'),
        (3000339, 1, 'No, do not allow event back-dating.', 'admin_levels_eventsettings'),
        (3000340, 1, 'HTML in Event Descriptions', 'admin_levels_eventsettings'),
        (3000341, 1, 'By default, the user may not enter any HTML tags into event descriptions. If you want to allow specific tags, you can enter them below (separated by commas). Example: b, img, a, embed, font', 'admin_levels_eventsettings')
   ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  ################ UPGRADE EXISTING EVENTS' PRIVACY OPTIONS
  if( !empty($plugin_info) && version_compare($plugin_info['plugin_version'], '3.00', '<') )
  {
    $database->database_query("UPDATE se_events SET event_privacy='255'  WHERE event_privacy='0' ") or die($database->database_error()." View Privacy Query #1");
    $database->database_query("UPDATE se_events SET event_privacy='127'  WHERE event_privacy='1' ") or die($database->database_error()." View Privacy Query #2");
    $database->database_query("UPDATE se_events SET event_privacy='63'   WHERE event_privacy='2' ") or die($database->database_error()." View Privacy Query #3");
    $database->database_query("UPDATE se_events SET event_privacy='31'   WHERE event_privacy='3' ") or die($database->database_error()." View Privacy Query #4");
    $database->database_query("UPDATE se_events SET event_privacy='15'   WHERE event_privacy='4' ") or die($database->database_error()." View Privacy Query #5");
    $database->database_query("UPDATE se_events SET event_privacy='7'    WHERE event_privacy='5' ") or die($database->database_error()." View Privacy Query #6");
    $database->database_query("UPDATE se_events SET event_privacy='1'    WHERE event_privacy='6' ") or die($database->database_error()." View Privacy Query #7");
    
    $database->database_query("UPDATE se_events SET event_comments='255' WHERE event_comments='0'") or die($database->database_error()." Comment Privacy Query #1");
    $database->database_query("UPDATE se_events SET event_comments='127' WHERE event_comments='1'") or die($database->database_error()." Comment Privacy Query #2");
    $database->database_query("UPDATE se_events SET event_comments='63'  WHERE event_comments='2'") or die($database->database_error()." Comment Privacy Query #3");
    $database->database_query("UPDATE se_events SET event_comments='31'  WHERE event_comments='3'") or die($database->database_error()." Comment Privacy Query #4");
    $database->database_query("UPDATE se_events SET event_comments='15'  WHERE event_comments='4'") or die($database->database_error()." Comment Privacy Query #5");
    $database->database_query("UPDATE se_events SET event_comments='7'   WHERE event_comments='5'") or die($database->database_error()." Comment Privacy Query #6");
    $database->database_query("UPDATE se_events SET event_comments='1'   WHERE event_comments='6'") or die($database->database_error()." Comment Privacy Query #7");
    $database->database_query("UPDATE se_events SET event_comments='0'   WHERE event_comments='7'") or die($database->database_error()." Comment Privacy Query #8");
  }

}

?>