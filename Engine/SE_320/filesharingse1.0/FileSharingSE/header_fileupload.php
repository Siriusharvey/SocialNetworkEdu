<?php

/* $Id: header_classified.php $ */

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE CLASSIFIEDS CLASS FILE
include "./include/class_uploads.php";

// INCLUDE CLASSIFIEDS FUNCTION FILE
include "./include/functions_upload.php";

// PRELOAD LANGUAGE
SE_Language::_preload(7800007);

// SET MAIN MENU VARS
if( ($user->user_exists && $user->level_info['level_file_upload_allow']) || (!$user->user_exists && $setting['setting_permission_fileuploads']) )
{
  $plugin_vars['menu_main'] = array('file' => 'browse_upload.php', 'title' => 7800007);
}

// SET USER MENU VARS
if( ($user->user_exists && $user->level_info['level_file_upload_allow']) )
{
  $plugin_vars['menu_user'] = array('file' => 'user_file_uploads.php', 'icon' => 'fileupload_fileupload16.gif', 'title' => 7800102);
}

if(strpos($_SERVER['SCRIPT_NAME'], 'upload_desc.php'))
	$smarty->assign('include_prototype', true);

// SET SEARCH HOOK
if($page == "search")
  SE_Hook::register("se_search_do", 'search_upload');

// SET USER DELETION HOOK
SE_Hook::register("se_user_delete", 'deleteuser_upload');

SE_Hook::register("se_site_statistics", 'site_statistics_fileuploads');
?>