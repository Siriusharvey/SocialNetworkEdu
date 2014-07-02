<?php

$page = "admin_badgeassignment_edit";
include "admin_header.php";

$task = rc_toolkit::get_request('task');
$badgeassignment_id = rc_toolkit::get_request('badgeassignment_id', 0);

$is_error = FALSE;
$result = FALSE;

$badgeassignment = new se_badgeassignment($badgeassignment_id);

if (!$badgeassignment->badgeassignment_exists) {
  rc_toolkit::redirect("admin_badgeassignments.php");
}

if ($task == "dosave") {
  
  $badgeassignment->badgeassignment_info['badgeassignment_desc'] = $_POST['badgeassignment_desc'];
  
  $sql = "UPDATE se_badgeassignments SET badgeassignment_desc = '{$badgeassignment->badgeassignment_info['badgeassignment_desc']}' WHERE badgeassignment_id = '$badgeassignment_id'";
  $database->database_query($sql);
    
  $result = 1;
}

$badgeassignment->badgeassignment_info['badgeassignment_desc'] = str_replace("\r\n", "", html_entity_decode($badgeassignment->badgeassignment_info['badgeassignment_desc']));

//rc_toolkit::debug($badgeassignment->badgeassignment_info['badgeassignment_desc'],'dd');
$smarty->assign('badgeassignment', $badgeassignment);

$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

include "admin_footer.php";
