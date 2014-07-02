<?php


$page = "admin_badge";
include "admin_header.php";

$task = rc_toolkit::get_request('task','main');

$rc_validator = new rc_validator();


$badge = new se_badge();

$result = 0;

// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_badge_license] = $_POST['setting_badge_license'];
  $setting[setting_permission_badge] = $_POST['setting_permission_badge'];
  $setting[setting_badge_width] = $_POST['setting_badge_width'];
  $setting[setting_badge_height] = $_POST['setting_badge_height'];

  $setting[setting_badge_exts] = $_POST['setting_badge_exts'];

  $setting_badge_levels = is_array($_POST['setting_badge_levels']) ? $_POST['setting_badge_levels'] : array();
  foreach ($setting_badge_levels as $k=>$v) { if (!$v) unset($setting_badge_levels[$k]); }
  $setting[setting_badge_levels] = serialize($setting_badge_levels);
  $setting_badge_subnets = is_array($_POST['setting_badge_subnets']) ? $_POST['setting_badge_subnets'] : array();
  foreach ($setting_badge_subnets as $k=>$v) { if (!$v) unset($setting_badge_subnets[$k]); }
  $setting[setting_badge_subnets] = serialize($setting_badge_subnets);
  $setting_badge_profilecats = is_array($_POST['setting_badge_profilecats']) ? $_POST['setting_badge_profilecats'] : array();
  foreach ($setting_badge_profilecats as $k=>$v) { if (!$v) unset($setting_badge_profilecats[$k]); }
  $setting[setting_badge_profilecats] = serialize($setting_badge_profilecats);
  
  // ENSURE THAT WIDTHS/HEIGHTS ARE EVEN
  if($setting[setting_badge_width]%2 != 0) { $setting[setting_badge_width] = $setting[setting_badge_width]+1; }
  if($setting[setting_badge_height]%2 != 0) { $setting[setting_badge_height] = $setting[setting_badge_height]+1; }

  $setting[setting_badge_profile_show] = $_POST['setting_badge_profile_show'];
  $setting[setting_badge_menu_badge_ids] = str_replace(' ','',trim($_POST['setting_badge_menu_badge_ids']));
  
  $rc_validator->license($setting[setting_badge_license],'badge','license');
  
  if (!$rc_validator->has_errors()) {
    
      $sql = "UPDATE se_settings SET 
          setting_badge_license='$setting[setting_badge_license]',
          setting_permission_badge='$setting[setting_permission_badge]',
          setting_badge_width='$setting[setting_badge_width]',
          setting_badge_height='$setting[setting_badge_height]',
          setting_badge_levels='$setting[setting_badge_levels]',
          setting_badge_subnets='$setting[setting_badge_subnets]',
          setting_badge_profilecats='$setting[setting_badge_profilecats]',
          setting_badge_profile_show='$setting[setting_badge_profile_show]',
          setting_badge_menu_badge_ids='$setting[setting_badge_menu_badge_ids]',
          setting_badge_exts='$setting[setting_badge_exts]'
          ";
          //rc_toolkit::debug($sql);
      $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
          
      $result = 1;
  }

}


$res = $database->database_query("SELECT * FROM se_levels");
while ($row = $database->database_fetch_assoc($res)) {
  $levels[] = $row;
}

$res = $database->database_query("SELECT * FROM se_subnets");
while ($row = $database->database_fetch_assoc($res)) {   
  SE_Language::_preload($row['subnet_name']);
  $subnets[] = $row;
}

$res = $database->database_query("SELECT * FROM se_profilecats WHERE profilecat_dependency = 0 ORDER BY profilecat_order ASC");
while ($row = $database->database_fetch_assoc($res)) {   
  SE_Language::_preload($row['profilecat_title']);
  $profilecats[] = $row;
}

$where = null;
$total_badges = $badge->badge_total($where);
$badges = $badge->badge_list(0, $total_badges, "badge_title ASC", $where);

$setting_badge_levels = unserialize($setting['setting_badge_levels']);
if (!is_array($setting_badge_levels)) $setting_badge_levels = array();

$setting_badge_subnets = unserialize($setting['setting_badge_subnets']);
if (!is_array($setting_badge_subnets)) $setting_badge_subnets = array();

$setting_badge_profilecats = unserialize($setting['setting_badge_profilecats']);
if (!is_array($setting_badge_profilecats)) $setting_badge_profilecats = array();


// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $rc_validator->has_errors());
$smarty->assign('error_message', join(" ",$rc_validator->get_errors()));

$smarty->assign('levels', $levels);
$smarty->assign('subnets', $subnets);
$smarty->assign('profilecats', $profilecats);
$smarty->assign_by_ref('cats', $badge->badge_categories());

$smarty->assign_by_ref('badges', $badges);

$smarty->assign('setting_badge_levels', $setting_badge_levels);
$smarty->assign('setting_badge_subnets', $setting_badge_subnets);
$smarty->assign('setting_badge_profilecats', $setting_badge_profilecats);  

include "admin_footer.php";
