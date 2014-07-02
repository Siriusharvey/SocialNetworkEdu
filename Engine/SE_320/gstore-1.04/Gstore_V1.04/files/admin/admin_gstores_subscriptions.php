<?php



$page = "admin_gstores_subscriptions";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['gstore_id'])) { $gstore_id = $_POST['gstore_id']; } elseif(isset($_GET['gstore_id'])) { $gstore_id = $_GET['gstore_id']; } else { $gstore_id = 0; }
if(isset($_POST['delete_gstores'])) { $delete_gstores = $_POST['delete_gstores']; } elseif(isset($_GET['delete_gstores'])) { $delete_gstores = $_GET['delete_gstores']; } else { $search = NULL; }

// VALIDATE gstore ENTRY ID OR SET TASK TO MAIN
if($task == "confirm" OR $task == "deleteentry") {
  if($database->database_num_rows($database->database_query("SELECT gstore_id FROM se_gstores WHERE gstore_id='$gstore_id'")) != 1) { $task = "main"; }
}


// CREATE gstore OBJECT
$entries_per_page = 100;
$gstore = new se_gstore();


// DELETE SINGLE ENTRY
if($task == "deleteentry") {
  if($database->database_num_rows($database->database_query("SELECT gstore_id FROM se_gstores WHERE gstore_id='$gstore_id'")) == 1) {
    $gstore->gstore_delete($gstore_id);
  }
}







// SET gstore ENTRY SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // gstore_ID
$t = "t";    // gstore_TITLE
$o = "o";    // OWNER OF ENTRY
$v = "v";    // VIEWS OF ENTRY
$d = "d";    // DATE OF ENTRY

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_gstores.gstore_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_gstores.gstore_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_gstores.gstore_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_gstores.gstore_title DESC";
  $t = "t";
} elseif($s == "o") {
  $sort = "se_users.user_username";
  $o = "od";
} elseif($s == "od") {
  $sort = "se_users.user_username DESC";
  $o = "o";
} elseif($s == "v") {
  $sort = "se_gstores.gstore_views";
  $v = "vd";
} elseif($s == "vd") {
  $sort = "se_gstores.gstore_views DESC";
  $v = "v";
} elseif($s == "d") {
  $sort = "se_gstores.gstore_date";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "se_gstores.gstore_date DESC";
  $d = "d";
} else {
  $sort = "se_gstores.gstore_id DESC";
  $i = "i";
}




// ADD CRITERIA FOR FILTER
$where = "";
if($f_owner != "") { $where .= "se_users.user_username LIKE '%$f_owner%'"; }
if($f_owner != "" & $f_title != "") { $where .= " AND"; }
if($f_title != "") { $where .= " se_gstores.gstore_title LIKE '%$f_title%'"; }
if($where != "") { $where = "(".$where.")"; }


// DELETE NECESSARY ENTRIES
if( $task=="deleteentries" && !empty($delete_gstores) )
{
  $gstore->gstore_delete($delete_gstores);
}


// GET TOTAL ENTRIES/MAKE ENTRY PAGES/GET ENTRY ARRAY
$total_gstores = $gstore->gstore_total($where);

$page_vars = make_page($total_gstores, $entries_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

$gstores = $gstore->gstore_list($page_vars[0], $entries_per_page, $sort, $where);


// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('total_gstores', $total_gstores);
$smarty->assign_by_ref('gstores', $gstores);

$smarty->assign('pages', $page_array);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_owner', $f_owner);
$smarty->assign('i', $i);
$smarty->assign('t', $t);
$smarty->assign('o', $o);
$smarty->assign('v', $v);
$smarty->assign('d', $d);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);

include "admin_footer.php";
?>