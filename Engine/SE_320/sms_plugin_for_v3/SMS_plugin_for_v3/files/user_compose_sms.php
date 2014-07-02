<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_compose_sms";
include "header.php";




// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting[setting_permission_invite] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}
##echo $username = $user->user_info['user_username'];

if(isset($_POST['Submit']))
{

	$userid=$user->user_info['user_id'];
	
	//$smsexp_resource = $database->database_query("select user_id from se_users where sms_expire >= NOW() and user_id='$userid'");
	$smsexp_resource = $database->database_query("select user_id from se_user_smssetting where ssms_credits !='0' and user_id='$userid'");
	$smsexp_result = $database->database_fetch_assoc($smsexp_resource);
	
	if($smsexp_result[user_id] == "")
	{
	header("Location: user_buy_credits.php");
	exit;
	}
	else
	{
			
	/*$sql_update="UPDATE se_users set ssms_credits=ssms_credits-1 where user_id='$userid'";
	$res_update=mysql_query($sql_update);*/


	$smsexp_resource = $database->database_query("select mobile_no from se_user_smssetting where  user_id='$userid'");
	$smsexp_result = $database->database_fetch_assoc($smsexp_resource);
	$from=$smsexp_result[mobile_no];

	 //$msi = $_POST['receiver'];
	 $txt=$_POST['message']."\n Sender Phone Number:".$from;
	 $dt=$from;
	 $tudei = date('Y-m-d');
	 $username = $user->user_info['user_username'];
	 
	 $msi = $_POST['receiver'];
	 $msi_all=@explode(",",$msi);
	
    
	for($i=0; $i<count($msi_all);$i++)
	{
		$api_mess = "";
		$msi = $msi_all[$i];
		
		include "clickatell.php";
		if($api_mess == "sent")
		{
		
			$sql_insert="INSERT INTO se_sms (username, message, date, tono,fromno) VALUES('".$username."','".$txt."','".$tudei."','".$msi."','".$dt."')";
				$smssend_ins=$database->database_query($sql_insert);
				$smsdet_ins = $database->database_query("update se_user_smssetting set ssms_credits=ssms_credits-1 where user_id='$userid'");
				$smarty->assign('dmsg', 'Sent Successfully!');
				
			
		}
		else
			$smarty->assign('dmsg', 'Send Failed!');
		
	}
 
 	
	
	

	 
	$head=explode(' ',$txt,2);
	$header=$head[1];
	//$sql_insert="INSERT INTO se_sms (username, message, date, tono,fromno) VALUES('".$username."','".$txt."','".$tudei."','".$msi."','".$dt."')";
	//$res_insert=mysql_query($sql_insert);
	$sql="select a.shop_name as shop_name from shop_keyword b LEFT JOIN shop_info a ON b.shop_id=a.shop_id where keyword='".$header."'";
	$res= mysql_query($sql);
	while($row1 = @mysql_fetch_array($res)){
	extract($row1);
	echo $shop[]=$row1['shop_name'];
		
}
$values = implode(",", $shop);


//$url="http://sms.globalbulksms.com/sendsmsv2.asp";

$s_POST_DATA = "user=blackpepper";
$s_POST_DATA .= "&password=kapilnawani"; 
$s_POST_DATA .= "&sender=blackpepper";
$s_POST_DATA .= "&
Number=$msi";
$s_POST_DATA .= "&text=$values";



//$mydata = kapil($url,$s_POST_DATA);
##$sql="select tono from se_sms where id='$user->user_info['user_id']'";
##$result=mysql_query($sql);
##$row=mysql_fetch_array($result);
##echo $row['tono'];
##template->set('$tono',$row['tono']);

//$smarty->assign("dmsg","SMS Send Successfully");
}
}

  $username = $user->user_info['user_username'];

$con=mysql_connect('localhost','inawatec','kapilnawani');
mysql_select_db('inawatec_socialengine',$con);
 $sql="select mobile_no from se_users where user_username='".$username."'";
$res2=mysql_query($sql);
$row2 = @mysql_fetch_array($res2);

##extract($row2);
 $tono=str_replace("%2C",",",urlencode($_GET['phone']));
 $fromno=$row2['mobile_no'];

// SET EMPTY VARS
$is_error = 0;
$result = 0;
$step=0;

$global_page_title[0] = 1074;
$global_page_description[0] = 1075;
// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('tono', "$tono");
$smarty->assign('fromno', "$fromno");

##$compose_sms.tpl->assign('tep', '111');

include "footer.php";
?>