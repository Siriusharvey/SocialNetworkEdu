<?php

$page = "admin_badge_edit";
include "admin_header.php";

$task = rc_toolkit::get_request('task');
$badge_id = rc_toolkit::get_request('badge_id');

$badge = new se_badge(NULL, $badge_id);

if (!$badge->badge_exists) {
  rc_toolkit::redirect("admin_badges.php");
}

$result = false;
$is_error = false;

if ($task == "dosave") {
  
  $keys = array(
      'badge_title',
      'badge_desc',
      'badge_badgecat_id',
      'badge_cost',
      'badge_epayment',
      'badge_approved',
      'badge_search',
      'badge_enabled',
      'badge_levels',
      'badge_subnets',
      'badge_profilecats',
      'badge_link_details',
  );
  foreach ($keys as $key) {
    $badge->badge_info[$key] = rc_toolkit::get_post($key);
  }  
  if ($_POST['badge_level_all']) {
    $badge->badge_info['badge_levels'] = array();
  }
  if ($_POST['badge_subnet_all']) {
    $badge->badge_info['badge_subnets'] = array();
  }
  if ($_POST['badge_profilecat_all']) {
    $badge->badge_info['badge_profilecats'] = array();
  }
    
  $badge->badge_edit($badge->badge_info);
  
  if (!empty($_FILES['photo']['name'])) {
    //print_r($_FILES['photo']);
    $badge->badge_photo_upload('photo');
  }
  
  if( !$badge->is_error )
  {    
    $badge->remove_cached_badge($badge_id);
    $result = TRUE;
  }
  else
  {
    SE_Language::_preload($is_error = $badge->is_error);
  }
  
}

$badge->badge_info['badge_desc'] = str_replace("\r\n", "", html_entity_decode($badge->badge_info['badge_desc']));


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




//rc_toolkit::debug($subnets);
$smarty->assign('result',     $result);
$smarty->assign('is_error',   $is_error);
$smarty->assign('levels', $levels);
$smarty->assign('subnets', $subnets);
$smarty->assign('profilecats', $profilecats);
$smarty->assign_by_ref('cats', $badge->badge_categories());
$smarty->assign('badge', $badge);

include "admin_footer.php";
