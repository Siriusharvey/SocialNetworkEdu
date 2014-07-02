<?php


// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

include_once "./include/class_radcodes.php";
include_once "./include/class_badge.php";
include_once "./include/functions_badge.php";



// PRELOAD LANGUAGE
SE_Language::_preload(11270098,11270099);

// SET MAIN MENU VARS
if( ($user->user_exists && (int)$user->level_info['level_badge_allow'] & 1) || (!$user->user_exists && $setting['setting_permission_badge']) )
{
  $plugin_vars['menu_main'] = Array('file' => 'browse_badges.php', 'title' => 11270098);
}

// SET USER MENU VARS
if( $user->user_exists && (int)$user->level_info['level_badge_allow'] & 2 )
{
  $plugin_vars['menu_user'] = Array('file' => 'user_badge.php', 'icon' => 'badge_badge16.gif', 'title' => 11270099);
}


$badge_topmenu_items = badge_topmenu_items();

//rc_toolkit::debug($badge_topmenu_items,'badge_topmenu_items');
$smarty->assign('badge_topmenu_items', $badge_topmenu_items);

if($page == "profile") {

  $se_badge = new se_badge();

  $badges = array();
  // GET LEVEL BADGE
  if ($badge = $se_badge->get_level_badge($owner->user_info['user_level_id'])) {
    $badges['level'] = $badge;
  }
  if ($badge = $se_badge->get_subnet_badge($owner->user_info['user_subnet_id'])) {
    $badges['subnet'] = $badge;
  }
  if ($badge = $se_badge->get_profilecat_badge($owner->user_info['user_profilecat_id'])) {
    $badges['profilecat'] = $badge;
  }
  
  $se_badgeassignment = new se_badgeassignment(null, $owner->user_info['user_id']);
  $badgeassignment_where = "badgeassignment_approved = '1' AND badgeassignment_profile = '1'";
  $total_badgeassignments = $se_badgeassignment->badgeassignment_total($badgeassignment_where);
  if ($total_badgeassignments) {
    $badges['assignments'] = $se_badgeassignment->badgeassignment_list(0, $total_badgeassignments, "badgeassignment_dateapproved DESC", $badgeassignment_where);
  }
  
  //rc_toolkit::debug($badges,'badges');
  $smarty->assign('badges', $badges);

  if (count($badges)) {
    
    if ($setting['setting_badge_profile_show'] == 'tab') {
      $plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_badge_tab.tpl', 'title' => 11270099);
    }
    else if ($setting['setting_badge_profile_show'] == 'side') {
      $plugin_vars['menu_profile_side'] = Array('file'=> 'profile_badge_side.tpl', 'title' => 11270099);
    }
    
    
  }

}


// SET HOOKS
SE_Hook::register("se_search_do", 'search_badge');
  
SE_Hook::register("se_user_delete", 'deleteuser_badge');
  
SE_Hook::register("se_site_statistics", 'site_statistics_badge');
