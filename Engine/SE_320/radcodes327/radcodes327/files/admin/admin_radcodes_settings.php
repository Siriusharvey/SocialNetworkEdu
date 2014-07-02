<?php

$page = "admin_radcodes_settings";
include "admin_header.php";

$task = rc_toolkit::get_request('task','main');

$result = 0;

if ($task == 'dosave') {
  
  $setting['setting_radcodes_remote_type'] = rc_toolkit::get_post('setting_radcodes_remote_type','file_get_contents');
  $setting['setting_radcodes_google_map_api'] = rc_toolkit::get_post('setting_radcodes_google_map_api');
  
  $sql = "UPDATE se_settings SET 
          setting_radcodes_remote_type='$setting[setting_radcodes_remote_type]',
          setting_radcodes_google_map_api='$setting[setting_radcodes_google_map_api]'
          ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
          
          
      $result = 1;
}

$smarty->assign('result', $result);

include "admin_footer.php";
