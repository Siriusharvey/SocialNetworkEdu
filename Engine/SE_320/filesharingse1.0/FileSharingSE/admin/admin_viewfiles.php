<?php

/* $Id: admin_viewuseruploads.php 7 2009-01-11 06:01:49Z john $ */

$page = "admin_viewfiles";
include "admin_header.php";
include "../include/class_uploads.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) 
{ $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['userupload_id'])) { $userupload_id = $_POST['userupload_id']; } elseif(isset($_GET['userupload_id'])) { $userupload_id = $_GET['userupload_id']; } else { $userupload_id = 0; }
if(isset($_POST['deletefile'])) { $deletefile = $_POST['deletefile']; } elseif(isset($_GET['deletefile'])) { $deletefile = $_GET['deletefile']; } else { $search = NULL; }



//print_r($_POST);

// CREATE userupload OBJECT
$files_per_page = 100;
$userupload = new se_fileuploads();


// DELETE SINGLE ENTRY
if($task == "deletefile") {
  if($database->database_num_rows($database->database_query("SELECT userupload_id FROM se_fileuploads WHERE userupload_id='$userupload_id'")) == 1) {
	$userupload->userupload_delete($userupload_id);
  }
}




// SET userupload ENTRY SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // userupload_ID
$t = "t";    // userupload_TITLE
$u = "u";    // OWNER OF ENTRY
$d = "d";    // DATE OF ENTRY

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_fileuploads.userupload_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_fileuploads.userupload_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_fileuploads.userupload_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_fileuploads.userupload_title DESC";
  $t = "t";
} elseif($s == "u") {
  $sort = "se_users.user_username";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "se_users.user_username DESC";
  $u = "u";
} elseif($s == "d") {
  $sort = "se_fileuploads.userupload_time";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "se_fileuploads.userupload_time DESC";
  $d = "d";
} else {
  $sort = "se_fileuploads.userupload_id DESC";
  $i = "i";
}




// ADD CRITERIA FOR FILTER
$where = "";
if($f_owner != "") { $where .= "se_users.user_username LIKE '%$f_owner%'"; }
if($f_owner != "" & $f_title != "") { $where .= " AND"; }
if($f_title != "") { $where .= " se_fileuploads.userupload_title LIKE '%$f_title%'"; }
if($where != "") { $where = "(".$where.")"; }



$start = ($p - 1) * $files_per_page;
// Delet selected files
if($task == "delete") {
	 $userupload->file_delete_selected($start, $files_per_page, $sort, $where); 
}



// GET TOTAL ENTRIES/MAKE ENTRY PAGES/GET ENTRY ARRAY
$total_useruploads = $userupload->userupload_total($where);

$page_vars = make_page($total_useruploads, $files_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

$useruploads = $userupload->userupload_list($page_vars[0], $files_per_page, $sort, $where);
//print_r($useruploads);

// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('total_files', $total_useruploads);
$smarty->assign_by_ref('files', $useruploads);

$smarty->assign('pages', $page_array);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_owner', $f_owner);
$smarty->assign('i', $i);
$smarty->assign('t', $t);
$smarty->assign('u', $u);
$smarty->assign('d', $d);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);

include "admin_footer.php";
?>