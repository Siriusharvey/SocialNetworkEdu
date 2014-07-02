<?php
$page = "user_vid";

// RETRIEVE SESSION ID/UPLOAD TOKEN
if(isset($_POST['vid_sessid'])) { define('SE_SESSION_RESUME', TRUE); $session_id = $_POST['vid_sessid']; }

include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// DISPLAY ERROR PAGE IF USER IS NOT ALLOWED TO UPLOAD/ADD VIDEOS
if($user->level_info[level_vid_allow] == 0) {
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 13500059);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}

if (isset($_GET['message'])) {
     $smarty->assign('encode', $video->vid_msg($_GET['message']));
}    

// GET PRIVACY SETTINGS
$level_vid_privacy = unserialize($user->level_info['level_vid_privacy']);
rsort($level_vid_privacy);
$level_vid_comments = unserialize($user->level_info['level_vid_comments']);
rsort($level_vid_comments);

if ($task == "delete_vid") {

     $vid_id = $_GET['id'];
     $video->delete_video($vid_id);

}

if ($task == "update_vid") {

   $id = $_POST['vid_id'];
   $vid_title = censor($_POST['vid_title']);
   $vid_tags = censor($_POST['vid_tags']);
   $vid_desc = str_replace("&lt;br&gt;", " ", $_POST['vid_desc']);
   $vid_desc = censor(str_replace("\r\n", "<br>", $vid_desc));
   $vid_search = $_POST['vid_search'];
   $vid_privacy = $_POST['vid_privacy'];
   $vid_comments = $_POST['vid_comments'];

   if(!in_array($vid_privacy, $level_vid_privacy)) { $vid_privacy = $level_vid_privacy[0]; }
   if(!in_array($vid_comments, $level_vid_comments)) { $vid_comments = $level_vid_comments[0]; }

   // CHECK THAT SEARCH IS NOT BLANK
   if($user->level_info['level_vid_search'] == 0 OR !$user->level_info['level_vid_search'])
     $vid_search = 1;

   if(trim($vid_title) == '' OR trim($vid_tags) == '' OR trim($vid_desc) == '') {

     $smarty->assign('msg', $video->vid_msg(7));

   } else {

     $vid_data = $database->database_fetch_assoc($database->database_query("SELECT vid_location FROM se_vids WHERE vid_id='".$id."' LIMIT 1"));
     $locations = explode(",", $vid_data[vid_location]);

     $thumbnail_output_dir = $video->video_dir($user->user_info[user_id]);

     if ($locations[1]) {
          $path = $thumbnail_output_dir.$id.'.jpg';
     } else {
          $path = $thumbnail_output_dir.$locations[0].'_thumb_1.jpg';
     }

     $action_vid[] = Array('media_link' => $url->url_create('vid_file', $user->user_info[user_username], $id),
				                             'media_path' => $path,
				                             'media_width' => 130,
				                             'media_height' => 10);

     $actions->actions_add($user, "newvid", Array($user->user_info[user_username], $user->user_displayname, $id, $vid_title), $action_vid, 3600, TRUE, "user", $user->user_info[user_id], $vid_privacy);

     $sql = "SELECT vid_tags FROM se_vids WHERE vid_id=$id";
     $result = $database->database_query($sql);
     $tag_clean = mysql_result($result, 0);

     $tags = explode(" ", $tag_clean);

     for($i = 0; $i < count($tags); $i++) {
         $database->database_query("UPDATE se_vidtags SET value = value-1 WHERE tag = '".$tags[$i]."'");
     }

     $database->database_query("DELETE FROM se_vidtags WHERE value=0");

     $tag_clean = preg_replace('#\s{2,}#', ' ', $vid_tags);
     $tag_lower = strtolower($tag_clean);
     $tags = explode(" ", trim($tag_lower));
     $tags = array_unique($tags);

     $key_value = 0;
     $tags_final = array();

     foreach ($tags as $val) {
         $tags_final[$key_value] = $val;
         $key_value += 1;
     }

     $tags_clean = implode(" ", $tags_final);

     for($i = 0; $i < count($tags_final); $i++) {
         $sql = "UPDATE se_vidtags SET value = value+1 WHERE tag = '".$tags_final[$i]."'";
         $result = $database->database_query($sql);
         if ($database->database_affected_rows($result) == 0) {
           $sql = "INSERT INTO se_vidtags (tag, value) VALUES ('".$tags_final[$i]."', 1)";
           $result = $database->database_query($sql);
         }
     }

     $database->database_query("UPDATE se_vids SET vid_title='".trim($vid_title)."', vid_desc='".trim($vid_desc)."', vid_tags='".trim($tags_clean)."', vid_comments='".$vid_comments."', vid_privacy='".$vid_privacy."', vid_search='".$vid_search."' WHERE vid_id=$id AND vid_user_id='".$user->user_info[user_id]."'");

   }
}

