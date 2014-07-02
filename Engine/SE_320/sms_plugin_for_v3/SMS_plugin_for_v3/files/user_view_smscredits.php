<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_view_smscredits";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting[setting_permission_invite] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

$is_error = 0;
$result = 0;
$step=0;
if($_POST['task1']=="next_task"){
$step=1;
}
$global_page_title[0] = 1074;
$global_page_description[0] = 1075;
$credit_query = $database->database_query("SELECT rsms_credits, ssms_credits  FROM se_user_smssetting  WHERE user_id='{$user->user_info['user_id']}'");
$credit_info = $database->database_fetch_assoc($credit_query);
$rsms_credits=$credit_info['rsms_credits'];
$ssms_credits=$credit_info['ssms_credits'];
$smarty->assign('rsms_credits', $rsms_credits);
$smarty->assign('ssms_credits', $ssms_credits);
// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);

$smarty->assign('step', $step);
include "footer.php";
?>