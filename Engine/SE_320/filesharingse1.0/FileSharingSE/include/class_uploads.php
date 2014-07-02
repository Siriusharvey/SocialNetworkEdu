<?php

defined('SE_PAGE') or exit();


class se_fileuploads
{
	// INITIALIZE VARIABLES
	var $is_error;				            // DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;			          // CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;				              // CONTAINS THE USER ID OF THE USER WHOSE CLASSIFIED WE ARE EDITING

	var $upload_exists;			      // DETERMINES WHETHER THE CLASSIFIED HAS BEEN SET AND EXISTS OR NOT

	var $upload_info;			        // CONTAINS THE CLASSIFIED INFO 

	var $url_string;		              // CONTAINS VARIOUS PARTIAL URL STRINGS (SITUATION DEPENDENT)
   
// THIS METHOD SETS INITIAL VARS
	// INPUT: $user_id (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE ALBUMS WE ARE CONCERNED WITH
	// OUTPUT: 
  
	function se_fileuploads($user_id = 0)
  	{
	  $this->user_id = $user_id;
	}
  
  // END se_album() METHOD
	function userupload_total($where = "")
  {
	  global $database;
    
	  // BEGIN FILE UPLOAD QUERY
	  $sql = "
      SELECT
        NULL
      FROM
        se_fileuploads
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
      ON
        se_fileuploads.userupload_userid=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        userupload_userid='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        {$where}
    ";
   // echo "Total Sql ".$sql;
	  // GET AND RETURN TOTAL PHOTO ALBUMS
	  $file_total = $database->database_num_rows($database->database_query($sql));
    
	  return (int) $file_total;
	}
  
  // END file_total() METHOD

function userupload_list($start, $limit, $sort_by = "userupload_id DESC", $where = "")
  {
	  global $database, $user, $owner;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_fileuploads.* ";
    
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if( !$this->user_id ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_fileuploads
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_fileuploads.userupload_userid=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        userupload_userid='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        {$where}
    ";
    
	  // ADD ORDER, AND LIMIT CLAUSE
	  $sql .= "
      ORDER BY
        {$sort_by}
      LIMIT
        {$start}, {$limit}
    ";
  //  echo "List ".$sql;
	  // RUN QUERY
	  $resource = $database->database_query($sql);
    
	  // GET ALBUMS INTO AN ARRAY
	  $file_array = Array();
	  while( $file_info = $database->database_fetch_assoc($resource) )
    {
	    // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
	    if( !$this->user_id )
      {
	      $author = new se_user();
	      $author->user_exists = TRUE;
	      $author->user_info['user_id']       = $file_info['user_id'];
	      $author->user_info['user_username'] = $file_info['user_username'];
	      $author->user_info['user_fname']    = $file_info['user_fname'];
	      $author->user_info['user_lname']    = $file_info['user_lname'];
	      $author->user_info['user_photo']    = $file_info['user_photo'];
	      $author->user_displayname();
      }
      
	    // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
	    elseif( $owner->user_exists && $owner->user_info['user_id']==$file_info['userupload_userid'] )
      {
	      $author =& $owner;
	    }
      elseif( $user->user_exists && $user->user_info['user_id'] == $file_info['userupload_userid'] )
      {
	      $author =& $user;
	    }
      
	       
	    // CREATE ARRAY OF ALBUM DATA
	    SE_Language::_preload(user_privacy_levels($file_info['album_privacy']));
      
      // SET OTHER INFO
      $file_info['file_author'] =& $author;
      $file_info['total_files'] = $file_info['total_files'];
      
	    $file_array[] = $file_info;
      
      unset($author, $file_info);
	  }
    
	  // RETURN ARRAY
	  return $file_array;
	}
  
  // END album_list() METHOD



	function userupload_delete($file_id){
		global $database;
		
		$query="select * from se_fileuploads where userupload_id=$file_id";
		$files=$database->database_query($query);
		while($rec=$database->database_fetch_assoc($files)){
			$file_path="./userfiles/";
			$thumb_path="./userthumbs/thumbnail/";
			unlink($file_path.$files['userupload_file']);
			unlink($file_path.$files['userupload_thumb']);
		}		


		$sql="delete from se_fileuploads where userupload_id=$file_id";
		$database->database_query($sql);

		$qry1="select * from se_filedownloads where userupload_id=$file_id";
		$tmp1=$database->database_query($qry1);
		$num1=$database->database_num_rows($tmp1);
		if($num1){
			$sql1="delete from se_filedownloads where userupload_id=$file_id";
			$database->database_query($sql1);
		}

		$qry2="select * from se_fileratings where userupload_id=$file_id";
		$tmp2=$database->database_query($qry2);
		$num2=$database->database_num_rows($tmp2);
		if($num2){
			$sql2="delete from se_fileratings where userupload_id=$file_id";
			$database->database_query($sql2);
		}
	//($hook = SE_Hook::exists('se_file_delete')) ? SE_Hook::call($hook, array()) : NULL;

	}

	
	// THIS METHOD DELETES SELECTED FILES
	// INPUT: $start REPRESENTING THE FILE TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF FILES TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: 
	function file_delete_selected($start, $limit, $sort_by = "userupload_id DESC", $where = "") {
	  global $database;

	  // BEGIN QUERY
	  $file_query = "SELECT se_fileuploads.* ";
	
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if($this->user_id == 0) { $file_query .= ", se_users.user_id, se_users.user_username, se_users.user_photo"; }

	  // CONTINUE QUERY
	  $file_query .= " FROM se_fileuploads "/*LEFT JOIN se_media ON se_fileuploads.album_id=se_media.media_album_id"*/;

	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if($this->user_id == 0) { $file_query .= " LEFT JOIN se_users ON se_fileuploads.userupload_userid=se_users.user_id"; }

	  // ADD WHERE IF NECESSARY
	  if($where != "" || $this->user_id != 0) { $file_query .= " WHERE"; }

	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $file_query .= " userupload_userid='".$this->user_id."'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && $where != "") { $file_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $file_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $file_query .= "  ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $files = $database->database_query($file_query);

	  // GET ALBUMS INTO AN ARRAY
	  $file_array = Array();
	  while($file_info = $database->database_fetch_assoc($files)) {
    	    $var = "delete_file_".$file_info[userupload_id];
	    if($_POST[$var] == 1) { $this->userupload_delete($file_info[userupload_id]); }
	  }

	} // END file_delete_selected() METHOD
	
	function total_files(){
		global $database;
		$sql="select count(userupload_id) as total from se_fileupload";
		$files=$database->database_query($sql);
		$arr=$database->database_fetch_assoc($files);
		$total=$arr['total'];
		return $total;		
	}

}
?>