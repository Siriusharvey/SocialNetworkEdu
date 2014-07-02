<?php



$plugin_name = "G-Store Plugin";
$plugin_version = "1.04";
$plugin_type = "gstore";
$plugin_desc = "This plugin allows your users to post store items. As the admin, you create the categories (like \"Electrical Store\", \"Furniture Store\", \"Software Store\", etc.) and your users can post relevant items. Your users will also be able to search for other items via a \"browse marketplace\" area, and each users' items will appear on their profile.";
$plugin_icon = "gstore_gstore16.gif";
$plugin_menu_title = "5555001";
$plugin_pages_main = "5555002<!>gstore_gstore16.gif<!>admin_viewgstores.php<~!~>5555003<!>gstore_gstore16.gif<!>admin_gstore.php<~!~>5555192<!>gstore_gstore16.gif<!>admin_gstores_subscriptions.php<~!~>";
$plugin_pages_level = "5555004<!>admin_levels_gstoresettings.php<~!~>";
$plugin_url_htaccess = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/gstores/([0-9]+)/?$ \$server_info/gstore.php?user=\$1&gstore_id=\$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/gstores/([0-9]+)/([^/]+)?$ \$server_info/gstore.php?user=\$1&gstore_id=\$2\$3 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/gstores/?$ \$server_info/gstores.php?user=\$1 [L]";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;




