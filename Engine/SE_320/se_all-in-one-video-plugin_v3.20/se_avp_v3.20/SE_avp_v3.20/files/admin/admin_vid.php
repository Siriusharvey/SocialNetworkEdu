<?php
$page = "admin_vid";
include "admin_header.php";


$task                 = ( !empty($_POST['task'])                ? $_POST['task']                : NULL );
$vidcat_id      = ( !empty($_POST['vidcat_id'])     ? $_POST['vidcat_id']     : NULL );
$vidcat_title   = ( !empty($_POST['vidcat_title'])  ? $_POST['vidcat_title']  : NULL );

// SET RESULT VARIABLE
$result = 0;


$skins_array = array();


$dir = '../include/vid_skins/';
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && strstr($file, '.') !== FALSE) {
            $ext = strrchr($file, '.'); 
            if($ext !== false) { 
                 $file = substr($file, 0, -strlen($ext)); 
            }  
            $skins_array[] = $file;
        }
    }
    closedir($handle);
}


// DELETE CATEGORY
if( $task=="deletevidcat" )
{
  $sql = "DELETE FROM se_vidcats WHERE vidcat_id='{$vidcat_id}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

  $sql = "UPDATE se_vids SET vid_cat = '0' WHERE vid_cat='{$vidcat_id}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if($database->database_affected_rows($resource))
    echo '{"result" : "success"}';
  else
    echo '{"result" : "failure"}';
  exit();
}


// CREATE CATEGORY
else if( $task=="createvidcat" )
{
  $lvar_id = SE_Language::edit(0, $vidcat_title, NULL, LANGUAGE_INDEX_SUBNETS);
  $sql = "INSERT INTO se_vidcats (vidcat_languagevar_id,vidcat_title) VALUES ('{$lvar_id}','{$vidcat_title}')";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_affected_rows($resource) )
    echo '{"result" : "success", "vidcat_id" : '.$database->database_insert_id().', "vidcat_languagevar_id" : '.$lvar_id.'}';
  else
    echo '{"result" : "failure"}';
  exit();
}


// EDIT CATEGORY
elseif( $task=="editvidcat" )
{
  // Get langvar id
  $sql = "SELECT * FROM se_vidcats WHERE vidcat_id='{$vidcat_id}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    echo '{"result" : "failure"}';
    exit();
  }
  
  $result = $database->database_fetch_assoc($resource);
  $lvar_id = $result['vidcat_languagevar_id'];
  
  
  SE_Language::edit($lvar_id, $vidcat_title);
  $sql = "UPDATE se_vidcats SET vidcat_title='{$vidcat_title}' WHERE vidcat_id='{$vidcat_id}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_affected_rows($resource) || $resource )
    echo '{"result" : "success"}';
  else
    echo '{"result" : "failure"}';
  
  exit();
}


// SAVE CHANGES
if($task == "dosave") {

      $vid_settings[skin] = $_POST['setting_vid_skin'];
      $vid_settings[ffmpeg] = $_POST['setting_vid_ffmpeg_path'];
      $vid_settings[flvtool2] = $_POST['setting_vid_flvtool2_path'];
      $vid_settings[permission] = $_POST['setting_permission_vid'];
      $vid_settings[width] = $_POST['vid_width'];
      $vid_settings[height] = $_POST['vid_height'];
      $vid_settings[thumb_width] = $_POST['vid_thumb_width'];
      $vid_settings[thumb_height] = $_POST['vid_thumb_height'];
      $vid_settings[yt] = $_POST['setting_yt_streaming'];
      $vid_settings[mimes] = preg_replace('#\s{1,}#', '', $_POST['setting_vid_mimes']);
      $vid_settings[exts] = preg_replace('#\s{1,}#', '', $_POST['setting_vid_exts']);
      $vid_settings[logo] = $_POST['vid_logo'];
      $vid_settings[embed] = $_POST['setting_vid_embed'];

        $width = explode('.', $vid_settings[width]);
        $height = explode('.', $vid_settings[height]);
        
        $width = $width[0];
        $height = $height[0];

        $width_half = $width/2;
        $height_half = $height/2;
        
        if($video->is_int($width_half) === false)
        {
          $width+=1;
        }

        if($video->is_int($height_half) === false)
        {
          $height+=1;
        }

        $thumb_width = explode('.', $vid_settings[thumb_width]);
        $thumb_height = explode('.', $vid_settings[thumb_height]);
        
        $thumb_width = $thumb_width[0];
        $thumb_height = $thumb_height[0];

        $thumb_width_half = $thumb_width/2;
        $thumb_height_half = $thumb_height/2;
        
        if($video->is_int($thumb_width_half) === false)
        {
          $thumb_width+=1;
        }

        if($video->is_int($thumb_height_half) === false)
        {
          $thumb_height+=1;
        }

      $vid_settings[width] = $width;
      $vid_settings[height] = $height;
      $vid_settings[thumb_width] = $thumb_width;
      $vid_settings[thumb_height] = $thumb_height;
      $vid_settings = $video_api->getAdminType($vid_settings);
      $vid_settings[disable] = $vid_settings[level_vid_prov];

      $database->database_query("UPDATE se_vidsettings SET setting_permission_vid='$vid_settings[permission]', setting_vid_skin='$vid_settings[skin]', setting_vid_ffmpeg_path='$vid_settings[ffmpeg]', setting_vid_flvtool2_path='$vid_settings[flvtool2]', vid_width='$vid_settings[width]', vid_height='$vid_settings[height]', vid_thumb_width='$vid_settings[thumb_width]', vid_thumb_height='$vid_settings[thumb_height]', setting_vid_mimes='$vid_settings[mimes]', setting_vid_exts='$vid_settings[exts]', setting_yt_streaming='$vid_settings[yt]', vid_logo='$vid_settings[logo]', vid_prov_disable='$vid_settings[level_vid_prov]', setting_vid_embed='$vid_settings[embed]'");

      $result = 1;
} 

$database->database_query("DELETE FROM se_vidcats WHERE vidcat_title=''");

// GET VIDEO CATEGORIES
$categories_array = se_vid::vid_category_list('true');

$providers = $video_api->getAllProviders();

// ASSIGN VARIABLES AND SHOW vid SETTINGS PAGE
$smarty->assign('providers', $providers);
$smarty->assign('vid_settings', $vid_settings);
$smarty->assign('result', $result);
$smarty->assign('skins', $skins_array);
$smarty->assign('vidcats', $categories_array);
include "admin_footer.php";
?>