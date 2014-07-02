<?php

$page = "admin_badges";
include "admin_header.php";

$task = rc_toolkit::get_request('task');

$s = rc_toolkit::get_request('s', 'title');
$p = rc_toolkit::get_request('p', 1);
$f_title = rc_toolkit::get_request('f_title', '');
$f_catid = rc_toolkit::get_request('f_catid','');
$task = rc_toolkit::get_request('task', 'main');
$badge_id = rc_toolkit::get_request('badge_id', 0);

// CREATE BADGE OBJECT
$badges_per_page = 20;
$badge = new se_badge();

$is_error = FALSE;
$result = FALSE;

// DELETE BADGE
if($task == "deletebadge") {
  if($database->database_num_rows($database->database_query("SELECT badge_id FROM se_badges WHERE badge_id='$badge_id'")) == 1) { 
    $badge->badge_delete($badge_id);
  }
}
else if ($task == "create") {
  
  $title = rc_toolkit::get_request('badge_title');
  $badgecat_id = rc_toolkit::get_request('badge_badgecat_id');
  
  if ($title && $badgecat_id) {
    
    $badge_data = array(
      'badge_title' => $title,
      'badge_badgecat_id' => $badgecat_id
    );
    
    $badge_id = $badge->badge_edit($badge_data);
    
    if ($badge_id) {
      rc_toolkit::redirect("admin_badge_edit.php?badge_id=$badge_id");
    }
    
  }
  else {
    $is_error = 11270001;
  }
}
else if ($task == "assign" && $badge_id) {

  $user = new se_user(array(0,rc_toolkit::get_post('username')));
  $badge = new se_badge(NULL, $badge_id);
  
  if (!$badge->badge_exists) {
    $is_error = 11270002;
  }
  else if (!$user->user_exists) {
    $is_error = 11270003;
  }
  else if ($badge->has_badge($user->user_info['user_id'])) {
    $is_error = 11270170;
  }
  else
  {
    $badgeassignment = new se_badgeassignment();
    $badgeassignment_id = $badgeassignment->create_user_badge($user, $badge);
    if ($badgeassignment_id) {
      rc_toolkit::redirect("admin_badgeassignment_edit.php?badgeassignment_id=$badgeassignment_id");
    }
  }
  
}


$cs = array();
if ($f_title != "") {
  $cs['title'] = "se_badges.badge_title LIKE '%$f_title%'";
}
if ($f_catid > 0) {
  $cs['catid'] = "se_badges.badge_badgecat_id = '$f_catid' OR badgecat_dependency='$f_catid'";
}

$where = rc_toolkit::criteria_builder($cs,'AND',false);
if ($s == 'title') {
  $sort = "badge_title ASC";
}
else if ($s == 'member') {
  $sort = "total_assignments DESC";
}
else {
  $sort = "badge_id DESC";
}
// DELETE NECESSARY BADGES
$start = ($p - 1) * $badges_per_page;

// GET TOTAL BADGES
$total_badges = $badge->badge_total($where);

// MAKE BADGE PAGES
$page_vars = make_page($total_badges, $badges_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
        'link' => $link);
}

// GET BADGE ARRAY
$badges = $badge->badge_list($page_vars[0], $badges_per_page, $sort, $where);

//rc_toolkit::debug($badges);

$smarty->assign_by_ref('cats', $badge->badge_categories());



// ASSIGN VARIABLES AND SHOW VIEW BADGES PAGE
$smarty->assign('total_badges', $total_badges);
$smarty->assign('pages', $page_array);
$smarty->assign('badges', $badges);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_catid', $f_catid);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);

$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

include "admin_footer.php";

