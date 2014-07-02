<?php

/* $Id: install_poll.php 283 2009-12-18 20:58:53Z phil $ */

$plugin_name = "Poll Plugin";
$plugin_version = "3.06";
$plugin_type = "poll";
$plugin_desc = "This plugin lets your users create and share their own polls. New polls are highlighted in the recent activity feed, adding an extra source of interactivity on your social network.";
$plugin_icon = "poll_poll16.gif";
$plugin_menu_title = "2500001";
$plugin_pages_main = "2500002<!>poll_poll16.gif<!>admin_viewpolls.php<~!~>2500003<!>poll_adminsettings16.gif<!>admin_poll.php<~!~>";
$plugin_pages_level = "2500004<!>admin_levels_pollsettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/polls/([0-9]+)/?$ \$server_info/poll.php?user=\$1&poll_id=\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/polls/([0-9]+)/([^/]+)?$ \$server_info/poll.php?user=\$1&poll_id=\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/polls/?$ \$server_info/polls.php?user=\$1 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;




if( $install=="poll" )
{
  //######### GET CURRENT PLUGIN INFORMATION
  $sql = "SELECT * FROM se_plugins WHERE plugin_type='$plugin_type' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  $plugin_info = array();
  if( $database->database_num_rows($resource) )
    $plugin_info = $database->database_fetch_assoc($resource);
  
  // Uncomment this line if you already upgraded to v3, but are having issues with everything being private
  //$plugin_info['plugin_version'] = '2.00';
  
  
  
  
  //######### INSERT ROW INTO se_plugins
  $sql = "SELECT NULL FROM se_plugins WHERE plugin_type='$plugin_type' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
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
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
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
      LIMIT
        1
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  
  //######### CREATE se_polls
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_polls'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_polls`
      (
        `poll_id`             INT           UNSIGNED  NOT NULL auto_increment,
        `poll_user_id`        INT           UNSIGNED  NOT NULL default 0,
        `poll_datecreated`    BIGINT        UNSIGNED  NOT NULL default 0,
        `poll_title`          VARCHAR(250)            NOT NULL default '',
        `poll_desc`           TEXT                        NULL,
        `poll_options`        TEXT                        NULL,
        `poll_answers`        TEXT                        NULL,
        `poll_voted`          TEXT                        NULL,
        `poll_search`         TINYINT       UNSIGNED  NOT NULL default 0,
        `poll_privacy`        SMALLINT      UNSIGNED  NOT NULL default 0,
        `poll_comments`       SMALLINT      UNSIGNED  NOT NULL default 0,
        `poll_closed`         TINYINT       UNSIGNED  NOT NULL default 0,
        
        `poll_totalvotes`     INT           UNSIGNED  NOT NULL default 0,
        `poll_views`          INT           UNSIGNED  NOT NULL default 0,
        `poll_totalcomments`  SMALLINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`poll_id`),
        KEY `INDEX`  (`poll_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  //######### ALTER se_polls
  else
  {
    $sql = "ALTER TABLE `se_polls` CHANGE poll_answers `poll_answers` TEXT NULL";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on poll_title
  $sql = "SHOW FULL COLUMNS FROM `se_polls` LIKE 'poll_title'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_polls MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on poll_desc
  $sql = "SHOW FULL COLUMNS FROM `se_polls` LIKE 'poll_desc'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_polls MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on poll_options
  $sql = "SHOW FULL COLUMNS FROM `se_polls` LIKE 'poll_options'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_polls MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Add poll_totalcomments
  $sql = "SHOW COLUMNS FROM `se_polls` LIKE 'poll_totalcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_polls ADD COLUMN `poll_totalcomments` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT poll_id FROM se_polls WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_polls SET poll_totalcomments=(SELECT COUNT(pollcomment_id) FROM se_pollcomments WHERE pollcomment_poll_id='{$result['poll_id']}') WHERE poll_id='{$result['poll_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  
  //######### CREATE se_pollcomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_pollcomments'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_pollcomments` (
        `pollcomment_id`            int(9)    UNSIGNED  NOT NULL auto_increment,
        `pollcomment_poll_id`       int(9)    UNSIGNED  NOT NULL default '0',
        `pollcomment_authoruser_id` int(9)    UNSIGNED  NOT NULL default '0',
        `pollcomment_date`          int(14)             NOT NULL default '0',
        `pollcomment_body`          TEXT                    NULL,
        PRIMARY KEY  (`pollcomment_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on pollcomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_pollcomments` LIKE 'pollcomment_body'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_pollcomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT se_urls
  if($database->database_num_rows($database->database_query("SELECT url_id FROM se_urls WHERE url_file='polls'")) == 0) {
    $database->database_query("INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Polls URL', 'polls', 'polls.php?user=\$user', '\$user/polls/')");
  }
  if($database->database_num_rows($database->database_query("SELECT url_id FROM se_urls WHERE url_file='poll'")) == 0) {
    $database->database_query("INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('Poll URL', 'poll', 'poll.php?user=\$user&poll_id=\$id1', '\$user/polls/\$id1/')");
  }
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newpoll'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('newpoll', 'poll_action_newpoll.gif', '1', '1', '2500131', '2500132', '[username],[displayname],[id],[title]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='votepoll'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('votepoll', 'poll_action_newpoll.gif', '1', '1', '2500133', '2500134', '[username1],[displayname1],[username2],[id],[title]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='pollcomment'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('pollcomment', 'action_postcomment.gif', '1', '1', '2500135', '2500136', '[username1],[displayname1],[username2],[displayname2],[comment],[id]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }
  
  
  
  //######### INSERT se_notifytypes
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='pollcomment'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes (
        notifytype_name,
        notifytype_desc,
        notifytype_icon,
        notifytype_url,
        notifytype_title
      ) VALUES (
        'pollcomment',
        '2500137',
        'action_postcomment.gif',
        'poll.php?user=%1\$s&poll_id=%2\$s',
        '2500138'
      )
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='pollcomment'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('pollcomment', '2500126', '2500127', '2500139', '2500140', '[displayname],[commenter],[link]')
    ";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE IF POLLS HAVE NEVER BEEN INSTALLED
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_poll_allow'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( !$column_info )
  {
    $sql = "
      ALTER TABLE se_levels 
      ADD COLUMN `level_poll_allow` int(1) NOT NULL default '7',
      ADD COLUMN `level_poll_entries` int(3) NOT NULL default '10',
      ADD COLUMN `level_poll_search` int(1) NOT NULL default '1',
      ADD COLUMN `level_poll_privacy` varchar(100) NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}',
      ADD COLUMN `level_poll_comments` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'
    ";
    
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  if( $column_info && strtoupper($column_info['Default'])!="7" )
  {
    $sql = "ALTER TABLE se_levels CHANGE `level_poll_allow` `level_poll_allow` TINYINT UNSIGNED NOT NULL default 7";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_levels SET level_poll_allow=7 WHERE level_poll_allow=1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_poll_privacy'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $column_info && strtoupper($column_info['Type'])!=="VARCHAR(100)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE {$column_info['Field']} {$column_info['Field']} VARCHAR(128) NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT level_id, {$column_info['Field']} FROM se_levels WHERE 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    while( $level_info = $database->database_fetch_assoc($resource) )
    {
      if( unserialize($level_info[$column_info['Field']]) ) continue;
      
      $sql = "UPDATE se_levels SET {$column_info['Field']}='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}' WHERE level_id='{$level_info['level_id']}' LIMIT 1";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_poll_comments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $column_info && strtoupper($column_info['Type'])!=="VARCHAR(100)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE {$column_info['Field']} {$column_info['Field']} VARCHAR(128) NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT level_id, {$column_info['Field']} FROM se_levels WHERE 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    while( $level_info = $database->database_fetch_assoc($resource) )
    {
      if( unserialize($level_info[$column_info['Field']]) ) continue;
      
      $sql = "UPDATE se_levels SET {$column_info['Field']}='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}' WHERE level_id='{$level_info['level_id']}' LIMIT 1";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_poll'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE `$database_name`.`se_settings` ADD COLUMN `setting_permission_poll` TINYINT UNSIGNED default 1";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_poll_html'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE `$database_name`.`se_settings` ADD COLUMN `setting_poll_html`       TEXT NULL";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "UPDATE se_settings SET setting_poll_html='a,b,br,font,i,img'";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_pollcomment'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_usersettings ADD COLUMN `usersetting_notify_pollcomment` TINYINT NOT NULL default 1";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=2500001 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* GENERAL */
        (2500001, 1, 'Poll Settings', ''),
        (2500002, 1, 'View Polls', ''),
        (2500003, 1, 'Global Poll Settings', ''),
        (2500004, 1, 'Poll Settings', ''),
        (2500005, 1, 'Polls', 'profile_poll')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=2500006 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* admin_levels_pollsettings */
        (2500006, 1, 'If you have allowed users to have create polls, you can adjust their details from this page.', 'admin_levels_pollsettings'),
        (2500007, 1, 'Allow polls?', 'admin_levels_pollsettings'),
        (2500008, 1, 'Do you want to let users create polls? If set to no, all other settings on this page will not apply.', 'admin_levels_pollsettings'),
        
        (2500011, 1, 'Polls Per Page', 'admin_levels_pollsettings'),
        (2500012, 1, 'How many polls will be shown per page? (Enter a number between 1 and 999)', 'admin_levels_pollsettings'),
        (2500013, 1, '%1\$s polls per page', 'admin_levels_pollsettings'),
        (2500014, 1, 'Poll Privacy Options', 'admin_levels_pollsettings'),
        (2500015, 1, 'Search Privacy Options', 'admin_levels_pollsettings'),
        (2500016, 1, 'If you enable this feature, users will be able to exclude their polls from search results. Otherwise, all polls will be included in search results.', 'admin_levels_pollsettings'),
        (2500017, 1, 'Yes, allow users to exclude their polls from search results.', 'admin_levels_pollsettings'),
        (2500018, 1, 'No, force all polls to be included in search results.', 'admin_levels_pollsettings'),
        (2500019, 1, 'Poll Privacy', 'admin_levels_pollsettings'),
        (2500020, 1, 'Your users can choose from any of the options checked below when they decide who can see their polls. These options appear on your users\' \"create poll\" and \"edit poll\" pages. If you do not check any options, everyone will be allowed to view polls.', 'admin_levels_pollsettings'),
        (2500021, 1, 'Poll Comment Options', 'admin_levels_pollsettings'),
        (2500022, 1, 'Your users can choose from any of the options checked below when they decide who can post comments on their polls. If you do not check any options, everyone will be allowed to post comments on polls.', 'admin_levels_pollsettings'),
        
        /* admin_poll */
        (2500023, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) view polls on your social network. If you have given them the option, your users will be able to make their polls private even though you have made them publically viewable here. For more permissions settings, please visit the General Settings page.', 'admin_poll'),
        (2500024, 1, 'Yes, public visitors can view polls if allowed by the poll creators.', 'admin_poll'),
        (2500025, 1, 'No, public visitors can never view polls.', 'admin_poll'),
        (2500026, 1, 'Allowing users to create polls adds some extra interactivity and fun to your social network.<br />For more poll-related settings, see the <a href=\"admin_levels.php\">user levels</a> area.', 'admin_poll'),
        
        /* poll */
        (2500027, 1, '<a href=\"%2\$s\">%1\$s</a>\'s <a href=\"%3\$s\">polls</a>', 'poll, polls'),
        (2500028, 1, '%1\$s votes', 'poll, polls, profile_poll, user_poll, user_poll_browse'),
        (2500029, 1, 'Created on %1\$s', 'poll, polls'),
        (2500030, 1, 'Vote', 'poll, polls'),
        (2500031, 1, 'or', 'poll, polls'),
        (2500032, 1, 'View Results', 'poll, polls'),
        (2500033, 1, 'Back to %1\$s\'s polls', 'poll, polls'),
        (2500034, 1, 'back to options', 'poll, polls'),
        
        /* polls */
        (2500035, 1, 'viewing poll %1\$d of %2\$d', 'polls'),
        (2500036, 1, 'viewing polls %1\$d-%2\$d of %3\$d', 'polls'),
        
        /* user_poll */
        (2500037, 1, 'My Polls', 'user_poll, user_poll_browse, user_poll_delete, user_poll_edit'),
        (2500038, 1, 'Browse Other Polls', 'user_poll, user_poll_browse, user_poll_delete, user_poll_edit'),
        (2500039, 1, 'Any polls you\'ve created in the past can be managed here.', 'user_poll'),
        (2500040, 1, 'Create New Poll', 'user_poll'),
        (2500041, 1, 'Search My Polls', 'user_poll'),
        (2500042, 1, 'Search my polls for:', 'user_poll'),
        (2500043, 1, 'No polls could be found with your search criteria.', 'user_poll, user_poll_browse'),
        (2500044, 1, 'You do not currently have any polls. <a href=\"user_poll_new.php\">Click here</a> to create one!', 'user_poll'),
        (2500045, 1, 'Poll Name', 'user_poll'),
        (2500046, 1, 'Open Poll', 'user_poll'),
        (2500047, 1, 'Close Poll', 'user_poll'),
        (2500048, 1, 'Delete Poll', 'user_poll'),
        
        /* user_poll */
        (2500049, 1, 'Browse Polls', 'user_poll_browse'),
        (2500050, 1, 'Check out some of the polls other people have created.', 'user_poll_browse'),
        (2500051, 1, 'Search polls for:', 'user_poll_browse'),
        (2500052, 1, 'Most Recent', 'user_poll_browse'),
        (2500053, 1, 'Most Votes', 'user_poll_browse'),
        (2500054, 1, 'No polls were found. <a href=\"user_poll_new.php\">Click here</a> to create one!', 'user_poll_browse'),
        (2500055, 1, 'Delete Poll?', 'user_poll'),
        (2500056, 1, 'Are you sure you want to delete this poll?', 'user_poll, admin_viewpolls'),
        
        /* user_poll_edit */
        (2500057, 1, 'Edit Poll', 'user_poll_edit'),
        (2500058, 1, 'Edit the details of this poll below.', 'user_poll_edit'),
        (2500059, 1, 'Title:', 'user_poll_edit, user_poll_new'),
        (2500060, 1, 'Description:', 'user_poll_edit, user_poll_new'),
        (2500061, 1, 'Show Privacy Settings', 'user_poll_edit, user_poll_new'),
        (2500062, 1, 'Include this poll in search results?', 'user_poll_edit, user_poll_new'),
        (2500063, 1, 'Yes, include this poll in search results.', 'user_poll_edit, user_poll_new'),
        (2500064, 1, 'No, exclude this poll from search results.', 'user_poll_edit, user_poll_new'),
        (2500065, 1, 'Who can see this poll?', 'user_poll_edit, user_poll_new'),
        (2500066, 1, 'Who can comment on this poll?', 'user_poll_edit, user_poll_new'),
        (2500067, 1, 'This poll has been opened for voting.', 'user_poll_edit'),
        (2500068, 1, 'This poll has been closed for voting. No one will be able to vote on it unless it is re-opened.', 'user_poll_edit'),
        
        /* user_poll_edit_comments */
        (2500069, 1, 'Poll Details', 'user_poll_edit, user_poll_edit_comments'),
        (2500070, 1, 'Back to My Polls', 'user_poll_edit, user_poll_edit_comments'),
        (2500071, 1, 'Poll Comments:', 'user_poll_edit_comments'),
        (2500072, 1, 'The comments below have been written about this poll by other people. To delete comments, click their checkboxes and then click the \"Delete Selected\" button below the comment list.', 'user_poll_edit_comments'),
        (2500073, 1, 'viewing comments %1\$d-%2\$d of %3\$d', 'user_poll_edit_comments'),
        (2500074, 1, 'No comments have been posted about this poll.', 'user_poll_edit_comments'),
        
        /* user_poll_new */
        (2500075, 1, 'Create New Poll', 'user_poll_new'),
        (2500076, 1, 'Give your new poll a title and description. If you\'re asking a question with this poll, you should put it in your title.', 'user_poll_new'),
        (2500077, 1, 'Create Poll', 'user_poll_new'),
        (2500078, 1, 'Poll Options:', 'user_poll_new'),
        (2500079, 1, 'Option', 'user_poll_new'),
        (2500080, 1, 'Add Option', 'user_poll_new'),
        (2500081, 1, 'Create Poll', 'user_poll_new'),
        
        /* admin_viewpolls */
        (2500082, 1, 'Title', 'admin_viewpolls'),
        (2500083, 1, 'Creator', 'admin_viewpolls'),
        (2500084, 1, '%1\$d polls found', 'admin_viewpolls'),
        (2500085, 1, 'Votes', 'admin_viewpolls'),
        (2500086, 1, 'Created', 'admin_viewpolls'),
        (2500087, 1, 'Options', 'admin_viewpolls'),
        (2500088, 1, 'view', 'admin_viewpolls'),
        
        /* MISC */
        (2500089, 1, 'Your polls per page field must contain an integer between 1 and 999.', 'admin_levels_pollsettings'),
        (2500090, 1, 'There was an error while attemting to vote on this poll.', 'poll_vote'),
        (2500091, 1, 'You must be a registered user to vote on this poll.', 'poll_vote'),
        (2500092, 1, 'Please select an option before voting.', 'poll_vote'),
        (2500093, 1, 'You have already voted on this poll.', 'poll_vote'),
        (2500094, 1, 'Sorry, this poll has been closed for voting.', 'poll_vote'),
        (2500095, 1, 'viewing comment %1\$d of %2\$d', 'user_poll_edit_comments'),
        (2500096, 1, 'select all comments', 'user_poll_edit_comments'),
        (2500098, 1, 'You cannot create more than twenty options.', 'user_poll_new'),
        (2500099, 1, 'This page lists all of the polls that users have created on your social network. You can use this page to monitor these polls and delete offensive or unwanted material if necessary. Entering criteria into the filter fields will help you find specific polls. Leaving the filter fields blank will show all the polls on your social network.', 'admin_viewpolls'),
        
        /* browse_polls */
        (2500100, 1, 'Browse Polls', 'browse_polls'),
        (2500101, 1, 'View:', 'browse_polls'),
        (2500102, 1, 'Order:', 'browse_polls'),
        (2500103, 1, 'Everyone\'s Polls', 'browse_polls'),
        (2500104, 1, 'My Friends\' Polls', 'browse_polls'),
        (2500105, 1, 'Recently Created', 'browse_polls'),
        (2500106, 1, 'Most Voted', 'browse_polls'),
        (2500107, 1, 'Most Viewed', 'browse_polls'),
        (2500108, 1, 'Created %1\$s by <a href=\"%2\$s\">%3\$s</a>', 'browse_polls'),
        
        /* MISC */
        (2500109, 1, 'HTML in Polls', 'admin_poll'),
        (2500110, 1, 'By default, the user may not enter any HTML tags into a poll\'s description or option labels. If you want to allow specific tags, you can enter them below (separated by commas). Example: a, b, br, font, i, img, hr', 'admin_poll'),
        
        /* search */
        (2500111, 1, '%1\$d polls', 'search'),
        (2500112, 1, 'Poll: %1\$s', 'search'),
        (2500113, 1, 'Poll created by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', 'search'),
        
        /* poll_ajax */
        (2500114, 1, 'There was an error processing your request.', 'poll_ajax'),
        (2500115, 1, 'Please wait until the previous operation is complete until starting another.', 'poll_ajax'),
        
        /* user_poll */
        (2500116, 1, 'Created:', 'user_poll'),
        (2500117, 1, 'Views:', 'user_poll'),
        (2500118, 1, 'Votes:', 'user_poll'),
        (2500119, 1, 'Comments:', 'user_poll'),
        (2500120, 1, 'Viewable By:', 'user_poll'),
        (2500121, 1, 'View Poll', 'user_poll'),
        (2500122, 1, '%1\$d views', 'user_poll'),
        
        /* user_poll_new */
        (2500123, 1, 'Please provide a title for this new poll.', 'user_poll_new'),
        (2500124, 1, 'Please provide at least two possible options for this poll.', 'user_poll_new'),
        (2500125, 1, 'Please create a poll with %1\$d options or less.', 'user_poll_new')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=2500126 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* GENERAL */
        (2500126, 1, 'New Poll Comment Email', ''),
        (2500127, 1, 'This is the email that gets sent to a user when a new comment is posted on one of their polls.', '')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=2500128 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* GENERAL */
        (2500128, 1, 'Polls: %1\$d polls', 'home'),
        (2500129, 1, 'Poll Comments: %1\$d comments', 'home'),
        (2500130, 1, 'Poll Votes: %1\$d votes', 'home')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=2500131 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (2500131, 1, 'Creating a Poll', 'actiontypes'),
        (2500132, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> created a new poll: <a href=\"poll.php?user=%1\$s&poll_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (2500133, 1, 'Voting on a Poll', 'actiontypes'),
        (2500134, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> voted on a poll: <a href=\"poll.php?user=%3\$s&poll_id=%4\$s\">%5\$s</a>', 'actiontypes'),
        (2500135, 1, 'Commenting on a Poll', 'actiontypes'),
        (2500136, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on <a href=\"profile.php?user=%3\$s\">%4\$s</a>\'s <a href=\"poll.php?user=%3\$s&poll_id=%6\$s\">poll</a>:<div class=\"recentaction_div\">%5\$s</div>', 'actiontypes'),
        (2500137, 1, 'When I receive a new poll comment.', 'notifytypes'),
        (2500138, 1, '%1\$d New Poll Comment(s): %2\$s', 'notifytypes'),
        (2500139, 1, 'New Poll Comment', 'systememails'),
        (2500140, 1, 'Hello %1\$s,\n\nA new comment has been posted on one of your polls by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=2500141 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (2500141, 1, 'Users may view, vote on, and create polls.', 'admin_levels_pollsettings'),
        (2500142, 1, 'Users may view and vote on polls.', 'admin_levels_pollsettings'),
        (2500143, 1, 'Users may only view polls.', 'admin_levels_pollsettings'),
        (2500144, 1, 'Users may not use polls.', 'admin_levels_pollsettings'),
        (2500145, 1, 'Private Poll', 'poll'),
        (2500146, 1, 'You do not have permission to view this poll.', 'poll')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
  ################ UPGRADE EXISTING POLLS' OPTIONS, ANSWERS, AND VOTED COLUMNS
  $sql = "SELECT * FROM se_polls";
  $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  $already_upgraded = FALSE;
  
  while( !$already_upgraded && ($poll_info=$database->database_fetch_assoc($resource)) )
  {
    // CHECK UPGRADED STATUS
    if( unserialize($poll_info['poll_options']) ) continue;
    
    // OLD->ARRAY
    $poll_info['poll_options']  = explode("[!]", $poll_info['poll_options']);
    $poll_info['poll_answers']  = explode("[!]", $poll_info['poll_answers']);
    $poll_info['poll_voted']    = explode(",",   $poll_info['poll_voted']  );
    
    // ARRAY->NEW
    $poll_options = serialize($poll_info['poll_options']);
    $poll_answers = serialize($poll_info['poll_answers']);
    $poll_voted   = serialize($poll_info['poll_voted']  );
    
    $sql = "
      UPDATE
        se_polls
      SET
        poll_options='{$poll_options}',
        poll_answers='{$poll_answers}',
        poll_voted='{$poll_voted}'
      WHERE
        poll_id='{$poll_info['poll_id']}'
      LIMIT
        1
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
  ################ UPGRADE EXISTING POLLS' PRIVACY OPTIONS
  if( !empty($plugin_info) && version_compare($plugin_info['plugin_version'], '3.00', '<') )
  {
    $database->database_query("UPDATE se_polls SET poll_privacy='63'  WHERE poll_privacy='0' ") or die($database->database_error()." View Privacy Query #1");
    $database->database_query("UPDATE se_polls SET poll_privacy='31'  WHERE poll_privacy='1' ") or die($database->database_error()." View Privacy Query #2");
    $database->database_query("UPDATE se_polls SET poll_privacy='15'  WHERE poll_privacy='2' ") or die($database->database_error()." View Privacy Query #3");
    $database->database_query("UPDATE se_polls SET poll_privacy='7'   WHERE poll_privacy='3' ") or die($database->database_error()." View Privacy Query #4");
    $database->database_query("UPDATE se_polls SET poll_privacy='3'   WHERE poll_privacy='4' ") or die($database->database_error()." View Privacy Query #5");
    $database->database_query("UPDATE se_polls SET poll_privacy='1'   WHERE poll_privacy='5' ") or die($database->database_error()." View Privacy Query #6");
    $database->database_query("UPDATE se_polls SET poll_privacy='0'   WHERE poll_privacy='6' ") or die($database->database_error()." View Privacy Query #7");
    
    $database->database_query("UPDATE se_polls SET poll_comments='63' WHERE poll_comments='0'") or die($database->database_error()." Comment Privacy Query #1");
    $database->database_query("UPDATE se_polls SET poll_comments='31' WHERE poll_comments='1'") or die($database->database_error()." Comment Privacy Query #2");
    $database->database_query("UPDATE se_polls SET poll_comments='15' WHERE poll_comments='2'") or die($database->database_error()." Comment Privacy Query #3");
    $database->database_query("UPDATE se_polls SET poll_comments='7'  WHERE poll_comments='3'") or die($database->database_error()." Comment Privacy Query #4");
    $database->database_query("UPDATE se_polls SET poll_comments='3'  WHERE poll_comments='4'") or die($database->database_error()." Comment Privacy Query #5");
    $database->database_query("UPDATE se_polls SET poll_comments='1'  WHERE poll_comments='5'") or die($database->database_error()." Comment Privacy Query #6");
    $database->database_query("UPDATE se_polls SET poll_comments='0'  WHERE poll_comments='6'") or die($database->database_error()." Comment Privacy Query #7");
  }
}
	
?>