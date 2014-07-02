<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_buy_credits";
include "header.php";

if($_GET[success]=="yes")
{
		$dmsg="Paid Successfully";
		$smarty->assign("dmsg",$dmsg);
			
}

if($_GET[success]=="no")
{
		$dmsg="Payment Failed !";
		$smarty->assign("dmsg",$dmsg);
			
}
//echo $web_url=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$web_url=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$web_url2=$sret=str_replace("user_buy_credits.php","",$web_url);
$smarty->assign("web_url2",$web_url2);
$smarty->assign("web_url",$web_url);

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
$total_pms = $sms->subscription(1, 0);

 $page_vars = make_page($total_pms, $pms_per_page, $p);
// GET ARRAY OF MESSAGES
 $pms = $sms->subscription_list($page_vars[0], $pms_per_page, 1);
// SET EMPTY VARS
$is_error = 0;
$result = 0;
$step=0;
if($_POST['task1']=="next_task"){
$step=1;
}
$global_page_title[0] = 1074;
$global_page_description[0] = 1075;
// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('total_pms', $total_pms);
$smarty->assign_by_ref('pms', $pms);
// ASSIGN VARIABLES AND INCLUDE FOOTER
$userid=$user->user_info['user_id'];
$smarty->assign('userid', $userid);
/*-------------------------------------paypal Email-------------------------------------*/
$sel_qry1=$database->database_query("select email,currency_name from  se_global_sms WHERE id='1'");
$views = $database->database_fetch_assoc($sel_qry1);
$smarty->assign('views', $views);


/*-------------------------------------paypal Email-------------------------------------*/
/*-------------------------------------paypal Button-------------------------------------*/
$sel_paypal=$database->database_query("select * from  se_subscription");
$tot=$database->database_num_rows($sel_paypal);

$smarty->assign('tot', $tot);

/*-------------------------------------paypal Button-------------------------------------*/



$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($pms));
$smarty->assign('step', $step);
include "footer.php";
?>