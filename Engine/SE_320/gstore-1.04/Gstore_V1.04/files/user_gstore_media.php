<?php


$page = "user_gstore_media";
include "header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = ""; }
if(isset($_GET['gstore_id'])) { $gstore_id = $_GET['gstore_id']; } elseif(isset($_POST['gstore_id'])) { $gstore_id = $_POST['gstore_id']; } else { $gstore_id = 0; }
if(isset($_GET['gstoremedia_id'])) { $gstoremedia_id = $_GET['gstoremedia_id']; } elseif(isset($_POST['gstoremedia_id'])) { $gstoremedia_id = $_POST['gstoremedia_id']; } else { $gstoremedia_id = 0; }
if(isset($_POST['spot'])) { $spot = $_POST['spot']; } else { $spot = "1"; }
if(isset($_GET['justadded'])) { $justadded = $_GET['justadded']; } elseif(isset($_POST['justadded'])) { $justadded = $_POST['justadded']; } else { $justadded = 0; }

// ENSURE CLASSIFIED ARE ENABLED FOR THIS USER
if( !$user->level_info['level_gstore_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// MAKE SURE THIS CLASSIFIED BELONGS TO THIS USER AND IS NUMERIC
$gstore = $database->database_query("SELECT * FROM se_gstores WHERE gstore_id='{$gstore_id}' AND gstore_user_id='{$user->user_info['user_id']}' LIMIT 1");
if( !$database->database_num_rows($gstore) )
{
  header("Location: user_gstore.php");
  exit();
}
$gstore_info = $database->database_fetch_assoc($gstore);

// INITIALIZE CLASSIFIED OBJECT
$gstore = new se_gstore($user->user_info['user_id'], $gstore_id);

// SHOW BLANK PAGE FOR AJAX
if($task == "blank") {
  exit;
}




// DELETE SMALL PHOTO WITH IFRAME AJAX
if($task == "deletemedia") {

  $gstore->gstore_media_delete(0, 1, "se_gstoremedia.gstoremedia_id DESC", "se_gstoremedia.gstoremedia_id='{$gstoremedia_id}'");
  exit;
}




// UPLOAD LARGE PHOTO WITH IFRAME AJAX
if($task == "uploadmedia") {

  // GET ALBUM INFO
  $gstorealbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_gstorealbums WHERE gstorealbum_gstore_id='{$gstore_id}' LIMIT 1"));

  // GET TOTAL SPACE USED
  $space_used = $gstore->gstore_media_space();
  $space_left = ( !empty($gstore->gstoreowner_level_info['level_gstore_album_storage']) ? ($gstore->gstoreowner_level_info['level_gstore_album_storage'] - $space_used) : FALSE );

  $fileid = "file";
  if($_FILES[$fileid]['name'] != "") {
    $file_result[$fileid] = $gstore->gstore_media_upload($fileid, $gstorealbum_info['gstorealbum_id'], $space_left);
    if($file_result[$fileid]['is_error'] == 0) {
      $file_result[$fileid]['message'] = $gstore->gstore_dir($gstore_id).$file_result[$fileid]['gstoremedia_id'].".".$file_result[$fileid]['gstoremedia_ext'];
      $gstoremedia_id_new = $file_result[$fileid]['gstoremedia_id'];
    } else {
      $file_result[$fileid]['message'] = addslashes($file_result[$fileid]['error_message']);
    }
  } else {
      $file_result[$fileid]['is_error'] = 1;
      $file_result[$fileid]['message'] = $user_gstore_edit_media[16];
  }

  $result = $file_result[$fileid]['message'];
  $result_code = $file_result[$fileid]['is_error'];

  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body onLoad=\"parent.uploadComplete('$result_code', '$result', '$spot', '$gstoremedia_id_new');\"></body></html>";
  exit;
}




// UPLOAD SMALL PHOTO
if($task == "upload") {
  $gstore->gstore_photo_upload("photo");
  $is_error = $gstore->is_error;
  $error_message = $gstore->error_message;
  if($is_error == 0) { $gstore->gstore_lastupdate(); }
}




// GET CLASSIFIED ALBUM INFO
$gstorealbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_gstorealbums WHERE gstorealbum_gstore_id='{$gstore->gstore_info['gstore_id']}' LIMIT 1"));

// GET TOTAL FILES IN CLASSIFIED ALBUM
$total_files = $gstore->gstore_media_total($gstorealbum_info[gstorealbum_id]);

// MAKE MEDIA PAGES
$files_per_page = 16;
$p = 1;
$page_vars = make_page($total_files, $files_per_page, $p);

// GET MEDIA ARRAY
$file_array = $gstore->gstore_media_list($page_vars[0], $files_per_page, "gstoremedia_id ASC", "(gstoremedia_gstorealbum_id='{$gstorealbum_info['gstorealbum_id']}')");

$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);
$smarty->assign('error_message', $error_message);
$smarty->assign('gstore', $gstore);
$smarty->assign('gstore_id', $gstore_id);
$smarty->assign('justadded', $justadded);
include "footer.php";
?>