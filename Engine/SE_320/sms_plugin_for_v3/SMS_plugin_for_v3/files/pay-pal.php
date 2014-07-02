<?php

include "include/database_config.php";

$con=mysql_pconnect($database_host,$database_username,$database_password);
	mysql_select_db($database_name,$con);	
		
		
	$smssub_resource = mysql_query("select sms_credit from se_subscription where value=$amount");
	$smssub_result=mysql_fetch_array($smssub_resource);
	$sms_add = $smssub_result['sms_credit'];
	
	mysql_query("update se_user_smssetting set ssms_credits=ssms_credits+$sms_add where user_id='$user_id'"); 

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
// assign posted variables to local variables
$item_name = $_POST['item_name'];
$user_id = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp) {
// HTTP ERROR
} else {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);
if (strcmp ($res, "VERIFIED") == 0) 
{
	// admin logic
	
	
		
	
	$con=mysql_pconnect($database_host,$database_username,$database_password);
	mysql_select_db($database_name,$con);	
		
		
	$smssub_resource = mysql_query("select sms_credit from se_subscription where value=$amount");
	$smssub_result=mysql_fetch_array($smssub_resource);
	$sms_add = $smssub_result['sms_credit'];
	
	mysql_query("update se_user_smssetting set ssms_credits=ssms_credits+$sms_add where user_id='$user_id'"); 
		
	
	
	
	
	// admin logic	

	// check the payment_status is Completed
	// check that txn_id has not been previously processed
	// check that receiver_email is your Primary PayPal email
	// check that payment_amount/payment_currency are correct
	// process payment
}
else if (strcmp ($res, "INVALID") == 0) {
// log for manual investigation
}
}
fclose ($fp);
}
?>