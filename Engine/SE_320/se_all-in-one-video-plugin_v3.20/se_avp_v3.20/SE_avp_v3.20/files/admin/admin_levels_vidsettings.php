<?php
$page = "admin_levels_vidsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { header("Location: admin_levels.php"); exit(); }
$level_info = $database->database_fetch_assoc($level);

// SET RESULT VARIABLE
$result = 0;
$is_error = 0;

// SAVE CHANGES
if($task == "dosave") {
  $level_info[level_vid_allow] = $_POST['level_vid_allow'];
  $level_info[level_vid_maxnum] = $_POST['level_vid_maxnum'];
  $level_info[level_vid_maxsize] = $_POST['level_vid_maxsize'];
  $level_info[level_vid_prov_maxnum] = $_POST['level_vid_prov_maxnum'];
  $level_info[level_vid_search] = $_POST['level_vid_search'];
  $level_info[level_vid_privacy] = is_array($_POST['level_vid_privacy']) ? $_POST['level_vid_privacy'] : Array();
  $level_info[level_vid_comments] = is_array($_POST['level_vid_comments']) ? $_POST['level_vid_comments'] : Array();

  // GET PRIVACY AND PRIVACY DIFFERENCES
  if( empty($level_info[level_vid_privacy]) || !is_array($level_info[level_vid_privacy]) ) $level_info[level_vid_privacy] = array(63);
  rsort($level_info[level_vid_privacy]);
  $new_privacy_options = $level_info[level_vid_privacy];
  $level_info[level_vid_privacy] = serialize($level_info[level_vid_privacy]);

  // GET COMMENT AND COMMENT DIFFERENCES
  if( empty($level_info[level_vid_comments]) || !is_array($level_info[level_vid_comments]) ) $level_info[level_vid_comments] = array(63);
  rsort($level_info[level_vid_comments]);
  $new_comments_options = $level_info[level_vid_comments];
  $level_info[level_vid_comments] = serialize($level_info[level_vid_comments]);

  $level_info = $video_api->getAdminType($level_info);

  if($level_info[level_vid_allow] == 2 AND ((!$level_info[level_vid_maxsize] OR $level_info[level_vid_maxsize] <= 1) OR (!$level_info[level_vid_maxnum] OR $level_info[level_vid_maxnum] < 1))) {
    $level_info[level_vid_maxnum] = 1;
    $level_info[level_vid_maxsize] = 20480;
  } elseif($level_info[level_vid_allow] == 1 AND (!$level_info[level_vid_prov] OR (!level_vid_prov_maxnum OR level_vid_prov_maxnum < 1))) {
    $video_provs = $video_api->getAllProviders();
    $level_info[level_vid_prov] = $video_provs[1][0];
    $level_info[level_vid_prov_maxnum] = 1;
  }

  // CHECK THAT A NUMBER GREATER THAN 1 WAS ENTERED FOR MAXSIZE
  if($level_info[level_vid_maxsize] <= 1 OR $video->is_int($level_info[level_vid_maxsize]) !== TRUE) {
    $is_error = 13500054;
 
  // CHECK THAT MAX ADD VIDS IS A NUMBER
  } elseif($level_info[level_vid_prov_maxnum] < 1 OR $video->is_int($level_info[level_vid_prov_maxnum]) !== TRUE) {
    $is_error = 13500052;
 
  // CHECK THAT MAX UPLOAD VIDS IS A NUMBER
  } elseif($level_info[level_vid_maxnum] < 1 OR $video->is_int($level_info[level_vid_maxnum]) !== TRUE) {
    $is_error = 13500052;

  } elseif(($level_info[level_vid_allow] == 2 OR $level_info[level_vid_allow] == 3) AND !$level_info[level_vid_prov]) {
    $is_error = 13500171;
  } else {

    $level_info[level_vid_maxsize] = $level_info[level_vid_maxsize]*1024;
    $database->database_query("UPDATE se_levels SET 
			level_vid_allow='$level_info[level_vid_allow]',
			level_vid_maxnum='$level_info[level_vid_maxnum]',
			level_vid_maxsize='$level_info[level_vid_maxsize]',
                        level_vid_prov='$level_info[level_vid_prov]',
                        level_vid_prov_maxnum='$level_info[level_vid_prov_maxnum]',
			level_vid_search='$level_info[level_vid_search]',
			level_vid_privacy='$level_info[level_vid_privacy]',
			level_vid_comments='$level_info[level_vid_comments]'
			WHERE level_id='$level_info[level_id]'");

    if( !$level_info[level_vid_search] )
    {
      $database->database_query("UPDATE se_vids INNER JOIN se_users ON se_users.user_id=se_vids.vid_user_id SET se_vids.vid_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    $database->database_query("UPDATE se_vids INNER JOIN se_users ON se_users.user_id=se_vids.vid_user_id SET se_vids.vid_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='$level_info[level_id]' AND se_vids.vid_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_vids INNER JOIN se_users ON se_users.user_id=se_vids.vid_user_id SET se_vids.vid_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='$level_info[level_id]' AND se_vids.vid_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $result = 1;
  }
} // END DOSAVE TASK

// GET MAXSIZE INTO KB AGAIN
$level_info[level_vid_maxsize] = $level_info[level_vid_maxsize]/1024;
$providers = $video_api->getAllProviders();

// GET PREVIOUS PRIVACY SETTINGS
for($c=6;$c>0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $privacy_options[$priv] = user_privacy_levels($priv);
  }
}

for($c=6;$c>=0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $comment_options[$priv] = user_privacy_levels($priv);
  }
}

// ASSIGN VARIABLES AND SHOW vid SETTINGS PAGE
$smarty->assign('providers', $providers);
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);
$smarty->assign('level_vid_privacy', unserialize($level_info[level_vid_privacy]));
$smarty->assign('level_vid_comments', unserialize($level_info[level_vid_comments]));
$smarty->assign('vid_privacy', $privacy_options);
$smarty->assign('vid_comments', $comment_options);
include "admin_footer.php";
?>