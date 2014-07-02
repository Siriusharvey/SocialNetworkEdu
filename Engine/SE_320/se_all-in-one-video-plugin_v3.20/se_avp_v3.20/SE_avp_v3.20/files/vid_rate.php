<?
$page = "vid_rate";
include "header.php";

// SET PARAMETER
$max_rating = 5;

// RETRIEVE REQUIRED VARIABLES
if(isset($_GET['object_id'])) { $object_id = $_GET['object_id']; } else { $object_id = 0; }
if(isset($_GET['rating'])) { $rating = (int)$_GET['rating']; } else { $rating = 0; }
if(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// EXIT IF USER IS NOT LOGGED IN
$rating_allowed = 1;
if($user->user_exists == 0) { $rating_allowed = 0; }

// EXIT IF VARIABLES AREN'T VALID
$object = $database->database_query("SELECT vid_id FROM se_vids WHERE vid_id=$object_id");
if($database->database_num_rows($object) != 1) { echo "Incorrect Parameters Specified"; exit(); }

// RETRIEVE RATING ROW
$rating_query = $database->database_query("SELECT vid_rating_value, vid_rating_raters, vid_rating_raters_num FROM se_vids WHERE vid_id=$object_id");
$rating_info = $database->database_fetch_assoc($rating_query);

// GET NUMBER OF FULL, PARTIAL, EMPTY STARS
$rating_full = floor($rating_info[vid_rating_value]);
if($rating_full != $rating_info[vid_rating_value]) { $rating_partial = 1; } else { $rating_partial = 0; }
$rating_empty = $max_rating-($rating_full+$rating_partial);

// RETRIEVE RATERS ARRAY
$raters = explode(",", trim($rating_info[vid_rating_raters]));
if(in_array($user->user_info[user_id], $raters)) { $rating_allowed = 0; }

// IF RATING IS ALLOWED AND RATING IS WITHIN THE CORRECT PARAMETERS
if($task == "rate" && $rating_allowed != 0 && $rating <= $max_rating && $rating >= 0) {

  $new_total_ratings = $rating_info[vid_rating_raters_num]+1;
  $new_rating = round(($rating_info[vid_rating_value]*$rating_info[vid_rating_raters_num]+$rating)/$new_total_ratings, 2);
  $new_raters = $rating_info[vid_rating_raters].",".$user->user_info[user_id];
  $database->database_query("UPDATE se_vids SET vid_rating_value='$new_rating', vid_rating_raters_num='$new_total_ratings', vid_rating_raters='$new_raters' WHERE vid_id=$object_id");

  // REFRESH
  header("Location: vid_rate.php?object_id=$object_id");
  exit();
}



// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('rating_value', $rating_info[rating_value]);
$smarty->assign('max_rating', $max_rating);
$smarty->assign('rating_full', $rating_full);
$smarty->assign('rating_partial', $rating_partial);
$smarty->assign('rating_empty', $rating_empty);
$smarty->assign('rating_total', $rating_info[vid_rating_raters_num]);
$smarty->assign('rating_allowed', $rating_allowed);
$smarty->assign('object_id', $object_id);
include "footer.php";
?>