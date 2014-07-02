<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

// INCLUDE SMS CLASS FILE
include "./include/class_sms.php";


// SET MAIN MENU VARS
$plugin_vars[menu_main] = "";

// SET USER MENU VARS
if($user->level_info[level_sms_allow] == 1) {
  $plugin_vars[menu_user] = Array('file' => 'user_sms_settings.php', 'icon' => 'mobile_mobile16.png', 'title' => 5000001);
}

$sms = new se_sms($owner->user_info[user_id]);  
  


  


?>