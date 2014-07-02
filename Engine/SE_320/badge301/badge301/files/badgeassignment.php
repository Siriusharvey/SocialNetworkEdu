<?php

$page = "badgeassignment";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_badge']) || ($user->user_exists && (1 & ~(int)$user->level_info['level_badge_allow'])) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

$badgeassignment_id = rc_toolkit::get_request('badgeassignment_id');

$badgeassignment = new se_badgeassignment($badgeassignment_id);
if (!$badgeassignment->badgeassignment_exists) {
  rc_toolkit::redirect("home.php");
}

if (!$badgeassignment->badgeassignment_info['badgeassignment_approved'])
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 11270162);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


$badgeassignment->badgeassignment_info['badgeassignment_desc'] = str_replace("\r\n", "", html_entity_decode($badgeassignment->badgeassignment_info['badgeassignment_desc']));


$smarty->assign('badgeassignment', $badgeassignment);

include "footer.php";