if ($task == "add_vid_youtube") {

     // CHECK THAT UPLOAD DIRECTORY EXISTS, IF NOT THEN CREATE
     $video_directory = $video->video_dir($user->user_info[user_id]);
     $video_path_array = explode("/", $video_directory);
     array_pop($video_path_array);
     array_pop($video_path_array);
     $subdir = implode("/", $video_path_array)."/";

     if(!is_dir($subdir))
     { 
       mkdir($subdir, 0777); 
       chmod($subdir, 0777); 
       $handle = fopen($subdir."index.php", 'x+');
       fclose($handle);
     }

     if(!is_dir($video_directory))
     {
       mkdir($video_directory, 0777);
       chmod($video_directory, 0777);
       $handle = fopen($video_directory."/index.php", 'x+');
       fclose($handle);
     }
	
     $vid_title = censor($_POST['vid_title']);
     $vid_tags = censor($_POST['vid_tags']);
     $vid_desc = str_replace("&lt;br&gt;", "", $_POST['vid_desc']);
     $vid_desc = censor(str_replace("\r\n", "<br>", $vid_desc));
     $vid_cat = $_POST['vid_cat'];
     $vid_location = $_POST['file2'];
     $vid_datecreated = time();
     $vid_search = $_POST['vid_search'];
     $vid_privacy = $_POST['vid_privacy'];
     $vid_comments = $_POST['vid_comments'];

     if(!in_array($vid_privacy, $level_vid_privacy)) { $vid_privacy = $level_vid_privacy[0]; }
     if(!in_array($vid_comments, $level_vid_comments)) { $vid_comments = $level_vid_comments[0]; }

     // CHECK THAT SEARCH IS NOT BLANK
     if($user->level_info['level_vid_search'] == 0 OR !$user->level_info['level_vid_search'])
      $vid_search = 1;

     $video->validation($vid_title, $vid_desc, $vid_tags, $vid_location, 1);
     
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

     if ($video->video_error == 0) {

          if (!in_array($video->type, $provider)) {
               $video->video_error = 9;
          }

          if ($video->video_error == 0) {
               $vid_location = $video->url.','.$video->type;
               $video->add_video($vid_datecreated, $vid_title, $vid_desc, $vid_cat, $vid_location, $vid_tags, $task, $vid_comments, $vid_privacy, $vid_search);
               $mysql_id_query = $database->database_fetch_assoc($database->database_query("SELECT vid_id FROM se_vids WHERE vid_location='".$vid_location."' LIMIT 1"));
               $mysql_id = $mysql_id_query[vid_id];

               $ch = curl_init();
               $timeout = 20;
               curl_setopt($ch,CURLOPT_URL,$video->img);
               curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
               curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
               $contents = curl_exec($ch);
               curl_close($ch);

               $thumbnail_output_dir = $video->video_dir($user->user_info[user_id]);
               $path = $thumbnail_output_dir.$mysql_id.'.jpg';
               $handle = fopen($path, 'w');
               fwrite($handle, $contents);
               fclose($handle);

               if (!file_exists($thumbnail_output_dir.$mysql_id.'.jpg') OR filesize($thumbnail_output_dir.$mysql_id.'.jpg') == 0) {
                    $video->delete_video($mysql_id);
                    $smarty->assign('msg', $video->vid_msg(10));
               } else {
                    $action_vid[] = Array('media_link' => $url->url_create('vid_file', $user->user_info[user_username], $mysql_id),
				                             'media_path' => $path,
				                             'media_width' => 130,
				                             'media_height' => 10);

                    $actions->actions_add($user, "newvid", Array($user->user_info[user_username], $user->user_displayname, $mysql_id, $vid_title), $action_vid, 0, FALSE, "user", $user->user_info[user_id], $vid_privacy);
               } 
          } else {
               $smarty->assign('msg', $video->vid_msg($video->video_error));
          }
     } else {
          $smarty->assign('msg', $video->vid_msg($video->video_error));
     }
}

