<?php




//  THIS CLASS CONTAINS gstore ENTRY-RELATED METHODS 
//  METHODS IN THIS CLASS:
//
//    se_gstore()
//
//    gstore_total()
//    gstore_list()
//
//    gstore_post()
//    gstore_delete()
//    gstore_dir()
//    gstore_photo()
//    gstore_photo_upload()
//    gstore_photo_delete()
//    gstore_lastupdate()
//    gstore_media_upload()
//    gstore_media_space()
//    gstore_media_total()
//    gstore_media_list()
//    gstore_media_delete()


defined('SE_PAGE') or exit();


class se_gstore
{
	// INITIALIZE VARIABLES
	var $is_error;				            // DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;			          // CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;				              // CONTAINS THE USER ID OF THE USER WHOSE gstore WE ARE EDITING

	var $gstore_exists;			      // DETERMINES WHETHER THE gstore HAS BEEN SET AND EXISTS OR NOT

	var $gstore_info;			        // CONTAINS THE gstore INFO OF THE gstore WE ARE EDITING
	var $gstorevalue_info;	      // CONTAINS THE gstore FIELD VALUE INFO
	var $gstoreowner_level_info;	// CONTAINS THE gstore CREATOR'S LEVEL INFO

	var $url_string;		              // CONTAINS VARIOUS PARTIAL URL STRINGS (SITUATION DEPENDENT)
  
  
  
  
  
  
  
  
  //
	// THIS METHOD SETS INITIAL VARS
  //
	// INPUT:
  //    $user_id (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE gstore WE ARE CONCERNED WITH
  //
	// OUTPUT: 
  //
  
	function se_gstore($user_id=NULL, $gstore_id=NULL)
  {
	  global $database, $user, $owner;
    
	  $this->user_id = $user_id;
	  $this->gstore_exists = FALSE;
	  $this->is_member = FALSE;
    
	  if( $gstore_id )
    {
      $sql = "SELECT * FROM se_gstores WHERE gstore_id='{$gstore_id}' LIMIT 1";
      $resource = $database->database_query($sql);
      
	    if( $database->database_num_rows($resource) )
      {
	      $this->gstore_exists = TRUE;
	      $this->gstore_info = $database->database_fetch_assoc($resource);
        
        $sql = "SELECT * FROM se_gstorevalues WHERE gstorevalue_gstore_id='{$gstore_id}' LIMIT 1";
        $resource = $database->database_query($sql);
        
        if( $database->database_num_rows($resource) )
          $this->gstorevalue_info = $database->database_fetch_assoc($resource);
        
	      // GET LEVEL INFO
	      if( $this->gstore_info['gstore_user_id']==$user->user_info['user_id'] )
	        $this->gstoreowner_level_info =& $user->level_info;
        elseif( $this->gstore_info['gstore_user_id']==$owner->user_info['user_id'] )
	        $this->gstoreowner_level_info =& $owner->level_info;
        
        if( !$this->gstoreowner_level_info )
        {
          $sql = "SELECT se_levels.* FROM se_users LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id WHERE se_users.user_id='{$this->gstore_info['gstore_user_id']}' LIMIT 1";
          $resource = $database->database_query($sql);
          
          if( $database->database_num_rows($resource) )
            $this->gstoreowner_level_info = $database->database_fetch_assoc($resource);
	      }
	    }
	  }
	}
  
  // END se_gstore() METHOD
  
  
  
  
  
  
  
  
	//
  // THIS METHOD RETURNS THE TOTAL NUMBER OF ENTRIES
	//
  // INPUT:
  //    $where                (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $gstore_details   (OPTIONAL) REPRESENTING WHETHER TO RETRIEVE THE VALUES FROM gstoreVALUES TABLE AS WELL
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF ENTRIES
  //
  
	function gstore_total($where=NULL, $gstore_details=FALSE)
  {
	  global $database;
    
	  // BEGIN ENTRY QUERY
	  $sql = "
      SELECT
        NULL
      FROM
        se_gstores
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_gstores.gstore_user_id=se_users.user_id
    ";
    
	  // IF gstore DETAILS
	  if( $gstore_details ) $sql .= "
      LEFT JOIN
        se_gstorevalues
        ON se_gstores.gstore_id=se_gstorevalues.gstorevalue_gstore_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        se_gstores.gstore_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND ";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        $where
    ";
    
	  // GET AND RETURN TOTAL gstore ENTRIES
	  $resource = $database->database_query($sql);
	  $gstore_total = $database->database_num_rows($resource);
    
	  return $gstore_total;
	}
  
