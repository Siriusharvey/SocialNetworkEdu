<?php

class se_sms
{

	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		// CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;			// CONTAINS THE USER ID OF THE USER WHOSE BLOG WE ARE EDITING
	
	var $user_info;			// CONTAINS USER'S INFORMATION FROM SE_USERS TABLE
	
	var $level_info;		// CONTAINS USER'S INFORMATION FROM SE_LEVELS TABLE
	
	function se_sms($user_id = 0) {

	  $this->user_id = $user_id;

	} // END se_blog() METHOD


//start of address book
 function addressbook($direction=0, $where=NULL, $do_joins=FALSE)

  {

    global $database, $user;

    

    $message_total = 0;

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	 

    

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

        COUNT(id) as pm_total

      FROM

        se_addressbook

      WHERE

        se_addressbook.owner='{$user->user_info['user_username']}'

    ";
    // RUN QUERY AND RETURN

    $resource = $database->database_query($sql);

    $result = $database->database_fetch_assoc($resource);

    

    //return (int) $database->database_num_rows($resource);

    return (int) $result['pm_total'];

  }

//end of addressbook


//start of sms history
 function smsaddressbook($direction=0, $where=NULL, $do_joins=FALSE)

  {

    global $database, $user;

    

    $message_total = 0;

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	

    

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

        COUNT(id) as pm_total

      FROM

        se_sms

      WHERE

        username='{$user->user_info['user_username']}'

    ";
    // RUN QUERY AND RETURN

    $resource = $database->database_query($sql);

    $result = $database->database_fetch_assoc($resource);

    

    //return (int) $database->database_num_rows($resource);

    return (int) $result['pm_total'];

  }
//end of sms history

/////start of addressbook
 function &user_addressbook_list($start=NULL, $limit=NULL, $direction=0, $where=NULL)

  {

    global $database, $user;

    

	  $message_array = array();

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	 

    

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

       *

      FROM

        se_addressbook

      WHERE

        se_addressbook.owner='{$user->user_info['user_username']}'

    ";

   // EXECUTE QUERY

    $resource = $database->database_query($sql);

    

    // GET MESSAGES

	  while( $message_info=$database->database_fetch_assoc($resource) )

    {

      // CREATE AN OBJECT FOR MESSAGE AUTHOR/RECIPIENT

      $pm_user = new SEUser();

      $pm_user->user_info['id']        = $message_info['id'];

     $pm_user->user_info['nickname']  = $message_info['nickname'];

     $pm_user->user_info['full_name']     = $message_info['first']." ".$message_info['last'];

      $pm_user->user_info['email']     = $message_info['email'];

     $pm_user->user_info['phone']     = $message_info['phone'];
       $pm_user->user_info['grup ']     = $message_info['grup'];
      $pm_user->user_displayname();

      

      // Remove breaks for preview

      $message_info['pm_body'] = str_replace("<br>", "", $message_info['pm_body']);

      

      // SET MESSAGE ARRAY

      $message_array[] = array(

        'pmconvo_id'      => $message_info['id'],

        'pmconvo_nickname' => $message_info['nickname'],

        'pm_fullname'         => $message_info['first']." ".$message_info['last'],

        'pm_email'         => $message_info['email'],
		 'pm_body'         => $message_info['pm_body'],
         'pm_grup'      => $message_info['grup'],
        'pm_replied'      => $message_info['phone']

      );

      

      unset($pm_user);

    }

    

    return $message_array;

  }
//end of addressbook


//start of sms address list
 function &sms_addressbook_list($start=NULL, $limit=NULL, $direction=0, $where=NULL)

  {

    global $database, $user;

    

	  $message_array = array();

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	  

    

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

       *

      FROM

        se_sms

      WHERE

       username='{$user->user_info['user_username']}'

    ";

   // EXECUTE QUERY

    $resource = $database->database_query($sql);

    

    // GET MESSAGES

	  while( $message_info=$database->database_fetch_assoc($resource) )

    {

      // CREATE AN OBJECT FOR MESSAGE AUTHOR/RECIPIENT

      $pm_user = new SEUser();

      $pm_user->user_info['id']        = $message_info['id'];

     $pm_user->user_info['username']  = $message_info['username'];

    $pm_user->user_info['message']     = $message_info['message'];

    $pm_user->user_info['date']     = $message_info['date'];

     $pm_user->user_info['tono']     = $message_info['tono'];
        $pm_user->user_info['fromno']     = $message_info['fromno'];
      $pm_user->user_displayname();

      

      // Remove breaks for preview

      $message_info['pm_body'] = str_replace("<br>", "", $message_info['pm_body']);

      

      // SET MESSAGE ARRAY

      $message_array[] = array(

        'pmconvo_id'      => $message_info['id'],

        'pmconvo_username' => $message_info['username'],

        'pm_message'         => $message_info['message'],

        'pm_date'         => $message_info['date'],
		'pm_body'         => $message_info['pm_body'],
		 'pm_tono'         => $message_info['tono'],
		  
         'pm_fromno'      => $message_info['fromno']
       

      );

      

      unset($pm_user);

    }

    

    return $message_array;

  }
  //end of sms address list

//start of group list

