<?php
$page = "user_vid_add";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "upload"; }

$count_yt = $database->database_num_rows($database->database_query("SELECT vid_id FROM se_vids WHERE (vid_user_id='".$user->user_info[user_id]."') AND (vid_location like \"%,%\")"));
$count_vids = $database->database_num_rows($database->database_query("SELECT vid_id FROM se_vids WHERE (vid_user_id='".$user->user_info[user_id]."') AND (vid_location not like \"%,%\")"));

if ($user->level_info[level_vid_allow] == 3) {
  if ($count_vids >= $user->level_info[level_vid_maxnum] AND $count_yt >= $user->level_info[level_vid_prov_maxnum]) {
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 13500059);
    $smarty->assign('error_submit', 641);
    include "footer.php";
  } elseif (($count_vids >= $user->level_info[level_vid_maxnum] AND $task == "upload") OR ($count_yt >= $user->level_info[level_vid_prov_maxnum] AND $task == "youtube")) {
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 13500059);
    $smarty->assign('error_submit', 641);
    include "footer.php";
  }
} elseif ($user->level_info[level_vid_allow] == 1 AND $count_vids >= $user->level_info[level_vid_maxnum]) {
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 13500059);
    $smarty->assign('error_submit', 641);
    include "footer.php";
} elseif ($user->level_info[level_vid_allow] == 2 AND $count_yt >= $user->level_info[level_vid_prov_maxnum]) {
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 13500059);
    $smarty->assign('error_submit', 641);
    include "footer.php";
} elseif ($user->level_info[level_vid_allow] == 4) {
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 13500059);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}

// GET PRIVACY SETTINGS
$level_vid_privacy = unserialize($user->level_info['level_vid_privacy']);
rsort($level_vid_privacy);
$level_vid_comments = unserialize($user->level_info['level_vid_comments']);
rsort($level_vid_comments);

// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_vid_privacy);$c++) {
    if(user_privacy_levels($level_vid_privacy[$c]) != "") {
      SE_Language::_preload(user_privacy_levels($level_vid_privacy[$c]));
      $privacy_options[$level_vid_privacy[$c]] = user_privacy_levels($level_vid_privacy[$c]);
    }
}

for($c=0;$c<count($level_vid_comments);$c++) {
    if(user_privacy_levels($level_vid_comments[$c]) != "") {
      SE_Language::_preload(user_privacy_levels($level_vid_comments[$c]));
      $comment_options[$level_vid_comments[$c]] = user_privacy_levels($level_vid_comments[$c]);
    }
} 

$cats_query = $database->database_query("SELECT * FROM se_vidcats WHERE vidcat_id>1");
$cats_array = Array();

if ($database->database_num_rows($cats_query) != 0) {
     while ($cats = $database->database_fetch_assoc($cats_query)) {
           $cats_array[] = array('id' => $cats['vidcat_id'], 'title' => $cats['vidcat_title']);
     }
}

$number_of_cats = $database->database_num_rows($cats_query);

$user->level_info[level_vid_maxsize] = $user->level_info[level_vid_maxsize]/1024;

$exts = str_replace(",", ", ", $vid_settings[exts]);

$q = "youtube";
$count_vids = $database->database_num_rows($database->database_query("SELECT * FROM se_vids WHERE (vid_user_id='".$user->user_info[user_id]."') AND (vid_location not like \"%$q%\")"));

if (isset($_POST['url'])) {

     $video_api->getVideoType($_POST['url']);
     $data = $video_api->catchData();

}

if ($task == 'youtube') {

     // GETS ALL ALLOWED VIDEO PROVIDERS' NAMES
     $providers = $video_api->getAllProviders();

     $provider = explode(",", $user->level_info[level_vid_prov]);
     array_shift($provider);

     $provider_disabled = explode(",", $vid_settings[disable]);
     array_shift($provider_disabled);

     $prov_number = count($provider);

     for ($i=0; $i<count($provider_disabled); $i++) {
         for ($j=0; $j<$prov_number; $j++) {
             if ($provider[$j] == $provider_disabled[$i]){
                 unset($provider[$j]);
             }
         }
     }

     $provider_keys = array_keys($provider);

     for ($i=0; $i<count($provider); $i++) {
         $real_provider[$i] = $provider[$provider_keys[$i]];
     }

     $provider = $real_provider;

     if (empty($provider)) {
         $page = "error";
         $smarty->assign('error_header', 639);
         $smarty->assign('error_message', 13500172);
         $smarty->assign('error_submit', 641);
         include "footer.php";
     }

     $prov_type = $providers[1];
     $prov_name = $providers[0];
     $prov_ex = $providers[3];
     $prov_img = $providers[4];
     $prov_url = $providers[5];
     $provs = array_combine($prov_type, $prov_name);
     $provs2 = array_combine($prov_type, $prov_ex);
     $provs3 = array_combine($prov_type, $prov_img);
     $provs4 = array_combine($prov_type, $prov_url);
     $providers_array = array();
     $providers_img_array = array();
     $providers_url_array = array();

     for ($i=0; $i<count($provider); $i++) {
          if (count($provider) >= 2) {
               $last = count($provider)-1;
               $sec_last = count($provider)-2;
               if ($i == $sec_last) {
                    $allowed_providers .= $provs[$provider[$i]].' & ';
               } elseif ($i == $last) {
                    $allowed_providers .= $provs[$provider[$i]];
               } else {
                    $allowed_providers .= $provs[$provider[$i]].', ';
               }
               $providers_array[] = $provs2[$provider[$i]];
               $providers_img_array[] = $provs3[$provider[$i]];
               $providers_url_array[] = $provs4[$provider[$i]];
          } else {
               $allowed_providers = $provs[$provider[$i]];
               $providers_array[] = $provs2[$provider[$i]];
               $providers_img_array[] = $provs3[$provider[$i]];
               $providers_url_array[] = $provs4[$provider[$i]];
          }
     }

     if ($video->is_int(count($providers_img_array)/2) === false) {
          $smarty->assign('int', 'ok');
     }

     $smarty->assign('allowed_providers', $allowed_providers);
     $smarty->assign('providers_array', $providers_array);
     $smarty->assign('providers_img_array', $providers_img_array);
     $smarty->assign('providers_url_array', $providers_url_array);

}

$exts_array = explode(',', $vid_settings[exts]);

$smarty->assign('count_yt', $count_yt);
$smarty->assign('count_vids', $count_vids);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('session_id', session_id());
$smarty->assign('task', $task);
$smarty->assign('exts_array', $exts_array);
$smarty->assign('location', trim($_POST['url']));
$smarty->assign('title', $data[0]);
$smarty->assign('description', $data[1]);
$smarty->assign('tags', $data[2]);
$smarty->assign('count_vids', $count_vids);
$smarty->assign('exts', $exts);
$smarty->assign('count_cats', $number_of_cats);
$smarty->assign('all_cats', $cats_array);
include "footer.php";
?>