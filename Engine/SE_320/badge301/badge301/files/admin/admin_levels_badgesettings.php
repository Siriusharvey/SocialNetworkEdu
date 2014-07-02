<?php

$page = "admin_levels_badgesettings";
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
if($task == "dosave") {
  $level_info[level_badge_allow] = $_POST['level_badge_allow'];
  $level_info[level_badge_edit] = $_POST['level_badge_edit'] ? 1 : 0;
  $level_info[level_badge_delete] = $_POST['level_badge_delete'] ? 1 : 0;
  $level_info[level_badge_maxnum] = (int)$_POST['level_badge_maxnum'];
  
  
    $database->database_query("UPDATE se_levels SET 
			level_badge_allow='$level_info[level_badge_allow]',
			level_badge_edit='$level_info[level_badge_edit]',
			level_badge_delete='$level_info[level_badge_delete]',
			level_badge_maxnum='$level_info[level_badge_maxnum]'
			WHERE level_id='{$level_info['level_id']}'
    ");

	if (!$level_info[level_badge_edit]) {
    $database->database_query("UPDATE se_badgeassignments JOIN se_users ON badgeassignment_user_id=se_users.user_id SET badgeassignment_profile='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
	}
			
    $result = 1;

} // END DOSAVE TASK



//rc_toolkit::debug($level_info);


// ASSIGN VARIABLES AND SHOW badge SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);

include "admin_footer.php";
