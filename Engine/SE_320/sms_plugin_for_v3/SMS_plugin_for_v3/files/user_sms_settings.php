<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_sms_settings";
include "header.php";

$userid=$user->user_info['user_id'];
if(isset($_POST['Submit']))
{
$mobile_no=$_POST[mobile_no];
$member_sms= $_POST[member_sms];
$admin_sms =$_POST[admin_sms];

$sms_row_exist = $database->database_query("select user_id from se_user_smssetting where  user_id='$userid'");
$smsexp_result_exist = $database->database_fetch_assoc($sms_row_exist);
$mobile_exist=$smsexp_result_exist[user_id];

if($mobile_exist=="")
{
$smsexp_resource = $database->database_query("insert into  se_user_smssetting set mobile_no='$mobile_no',member_sms='$member_sms',admin_sms='$admin_sms',user_id='$userid'");
$smarty->assign("dmsg","Inserted Successfully");	

}
else
{

	$smsexp_resource = $database->database_query("update se_user_smssetting set mobile_no='$mobile_no',member_sms='$member_sms',admin_sms='$admin_sms' where user_id='$userid'");
$smarty->assign("dmsg","Updated Successfully");	
}
		
}

$sms_row = $database->database_query("select * from se_user_smssetting where  user_id='$userid'");
$smsexp_result = $database->database_fetch_assoc($sms_row);
$smarty->assign("smsexp_result",$smsexp_result);
include "footer.php";
?>