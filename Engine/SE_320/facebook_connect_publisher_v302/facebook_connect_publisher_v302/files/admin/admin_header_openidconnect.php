<?php

defined('SE_PAGE') or exit();

if( defined('SEMODS_HEADER_OPENIDCONNECT') ) return;
define('SEMODS_HEADER_OPENIDCONNECT', TRUE);

// INCLUDE CLASS FILE
include_once "../include/class_semods.php";
include_once "../include/class_semods_utils.php";
include_once "../include/class_openidconnect.php";

// INCLUDE FUNCTION FILE
include_once "../include/functions_openidconnect.php";

// for all services
if(file_exists('admin_header_openidconnect_facebook.php')) {
  include_once 'admin_header_openidconnect_facebook.php';
}


?>