$count_yt = $database->database_num_rows($database->database_query("SELECT vid_id FROM se_vids WHERE (vid_user_id='".$user->user_info[user_id]."') AND (vid_location like \"%,%\")"));
$count_vids = $database->database_num_rows($database->database_query("SELECT vid_id FROM se_vids WHERE (vid_user_id='".$user->user_info[user_id]."') AND (vid_location not like \"%,%\")"));

if ($task == "add_vid") {

  include 'SolmetraUploader.php';

  $solmetraUploader = new SolmetraUploader();
  $solmetraUploader->gatherUploadedFiles();

  // CHECK THAT UPLOAD DIRECTORY EXISTS, IF NOT THEN CREATE
  $video_directory = $video->video_dir($user->user_info[user_id]);
  $video_path_array = explode("/", $video_directory);
  array_pop($video_path_array);
  array_pop($video_path_array);
  $subdir = implode("/", $video_path_array)."/";

  if(!is_dir($subdir))
  { 
    mkdir($subdir, 0777); 
    chmod($subdir, 0777); 
    $handle = fopen($subdir."index.php", 'x+');
    fclose($handle);
  }

  if(!is_dir($video_directory))
  {
    mkdir($video_directory, 0777);
    chmod($video_directory, 0777);
    $handle = fopen($video_directory."/index.php", 'x+');
    fclose($handle);
  }


  if((empty($_FILES["file"])) && ($_FILES['file']['error'] > 0))
  {
      exit();  
  } else {

    if ($_FILES["file"]["size"] < $user->level_info[level_vid_maxsize]) {

       if ($count_vids < $user->level_info[level_vid_maxnum]) {

          $exts = explode(",", $vid_settings[exts]);

          $filename_ext = $_FILES['file']['name'];
          $ext = substr($filename_ext, strrpos($filename_ext,'.')+1, strlen($filename_ext)-(strrpos($filename_ext,'.')+1));

          if ((in_array($ext, $exts, true)) OR ($exts[0] == '')) {

              $tmp_dir = './uploads_vid/tmp/';

              $filename = basename($_FILES["file"]["tmp_name"]);
              move_uploaded_file($_FILES["file"]["tmp_name"], $tmp_dir.$filename);

              $vid_title = censor($_POST['vid_title']);
              $vid_tags = $_POST['vid_tags'];
              $vid_desc = str_replace("&lt;br&gt;", "", $_POST['vid_desc']);
              $vid_desc = censor(str_replace("\r\n", "<br>", $vid_desc));
              $vid_cat = $_POST['vid_cat'];
              $vid_location = $filename;
              $vid_datecreated = time();
              $vid_search = $_POST['vid_search'];
              $vid_privacy = $_POST['vid_privacy'];
              $vid_comments = $_POST['vid_comments'];

              if(!in_array($vid_privacy, $level_vid_privacy)) { $vid_privacy = $level_vid_privacy[0]; }
              if(!in_array($vid_comments, $level_vid_comments)) { $vid_comments = $level_vid_comments[0]; }

              // CHECK THAT SEARCH IS NOT BLANK
              if($user->level_info['level_vid_search'] == 0 OR !$user->level_info['level_vid_search'])
                $vid_search = 1;

              $video->validation($vid_title, $vid_desc, $vid_tags);

              if ($video->video_error == 0)
              {

                // DELETE OLD AND FAILED ENCODING JOBS AFTER 1h30min
                $current_encoder_time = time()-5400;
                $vid_delete_failed = "SELECT vid_id, vid_user_id FROM se_vids WHERE vid_is_converted='2' AND vid_datecreated < ".$current_encoder_time." LIMIT 1";
                $vdf = $database->database_fetch_assoc($database->database_query($vid_delete_failed));
                $video->delete_video($vdf[vid_id], $vdf[vid_user_id], FALSE);              

                $video->add_video($vid_datecreated, $vid_title, $vid_desc, $vid_cat, $vid_location, $vid_tags, $task, $vid_comments, $vid_privacy, $vid_search);
                header("Location: user_vid.php?message=8");
                exit;

              } else {
                $smarty->assign('msg', $video->vid_msg($video->video_error));
              }
          } else {
              $smarty->assign('msg', $video->vid_msg(4));
          }
       } else {
          $smarty->assign('msg', $video->vid_msg(12));
       } 
    } else {
       $smarty->assign('msg', $video->vid_msg(6));
    }
  }
}

