<?php

/* $Id: admin_levels_filesettings.php 2 2009-01-10 20:53:09Z john $ */

$page = "admin_levels_filesettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { header("Location: admin_levels.php"); exit(); }
$level_info = $database->database_fetch_assoc($level);

// SET RESULT VARIABLE
$result = 0;
$is_error = 0;


// SAVE CHANGES
if($task == "dosave")
{
 // $level_info['level_file_upload_allow']    = $_POST['level_file_upload_allow'];
  $level_info['level_file_upload_exts']     = str_replace(", ", ",", $_POST['level_file_upload_exts']);
    $level_info['level_file_upload_maxsize']  = $_POST['level_file_upload_maxsize'];
 // $level_info['level_file_upload_maxnum']   = $_POST['level_file_upload_maxnum'];
    
  // CHECK THAT A NUMBER BETWEEN 1 AND 40096 (4MB) WAS ENTERED FOR MAXSIZE
  if(!is_numeric($level_info[level_file_upload_maxsize]) || $level_info[level_file_upload_maxsize] < 1 || $level_info[level_file_upload_maxsize] > 400096)
  {
    $is_error = 7800027;
  }
  
    // CHECK THAT MAX FILES IS A NUMBER
//   elseif(!is_numeric($level_info[level_file_upload_maxnum]) || $level_info[level_file_upload_maxnum] < 1 || $level_info[level_file_upload_maxnum] > 999)
//   {
//     $is_error = 7800029;
//   }
  
  else
  {
    $level_info[level_file_upload_maxsize] = $level_info[level_file_upload_maxsize]*1024;


//     echo   "
//         UPDATE se_levels SET 
//   			level_file_upload_allow='$level_info[level_file_upload_allow]',
//   			level_file_upload_maxnum='$level_info[level_file_upload_maxnum]',
//   			level_file_upload_exts='$level_info[level_file_upload_exts]',
//   			level_file_upload_maxsize='$level_info[level_file_upload_maxsize]'
//         WHERE level_id='$level_info[level_id]' LIMIT 1
//       ";
// die;
    mysql_query("
      UPDATE se_levels SET 
 			level_file_upload_exts='$level_info[level_file_upload_exts]',
			level_file_upload_maxsize='$level_info[level_file_upload_maxsize]'
			
      WHERE level_id='$level_info[level_id]' LIMIT 1
    ");

   /* if( !$level_info['level_userupload_search'] )
    {
      $database->database_query("UPDATE se_fileuploads INNER JOIN se_users ON se_fileuploads.userupload_userid=se_users.user_id SET se_fileuploads.userupload_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
   */ 
    	
    $result = 1;
  }
} // END DOSAVE TASK



// ADD SPACES AFTER COMMAS
$level_info[level_file_upload_exts] = str_replace(",", ", ", $level_info[level_file_upload_exts]);
$level_info[level_file_upload_maxsize] = $level_info[level_file_upload_maxsize]/1024;


// ASSIGN VARIABLES AND SHOW ALBUM SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);
include "admin_footer.php";
?>