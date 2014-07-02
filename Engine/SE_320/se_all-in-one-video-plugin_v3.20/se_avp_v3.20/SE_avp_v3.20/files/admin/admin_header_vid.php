<?php  
// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT 
if(!defined('SE_PAGE')) { exit(); }  

// INCLUDE VIDEO API CLASS FILE
include "../include/class_vid_api.php";
$video_api = new se_vid_api();

// INCLUDE VIDEO CLASS FILE
include "../include/class_vid.php";
$video = new se_vid();

// INCLUDE VIDEO FUNCTION FILE
include "../include/functions_vid.php";

$vid_settings = $video->vid_settings();

?>