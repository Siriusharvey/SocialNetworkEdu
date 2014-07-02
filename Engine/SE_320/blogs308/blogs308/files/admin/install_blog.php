<?php

/* $Id: install_blog.php 241 2009-11-14 02:48:21Z phil $ */

$plugin_name = "Blog Plugin";
$plugin_version = "3.08";
$plugin_type = "blog";
$plugin_desc = "This plugin gives each of your users their own personal blog. This is a great way to encourage content generation and personal expression. Blogs are also an excellent way to improve the search engine visibility of your social network.";
$plugin_icon = "blog_blog16.gif";
$plugin_menu_title = "1500001";
$plugin_pages_main = "1500002<!>blog_blog16.gif<!>admin_viewblogs.php<~!~>1500003<!>blog_adminsettings16.gif<!>admin_blog.php<~!~>";
$plugin_pages_level = "1500004<!>admin_levels_blogsettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/blog/([0-9]+)/?\$ \$server_info/blog.php?user=\$1&blogentry_id=\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/blog/([^/]+)?\$ \$server_info/blog.php?user=\$1\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/blog/?\$ \$server_info/blog.php?user=\$1 [L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/trackback/([0-9]+)/?\$ \$server_info/blog_ajax.php?task=trackback&user=\$1&blogentry_id=\$2 [L]
";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;




