<?php



// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE ggstoreS CLASS FILE
include "../include/class_gstore.php";

// INCLUDE ggstoreS FUNCTION FILE
include "../include/functions_gstore.php";


// SET HOOKS
SE_Hook::register("se_user_delete", 'deleteuser_gstore');

SE_Hook::register("se_site_statistics", 'site_statistics_gstore');

?>