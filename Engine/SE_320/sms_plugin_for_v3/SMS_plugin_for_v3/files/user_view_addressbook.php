<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_view_addressbook";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting[setting_permission_invite] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
 
}
if($_GET[id]!="")
{
$id=$_GET[id];
$id=@explode(",",$id);
$id1=$id[0];
$id2=$id[1];

$sql="DELETE FROM se_addressbook  WHERE id='$id1' AND owner = '".$user->user_info['user_username']."'";
$res=mysql_query($sql);
$sq2="DELETE FROM se_addressbook  WHERE id='$id2' AND owner = '".$user->user_info['user_username']."'";
$res=mysql_query($sq2);
$smarty->assign("dmsg","Deleted Successfully");
}

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
$pms_per_page = 20;
 
// SET EMPTY VARS
$is_error = 0;
$result = 0;
$step=0;
if($_REQUEST['id'] !=""){
//$sql="DELETE FROM se_addressbook  WHERE id='".$_REQUEST['id']."' AND owner = '".$user->user_info['user_username']."'";
//$res=mysql_query($sql);
}
$global_page_title[0] = 1074;
$global_page_description[0] = 1075;
// ASSIGN VARIABLES AND INCLUDE FOOTER
 $total_pms = $sms->addressbook(1, 0);


// MAKE PM PAGES

 $page_vars = make_page($total_pms, $pms_per_page, $p);
// GET ARRAY OF MESSAGES
 $pms = $sms->user_addressbook_list($page_vars[0], $pms_per_page, 1);

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('total_pms', $total_pms);
$smarty->assign_by_ref('pms', $pms);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($pms));
$smarty->assign('is_error', $is_error);
$smarty->assign('step', $step);
include "footer.php";
?>