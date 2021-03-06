<?php
$page = "browse_vids";
include "header.php";

if(isset($_GET['q'])) { $q = trim($_GET['q']); } else { $q = ""; }
if(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "v"; }
if(isset($_GET['c'])) { $c = (int)$_GET['c']; } else { $c = 1; }
if(isset($_GET['b'])) { $b = (int)$_GET['b']; } else { $b = 0; }
if(isset($_GET['p'])) { $p = (int)$_GET['p']; } else { $p = 1; }
if(isset($_GET['type'])) { $type = $_GET['type']; } else { $type = "search"; }

if($database->database_num_rows($database->database_query("SELECT * FROM se_vidcats WHERE vidcat_id='".$c."'")) == 0) {
     $c = 1;
}

$query = "SELECT * FROM se_vids WHERE vid_search=1 AND vid_is_converted=1";

if($c > 1) {
     $query .= " AND vid_cat='".$c."'";
}

if($q != '') {
     $q = strip_tags($q);
     $q = mysql_real_escape_string($q);
     switch ($type) {
          case "search":
                    $query .= " AND ((vid_title like \"%$q%\") OR (vid_desc like \"%$q%\"))";
          break;
          case "tag":
                    $query .= " AND vid_tags like \"%$q%\"";
          break;
          default:
                    $query .= " AND ((vid_title like \"%$q%\") OR (vid_desc like \"%$q%\"))";
                    $type = "search";
     }
}

switch ($s) {
     case "p":
               $query .= " ORDER BY vid_rating_value";
     break;
     case "v":
               $query .= " ORDER BY vid_views";
     break;
     case "c":
               $query .= " ORDER BY vid_datecreated";
     break;
     default:
               $query .= " ORDER BY vid_views";
               $s = "v";
}

switch ($b) {
     case 0:
               $query .= " DESC";
     break;
     case 1:
               $query .= " ASC";
     break;
     default:
               $query .= " DESC";
               $s = 0;
}

$result = $database->database_query($query);

$num = 0;

while ($videos_check = $database->database_fetch_assoc($result)) {
          $username = $database->database_fetch_assoc($database->database_query("SELECT user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_id='".$videos_check['vid_user_id']."'"));
          
          $vid_author = new se_user();
          $vid_author->user_exists = 1;
          $vid_author->user_info['user_id'] = $videos_check['vid_user_id'];
          $vid_author->user_info['user_username'] = $username['user_username'];
          $vid_author->user_info['user_fname'] = $username['user_fname'];
          $vid_author->user_info['user_lname'] = $username['user_lname'];
          $vid_author->user_info['user_photo'] = $username['user_photo'];
          $vid_author->user_displayname();

          $vid_p = $vid_author->user_privacy_max($user);
                 	          	   
          if(!($videos_check['vid_privacy'] & $vid_p)) {
             continue;
          } else {
             $num += 1;
          }
}

$page_vars = $video->vid_page_vars($p, $num, 21);
$vids_array = Array();

$query .= " LIMIT $page_vars[3],21";

$result = $database->database_query($query);

$max_rating = 5;

while ($videos = $database->database_fetch_assoc($result)) {
          $category = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_vidcats WHERE vidcat_id='".$videos['vid_cat']."'"));

          $rating_full = floor($videos[vid_rating_value]);
   
          if($rating_full != $videos[vid_rating_value]) { $rating_partial = 1; } else { $rating_partial = 0; }
          $rating_empty = $max_rating-($rating_full+$rating_partial);
          
          $username = $database->database_fetch_assoc($database->database_query("SELECT user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_id='".$videos['vid_user_id']."'"));

          $video_dir = $video->video_dir($videos['vid_user_id']);

          $vid_locations = explode(',', $videos['vid_location']);
   
          if (count($vid_locations) == 2) {
             $vid_location = $vid_locations[0];
             $vid_img_location = $video_dir.$videos[vid_id].'.jpg';;
          } else {
             $vid_location = $video_dir.$vid_locations[0].'flv';
             $vid_img_location = $video_dir.$vid_locations[0].'_thumb_0.jpg';
          }
          
          $vid_author = new se_user();
          $vid_author->user_exists = 1;
          $vid_author->user_info['user_id'] = $videos['vid_user_id'];
          $vid_author->user_info['user_username'] = $username['user_username'];
          $vid_author->user_info['user_fname'] = $username['user_fname'];
          $vid_author->user_info['user_lname'] = $username['user_lname'];
          $vid_author->user_info['user_photo'] = $username['user_photo'];
          $vid_author->user_displayname();

          $vid_privacy_max = $vid_author->user_privacy_max($user);
                 	          	   
          if(!($videos['vid_privacy'] & $vid_privacy_max)) {
             continue;
          } else {
             $vids_array[] = array('title' => $videos['vid_title'], 'desc' => $videos['vid_desc'], 'cat_lang' => $category['vidcat_languagevar_id'], 'cat_id' => $category['vidcat_id'], 'location' => $vid_location, 'date' =>  $videos['vid_datecreated'], 'img' => $vid_img_location, 'id' => $videos['vid_id'], 'views' => $videos['vid_views'], 'full' => $rating_full, 'empty' => $rating_empty, 'partial' => $rating_partial, 'username' => $username['user_username']);
          }
}

$number_of_cats = $database->database_num_rows($database->database_query("SELECT * FROM se_vidcats"));
$categories_array = se_vid::vid_category_list('false');

$tags_array = $video->CreateTagArray();

$smarty->assign('tag_cloud', $video->printTagCloud($tags_array));
$smarty->assign('vid_settings', $vid_settings);
$smarty->assign('count_cats', $number_of_cats);
$smarty->assign('vidcats', $categories_array);
$smarty->assign('all_videos', $vids_array);
$smarty->assign('s', $s);
$smarty->assign('c', $c);
$smarty->assign('b', $b);
$smarty->assign('p', $page_vars[0]);
$smarty->assign('q', $q);
$smarty->assign('type', $type);
$smarty->assign('p_start', $page_vars[3]);
$smarty->assign('p_start_lang', $page_vars[4]);
$smarty->assign('p_end', $page_vars[5]);
$smarty->assign('p_end_lang', $page_vars[6]);
$smarty->assign('pages', $page_vars[7]);
$smarty->assign('total_videos', $page_vars[1]);
include "footer.php";
?>