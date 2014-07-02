<?php

/* $Id: classified.php 16 2009-01-13 04:01:31Z john $ */

$page = "upload_desc";
include "header.php";

$user_id=$user->user_info['user_id'];
// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_fileuploads'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// GET PRIVACY LEVEL
$sql="select * from se_fileuploads where userupload_id=".$_GET['upid'];
$tmp = mysql_query($sql);
$fileupload = mysql_fetch_object($tmp);

$privacy_max = $owner->user_privacy_max($user);
$allowed_to_view    = (bool) ($privacy_max & $fileupload->userupload_privacy);
$allowed_to_comment = (bool) ($privacy_max & $fileupload->fileuploads_comments);

if(isset($_GET['user'])|| isset($_GET['upid'])) {
	$userid=$_GET['user'];
	$upload_id=$_GET['upid'];

	$qry="select * from se_fileuploads where userupload_id=$upload_id and userupload_userid=$userid";
	$tmp=$database->database_query($qry);
	$check=$database->database_num_rows($tmp);
if($check){

	$sql="select sup.*,suf.userfiledownload_count,suf.userfiledownload_time from se_fileuploads as sup LEFT JOIN se_filedownloads as suf ON suf.userupload_id=sup.userupload_id WHERE sup.userupload_id=$upload_id and sup.userupload_userid=$userid";

	$temp=$database->database_query($sql);
	if($database->database_num_rows($temp)){
		while($rs=$database->database_fetch_assoc($temp)){
			$rs['userupload_time']=strtotime($rs['userupload_time']);
			$rs['modified_at']=strtotime($rs['modified_at']);
			$arr[]=$rs;
		}
	}


// DISPLAY ERROR PAGE IF NO OWNER

	$sql="SELECT sum(total_value) as votes ,count(total_votes) as count FROM se_fileratings  where userupload_id='$upload_id' group by userupload_id";

	$temp=$database->database_query($sql);
	while($numbers=$database->database_fetch_assoc($temp)){
		$count=$numbers['count'];//how many votes total
		$rating=$numbers['votes'];	
	}
	$current_rating=($rating/$count) * 25;
	
	
	}else {
		$file_exist='not exist';
	}

	$sql="select * from se_users where user_id=$userid";
	$temp=mysql_query($sql);
	$userObj=mysql_fetch_object($temp);
	$username=$userObj->user_username;
	$fname=$userObj->user_fname;
	$lname=$userObj->user_lname;
	
}
// GET FILE COMMENTS

$comment = new se_comment('fileuploads', 'file_id',$upload_id );
$total_comments = $comment->comment_total();
$comments = $comment->comment_list(0, 10);


$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('typeIdentifier', $upload_id);

$smarty->assign('user_id',$user_id);
$smarty->assign('current_rating',$current_rating);
$smarty->assign('uploads',$arr);
$smarty->assign('fname',$fname);
$smarty->assign('lname',$lname);
$smarty->assign('uname',$username);
$smarty->assign('exist',$file_exist);

// ASSIGN VARIABLES AND DISPLAY CLASSIFIED PAGE

include "footer.php";
?>