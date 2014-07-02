<?php

/* $Id: admin_header_music.php 11 2009-01-11 06:03:58Z john $ */

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE MUSIC CLASS FILE
include "../include/class_music.php";
include "../include/functions_music.php";


// SET HOOKS
SE_Hook::register("se_user_delete", "deleteuser_music");

SE_Hook::register("se_site_statistics", "site_statistics_music");

?>