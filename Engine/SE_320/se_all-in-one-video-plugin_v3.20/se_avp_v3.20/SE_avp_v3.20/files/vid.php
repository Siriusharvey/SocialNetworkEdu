<?php
$page = "vid";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_GET['video_id'])) { $video_id = $_GET['video_id']; } else { $video_id = ''; }

if($admin->admin_exists) {
  $vidq = $database->database_query("SELECT vid_user_id FROM se_vids WHERE vid_id='".$video_id."'");
  if($database->database_num_rows($vidq) == 1) {
      $vid_user_id = $database->database_fetch_assoc($vidq);
      $username = $database->database_fetch_assoc($database->database_query("SELECT user_username FROM se_users WHERE user_id='".$vid_user_id[vid_user_id]."'"));
      $owner = new SEUser(Array('', $username[user_username]));
  }
}

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($vid_settings[permission] == 0 && $user->user_exists == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

if($owner->user_exists == 0 || $video_id == '') {
      $page = "error";
      $smarty->assign('error_header', 639);
      $smarty->assign('error_message', 13500015);
      $smarty->assign('error_submit', 641);
      include "footer.php";
}

$vids_array = $video->info($video_id);

if(strstr($vid_settings[disable], $vids_array[type])) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 13500163);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

if ($video->video_error == 1) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 13500015);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

if ($vids_array['is_converted'] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 13500023);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if(!($vids_array[vid_privacy] & $privacy_max)) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 13500205);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

$query = $database->database_query("UPDATE se_vids SET vid_views = vid_views+1 WHERE vid_user_id='".$owner->user_info[user_id]."' AND vid_id='".$video_id."'");

$if_exists = $database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id = '".$user->user_info['user_id']."' AND vidfav_ids LIKE '%,".$vids_array[id]."%' LIMIT 1");
if ($database->database_num_rows($if_exists) == 1) {
          $smarty->assign('is_favorite', TRUE);
}

// GET COMMENTS
$comment = new se_comment('vid', 'vid_id', $vids_array['id']);
$total_comments = $comment->comment_total();

$search_title = preg_replace('#\s{2,}#', ' ', $vids_array['title']);
$search_tags = implode(" ", $vids_array['tags']);
$search_query = mysql_real_escape_string($search_title).' '.mysql_real_escape_string($search_tags);

$results_s = $database->database_query("SELECT *, MATCH(vid_title,vid_tags) AGAINST ('$search_query' IN BOOLEAN MODE) AS score FROM se_vids WHERE MATCH(vid_title,vid_tags) AGAINST ('$search_query' IN BOOLEAN MODE) AND vid_id<>'".$vids_array['id']."' AND vid_is_converted=1 ORDER BY score DESC LIMIT 0,10");

$max_rating = 5;
  
if($database->database_affected_rows($results_s) != 0) {

     $jprelvideos_array = Array();

     while ($rel_videos = $database->database_fetch_assoc($results_s)) {

            $category = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_vidcats WHERE vidcat_id='".$rel_videos['vid_cat']."'"));
               
            $rating_full = floor($rel_videos[vid_rating_value]);
               
            if($rating_full != $rel_videos[vid_rating_value]) { $rating_partial = 1; } else { $rating_partial = 0; }
            $rating_empty = $max_rating-($rating_full+$rating_partial);
               
            $video_dir = $video->video_dir($rel_videos['vid_user_id']);

            $vid_locations = explode(',', $rel_videos['vid_location']);
               
            if (count($vid_locations) == 2) {
              $vid_location = $vid_locations[0];
              $vid_img_location = $video_dir.$rel_videos[vid_id];
              $vid_type = 'youtube';
            } else {
              $vid_location = $video_dir.$vid_locations[0].'flv';
              $vid_img_location = $video_dir.$vid_locations[0];
              $vid_type = 'self';
            }
            
            $username = $database->database_fetch_assoc($database->database_query("SELECT user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_id='".$rel_videos['vid_user_id']."'"));

            $vid_author = new se_user();
            $vid_author->user_exists = 1;
            $vid_author->user_info['user_id'] = $rel_videos['vid_user_id'];
            $vid_author->user_info['user_username'] = $username['user_username'];
            $vid_author->user_info['user_fname'] = $username['user_fname'];
            $vid_author->user_info['user_lname'] = $username['user_lname'];
            $vid_author->user_info['user_photo'] = $username['user_photo'];
            $vid_author->user_displayname();
                 
            $vid_privacy_max = $vid_author->user_privacy_max($user);
                 	          	   
            if(!($rel_videos['vid_privacy'] & $vid_privacy_max)) {
                 continue;
            } else {
                 $jprelvideos_array[] = array('title' => $rel_videos['vid_title'], 'location' => $vid_location, 'cat_id' => $category['vidcat_id'], 'cat_lang' => (int)$category['vidcat_languagevar_id'], 'img' => $vid_img_location, 'id' => $rel_videos['vid_id'], 'views' => $rel_videos['vid_views'], 'full' => $rating_full, 'empty' => $rating_empty, 'partial' => $rating_partial, 'type' => $vid_type, 'username' => $username['user_username']);
            }
     }
}