  // END gstores_total() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS AN ARRAY OF gstore ENTRIES
  //
	// INPUT:
  //    $start                REPRESENTING THE ENTRY TO START WITH
	//	  $limit                REPRESENTING THE NUMBER OF ENTRIES TO RETURN
	//	  $sort_by              (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where                (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $gstore_details   (OPTIONAL) REPRESENTING WHETHER TO RETRIEVE THE VALUES FROM gstoreVALUES TABLE AS WELL
  //
	// OUTPUT:
  //    AN ARRAY OF gstore ENTRIES
  //
  
	function gstore_list($start, $limit, $sort_by="gstore_date DESC", $where=NULL, $gstore_details=FALSE)
  {
	  global $database, $user, $owner;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_gstores.*,
        main_category.gstorecat_id AS main_category_id,
        main_category.gstorecat_title AS main_category_title,
        parent_category.gstorecat_id AS parent_category_id,
        parent_category.gstorecat_title AS parent_category_title,
        se_gstores.gstore_totalcomments AS total_comments
    ";
    
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if( !$this->user_id ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // IF gstore DETAILS
    if( $gstore_details ) $sql .= ",
        se_gstorevalues.*
    ";
    
	  // CONTINUE QUERY
	  $sql .= " 
      FROM
        se_gstores
      LEFT JOIN
        se_gstorecats AS main_category
        ON main_category.gstorecat_id=se_gstores.gstore_gstorecat_id
      LEFT JOIN
        se_gstorecats AS parent_category
        ON parent_category.gstorecat_id=main_category.gstorecat_dependency
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_gstores.gstore_user_id=se_users.user_id
    ";
    
	  // IF gstore DETAILS
	  if( $gstore_details ) $sql .= "
      LEFT JOIN
        se_gstorevalues
        ON se_gstores.gstore_id=se_gstorevalues.gstorevalue_gstore_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        gstore_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) )
      $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
      $where
    ";
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $sql .= "
    /*
      GROUP BY
        gstore_id */
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";
    
	  // RUN QUERY
	  $resource = $database->database_query($sql);
    
	  // GET gstore ENTRIES INTO AN ARRAY
	  $gstore_array = array();
	  while( $gstore_info=$database->database_fetch_assoc($resource) )
    {
	    // CONVERT HTML CHARACTERS BACK
	    $gstore_info['gstore_body'] = str_replace("\r\n", "", html_entity_decode($gstore_info['gstore_body']));
      
	    // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
	    if( !$this->user_id )
      {
	      $author = new se_user();
	      $author->user_exists = 1;
	      $author->user_info['user_id']       = $gstore_info['user_id'];
	      $author->user_info['user_username'] = $gstore_info['user_username'];
	      $author->user_info['user_photo']    = $gstore_info['user_photo'];
	      $author->user_info['user_fname']    = $gstore_info['user_fname'];
	      $author->user_info['user_lname']    = $gstore_info['user_lname'];
	      $author->user_displayname();
      }
      
	    // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
	    elseif( $owner->user_exists && $owner->user_info['user_id']==$gstore_info['gstore_user_id'] )
      {
	      $author =& $owner;
	    }
      elseif( $user->user_exists && $user->user_info['user_id']==$gstore_info['gstore_user_id'] )
      {
	      $author =& $user;
	    }
      else
      {
        $author = new se_user(array($gstore_info['gstore_user_id']));
      }
      
	    // GET ENTRY COMMENT PRIVACY
      // FIND A WAY TO MAKE THIS WORK WITH THE AUTHOR
	    $allowed_to_comment = TRUE;
	    if( $owner->user_exists )
      {
	      $comment_level = $owner->user_privacy_max($user);
	      if( !($comment_level & $gstore_info['gstore_comments']) )
          $allowed_to_comment = FALSE;
	    }
      
      // PRELOAD CATEGORY TITLE
      if( $gstore_info['main_category_title'] )
        SE_Language::_preload($gstore_info['main_category_title']);
        
      if( $gstore_info['parent_category_title'] )
        SE_Language::_preload($gstore_info['parent_category_title']);
      
	    // CREATE OBJECT FOR gstore
	    $gstore = new se_gstore($gstore_info['user_id']);
	    $gstore->gstore_exists = TRUE;
	    $gstore->gstore_info = $gstore_info;
      
	    // SET gstore ARRAY
	    $gstore_array[] = array
      (
        'gstore'                      => &$gstore,
        'gstore_author'               => &$author,
        'total_comments'                  => $gstore_info['total_comments'],
        'allowed_to_comment'              => $allowed_to_comment
      );
      
      unset($author, $gstore);
	  }
    
