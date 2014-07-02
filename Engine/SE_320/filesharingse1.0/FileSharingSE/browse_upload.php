<?php

/* $Id: userupload.php   $ */

$page = "browse_upload";
include "header.php";

$user_id=$user->user_info['user_id'];
$files_per_page = 5;
// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION

/*if( !$user->user_exists && !$setting['setting_permission_fileuploads'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}*/
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; } 

if(isset($_POST['vs'])) { $vs = $_POST['vs']; } elseif(isset($_GET['vs'])) { $vs = $_GET['vs']; } else { $vs = "userupload_time DESC"; }

if(isset($_GET['task']) && isset($_GET['upload_id'])){
	$sql1="delete from se_fileuploads where userupload_id=$_GET[upload_id]";
	$database->database_query($sql1);
	$sql2="delete from se_filedownloads where userupload_id=$_GET[upload_id]";
	$database->database_query($sql2);
}

if(isset($_POST['uploadSearch'])){

$upload_search=$_POST['upload_search'];

$sql="select count(*) as count from se_fileuploads ";

if(isset($_POST['upload_search'])){
	$sql.=" where (userupload_description like '%$upload_search%' OR userupload_title like '%$upload_search%')";
}


$rs=$database->database_query($sql);
$total_uploads=$database->database_fetch_assoc($rs);
$total_files=$total_uploads['count'];

$max_page = ceil($total_uploads['count'] / $files_per_page);
$limit=" limit ".($p-1)*$files_per_page.", ".$files_per_page ;
$p_start=($p-1)*$files_per_page;

if($p != $max_page){
	$p_end=($p-1)*$files_per_page+$files_per_page;
}else {
	$p_end=$total_uploads['count'];
}

$sqlQuery="select sup.*,suf.userfiledownload_count,suf.userfiledownload_time "; 

if( $vs== 'userupload_rating DESC'){
$sqlQuery.=" ,sum(total_value)/count(total_votes) as userupload_rating ";
}

$sqlQuery.=" from";

if($vs== 'userupload_rating DESC'){
$sqlQuery.=" (";
}

$sqlQuery.=" se_fileuploads as sup left join se_filedownloads as suf on sup.userupload_id=suf.userupload_id";

if($vs== 'userupload_rating DESC'){
$sqlQuery.=" ) left join se_fileratings as sr on sup.userupload_id=sr.userupload_id ";
}

if(isset($_POST['upload_search'])){
	$sqlQuery.=" where (sup.userupload_description like '%$upload_search%' OR sup.userupload_title like '%$upload_search%')";
}

if($vs== 'userupload_rating DESC'){
$sqlQuery.=" group by sup.userupload_id ";
}

$sqlQuery.=" order by $vs $limit";


//echo $sqlQuery;
//echo "<br/> If Query <br/>".$sqlQuery;

$rs=$database->database_query($sqlQuery);
	if($database->database_num_rows($rs)){
		while( $cat=$database->database_fetch_assoc($rs) ){
			$cat['userupload_time']=strtotime($cat['userupload_time']);
			$cat['modified_at']=strtotime($cat['modified_at']);
			$arr[] = $cat;
		}
	
	}


}else {



$sql="select count(*) as count from se_fileuploads";

if($_GET['selcat']){
	$sql.= ' where userupload_categoryid='.$_GET['selcat'];
}

$rs=$database->database_query($sql);
$total_uploads=$database->database_fetch_assoc($rs);
$total_files=$total_uploads['count'];
$max_page = ceil($total_uploads['count'] / $files_per_page);
$limit=" limit ".($p-1)*$files_per_page.", ".$files_per_page ;
$p_start=($p-1)*$files_per_page;
if($p != $max_page){
	$p_end=($p-1)*$files_per_page+$files_per_page;
}else {
	$p_end=$total_uploads['count'];
}


$sqlQuery="select sup.*,suf.userfiledownload_count,suf.userfiledownload_time "; 

if( $vs== 'userupload_rating DESC'){
$sqlQuery.=" ,sum(total_value)/count(total_votes) as userupload_rating ";
}

$sqlQuery.=" from";

if($vs== 'userupload_rating DESC'){
$sqlQuery.=" (";
}

$sqlQuery.=" se_fileuploads as sup left join se_filedownloads as suf on sup.userupload_id=suf.userupload_id";

if($vs== 'userupload_rating DESC'){
$sqlQuery.=" ) left join se_fileratings as sr on sup.userupload_id=sr.userupload_id ";
}

if($_GET['selcat']){
	$sqlQuery.= ' where sup.userupload_categoryid='.$_GET['selcat'];
}

if($vs== 'userupload_rating DESC'){
$sqlQuery.=" group by sup.userupload_id ";
}

$sqlQuery.=" order by $vs $limit";


//echo "<br/>Else Query <br/>".$sqlQuery;

	$UploadCat = $database->database_query($sqlQuery);
	if($database->database_num_rows($UploadCat)){
		while( $cat=$database->database_fetch_assoc($UploadCat) ){
			$cat['userupload_time']=strtotime($cat['userupload_time']);
			$cat['modified_at']=strtotime($cat['modified_at']);
			$arr[] = $cat;
			
		}
	}



}

	$query="select * from se_fileuploadcats order by fileuploadcat_name ";
	$temp=$database->database_query($query);
	if($database->database_num_rows($temp)){
		while( $row=$database->database_fetch_assoc($temp) ){
			$catagory[] = $row;
		
		}
	}
//$smarty->assign('current_rating',$current_rating);
$_SESSION['back']='browse_upload.php';
$smarty->assign('back','browse_upload');
$smarty->assign('user_id', $user_id);
$smarty->assign('vs', $vs);
$smarty->assign('p', $p);
$smarty->assign('maxpage', $max_page);
$smarty->assign('p_start', $p_start);
$smarty->assign('p_end', $p_end);
$smarty->assign('total_files',$total_files);
$smarty->assign('cat',$arr);
$smarty->assign('search',$catagory);

include "footer.php";