<?php
$page = "admin_file";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave")
{
	
  $setting['setting_permission_fileuploads'] = $_POST['setting_permission_fileuploads'];
  $database->database_query("UPDATE se_settings SET 
			setting_permission_fileuploads='$setting[setting_permission_fileuploads]'");

if($database->database_affected_rows()){
  $result = 1;
}	
}
# Add Category for files

if(isset($_POST['save_changes'])){

// SAVE CHANGES
		$categName = trim( $_POST['cat_new_input'] );

		if( $categName ) {
			
		$sql="select * from se_fileuploadcats where se_fileuploadcat_name like '$categName'";
		$tmp=$database->database_query($sql);
		$num=$database->database_num_rows($tmp);
		if(!$num){
			$database->database_query( "INSERT INTO `se_fileuploadcats` SET `fileuploadcat_name`='$categName'" );
			$newID = mysql_insert_id();
				if( $newID ){
					$action=1;
					$actionMsg = ' 7800044';
					}	
				else
				{
					$error=1;
					$errorMsg = '7800047';
		    	    	 }
			}
			else{
				$error=1;
				$errorMsg =' 7800045';
			}
		} else{
			$error=1;
			$errorMsg = '7800046';
		}
}

if($_REQUEST['action']){
switch( $_REQUEST['action'] )
{
	case 'del_cat':
		$catID = (int)$_GET['cat'];
		if( $catID ) {
			$database->database_query( "DELETE FROM `se_fileuploadcats` WHERE `fileuploadcat_id`=$catID" );
		
		$sql="select userupload_id from `se_fileuploads` WHERE `userupload_categoryid`=$catID ";
		$tmp=$database->database_query($sql);			
		while($row=$database->database_fetch_assoc($tmp)){

			$sql1="select * from se_filedownloads where userupload_id=".$row['userupload_id'];
			$tmp1=$database->database_query($sql1);
			$num1=$database->database_num_rows($tmp1);
			if($num1){
				$database->database_query( "DELETE FROM `se_filedownloads`  WHERE `userupload_id`=$row[userupload_id]" );
			}
			$sql2="select * from se_fileratingss where userupload_id=".$row['userupload_id'];
			$tmp2=$database->database_query($sql2);			$num2=$database->database_num_rows($tmp2);
			if($num2){
				$database->database_query( "DELETE FROM `se_fileratings`  WHERE `userupload_id`=$row[userupload_id]" );
			}	
		}
			$database->database_query( "DELETE FROM `se_fileuploads`  WHERE `userupload_categoryid`=$catID" );
		
				
			if( mysql_affected_rows() ){
				$action=1;
				$actionMsg .= '7800048';
			}
			else{
				$error=1;
				//$errorMsg .= 'Couldn\'t delete';
			}
		}
	break;
	case 'edit_cat':
		$catID = (int)$_REQUEST['cat'];
		$name = trim( $_REQUEST['name'] );

		if( $catID and $name ) {
			$database->database_query( "UPDATE `se_fileuploadcats` SET `fileuploadcat_name`='".$name."' WHERE `fileuploadcat_id`=$catID" );
			if( $database->database_affected_rows()){
				$action=1;
				$actionMsg = 'Renamed succesfully';
				}
			else{
				$error=1;	
				$errorMsg = 'Error while renaming';
			}
		}
	break;
	
}

}

$sql= "Select * from  `se_fileuploadcats` order by fileuploadcat_name ";
$res=$database->database_query($sql);
	if($database->database_num_rows($res)){
		$arr = array();
		while( $r = $database->database_fetch_assoc($res) )
		$arr[] = $r;
	}	
$smarty->assign('act', $action);
$smarty->assign('error', $error);
$smarty->assign('actionMsg',$errorMsg);
$smarty->assign('actionMsg',$actionMsg);
$smarty->assign('cats',$arr);

// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);

include "admin_footer.php";
?>