<?php
//echo $num=$database->database_num_rows($database->database_query("SELECT * FROM se_global_sms "));
$page = "sms_sent";
include "admin_header.php";
if($_POST[submit]=="Save")
{
$sms_message=$_POST[sms_message];
$txt = urlencode($sms_message);

//echo "select mobile_no,user_id from  se_user_smssetting  WHERE admin_sms='1'";
$messege_send=$database->database_query("select mobile_no,user_id from  se_user_smssetting  WHERE admin_sms='1'");

$mobile_numbers = $database->database_fetch_assoc($messege_send);

if(is_array($mobile_numbers)){
for($i=0;$i<count($mobile_numbers);$i++)
{

$msi=$mobile_numbers[$i][mobile_no];
include "../clickatell.php";
$smarty->assign('dmsg', 'Send Success!');


}
}
else
$smarty->assign('dmsg', 'There is no records!');
	

	
}
include "admin_footer.php";
?>