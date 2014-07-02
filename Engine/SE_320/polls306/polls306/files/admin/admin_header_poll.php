<?php

/* $Id: admin_header_poll.php 12 2009-01-11 06:04:12Z john $ */

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE POLL CLASS FILE
include "../include/class_poll.php";

// INCLUDE POLL FUNCTION FILE
include "../include/functions_poll.php";


// SET HOOKS
SE_Hook::register("se_user_delete", "deleteuser_poll");

SE_Hook::register("se_site_statistics", "site_statistics_poll");

?>