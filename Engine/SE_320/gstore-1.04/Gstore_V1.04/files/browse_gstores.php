<?php



$page = "browse_gstores";
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


// PARSE GET/POST
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "gstore_datecreated DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }
if(isset($_POST['gstorecat_id'])) { $gstorecat_id = $_POST['gstorecat_id']; } elseif(isset($_GET['gstorecat_id'])) { $gstorecat_id = $_GET['gstorecat_id']; } else { $gstorecat_id = 0; }
if(isset($_POST['gstore_search'])) { $gstore_search = $_POST['gstore_search']; } elseif(isset($_GET['gstore_search'])) { $gstore_search = $_GET['gstore_search']; } else { $gstore_search = NULL; }

// ENSURE SORT/VIEW ARE VALID
if($s != "gstore_date DESC" && $s != "gstore_dateupdated DESC" && $s != "gstore_views DESC" && $s != "total_comments DESC") { $s = "gstore_date DESC"; }
if($v != "0" && $v != "1") { $v = 0; }


// SET WHERE CLAUSE
$where = "CASE
	    WHEN se_gstores.gstore_user_id='{$user->user_info['user_id']}'
	      THEN TRUE
	    WHEN ((se_gstores.gstore_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
	      THEN TRUE
	    WHEN ((se_gstores.gstore_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
	      THEN TRUE
	    WHEN ((se_gstores.gstore_privacy & @SE_PRIVACY_FRIEND) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_gstores.gstore_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
	      THEN TRUE
	    WHEN ((se_gstores.gstore_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_users WHERE user_id=se_gstores.gstore_user_id AND user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
	      THEN TRUE
	    WHEN ((se_gstores.gstore_privacy & @SE_PRIVACY_FRIEND2) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_gstores.gstore_user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
	      THEN TRUE
	    ELSE FALSE
	END";



// ONLY MY FRIENDS' CLASSIFIEDS
if( $v=="1" && $user->user_exists )
{
  // SET WHERE CLAUSE
  $where .= " AND (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$user->user_info['user_id']}' AND friend_user_id2=se_gstores.gstore_user_id AND friend_status=1)";
}



// SPECIFIC CLASSIFIED CATEGORY
if( $gstorecat_id )
{
  $sql = "SELECT gstorecat_id, gstorecat_title, gstorecat_dependency FROM se_gstorecats WHERE gstorecat_id='{$gstorecat_id}' LIMIT 1";
  $resource = $database->database_query($sql);
  
  if( $database->database_num_rows($resource) )
  {
    $gstorecat = $database->database_fetch_assoc($resource);
    
    if( !$gstorecat['gstorecat_dependency'] )
    {
      $cat_ids[] = $gstorecat['gstorecat_id'];
      $depcats = $database->database_query("SELECT gstorecat_id FROM se_gstorecats WHERE gstorecat_id='{$gstorecat['gstorecat_id']}' OR gstorecat_dependency='{$gstorecat['gstorecat_id']}'");
      while($depcat_info = $database->database_fetch_assoc($depcats)) { $cat_ids[] = $depcat_info['gstorecat_id']; }
      $where .= " AND se_gstores.gstore_gstorecat_id IN('".implode("', '", $cat_ids)."')";
    }
    else
    {
      $where .= " AND se_gstores.gstore_gstorecat_id='{$gstorecat['gstorecat_id']}'";
      $gstoresubcat = $gstorecat;
      $gstorecat = $database->database_fetch_assoc($database->database_query("SELECT gstorecat_id, gstorecat_title FROM se_gstorecats WHERE gstorecat_id='{$gstorecat['gstorecat_dependency']}'"));
    }
  }
}


// GET CATS
$field = new se_field("gstore");
$field->cat_list(0, 0, 0, "", "", "");
$cat_menu_array = $field->cats;

//$field->cat_list(0, 0, 1, "gstorecat_id='{$gstorecat['gstorecat_id']}'", "", "");
$field->field_list(0, 0, 1, "gstorefield_gstorecat_id='{$gstorecat['gstorecat_id']}' && gstorefield_search<>'0'");


// BEGIN CONSTRUCTING SEARCH QUERY
//echo $field->field_query;
if( $field->field_query )
  $where .= " && ".$field->field_query;

if( !empty($gstore_search) )
{
  $where .= " && MATCH(gstore_title, gstore_body) AGAINST ('{$gstore_search}' IN BOOLEAN MODE) ";
}


// CREATE CLASSIFIED OBJECT, GET TOTAL CLASSIFIEDS, MAKE ENTRY PAGES, GET CLASSIFIED ARRAY
$gstore = new se_gstore();

$total_gstores = $gstore->gstore_total($where, TRUE);
$gstores_per_page = 8;
$page_vars = make_page($total_gstores, $gstores_per_page, $p);

$gstore_array = $gstore->gstore_list($page_vars[0], $gstores_per_page, $s, $where, TRUE);



// GET MOST INTEREST ITEMS, ITEMS WITH MOST VIEWS
$mi = $_POST['mi'];
$mi = "gstore_views DESC";
$most_interest = $gstore->gstore_total($where, TRUE);
$most_interest_per_page = 5;
$page_vars = make_page($most_interest, $most_interest_per_page, $p);

$most_interest_array = $gstore->gstore_list($page_vars[0], $most_interest_per_page, $mi, $where, TRUE);

// GET BEST SELLERS
$bs = "item_sales DESC";
$best_sellers = $gstore->gstore_total($where, TRUE);
$best_sellers_per_page = 5;
$page_vars = make_page($best_sellers, $best_sellers_per_page, $p);

$best_sellers_array = $gstore->gstore_list($page_vars[0], $best_sellers_per_page, $bs, $where, TRUE);

 $smarty->assign('number', $number);
$smarty->assign('best_sellers', $best_sellers_array);





// ASSIGN SMARTY VARIABLES AND DISPLAY CLASSIFIEDS PAGE
$smarty->assign('gstorecat_id', $gstorecat_id);
$smarty->assign('gstorecat', $gstorecat);
$smarty->assign('gstoresubcat', $gstoresubcat);
$smarty->assign('gstore_search', $gstore_search);

$smarty->assign_by_ref('cats_menu', $cat_menu_array);
$smarty->assign_by_ref('cats', $field->cats);
$smarty->assign_by_ref('fields', $field->fields);
$smarty->assign_by_ref('url_string', $field->url_string);

$smarty->assign('most_interest', $most_interest_array);
$smarty->assign('gstores', $gstore_array);
$smarty->assign('total_gstores', $total_gstores);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($gstore_array));
$smarty->assign('mi', $mi);
$smarty->assign('s', $s);
$smarty->assign('v', $v);
include "footer.php";
?>