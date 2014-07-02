<?php

$page = "badgeusers";
include "header.php";

//rc_toolkit::debug($user);

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
$s = rc_toolkit::get_request('s', "user_dateupdated DESC");

$type = rc_toolkit::get_request('type');
$type_id = rc_toolkit::get_request('type_id');

$badge_type_maps = unserialize($setting["setting_badge_{$type}s"]);
$badge_id = $badge_type_maps[$type_id];
//rc_toolkit::debug($badge_type_maps, $badge_id);

if (!in_array($type, array('level','subnet','profilecat')) || !$badge_id) {
  rc_toolkit::redirect("home.php");
}

$badge = new se_badge(null, $badge_id);
if (!$badge->badge_exists) {
  rc_toolkit::redirect("home.php");
}

$badge->badge_info[badge_desc] = html_entity_decode($badge->badge_info[badge_desc], ENT_QUOTES);


$users_per_page = 30;

$where = "user_{$type}_id = '$type_id' AND se_users.user_enabled='1'";
$sql = "SELECT COUNT(user_id) as total FROM se_users WHERE $where";
//rc_toolkit::debug($sql, 'count');
$total_result = $database->database_fetch_assoc($database->database_query($sql));
$total_users = $total_result['total'] ? $total_result['total'] : 0;

$page_vars = make_page($total_users, $users_per_page, $p);

$sort = "user_id desc";

$sql = "SELECT se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname, se_users.user_photo 
  FROM se_users
  WHERE $where
    ORDER BY $s LIMIT $page_vars[0], $users_per_page 
  ";
//rc_toolkit::debug($sql, 'select');
    
$res = $database->database_query($sql);
    
$user_array = array();
while ($user_info = $database->database_fetch_assoc($res))
{
  $user_array[] = rc_toolkit::init_se_user_from_data($user_info);  
}
    
//rc_toolkit::debug($user_array,'user_array');

// LOAD OTHER TYPE BADGES ----

$type_badges = $badge->get_type_badges($type);


// LOAD TYPE TITLE -----------
if ($type == 'level') {
  $sql = "SELECT level_name as type_title FROM se_levels WHERE level_id = '$type_id'";
}
elseif ($type == 'subnet') {
  $sql = "SELECT subnet_name as type_title FROM se_subnets WHERE subnet_id = '$type_id'";
}
elseif ($type == 'profilecat') {
  $sql = "SELECT profilecat_title as type_title FROM se_profilecats WHERE profilecat_id = '$type_id'";
}
$type_row = $database->database_fetch_assoc($database->database_query($sql));
$type_title = $type_row['type_title'];
if ($type == 'subnet' || $type == 'profilecat') {
  $type_title = SELanguage::_get($type_title);
}




$smarty->assign('badge', $badge);
$smarty->assign('type', $type);
$smarty->assign('type_id', $type_id);
$smarty->assign('type_title', $type_title);
$smarty->assign('type_badges', $type_badges);

$smarty->assign('users', $user_array);
$smarty->assign('total_users', $total_users);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($user_array));

$smarty->assign('s', $s);

include "footer.php";
