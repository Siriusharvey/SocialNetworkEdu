<?php

/* $Id: userupload.php   $ */

$page = "user_file_uploads";
include "header.php";
$user_id=$user->user_info['user_id'];

$files_per_page = 5;
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; } 

if(isset($_POST['sv'])){ $sv = $_POST['sv'];} elseif(isset($_GET['sv'])){$sv =$_GET['sv']; } else { $sv = "userupload_time DESC"; }


if(isset($_GET['task']) && isset($_GET['upload_id'])){

	$fileupload=new se_fileuploads();
	$fileupload->userupload_delete($_GET['upload_id']);

}

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_fileuploads'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

$sql="select count(*) as count from se_fileuploads  where userupload_userid='".$user_id."'";


$rs=$database->database_query($sql);
$total_uploads=$database->database_fetch_assoc($rs);
$max_page = ceil($total_uploads['count'] / $files_per_page);
$limit=" limit ".($p-1)*$files_per_page.", ".$files_per_page ;
$p_start=($p-1)*$files_per_page;

if($p != $max_page){
	$p_end=($p-1)*$files_per_page+$files_per_page;
}else {
	$p_end=$total_uploads['count'];
}
	
$sqlQuery="select sup.*,suf.userfiledownload_count,suf.userfiledownload_time "; 

if($sv == 'userupload_rating DESC'){
$sqlQuery.=" ,sum(total_value)/count(total_votes) as userupload_rating ";
}

$sqlQuery.=" from";

if($sv == 'userupload_rating DESC'){
$sqlQuery.=" (";
}

$sqlQuery.=" se_fileuploads as sup left join se_filedownloads as suf on sup.userupload_id=suf.userupload_id";

if($sv == 'userupload_rating DESC'){
$sqlQuery.=" ) left join se_fileratings as sr on sup.userupload_id=sr.userupload_id ";
}


$sqlQuery.=" where sup.userupload_userid='".$user_id."'";

if($sv == 'userupload_rating DESC'){
$sqlQuery.=" group by sup.userupload_id ";
}

if($sv == 'userupload_rating DESC'){
	 $sqlQuery.=" order by  $sv $limit";
}
else{ 
$sqlQuery.=" order by  $sv $limit";
}

//echo "<br/> Query <br/>".$sqlQuery;

	$UploadCat = $database->database_query($sqlQuery);
	if($database->database_num_rows($UploadCat)){
		while( $cat=$database->database_fetch_assoc($UploadCat) ){
			$cat['userupload_time']=strtotime($cat['userupload_time']);
			$cat['modified_at']=strtotime($cat['modified_at']);
			$arr[] = $cat;
			
		}
	}




	$query="select count(userupload_id) as counts from se_fileuploads where userupload_userid=$user_id";
	$temp=$database->database_query($query);
	if($database->database_num_rows($temp)){
		$row=mysql_fetch_row($temp);
		$category=$row[0];
	/*	
		while( $row=$database->database_fetch_assoc($temp) ){
			$catagory = $row['counts'];
		}
	*/
	}


$_SESSION['back']='user_file_uploads.php';
$smarty->assign('user_id', $user_id);
$smarty->assign('sv', $sv);
$smarty->assign('p', $p);
$smarty->assign('maxpage', $max_page);
$smarty->assign('p_start', $p_start);
$smarty->assign('p_end', $p_end);

$smarty->assign('cat',$arr);
$smarty->assign('total_files',$category);

include "footer.php";
?>