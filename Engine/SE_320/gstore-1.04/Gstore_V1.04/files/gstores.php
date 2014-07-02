<?php



$page = "gstores";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_gstore'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if( !$owner->user_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// ENSURE gstoreS ARE ENABLED FOR THIS USER
if( !$owner->level_info['level_gstore_allow'] )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}


// INIT VARS
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }


// SET PRIVACY LEVEL AND WHERE CLAUSE
$privacy_max = $owner->user_privacy_max($user);
$where = "(gstore_privacy & $privacy_max)";


// CREATE gstore OBJECT
$entries_per_page = (int)$owner->level_info['level_gstore_entries'];
if($entries_per_page <= 0) { $entries_per_page = 10; }
$gstore = new se_gstore($owner->user_info[user_id]);


// GET TOTAL ENTRIES, MAKE ENTRY PAGES, GET ENTRY ARRAY
$total_gstores = $gstore->gstore_total($where);
$page_vars = make_page($total_gstores, $entries_per_page, $p);
$gstores = $gstore->gstore_list($page_vars[0], $entries_per_page, "gstore_date DESC", $where);


// SET SEO STUFF
$global_page_title = array(5555143, $owner->user_displayname);
$global_page_description = array(5555143, $owner->user_displayname);


// ASSIGN VARIABLES AND DISPLAY gstore PAGE
$smarty->assign('gstore_price', $gstore_price);
$smarty->assign('gstores', $gstores);
$smarty->assign('total_gstores', $total_gstores);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($gstores));
include "footer.php";
?>