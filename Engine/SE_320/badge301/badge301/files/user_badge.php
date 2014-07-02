<?php

$page = "user_badge";
include "header.php";

$task = rc_toolkit::get_request('task','main');
$p = rc_toolkit::get_request('p',1);
$search = rc_toolkit::get_request('search');
$badge_id = rc_toolkit::get_request('badge_id');
$badgeassignment_id = rc_toolkit::get_request('badgeassignment_id');

$result = 0;
$is_error = 0;

// ENSURE BADGE IS ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_badge_allow'] & 3 ) {
  rc_toolkit::redirect("user_home.php");
}


if ($task == "add") {
  $badge = new se_badge(NULL, $badge_id);
  if ($badge->can_add_badge($user)) {
    $badgeassignment = new se_badgeassignment();
    $badgeassignment_id = $badgeassignment->create_user_badge($user, $badge);
    if ($badgeassignment_id) {
      if ($badgeassignment->badgeassignment_info['badgeassignment_approved']) {
        $badgeassignment->newbadgeassignment_action();
        $result = 11270070;
      }
      else {
        $result = 11270071;
      }
      if ($badgeassignment->badgeassignment_info['badgeassignment_epayment']) {
        $result = 11270072;
      }
    }
  }
}
elseif ($task == "delete") {
  $badgeassignment = new se_badgeassignment($badgeassignment_id);
  if ($badgeassignment->badgeassignment_exists 
    && $badgeassignment->badgeassignment_info['badgeassignment_user_id'] == $user->user_info['user_id']) {
    $badgeassignment->badgeassignment_delete();
    $result = 11270038;
  }
}
elseif ($task == "update") {
  $value = rc_toolkit::get_request('badgeassignment_profile') ? 1 : 0;
  $database->database_query("UPDATE se_badgeassignments SET badgeassignment_profile = '$value' WHERE badgeassignment_id='$badgeassignment_id' AND badgeassignment_user_id='{$user->user_info['user_id']}'");
  $result = 11270153;
}


$se_badgeassignment = new se_badgeassignment(null, $user->user_info['user_id'], null);

$entries_per_page = 10;

$where = "";
$total_badgeassignments = $se_badgeassignment->badgeassignment_total($where, 1);

$page_vars = make_page($total_badgeassignments, $entries_per_page, $p);

$badgeassignments = $se_badgeassignment->badgeassignment_list($page_vars[0], $entries_per_page, "badgeassignment_datecreated DESC", $where, 1);

// ASSIGN VARIABLES AND SHOW VIEW BADGES PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

$smarty->assign('badgeassignments', $badgeassignments);
$smarty->assign('total_badgeassignments', $total_badgeassignments);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($badgeassignments));


include "footer.php";
