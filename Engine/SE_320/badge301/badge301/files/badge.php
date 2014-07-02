<?php

$page = "badge";
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

$p = rc_toolkit::get_request('p', 1);
$s = rc_toolkit::get_request('s', "badgeassignment_dateapproved DESC");
$badge_id = rc_toolkit::get_request('badge_id');

$badge = new se_badge(null, $badge_id);
if (!$badge->badge_exists) {
  rc_toolkit::redirect("home.php");
}

if (!in_array($s, array('badgeassignment_dateapproved DESC',
'badgeassignment_dateapproved ASC',
'user_dateupdated DESC',
'user_dateupdated ASC',
'user_lastlogindate DESC',
'user_lastlogindate ASC',
'user_signupdate DESC',
'user_signupdate ASC'))) {
  $s = "badgeassignment_dateapproved DESC";
}

$se_badgeassignment = new se_badgeassignment(null, null, $badge_id);
$entries_per_page = 30;

$where = "badgeassignment_approved = '1'";
$total_badgeassignments = $se_badgeassignment->badgeassignment_total($where);

$page_vars = make_page($total_badgeassignments, $entries_per_page, $p);

$badgeassignments = $se_badgeassignment->badgeassignment_list($page_vars[0], $entries_per_page, $s, $where);

$badge->badge_info[badge_desc] = html_entity_decode($badge->badge_info[badge_desc], ENT_QUOTES);

$badgecat_id = $badge->badge_info['badge_badgecat_id'];
$other_badges = $badge->badge_list(0, 10, "RAND()", "badge_id != '$badge_id' AND badge_search='1' AND (badge_badgecat_id='$badgecat_id' OR badgecat_dependency='$badgecat_id')");
///rc_toolkit::debug($other_badges,'other_badges');

$can_add_badge = $user->user_exists ? $badge->can_add_badge($user) : false;


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 11270157;
$global_page_title[1] = $badge->badge_info[badge_title];
$global_page_description[0] = 11270102;
$global_page_description[1] = rc_toolkit::truncate_text(str_replace("\r\n"," ",strip_tags($badge->badge_info[badge_desc])), 200);

$smarty->assign('badge', $badge);
$smarty->assign('badge_id', $badge->badge_info['badge_id']);

$smarty->assign('badgeassignments', $badgeassignments);
$smarty->assign('total_badgeassignments', $total_badgeassignments);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($badgeassignments));

$smarty->assign('other_badges', $other_badges);
$smarty->assign('can_add_badge', $can_add_badge);
$smarty->assign('s', $s);

include "footer.php";