if ((isset($_GET['p'])) && ($_GET['p'] >= 1)) { $p = $_GET['p']; } else { $p = 1; }

$jpvideos_whole_array = $video->vid_list($user->user_info[user_id], TRUE, 0, $p, 0, 10, "", TRUE, FALSE);

if ($user->level_info[level_vid_allow] == 2 OR $user->level_info[level_vid_allow] == 3) {

     // GETS ALL ALLOWED VIDEO PROVIDERS' NAMES
     $providers = $video_api->getAllProviders();
     $provider = explode(",", $user->level_info[level_vid_prov]);
     array_shift($provider);

     $prov_type = $providers[1];
     $prov_name = $providers[0];
     $provs = array_combine($prov_type, $prov_name);

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
          } else {
               $allowed_providers = $provs[$provider[$i]];
          }
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

     $provider2 = explode(",", $user->level_info[level_vid_prov]);
     array_shift($provider2);

     $provider_disabled = explode(",", $vid_settings[disable]);
     array_shift($provider_disabled);

     $prov_number = count($provider2);

     for ($i=0; $i<count($provider_disabled); $i++) {
         for ($j=0; $j<$prov_number; $j++) {
             if ($provider2[$j] == $provider_disabled[$i]){
                 unset($provider2[$j]);
             }
         }
     }

     $provider_keys = array_keys($provider2);

     for ($i=0; $i<count($provider2); $i++) {
         $real_provider[$i] = $provider2[$provider_keys[$i]];
     }

     $provider2 = $real_provider;

     $smarty->assign('provider2', $provider2);
}

$vids_failed = Array();
$vids_failed_query = $database->database_query("SELECT vid_id, vid_title, vid_location FROM se_vids WHERE vid_is_converted='4' AND vid_user_id='".$user->user_info[user_id]."'");
if ($database->database_num_rows($vids_failed_query) > 0) {
     while ($vids_failed_items = $database->database_fetch_assoc($vids_failed_query)) {
         $vids_failed[] = array('id' => $vids_failed_items[vid_id], 'title' => $vids_failed_items[vid_title], 'location' => $vids_failed_items[vid_location]);
     }
     $smarty->assign('failed_vids', $vids_failed);
}

$num_favs = $database->database_fetch_assoc($database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id='".$user->user_info[user_id]."' LIMIT 1"));
$num_favs = (int)count(explode(",", $num_favs[vidfav_ids]))-1;
$num_vids = $database->database_num_rows($database->database_query("SELECT * FROM se_vids WHERE vid_user_id='".$user->user_info[user_id]."' AND vid_is_converted='1'"));
$smarty->assign('num_favs', $num_favs);
$smarty->assign('num_vids', $num_vids);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('allowed_providers', $allowed_providers);
$smarty->assign('count_vids', $count_vids);
$smarty->assign('count_yt', $count_yt);
$smarty->assign('count_videos', $jpvideos_whole_array['page_vars'][1]);
$smarty->assign('page_vars', $jpvideos_whole_array['page_vars']);
$smarty->assign('all_videos', $jpvideos_whole_array['videos']);
include "footer.php";
?>