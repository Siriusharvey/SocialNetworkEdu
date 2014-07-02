<?php

include "header.php";
if(isset($_GET['t'])){
	$type=$_GET['t'];
}
if(isset($_GET['fid'])){
	$fid=$_GET['fid'];
}
if(isset($_GET['name'])){
	$name=$_GET['name'];
}

$sql="select * from se_filedownloads where userupload_id=$fid";
$rs=$database->database_query($sql);
$num=$database->database_num_rows($rs);
$row=$database->database_fetch_assoc($rs);
//echo $sql."<br/>".$num; die;
if($num){
	//echo $row['userfiledownload_count']."<br/>";
	$count=(int)$row['userfiledownload_count']+1;
	//echo "Count ".$count;die;
	$sql="update se_filedownloads set userfiledownload_count=$count ,userfiledownload_time=now() where userupload_id=$fid ";	
	$a=$database->database_query($sql);
}else {
	$sql="insert into se_filedownloads(`userupload_id`,`userfiledownload_time`,`userfiledownload_count`)values('$fid',now(),'1')";
	$a=$database->database_query($sql);
}

	header('Content-type:'.$type); 
	header('Content-Disposition: attachment; filename='.$name);
	header('Pragma: no-cache');
        header('Expires: 0');
        // Send the file contents.
	readfile("./userfiles/".$name);

include "footer.php";
?>