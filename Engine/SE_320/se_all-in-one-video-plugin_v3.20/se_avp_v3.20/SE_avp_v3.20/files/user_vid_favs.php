<?php
$page = "user_vid_favs";
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

if ($task == 'remove_fav' AND isset($_GET['id'])) {
    $database->database_query("UPDATE se_vidfavs SET vidfav_ids = REPLACE(se_vidfavs.vidfav_ids, ',".$_GET['id']."', '') WHERE vidfav_user_id = '".$user->user_info['user_id']."'");
}

if ((isset($_GET['p'])) && ($_GET['p'] >= 1)) { $p = $_GET['p']; } else { $p = 1; }

$query = "SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id='".$user->user_info['user_id']."' LIMIT 1";

$vidfav = $database->database_fetch_assoc($database->database_query($query));

if ($database->database_num_rows($database->database_query($query)) == 0 OR $vidfav[vidfav_ids] == "") {
    header("Location: user_vid.php");
    exit;
}

$vidfav = explode(",", $vidfav[vidfav_ids]);

$ii=0;
foreach($vidfav as $key_null=>$val_null){
    if($val_null && $database->database_num_rows($database->database_query("SELECT vid_location FROM se_vids WHERE vid_id='".$val_null."' LIMIT 1")) == 1){
        $vidfavs[$ii]=$val_null;
        $ii++;
    } else {
        if ($val_null) {
            $database->database_query("UPDATE se_vidfavs SET vidfav_ids = REPLACE(se_vidfavs.vidfav_ids, ',".$val_null."', '') WHERE vidfav_user_id = '".$user->user_info['user_id']."'");
        }
    }
}

$num_of_favs = count($vidfavs);

$where = "";
for ($i=0; $i<$num_of_favs; $i++){
    if ($num_of_favs > 1) {
        if($i == 0){
            $where .= " AND (";
            $where .= "vid_id='".$vidfavs[$i]."'";
        } elseif ($i == ($num_of_favs-1)) {
            $where .= " OR vid_id='".$vidfavs[$i]."')";
        } else {
            $where .= " OR vid_id='".$vidfavs[$i]."'";
        }
    } else {
        $where .= " AND vid_id='".$vidfavs[$i]."'";
    }
}

if ($where != "") {
    $jpvideos_whole_array = $video->vid_list($user->user_info[user_id], TRUE, 0, $p, 0, 10, $where, FALSE, FALSE);
    $smarty->assign('page_vars', $jpvideos_whole_array['page_vars']);
    $smarty->assign('all_videos', $jpvideos_whole_array['videos']);
} else {
    header("Location: user_vid.php");
    exit;
}

$num_favs = $database->database_fetch_assoc($database->database_query("SELECT vidfav_ids FROM se_vidfavs WHERE vidfav_user_id='".$user->user_info[user_id]."' LIMIT 1"));
$num_favs = (int)count(explode(",", $num_favs[vidfav_ids]))-1;
$num_vids = $database->database_num_rows($database->database_query("SELECT * FROM se_vids WHERE vid_user_id='".$user->user_info[user_id]."' AND vid_is_converted='1'"));
$smarty->assign('num_favs', $num_favs);
$smarty->assign('num_vids', $num_vids);
include "footer.php";
?>