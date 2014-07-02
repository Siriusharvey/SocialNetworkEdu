<?php

$smsset_resource = $database->database_query("select * from se_global_sms  where id='1'");
$smsset_result=$database->database_fetch_assoc($smsset_resource);

$user = $smsset_result[sms_userid];
$password = $smsset_result[password];
$api_id = $smsset_result[apiid];

/*$user = "smattech";
$password = "ramesh123456";
$api_id = "3164150";*/
$baseurl ="http://api.clickatell.com";
$text = urlencode($txt);
$to = $msi;
// auth call
$url = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";
// do auth call
$ret = file($url);
// split our response. return string is on first line of the data returned
$sess = split(":",$ret[0]);
if ($sess[0] == "OK") {
$sess_id = trim($sess[1]); // remove any whitespace
$url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";
// do sendmsg call
$ret = file($url);
$send = split(":",$ret[0]);
if ($send[0] == "ID")
$api_mess= "sent";
else
$api_mess= "fail";
} else {
$api_mess= "Authentication failure: ". $ret[0];

}


?>