 function &group_list($start=NULL, $limit=NULL, $direction=0, $where=NULL)

  {

    global $database, $user;

    

	  $message_array = array();

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	  

    

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

       *

      FROM

        se_groups

      WHERE

       owner='{$user->user_info['user_username']}'

    ";

   // EXECUTE QUERY

    $resource = $database->database_query($sql);

    

    // GET MESSAGES

	  while( $message_info=$database->database_fetch_assoc($resource) )

    {

      // CREATE AN OBJECT FOR MESSAGE AUTHOR/RECIPIENT

      $pm_user = new SEUser();

      $pm_user->user_info['id']        = $message_info['id'];

     $pm_user->user_info['grup']  = $message_info['grup'];

   $pm_user->user_info['owner']     = $message_info['owner'];

      $pm_user->user_displayname();

      

      // Remove breaks for preview

      $message_info['pm_body'] = str_replace("<br>", "", $message_info['pm_body']);

      

      // SET MESSAGE ARRAY

      $message_array[] = array(

        'pmconvo_id'      => $message_info['id'],

        'pmconvo_grup' => $message_info['grup'],

        'pm_owner'         => $message_info['owner'],
		'pm_body'         => $message_info['pm_body']
		

      );

      

      unset($pm_user);

    }

    

    return $message_array;

  }
  //end of sms address list
  
  // start of subscription list
 function &subscription_list($start=NULL, $limit=NULL, $direction=0, $where=NULL)

  {

    global $database, $user;

    

	  $message_array = array();

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	 $set_sql = "select currency_sign from se_global_sms where id='1'";
	 $set_resource = $database->database_query($set_sql);
     $set_result = $database->database_fetch_assoc($set_resource);
	 $currency_sign = $set_result[currency_sign];

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

       *

      FROM

        se_subscription

 ";

   // EXECUTE QUERY

    $resource = $database->database_query($sql);

    

    // GET MESSAGES

	  while( $message_info=$database->database_fetch_assoc($resource) )

    {

      // CREATE AN OBJECT FOR MESSAGE AUTHOR/RECIPIENT

      $pm_user = new SEUser();

      $pm_user->user_info['sno']        = $message_info['sno'];

     $pm_user->user_info['text']  = $message_info['text'];

    $pm_user->user_info['value']     = $message_info['value'];
	
	 $pm_user->user_info['sms_credit']     = $message_info['sms_credit'];
	 
      $pm_user->user_displayname();

      

      // Remove breaks for preview

      $message_info['pm_body'] = str_replace("<br>", "", $message_info['pm_body']);

      

      // SET MESSAGE ARRAY

      $message_array[] = array(

        'pmconvo_sno'      => $message_info['sno'],
		'pmconvo_destext' => $message_info['text'],
        'pmconvo_text' => $message_info['text']." - ".$currency_sign.$message_info['value']." For ".$message_info['sms_credit']." SMS ",
        'pm_value'         => $message_info['value'],
		'pm_body'         => $message_info['pm_body']     

      );

      

      unset($pm_user);

    }

    

    return $message_array;

  }

  //end of subscription list
  
  //start of subscription
 function subscription($direction=0, $where=NULL, $do_joins=FALSE)

  {

    global $database, $user;

    

    $message_total = 0;

    

	  // MAKE SURE MESSAGES ARE ALLOWED

	  

    

    // BEGIN MESSAGE QUERY

   $sql = "

      SELECT

        COUNT(id) as pm_total

      FROM

        subscription
 ";
    // RUN QUERY AND RETURN

    $resource = $database->database_query($sql);

    $result = $database->database_fetch_assoc($resource);

    

    //return (int) $database->database_num_rows($resource);

    return (int) $result['pm_total'];

  }
  //end of subscription
}  
?>