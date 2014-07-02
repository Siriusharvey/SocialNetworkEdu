<?php

$page = "userupload";
include "header.php";

$userID=$user->user_info['user_id'];
// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_fileuploads'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

function getExtension($str) 
{
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }
if(isset($_POST['Cancel'])){
	header('location:./user_file_uploads.php');
}


if(isset($_POST['add']) || isset($_POST['update']))
{


// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels limit 1");
$level_info = $database->database_fetch_assoc($level);	
$max_file_size=$level_info[level_file_upload_maxsize]; 
$file_exts_arr=explode(',',$level_info[level_file_upload_exts]);

//print_r($file_exts_arr);die;

 if(isset($_POST['add'])){
	
	$discription=addslashes($_POST['discription']);
	$Category=$_POST['Category'];
	$title= addslashes($_POST['title']);
	$userfiles=$_FILES["userfiles"]["name"];

	$userthumb=$_FILES["userthumb"]["name"];
	$filename = stripslashes($_FILES['userfiles']['name']);
        $filenamearray = explode(".", $filename);
   	$files_name =$filenamearray[0]; 	
  	$extension = getExtension($filename);
	$files_Name=$files_name.time().'.'.$extension;
	
	$img=$_FILES['userthumb'];
	$img_name = stripslashes($_FILES['userthumb']['name']);
        $imgnamearray = explode(".", $img_name);
   	$imgname =$imgnamearray[0]; 	
  	$extension_thumb = getExtension($img_name);
	$thumb_image=$imgname.time().'.'.$extension_thumb;
	//$file_maxsize=4* 1024 * 1024;
	if($filename!='' && $_FILES["userfiles"]["size"] < $max_file_size && in_array($extension,$file_exts_arr))
	{	
		$size=$_FILES["userfiles"]["size"];
		$type=addslashes($_FILES["userfiles"]["type"]);
		move_uploaded_file($_FILES["userfiles"]["tmp_name"],"userfiles/" . $files_Name);

			if($img_name!='')
			{
			if ((($_FILES["userthumb"]["type"] == "image/gif")
					|| ($_FILES["userthumb"]["type"] == "image/jpeg")
					|| ($_FILES["userthumb"]["type"] == "image/pjpeg"))
					&& ($_FILES["userthumb"]["size"] < 2* 1024 * 1024))
	
					{

	$large_image_location="userthumbs/" . $thumb_image;	
	$thumb="userthumbs/thumbnail/thumb_" . $thumb_image;
					move_uploaded_file($_FILES["userthumb"]["tmp_name"],"userthumbs/" . $thumb_image);

	$thumbwidth=100;
	$thumbheight=100;				
	$uploaded =resizeImage($large_image_location,$thumb,$thumbwidth,$thumbheight,$extension_thumb);

						$sql="INSERT INTO se_fileuploads (userupload_userid,userupload_categoryid, userupload_title,userupload_description,userupload_userfiles,userupload_userthumbs,userupload_filetype,userupload_filesize,userupload_time,userupload_search,userupload_privacy,fileuploads_comments)  values ('$userID','$Category','$title','$discription','$files_Name','thumb_$thumb_image','$type','$size',now(),'1','1','63')";

							$result=mysql_query($sql) or die(mysql_error());
							if($result)	
							$Actionmsg=7800022;
				}	
					else
					{
					 $Actionmsg=7800021;	
					}	
					
	
			
			}else
				{
					$sql="INSERT INTO se_fileuploads (userupload_userid,userupload_categoryid, userupload_title,userupload_description,userupload_userfiles,userupload_filetype,userupload_filesize,userupload_time,userupload_search,userupload_privacy,fileuploads_comments)  values ('$userID','$Category','$title','$discription','$files_Name','$type','$size',now(),'1','1','63')";		
					$result=mysql_query($sql) or die(mysql_error());
							if($result)	
							$Actionmsg=7800022;
	
				}	

		#--------Action manage to display on home page-------#

	$icon_sql="select icon_name,icon_alt from se_fileicons where file_type like '%$type%'";
	
		$tmp=$database->database_query($icon_sql);
		$num=$database->database_num_rows($tmp);
	if($num){
		$res=mysql_fetch_row($tmp);
		$icon=$res[0];
		$icon_alt=$res[1];
	}
	else{
		$icon='unknown-icon.gif';
		$icon_alt='Unknown File';
	}		
		
		$file_id=mysql_insert_id();
		$actions->actions_add($user, "newfileupload", Array($user->user_info[user_id],$user->user_info[user_username], $user->user_displayname, $file_id, $title,$icon,$icon_alt), Array(), 0, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);

		#-------xxxxxxxxxx-------#		

		header("Location:user_file_uploads.php");
	}else
		{
		    $Actionmsg=7800023;	
		}	
  
 	}
	else if(isset($_POST['update'])){

	$userfile_path="./userfile";
	$userthumb_path="./userthumbs";
	
	$id=$_POST['uid']; // userupload Id 
	$sql="select * from se_fileuploads where userupload_id=$id";
	$temp=$database->database_query($sql);
	while($rec=$database->database_fetch_assoc($temp)){
		$old_filename=$rec['userupload_userfiles'];
		$old_thumbname=$rec['userupload_userthumbs'];
	}		
		$description=addslashes($_POST['discription']);
		$category=$_POST['Category'];
		$title= addslashes($_POST['title']);
		
	$upSql="update se_fileuploads set userupload_description='$description',  userupload_categoryid=$category , userupload_title='$title',modify='1' ";
	
	if($_FILES['userfiles']['name']!=''){
		
		if(file_exists($userfile_path.$old_filename)){
			unlink($userfile_path.$old_filename);
		}
		
		$filename = stripslashes($_FILES['userfiles']['name']);
		
		$filenamearray = explode(".", $filename);
		$files_name =$filenamearray[0]; 	
	#----------GET Extension of file-----------#	
		$extension = getExtension($filename);
		$files_Name=$files_name.time().'.'.$extension;

			if($_FILES["userfiles"]["size"] < $max_file_size && in_array($extension,$file_exts_arr))
			{
				$filesize=$_FILES['userfiles']['size'];
				$type=addslashes($_FILES["userfiles"]["type"]);
				move_uploaded_file($_FILES["userfiles"]["tmp_name"],"userfiles/" . $files_Name);
				$upSql.=" ,userupload_userfiles='$files_Name', userupload_filetype='$type',userupload_filesize='$filesize' ";
			}else{
				$Actionmsg=7800023;	
			}
		}
	
	if($_FILES['userthumb']['name']!=''){
		
		
		if(file_exists($userthumb_path.$old_thumbname)){
			unlink($userthumb_path.$old_thumbname);
		}
			$img_name = stripslashes($_FILES['userthumb']['name']);
			$imgnamearray = explode(".", $img_name);
			$imgname =$imgnamearray[0];
		
 		#----------GET Extension of Thumb-----------#	
			$extension_thumb = getExtension($img_name);
			$thumb_image=$imgname.time().'.'.$extension_thumb;

			if ((($_FILES["userthumb"]["type"] == "image/gif")
					|| ($_FILES["userthumb"]["type"] == "image/jpeg")
					|| ($_FILES["userthumb"]["type"] == "image/pjpeg")|| ($_FILES["userthumb"]["type"] == "image/gif"))
					&& ($_FILES["userthumb"]["size"] < 2* 1024 * 1024))
			{
				$large_image_location="userthumbs/" . $thumb_image;	
				$thumb="userthumbs/thumbnail/thumb_" . $thumb_image;
				move_uploaded_file($_FILES["userthumb"]["tmp_name"],"userthumbs/" . $thumb_image);
	
				$width=100;
				$height=100;				
				$uploaded =resizeImage($large_image_location,$thumb,$width,$height,$extension_thumb);
			$upSql.=" ,userupload_userthumbs= 'thumb_$thumb_image'";
			}
			else
			{
				 $Actionmsg=7800021;	
			}	
		
		}
		
		$upSql.=" ,modified_at=now() where userupload_id=$_POST[uid]";

		$database->database_query($upSql);
		
		$file_id=$_POST['uid'];
		$file_title=$title;
	#--------Action manage to display on home page-------#
	$icon_sql="select icon_name,icon_alt from se_fileicons where file_type like '%$type%'";
	//echo $icon_sql;die;
	$tmp=$database->database_query($icon_sql);
	$num=$database->database_num_rows($tmp);
	if($num){
		$res=mysql_fetch_row($tmp);
		$icon=$res[0];
		$icon_alt=$res[1];
	}
	else{
		$icon='unknown-icon.gif';
		$icon_alt='Unknown File';
	}	

		$actions->actions_add($user, "editfile", Array($user->user_info[user_id],$user->user_info[user_username], $user->user_displayname, $file_id, $file_title,$icon,$icon_alt), Array(), 0, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);

	#-------xxxxxxxxxx-------#		
		if(strlen($Actionmsg)!=0){
			$id=$_POST['uid'];
			$sql="select * from se_fileuploads where userupload_id=$id";
			$rs=$database->database_query($sql);
			while( $row=$database->database_fetch_assoc($rs) ){
				$upTitle = $row['userupload_title'];
				$upDesc = $row['userupload_description'];
				$c=$row['userupload_categoryid'];
			}
			$smarty->assign('upTitle',$upTitle);
			$smarty->assign('upDesc',$upDesc);
			$smarty->assign('update','Update');
			$smarty->assign('c',$c);		
		}else {
			header("Location:user_file_uploads.php");
		}
	
  	}// End of update if

}	

#


if(isset($_GET['task']) && isset($_GET['upload_id'])){
		$id=$_GET['upload_id'];
		$sql="select * from se_fileuploads where userupload_id=$id";
		$rs=$database->database_query($sql);
		while( $row=$database->database_fetch_assoc($rs) ){
			$upTitle = $row['userupload_title'];
			$upDesc = $row['userupload_description'];
			$c=$row['userupload_categoryid'];
		}
		$smarty->assign('upTitle',$upTitle);
		$smarty->assign('upDesc',$upDesc);
		$smarty->assign('update','Update');	
		$smarty->assign('c',$c);
		$smarty->assign('uid',$id);	
	}
$sqlQuery = "SELECT *
			FROM `se_fileuploadcats`
			 ";
	$UploadCat = $database->database_query($sqlQuery);
	if($database->database_num_rows($UploadCat)){
		while( $cat=$database->database_fetch_assoc($UploadCat) ){
			$arr[] = $cat;
		}
	}
$smarty->assign('user_id',$user_id);
$smarty->assign('user_id',$user_id);
$smarty->assign('myCategory',$arr);
$smarty->assign('Actionmsg',$Actionmsg);
include "footer.php";
?>
