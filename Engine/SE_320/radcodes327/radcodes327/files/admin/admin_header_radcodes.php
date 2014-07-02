<?php
// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

include_once "../include/class_radcodes.php";
include_once "../include/class_radcodes_map.php";

SE_Hook::register("se_admin_notifications", "radcodes_hook_se_admin_notifications");

if ($page == 'admin_viewplugins') {
  $radcodes_plugins = rc_toolkit::remote_check_plugins();
  foreach ($radcodes_plugins as $k=>$v) {
    $versions[$k] = $v['version'];
    $versions[$k."_rounded"] = $v['version_rounded'];
  }
}

if (!class_exists('se_comment')) {
  include_once "include/class_comment.php";
}