if( $install=="blog" )
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
  $sql = "SELECT NULL FROM se_plugins WHERE plugin_type='$plugin_type'";
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
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
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
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_blogcomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogcomments'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogcomments`
      (
        `blogcomment_id`              INT     UNSIGNED  NOT NULL auto_increment,
        `blogcomment_blogentry_id`    INT     UNSIGNED  NOT NULL default '0',
        `blogcomment_authoruser_id`   INT     UNSIGNED  NOT NULL default '0',
        `blogcomment_date`            BIGINT            NOT NULL default '0',
        `blogcomment_body`            TEXT                  NULL,
        PRIMARY KEY  (`blogcomment_id`),
        KEY `INDEX` (`blogcomment_blogentry_id`,`blogcomment_authoruser_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogcomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_blogcomments` LIKE 'blogcomment_body'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogcomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  
  //######### CREATE se_blogentries
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogentries'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogentries`
      (
        `blogentry_id`              INT           UNSIGNED  NOT NULL auto_increment,
        `blogentry_user_id`         INT           UNSIGNED  NOT NULL default 0,
        `blogentry_blogentrycat_id` INT           UNSIGNED  NOT NULL default 0,
        `blogentry_date`            BIGINT                  NOT NULL default 0,
        `blogentry_views`           INT           UNSIGNED  NOT NULL default 0,
        `blogentry_title`           VARCHAR(128)            NOT NULL default '',
        `blogentry_body`            TEXT                        NULL,
        `blogentry_search`          TINYINT       UNSIGNED  NOT NULL default 0,
        `blogentry_privacy`         TINYINT       UNSIGNED  NOT NULL default 0,
        `blogentry_comments`        TINYINT       UNSIGNED  NOT NULL default 0,
        `blogentry_trackbacks`      TEXT                        NULL,
        `blogentry_totalcomments`   SMALLINT      UNSIGNED  NOT NULL default 0,
        `blogentry_totaltrackbacks` SMALLINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`blogentry_id`),
        KEY `LISTBYDATE` (`blogentry_user_id`, `blogentry_privacy`, `blogentry_date`),
        KEY `LISTBYCAT`  (`blogentry_user_id`, `blogentry_blogentrycat_id`, `blogentry_privacy`, `blogentry_date`),
        FULLTEXT `SEARCH` (`blogentry_title`, `blogentry_body`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Add trackback column
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_blogentries` LIKE 'blogentry_trackbacks'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_blogentries ADD COLUMN `blogentry_trackbacks` TEXT NULL";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_blogentries` LIKE 'blogentry_totalcomments'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_blogentries ADD COLUMN `blogentry_totalcomments` SMALLINT UNSIGNED  NOT NULL default 0";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT blogentry_id FROM se_blogentries WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_blogentries SET blogentry_totalcomments=(SELECT COUNT(blogcomment_id) FROM se_blogcomments WHERE blogcomment_blogentry_id='{$result['blogentry_id']}') WHERE blogentry_id='{$result['blogentry_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_blogentries` LIKE 'blogentry_totaltrackbacks'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_blogentries ADD COLUMN `blogentry_totaltrackbacks` SMALLINT UNSIGNED  NOT NULL default 0";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    if( $database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_blogtrackbacks'")) )
    {
      $sql = "SELECT blogentry_id FROM se_blogentries WHERE 1";
      $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
      while( $result = $database->database_fetch_assoc($resource) )
      {
        $sql = "UPDATE se_blogentries SET blogentry_totaltrackbacks=(SELECT COUNT(blogtrackback_id) FROM se_blogtrackbacks WHERE blogtrackback_blogentry_id='{$result['blogentry_id']}') WHERE blogentry_id='{$result['blogentry_id']}' LIMIT 1";
        $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
      }
    }
  }
  
  
  
  
  // Ensure utf8 on blogentry_title
  $sql = "SHOW FULL COLUMNS FROM `se_blogentries` LIKE 'blogentry_title'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogentries MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogentry_body
  $sql = "SHOW FULL COLUMNS FROM `se_blogentries` LIKE 'blogentry_body'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogentries MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  // Change body to longtext
  if( $result && strtoupper($result['Type'])=="TEXT" )
  {
    $sql = "ALTER TABLE se_blogentries MODIFY {$result['Field']} LONGTEXT CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation} NULL";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogentry_trackbacks
  $sql = "SHOW FULL COLUMNS FROM `se_blogentries` LIKE 'blogentry_trackbacks'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogentries MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Add full text index on blogentry_title and blogentry_body (should be after ensuring they are in utf8)
  $sql = "SHOW FULL COLUMNS FROM `se_blogentries` LIKE 'blogentry_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && !$result['Key'] )
  {
    $sql = "ALTER TABLE `se_blogentries` ADD FULLTEXT `SEARCH` (`blogentry_title`, `blogentry_body`)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add an index on the date column
  $sql = "SHOW FULL COLUMNS FROM `se_blogentries` LIKE 'blogentry_date'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && !$result['Key'] )
  {
    $sql = "ALTER TABLE `se_blogentries` ADD INDEX `blogentry_date` (`blogentry_date`)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add an index for browsing by category/date
  $sql = "SHOW FULL COLUMNS FROM `se_blogentries` LIKE 'blogentry_user_id'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && !$result['Key'] )
  {
    $sql = "ALTER TABLE `se_blogentries` ADD INDEX `LISTBYCAT`  (`blogentry_user_id`, `blogentry_blogentrycat_id`, `blogentry_privacy`, `blogentry_date`)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "ALTER TABLE `se_blogentries` ADD INDEX `LISTBYDATE` (`blogentry_user_id`, `blogentry_privacy`, `blogentry_date`)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_blogentrycats
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogentrycats'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogentrycats`
      (
        `blogentrycat_id`               INT           UNSIGNED  NOT NULL auto_increment,
        `blogentrycat_user_id`          INT           UNSIGNED  NOT NULL default 0,
        `blogentrycat_title`            VARCHAR(128)            NOT NULL default '',
        `blogentrycat_languagevar_id`   INT           UNSIGNED  NOT NULL default 0,
        `blogentrycat_parentcat_id`     INT           UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`blogentrycat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_blogentrycats` LIKE 'blogentrycat_languagevar_id'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_blogentrycats 
      ADD COLUMN `blogentrycat_languagevar_id`   INT           UNSIGNED  NOT NULL default 0,
      ADD COLUMN `blogentrycat_parentcat_id`     INT           UNSIGNED  NOT NULL default 0
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_blogentrycats` LIKE 'blogentrycat_user_id'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_blogentrycats ADD COLUMN `blogentrycat_user_id`   INT           UNSIGNED  NOT NULL default 0";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Create language variables for categories
  $sql = "SELECT * FROM se_blogentrycats WHERE blogentrycat_languagevar_id=0 && blogentrycat_user_id=0";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  while( $result=$database->database_fetch_assoc($resource) )
  {
    $lvar_id = SE_Language::edit(0, $result['blogentrycat_title'], NULL, LANGUAGE_INDEX_SUBNETS);
    $sql = "UPDATE se_blogentrycats SET blogentrycat_languagevar_id='{$lvar_id}' WHERE blogentrycat_id='{$result['blogentrycat_id']}' LIMIT 1";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogentrycat_title
  $sql = "SHOW FULL COLUMNS FROM `se_blogentrycats` LIKE 'blogentrycat_title'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogentrycats MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_blogpings
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogpings'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogpings`
      (
        `blogping_id`           INT           UNSIGNED  NOT NULL auto_increment,
        `blogping_blogentry_id` INT           UNSIGNED  NOT NULL default 0,
        `blogping_target_url`   TEXT                        NULL,
        `blogping_source_url`   TEXT                        NULL,
        `blogping_status`       TINYINT       UNSIGNED  NOT NULL default 0,
        `blogping_type`         TINYINT       UNSIGNED  NOT NULL default 0,
        `blogping_ip`           VARCHAR(16)             NOT NULL default '',
        PRIMARY KEY  (`blogping_id`),
        KEY `INDEX` (`blogping_status`, `blogping_type`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_blogstyles
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogstyles'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogstyles`
      (
        `blogstyle_id`        INT           UNSIGNED  NOT NULL auto_increment,
        `blogstyle_user_id`   INT           UNSIGNED  NOT NULL default 0,
        `blogstyle_css`       TEXT                        NULL,
        PRIMARY KEY  (`blogstyle_id`),
        KEY `INDEX` (`blogstyle_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogstyle_css
  $sql = "SHOW FULL COLUMNS FROM `se_blogstyles` LIKE 'blogstyle_css'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogstyles MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_blogsubscriptions
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogsubscriptions'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogsubscriptions`
      (
        `blogsubscription_id`           INT           UNSIGNED  NOT NULL auto_increment,
        `blogsubscription_user_id`      INT           UNSIGNED  NOT NULL default 0,
        `blogsubscription_owner_id`     INT           UNSIGNED  NOT NULL default 0,
        `blogsubscription_date`         BIGINT                  NOT NULL default 0,
        PRIMARY KEY  (`blogsubscription_id`),
        UNIQUE KEY `INDEX` (`blogsubscription_user_id`, `blogsubscription_owner_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### CREATE se_blogtrackbacks
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_blogtrackbacks'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_blogtrackbacks`
      (
        `blogtrackback_id`            INT           UNSIGNED  NOT NULL auto_increment,
        `blogtrackback_blogentry_id`  INT           UNSIGNED  NOT NULL default 0,
        `blogtrackback_name`          VARCHAR(255)            NOT NULL default '',
        `blogtrackback_title`         VARCHAR(255)            NOT NULL default '',
        `blogtrackback_excerpt`       TEXT                        NULL,
        `blogtrackback_excerpthash`   VARCHAR(32)             NOT NULL default '',
        `blogtrackback_url`           TEXT                        NULL,
        `blogtrackback_ip`            VARCHAR(16)             NOT NULL default '',
        `blogtrackback_date`          BIGINT                  NOT NULL default 0,
        PRIMARY KEY  (`blogtrackback_id`),
        KEY `INDEX` (`blogtrackback_blogentry_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogtrackback_title
  $sql = "SHOW FULL COLUMNS FROM `se_blogtrackbacks` LIKE 'blogtrackback_title'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogtrackbacks MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  // Ensure utf8 on blogtrackback_excerpt
  $sql = "SHOW FULL COLUMNS FROM `se_blogtrackbacks` LIKE 'blogtrackback_excerpt'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  $result = $database->database_fetch_assoc($resource);

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_blogtrackbacks MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT se_urls
  $sql = "SELECT NULL FROM se_urls WHERE url_file='blog'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Blog URL', 'blog', 'blog.php?user=\$user', '\$user/blog/')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT NULL FROM se_urls WHERE url_file='blog_entry'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Blog Entry URL', 'blog_entry', 'blog.php?user=\$user&blogentry_id=\$id1', '\$user/blog/\$id1/')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT NULL FROM se_urls WHERE url_file='blog_trackback'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_urls
        (url_title, url_file, url_regular, url_subdirectory)
      VALUES
        ('Blog Trackback URL', 'blog_trackback', 'blog_ajax.php?task=trackback&user=\$user&blogentry_id=\$id1', '\$user/trackback/\$id1/')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ALTER ROWS se_urls
  $sql = "UPDATE se_urls SET url_regular='blog.php?user=\$user&blogentry_id=\$id1' WHERE url_regular='blog_entry.php?user=\$user&blogentry_id=\$id1'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='postblog'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('postblog', 'blog_action_postblog.gif', '1', '1', '1500152', '1500153', '[username],[displayname],[id],[title]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='blogcomment'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('blogcomment', 'action_postcomment.gif', '1', '1', '1500154', '1500155', '[username1],[displayname1],[username2],[displayname2],[comment],[entryid]', '0')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }
  
  
  
  //######### INSERT se_notifytypes
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='blogcomment'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('blogcomment', '1500156', 'action_postcomment.gif', 'blog.php?user=%1\$s&blogentry_id=%2\$s', '1500157')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='newblogsubscriptionentry'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('newblogsubscriptionentry', '1500158', 'action_postcomment.gif', 'blog.php?user=%1\$s&blogentry_id=%2\$s', '1500159')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ALTER ROWS se_notifytypes
  $sql = "UPDATE se_notifytypes SET notifytype_url='blog.php?user=%1\$s&blogentry_id=%2\$s' WHERE notifytype_url='blog_entry.php?user=%1\$s&blogentry_id=%2\$s'";
  $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  
  //######### ADD COLUMNS se_levels
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_entries'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_levels 
      ADD COLUMN `level_blog_view`      TINYINT       UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_blog_create`    TINYINT       UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_blog_entries`   SMALLINT      UNSIGNED  NOT NULL default '20',
      ADD COLUMN `level_blog_style`     TINYINT       UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_blog_search`    TINYINT       UNSIGNED  NOT NULL default 1,
      ADD COLUMN `level_blog_privacy`   VARCHAR(128)            NOT NULL default 'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}',
      ADD COLUMN `level_blog_comments`  VARCHAR(128)            NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  // ALTER COLUMNS se_levels
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_privacy'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $column_info=$database->database_fetch_assoc($resource) )
  {
    $field_name = $column_info['Field'];
    $field_type = $column_info['Type'];
    $field_default = $column_info['Default'];
    if( $field_type=="varchar(10)" )
    {
      $database->database_query("ALTER TABLE se_levels CHANGE level_blog_privacy level_blog_privacy varchar(100) NOT NULL default ''");
      $database->database_query("UPDATE se_levels SET level_blog_privacy='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'");
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_comments'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $column_info=$database->database_fetch_assoc($resource) )
  {
    $field_name = $column_info['Field'];
    $field_type = $column_info['Type'];
    $field_default = $column_info['Default'];
    if( $field_type=="varchar(10)" )
    {
      $database->database_query("ALTER TABLE se_levels CHANGE level_blog_comments level_blog_comments varchar(100) NOT NULL default ''");
      $database->database_query("UPDATE se_levels SET level_blog_comments='a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}'");
    }
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_trackbacks_allow'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_levels
      ADD COLUMN `level_blog_trackbacks_allow`  TINYINT NOT NULL default 1,
      ADD COLUMN `level_blog_trackbacks_detect` TINYINT NOT NULL default 1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_html'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_blog_html`  TEXT NULL";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "UPDATE se_levels SET level_blog_html='strong,b,em,i,u,strike,sub,sup,p,div,pre,address,h1,h2,h3,h4,h5,h6,span,ol,li,ul,a,img,embed,br,hr'";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_view'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_blog_view` TINYINT UNSIGNED NOT NULL default 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_blog_create` TINYINT UNSIGNED NOT NULL default 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  // ALTER COLUMNS/ROWS se_levels
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_allow'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) )
  {
    $sql = "UPDATE se_levels SET level_blog_view=level_blog_allow, level_blog_create=level_blog_allow";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "ALTER TABLE se_levels DROP `level_blog_allow`";
    $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_blog_category_create'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_blog_category_create` TINYINT NOT NULL default 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_blog'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_settings ADD COLUMN `setting_permission_blog` TINYINT NOT NULL default 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='blogcomment'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('blogcomment', '1500005', '1500006', '1500160', '1500161', '[displayname],[commenter],[link]')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='blogtrackback'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('blogtrackback', '1500005', '1500006', '1500162', '1500163', '[displayname],[trackbackblogname],[trackbackblogurl],[link]')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='newblogsubscriptionentry'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('newblogsubscriptionentry', '1500005', '1500006', '1500164', '1500165', '[displayname],[blogposter],[link]')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_blogcomment'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_usersettings ADD COLUMN `usersetting_notify_blogcomment` int(1) NOT NULL default 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_newblogsubscriptionentry'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_usersettings
      ADD COLUMN `usersetting_notify_blogtrackback`             TINYINT NOT NULL default 1,
      ADD COLUMN `usersetting_notify_newblogsubscriptionentry`  TINYINT NOT NULL default 1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500001 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1500001, 1, 'Blog Settings', ''),
        (1500002, 1, 'View Blog Entries', ''),
        (1500003, 1, 'Global Blog Settings', ''),
        (1500004, 1, 'Blog Settings', ''),
        (1500005, 1, 'New Blog Comment Email', ''),
        (1500006, 1, 'This is the email that gets sent to a user when a new comment is posted on one of their blog entries.', ''),
        (1500007, 1, 'Blog', '')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500008 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* blog_ajax */
        (1500008, 1, 'A valid entry ID must be specified.', 'blog_ajax'),
        (1500009, 1, 'A valid return URL must be specified.', 'blog_ajax'),
        (1500010, 1, 'The specified entry does not exist.', 'blog_ajax'),
        (1500011, 1, 'This trackback has already been received.', 'blog_ajax'),
        (1500012, 1, 'You cannot send more than one trackback every 15 seconds per IP Address.', 'blog_ajax'),
        (1500013, 1, 'An unknown error occurred, trackback was not added.', 'blog_ajax'),
        (1500014, 1, 'The trackback was received successfully.', 'blog_ajax'),
        
        /* blog */
        (1500015, 1, 'Untitled', 'blog'),
        (1500016, 1, 'Posted:', 'blog, profile_blog'),
        (1500017, 1, 'Comments:', 'blog, user_blog, user_blog_entry'),
        (1500018, 1, 'Trackbacks:', 'blog'),
        (1500019, 1, '%1\$d comment(s)', 'blog, user_blog, user_blog_entry'),
        (1500020, 1, '%1\$d trackback(s)', 'blog'),
        (1500021, 1, 'Comment', 'blog'),
        (1500022, 1, 'Trackback', 'blog'),
        (1500023, 1, '<b><a href=\"%2\$s\">%1\$s</a></b> has not posted any blog entries.', 'blog'),
        (1500024, 1, 'Back to %1\$s\'s blog', 'blog'),
        (1500025, 1, 'Trackbacks', 'blog'),
        (1500026, 1, 'report', 'blog'),
        (1500027, 1, 'Subscribe', 'blog'),
        (1500028, 1, 'Unsubscribe', 'blog'),
        (1500029, 1, 'Archive', 'blog'),
        (1500030, 1, 'Categories', 'blog'),
        
        /* browse_blogs */
        (1500031, 1, 'Browse Blog Entries', 'browse_blogs'),
        (1500032, 1, 'View:', 'browse_blogs'),
        (1500033, 1, 'Order:', 'browse_blogs'),
        (1500034, 1, 'Category:', 'browse_blogs'),
        (1500035, 1, 'Uncategorized', 'browse_blogs'),
        (1500036, 1, 'Date Posted', 'browse_blogs'),
        (1500037, 1, 'Most Viewed', 'browse_blogs'),
        (1500038, 1, 'Most Commented', 'browse_blogs'),
        (1500039, 1, 'Posted %1\$s by <a href=\"%2\$s\">%3\$s</a>', 'browse_blogs'),
        (1500040, 1, 'Views:', 'browse_blogs'),
        (1500041, 1, '%1\$d views', 'browse_blogs'),
        (1500042, 1, '%1\$d comments', 'browse_blogs'),
        
        /* profile_blog */
        (1500043, 1, 'Blog Entries', 'profile_blog'),
        
        /* user_blog */
        (1500044, 1, 'My Blog Entries', 'user_blog'),
        (1500045, 1, 'Your blog is a place for you to share your personal thoughts and news with other people. Use this page to search for and manage blog entries that you have already written.', 'user_blog'),
        (1500046, 1, 'Compose New Entry', 'user_blog'),
        (1500047, 1, 'My Subscriptions', 'user_blog'),
        (1500048, 1, 'Search Entries', 'user_blog'),
        (1500049, 1, 'Search entries for:', 'user_blog'),
        (1500050, 1, 'No blog entries were found.', 'user_blog'),
        (1500051, 1, 'You do not have any blog entries. Click <a href=\"%1\$s\">here</a> to create one.', 'user_blog'),
        (1500052, 1, 'Title', 'user_blog'),
        
        /* user_blog_entry */
        (1500053, 1, 'Compose Blog Entry', 'user_blog_entry'),
        (1500054, 1, 'Create or edit your entry below, then click \"Post Entry\" to publish the entry on your blog.', 'user_blog_entry'),
        (1500055, 1, 'Back to My Blog', 'user_blog_entry, user_blog_settings, user_blog_subscriptions'),
        (1500056, 1, 'Title:', 'user_blog_entry'),
        (1500057, 1, 'Category:', 'user_blog_entry'),
        (1500058, 1, '<b><a href=\"%2\$s\">%1\$d comments</a></b> have been written about this entry.', 'user_blog_entry'),
        (1500059, 1, 'Show Entry Settings', 'user_blog_entry'),
        (1500060, 1, 'Hide Entry Settings', 'user_blog_entry'),
        (1500061, 1, 'Include this blog entry in search results?', 'user_blog_entry'),
        (1500062, 1, 'Yes, include this blog entry in search results.', 'user_blog_entry'),
        (1500063, 1, 'No, exclude this blog entry from search results.', 'user_blog_entry'),
        (1500064, 1, 'Trackback URLs', 'user_blog_entry'),
        (1500065, 1, 'Post Entry', 'user_blog_entry'),
        (1500066, 1, 'Preview Entry', 'user_blog_entry'),
        (1500067, 1, 'Preview:', 'user_blog_entry'),
        (1500068, 1, 'Return to Editing', 'user_blog_entry'),
        
        /* user_blog_settings */
        (1500069, 1, 'Edit blog settings such as your blog\'s style.', 'user_blog_settings'),
        (1500070, 1, 'Custom Blog Styles', 'user_blog_settings'),
        (1500071, 1, 'You can change the colors, fonts, and styles of your blog by adding CSS code below. The contents of the text area below will be output between &lt;style&gt; tags on your blog.', 'user_blog_settings'),
        (1500072, 1, 'Custom Blog Styles', 'user_blog_settings'),
        (1500073, 1, 'Blog Notifications', 'user_blog_settings'),
        (1500074, 1, 'Notify me by email when someone writes a comment on my blog entries.', 'user_blog_settings'),
        (1500075, 1, 'Notify me by email when someone responds through a trackback to one of my blog entries.', 'user_blog_settings'),
        (1500076, 1, 'Notify me by email when a new entry is posted on a blog I have subscribed to.', 'user_blog_settings'),
        
        /* user_blog_subscriptions */
        (1500077, 1, 'My Blog Subscriptions', 'user_blog_subscriptions'),
        (1500078, 1, 'You can view or manage the blogs you have subscribed to here.', 'user_blog_subscriptions'),
        (1500079, 1, 'You have not yet subscribed to any blogs.', 'user_blog_subscriptions'),
        (1500080, 1, 'Owner', 'admin_viewblogs, user_blog_subscriptions'),
        (1500081, 1, 'Last Post:', 'user_blog_subscriptions'),
        (1500082, 1, 'Most Recent Entry', 'user_blog_subscriptions'),
        (1500083, 1, 'view blog', 'user_blog_subscriptions'),
        
        /* admin_blog */
        (1500084, 1, 'This page contains general blog settings that affect your entire social network.', 'admin_blog'),
        (1500085, 1, 'Public Permission Defaults', 'admin_blog'),
        (1500086, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\'admin_general.php\'>General Settings</a> page.', 'admin_blog'),
        (1500087, 1, 'Yes, the public can view blogs unless they are made private.', 'admin_blog'),
        (1500088, 1, 'No, the public cannot view blogs.', 'admin_blog'),
        (1500089, 1, 'Blog Entry Categories', 'admin_blog'),
        (1500090, 1, 'If you want to allow your users to categorize their blog entries, create the categories below. This feature is useful if you want to list all your users\' blog entries that  If you have no categories, your users will not be given the option of assigning a blog category.', 'admin_blog'),
        (1500091, 1, 'Add Category', 'admin_blog'),
        
        /* admin_levels_blogsettings */
        (1500092, 1, 'If you have allowed users to have blogs, you can adjust their details from this page.', 'admin_levels_blogsettings'),
        
        (1500097, 1, 'Entries Per Page', 'admin_levels_blogsettings'),
        (1500098, 1, 'How many blog entries will be shown per page? (Enter a number between 1 and 999)', 'admin_levels_blogsettings'),
        (1500099, 1, '%1\$s entries per page', 'admin_levels_blogsettings'),
        (1500100, 1, 'Blog Privacy Options', 'admin_levels_blogsettings'),
        (1500101, 1, '<b>Search Privacy Options</b><br>If you enable this feature, users will be able to exclude their blog entries from search results. Otherwise, all blog entries will be included in search results.', 'admin_levels_blogsettings'),
        (1500102, 1, 'Yes, allow users to exclude their blog entries from search results.', 'admin_levels_blogsettings'),
        (1500103, 1, 'No, force all blog entries to be included in search results.', 'admin_levels_blogsettings'),
        (1500104, 1, '<b>Blog Entry Privacy</b><br>Your users can choose from any of the options checked below when they decide who can see their blog entries. These options appear on your users\' \"Add Entry\" and \"Edit Entry\" pages. If you do not check any options, everyone will be allowed to view blogs.', 'admin_levels_blogsettings'),
        (1500105, 1, '<b>Blog Comment Options</b><br>Your users can choose from any of the options checked below when they decide who can post comments on their entries. If you do not check any options, everyone will be allowed to post comments on entries.', 'admin_levels_blogsettings'),
        (1500106, 1, 'Allow Custom CSS Styles?', 'admin_levels_blogsettings'),
        (1500107, 1, 'If you enable this feature, your users will be able to customize the colors and fonts of their blogs by altering their CSS styles.', 'admin_levels_blogsettings'),
        (1500108, 1, 'Yes, enable custom CSS styles.', 'admin_levels_blogsettings'),
        (1500109, 1, 'No, disable custom CSS styles.', 'admin_levels_blogsettings'),
        (1500110, 1, 'The blog entries per page must be a number between 1 and 999.', 'admin_levels_blogsettings'),
        
        /* admin_viewblogs */
        (1500111, 1, 'Title', 'admin_viewblogs'),
        (1500112, 1, 'No entries were found.', 'admin_viewblogs'),
        (1500113, 1, '%1\$d blog entries found', 'admin_viewblogs'),
        (1500114, 1, 'Are you sure you want to delete this blog entry?', 'admin_viewblogs'),
        (1500115, 1, 'view', 'admin_viewblogs'),
        
        /* MISC */
        (1500116, 1, 'Everyone\'s Blog Entries', 'browse_blogs'),
        (1500117, 1, 'My Friends\' Blog Entries', 'browse_blogs'),
        (1500118, 1, 'Blog entry: %1\$s', 'search'),
        (1500119, 1, 'Blog entry posted by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', 'search'),
        (1500120, 1, '%1\$d blog entries', 'search'),
        (1500121, 1, 'View All Entries', 'blog'),
        (1500122, 1, 'Delete Blog Entry?', 'user_blog'),
        (1500123, 1, 'There was an error processing your request.', ''),
        (1500124, 1, '%1\$s\'s blog', 'header_global'),
        (1500125, 1, '%1\$s\'s blog - %2\$s', 'header_global'),
        (1500126, 1, 'Trackbacks are a way to notify other blogs that you\'ve linked to them. Add the trackback URLs here, one per line.', 'user_blog_entry'),
        (1500127, 1, 'Posted %1\$s', 'user_blog'),
        (1500128, 1, '[Create]', 'user_blog_entry'),
        (1500129, 1, 'unsubscribe', 'user_blog_subscriptions'),
        (1500130, 1, 'Edit Blog Entry', 'user_blog_entry'),
        (1500131, 1, 'This page lists all of the blog entries your users have posted. You can use this page to monitor these blogs and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific blog entries. Leaving the filter fields blank will show all the blog entries on your social network.', 'admin_viewblogs')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500132 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* MISC */
        (1500132, 1, 'Who can see this entry?', 'user_blog_entry'),
        (1500133, 1, 'Who can comment on this entry?', 'user_blog_entry'),
        (1500134, 1, 'HTML in Blog Entries', 'admin_levels_blogsettings'),
        (1500135, 1, 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: <i>b, img, a, embed, font<i>', 'admin_levels_blogsettings'),
        (1500136, 1, 'Trackbacks', 'admin_levels_blogsettings'),
        (1500137, 1, 'Trackbacks allow remote users using blogging software to post a response about a user\'s blog. This will be sent to your server and will show up when viewing the blog entry as a response. In turn, enabling this feature will allow local users to send a reponse to other blogging sites by providing a trackback URL.', 'admin_levels_blogsettings'),
        (1500138, 1, 'Yes, allow remote users to trackback to this user level\'s blog entries.', 'admin_levels_blogsettings'),
        (1500139, 1, 'No,  do not allow remote users to trackback.', 'admin_levels_blogsettings'),
        (1500140, 1, 'If you have enabled trackbacks, you can scan blog entries\' body text for URLs to attempt to send a trackback to.', 'admin_levels_blogsettings'),
        (1500141, 1, 'Yes, attempt to detect trackbacks from this user level\'s blog entries\' body text.', 'admin_levels_blogsettings'),
        (1500142, 1, 'No,  do not attempt to detect trackbacks.', 'admin_levels_blogsettings'),
        
        /* admin_levels_blogsettings */
        (1500143, 1, 'Allow Viewing and Creation of Blogs?', 'admin_levels_blogsettings'),
        (1500144, 1, 'Do you want to let users view blogs? If set to no, some other settings on this page may not apply.', 'admin_levels_blogsettings'),
        (1500145, 1, 'Yes, allow viewing and subscription of blogs.', 'admin_levels_blogsettings'),
        (1500146, 1, 'No, do not allow blog to be viewed or subscribed to.', 'admin_levels_blogsettings'),
        (1500147, 1, 'Do you want to let users create blogs? If set to no, some other settings on this page may not apply. This is useful if you want users to be able to view blogs and manage their subscriptions, but only want certain levels to be able to create blogs.', 'admin_levels_blogsettings'),
        (1500148, 1, 'Yes, allow creation of blogs.', 'admin_levels_blogsettings'),
        (1500149, 1, 'No, do not allow blog to be created.', 'admin_levels_blogsettings')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "UPDATE se_languagevars SET languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> wrote a blog entry: <a href=\"blog.php?user=%1\$s&blogentry_id=%3\$s\">%4\$s</a>' WHERE languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> wrote a blog entry: <a href=\"blog_entry.php?user=%1\$s&blogentry_id=%3\$s\">%4\$s</a>'";
  $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  $sql = "UPDATE se_languagevars SET languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on <a href=\"profile.php?user=%3\$s\">%4\$s</a>\'s <a href=\"blog.php?user=%3\$s&blogentry_id=%6\$s\">blog entry</a>:<div class=\"recentaction_div\">%5\$s</div>' WHERE languagevar_value='<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on <a href=\"profile.php?user=%3\$s\">%4\$s</a>\'s <a href=\"blog_entry.php?user=%3\$s&blogentry_id=%6\$s\">blog entry</a>:<div class=\"recentaction_div\">%5\$s</div>'";
  $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500150 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* MISC */
        (1500150, 1, 'Blog Entries: %1\$d entries', 'home'),
        (1500151, 1, 'Blog Subscriptions: %1\$d subscriptions', 'home')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500152 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        /* MISC */
        (1500152, 1, 'Posting a Blog Entry', 'actiontypes'),
        (1500153, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> wrote a blog entry: <a href=\"blog.php?user=%1\$s&blogentry_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (1500154, 1, 'Posting a Blog Entry Comment', 'actiontypes'),
        (1500155, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on <a href=\"profile.php?user=%3\$s\">%4\$s</a>\'s <a href=\"blog.php?user=%3\$s&blogentry_id=%6\$s\">blog entry</a>:<div class=\"recentaction_div\">%5\$s</div>', 'actiontypes'),
        (1500156, 1, '%1\$d New Blog Comment(s): %2\$s', 'notifytypes'),
        (1500157, 1, 'When a comment is posted on one of my blog entries.', 'notifytypes'),
        (1500158, 1, '%1\$d New Blog Entry: %2\$s', 'notifytypes'),
        (1500159, 1, 'When I an entry is posted in a blog I have subscribed to.', 'notifytypes'),
        (1500160, 1, 'New Blog Entry Comment', 'systememails'),
        (1500161, 1, 'Hello %1\$s,\n\nA new comment has been posted on one of your blog entries by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (1500162, 1, 'New Blog Entry Trackback', 'systememails'),
        (1500163, 1, 'Hello %1\$s,\n\nA new response has been posted by trackback on one of your blog entries from a blog called <a href=\"%3\$s\">%2\$s</a>. Please click the following link to view the response:\n\n%4\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
        (1500164, 1, 'New Blog Entry from a Subscribed Blog', 'systememails'),
        (1500165, 1, 'Hello %1\$s,\n\nA new entry has been posted on the blog you have subscribed to by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500166 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1500166, 1, 'Blog Category Creation', 'admin_levels_blogsettings'),
        (1500167, 1, 'Allow users to create their own blog categories? If set to no, users can still choose from the categories you have created on the Global Blog Settings page.', 'admin_levels_blogsettings'),
        (1500168, 1, 'Yes, allow users to create their own blog categories.', 'admin_levels_blogsettings'),
        (1500169, 1, 'No, do not allow users to create their own blog categories.', 'admin_levels_blogsettings')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=1500170 LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (1500170, 1, 'Edit This Entry', 'blog')
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  
  ################ UPGRADE EXISTING BLOG ENTRIES' PRIVACY OPTIONS
  if( !empty($plugin_info) && version_compare($plugin_info['plugin_version'], '3.00', '<') )
  {
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='63'  WHERE blogentry_privacy='0' ") or die($database->database_error()." View Privacy Query #1");
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='31'  WHERE blogentry_privacy='1' ") or die($database->database_error()." View Privacy Query #2");
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='15'  WHERE blogentry_privacy='2' ") or die($database->database_error()." View Privacy Query #3");
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='7'   WHERE blogentry_privacy='3' ") or die($database->database_error()." View Privacy Query #4");
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='3'   WHERE blogentry_privacy='4' ") or die($database->database_error()." View Privacy Query #5");
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='1'   WHERE blogentry_privacy='5' ") or die($database->database_error()." View Privacy Query #6");
    $database->database_query("UPDATE se_blogentries SET blogentry_privacy='0'   WHERE blogentry_privacy='6' ") or die($database->database_error()." View Privacy Query #7");
    
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='63' WHERE blogentry_comments='0'") or die($database->database_error()." Comment Privacy Query #1");
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='31' WHERE blogentry_comments='1'") or die($database->database_error()." Comment Privacy Query #2");
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='15' WHERE blogentry_comments='2'") or die($database->database_error()." Comment Privacy Query #3");
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='7'  WHERE blogentry_comments='3'") or die($database->database_error()." Comment Privacy Query #4");
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='3'  WHERE blogentry_comments='4'") or die($database->database_error()." Comment Privacy Query #5");
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='1'  WHERE blogentry_comments='5'") or die($database->database_error()." Comment Privacy Query #6");
    $database->database_query("UPDATE se_blogentries SET blogentry_comments='0'  WHERE blogentry_comments='6'") or die($database->database_error()." Comment Privacy Query #7");
  }
  
}  

?>