<?php
//echo $num=$database->database_num_rows($database->database_query("SELECT * FROM se_global_sms "));
$page = "global_sms";
include "admin_header.php";
if($_POST[submit]=="Save")
{
$apiid=$_POST[apiid];
$password=$_POST[password];
$sms_userid=$_POST[sms_userid];
$email=$_POST[email];
$currency_sign=$_POST[currency_sign];
$currency_name=$_POST[currency_name];

$sel_qry1=$database->database_query("select * from  se_global_sms WHERE id='1'");
$views = $database->database_fetch_assoc($sel_qry1);
if($views=="")
	$insert_query = $database->database_query("insert into se_global_sms set apiid='$apiid',password='$password', sms_userid='$sms_userid',email='$email',currency_sign='$currency_sign',currency_name='$currency_name'");

else
	$database->database_query("UPDATE se_global_sms SET apiid='$apiid',password='$password', sms_userid='$sms_userid',email='$email',currency_sign='$currency_sign',currency_name='$currency_name' WHERE id='1'");
	
$dmsg="Saved Successfully";	
	
}
$sel_qry1=$database->database_query("select * from  se_global_sms WHERE id='1'");
$views = $database->database_fetch_assoc($sel_qry1);
$smarty->assign("sel_qry",$views);
$smarty->assign("dmsg",$dmsg);
include "admin_footer.php";
?>