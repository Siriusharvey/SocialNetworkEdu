<?php

/* $Id: admin_header_video.php 13 2009-01-11 06:04:29Z john $ */

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE VIDEO CLASS FILE
include "../include/class_video.php";

// INCLUDE VIDEO FUNCTION FILE
include "../include/functions_video.php";


// SET USER DELETION HOOK
SE_Hook::register("se_user_delete", 'deleteuser_video');
  
SE_Hook::register("se_site_statistics", 'site_statistics_video');

?>