	  // RETURN ARRAY
	  return $gstore_array;
	}
  
  // END gstores_list() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD POSTS/EDITS AN ENTRY
  //
	// INPUT:
  //    $gstore_id                REPRESENTING THE ID OF THE gstore ENTRY TO EDIT. IF NO ENTRY WITH THIS ID IS FOUND, A NEW ENTRY WILL BE ADDED
	//	  $gstore_title             REPRESENTING THE TITLE OF THE gstore ENTRY
	//	  $gstore_price             REPRESENTING THE PRICE OF THE gstore ENTRY
	//	  $gstore_stock             REPRESENTING THE stock OF THE gstore ENTRY
	//	  $item_sales             REPRESENTING THE total sales OF THE gstore ENTRY
	//	  $gstore_body              REPRESENTING THE BODY OF THE gstore ENTRY
	//	  $gstore_gstorecat_id  REPRESENTING THE ID OF THE SELECTED gstore ENTRY CATEGORY
	//	  $gstore_search            REPRESENTING WHETHER THE gstore ENTRY SHOULD BE INCLUDED IN SEARCH RESULTS
	//	  $gstore_privacy           REPRESENTING THE PRIVACY LEVEL OF THE ENTRY
	//	  $gstore_comments          REPRESENTING WHO CAN COMMENT ON THE ENTRY
	//	  $gstore_field_query       REPRESENTING THE PARTIAL QUERY TO SAVE IN THE gstore'S VALUE TABLE
  //
	// OUTPUT:
  //
  
	function gstore_post($gstore_id=NULL, $gstore_title, $gstore_price, $gstore_stock, $band_a_charge, $band_b_charge, $band_c_charge, $band_d_charge, $apply_shipping_charges, $gstore_body, $gstore_gstorecat_id, $gstore_search, $gstore_privacy, $gstore_comments, $gstore_field_query, $item_sales)
  {
	  global $database, $user;
    
    // INIT VARS
	  $gstore_date = time();
	  $gstore_title = censor($gstore_title);
	  $gstore_price = censor($gstore_price);
	  $gstore_stock = censor($gstore_stock);
	  $band_a_charge = censor($band_a_charge);
	  $band_b_charge = censor($band_b_charge);
	  $band_c_charge = censor($band_c_charge);
	  $band_d_charge = censor($band_d_charge);
	  $apply_shipping_charges = censor($apply_shipping_charges);
	  $gstore_body = censor(htmlspecialchars_decode($gstore_body));
	  $gstore_body = cleanHTML($gstore_body, $user->level_info['level_gstore_html']);
	  $gstore_body = security($gstore_body);
	  $item_sales = censor($item_sales);
	  
    
    
    // UPDATE TABLE ROW
    if($gstore_id )
    {
      $sql = "
        UPDATE
          se_gstores
        SET
          gstore_gstorecat_id='{$gstore_gstorecat_id}',
          gstore_dateupdated='{$gstore_date}',
          gstore_title='{$gstore_title}',
		  gstore_price='{$gstore_price}',
		  gstore_stock='{$gstore_stock}',
		  band_a_charge='{$band_a_charge}',
		  band_b_charge='{$band_b_charge}',
		  band_c_charge='{$band_c_charge}',
		  band_d_charge='{$band_d_charge}',
		  apply_shipping_charges='{$apply_shipping_charges}',
          gstore_body='{$gstore_body}',
          gstore_search='{$gstore_search}',
          gstore_privacy='{$gstore_privacy}',
          gstore_comments='{$gstore_comments}'
        WHERE
          gstore_id='{$gstore_id}' &&
          gstore_user_id='{$this->user_id}'
        LIMIT
          1
      ";
      
      $database->database_query($sql);
    }
    
    // ADD TABLE ROW
    else
    {
      $sql = "
        INSERT INTO se_gstores (
          gstore_user_id,
          gstore_gstorecat_id,
          gstore_date,
          gstore_dateupdated,
          gstore_title,
		  gstore_price,
		  gstore_stock,
		  band_a_charge,
		  band_b_charge,
		  band_c_charge,
		  band_d_charge,
		  apply_shipping_charges,
          gstore_body,
          gstore_search,
          gstore_privacy,
          gstore_comments,
		  item_sales
		  
        ) VALUES (
          '{$this->user_id}',
          '{$gstore_gstorecat_id}',
          '{$gstore_date}',
          '{$gstore_date}',
          '{$gstore_title}',
		  '{$gstore_price}',
		  '{$gstore_stock}',
		  '{$band_a_charge}',
		  '{$band_b_charge}',
		  '{$band_c_charge}',
		  '{$band_d_charge}',
		  '{$apply_shipping_charges}',
          '{$gstore_body}',
          '{$gstore_search}',
          '{$gstore_privacy}',
          '{$gstore_comments}',
		  '{$item_sales}'
		  
        )
      ";
      
      $database->database_query($sql);
      $gstore_id = $database->database_insert_id();
      
      // ADD gstore FIELD VALUE ROW
      $sql = "INSERT INTO se_gstorevalues (gstorevalue_gstore_id) VALUES ('{$gstore_id}')";
      $database->database_query($sql);
      
      // ADD gstore ALBUM
      $sql = "
        INSERT INTO se_gstorealbums (
          gstorealbum_gstore_id,
          gstorealbum_datecreated,
          gstorealbum_dateupdated,
          gstorealbum_title,
          gstorealbum_desc,
          gstorealbum_search,
          gstorealbum_privacy,
          gstorealbum_comments
        ) VALUES (
          '{$gstore_id}',
          '{$gstore_date}',
          '{$gstore_date}',
          '',
          '',
          '{$gstore_search}',
          '{$gstore_privacy}',
          '{$gstore_comments}'
        )
      ";
      $database->database_query($sql);
    }
    
    // UPDATE gstore FIELD VALUES
    if( !empty($gstore_field_query) )
    {
      $sql = "UPDATE se_gstorevalues SET {$gstore_field_query} WHERE gstorevalue_gstore_id='{$gstore_id}' LIMIT 1";
      $database->database_query($sql);
    }
    
    // CHECK AND ADD gstore DIRECTORY
    $gstore_directory = $this->gstore_dir($gstore_id);
    $gstore_path_array = explode("/", $gstore_directory);
    array_pop($gstore_path_array);
    array_pop($gstore_path_array);
    $subdir = implode("/", $gstore_path_array)."/";
    
    if( !is_dir($subdir) )
    { 
      mkdir($subdir, 0777); 
      chmod($subdir, 0777); 
      $handle = fopen($subdir."index.php", 'x+');
      fclose($handle);
    }
    
    if( !is_dir($gstore_directory) )
    {
      mkdir($gstore_directory, 0777);
      chmod($gstore_directory, 0777);
      $handle = fopen($gstore_directory."/index.php", 'x+');
      fclose($handle);
    }
    
	  return $gstore_id;
	}
  
  // END gstore_post() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD DELETES gstore ENTRIES
  //
	// INPUT:
  //    $gstore_id  REPRESENTING THE ID OF THE ENTRY TO DELETE
  //
	// OUTPUT:
  //
  
	function gstore_delete($gstore_id=NULL)
  {
	  global $database;
    
    // IF EMPTY, TRY TO GET FROM OBJECT
	  if( !$gstore_id && !$this->gstore_exists )
      return FALSE;
    elseif( !$gstore_id )
      $gstore_id = $this->gstore_info['gstore_id'];
    
    // IF ARRAY
    if( is_array($gstore_id) )
      return array_map(array(&$this, 'gstore_delete'), $gstore_id);
    
	  // DELETE gstore ALBUM AND MEDIA
    $sql = "DELETE FROM se_gstorealbums, se_gstoremedia USING se_gstorealbums LEFT JOIN se_gstoremedia ON se_gstorealbums.gstorealbum_id=se_gstoremedia.gstoremedia_gstorealbum_id WHERE se_gstorealbums.gstorealbum_gstore_id='{$gstore_id}'";
	  $database->database_query($sql);
    
	  // DELETE gstore VALUES
	  $sql = "DELETE FROM se_gstorevalues WHERE se_gstorevalues.gstorevalue_gstore_id='{$gstore_id}' LIMIT 1";
    $database->database_query($sql);
    
	  // DELETE gstore ROW
	  $sql = "DELETE FROM se_gstores WHERE se_gstores.gstore_id='{$gstore_id}' LIMIT 1";
    $database->database_query($sql);
    
	  // DELETE gstore COMMENTS
	  $sql = "DELETE FROM se_gstorecomments WHERE se_gstorecomments.gstorecomment_gstore_id='{$gstore_id}'";
    $database->database_query($sql);
    
	  // DELETE gstore'S FILES
	  if( is_dir($this->gstore_dir($gstore_id)) )
	    $dir = $this->gstore_dir($gstore_id);
	  else
	    $dir = ".".$this->gstore_dir($gstore_id);
    
	  if( $dh = @opendir($dir) )
    {
	    while( ($file = @readdir($dh))!==FALSE )
	      if($file != "." & $file != "..")
	        @unlink($dir.$file);
      
	    @closedir($dh);
	  }
	  @rmdir($dir);
    
    return TRUE;
	}
  
  // END gstore_delete() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE PATH TO THE GIVEN gstore'S DIRECTORY
  //
	// INPUT:
  //    $gstore_id (OPTIONAL) REPRESENTING A gstore'S gstore
  //
	// OUTPUT:
  //    A STRING REPRESENTING THE RELATIVE PATH TO THE gstore'S DIRECTORY
  //
  
	function gstore_dir($gstore_id=NULL)
  {
	  if( !$gstore_id && $this->gstore_exists )
      $gstore_id = $this->gstore_info['gstore_id'];
    
	  $subdir = $gstore_id+999-(($gstore_id-1)%1000);
	  $gstoredir = "./uploads_gstore/$subdir/$gstore_id/";
	  return $gstoredir;
	}
  
  // END gstore_dir() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD OUTPUTS THE PATH TO THE gstore'S PHOTO OR THE GIVEN NOPHOTO IMAGE
  //
	// INPUT:
  //    $nophoto_image (OPTIONAL) REPRESENTING THE PATH TO AN IMAGE TO OUTPUT IF NO PHOTO EXISTS
  //
	// OUTPUT:
  //    A STRING CONTAINING THE PATH TO THE gstore'S PHOTO
  //
  
	function gstore_photo($nophoto_image=NULL, $thumb=FALSE)
  {
    if( empty($this->gstore_info['gstore_photo']) )
      return $nophoto_image;
    
	  $gstore_dir = $this->gstore_dir($this->gstore_info['gstore_id']);
	  $gstore_photo = $gstore_dir.$this->gstore_info['gstore_photo'];
    if( $thumb )
    {
      $gstore_thumb = substr($gstore_photo, 0, strrpos($gstore_photo, "."))."_thumb".substr($gstore_photo, strrpos($gstore_photo, "."));
      if( file_exists($gstore_thumb) )
        return $gstore_thumb;
    }
    
    if( file_exists($gstore_photo) )
      return $gstore_photo;
    
    return $nophoto_image;
	}
  
  // END gstore_photo() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPLOADS AN gstore PHOTO ACCORDING TO SPECIFICATIONS AND RETURNS gstore PHOTO
  //
	// INPUT:
  //    $photo_name REPRESENTING THE NAME OF THE FILE INPUT
  //
	// OUTPUT:
  //
  
	function gstore_photo_upload($photo_name)
  {
	  global $database, $url;
    
	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->gstoreowner_level_info['level_gstore_photo_exts'])));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = $this->gstoreowner_level_info['level_gstore_photo_width'];
	  $file_maxheight = $this->gstoreowner_level_info['level_gstore_photo_height'];
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $this->gstore_dir($this->gstore_info['gstore_id']).$photo_newname;
	  $thumb_dest = substr($file_dest, 0, strrpos($file_dest, "."))."_thumb".substr($file_dest, strrpos($file_dest, "."));
    
	  $new_photo = new se_upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if( !$new_photo->is_error )
    {
	    // DELETE OLD AVATAR IF EXISTS
	    $this->gstore_photo_delete();
      
	    // UPLOAD THUMB
	    $new_photo->upload_thumb($thumb_dest, 130);
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if( $new_photo->is_image )
	      $new_photo->upload_photo($file_dest);
	    else
	      $new_photo->upload_file($file_dest);
      
	    // UPDATE gstore INFO WITH IMAGE IF STILL NO ERROR
	    if( !$new_photo->is_error )
      {
        $sql = "UPDATE se_gstores SET gstore_photo='{$photo_newname}' WHERE gstore_id='{$this->gstore_info['gstore_id']}'";
	      $database->database_query($sql);
	      $this->gstore_info['gstore_photo'] = $photo_newname;
	    }
	  }
    
	  $this->is_error = $new_photo->is_error;
	  $this->error_message = $new_photo->error_message;
	}
  
  // END gstore_photo_upload() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD DELETES A gstore PHOTO
  //
	// INPUT: 
  //
	// OUTPUT: 
  //
  
	function gstore_photo_delete()
  {
	  global $database;
    
	  $gstore_photo = $this->gstore_photo();
    
	  if( !empty($gstore_photo) )
    {
	    $sql = "UPDATE se_gstores SET gstore_photo='' WHERE gstore_id='{$this->gstore_info['gstore_id']}' LIMIT 1";
	    $database->database_query($sql);
	    $this->gstore_info['gstore_photo'] = "";
	    @unlink($gstore_photo);
	  }
	}
  
  // END gstore_photo_delete() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPDATES THE gstore'S LAST UPDATE DATE
  //
	// INPUT: 
  //
	// OUTPUT: 
  //
  
	function gstore_lastupdate()
  {
	  global $database;
    $sql = "UPDATE se_gstores SET gstore_dateupdated='".time()."' WHERE gstore_id='{$this->gstore_info['gstore_id']}' LIMIT 1";
	  $database->database_query($sql);
	}
  
  // END gstore_lastupdate() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPLOADS MEDIA TO A gstore ALBUM
  //
	// INPUT:
  //    $file_name          REPRESENTING THE NAME OF THE FILE INPUT
	//	  $gstorealbum_id REPRESENTING THE ID OF THE gstore ALBUM TO UPLOAD THE MEDIA TO
	//	  $space_left         REPRESENTING THE AMOUNT OF SPACE LEFT
  //
	// OUTPUT:
  //
  
	function gstore_media_upload($file_name, $gstorealbum_id, &$space_left)
  {
	  global $class_gstore, $database, $url;
    
	  // SET KEY VARIABLES
	  $file_maxsize   = $this->gstoreowner_level_info['level_gstore_album_maxsize'];
	  $file_exts      = explode(",", str_replace(" ", "", strtolower($this->gstoreowner_level_info['level_gstore_album_exts'])));
	  $file_types     = explode(",", str_replace(" ", "", strtolower($this->gstoreowner_level_info['level_gstore_album_mimes'])));
	  $file_maxwidth  = $this->gstoreowner_level_info['level_gstore_album_width'];
	  $file_maxheight = $this->gstoreowner_level_info['level_gstore_album_height'];
    
	  $new_media = new se_upload();
	  $new_media->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if( !$new_media->is_error )
    {
	    // INSERT ROW INTO MEDIA TABLE
      $sql = "
        INSERT INTO se_gstoremedia
          (gstoremedia_gstorealbum_id, gstoremedia_date)
        VALUES
          ('{$gstorealbum_id}', '".time()."')
      ";
      
      $database->database_query($sql);
	    $gstoremedia_id = $database->database_insert_id();
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
      $gstore_dir = $this->gstore_dir($this->gstore_info['gstore_id']);
	    if( $new_media->is_image )
      {
	      $file_dest  = "{$gstore_dir}{$gstoremedia_id}.jpg";
	      $thumb_dest = "{$gstore_dir}{$gstoremedia_id}_thumb.jpg";
        
	      // UPLOAD THUMB
	      $new_media->upload_thumb($thumb_dest, 130);
        
	      // UPLOAD FILE
	      $new_media->upload_photo($file_dest);
	      $file_ext = "jpg";
	      $file_filesize = filesize($file_dest);
	    }
      
      else
      {
	      $file_dest  = "{$gstore_dir}{$gstoremedia_id}.{$new_media->file_ext}";
	      $thumb_dest = "{$gstore_dir}{$gstoremedia_id}_thumb.jpg";
        
	      if( $new_media->file_ext=='gif' )
	        $new_media->upload_thumb($thumb_dest, 130);
        
	      $new_media->upload_file($file_dest);
	      $file_ext = $new_media->file_ext;
	      $file_filesize = filesize($file_dest);
	    }
      
      // CHECK SPACE LEFT
      if( $space_left!==FALSE && $file_filesize > $space_left)
      {
        $new_media->is_error = 1;
        $new_media->error_message = $class_gstore[1].$_FILES[$file_name]['name']; // TODO LANG
      }
      elseif( $space_left!==FALSE )
      {
	      $space_left = $space_left - $file_filesize;
	    }
      
	    // DELETE FROM DATABASE IF ERROR
	    if( $new_media->is_error )
      {
        $sql = "DELETE FROM se_gstoremedia WHERE gstoremedia_id='{$gstoremedia_id}' AND gstoremedia_gstorealbum_id='{$gstorealbum_id}' LIMIT 1";
	      $database->database_query($sql);
	      @unlink($file_dest);
	    }
      
	    // UPDATE ROW IF NO ERROR
      else
      {
	      $sql = "UPDATE se_gstoremedia SET gstoremedia_ext='{$file_ext}', gstoremedia_filesize='{$file_filesize}' WHERE gstoremedia_id='{$gstoremedia_id}' AND gstoremedia_gstorealbum_id='{$gstorealbum_id}' LIMIT 1";
        $database->database_query($sql);
        
        // Update parent row
        $sql = "UPDATE se_gstorealbums SET gstorealbum_totalfiles=gstorealbum_totalfiles+1, gstorealbum_totalspace=gstorealbum_totalspace+'{$file_filesize}' WHERE gstorealbum_id='{$gstorealbum_id}' LIMIT 1";
        $database->database_query($sql);
	    }
	  }
    
	  // RETURN FILE STATS
	  return array(
      'is_error'                  => $new_media->is_error,
			'error_message'             => $new_media->error_message,
			'gstoremedia_id'        => $gstoremedia_id,
			'gstoremedia_ext'       => $file_ext,
			'gstoremedia_filesize'  => $file_filesize
    );
	}
  
  // END gstore_media_upload() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE SPACE USED
  //
	// INPUT:
  //    $gstorealbum_id (OPTIONAL) REPRESENTING THE ID OF THE ALBUM TO CALCULATE
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE SPACE USED
  //
  
	function gstore_media_space($gstorealbum_id=NULL)
  {
	  global $database;
    
    // NEW HANDLING METHOD
    if( !$gstorealbum_id )
    {
      $sql = "
        SELECT
          se_gstorealbums.gstorealbum_totalspace AS total_space
        FROM
          se_gstorealbums
        WHERE
          se_gstorealbums.gstorealbum_id='{$gstorealbum_id}'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
      
      if( $resource )
      {
        $space_info = $database->database_fetch_assoc($resource);
        return $space_info['total_space'];
      }
    }
    
    // OLD HANDLING METHOD - BACKWARDS COMPATIBILITY
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        SUM(se_gstoremedia.gstoremedia_filesize) AS total_space
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_gstorealbums
      LEFT JOIN
        se_gstoremedia
        ON se_gstorealbums.gstorealbum_id=se_gstoremedia.gstoremedia_gstorealbum_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->gstore_exists || $gstorealbum_id ) $sql .= "
      WHERE
    ";
    
	  // IF gstore EXISTS, SPECIFY gstore ID
	  if( $this->gstore_exists ) $sql .= "
        se_gstorealbums.gstorealbum_gstore_id='{$this->gstore_info['gstore_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->gstore_exists && $gstorealbum_id )
      $sql .= " AND";
    
	  // SPECIFY ALBUM ID IF NECESSARY
	  if( $gstorealbum_id ) $sql .= "
        se_gstorealbums.gstorealbum_id='{$gstorealbum_id}'
    ";
    
	  // GET AND RETURN TOTAL SPACE USED
    $resource = $database->database_query($sql);
	  $space_info = $database->database_fetch_assoc($resource);
	  return $space_info['total_space'];
	}
  
  // END gstore_media_space() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE NUMBER OF gstore MEDIA
  //
	// INPUT:
  //    $gstorealbum_id (OPTIONAL) REPRESENTING THE ID OF THE clagstoressified ALBUM TO CALCULATE
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF FILES
  //
  
	function gstore_media_total($gstorealbum_id=NULL)
  {
	  global $database;
    
    // NEW HANDLING METHOD
    if( !$gstorealbum_id )
    {
      $sql = "
        SELECT
          se_gstorealbums.gstorealbum_totalfiles AS total_files
        FROM
          se_gstorealbums
        WHERE
          se_gstorealbums.gstorealbum_id='{$gstorealbum_id}'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
      
      if( $resource )
      {
        $file_info = $database->database_fetch_assoc($resource);
        return $file_info['total_files'];
      }
    }
    
    // OLD HANDLING METHOD - BACKWARDS COMPATIBILITY
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        COUNT(se_gstoremedia.gstoremedia_id) AS total_files
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_gstorealbums
      LEFT JOIN
        se_gstoremedia
        ON se_gstorealbums.gstorealbum_id=se_gstoremedia.gstoremedia_gstorealbum_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->gstore_exists || $gstorealbum_id ) $sql .= "
      WHERE
    ";
    
	  // IF gstore EXISTS, SPECIFY gstore ID
	  if( $this->gstore_exists ) $sql .= "
        se_gstorealbums.gstorealbum_gstore_id='{$this->gstore_info['gstore_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->gstore_exists && $gstorealbum_id )
      $sql .= " AND";
    
	  // SPECIFY ALBUM ID IF NECESSARY
	  if( $gstorealbum_id ) $sql .= "
        se_gstorealbums.gstorealbum_id='{$gstorealbum_id}'
    ";
    
	  // GET AND RETURN TOTAL FILES
    $resource = $database->database_query($sql);
	  $file_info = $database->database_fetch_assoc($resource);
	  return $file_info['total_files'];
	}
  
  // END gstore_media_total() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS AN ARRAY OF gstore MEDIA
  //
	// INPUT:
  //    $start REPRESENTING THE gstore MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF gstore MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    AN ARRAY OF gstore MEDIA
  //
  
	function gstore_media_list($start, $limit, $sort_by = "gstoremedia_id DESC", $where=NULL, $file_details=FALSE)
  {
	  global $database;
    
    if( !$start ) $start = '0';
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_gstoremedia.*,
        se_gstorealbums.gstorealbum_id,
        se_gstorealbums.gstorealbum_gstore_id,
        se_gstorealbums.gstorealbum_title
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_gstoremedia
      LEFT JOIN
        se_gstorealbums
        ON se_gstorealbums.gstorealbum_id=se_gstoremedia.gstoremedia_gstorealbum_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->gstore_exists || !empty($where) ) $sql .= "
      WHERE
    ";
    
	  // IF gstore EXISTS, SPECIFY gstore ID
	  if( $this->gstore_exists ) $sql .= "
        se_gstorealbums.gstorealbum_gstore_id='{$this->gstore_info['gstore_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->gstore_exists && !empty($where) )
      $sql .= " AND";
    
	  // ADD ADDITIONAL WHERE CLAUSE
	  if( !empty($where) ) $sql .= "
        $where
    ";
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $sql .= "
    /*
      GROUP BY
        gstoremedia_id */
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";
    
	  // RUN QUERY
    $resource = $database->database_query($sql);
    
	  // GET gstore MEDIA INTO AN ARRAY
    $gstore_dir = $this->gstore_dir($this->gstore_info['gstore_id']);
	  $gstoremedia_array = array();
	  while( $gstoremedia_info=$database->database_fetch_assoc($resource) )
    {
      $gstoremedia_info['gstoremedia_desc'] = str_replace("<br />", "\r\n", $gstoremedia_info['gstoremedia_desc']);
      
      if( $file_details )
      {
        $mediasize = getimagesize($gstore_dir.$gstoremedia_info['gstoremedia_id'].'.'.$gstoremedia_info['gstoremedia_ext']);
        $gstoremedia_info['gstoremedia_dir']  = $gstore_dir;
        $gstoremedia_info['gstoremedia_width']  = $mediasize[0];
        $gstoremedia_info['gstoremedia_height'] = $mediasize[1];
        
      }
      
	    $gstoremedia_array[] = $gstoremedia_info;
	  }
    
	  // RETURN ARRAY
	  return $gstoremedia_array;
	}
  
  // END gstore_media_list() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD DELETES SELECTED gstore MEDIA
  //
	// INPUT:
  //    $start    REPRESENTING THE gstore MEDIA TO START WITH
	//	  $limit    REPRESENTING THE NUMBER OF gstore MEDIA TO RETURN
	//	  $sort_by  (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where    (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //
  
	function gstore_media_delete($start, $limit, $sort_by = "gstoremedia_id DESC", $where = "")
  {
	  global $database, $url;
    
	  // BEGIN QUERY
	  $gstoremedia_query = "SELECT se_gstoremedia.*, se_gstorealbums.gstorealbum_id, se_gstorealbums.gstorealbum_gstore_id, se_gstorealbums.gstorealbum_title";
    
	  // CONTINUE QUERY
	  $gstoremedia_query .= " FROM se_gstoremedia LEFT JOIN se_gstorealbums ON se_gstorealbums.gstorealbum_id=se_gstoremedia.gstoremedia_gstorealbum_id";
    
	  // ADD WHERE IF NECESSARY
	  if($this->gstore_exists != 0 | $where != "") { $gstoremedia_query .= " WHERE"; }
    
	  // IF gstore EXISTS, SPECIFY gstore ID
	  if($this->gstore_exists != 0) { $gstoremedia_query .= " se_gstorealbums.gstorealbum_gstore_id='{$this->gstore_info['gstore_id']}'"; }
    
	  // ADD AND IF NECESSARY
	  if($this->gstore_exists != 0 & $where != "") { $gstoremedia_query .= " AND"; }
    
	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $gstoremedia_query .= " $where"; }
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $gstoremedia_query .= " GROUP BY se_gstoremedia.gstoremedia_id ORDER BY $sort_by LIMIT $start, $limit";
    
	  // RUN QUERY
	  $gstoremedia = $database->database_query($gstoremedia_query);
    
	  // LOOP OVER gstore MEDIA
	  $gstoremedia_delete = "";
	  while($gstoremedia_info = $database->database_fetch_assoc($gstoremedia))
    {
	    $var = "delete_gstoremedia_".$gstoremedia_info['gstoremedia_id']; 
	    if($gstoremedia_delete != "") { $gstoremedia_delete .= " OR "; }
	    $gstoremedia_delete .= "gstoremedia_id='$gstoremedia_info[gstoremedia_id]'";
	    $gstoremedia_path = $this->gstore_dir($this->gstore_info['gstore_id']).$gstoremedia_info['gstoremedia_id'].".".$gstoremedia_info['gstoremedia_ext'];
	    if(file_exists($gstoremedia_path)) { @unlink($gstoremedia_path); }
	    $thumb_path = $this->gstore_dir($this->gstore_info['gstore_id']).$gstoremedia_info['gstoremedia_id']."_thumb.".$gstoremedia_info['gstoremedia_ext'];
	    if(file_exists($thumb_path)) { @unlink($thumb_path); }
      
      // Update parent row
      $sql = "UPDATE se_gstorealbums SET gstorealbum_totalfiles=gstorealbum_totalfiles-1, gstorealbum_totalspace=gstorealbum_totalspace-'{$gstoremedia_info['gstoremedia_filesize']}' WHERE gstorealbum_id='{$gstoremedia_info['gstoremedia_gstorealbum_id']}' LIMIT 1";
      $database->database_query($sql);
	  }
    
	  // IF DELETE CLAUSE IS NOT EMPTY, DELETE gstore MEDIA
	  if($gstoremedia_delete != "") { $database->database_query("DELETE FROM se_gstoremedia WHERE ($gstoremedia_delete)"); }
	}
  
  // END gstore_media_delete() METHOD
}

?>