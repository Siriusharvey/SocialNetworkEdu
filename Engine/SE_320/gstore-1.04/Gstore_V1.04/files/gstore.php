<?php



$page = "gstore";
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
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_GET['gstore_id'])) { $gstore_id = $_GET['gstore_id']; } elseif(isset($_POST['gstore_id'])) { $gstore_id = $_POST['gstore_id']; } else { $gstore_id = 0; }


// DISPLAY ERROR PAGE IF NO OWNER
$gstore = new se_gstore($user->user_info['user_id'], $gstore_id);
if( !$gstore->gstore_exists || !$owner->user_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// GET MOST INTEREST ITEMS, ITEMS WITH MOST VIEWS
$mi = $_POST['mi'];
$mi = "MATCH(gstore_title, gstore_body) AGAINST ('{$gstore_search}' IN BOOLEAN MODE) ";
$most_interest = $gstore->gstore_total($where, TRUE);
$most_interest_per_page = 5;
$page_vars = make_page($most_interest, $most_interest_per_page, $p);

$most_interest_array = $gstore->gstore_list($page_vars[0], $most_interest_per_page, $mi, $where, TRUE);



// GET PRIVACY LEVEL
$privacy_max = $owner->user_privacy_max($user);
$allowed_to_view    = (bool) ($privacy_max & $gstore->gstore_info['gstore_privacy' ]);
$allowed_to_comment = (bool) ($privacy_max & $gstore->gstore_info['gstore_comments']);


// UPDATE gstore VIEWS IF GROUP VISIBLE
if( $allowed_to_view )
{
  $gstore->gstore_info['gstore_views']++;
  $sql = "UPDATE se_gstores SET gstore_views='{$gstore->gstore_info['gstore_views']}' WHERE gstore_id='{$gstore->gstore_info['gstore_id']}' LIMIT 1";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
}


// GET gstore CATEGORY
/*
$group_category = "";
$group_category_query = $database->database_query("SELECT groupcat_id, groupcat_title FROM se_groupcats WHERE groupcat_id='".$group->group_info[group_groupcat_id]."' LIMIT 1");
if($database->database_num_rows($group_category_query) == 1) {
  $group_category_info = $database->database_fetch_assoc($group_category_query);
  $group_category = $group_category_info[groupcat_title];
}
*/


// GET gstore COMMENTS
$comment = new se_comment('gstore', 'gstore_id', $gstore->gstore_info['gstore_id']);
$total_comments = $comment->comment_total();
$comments = $comment->comment_list(0, 10);


// MAKE SURE TITLE IS NOT EMPTY, CONVERT BODY HTML CHARACTERS BACK
if( !$gstore->gstore_info['gstore_title'] )
  $gstore->gstore_info['gstore_title'] = 'Untitled';

$gstore->gstore_info['gstore_body'] = str_replace("\r\n", "", html_entity_decode($gstore->gstore_info['gstore_body']));




// GET gstore ALBUM INFO AND MEDIA ARRAY
$sql = "SELECT * FROM se_gstorealbums WHERE gstorealbum_gstore_id='{$gstore->gstore_info['gstore_id']}' LIMIT 1";
$resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);

if( $database->database_num_rows($resource) )
{
  $gstorealbum_info = $database->database_fetch_assoc($resource);
  
  $file_array = $gstore->gstore_media_list(0, 10, "gstoremedia_id ASC", "(gstoremedia_gstorealbum_id='{$gstorealbum_info['gstorealbum_id']}')", TRUE);
  $total_files = $gstore->gstore_media_total($gstorealbum_info['gstorealbum_id']);
}


// GET SUBCAT IF NECESSARY
$sql = "SELECT gstorecat_id, gstorecat_dependency FROM se_gstorecats WHERE gstorecat_id='{$gstore->gstore_info['gstore_gstorecat_id']}' LIMIT 1";
$resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
if( $database->database_num_rows($resource) )
  $thiscat = $database->database_fetch_assoc($resource);

if( !$thiscat || !$thiscat['gstorecat_dependency'] )
{
  $gstore->gstore_info['gstore_gstoresubcat_id'] = 0;
}
else
{
  $gstore->gstore_info['gstore_gstoresubcat_id']  = $gstore->gstore_info['gstore_gstorecat_id'];
  $gstore->gstore_info['gstore_gstorecat_id']     = $thiscat['gstorecat_dependency'];
}


