<?php



$page = "user_gstore";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }

// ENSURE gstoreS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_gstore_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// SET CLAUSES
$sort = "gstore_date DESC";
if( trim($search) )
  $where = "(gstore_title LIKE '%$search%' OR gstore_body LIKE '%$search%')";
else
  $where = NULL;

// CREATE gstore OBJECT
$entries_per_page = 10;
$gstore = new se_gstore($user->user_info['user_id']);

// DELETE NECESSARY ENTRIES
//$start = ($p - 1) * $entries_per_page;
//if($task == "delete") { $gstore->gstore_delete($start, $entries_per_page, $sort, $where); }

// GET TOTAL ENTRIES
$total_gstores = $gstore->gstore_total($where);

// MAKE ENTRY PAGES
$page_vars = make_page($total_gstores, $entries_per_page, $p);

// GET ENTRY ARRAY
$gstores = $gstore->gstore_list($page_vars[0], $entries_per_page, $sort, $where);

// UPDATE STOCK
 $stock = $_POST['stock'];
 $id = $_POST['id'];
 if( $id )
 {
 $sql = "UPDATE se_gstores SET gstore_stock='$stock' WHERE gstore_id='$id' LIMIT 1";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
  
header($gstore_id ? "Location: user_gstore.php" : "Location: user_gstore_media.php?gstore_id={$gstore->gstore_info['gstore_id']}&justadded=1" );
    exit();
}

// GET THIS USER'S gstore SETTINGS
$sql = "SELECT paypal_email FROM se_gstore_settings WHERE gstore_settings_user_id='{$user->user_info['user_id']}' LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

if( $database->database_num_rows($resource) )
{ 
  $paypal_settings = $database->database_fetch_assoc($resource); 
}

// ASSIGN SMARTY VARIABLES AND DISPLAY gstore STYLE PAGE
$smarty->assign('paypal_email', $paypal_settings['paypal_email']);
$smarty->assign('result', $result);



// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('search', $search);
$smarty->assign('gstores', $gstores);
$smarty->assign('gstore_photo', $gstore_photo);
$smarty->assign('total_gstores', $total_gstores);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($gstores));
include "footer.php";
?>