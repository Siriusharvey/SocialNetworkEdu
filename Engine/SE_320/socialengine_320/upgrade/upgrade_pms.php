<?php

/* $Id: upgrade_pms.php 8 2009-01-11 06:02:53Z john $ */


/*

-- phpMyAdmin SQL Dump
-- version 2.11.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 20, 2008 at 05:06 PM
-- Server version: 3.23.32
-- PHP Version: 4.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: ``
--

-- --------------------------------------------------------

--
-- Table structure for table `se_pmconvoops`
--

CREATE TABLE IF NOT EXISTS `se_pmconvoops` (
  `pmconvoop_id` int(10) unsigned NOT NULL auto_increment,
  `pmconvoop_pmconvo_id` int(10) unsigned NOT NULL default '0',
  `pmconvoop_user_id` int(10) unsigned NOT NULL default '0',
  `pmconvoop_read` tinyint(1) unsigned NOT NULL default '0',
  `pmconvoop_deleted_inbox` tinyint(3) unsigned NOT NULL default '0',
  `pmconvoop_deleted_outbox` tinyint(3) unsigned NOT NULL default '0',
  `pmconvoop_pmdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pmconvoop_id`),
  UNIQUE KEY `INDEX` (`pmconvoop_pmconvo_id`,`pmconvoop_user_id`),
  KEY `total_outbox` (`pmconvoop_user_id`,`pmconvoop_deleted_outbox`,`pmconvoop_read`),
  KEY `last_pm_date` (`pmconvoop_pmdate`),
  KEY `total_inbox` (`pmconvoop_user_id`,`pmconvoop_deleted_inbox`,`pmconvoop_read`,`pmconvoop_pmdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `se_pmconvos`
--

CREATE TABLE IF NOT EXISTS `se_pmconvos` (
  `pmconvo_id` int(9) NOT NULL auto_increment,
  `pmconvo_subject` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `pmconvo_recipients` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmconvo_id`),
  KEY `pmconvo_recipients` (`pmconvo_recipients`),
  FULLTEXT KEY `SEARCH` (`pmconvo_subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `se_pms`
--

CREATE TABLE IF NOT EXISTS `se_pms` (
  `pm_id` int(9) NOT NULL auto_increment,
  `pm_authoruser_id` int(9) NOT NULL default '0',
  `pm_pmconvo_id` int(9) NOT NULL default '0',
  `pm_date` int(14) NOT NULL default '0',
  `pm_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`pm_id`),
  KEY `pm_pmconvo_id` (`pm_pmconvo_id`),
  KEY `list_subquery` (`pm_pmconvo_id`,`pm_authoruser_id`,`pm_id`),
  FULLTEXT KEY `SEARCH` (`pm_body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


*/

include "header.php";
$result = '';




//######### ALTER se_pmconvos, CREATE se_pmconvoops
$sql = "SHOW TABLES FROM `$database_name` LIKE 'se_pmconvoops'";
$resource = $database->database_query($sql) or die($database->database_error()." ".$sql);

