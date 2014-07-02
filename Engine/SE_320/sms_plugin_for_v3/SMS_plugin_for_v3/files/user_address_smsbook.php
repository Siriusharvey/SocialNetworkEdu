<?php

/* $Id: invite.php 42 2009-01-29 04:55:14Z john $ */

$page = "user_address_smsbook";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting[setting_permission_invite] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
 
}
if(isset($_POST['Submit'])){
$nickname = $_POST[nickname];
$mobile = $_POST[mobile];
$fax = $_POST[fax];
$home = $_POST[home];
$grup = $_POST[group];
$first = $_POST[first];
$last = $_POST[last];
$email = $_POST[email];
$address = $_POST[address];
$city = $_POST[city];
$state = $_POST[state];
$zip = $_POST[zip];
$country = $_POST[country];
$details = $_POST[details];
$owner = $user->user_info['user_username'];
$sql="INSERT INTO se_addressbook (nickname, phone, grup, first, last, email, address, city, state, zip, home, country, fax, details, owner) VALUES('".$nickname."','".$mobile."','".$grup."','".$first."','".$last."','".$email."','".$address."','".$city."','".$state."','".$zip."','".$home."','".$country."','".$fax."','".$details."','".$owner."')";
$res=mysql_query($sql);
$smarty->assign("msg","Added Successfully");
}
if($_REQUEST['id'] != ""){
$sql_select="select * from se_addressbook where id='".$_REQUEST['id']."'";
$res_select=mysql_query($sql_select);
$row_select=mysql_fetch_array($res_select);
$id=$row_select['id'];
$nickname=$row_select['nickname'];
$phone=$row_select['phone'];
$grup=$row_select['grup'];
$first=$row_select['first'];
$last=$row_select['last'];
$email=$row_select['email'];
$address=$row_select['address'];
$city=$row_select['city'];
$state=$row_select['state'];
$zip=$row_select['zip'];
$home=$row_select['home'];
$country=$row_select['country'];
$fax=$row_select['fax'];
$details=$row_select['details'];
$update="1";
}
if(isset($_POST['Update'])){
$nickname = $_POST[nickname];
$mobile = $_POST[mobile];
$fax = $_POST[fax];
$home = $_POST[home];
$grup = $_POST[group];
$first = $_POST[first];
$last = $_POST[last];
$email = $_POST[email];
$address = $_POST[address];
$city = $_POST[city];
$state = $_POST[state];
$zip = $_POST[zip];
$country = $_POST[country];
$details = $_POST[details];
$id = $_POST[id];
$owner = $user->user_info['user_username'];
$sql="update se_addressbook set nickname='".$nickname."',phone='".$mobile."',grup='".$grup."',first='".$first."',last='".$last."',email='".$email."',address='".$address."',city='".$city."',state='".$state."',zip='".$zip."',home='".$home."',country='".$country."',fax='".$fax."',details='".$details."' WHERE owner = '".$owner."' AND id='".$id."'";
$res=mysql_query($sql);
}
if(isset($_POST['add'])){
$add_group= $_POST[add_group];
$owner = $user->user_info['user_username'];
$sql_group="INSERT INTO se_groups(grup, owner) VALUES('".$add_group."','".$owner."')";
$res_group=mysql_query($sql_group);
}
// SET EMPTY VARS
$is_error = 0;
$result = 0;
$step=0;

$global_page_title[0] = 1074;
$global_page_description[0] = 1075;
 $pms = $sms->group_list(0, $pms_per_page, 1);
// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign_by_ref('pms', $pms);
$smarty->assign('is_error', $is_error);
$smarty->assign('id', $id);
$smarty->assign('nickname', $nickname);
$smarty->assign('phone', $phone);
$smarty->assign('grup', $grup);
$smarty->assign('first', $first);
$smarty->assign('last', $last);
$smarty->assign('email', $email);
$smarty->assign('address', $address);
$smarty->assign('city', $city);
$smarty->assign('country', $country);
$smarty->assign('state', $state);
$smarty->assign('zip', $zip);
$smarty->assign('home', $home);
$smarty->assign('fax', $fax);
$smarty->assign('details', $details);
$smarty->assign('update', $update);
include "footer.php";
?>