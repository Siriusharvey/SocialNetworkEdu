<?php

$page = "browse_badges";
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
$s = rc_toolkit::get_request('s');

$badgecat_id = rc_toolkit::get_request('badgecat_id');

$sort_map = array(
  'date' => "badge_datecreated DESC",
  'member' => "total_approved DESC"
);

if (!array_key_exists($s, $sort_map)) {
  $s = "date";
}


$where = "badge_search='1'";

// CREATE BADGE OBJECT
$badge = new se_badge();
$cat_array = $badge->badge_categories();

$badgecat_ids = rc_toolkit::flaten_field_cats($cat_array);
$badgecat_languagevar_id = null;
if (array_key_exists($badgecat_id, $badgecat_ids)) {
  $badgecat_languagevar_id = $badgecat_ids[$badgecat_id];
  $where .= " AND (badge_badgecat_id='$badgecat_id' OR badgecat_dependency='$badgecat_id')";
}
else {
  $badgecat_id = 0;
}


// GET TOTAL BADGES
$total_badges = $badge->badge_total($where);

// MAKE ENTRY PAGES
$badges_per_page = 15;
$page_vars = make_page($total_badges, $badges_per_page, $p);

// GET BADGE ARRAY
$badge_array = $badge->badge_list($page_vars[0], $badges_per_page, $sort_map[$s], $where, 1);



// ASSIGN SMARTY VARIABLES AND DISPLAY BADGES PAGE
$smarty->assign('cats', $cat_array);
$smarty->assign('badgecat_languagevar_id', $badgecat_languagevar_id);
$smarty->assign('badgecat_id', $badgecat_id);

$smarty->assign('badges', $badge_array);
$smarty->assign('total_badges', $total_badges);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($badge_array));

$smarty->assign('s', $s);

include "footer.php";
