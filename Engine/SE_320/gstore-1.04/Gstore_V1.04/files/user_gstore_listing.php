<?php



$page = "user_gstore_listing";
include "header.php";


$task           = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])           ? $_GET['task']           : NULL ) );
$gstore_id  = ( !empty($_POST['gstore_id']) ? $_POST['gstore_id'] : ( !empty($_GET['gstore_id'])  ? $_GET['gstore_id']  : NULL ) );


// ENSURE gstoreS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_gstore_allow'] )
{
  header("Location: user_home.php");
  exit();
}


// GET PRIVACY SETTINGS
$level_gstore_privacy = unserialize($user->level_info['level_gstore_privacy']);
rsort($level_gstore_privacy);
$level_gstore_comments = unserialize($user->level_info['level_gstore_comments']);
rsort($level_gstore_comments);


// INITIALIZE VARIABLES
$is_error = FALSE;

$gstore = new se_gstore($user->user_info['user_id'], $gstore_id);

if( $gstore->gstore_exists && $user->user_info['user_id']!=$gstore->gstore_info['gstore_user_id'] )
{
  header('user_home.php');
  exit();
}

if( !$gstore->gstore_exists ) $gstore->gstore_info = array
( 
  'gstore_title'                => '',
  'gstore_price'                => '',
  'gstore_stock'                => '',
  'band_a_charge'                => '',
  'band_b_charge'                => '',
  'band_c_charge'                => '',
  'band_d_charge'                => '',
  'apply_shipping_charges'                => '',
  'gstore_body'                 => '',
  'gstore_gstorecat_id'     => 0,
  'gstore_gstoresubcat_id'  => 0,
  'gstore_search'               => 1,
  'gstore_privacy'              => $level_gstore_privacy[0],
  'gstore_comments'             => $level_gstore_comments[0]
);


// BEGIN POST ENTRY TASK
if( $task=="dosave" )
{
  $gstore->gstore_info['gstore_id']                   = $_POST['gstore_id'];
  $gstore->gstore_info['gstore_title']                = censor($_POST['gstore_title']);
  $gstore->gstore_info['gstore_price']                = censor($_POST['gstore_price']);
  $gstore->gstore_info['gstore_stock']                = censor($_POST['gstore_stock']);
  $gstore->gstore_info['band_a_charge']                = censor($_POST['band_a_charge']);
  $gstore->gstore_info['band_b_charge']                = censor($_POST['band_b_charge']);
  $gstore->gstore_info['band_c_charge']                = censor($_POST['band_c_charge']);
  $gstore->gstore_info['band_d_charge']                = censor($_POST['band_d_charge']);
  $gstore->gstore_info['apply_shipping_charges']       = censor($_POST['apply_shipping_charges']);
  $gstore->gstore_info['gstore_body']                 = censor(str_replace("\r\n", "<br />", $_POST['gstore_body']));
  $gstore->gstore_info['gstore_search']               = $_POST['gstore_search'];
  $gstore->gstore_info['gstore_privacy']              = $_POST['gstore_privacy'];
  $gstore->gstore_info['gstore_comments']             = $_POST['gstore_comments'];
  $gstore->gstore_info['gstore_gstorecat_id']     = $_POST['gstore_gstorecat_id'];
  $gstore->gstore_info['gstore_gstoresubcat_id']  = $_POST['gstore_gstoresubcat_id'];
  
  // GET FIELDS
  $field = new se_field("gstore");
  $field->cat_list(1, 0, 0, "gstorecat_id='{$gstore->gstore_info[gstore_gstorecat_id]}'", "", "");
  $selected_fields = $field->fields_all;
  $is_error = $field->is_error;
  
  if( !$gstore->gstore_info['gstore_id'] )
    $gstore->gstore_info['gstore_id'] = NULL;
	
  
  // CHECK TO MAKE SURE TITLE HAS BEEN ENTERED
  if( !trim($gstore->gstore_info['gstore_title']) )
    $is_error = 5555100;
	
  // CHECK TO MAKE SURE PRICE HAS BEEN ENTERED
  if( !trim($gstore->gstore_info['gstore_price']) )
    $is_error = 55550100;
	
	  // CHECK TO MAKE SURE stock HAS BEEN ENTERED
  if( !trim($gstore->gstore_info['gstore_stock']) )
    $is_error = 5555163;
	
  // CHECK TO MAKE SURE PRICE HAS BEEN ENTERED CORRECTLY

  if(!preg_match('#^(\d)*\.?\d*$#', $gstore->gstore_info['gstore_price']) )  
    $is_error = 55550100;
	
	
	 // CHECK TO MAKE SURE  shipping PRICE HAS BEEN ENTERED CORRECTLY
  if(!preg_match('#^(\d)*\.?\d*$#', $gstore->gstore_info['band_a_charge']) )
    $is_error = 55550100;
	
	
		 // CHECK TO MAKE SURE  shipping PRICE HAS BEEN ENTERED CORRECTLY
  if(!preg_match('#^(\d)*\.?\d*$#', $gstore->gstore_info['band_b_charge']) )
    $is_error = 55550100;
	
		 // CHECK TO MAKE SURE  shipping PRICE HAS BEEN ENTERED CORRECTLY
  if(!preg_match('#^(\d)*\.?\d*$#', $gstore->gstore_info['band_c_charge']) )
    $is_error = 55550100;
	
		 // CHECK TO MAKE SURE  shipping PRICE HAS BEEN ENTERED CORRECTLY
  if(!preg_match('#^(\d)*\.?\d*$#', $gstore->gstore_info['band_d_charge']) )
    $is_error = 55550100;
	
	  // CHECK TO MAKE SURE stock HAS BEEN ENTERED CORRECTLY
  if( !preg_match('#^[0-9]+$#', $gstore->gstore_info['gstore_stock']) )
    $is_error = 5555164;

  // CHECK TO MAKE SURE CATEGORY HAS BEEN SELECTED
  if( !$gstore->gstore_info['gstore_gstorecat_id'] )
    $is_error = 5555101;
    
  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if( !in_array($gstore->gstore_info['gstore_privacy'] , $level_gstore_privacy ) )
    $gstore->gstore_info['gstore_privacy']  = $level_gstore_privacy[0] ;
  if( !in_array($gstore->gstore_info['gstore_comments'], $level_gstore_comments) )
    $gstore->gstore_info['gstore_comments'] = $level_gstore_comments[0];
  
  // CHECK THAT SEARCH IS NOT BLANK
  if( !$user->level_info['level_gstore_search'] )
    $gstore->gstore_info['gstore_search'] = 1;
  
  
  // IF NO ERROR, SAVE GROUP
  if( !$is_error )
  {
    // SET gstore CATEGORY ID
    if( $gstore->gstore_info['gstore_gstoresubcat_id'] && $gstore->gstore_info['gstore_gstoresubcat_id'] )
      $gstore->gstore_info['gstore_gstorecat_id'] = $gstore->gstore_info['gstore_gstoresubcat_id'];
    
    $gstore->gstore_info['gstore_id'] = $gstore->gstore_post($gstore->gstore_info['gstore_id'],
      $gstore->gstore_info['gstore_title'],
	  $gstore->gstore_info['gstore_price'],
	   $gstore->gstore_info['gstore_stock'],
	   $gstore->gstore_info['band_a_charge'],
	   $gstore->gstore_info['band_b_charge'],
	   $gstore->gstore_info['band_c_charge'],
	   $gstore->gstore_info['band_d_charge'],
	   $gstore->gstore_info['apply_shipping_charges'],
      $gstore->gstore_info['gstore_body'],
      $gstore->gstore_info['gstore_gstorecat_id'],
      $gstore->gstore_info['gstore_search'],
      $gstore->gstore_info['gstore_privacy'],
      $gstore->gstore_info['gstore_comments'],
      $field->field_query
    );
    
    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $user->user_lastupdate();
    
    // INSERT ACTION
    if( $gstore_id )
    {
      $gstore_title_short = $gstore->gstore_info['gstore_title'];
      if( strlen($gstore_title_short) > 100 ) $gstore_title_short = substr($gstore_title_short, 0, 97); $gstore_title_short .= "...";
      $actions->actions_add(
        $user,
        "editgstore",
        array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $gstore->gstore_info['gstore_id'],
          $gstore_title_short
        )
      );
	  
    }
	else
	    {
      $gstore_title_short = $gstore->gstore_info['gstore_title'];
      if( strlen($gstore_title_short) > 100 ) $gstore_title_short = substr($gstore_title_short, 0, 97); $gstore_title_short .= "...";
      $actions->actions_add(
        $user,
        "postgstore",
        array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $gstore->gstore_info['gstore_id'],
          $gstore_title_short
        )
      );
	  
    }
    
    header($gstore_id ? "Location: user_gstore.php" : "Location: user_gstore_media.php?gstore_id={$gstore->gstore_info['gstore_id']}&justadded=1" );
    exit();
  }
}








// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_gstore_privacy);$c++) {
  if(user_privacy_levels($level_gstore_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_gstore_privacy[$c]));
    $privacy_options[$level_gstore_privacy[$c]] = user_privacy_levels($level_gstore_privacy[$c]);
  }
}

for($c=0;$c<count($level_gstore_comments);$c++) {
  if(user_privacy_levels($level_gstore_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_gstore_comments[$c]));
    $comment_options[$level_gstore_comments[$c]] = user_privacy_levels($level_gstore_comments[$c]);
  }
}


// GET FIELDS
$field = new se_field("gstore", $gstore->gstorevalue_info);
$field->cat_list(0, 0, 0, "", "", "");
$cat_array = $field->cats;
if( $is_error && $gstore_info['gstore_gstorecat_id'] )
{
  $selected_cat_array = array_filter($cat_array, create_function('$a', 'if($a["cat_id"] == "'.$gstore_info['gstore_gstorecat_id'].'") { return $a; }'));
  foreach( $selected_cat_array as $key=>$val )
  {
    $cat_array[$key]['fields'] = $selected_fields;
  }
}


// GET SUBCAT IF NECESSARY
$thiscat = $database->database_fetch_assoc($database->database_query("SELECT gstorecat_id, gstorecat_dependency FROM se_gstorecats WHERE gstorecat_id='{$gstore->gstore_info[gstore_gstorecat_id]}'"));
if( !$thiscat['gstorecat_dependency'] )
{
  $gstore->gstore_info['gstore_gstoresubcat_id'] = 0;
}
else
{
  $gstore->gstore_info['gstore_gstoresubcat_id'] = $gstore->gstore_info['gstore_gstorecat_id'];
  $gstore->gstore_info['gstore_gstorecat_id'] = $thiscat['gstorecat_dependency'];
}


// REMOVE BREAKS
$gstore->gstore_info['gstore_body'] = str_replace("<br />", "\r\n", $gstore->gstore_info['gstore_body']);



// ASSIGN VARIABLES AND SHOW ADD GROUPS PAGE
$smarty->assign('is_error', $is_error);

$smarty->assign_by_ref('gstore', $gstore);
$smarty->assign_by_ref('cats', $cat_array);
$smarty->assign('gstore_id', $gstore_id);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
include "footer.php";
?>