<?php
$page = "vids";
include "header.php";

if ((isset($_GET['p'])) && ($_GET['p'] >= 1)) { $p = $_GET['p']; } else { $p = 1; }

$jpvideos_whole_array = $video->vid_list($owner->user_info[user_id], FALSE, 0, $p, 0, 21);

$smarty->assign('count_videos', $jpvideos_whole_array['page_vars'][1]);
$smarty->assign('page_vars', $jpvideos_whole_array['page_vars']);
$smarty->assign('all_videos', $jpvideos_whole_array['videos']);
$smarty->assign('vid_settings', $vid_settings);
include "footer.php";
?>
