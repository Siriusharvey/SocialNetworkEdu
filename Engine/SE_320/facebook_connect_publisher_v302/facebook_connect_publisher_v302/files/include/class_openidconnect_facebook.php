<?php

define('OPENIDCONNECT_FACEBOOK_PIC_SQUARE_DEFAULT','http://static.ak.fbcdn.net/pics/q_silhouette.gif');


// local service
// TBD: singleton?
class openidfacebook extends openidapi {

  var $facebook_api_key;
  var $facebook_secret;

  var $user_details = array();
  
  var $api_client = null;
  

  function openidfacebook() {
    global $url, $folder;

    if($folder == "base") {
        $serverpath = ".";
    } else {
        $serverpath = "..";
    }

    if(defined('SE_ROOT')) {
      $serverpath = SE_ROOT;
    }

    $this->facebook_api_key = semods::get_setting('openidconnect_facebook_api_key');
    $this->facebook_secret = semods::get_setting('openidconnect_facebook_secret');

    //$this->api_client = new Facebook($this->facebook_api_key, $this->facebook_secret);

    require_once( $serverpath . '/include/facebook/facebook.php' );
    
    
    $this->session = 'fbsession';
    
  }
  
  
  function verify_api_keys() {

	$facebook = $this->api_client();

    // no need for session key
    $session_key = $facebook->api_client->session_key;
	$facebook->api_client->session_key = null;

    try {

      $result = $facebook->api_client->admin_getAppProperties( array('app_id') );

    } catch (Exception $ex) {
      
      $this->error_message = $ex->getMessage();
      
      // TBD: watch for API_EC_UNKNOWN, API_EC_SERVICE, etc
      return false;
      
    }
    
	$facebook->api_client->session_key = $session_key;
    
    return true;
      
  }
  
  function &api_client() {
    if($this->api_client == null) {
      $this->api_client = new Facebook($this->facebook_api_key, $this->facebook_secret);
    }
    
    return $this->api_client;
  }



  /*** LOCAL FUNCTIONS ***/
  

  /*** REMOTE FUNCTIONS ***/
  
  /*
   * TODO: if no session - show "login via" form. now - just redirect to login
   *
   */
  //function require_login() {
    
    //if(($this->session == null) || !$this->get_user_details()){
      //semods::redirect('login.php');
    //}
    
    
    //if(($this->session == null) || !$this->get_user_details()){
      //semods::redirect('login.php');
    //}
    
    //return isset($this->user_details['user_id']) && !empty($this->user_details['user_id']);
  //}

  function get_user_details() {

    $facebook = $this->api_client();
    
    
    // try to load cached values
    if(isset($_SESSION['openid_imported_fields'])) {
      
      $user_details = $_SESSION['openid_imported_fields'];
      
    } else {

      $this->user_id = $facebook->get_loggedin_user();

      // no user
      if(is_null($this->user_id)) {
        return false;
      }
    
      /*** IMPORT FB USER DETAILS ***/
      
      $fields = array('first_name', 'last_name', 'name', 'birthday', 'sex',
                      'about_me', 'activities', 'interests', 'movies', 'music', 'quotes',
                      'books', 'hometown_location', 'pic_big', 'political', 'timezone', 'tv', 'profile_url',
                      'status','current_location', 'meeting_for','meeting_sex','relationship_status','religion',
                      'pic','pic_square', 'pic_square_with_logo'
                      );

      try {    

        $fb_user_details = $facebook->api_client->users_getInfo($this->user_id, $fields);
        
      } catch (Exception $ex) {
        
        // session expired
        if($ex->getCode() == 102) {
  
          // clear all FB session cookies
          $cookies = array('user', 'session_key', 'expires', 'ss');
          foreach ($cookies as $name) {
            setcookie($this->facebook_api_key . '_' . $name, false, time() - 3600);
            unset($_COOKIE[$this->facebook_api_key . '_' . $name]);
          }
          setcookie($this->facebook_api_key, false, time() - 3600);
          unset($_COOKIE[$this->facebook_api_key]);
          
        }
        
        return false;

      }
  

      /*** REFACTOR FIELDS ***/
      
      $user_details = $fb_user_details[0];  
     
      $user_details['meeting_for'] = is_array($user_details['meeting_for']) ? implode(',',$user_details['meeting_for']) : '';
      $user_details['meeting_sex'] = is_array($user_details['meeting_sex']) ? implode(',',$user_details['meeting_sex']) : '';
      
      // hometown_location
      if(semods::g($user_details,'hometown_location','') != '') {
        $hometown_location = $user_details['hometown_location'];
        $user_details['hometown_location_city'] = semods::g($hometown_location,'city','');
        $user_details['hometown_location_state'] = semods::g($hometown_location,'state','');
        $user_details['hometown_location_country'] = semods::g($hometown_location,'country','');
        $user_details['hometown_location_zip'] = semods::g($hometown_location,'zip','');
      }
      unset($user_details['hometown_location']);
  
      // current_location
      if(semods::g($user_details,'current_location','') != '') {
        $current_location = $user_details['current_location'];
        $user_details['current_location_city'] = semods::g($current_location,'city','');
        $user_details['current_location_state'] = semods::g($current_location,'state','');
        $user_details['current_location_country'] = semods::g($current_location,'country','');
        $user_details['current_location_zip'] = semods::g($current_location,'zip','');
      }
      unset($user_details['current_location']);
      
      $user_details['user_id'] = $this->user_id;
      
      $user_details['openid_service_id'] = 1;
      
      // auto generate username from first / last
      $user_details['nickname'] = $user_details['first_name'] . $user_details['last_name'];
      
      // cache
      $_SESSION['openid_imported_fields'] = $user_details;
    }

    $this->user_details = $user_details;

    return true;
  }