// GET VIDEO COMMENT PRIVACY
$allowed_to_comment = 1;
if(!($privacy_max & $vids_array[vid_comments])) { $allowed_to_comment = 0; }

$jpallvideos_array = $video->vid_list($owner->user_info[user_id], FALSE, $vids_array['id'], 0, 10);
$total_videos = $video->vid_total($owner->user_info[user_id]);

$share_url = $url->url_create('vid_file', $owner->user_info[user_username], $vids_array[id]);
$share_title = $vids_array[title];

if ($vids_array[directly] == 0 AND $vids_array[type] != "self") {
                   $share_embed = preg_replace("/\n|\r\n|\r$/", "", $vids_array[location]);
                   $share_embed = preg_replace("/>\s{2,}</", "><", $share_embed);
} elseif ($vids_array[type] == "self") {
                   $share_embed = '<object width="640" height="360"><param name="wmode" value="transparent"></param><param name="movie" value="'.$url->url_base.'player.swf?file='.$vids_array[location].'.flv&streamer='.$url->url_base.'vid_uri.php&type=http"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed id="VideoPlayback" src="'.$url->url_base.'player.swf?file='.$vids_array[location].'.flv&streamer='.$url->url_base.'vid_uri.php&type=http" style="width:640px;height:360px" allowFullScreen="true" allowScriptAccess="always" type="application/x-shockwave-flash" wmode="transparent"></embed></object>';
} elseif ($vids_array[directly] == 1 AND $vids_array[type] == 'youtube_api') {
                   $share_embed = '<object width="640" height="360"><param name="wmode" value="transparent"></param><param name="movie" value="'.$url->url_base.'player.swf?file='.$vids_array[location].'&type=youtube"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed id="VideoPlayback" src="'.$url->url_base.'player.swf?file='.$vids_array[location].'&type=youtube" style="width:640px;height:360px" allowFullScreen="true" allowScriptAccess="always" type="application/x-shockwave-flash" wmode="transparent"></embed></object>';
} else {
                   $share_embed = preg_replace("/\n|\r\n|\r$/", "", $video_api->catchEmbed());
                   $share_embed = preg_replace("/>\s{2,}</", "><", $share_embed);
}

$smarty->assign('total_videos', $total_videos);
$smarty->assign('all_rel_videos', $jprelvideos_array);
$smarty->assign('all_videos', $jpallvideos_array['videos']);
$smarty->assign('vid_settings', $vid_settings);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('vids_array', $vids_array);
$smarty->assign('share_url', urlencode($share_url));
$smarty->assign('share_url_raw', $share_url);
$smarty->assign('share_url_raw_no', str_replace("http://", "", str_replace("https://", "", $share_url)));
$smarty->assign('share_title', urlencode($share_title));
$smarty->assign('share_title_space', urlencode(str_replace("+", "%20", $share_title)));
$smarty->assign('share_title_raw', $share_title);
$smarty->assign('share_desc', urlencode($vids_array[desc_short]));
$smarty->assign('share_embed', $share_embed);
$smarty->assign('not_report', SELanguage::_get(13500186));
$smarty->assign('not_favorite', SELanguage::_get(13500187));
include "footer.php";
?>