// GET FIELDS
$gstorecat_info = $database->database_fetch_assoc($database->database_query("SELECT t1.gstorecat_id AS subcat_id, t1.gstorecat_title AS subcat_title, t1.gstorecat_dependency AS subcat_dependency, t2.gstorecat_id AS cat_id, t2.gstorecat_title AS cat_title FROM se_gstorecats AS t1 LEFT JOIN se_gstorecats AS t2 ON t1.gstorecat_dependency=t2.gstorecat_id WHERE t1.gstorecat_id='{$gstore->gstore_info['gstore_gstorecat_id']}'"));
if( !$gstorecat_info['subcat_dependency'] )
{
  $cat_where = "gstorecat_id='{$gstore->gstore_info['gstore_gstorecat_id']}'";
}
else
{
  $cat_where = "gstorecat_id='{$gstorecat_info['subcat_dependency']}'";
}
$field = new se_field("gstore", $gstore->gstorevalue_info);
$field->cat_list(0, 1, 0, $cat_where, "gstorecat_id='0'", "");


// DELETE NOTIFICATIONS
if( $user->user_info['user_id']==$owner->user_info['user_id'] )
{
  $database->database_query("
    DELETE FROM
      se_notifys
    USING
      se_notifys
    LEFT JOIN
      se_notifytypes
      ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
    WHERE
      se_notifys.notify_user_id='{$owner->user_info[user_id]}' AND
      se_notifytypes.notifytype_name='gstorecomment' AND
      notify_object_id='{$gstore->gstore_info['gstore_id']}'
  ");
}


// SET SEO STUFF
$global_page_content = $gstore->gstore_info['gstore_title'];
$global_page_content = cleanHTML(str_replace('>', '> ', $global_page_content), NULL);
if( strlen($global_page_content)>255 ) $global_page_content = substr($global_page_content, 0, 251).'...';
$global_page_content = addslashes(trim(preg_replace('/\s+/', ' ',$global_page_content)));

$global_page_title = array(
  5555144,
  $owner->user_displayname,
  $global_page_content
);

$global_page_content = $gstore->gstore_info['gstore_body'];
$global_page_content = cleanHTML(str_replace('>', '> ', $global_page_content), NULL);
if( strlen($global_page_content)>255 ) $global_page_content = substr($global_page_content, 0, 251).'...';
$global_page_content = addslashes(trim(preg_replace('/\s+/', ' ',$global_page_content)));

$global_page_description = array(
  5555144,
  $owner->user_displayname,
  $global_page_content
);









// GET THIS USER'S gstore SETTINGS
$sql = "SELECT paypal_email FROM se_gstore_settings WHERE gstore_settings_user_id='{$owner->user_info['user_id']}' LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

if( $database->database_num_rows($resource) )
{ 
  $paypal_settings = $database->database_fetch_assoc($resource); 
}
else
{
  $sql = "INSERT INTO se_gstore_settings (gstore_settings_user_id, paypal_email) VALUES ('{$user->user_info['user_id']}', '')";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  $paypal_settings = array
  (
    'gstore_settings_id'      => $database->database_insert_id(),
    'gstore_settings_user_id' => $user->user_info['user_id'],
    'paypal_email'     => ''
  );
}


// ASSIGN USER SETTINGS
$user->user_settings();


// ASSIGN SMARTY VARIABLES AND DISPLAY gstore STYLE PAGE
$smarty->assign('paypal_email', $paypal_settings['paypal_email']);
$smarty->assign('result', $result);





// GET SELLER TOTAL SALES
$gstore_owner_id = $owner->user_info['user_id'];
$query = "SELECT user_sales FROM se_users WHERE user_id='$gstore_owner_id' LIMIT 1";
$result = mysql_query($query);

while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
    $seller_sales = $row['user_sales'];
} 
$smarty->assign_by_ref('seller_sales', $seller_sales);











// GET OTHER ITEMS BY THIS SELLER
$otheritems = new se_gstore($owner->user_info[user_id]);
$mi = $_POST['mi'];
$mi = "gstore_views DESC";
$other_items = $otheritems->gstore_total($where, TRUE);
$other_items_per_page = 5;
$page_vars = make_page($other_items, $other_items_per_page, $p);

$other_items_array = $otheritems->gstore_list($page_vars[0], $other_items_per_page, $mi, $where, TRUE);
$smarty->assign('other_items', $other_items_array);
$smarty->assign('mi', $mi);


// smarty assign so can pass to paypal bounce page
$stockinhand = $gstore->gstore_info['gstore_stock'];
$smarty->assign_by_ref('stockinhand', $stockinhand);
$item_title = $gstore->gstore_info['gstore_title'];
$smarty->assign_by_ref('item_title', $item_title);
$price = $gstore->gstore_info['gstore_price'];
$smarty->assign_by_ref('price', $price);
$item_sales = $gstore->gstore_info['item_sales'];
$smarty->assign_by_ref('item_sales', $item_sales);



// ASSIGN VARIABLES AND DISPLAY gstore PAGE

$smarty->assign_by_ref('gstore', $gstore);
$smarty->assign_by_ref('gstore_id', $gstore_id);
$smarty->assign_by_ref('cats', $field->cats);

$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);

$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);

$smarty->assign('most_interest', $most_interest_array);
$smarty->assign('mi', $mi);
include "footer.php";
?>