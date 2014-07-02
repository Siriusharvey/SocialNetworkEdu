<?php
$page = "sms_package";
include "admin_header.php";

if($_GET[act]=="delete")
{
$database->database_query("delete from se_subscription  WHERE sno='$_GET[id]'");
$msg="Deleted Successfully";
}


if($_POST[submit]=="Save")
{
$text=$_POST[pac_text];
$value=$_POST[pac_value];
$sms_credit=$_POST[sms_credit];
$insert_query = $database->database_query("insert into se_subscription set text='$text',value='$value', sms_credit='$sms_credit'");
$dmsg="Saved Successfully";	
}
if($_POST[submit]=="Update")
{
$text=$_POST[pac_text];
$value=$_POST[pac_value];
$sms_credit=$_POST[sms_credit];
$database->database_query("UPDATE se_subscription SET text='$text',value='$value', sms_credit='$sms_credit' WHERE sno='$_GET[id]'");
$dmsg="Updated Successfully";	
}




$sel_qry=$database->database_query("select * from  se_subscription");
$i=0;
while($views = $database->database_fetch_assoc($sel_qry))
{
	$view_arr[$i][id]=$views[sno];
	$view_arr[$i][text]=$views[text];
	$view_arr[$i][value]=$views[value];
	$view_arr[$i][sms_credit]=$views[sms_credit];
	$i++;
}

$smarty->assign("view_arr",$view_arr);
$smarty->assign("msg",$msg);
/*----------------------------------package Edit----------------------------------------*/
$sel_qry=$database->database_query("select * from  se_subscription  where sno='$_GET[id]'");
$views = $database->database_fetch_assoc($sel_qry);
$smarty->assign("views",$views);
$smarty->assign("dmsg",$dmsg);
/*----------------------------------package Edit----------------------------------------*/
$smarty->assign("act",$_GET[act]);
include "admin_footer.php";
?>