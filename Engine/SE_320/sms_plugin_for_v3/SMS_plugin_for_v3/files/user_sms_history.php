<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_sms_history";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting[setting_permission_invite] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
$pms_per_page = 20;
if(isset($_POST['download'])){
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=history.csv");
header("Pragma: no-cache");
header("Expires: 0");
$sql = "SELECT message, date, tono, fromno FROM se_sms WHERE username = '".$user->user_info['user_username']."'";
$result = mysql_query($sql);
if (!$result) {
   echo "Could not successfully run query ($sql) from DB: " . mysql_error();
   exit;
}
echo "Date,From,To,Message\n";
while ($row = mysql_fetch_assoc($result)) {
echo "$row[date],$row[fromno],$row[tono],$row[message]";
echo "\n";
}
}
 $total_pms = $sms->smsaddressbook(1, 0);
 $p;
 $page_vars = make_page($total_pms, $pms_per_page, $p);
// GET ARRAY OF MESSAGES
 $pms = $sms->sms_addressbook_list($page_vars[0], $pms_per_page, 1);
// SET EMPTY VARS
$is_error = 0;
$result = 0;
$step=0;

$global_page_title[0] = 1074;
$global_page_description[0] = 1075;
$smarty->assign('total_pms', $total_pms);
$smarty->assign_by_ref('pms', $pms);
// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($pms));
$smarty->assign('is_error', $is_error);

include "footer.php";
?>