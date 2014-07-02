<?php
$page = "user_sms";
include "admin_header.php";


if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

if($_POST[submit1]>0)
{
	$id= $_POST[submit1];
	$ssms_credits= $_POST[ssms_credits];
	$database->database_query("update se_user_smssetting set ssms_credits='$ssms_credits'  WHERE user_id='$id'");
	$msg="Updated Successfully";
}
if($_POST[Search]=="Search")
{
	$search_user= $_POST[search_user];
	$user_query="select * from  se_users where user_username LIKE '%$search_user%'";
}
else
{
	$user_query="select * from  se_users";
}

// GET TOTAL USERS
$total_users = $database->database_num_rows($database->database_query($user_query));

// MAKE USER PAGES
$users_per_page = 2;
$page_vars = make_page($total_users, $users_per_page, $p);

$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

$user_query .= " LIMIT $page_vars[0], $users_per_page";

$sel_qry=$database->database_query($user_query);

$i=0;
while($views = $database->database_fetch_assoc($sel_qry))
{
	$view_arr[$i][id]=$views[user_id];
	$view_arr[$i][username]=$views[user_username];
	$view_arr[$i][email]=$views[user_email];
	
	$sms_cridit = $database->database_query("select ssms_credits from  se_user_smssetting WHERE user_id='".$views[user_id]."'");
	$viewsms_cridit = $database->database_fetch_assoc($sms_cridit);
	
	$view_arr[$i][ssms_credits]=$viewsms_cridit[ssms_credits];
	$i++;
}

$smarty->assign("view_arr",$view_arr);
$smarty->assign("msg",$msg);
$smarty->assign('total_users', $total_users);
$smarty->assign('pages', $page_array);
$smarty->assign('p', $page_vars[1]);
include "admin_footer.php";
?>