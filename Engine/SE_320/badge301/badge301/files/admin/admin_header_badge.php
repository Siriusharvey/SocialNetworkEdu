<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

include_once "../include/class_radcodes.php";
include_once "../include/class_badge.php";
include_once "../include/functions_badge.php";

// SET USER DELETION HOOK
SE_Hook::register("se_user_delete", 'deleteuser_badge');
  
SE_Hook::register("se_site_statistics", 'site_statistics_badge');
