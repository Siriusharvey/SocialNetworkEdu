<?php
// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

// INCLUDE VIDEO API CLASS FILE
include "./include/class_vid_api.php";
$video_api = new se_vid_api();

// INCLUDE VIDEO CLASS FILE
include "./include/class_vid.php";
$video = new se_vid();

// INCLUDE VIDEO FUNCTION FILE
include "./include/functions_vid.php";

$vid_settings = $video->vid_settings();

// PRELOAD LANGUAGE
SE_Language::_preload_multi(13500016, 13500007);

// SET MAIN MENU VARS
if($user->user_exists || (!$user->user_exists && $vid_settings[permission] == 1))
{
$plugin_vars[menu_main] = Array('file' => 'browse_vids.php', 'title' => 13500016);
}

if($user->user_exists && ($user->level_info['level_vid_allow'] == 1 OR $user->level_info['level_vid_allow'] == 2 OR $user->level_info['level_vid_allow'] == 3)) {
      $plugin_vars[menu_user] = Array('file' => 'user_vid.php', 'icon' => 'vid_myvid16.gif', 'title' => 13500007);
      $num_favs = $database->database_fetch_assoc($database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id='".$user->user_info[user_id]."' LIMIT 1"));
      $num_favs = (int)count(explode(",", $num_favs[vidfav_ids]))-1;
      $num_vids = $database->database_num_rows($database->database_query("SELECT * FROM se_vids WHERE vid_user_id='".$user->user_info[user_id]."' AND vid_is_converted='1'"));
      $smarty->assign('num_favs', $num_favs);
      $smarty->assign('num_vids', $num_vids);
}

// SET PROFILE MENU VARS
if($page == "profile") {
      $jpvideos_whole_array = $video->vid_list($owner->user_info[user_id], FALSE, 0, 0, 6, 0, "", TRUE, TRUE);

      $total_vids = count($jpvideos_whole_array['videos']);

      if ($total_vids > 0) {
          $plugin_vars[menu_profile_tab] = Array('file'=> 'profile_vid_tab.tpl', 'title' => 13500007);
      }

      $smarty->assign('all_videos', $jpvideos_whole_array['videos']);
      $smarty->assign('count_videos', $total_vids);
}

if($page == "admin_viewvids" OR $page == "admin_vids" OR $page == "user_vid_add" OR $page == "user_vid" OR $page == "browse_vids" OR $page == "vid" OR $page == "vids") {
      $vid_curl_cmd = "SELECT vid_id FROM se_vids WHERE vid_is_converted='3' ORDER BY vid_datecreated DESC LIMIT 1";
      $vid_curl_query = $database->database_query($vid_curl_cmd);

      $vid_curlb_cmd = "SELECT vid_id FROM se_vids WHERE vid_is_converted='2'";
      $vid_curlb_query = $database->database_query($vid_curl_cmd);

      if ($database->database_num_rows($vid_curl_query) > 0 && $database->database_num_rows($vid_curlb_query) < 4) {
          $curl = $url->url_base."vid_encode.php";
          @exec("curl -m 20 -G $curl > ./uploads_vid/log/curl.log &");
      }
}

$tmp_dir = './uploads_vid/tmp/';

SE_Hook::register("se_search_do", 'search_vid');
SE_Hook::register("se_user_delete", 'deleteuser_vid');
SE_Hook::register("se_site_statistics", 'site_statistics_vid');
?>