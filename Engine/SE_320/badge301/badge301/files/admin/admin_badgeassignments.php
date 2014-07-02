<?php

$page = "admin_badgeassignments";
include "admin_header.php";

$task = rc_toolkit::get_request('task');

$s = rc_toolkit::get_request('s', 'id');
$p = rc_toolkit::get_request('p', 1);
$f_badgeid = rc_toolkit::get_request('f_badgeid', '');
$f_username = rc_toolkit::get_request('f_username','');
$f_approved = rc_toolkit::get_request('f_approved');
$f_epayment = rc_toolkit::get_request('f_epayment');
$f_transaction = rc_toolkit::get_request('f_transaction');
$task = rc_toolkit::get_request('task', 'main');
$badgeassignment_id = rc_toolkit::get_request('badgeassignment_id', 0);

// CREATE BADGE OBJECT
$badgeassignments_per_page = 20;
$badgeassignment = new se_badgeassignment();

$is_error = FALSE;
$result = FALSE;

// DELETE BADGE
if($task == "deletebadgeassignment") {
  if($database->database_num_rows($database->database_query("SELECT badgeassignment_id FROM se_badgeassignments WHERE badgeassignment_id='$badgeassignment_id'")) == 1) { 
    $badgeassignment->badgeassignment_delete($badgeassignment_id);
    $result = TRUE;
  }
}
elseif ($task == "update_approved" && $badgeassignment_id) {
  $item = new se_badgeassignment($badgeassignment_id);
  if ($item->badgeassignment_exists) {
    $item->badgeassignment_approve(rc_toolkit::get_request('value'));
    $result = 1;
  }
}
elseif ($task == "update_epayment" && $badgeassignment_id) {
  
  $value = rc_toolkit::get_request('value') ? 1 : 0;
  
  $sql = "UPDATE se_badgeassignments SET badgeassignment_epayment = '$value' WHERE badgeassignment_id = '$badgeassignment_id'";
  $database->database_query($sql);

  $result = 1;

}
elseif ($task == "update" && $badgeassignment_id) {
  $value = rc_toolkit::get_post('badgeassignment_desc');
  $sql = "UPDATE se_badgeassignments SET badgeassignment_desc = '$value' WHERE badgeassignment_id = '$badgeassignment_id'";
  $database->database_query($sql);

  $result = 1;
}
if (rc_toolkit::get_request('justadded')) {
  $result = 1;
}


$yn = array('y'=>1,'n'=>0);

$cs = array();
if ($f_badgeid != "") {
  $cs['badgeid'] = "se_badgeassignments.badgeassignment_badge_id = '$f_badgeid'";
}
if ($f_username != "") {
  $cs['username'] = "se_users.user_username = '$f_username'";
}
if ($f_approved) {
  $cs['approved'] = "se_badgeassignments.badgeassignment_approved='{$yn[$f_approved]}'";
}
if ($f_epayment) {
  $cs['epayment'] = "se_badgeassignments.badgeassignment_epayment='{$yn[$f_epayment]}'";
}
if ($f_transaction == 'y') {
  $cs['transaction'] = "epaymenttransaction_id IS NOT NULL";
}
elseif ($f_transaction == 'n') {
  $cs['transaction'] = "epaymenttransaction_id IS NULL";
}

$where = rc_toolkit::criteria_builder($cs,'AND',false);

if ($s == 'badge') {
  $sort = "se_badges.badge_title ASC";
}
else if ($s == 'user') {
  $sort = "se_users.user_username ASC";
}
else {
  $sort = "badgeassignment_id DESC";
}

// DELETE NECESSARY BADGES
$start = ($p - 1) * $badgeassignments_per_page;

// GET TOTAL BADGES
$total_badgeassignments = $badgeassignment->badgeassignment_total($where, 1);

// MAKE BADGE PAGES
$page_vars = make_page($total_badgeassignments, $badgeassignments_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
        'link' => $link);
}

// GET BADGE ARRAY
$badgeassignments = $badgeassignment->badgeassignment_list($page_vars[0], $badgeassignments_per_page, $sort, $where, 1);

//rc_toolkit::debug($where, '$where');
//rc_toolkit::debug($badgeassignments, '$badgeassignments');

// ASSIGN VARIABLES AND SHOW VIEW BADGES PAGE
$smarty->assign('total_badgeassignments', $total_badgeassignments);
$smarty->assign('pages', $page_array);
$smarty->assign('badgeassignments', $badgeassignments);

$smarty->assign('f_badgeid', $f_badgeid);
$smarty->assign('f_username', $f_username);
$smarty->assign('f_approved', $f_approved);
$smarty->assign('f_epayment', $f_epayment);
$smarty->assign('f_transaction', $f_transaction);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);

$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

include "admin_footer.php";