  function get_loggedin_user() {
    $facebook = $this->api_client();

    $user_id = null;
    
    try {    
      
      $user_id = $facebook->get_loggedin_user();
      
    } catch (Exception $ex) {

      
    }
    
    return is_null($user_id) ? 0 : $user_id;
    
  }
  
  // mutual friends ?
  // http://wiki.developers.facebook.com/index.php/Friends.getMutualFriends
  function get_linked_friends($start = 0, $limit = 10, $orderby = "", $mutual_facebook_user_id = 0) {

    $facebook = $this->api_client();
    
    $fb_current_user = $facebook->get_loggedin_user();
    
    $fql = "SELECT uid, pic_square, pic_square_with_logo FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = {$fb_current_user}) AND has_added_app = 1";

    $friends = array();
    $users = array();
    
    try {    

      $result = $facebook->api_client->fql_query($fql);
      
      if (is_array($result) && count($result)) {
        foreach ($result as $friend) {
          $friends[] = $friend['uid'];
          $friends_indexed[$friend['uid']] = $friend;
        }
      }
      
    } catch (Exception $ex) {
      
      // session expired or else
      
    }

    if (is_array($friends) && count($friends)) {
      $friends_for_query = implode(',',$friends);
      
      $facebook_service_id = openidconnect_get_service_id('facebook');
      $sql = "SELECT *
              FROM se_semods_usersopenid O
              JOIN se_users U
                ON O.openid_user_id = U.user_id
              WHERE O.openid_user_key IN ($friends_for_query)
                AND O.openid_service_id = $facebook_service_id
                AND U.user_enabled = 1
                AND U.user_verified = 1
                LIMIT $start, $limit";
                
                // ORDER BY ?
                
      $rows = new semods_db_iterator_assoc($sql);
      while($row = $rows->next()) {
        semods_utils::create_user_displayname_ex($row);
        semods_utils::create_user_photo($row, './images/nophoto.gif', true);
        
        $row['user_openid_thumb'] = ($friends_indexed[$row['openid_user_key']]['pic_square_with_logo'] != '' ? $friends_indexed[$row['openid_user_key']]['pic_square_with_logo'] : OPENIDCONNECT_FACEBOOK_PIC_SQUARE_DEFAULT);
        $row['user_openid_uid'] = $friends_indexed[$row['openid_user_key']]['uid'];
        
        $users[] = $row;
      }
    }

    return $users;
    
  }
  

  function get_unlinked_friends($max = 0) {

    $facebook = $this->api_client();
    
    $fb_current_user = $facebook->get_loggedin_user();
    
    $fql = "SELECT uid, name, pic_square, pic_square_with_logo FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = {$fb_current_user}) AND has_added_app = 0";

    $users = array();
    
    try {    

      $result = $facebook->api_client->fql_query($fql);

      if (is_array($result) && count($result)) {
        $users = $result;
      }
      
    } catch (Exception $ex) {
      
      // session expired or else

      
    }
      
    if($max != 0) {
      $users = array_slice($users, 0, $max);
    }
    
    // fill empty pics
    foreach($users as $key => $fb_user) {
      if($fb_user['pic_square'] == '') {
        $fb_user['pic_square'] = $fb_user['pic_square_with_logo'] = OPENIDCONNECT_FACEBOOK_PIC_SQUARE_DEFAULT;
        $users[$key] = $fb_user;
      }
    }
    
    return $users;
    
  }
  

  function get_linked_friends_stats() {

    $facebook = $this->api_client();
    
    $fb_current_user = $facebook->get_loggedin_user();

    $fql = "SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = {$fb_current_user}) AND has_added_app = 1";

    try {    

      $result = $facebook->api_client->fql_query($fql);
      
      $connected_friends = count($result);

      $friends = $facebook->api_client->friends_get();
      $friends_count = count($friends);
      
      $unconnected_friends = $friends_count - $connected_friends;
      
      //$unconnected_friends = $facebook->api_client->getUnconnectedFriendsCount();
      //$unconnected_friends = (int)$facebook->api_client->call_method('facebook.connect.getUnconnectedFriendsCount', array());
      
      
    } catch (Exception $ex) {

      return false;

    }
  
    
    
    return array('connected_friends'  => $connected_friends,
                 'unconnected_friends' => $unconnected_friends
                );
    
  }
  
  function hasPermission($permission) {

    $facebook = $this->api_client();

    try {    

      $result = $facebook->api_client->users_hasAppPermission($permission);
      
    } catch (Exception $ex) {
      
      $result = false;

    }
    
    return $result;
    
  }
  
  function get_signup_landing_page() {
    return 'user_openid_invite_facebook.php';
  }
  
}




?>