if( !$database->database_num_rows($resource) )
{
  $sql = "
    CREATE TABLE `se_pmconvoops`
    (
      `pmconvoop_id`              int(10)     unsigned  NOT NULL auto_increment,
      `pmconvoop_pmconvo_id`      int(10)     unsigned  NOT NULL default '0',
      `pmconvoop_user_id`         int(10)     unsigned  NOT NULL default '0',
      `pmconvoop_read`            tinyint(1)  unsigned  NOT NULL default '0',
      `pmconvoop_deleted_inbox`   tinyint(3)  unsigned  NOT NULL default '0',
      `pmconvoop_deleted_outbox`  tinyint(3)  unsigned  NOT NULL default '0',
      `pmconvoop_pmdate`          int(11)               NOT NULL default '0',
      PRIMARY KEY  (`pmconvoop_id`),
      UNIQUE KEY `INDEX` (`pmconvoop_pmconvo_id`,`pmconvoop_user_id`),
      KEY `total_outbox` (`pmconvoop_user_id`,`pmconvoop_deleted_outbox`,`pmconvoop_read`),
      KEY `last_pm_date` (`pmconvoop_pmdate`),
      KEY `total_inbox` (`pmconvoop_user_id`,`pmconvoop_deleted_inbox`,`pmconvoop_read`,`pmconvoop_pmdate`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8
  ";
  
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  
  $sql = "ALTER TABLE se_pmconvos ADD COLUMN pmconvo_recipients INT     UNSIGNED NOT NULL default 0";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  
  $sql = "
    ALTER TABLE se_pms
    ADD INDEX `pm_pmconvo_id` (`pm_pmconvo_id`),
    ADD INDEX `list_subquery` (`pm_pmconvo_id`,`pm_authoruser_id`,`pm_id`),
    ADD FULLTEXT `SEARCH` (`pm_body`)
  ";
  
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  
  $sql = "
    ALTER TABLE se_pmconvos
    ADD INDEX `pmconvo_recipients` (`pmconvo_recipients`),
    ADD FULLTEXT `SEARCH` (`pmconvo_subject`)
  ";
  
  $database->database_query($sql) or die($database->database_error()." ".$sql);
}





////######### STATS
$sql = "SELECT NULL FROM se_pmconvos";
$resource = $database->database_query($sql) or die($database->database_error()." ".$sql);
$pmconvo_total = $database->database_num_rows($resource);

$sql = "SELECT NULL FROM se_pmconvos WHERE pmconvo_recipients=0";
$resource = $database->database_query($sql) or die($database->database_error()." ".$sql);
$pmconvo_total_unupgraded = $database->database_num_rows($resource);

$pmconvo_total_upgraded = $pmconvo_total - $pmconvo_total_unupgraded;





//######### CONVERT se_pmconvos
$sql = "SHOW COLUMNS FROM se_pmconvos FROM `$database_name` LIKE 'pmconvo_collaborators'";
$resource = $database->database_query($sql) or die($database->database_error()." ".$sql);

if( $database->database_num_rows($resource) )
{
  $sql = "
    SELECT
      pmconvo_id,
      pmconvo_collaborators,
      pmconvo_read,
      pmconvo_deleted_outbox,
      pmconvo_deleted_inbox,
      (SELECT pm_date FROM se_pms WHERE se_pms.pm_pmconvo_id=se_pmconvos.pmconvo_id ORDER BY se_pms.pm_date DESC LIMIT 1) AS pm_date
    FROM
      se_pmconvos
    WHERE
      pmconvo_recipients=0
    LIMIT
      50
  ";
  
  $resource = $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  while( $pmconvo_info=$database->database_fetch_assoc($resource) )
  {
    $collaborators  = array_unique(array_filter(explode(',', $pmconvo_info['pmconvo_collaborators'])));
    $read           = array_unique(array_filter(explode(',', $pmconvo_info['pmconvo_read'])));
    $deleted_inbox  = array_unique(array_filter(explode(',', $pmconvo_info['pmconvo_deleted_inbox'])));
    $deleted_outbox = array_unique(array_filter(explode(',', $pmconvo_info['pmconvo_deleted_outbox'])));
    $recipients     = count($collaborators);
    
    // INSERT se_pmconvoops
    $sql = "
      INSERT INTO se_pmconvoops
        (pmconvoop_pmconvo_id, pmconvoop_user_id, pmconvoop_read, pmconvoop_deleted_inbox, pmconvoop_deleted_outbox, pmconvoop_pmdate)
      VALUES
    ";
    
    $is_first = TRUE;
    foreach( $collaborators as $collaborator_user_id )
    {
      if( !$is_first ) $sql .= ",";
      $is_read            = in_array($collaborator_user_id, $read);
      $is_deleted_inbox   = in_array($collaborator_user_id, $deleted_inbox);
      $is_deleted_outbox  = in_array($collaborator_user_id, $deleted_outbox);
      $sql .= "\n ('{$pmconvo_info['pmconvo_id']}', '{$collaborator_user_id}', '{$is_read}', '{$is_deleted_inbox}', '{$is_deleted_outbox}', '{$pmconvo_info['pm_date']}')";
      $is_first = FALSE;
    }
    
    $database->database_query($sql) or die($database->database_error()." ".$sql);
    
    // UPDATE se_pmconvos
    if( !$recipients ) $recipients = 1; // MEH, BUT IF ITS ZERO, THE UPGRADE SCRIPT WILL INFINITE LOOP. SIDES, IT SHOULD NEVER BE ZERO
    $sql = "UPDATE se_pmconvos SET pmconvo_recipients='{$recipients}' WHERE pmconvo_id='{$pmconvo_info['pmconvo_id']}' LIMIT 1";
    $database->database_query($sql) or die($database->database_error()." ".$sql);
  }
}





//######### DROP COLUMNS se_pmconvos
if( !$pmconvo_total_unupgraded )
{
  $sql = "SHOW COLUMNS FROM se_pmconvos FROM `$database_name` LIKE 'pmconvo_collaborators'";
  $resource = $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  // DROP COLUMNS se_pmconvos
  if( $database->database_num_rows($resource) )
  {
    $sql = "
      ALTER TABLE se_pmconvos
      DROP pmconvo_collaborators,
      DROP pmconvo_read,
      DROP pmconvo_deleted_outbox,
      DROP pmconvo_deleted_inbox
    ";
    
    $database->database_query($sql) or die($database->database_error()." ".$sql);
  }
  
  $sql = "ANALYZE TABLE se_pms";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  $sql = "ANALYZE TABLE se_pmconvos";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  $sql = "ANALYZE TABLE se_pmconvoops";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  $sql = "OPTIMIZE TABLE se_pms";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  $sql = "OPTIMIZE TABLE se_pmconvos";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  $sql = "OPTIMIZE TABLE se_pmconvoops";
  $database->database_query($sql) or die($database->database_error()." ".$sql);
  
  $result = "Done!";
}

?>
<html>
<head>
<script type="text/javascript">
  <?php if( $pmconvo_total_unupgraded ) { ?>
  window.onload = function()
  {
    window.location.href = unescape(window.location.pathname);
  }
  <?php } ?>
</script>
</head>
<body>

<p>Total PM Convos: <?php echo $pmconvo_total; ?></p>

<p>Total PM Convos (Upgraded): <?php echo $pmconvo_total_upgraded; ?></p>

<p>Total PM Convos (Remaining): <?php echo $pmconvo_total_unupgraded; ?></p>

<?php if( !empty($result) ) { ?><p><?php echo $result; ?></p><?php } ?>

</body>
</html>