if($install == "gstore")
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
  
  
  
  
  //######### CREATE se_gstorealbums
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstorealbums'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstorealbums` (
        `gstorealbum_id`              INT       UNSIGNED  NOT NULL auto_increment,
        `gstorealbum_gstore_id`   INT       UNSIGNED  NOT NULL default 0,
        `gstorealbum_datecreated`     INT                 NOT NULL default 0,
        `gstorealbum_dateupdated`     INT                 NOT NULL default 0,
        `gstorealbum_title`           VARCHAR(64)             NULL,
        `gstorealbum_desc`            TEXT                    NULL,
        `gstorealbum_search`          TINYINT   UNSIGNED  NOT NULL default 0,
        `gstorealbum_privacy`         TINYINT   UNSIGNED  NOT NULL default 0,
        `gstorealbum_comments`        TINYINT   UNSIGNED  NOT NULL default 0,
        `gstorealbum_cover`           INT       UNSIGNED  NOT NULL default 0,
        `gstorealbum_views`           INT       UNSIGNED  NOT NULL default 0,
        `gstorealbum_totalfiles`      SMALLINT  UNSIGNED  NOT NULL default 0,
        `gstorealbum_totalspace`      BIGINT    UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`gstorealbum_id`),
        KEY `INDEX` (`gstorealbum_gstore_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add gstorealbum_totalfiles
  $sql = "SHOW COLUMNS FROM `se_gstorealbums` LIKE 'gstorealbum_totalfiles'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $totalfiles_exists = (bool) $database->database_num_rows($resource);
  
  if( !$totalfiles_exists )
  {
    $sql = "ALTER TABLE se_gstorealbums ADD COLUMN `gstorealbum_totalfiles` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Populate gstorealbum_totalfiles
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstoremedia'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) && (!$totalfiles_exists || $plugin_reindex_totals) )
  {
    $sql = "SELECT gstorealbum_id FROM se_gstorealbums WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_gstorealbums SET gstorealbum_totalfiles=(SELECT COUNT(gstoremedia_id) FROM se_gstoremedia WHERE gstoremedia_gstorealbum_id=gstorealbum_id) WHERE gstorealbum_id='{$result['gstorealbum_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Add gstorealbum_totalspace
  $sql = "SHOW COLUMNS FROM `se_gstorealbums` LIKE 'gstorealbum_totalspace'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $totalspace_exists = (bool) $database->database_num_rows($resource);
  
  if( !$totalspace_exists )
  {
    $sql = "ALTER TABLE se_gstorealbums ADD COLUMN `gstorealbum_totalspace` BIGINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Populate album_totalspace
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstoremedia'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_num_rows($resource) && (!$totalspace_exists || $plugin_reindex_totals) )
  {
    $sql = "SELECT gstorealbum_id FROM se_gstorealbums WHERE (SELECT COUNT(gstoremedia_id) FROM se_gstoremedia WHERE gstoremedia_gstorealbum_id=gstorealbum_id)>0";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_gstorealbums SET gstorealbum_totalspace=(SELECT SUM(gstoremedia_filesize) FROM se_gstoremedia WHERE gstoremedia_gstorealbum_id=gstorealbum_id) WHERE gstorealbum_id='{$result['gstorealbum_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on gstorealbum_title
  $sql = "SHOW FULL COLUMNS FROM `se_gstorealbums` LIKE 'gstorealbum_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorealbums MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstorealbum_desc
  $sql = "SHOW FULL COLUMNS FROM `se_gstorealbums` LIKE 'gstorealbum_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorealbums MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
 
  
  
  
  //######### CREATE se_gstorecats
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstorecats'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstorecats` (
        `gstorecat_id`          INT         UNSIGNED  NOT NULL auto_increment,
        `gstorecat_dependency`  INT         UNSIGNED  NOT NULL default 0,
        `gstorecat_title`       INT         UNSIGNED  NOT NULL default 0,
        `gstorecat_order`       SMALLINT    UNSIGNED  NOT NULL default 0,
        `gstorecat_signup`      TINYINT     UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`gstorecat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  //######### ALTER se_gstorecats LANGUAGIFY gstorecat_title
  $sql = "SHOW FULL COLUMNS FROM `se_gstorecats` LIKE 'gstorecat_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  // Fix collation, load data, drop column
  $gstorecat_info = array();
  if( $column_info && strtoupper(substr($column_info['Type'], 0, 7))=="VARCHAR" )
  {
    // Fix collation
    if( $column_info['Collation']!=$plugin_db_collation )
    {
      $sql = "ALTER TABLE se_gstorecats MODIFY {$column_info['Field']} {$column_info['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    // Languagify title column
    $sql = "SELECT * FROM se_gstorecats";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    if( $database->database_num_rows($resource) )
      while( $result=$database->database_fetch_assoc($resource) )
        $gstorecat_info[] = $result;
    
    // Drop column
    $sql = "ALTER TABLE se_gstorecats DROP COLUMN gstorecat_title";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    unset($column_info);
  }
  
  // Add column
  if( !$column_info )
  {
    $sql = "ALTER TABLE se_gstorecats ADD COLUMN gstorecat_title INT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Update column
  if( !empty($gstorecat_info) )
  {
    // Update title
    foreach( $gstorecat_info as $gstorecat_info_array )
    {
      $gstorecat_title_lvid = SE_Language::edit(0, $gstorecat_info_array['gstorecat_title'], NULL, LANGUAGE_INDEX_FIELDS);
      
      $sql = "
        UPDATE
          se_gstorecats
        SET
          gstorecat_title='{$gstorecat_title_lvid}'
        WHERE
          gstorecat_id='{$gstorecat_info_array['gstorecat_id']}'
        LIMIT
          1
      ";
      
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  
  
  //######### ALTER se_gstorecats ADD COLUMNS
  $sql = "SHOW COLUMNS FROM `se_gstorecats` LIKE 'gstorecat_order'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_gstorecats
      ADD COLUMN gstorecat_order  SMALLINT  UNSIGNED  NOT NULL default 0,
      ADD COLUMN gstorecat_signup TINYINT   UNSIGNED  NOT NULL default 0
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT se_gstorecats
  $sql = "SELECT NULL FROM se_gstorecats";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $gstorecat_title_lvid = SE_Language::edit(0, "Other", NULL, LANGUAGE_INDEX_FIELDS);
    $sql = "INSERT INTO se_gstorecats (gstorecat_title) VALUES ('{$gstorecat_title_lvid}')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  //######### CREATE se_ratings
if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_ratings'")) == 0) {
  $database->database_query("CREATE TABLE `se_ratings` (
  `rating_id` int(9) NOT NULL auto_increment,
  `rating_object_table` varchar(35) NOT NULL default '0',
  `rating_object_primary` varchar(30) NOT NULL default '0',
  `rating_object_id` int(9) NOT NULL default '0',
  `rating_value` float NOT NULL default '0',
  `rating_raters` text NULL,
  `rating_raters_num` int(9) NOT NULL default '0',
  PRIMARY KEY  (`rating_id`)
  )");
}



  
  
  
  //######### CREATE se_gstorecomments
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstorecomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstorecomments` (
        `gstorecomment_id`              INT         UNSIGNED  NOT NULL auto_increment,
        `gstorecomment_gstore_id`   INT         UNSIGNED  NOT NULL default 0,
        `gstorecomment_authoruser_id`   INT         UNSIGNED  NOT NULL default 0,
        `gstorecomment_date`            INT         UNSIGNED  NOT NULL default 0,
        `gstorecomment_body`            TEXT                      NULL,
        PRIMARY KEY  (`gstorecomment_id`),
        KEY `INDEX` (`gstorecomment_gstore_id`,`gstorecomment_authoruser_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstorecomment_body
  $sql = "SHOW FULL COLUMNS FROM `se_gstorecomments` LIKE 'gstorecomment_body'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorecomments MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_gstorefields
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstorefields'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstorefields` (
        `gstorefield_id`                INT           UNSIGNED  NOT NULL auto_increment,
        `gstorefield_gstorecat_id`  INT           UNSIGNED  NOT NULL default 0,
        `gstorefield_order`             SMALLINT      UNSIGNED  NOT NULL default 0,
        `gstorefield_dependency`        INT           UNSIGNED  NOT NULL default 0,
        `gstorefield_title`             INT           UNSIGNED  NOT NULL default 0,
        `gstorefield_desc`              INT           UNSIGNED  NOT NULL default 0,
        `gstorefield_error`             INT           UNSIGNED  NOT NULL default 0,
        `gstorefield_type`              TINYINT       UNSIGNED  NOT NULL default 0,
        `gstorefield_style`             VARCHAR(255)                NULL,
        `gstorefield_maxlength`         SMALLINT      UNSIGNED  NOT NULL default 0,
        `gstorefield_link`              VARCHAR(255)                NULL,
        `gstorefield_options`           LONGTEXT                    NULL,
        `gstorefield_required`          TINYINT       UNSIGNED  NOT NULL default 0,
        `gstorefield_regex`             VARCHAR(255)                NULL,
        `gstorefield_html`              VARCHAR(255)                NULL,
        `gstorefield_search`            TINYINT       UNSIGNED  NOT NULL default 0,
        `gstorefield_signup`            TINYINT       UNSIGNED  NOT NULL default 0,
        `gstorefield_display`           TINYINT       UNSIGNED  NOT NULL default 0,
        `gstorefield_special`           TINYINT       UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`gstorefield_id`),
        KEY `INDEX` (`gstorefield_gstorecat_id`,`gstorefield_dependency`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### ALTER se_gstorefields LANGUAGIFY gstorefield_title,gstorefield_desc,gstorefield_error
  $sql = "SHOW FULL COLUMNS FROM `se_gstorefields` LIKE 'gstorefield_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $column_info = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  // Fix collation, load text, drop columns
  $gstorefield_info = array();
  if( $column_info && strtoupper(substr($column_info['Type'], 0, 7))=="VARCHAR" )
  {
    // Fix collation
    if( $column_info['Collation']!=$plugin_db_collation )
    {
      $sql = "
        ALTER TABLE se_gstorefields
        MODIFY gstorefield_title  VARCHAR(255) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation} NOT NULL default '',
        MODIFY gstorefield_desc   VARCHAR(255) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation} NOT NULL default '',
        MODIFY gstorefield_error  VARCHAR(255) CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation} NOT NULL default ''
      ";
      
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    // Load title column
    $sql = "SELECT * FROM se_gstorefields";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    if( $database->database_num_rows($resource) )
      while( $result=$database->database_fetch_assoc($resource) )
        $gstorefield_info[] = $result;
    
    // Crop column
    $sql = "ALTER TABLE se_gstorefields DROP COLUMN gstorefield_title, DROP COLUMN gstorefield_desc, DROP COLUMN gstorefield_error";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    unset($column_info);
  }
  
  // Add columns
  if( !$column_info )
  {
    $sql = "
      ALTER TABLE se_gstorefields
      ADD COLUMN gstorefield_title  INT UNSIGNED NOT NULL default 0,
      ADD COLUMN gstorefield_desc   INT UNSIGNED NOT NULL default 0,
      ADD COLUMN gstorefield_error  INT UNSIGNED NOT NULL default 0
    ";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  // Update column
  if( !empty($gstorefield_info) )
  {
    // Update column
    foreach( $gstorefield_info as $gstorefield_info_array )
    {
      $gstorefield_title_lvid = SE_Language::edit(0, $gstorefield_info_array['gstorefield_title'], NULL, LANGUAGE_INDEX_FIELDS);
      $gstorefield_desc_lvid  = SE_Language::edit(0, $gstorefield_info_array['gstorefield_desc' ], NULL, LANGUAGE_INDEX_FIELDS);
      $gstorefield_error_lvid = SE_Language::edit(0, $gstorefield_info_array['gstorefield_error'], NULL, LANGUAGE_INDEX_FIELDS);
      
      $sql = "
        UPDATE
          se_gstorefields
        SET
          gstorefield_title='{$gstorefield_title_lvid}',
          gstorefield_desc='{$gstorefield_desc_lvid}',
          gstorefield_error='{$gstorefield_error_lvid}'
        WHERE
          gstorefield_id='{$gstorefield_info_array['gstorefield_id']}'
        LIMIT
          1
      ";
      
      $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
  }
  
  
  
  
  //######### ALTER se_gstorefields ADD COLUMNS
  $sql = "SHOW COLUMNS FROM `se_gstorefields` LIKE 'gstorefield_signup'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_gstorefields
      ADD COLUMN gstorefield_signup   TINYINT   UNSIGNED  NOT NULL default 0,
      ADD COLUMN gstorefield_display  TINYINT   UNSIGNED  NOT NULL default 0,
      ADD COLUMN gstorefield_special  TINYINT   UNSIGNED  NOT NULL default 0
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstorefield_style
  $sql = "SHOW FULL COLUMNS FROM `se_gstorefields` LIKE 'gstorefield_style'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorefields MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstorefield_link
  $sql = "SHOW FULL COLUMNS FROM `se_gstorefields` LIKE 'gstorefield_link'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorefields MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstorefield_regex
  $sql = "SHOW FULL COLUMNS FROM `se_gstorefields` LIKE 'gstorefield_regex'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorefields MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstorefield_html
  $sql = "SHOW FULL COLUMNS FROM `se_gstorefields` LIKE 'gstorefield_html'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstorefields MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_gstoremedia
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstoremedia'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstoremedia` (
        `gstoremedia_id`                  INT           UNSIGNED  NOT NULL auto_increment,
        `gstoremedia_gstorealbum_id`  INT           UNSIGNED  NOT NULL default 0,
        `gstoremedia_date`                INT                     NOT NULL default 0,
        `gstoremedia_title`               VARCHAR(128)                NULL default '',
        `gstoremedia_desc`                TEXT                        NULL,
        `gstoremedia_ext`                 VARCHAR(8)              NOT NULL default '',
        `gstoremedia_filesize`            INT           UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`gstoremedia_id`),
        KEY `INDEX` (`gstoremedia_gstorealbum_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstoremedia_title
  $sql = "SHOW FULL COLUMNS FROM `se_gstoremedia` LIKE 'gstoremedia_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstoremedia MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstoremedia_desc
  $sql = "SHOW FULL COLUMNS FROM `se_gstoremedia` LIKE 'gstoremedia_desc'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstoremedia MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_gstores
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstores'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstores` (
        `gstore_id`               INT           UNSIGNED  NOT NULL auto_increment,
        `gstore_user_id`          INT           UNSIGNED  NOT NULL default 0,
        `gstore_gstorecat_id`      INT           UNSIGNED  NOT NULL default 0,
        `gstore_date`             INT                     NOT NULL default 0,
        `gstore_dateupdated`      INT                     NOT NULL default 0,
        `gstore_views`            INT           UNSIGNED  NOT NULL default 0,
        `gstore_title`            VARCHAR(128)            NOT NULL default '',
		`gstore_price`            VARCHAR(20)             NOT NULL default '',
		`gstore_stock`            VARCHAR(100)            NOT NULL default 0,
        `gstore_body`             TEXT                        NULL,
        `gstore_photo`            VARCHAR(16)             NOT NULL default '',
        `gstore_search`           TINYINT       UNSIGNED  NOT NULL default 0,
        `gstore_privacy`          TINYINT       UNSIGNED  NOT NULL default 0,
        `gstore_comments`         TINYINT       UNSIGNED  NOT NULL default 0,
        `gstore_totalcomments`    SMALLINT      UNSIGNED  NOT NULL default 0,
		`item_sales`             INT           UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`gstore_id`),
        KEY `INDEX` (`gstore_user_id`, `gstore_gstorecat_id`),
        FULLTEXT `SEARCH` (`gstore_title`, `gstore_body`)
      )
      ENGINE=MyISAM CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add gstore_totalcomments
  $sql = "SHOW COLUMNS FROM `se_gstores` LIKE 'gstore_totalcomments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_gstores ADD COLUMN `gstore_totalcomments` SMALLINT UNSIGNED NOT NULL default 0";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  if( !$database->database_num_rows($resource) || $plugin_reindex_totals )
  {
    $sql = "SELECT gstore_id FROM se_gstores WHERE 1";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    while( $result = $database->database_fetch_assoc($resource) )
    {
      $sql = "UPDATE se_gstores SET gstore_totalcomments=(SELECT COUNT(gstorecomment_id) FROM se_gstorecomments WHERE gstorecomment_gstore_id=gstore_id) WHERE gstore_id='{$result['gstore_id']}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    }
  }
  
  
  // Ensure utf8 on gstore_title
  $sql = "SHOW FULL COLUMNS FROM `se_gstores` LIKE 'gstore_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstores MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
    // Ensure utf8 on gstore_price
  $sql = "SHOW FULL COLUMNS FROM `se_gstores` LIKE 'gstore_price'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstores MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Ensure utf8 on gstore_body
  $sql = "SHOW FULL COLUMNS FROM `se_gstores` LIKE 'gstore_body'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );

  if( $result && $result['Collation']!=$plugin_db_collation )
  {
    $sql = "ALTER TABLE se_gstores MODIFY {$result['Field']} {$result['Type']} CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  // Add full text index (should be after ensuring they are in utf8)
  $sql = "SHOW FULL COLUMNS FROM `se_gstores` LIKE 'gstore_title'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && !$result['Key'] )
  {
    $sql = "ALTER TABLE `se_gstores` ADD FULLTEXT `SEARCH` (`gstore_title`, `gstore_body`)";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_gstore_settings
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstore_settings'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstore_settings` (
        `gstore_settings_id`              INT    NOT NULL auto_increment,
        `gstore_settings_user_id`         INT    NOT NULL default 0,
        `paypal_email`             TEXT       NULL,
        PRIMARY KEY  (`gstore_settings_id`),
        KEY `INDEX` (`gstore_settings_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### CREATE se_gstorevalues
  $sql = "SHOW TABLES FROM `$database_name` LIKE 'se_gstorevalues'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      CREATE TABLE `se_gstorevalues` (
        `gstorevalue_id`            INT           UNSIGNED  NOT NULL auto_increment,
        `gstorevalue_gstore_id` INT           UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`gstorevalue_id`),
        KEY `INDEX` (`gstorevalue_gstore_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT se_urls
  $sql = "SELECT url_id FROM se_urls WHERE url_file='gstores'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('gstores URL', 'gstores', 'gstores.php?user=\$user', '\$user/gstores/')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT url_id FROM se_urls WHERE url_file='gstore'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "INSERT INTO se_urls (url_title, url_file, url_regular, url_subdirectory) VALUES ('gstore item URL', 'gstore', 'gstore.php?user=\$user&gstore_id=\$id1', '\$user/gstores/\$id1/')";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='postgstore'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('postgstore', 'gstore_action_postgstore.gif', '1', '1', '5555148', '5555149', '[username],[displayname],[id],[title]', '0')
    ");
    
    $actiontypes[] = $database->database_insert_id();
  }
  
  
    $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='editgstore'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('editgstore', 'gstore_action_editgstore.png', '1', '1', '5555158', '5555156', '[username],[displayname],[id],[title]', '0')
    ");
    
    $actiontypes[] = $database->database_insert_id();
  }
  
  
  $sql = "SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='gstorecomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('gstorecomment', 'action_postcomment.gif', '1', '1', '5555150', '5555151', '[username1],[displayname1],[username2],[displayname2],[comment],[id]', '0')
    ");
    
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }
  
  
  
  
  //######### INSERT se_notifytypes
  $sql = "SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='gstorecomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('gstorecomment', '5555152', 'action_postcomment.gif', 'gstore.php?user=%1\$s&gstore_id=%2\$s', '5555153')
    ");
  }
  
  
  
  //######### FIX se_notifytypes
  $sql = "UPDATE se_notifytypes SET notifytype_url='gstore.php?user=%1\$s&gstore_id=%2\$s' WHERE notifytype_url='gstore.php?gstore_id=%2\$s' LIMIT 1";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  
  
  //######### ADD COLUMNS/VALUES TO LEVELS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_gstore_allow'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_levels 
      ADD COLUMN `level_gstore_allow` int(1) NOT NULL default '1',
      ADD COLUMN `level_gstore_entries` int(3) NOT NULL default '50',
      ADD COLUMN `level_gstore_search` int(1) NOT NULL default '1',
      ADD COLUMN `level_gstore_privacy` varchar(100) NOT NULL default 'a:2:{i:4;s:2:\"31\";i:5;s:2:\"63\";}',
      ADD COLUMN `level_gstore_comments` varchar(100) NOT NULL default 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"3\";i:3;s:1:\"7\";i:4;s:2:\"15\";i:5;s:2:\"31\";i:6;s:2:\"63\";}',
      ADD COLUMN `level_gstore_photo` int(1) NOT NULL default '1',
      ADD COLUMN `level_gstore_photo_width` varchar(3) NOT NULL default '500',
      ADD COLUMN `level_gstore_photo_height` varchar(3) NOT NULL default '500',
      ADD COLUMN `level_gstore_photo_exts` varchar(50) NOT NULL default '',
      ADD COLUMN `level_gstore_album_exts` text NULL,
      ADD COLUMN `level_gstore_album_mimes` text NULL,
      ADD COLUMN `level_gstore_album_storage` bigint(14) NOT NULL default '5242880',
      ADD COLUMN `level_gstore_album_maxsize` bigint(14) NOT NULL default '2048000',
      ADD COLUMN `level_gstore_album_width` varchar(4) NOT NULL default '500',
      ADD COLUMN `level_gstore_album_height` varchar(4) NOT NULL default '500',
      ADD COLUMN `level_gstore_html` text NULL
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "
      UPDATE
        se_levels
      SET
        level_gstore_html='a,b,br,div,i,img,p,u',
        level_gstore_photo_exts='jpg,jpeg,gif,png',
        level_gstore_album_exts='jpg,gif,jpeg,png,bmp,mp3,mpeg,avi,mpa,mov,qt,swf',
        level_gstore_album_mimes='image/jpeg,image/pjpeg,image/jpg,image/jpe,image/pjpg,image/x-jpeg,x-jpg,image/gif,image/x-gif,image/png,image/x-png,image/bmp,audio/mpeg,video/mpeg,video/x-msvideo,video/quicktime,application/x-shockwave-flash'
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_gstore_privacy'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && strtoupper($result['Type'])=="VARCHAR(10)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE level_gstore_privacy level_gstore_privacy varchar(100) NOT NULL default ''";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_levels SET level_gstore_privacy='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_gstore_comments'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $result = ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : NULL );
  
  if( $result && strtoupper($result['Type'])=="VARCHAR(10)" )
  {
    $sql = "ALTER TABLE se_levels CHANGE level_gstore_comments level_gstore_comments varchar(100) NOT NULL default ''";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "UPDATE se_levels SET level_gstore_comments='a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"7\";i:3;s:2:\"15\";i:4;s:2:\"31\";i:5;s:2:\"63\";}'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_gstore_style'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_gstore_style` TINYINT NOT NULL default 1";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_levels` LIKE 'level_gstore_html'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_levels ADD COLUMN `level_gstore_html`  TEXT NULL";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    $sql = "UPDATE se_levels SET level_gstore_html='a,b,br,div,i,img,p,u'";
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  }
  
  
  
  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_gstore'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_settings ADD COLUMN `setting_permission_gstore` int(1) NOT NULL default '1'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
    //######### ADD currency TO SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'gstore_currency'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_settings ADD COLUMN `gstore_currency` VARCHAR(10) NOT NULL default '5555187'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  $sql = "SELECT systememail_id FROM se_systememails WHERE systememail_name='gstorecomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('gstorecomment', '5555005', '5555006', '5555154', '5555155', '[displayname],[commenter],[link]')
    ");
  }
  
  
  
  
  //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  $sql = "SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_gstorecomment'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "ALTER TABLE se_usersettings ADD COLUMN `usersetting_notify_gstorecomment` int(1) NOT NULL default '1'";
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  /* INSERT USER_SALES ROW */
$database->database_query("ALTER TABLE `se_users` ADD `user_sales` INT( 100 ) NOT NULL default '0' ");
  
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS NOT BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555001 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES 
        (5555001, 1, 'Store Settings', ''),
        (5555002, 1, 'View Store Items', ''),
        (5555003, 1, 'Global Store Settings', ''),
        (5555004, 1, 'Store Settings', ''),
        (5555005, 1, 'New Store Comment Email', ''),
        (5555006, 1, 'This is the email that gets sent to a user when a new comment is posted on one of their store items.', ''),
        (5555007, 1, 'My Store Management', '')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  //######### INSERT LANGUAGE VARS (v3 COMPATIBLE HAS BEEN INSTALLED)
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555008 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        /* admin_levels_storesettings */
        (5555008, 1, 'Your items per page field must contain an integer between 1 and 999.', 'admin_levels_storesettings'),
        (5555009, 1, 'Photo width and height must be integers between 1 and 999.', 'admin_levels_storesettings'),
        (5555010, 1, 'Your maximum filesize field must contain an integer between 1 and 204800.', 'admin_levels_storesettings'),
        (5555011, 1, 'Your maximum width and height fields must contain integers between 1 and 9999.', 'admin_levels_storesettings'),
        (5555012, 1, 'If you have allowed users to have stores items, you can adjust their details from this page.', 'admin_levels_storesettings'),
        (5555013, 1, 'Allow Stores?', 'admin_levels_storesettings'),
        (5555014, 1, 'Do you want to let users have store items? If set to no, all other settings on this page will not apply.', 'admin_levels_storesettings'),
        (5555015, 1, 'Yes, allow store items.', 'admin_levels_storesettings'),
        (5555016, 1, 'No, do not allow store items.', 'admin_levels_storesettings'),
        (5555017, 1, 'Allow Store Photos?', 'admin_levels_storesettings'),
        (5555018, 1, 'If you enable this feature, users will be able to upload a small photo icon when creating or editing a store item. This can be displayed next to the store name in search/browse results, etc.', 'admin_levels_storesettings'),
        (5555019, 1, 'Yes, users can upload a photo icon when they create/edit a store item.', 'admin_levels_storesettings'),
        (5555020, 1, 'No, users can not upload a photo icon when they create/edit a store item.', 'admin_levels_storesettings'),
        (5555021, 1, 'If you have selected YES above, please input the maximum dimensions for the store photos. If your users upload a photo that is larger than these dimensions, the server will attempt to scale them down automatically. This feature requires that your PHP server is compiled with support for the GD Libraries.', 'admin_levels_storesettings'),
        (5555022, 1, 'Maximum Width:', 'admin_levels_storesettings'),
        (5555023, 1, 'Maximum Height:', 'admin_levels_storesettings'),
        (5555024, 1, '(in pixels, between 1 and 999)', 'admin_levels_storesettings'),
        (5555025, 1, 'What file types do you want to allow for store photos (gif, jpg, jpeg, or png)? Separate file types with commas, i.e. jpg, jpeg, gif, png', 'admin_levels_storesettings'),
        (5555026, 1, 'Allowed File Types:', 'admin_levels_storesettings'),
        (5555027, 1, 'Items Per Page', 'admin_levels_storesettings'),
        (5555028, 1, 'How many store items will be shown per page? (Enter a number between 1 and 999)', 'admin_levels_storesettings'),
        (5555029, 1, 'items per page', 'admin_levels_storesettings'),
        (5555030, 1, 'Store Privacy Options', 'admin_levels_storesettings'),
        (5555031, 1, '<b>Search Privacy Options</b><br>If you enable this feature, users will be able to exclude their store items from search results. Otherwise, all store items will be included in search results.', 'admin_levels_storesettings'),
        (5555032, 1, 'Yes, allow users to exclude their store items from search results.', 'admin_levels_storesettings'),
        (5555033, 1, 'No, force all store items to be included in search results.', 'admin_levels_storesettings'),
        (5555034, 1, '<b>Store Item Privacy</b><br>Your users can choose from any of the options checked below when they decide who can see their store items. These options appear on your users\' \"Add item\" and \"Edit item\" pages. If you do not check any options, everyone will be allowed to view stores.', 'admin_levels_storesettings'),
        (5555035, 1, '<b>Store Comment Options</b><br>Your users can choose from any of the options checked below when they decide who can post comments on their items. If you do not check any options, everyone will be allowed to post comments on items.', 'admin_levels_storesettings'),
        (5555036, 1, 'Store File Settings', 'admin_levels_storesettings'),
        (5555037, 1, 'List the following file extensions that users are allowed to upload. Separate file extensions with commas, for example: jpg, gif, jpeg, png, bmp', 'admin_levels_storesettings'),
        (5555038, 1, 'To successfully upload a file, the file must have an allowed filetype extension as well as an allowed MIME type. This prevents users from disguising malicious files with a fake extension. Separate MIME types with commas, for example: image/jpeg, image/gif, image/png, image/bmp', 'admin_levels_storesettings'),
        (5555039, 1, 'How much storage space should each item have to store its files?', 'admin_levels_storesettings'),
        (5555040, 1, 'Unlimited', 'admin_levels_storesettings'),
        (5555041, 1, '%1\$s KB', 'admin_levels_storesettings'),
        (5555042, 1, '%1\$s MB', 'admin_levels_storesettings'),
        (5555043, 1, '%1\$s GB', 'admin_levels_storesettings'),
        (5555044, 1, 'Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 204800.', 'admin_levels_storesettings'),
        (5555045, 1, 'Enter the maximum width and height (in pixels) for images uploaded to items. Images with dimensions outside this range will be sized down accordingly if your server has the GD Libraries installed. Note that unusual image types like BMP, TIFF, RAW, and others may not be resized.', 'admin_levels_storesettings'),
        (5555046, 1, 'Maximum Width:', 'admin_levels_storesettings'),
        (5555047, 1, 'Maximum Height:', 'admin_levels_storesettings'),
        (5555048, 1, '(in pixels, between 1 and 9999)', 'admin_levels_storesettings'),
        
        /* admin_viewstores */
        (5555049, 1, 'This page lists all of the store items your users have posted. You can use this page to monitor these stores and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific store items. Leaving the filter fields blank will show all the store items on your social network.', 'admin_viewstores'),
        (5555050, 1, 'No items were found.', 'admin_viewstores'),
        (5555051, 1, '%1\$d Store Items Found', 'admin_viewstores'),
        (5555052, 1, 'Title', 'admin_viewstores'),
        (5555053, 1, 'Owner', 'admin_viewstores'),
        (5555054, 1, 'view', 'admin_viewstores, stores'),
        (5555055, 1, 'Are you sure you want to delete this store item?', 'admin_viewstores'),
        
        /* store */
        (5555056, 1, '<a href=\"%2\$s\">%1\$s\'s</a> Store Item no: %3\$s', 'store'),
        (5555057, 1, 'Created: %1\$s', 'store, stores'),
        (5555058, 1, 'Store:', 'store'),
        (5555059, 1, 'Back to %1\$s\'s Items', 'store'),
        
        /* stores */
        (5555060, 1, '<a href=\"%2\$s\">%1\$s</a>\'s Store Items', 'stores'),
        (5555061, 1, '<b><a href=\"%2\$s\">%1\$s</a></b> has not posted any store items.', 'stores'),
        (5555062, 1, 'Views: %1\$d views', 'stores'),
        (5555063, 1, 'Comments: %1\$d comments', 'stores'),
        
        /* profile_store */
        (5555064, 1, 'Posted:', 'profile_store'),
        
        /* user_store */
        (5555065, 1, 'Post New Item', 'user_store'),
        (5555066, 1, 'My Store Settings', 'user_store'),
        (5555067, 1, 'Search My Items', 'user_store'),
        (5555068, 1, 'My Store Management', 'user_store'),
        (5555069, 1, 'The network store is a great way to start selling your items online. Receive payments directly into your Paypal account.', 'user_store'),
        (5555070, 1, 'No store items were found.', 'user_store'),
        (5555071, 1, 'Start selling and receiving payments on the network today.<br />A few simple steps that wont take more than a moment <a href=user_gstore_settings.php><br /><br />Click here</a> to Get Started.', 'user_store'),
        (5555072, 1, '%1\$d views', 'browse_stores, user_store'),
        (5555073, 1, 'View Item', 'user_store'),
        (5555074, 1, 'Edit Item Details', 'user_store'),
        (5555075, 1, 'Edit Item Photos', 'user_store'),
        (5555076, 1, 'Delete Item', 'user_store'),
        
        /* admin_store */
        (5555077, 1, 'General Store Settings', 'admin_store'),
        (5555078, 1, 'This page contains general store settings that affect your entire social network.', 'admin_store'),
        (5555079, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the <a href=\"admin_general.php\">General Settings</a> page.', 'admin_store'),
        (5555080, 1, 'Yes, the public can view stores unless they are made private.', 'admin_store'),
        (5555081, 1, 'No, the public cannot view stores.', 'admin_store'),
        (5555082, 1, 'Stores and Departments', 'admin_store'),
        (5555083, 1, 'You may want to allow your users to categorize the items they are selling. A categorized store makes it easier for users to find the items that interest them. <br>You can create Stores and departments below.<br /><br />Within each store or department, you can create store fields. When an item is posted by a member, the creator (owner) will describe the store by filling in some fields with information about the store. Add the fields you want to include below. <br />Remember that a \"Item Title\" \"Item Price\" and \"Item Description\" field will always be available and required. Drag the icons next to the categories and fields to reorder them.', 'admin_store'),
        (5555084, 1, 'Stores', 'admin_store'),
        
        /* user_store_item */
        (5555085, 1, 'Post New Store Item', 'user_store_item'),
        (5555086, 1, 'Edit Store Item', 'user_store_item'),
        (5555087, 1, 'Describe your new item below, then click \"Post Item\" to publish the item on your chosen store.', 'user_store_item'),
        (5555088, 1, 'Edit the details of your item below.', 'user_store_item'),
        (5555089, 1, 'Item Title', 'user_store_item'),
        (5555090, 1, 'Item Description', 'user_store_item'),
        (5555091, 1, 'Item Store and Department', 'user_store_item'),
        (5555092, 1, 'Include this item in search/browse results?', 'user_store_item'),
        (5555093, 1, 'Yes, include this item in search/browse results.', 'user_store_item'),
        (5555094, 1, 'No, exclude this item from search/browse results.', 'user_store_item'),
        (5555095, 1, 'Who can see buy item?', 'user_store_item'),
        (5555096, 1, 'You can decide who gets to see this item.', 'user_store_item'),
        (5555097, 1, 'Allow Comments?', 'user_store_item'),
        (5555098, 1, 'You can decide who can post comments on this item.', 'user_store_item'),
        (5555099, 1, 'Post Item', 'user_store_item'),
        (5555100, 1, 'Please enter a title for your item.', 'user_store_item'),
        (5555101, 1, 'Please select a category for this Item.', 'user_store_item'),
        (5555102, 1, 'Back to My Store Management', 'user_store_item, user_store_media'),
		(55550100, 1, 'Please enter a price for your item. You may have entered the price incorrectly The correct format is eg 10.00 or eg 5000.00 Do not insert a currency symbol.', 'user_store_item'),
		
        
        /* user_store_media */
        (5555103, 1, 'Edit Item Photos', 'user_store_media'),
        (5555104, 1, 'Use this page to change the photos shown on this store item.', 'user_store_media'),
        (5555105, 1, 'Your store item has been posted! Do you want to add some photos?', 'user_store_media'),
        (5555106, 1, 'Add Photos Now', 'user_store_media'),
        (5555107, 1, 'Maybe Later', 'user_store_media'),
        (5555108, 1, 'Small Photo', 'user_store_media'),
        (5555109, 1, 'Replace this photo with:', 'user_store_media'),
        (5555110, 1, 'delete photo', 'user_store_media'),
        (5555111, 1, 'Deleting photo...', 'user_store_media'),
        (5555112, 1, 'Add a photo:', 'user_store_media'),
        (5555113, 1, 'Upload', 'user_store_media'),
        (5555114, 1, 'Large Photos', 'user_store_media'),
        
        /* user_store_settings */
        (5555115, 1, 'Edit Your Store Settings', 'user_store_settings'),
        (5555116, 1, 'Edit settings pertaining to your store. Its very important you take care and ensure these settings are correct', 'user_store_settings'),
        (5555117, 1, 'Custom Store Styles', 'user_store_settings'),
        (5555118, 1, 'You can change the colors, fonts, and styles of your store item by adding CSS code below. The contents of the text area below will be output between &lt;style&gt; tags on your store item.', 'user_store_settings'),
        (5555119, 1, 'Store Notifications', 'user_store_settings'),
        (5555120, 1, 'Notify me by email when someone writes a comment on my store items.', 'user_store_settings'),
        
        /* MISC */
        (5555121, 1, 'Delete Store Item?', 'user_store'),
        (5555122, 1, 'Are you sure you want to delete this store item?', 'user_store'),
        (5555123, 1, 'There was an error processing your request.', 'user_store'),
        
        /* browse_stores */
        (5555124, 1, 'Showing all Store Items', 'browse_stores'),
        (5555125, 1, 'View:', 'browse_stores'),
        (5555126, 1, 'Order:', 'browse_stores'),
        (5555127, 1, 'Everyone\'s Items', 'browse_stores'),
        (5555128, 1, 'My Friends\' Items', 'browse_stores'),
        (5555129, 1, 'Recently Created', 'browse_stores'),
        (5555130, 1, 'Recently Updated', 'browse_stores'),
        (5555131, 1, 'Most Viewed', 'browse_stores'),
        (5555132, 1, 'Most Commented', 'browse_stores'),
        (5555133, 1, 'Show all Store Items', 'browse_stores'),
        (5555134, 1, 'No items were found matching your criteria.', 'browse_stores'),
        (5555135, 1, 'created %1\$s', 'browse_stores'),
        (5555136, 1, 'updated %1\$s', 'browse_stores'),
        
        /* search */
        (5555137, 1, 'Store item: %1\$s', 'search'),
        (5555138, 1, 'Store item posted by <a href=\'%1\$s\'>%2\$s</a><br>%3\$s', 'search'),
        (5555139, 1, '%1\$d stores', 'search'),
        
        /* MISC */
        (5555140, 1, 'HTML in Store Items', 'admin_levels_storesettings'),
        (5555141, 1, 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: <i>b, img, a, embed, font<i>', 'admin_levels_storesettings'),
        (5555142, 1, 'Store Photo', 'store'),
        (5555143, 1, '%1\$s\'s store items', 'header_global'),
        (5555144, 1, '%1\$s\'s store item - %2\$s', 'header_global')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555145 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5555145, 1, 'Stores: %1\$d Items for Sale', 'home'),
        (5555146, 1, 'Store Comments: %1\$d comments', 'home'),
        (5555147, 1, 'Store Media: %1\$d media', 'home')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555148 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5555148, 1, 'Posting a Store Item', 'actiontypes'),
        (5555149, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a store item: <a href=\"gstore.php?user=%1\$s&gstore_id=%3\$s\">%4\$s</a>', 'actiontypes'),
        (5555150, 1, 'Posting a Store Comment', 'actiontypes'),
        (5555151, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a comment on <a href=\"profile.php?user=%3\$s\">%4\$s</a>\'s <a href=\"gstore.php?user=%3\$s&gstore_id=%6\$s\">store item</a>:<div class=\"recentaction_div\">%5\$s</div>', 'actiontypes'),
        (5555152, 1, '%1\$d New Store Comment(s): %2\$s', 'notifytypes'),
        (5555153, 1, 'When I receive a store comment.', 'notifytypes'),
        (5555154, 1, 'New Store Item Comment', 'systememails'),
        (5555155, 1, 'Hello %1\$s,\n\nA new comment has been posted on one of your store items by %2\$s. Please click the following link to view it:\n\n%3\$s\n\nBest Regards,\nSocial Network Administration', 'systememails'),
		(5555156, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> changed the details of the store item: <a href=\"gstore.php?user=%1\$s&gstore_id=%3\$s\">%4\$s</a>', 'actiontypes'),
		(5555157, 1, 'Most Interesting Items', 'actiontypes'),
		(5555158, 1, 'Editing a Store Item', 'actiontypes'),
		(5555159, 1, 'My\&nbsp\;Store', 'actiontypes'),
		(5555160, 1, 'Store', 'main menu'),
		(5555161, 1, 'Checkout', 'checkout'),
		(5555162, 1, 'Thankyou for shopping with us today', 'checkout'),
		(5555163, 1, 'You must enter the number of items you have avialable for sale', 'error'),
		(5555164, 1, 'You entered an invalid charicter in the stock field , please use numbers only', 'error'),
		(5555165, 1, '', 'error'),
		(5555166, 1, '', 'blank')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
    $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555167 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5555167, 1, 'Unit price', 'store management'),
        (5555168, 1, 'Sales Details', 'store management'),
        (5555169, 1, 'Item id', 'store management'),
		(5555170, 1, 'Total item sales', 'store management'),
        (5555171, 1, 'Update your stock levels', 'store management'),
        (5555172, 1, 'Update stock', 'store management'),
		(5555173, 1, 'Number of items left in stock', 'store management'),
		(5555174, 1, 'Out of stock', 'store management')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
     $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555175 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
		(5555175, 1, 'Purchace Details', 'store'),
        (5555176, 1, 'Items Sold', 'store'),
        (5555177, 1, 'Left in stock', 'store'),
		(5555178, 1, 'qty', 'store'),
		(5555179, 1, 'Buy Now', 'store'),
		(5555180, 1, 'Seller info', 'store'),
        (5555181, 1, 'name', 'store'),
		(5555182, 1, 'sales', 'store'),
		(5555183, 1, 'Total sales', 'store')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
    ################ UPGRADE EXISTING gstoreS' FOR V1.03
      $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555184 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5555184, 1, 'Choose the Store Currency', 'admin'),
        (5555185, 1, 'Select the currency your store will use. This currency will be applied to all products your users upload, although the paypal account of the buyer will convert it back to there prefered currency upon checkout.', 'admin'),
        (5555186, 1, 'Select Currency', 'admin'),
		(5555187, 1, '&#036;', 'dollar'),
		(5555188, 1, '&#163;', 'pound'),
		(5555189, 1, '&#165', 'yen'),
		(5555190, 1, '&#8364;', 'euro'),
		(5555191, 1, 'Popular Items From This Seller', 'store')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  
  
  ################ UPGRADE EXISTING gstoreS' FOR V1.04
       $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=5555192 LIMIT 1";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
  if( !$database->database_num_rows($resource) )
  {
    $sql = "
      INSERT INTO `se_languagevars`
        (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
      VALUES
        (5555192, 1, 'Gstore Subsrriptions', 'admin_subscriptions'),
        (5555193, 1, 'You can set the shipping bands here that your sellers will get to chose from. Sellers will have to apply a charge for delivery to each of these bands from there location, if they opt to apply a shipping charge to there item.', 'admin_subscriptions'),
        (5555194, 1, 'Chose The Shipping Bands the Store Will Use', 'admin'),
		(5555195, 1, 'United Kingdom', 'admin_subscriptions'),
		(5555196, 1, 'Europe', 'admin_subscriptions'),
		(5555197, 1, 'Northern Hemisphere', 'admin_subscriptions'),
		(5555198, 1, 'Southern Hemisphere', 'admin_subscriptions'),
		(5555199, 1, '', 'admin_subscriptions')
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
    /* INSERT SHIPPING BAND ROWS */
  $sql = "SHOW COLUMNS FROM `se_settings` LIKE 'gstore_band_a'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $gstore_band_a_exists = (bool) $database->database_num_rows($resource);
  
  if( !$gstore_band_a_exists )
  {
     $sql = "ALTER TABLE `se_settings`
	 		 ADD COLUMN `gstore_band_a` VARCHAR(100) NOT NULL default 'United Kingdom' ,
    		 ADD COLUMN `gstore_band_b` VARCHAR(100) NOT NULL default 'Europe' ,
			 ADD COLUMN `gstore_band_c` VARCHAR(100) NOT NULL default 'Northern Hemisphere' ,
			 ADD COLUMN `gstore_band_d` VARCHAR(100) NOT NULL default 'Southern Hemisphere' ";
	
   $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
      /* INSERT SHIPPING BAND Charges ROWS */
  $sql = "SHOW COLUMNS FROM `se_gstores` LIKE 'band_a_charge'";
  $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  $band_a_charge_exists = (bool) $database->database_num_rows($resource);
  
  if( !$band_a_charge_exists )
  {
     $sql = "ALTER TABLE `se_gstores` 
	 ADD COLUMN `band_a_charge` VARCHAR(10) NOT NULL ,
     ADD COLUMN `band_b_charge` VARCHAR(10) NOT NULL ,
	 ADD COLUMN `band_c_charge` VARCHAR(10) NOT NULL ,
	 ADD COLUMN `band_d_charge` VARCHAR(10) NOT NULL ,
	 ADD COLUMN `apply_shipping_charges` VARCHAR(30) NOT NULL ";
	
   $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  }
  
  
  
  ################ UPGRADE EXISTING gstoreS' PRIVACY OPTIONS
  if( !empty($plugin_info) && version_compare($plugin_info['plugin_version'], '3.00', '<') )
  {
    $database->database_query("UPDATE se_gstores SET gstore_privacy='63'  WHERE gstore_privacy='0' ") or die($database->database_error()." View Privacy Query #1");
    $database->database_query("UPDATE se_gstores SET gstore_privacy='31'  WHERE gstore_privacy='1' ") or die($database->database_error()." View Privacy Query #2");
    $database->database_query("UPDATE se_gstores SET gstore_privacy='15'  WHERE gstore_privacy='2' ") or die($database->database_error()." View Privacy Query #3");
    $database->database_query("UPDATE se_gstores SET gstore_privacy='7'   WHERE gstore_privacy='3' ") or die($database->database_error()." View Privacy Query #4");
    $database->database_query("UPDATE se_gstores SET gstore_privacy='3'   WHERE gstore_privacy='4' ") or die($database->database_error()." View Privacy Query #5");
    $database->database_query("UPDATE se_gstores SET gstore_privacy='1'   WHERE gstore_privacy='5' ") or die($database->database_error()." View Privacy Query #6");
    $database->database_query("UPDATE se_gstores SET gstore_privacy='0'   WHERE gstore_privacy='6' ") or die($database->database_error()." View Privacy Query #7");
    
    $database->database_query("UPDATE se_gstores SET gstore_comments='63' WHERE gstore_comments='0'") or die($database->database_error()." Comment Privacy Query #1");
    $database->database_query("UPDATE se_gstores SET gstore_comments='31' WHERE gstore_comments='1'") or die($database->database_error()." Comment Privacy Query #2");
    $database->database_query("UPDATE se_gstores SET gstore_comments='15' WHERE gstore_comments='2'") or die($database->database_error()." Comment Privacy Query #3");
    $database->database_query("UPDATE se_gstores SET gstore_comments='7'  WHERE gstore_comments='3'") or die($database->database_error()." Comment Privacy Query #4");
    $database->database_query("UPDATE se_gstores SET gstore_comments='3'  WHERE gstore_comments='4'") or die($database->database_error()." Comment Privacy Query #5");
    $database->database_query("UPDATE se_gstores SET gstore_comments='1'  WHERE gstore_comments='5'") or die($database->database_error()." Comment Privacy Query #6");
    $database->database_query("UPDATE se_gstores SET gstore_comments='0'  WHERE gstore_comments='6'") or die($database->database_error()." Comment Privacy Query #7");
  }
}

?>