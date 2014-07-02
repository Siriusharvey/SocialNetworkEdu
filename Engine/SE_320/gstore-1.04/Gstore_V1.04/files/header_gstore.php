<?php



// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE gstoreS CLASS FILE
include "./include/class_gstore.php";

// INCLUDE gstoreS FUNCTION FILE
include "./include/functions_gstore.php";

// PRELOAD LANGUAGE
SE_Language::_preload(5555007);

// SET MAIN MENU VARS
if( ($user->user_exists && $user->level_info['level_gstore_allow']) || (!$user->user_exists && $setting['setting_permission_gstore']) )
{
  $plugin_vars['menu_main'] = array('file' => 'browse_gstores.php', 'title' => 5555160);
}

// SET USER MENU VARS
if( ($user->user_exists && $user->level_info['level_gstore_allow']) )
{
  $plugin_vars['menu_user'] = array('file' => 'user_gstore.php', 'icon' => 'gstore_gstore16.gif', 'title' => 5555007);
}

// SET PROFILE MENU VARS
if( $owner->level_info['level_gstore_allow'] && $page=="profile" )
{

// INIT VARS
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

  // START gstore
  $entries_per_page = (int)6;
  if($entries_per_page <= 0) { $entries_per_page = 6; }
  $gstore = new se_gstore($owner->user_info['user_id']);
  
    // GET PRIVACY LEVEL AND SET WHERE
  $privacy_max = $owner->user_privacy_max($user);
  $where = "(gstore_privacy & $privacy_max)";
  
  // GET TOTAL ENTRIES, MAKE ENTRY PAGES, GET ENTRY ARRAY
$total_gstores = $gstore->gstore_total($where);
$page_vars = make_page($total_gstores, $entries_per_page, $p);
$gstores = $gstore->gstore_list($page_vars[0], $entries_per_page, "gstore_date DESC", $where);

	
			
			  // ASSIGN ENTRIES SMARY VARIABLE
			  $smarty->assign_by_ref('gstores', $gstores);
			  $smarty->assign('total_gstores', $total_gstores);
			  $smarty->assign('p', $page_vars[1]);
			  $smarty->assign('maxpage', $page_vars[2]);
			  $smarty->assign('p_start', $page_vars[0]+1);
			  $smarty->assign('p_end', $page_vars[0]+count($gstores));
  
  //print_r($gstores);
  
  // SET PROFILE MENU VARS
  if( $total_gstores )
  {
    $plugin_vars['menu_profile_tab'] = array('file'=> 'profile_gstore.tpl', 'title' => 5555159);
    $plugin_vars['menu_profile_side'] = "";
  }
}



// SET HOOKS
SE_Hook::register("se_search_do", 'search_gstore');

SE_Hook::register("se_user_delete", 'deleteuser_gstore');

SE_Hook::register("se_site_statistics", 'site_statistics_gstore');

?>