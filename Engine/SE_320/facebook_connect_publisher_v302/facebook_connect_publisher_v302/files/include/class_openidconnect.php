<?php


class se_user_openid extends se_user {
  
  var $openidapi;

  // get from db
  var $valid_services = array('api','facebook','myspace','live','linkedin','hyves');

  //function se_user_openid( $openid_user = null, $openid_session = null ) {
  function se_user_openid( $service = 'api' ) {

    $class_name = 'openid' . $service;
    
    if(!in_array($service, $this->valid_services) || !class_exists($class_name) ) {
      $service = 'api';
    }

    $class_name = 'openid' . $service;
   
    $this->openidapi = new $class_name();

    //if( $openid_user && $openid_session) {
    //  $this->openidapi->set_user( $openid_user, $openid_session );
    //}

    parent::se_user( array(0) ) ;

  }



  function user_create_from_openid($signup_email, $signup_username, $signup_timezone, $signup_language, $signup_cat, $profile_field_query) {
    global $database, $setting, $url, $actions, $field;

    // generate random password
    $signup_password = randomcode(10);
    
    // TODO: admin setting it to force/not
    // email verification cheat
    //$setting_signup_verify = $setting['setting_signup_verify'];
    $setting['setting_signup_verify'] = 0;
    
    // Signup
    $this->user_create( $signup_email, $signup_username, $signup_password, $signup_timezone, $signup_language, $signup_cat, $profile_field_query );

    // Link OpenID Information
    //$database->database_query( "INSERT INTO se_semods_usersopenid (openid_user_id,openid_user_key,openid_service_id) VALUES ({$this->user_info[user_id]}, '{$this->openid_user_id}', {$this->openid_service_id})" );
    
    $this->user_openid_link($this->user_info['user_id']);
  
    // INSERT ACTION IF VERIFICATION NOT NECESSARY
	//if( !$setting['setting_signup_verify'] ) {
    // delete all "signup" actions for this user, since they are replaced with signup_openid
    //  $actions->actions_add($this, "signup_facebook", Array($this->user_info['user_username'], $this->user_displayname), Array(), 0, false, "user", $this->user_info['user_id'], $this->user_info['user_privacy']);
    //}

    // download profile photo if set
    if(semods::g($this->openidapi->user_details,'pic_big','') != '') {
      $this->download_profile_photo( $this->openidapi->user_details['pic_big'] );
    }

    // Refresh user to load all the stuff including salt, the most important
    $this->se_user(array($this->user_info['user_id']));

  }
  
  function user_openid_link($user_id) {
    
    //semods::db_query( "INSERT INTO se_semods_usersopenid (openid_user_id,openid_user_key,openid_service_id) VALUES ({$this->user_info[user_id]}, '{$this->openid_user_id}', {$openid_service_id})" );
    semods::db_query( "INSERT INTO se_semods_usersopenid (openid_user_id,openid_user_key,openid_service_id) VALUES ({$user_id}, '{$this->openid_user_id}', {$this->openid_service_id})" );
    
  }

  function user_openid_unlink($user_id, $openid_service) {

    $openid_service = openidconnect_get_service_id($openid_service);
    
    if($openid_service == 0) {
      return false;
    }
    
    semods::db_query( "DELETE FROM se_semods_usersopenid WHERE openid_user_id = {$user_id} AND openid_service_id = {$openid_service}" );
    
  }
  
  // static
  function user_openid_is_connected($user_id, $openid_service) {

    return se_user_openid::user_openid_get_userid($user_id, $openid_service) != 0;

  }
  
  function user_openid_displayname() {

    $name = semods::g($this->openidapi->user_details,'name','');

    if($name == '') {

      $name = semods::g($this->openidapi->user_details,'first_name','') . ' ' . semods::g($this->openidapi->user_details,'last_name','');
      $name = trim($name);

    }
    
    return $name;

  }

  function user_openid_photo() {
    
    return semods::g($this->openidapi->user_details,'pic_big','');
    
  }

  function user_openid_thumb() {
    
    $photo = semods::g($this->openidapi->user_details,'pic_square','');
    if($photo == '') {
      $photo = semods::g($this->openidapi->user_details,'pic_big','');
    }
    
    return $photo;
    
  }


  // static
  // cache to $_SESSION?
  function user_openid_get_userid($user_id, $openid_service) {

    $openid_service = openidconnect_get_service_id($openid_service);
    
    if($openid_service == 0) {
      return 0;
    }
    
    return semods::db_query_count( "SELECT openid_user_key FROM se_semods_usersopenid WHERE openid_user_id = '{$user_id}' AND openid_service_id = '{$openid_service}'" );
  }


  // static
  /*
   * Logged in to network and linked to current user
   *
   */
  function get_loggedin_user() {
    return $this->openidapi->get_loggedin_user();
  }


  //function user_login_openid($openid_session = null) {
  function user_login_openid($redirect_to_login = true, $full_login = true) {
    global $database, $setting, $class_user;

    if(!$this->openidapi->require_login($redirect_to_login ? 'login.php' : null) ) {
      $this->is_error = 1;
      return false;
    }

    $this->openid_user_id = $database->database_real_escape_string( $this->openidapi->user_details['user_id'] );
    $this->openid_service_id = $database->database_real_escape_string( $this->openidapi->user_details['openid_service_id'] );

    $userid = semods::db_query_count( "SELECT openid_user_id
                                       FROM se_semods_usersopenid
                                       WHERE openid_user_key = '{$this->openid_user_id}'
                                         AND openid_service_id = '{$this->openid_service_id}'
                                         " );

    if($userid == 0) {
      $this->is_error = 1;
      return false;
    }

    $this->se_user(Array($userid));
    
    if(!$this->user_exists) {
      $this->is_error = 1;
      return false;
    }
    
    if(!$full_login) {
      return;
    }
    
    $current_time = time();
    $login_result = 0;

    if($this->user_exists == 0) {
      $this->is_error = 676;

    // CHECK IF USER IS ENABLED
    } elseif($this->user_info[user_enabled] == 0) {
      $this->is_error = 677;

	  // CHECK IF EMAIL IS VERIFIED - TBD: setting, verify or not email for openid users
//    } elseif( !$this->user_info['user_verified'] && $setting['setting_signup_verify'] ) {
//	    $this->is_error = 678;

    // INITIATE LOGIN AND ENCRYPT COOKIES
    } else {

      // SET LOGIN RESULT VAR
      $login_result = 1;

      // UPDATE USER LOGIN INFO
      $database->database_query("UPDATE se_users SET user_lastlogindate='{$current_time}', user_logins=user_logins+1, user_lastactive='{$current_time}', user_ip_lastactive='{$_SERVER['REMOTE_ADDR']}' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");

      // LOG USER IN
      //$this->user_setcookies($persistent);
      $this->user_setcookies(true);

      // FIX VISITOR TABLE
      $visitor_ip = ip2long($_SERVER['REMOTE_ADDR']);
      $visitor_browser = addslashes(substr($_SERVER['HTTP_USER_AGENT'], 0, 255));
      $sql = "DELETE FROM se_visitors WHERE visitor_ip='{$visitor_ip}' && visitor_browser='{$visitor_browser}' && visitor_user_id='0'";
      $database->database_query($sql);
      setcookie("se_user_lastactive", time() - 3600, 0, "/");

      // UPDATE LOGIN STATS
      update_stats("logins");
      
      $email = $this->user_info['user_email'];
      $database->database_query("INSERT INTO se_logins (login_email, login_date, login_ip, login_result) VALUES ('$email', '$current_time', '".$_SERVER['REMOTE_ADDR']."', '$login_result')");
      bumplog();

    }

    // BUMP LOG
//    $database->database_query("INSERT INTO se_logins (login_email, login_date, login_ip, login_result) VALUES ('$email', '$current_time', '".$_SERVER['REMOTE_ADDR']."', '$login_result')");
//    bumplog();

  }



  function download_profile_photo( $photo_url ) {
    global $url;

    $file_dest = tempnam( "tmp", $this->user_info['user_id'] );
    
    // download remote file
    if(function_exists('curl_init')) {

      $result = $this->download_profile_photo_curl($photo_url, $file_dest);

    } else {

      $result = $this->download_profile_photo_fopen($photo_url, $file_dest);
      
      if(!$result) {
        $result = $this->download_profile_photo_sockets($photo_url, $file_dest);
      }
      
    }

    if(!$result) {
      return false;
    }

    // "upload" photo
    $photo_file_size = 1000;
    $photo_name = 'photo';
    
    // photo upload nasty hack
    
    $_FILES[$photo_name]['name'] = basename($photo_url);
    $_FILES[$photo_name]['type'] = 'image/jpg';
    $_FILES[$photo_name]['tmp_name'] = $file_dest;
    $_FILES[$photo_name]['size'] = $photo_file_size;
    $_FILES[$photo_name]['error'] = '';
    
    $this->user_photo_upload_ex($photo_name);

  }
  
  
  
  function download_profile_photo_curl($photo_url, $file_dest) {

    $ch = curl_init( $photo_url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    //curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );

    $response = curl_exec($ch);
    if(curl_errno($ch) != 0) {
      return false;
    }

    file_put_contents( $file_dest, $response );
    return true;
    
  }


  function download_profile_photo_fopen($photo_url, $file_dest) {

    $fp = @fopen( $photo_url, 'r' );
    if (!$fp) {
      return false;
    }
    
    $response = @stream_get_contents($fp);
    if( $response === false ) {
      return false;
    }

    file_put_contents( $file_dest, $response );
    fclose( $fp );
    return true;

  }



  function download_profile_photo_sockets($photo_url, $file_dest) {

    // url MUST have scheme
    $start = strpos( $photo_url, '//' ) + 2;
    $end = strpos( $photo_url, '/', $start );
    $host = substr( $photo_url, $start, $end - $start );
    $post_path = substr( $photo_url, $end );
    $fp = @fsockopen( $host, 80 );
    if (!$fp) {
      return false;
    }
    fputs( $fp, "GET $post_path HTTP/1.0\n" .
                "Host: $host\n" .
                'User-Agent: HTTP Client 1.0 (non-curl) '. phpversion() . "\n\n"
                );
    $response = '';
    while(!feof($fp)) {
        $response .= fgets($fp, 4096);
    }
    fclose ($fp);
    // get response code
    preg_match( '/^\S+\s(\S+)/', $response, $matches );
    if( $matches[1] != "200" ) {
      return false;
    }
    // get response body
    preg_match( '/\r?\n\r?\n(.*?)$/sD', $response, $matches );
    $response = $matches[1];

    file_put_contents( $file_dest, $response );
    return true;

  }


  // 2 changes from original function
  function user_photo_upload_ex($photo_name) {
    global $database, $url;

    // ENSURE USER DIRECTORY IS ADDED
    $user_directory = $url->url_userdir($this->user_info['user_id']);
    $user_path_array = explode("/", $user_directory);
    array_pop($user_path_array);
    array_pop($user_path_array);
    $subdir = implode("/", $user_path_array)."/";
    if( !is_dir($subdir) ) {
      mkdir($subdir, 0777);
      chmod($subdir, 0777);
      $handle = fopen($subdir."index.php", 'x+');
      fclose($handle);
    }
    
    if( !is_dir($user_directory) ) {
      mkdir($user_directory, 0777);
      chmod($user_directory, 0777);
      $handle = fopen($user_directory."/index.php", 'x+');
      fclose($handle);
    }

    // SET KEY VARIABLES
    $file_maxsize = "4194304";
    $file_exts = explode(",", str_replace(" ", "", strtolower($this->level_info[level_photo_exts])));
    $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
    $file_maxwidth = $this->level_info[level_photo_width];
    $file_maxheight = $this->level_info[level_photo_height];
    $photo_newname = "0_".rand(1000, 9999).".jpg";
    $file_dest = $url->url_userdir($this->user_info[user_id]).$photo_newname;
    $thumb_dest = substr($file_dest, 0, strrpos($file_dest, "."))."_thumb".substr($file_dest, strrpos($file_dest, "."));

    $new_photo = new se_upload();
    $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);

    //-------------------------------
    // 1/2 ignore is_uploaded_file
    //-------------------------------
    if($new_photo->is_error == 718) {
      $new_photo->is_error = 0;
    }
    //-------------------------------

    // UPLOAD AND RESIZE PHOTO IF NO ERROR
    if($new_photo->is_error == 0) {

      // DELETE OLD AVATAR IF EXISTS
      $this->user_photo_delete();

      // UPLOAD THUMB
      $new_photo->upload_thumb($thumb_dest);

      // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
      if($new_photo->is_image == 1) {
        $new_photo->upload_photo($file_dest);
      } else {

        //$new_photo->upload_file($file_dest);

        //-------------------------------
        // 2/2 move local file
        //-------------------------------
        rename($new_photo->file_tempname,$file_dest);
        chmod($file_dest, 0777);
        //-------------------------------

      }

      // UPDATE USER INFO WITH IMAGE IF STILL NO ERROR
      if($new_photo->is_error == 0) {
        $database->database_query("UPDATE se_users SET user_photo='$photo_newname' WHERE user_id='".$this->user_info[user_id]."'");
        $this->user_info[user_photo] = $photo_newname;
      }
    }

    $this->is_error = $new_photo->is_error;

  }
 


  function map_imported_fields($signup_cat) {
    global $setting;
    
    // check if service wants to import data
    $service_info = semods::db_query_assoc("SELECT * FROM se_semods_openidservices WHERE openidservice_id = {$this->openid_service_id}");
    
    if(($service_info === false) || ($service_info['openidservice_import_profiledata'] != 1)) {
      return;
    }
    
    // load fields map
    $fields_remap = semods::db_query_assoc_all("SELECT * FROM se_semods_openidfieldmap WHERE openidfieldmap_cat_id = $signup_cat");
    
    foreach($fields_remap as $field_remap) {
      $field_key = 'field_' . $field_remap['openidfieldmap_field_id'];
      $field_value = semods::g( $this->openidapi->user_details, $field_remap['openidfieldmap_name'],'');
      
      // handle special fields
      switch($field_remap['openidfieldmap_name']) {

        case 'birthday':
          // ex: 'January 1, 1900'
          if($field_value != '') {
            $field_value = strtotime( $field_value );
            $month = date('n', $field_value);
            $day = date('j', $field_value);
            $year = date('Y', $field_value);
            
            if(empty($_POST[$field_key."_1"])) {
              $_POST[$field_key."_1"] = $day;
            }
          
            if(empty($_POST[$field_key."_2"])) {
              $_POST[$field_key."_2"] = $month;
            }
  
            if(empty($_POST[$field_key."_3"])) {
              $_POST[$field_key."_3"] = $year;
            }
          }
          break;
        
        
        case 'sex':
          // must be!
          // male ==> 1
          // female ==> 2
          if(!empty($field_value)) {
            $field_value = (strtolower($field_value) == 'male') ? 1 : 2;
            if(empty($_POST[$field_key])) {
              $_POST[$field_key] = $field_value;
            }
          }
          break;

        
        default:
          if(empty($_POST[$field_key])) {
            $_POST[$field_key] = $field_value;
          }
        
      }
    }
    
  }

  
  /*** network specific functions ***/
  
  function hasPermission($permission) {
    return $this->openidapi->hasPermission($permission);
  }

  function get_linked_friends($start = 0, $limit = 10, $orderby = "") {
    return $this->openidapi->get_linked_friends($start, $limit, $orderby);
  }

  function get_unlinked_friends($max = 0) {
    return $this->openidapi->get_unlinked_friends($max);
  }
  
  function get_linked_friends_stats() {
    return $this->openidapi->get_linked_friends_stats();
  }
  
  function get_signup_landing_page() {
    return $this->openidapi->get_signup_landing_page();
  }
  
  

}






class openidapi {

  var $api_key;
  var $secret;

  var $session;
  var $user_details = array();
  var $user_id = null;
  
  var $server_addr = "http://www.openidgo.com/restserver.php";
  

  function openidapi() {
    $this->api_key = semods::get_setting('openidconnect_api_key');
    $this->secret = semods::get_setting('openidconnect_secret');
    
    $this->session = semods::getpost('openidsession');
  }



  /*** LOCAL FUNCTIONS ***/
  


  /*** REMOTE FUNCTIONS ***/
  
  /*
   * TODO: if no session - show "login via" form. now - just redirect to login
   *
   */
  function require_login($redirect = null) {
    if(($this->session == null) || !$this->get_user_details()){
      if(!empty($redirect)) {
        //semods::redirect('login.php');
        semods::redirect($redirect);
      } else {
        return false;
      }
    }
    
    return isset($this->user_details['user_id']) && !empty($this->user_details['user_id']);
  }


  function is_logged_in() {
    if(($this->session == null) || !$this->get_user_details()){
      return false;
    }
    
    return true;
  }

  function get_user_details() {

    // try to load cached values
    if(isset($_SESSION['openid_imported_fields'])) {
      
      $result = $_SESSION['openid_imported_fields'];
      
    } else {
    
      $result = $this->call_method
            ('getUserDetails',
             array('session' => $this->session
                   )
             );
  
      // error occured
      if($result === null) {
          return false;
      }
      
      // cache
      $_SESSION['openid_imported_fields'] = $result;
    }

    $this->user_details = $result;

    return true;
  }

  
  // prototype
  function hasPermission($permission) {
    return false;
  }

  // prototype
  function get_loggedin_user() {
    return 0;
  }
  
  // prototype
  function get_linked_friends($start = 0, $limit = 10, $orderby = "") {
    return array();
  }

  // prototype
  function get_unlinked_friends($max = 0) {
    return array();
  }

  // prototype
  function get_linked_friends_stats() {
    return array();
  }

  function get_signup_landing_page() {
    return 'user_home.php';
  }



  /*** INTERNAL FUNCTIONS ***/

  function getErrorCode() {
    return $this->errID;
  }

  function getErrorMessage() {
    return $this->errMsg;
  }

  function clearError() {
    $this->errID = 0;
    $this->errMsg = '';
  }

  function call_method($method, $params) {
    $this->clearError();
    $xml = $this->post_request($method, $params);
    if(is_null($xml))
      return null;
    
    $result = $this->load_and_parse_xml( $xml );
    if (is_array($result) && isset($result['error_code'])) {
        $this->errMsg = $result['error_msg'];
        $this->errID = $result['error_code'];
        return null;
    }
    return $result;
  }

  function load_and_parse_xml($xml) {
    if(function_exists('simplexml_load_string')) {
      $sxml = @simplexml_load_string($xml);
      return $this->convert_simplexml_to_array( $sxml );
    } else {

      include_once 'include/simplexml44-0_4_4/class/IsterXmlSimpleXMLImpl.php';

      $impl = new IsterXmlSimpleXMLImpl();
      $sxml = $impl->load_string($xml);
      $result = array();
      $children = $sxml->children();
      return $this->convert_simplexml44_to_array($children[0]);

    }


  }


  function post_request($method, $params) {
    $params['method'] = $method;
    $params['api_key'] = $this->api_key;
    if (!isset($params['v'])) {
      $params['v'] = '1.0';
    }

    $post_string = $this->convert_array_to_params( $params, true );

    // Use CURL if installed
    if (function_exists('curl_init'))
      return $this->post_request_with_curl( $post_string );
    else {
      $result = $this->post_request_without_curl( $post_string );

      // no url wrappers / allow_url_fopen is disabled
      if(($result == null) && ($this->errID == 1001)) {
        $this->errID = 0;
        $this->errMsg = '';
        $result = $this->post_request_without_curl_php4( $post_string );
      }
      
      return $result;
    }

  }

  function post_request_with_curl($data) {
      $ch = curl_init();

      curl_setopt( $ch, CURLOPT_URL, $this->server_addr );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt( $ch, CURLOPT_ENCODING, '' );
      curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );
      curl_setopt( $ch, CURLOPT_USERAGENT, 'OpenidGO PHP REST Client 1.0 (curl) ' . phpversion() );

      $result = curl_exec($ch);
      if(curl_errno($ch)) {
        $this->errMsg = 'HTTP Error: ' . curl_error( $ch );
        $this->errID = $API_E_HTTP;
        return null;
      }
      curl_close($ch);
      return $result;
  }

  function post_request_without_curl($data) {
      $context_opts =
        array('http' =>
              array('method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
                                'User-Agent: OpenidGO PHP REST Client 1.0 (non-curl) '. phpversion() . "\r\n" .
                                'Content-Length: ' . strlen($data),
                    'content' => $data));
      $context = stream_context_create($context_opts);
      $fp = @fopen($this->server_addr, 'r', false, $context);
      if (!$fp) {
        $this->errMsg = 'HTTP Error';
        $this->errID = 1001;
        return null;
      }
      $result = @stream_get_contents($fp);
      if( $result === false ) {
        $this->errMsg = 'HTTP Error';
        $this->errID = $API_E_HTTP;
        return null;
      }
      return $result;
  }

  function post_request_without_curl_php4($data) {
    // url MUST have scheme
	$start = strpos( $this->server_addr, '//' ) + 2;
	$end = strpos( $this->server_addr, '/', $start );
	$host = substr( $this->server_addr, $start, $end - $start );
	$post_path = substr( $this->server_addr, $end );
    $fp = @fsockopen( $host, 80 );
    if (!$fp) {
      $this->errMsg = 'HTTP Error';
      $this->errID = $API_E_HTTP;
      return null;
    }
    fputs( $fp, "POST $post_path HTTP/1.0\n" .
                "Host: $host\n" .
                'User-Agent: OpenidGO PHP REST Client 1.0 (non-curl) '. phpversion() . "\n" .
                "Content-Type: application/x-www-form-urlencoded\n" .
                "Content-Length: " . strlen($data) . "\n\n" .
                "$data\n\n" );
	$response = '';
	while(!feof($fp)) {
		$response .= fgets($fp, 4096);
	}
	fclose ($fp);
    // get response code
    preg_match( '/^\S+\s(\S+)/', $response, $matches );
    if( $matches[1] != "200" ) {
      $this->errMsg = 'HTTP Error';
      $this->errID = $API_E_HTTP;
      return null;
    }
    // get response body
    preg_match( '/\r?\n\r?\n(.*?)$/sD', $response, $matches );
    $response = $matches[1];
	return $response;
  }

  function convert_array_to_params($params, $addSig = false) {
    $post_params = array();
    foreach ($params as $key => $val) {
      if (is_array($val)) $val = implode(',', $val);
      $post_params[] = $key.'='.urlencode($val);
    }
    if($addSig) {
        $secret = $this->secret;
        $post_params[] = 'sig='.$this->generate_sig($params, $secret);
    }

    return implode('&', $post_params);
  }

  function convert_simplexml_to_array($sxml) {
    $arr = array();
    if ($sxml) {
      foreach ($sxml as $k => $v) {
        if ($sxml['list']) {
          $arr[] = $this->convert_simplexml_to_array($v);
        } else {
          $arr[$k] = $this->convert_simplexml_to_array($v);
        }
      }
    }
    if (sizeof($arr) > 0) {
      return $arr;
    } else {
      return (string)$sxml;
    }
  }

  function convert_simplexml44_to_array($sxml) {
    if ($sxml) {
      $arr = array();
      $attrs = $sxml->attributes();
      foreach ($sxml->children() as $child) {
        if (!empty($attrs['list'])) {
          $arr[] = $this->convert_simplexml44_to_array($child);
        } else {
          $arr[$child->___n] = $this->convert_simplexml44_to_array($child);
        }
      }
      if (sizeof($arr) > 0) {
        return $arr;
      } else {
        return (string)$sxml->CDATA();
      }
    } else {
      return '';
    }
  }

  function generate_sig($params_array, $secret) {
    $str = '';
    ksort($params_array);
    foreach ($params_array as $k=>$v) {
      $str .= "$k=$v";
    }
    $str .= $secret;
    return md5($str);
  }

  function encrypt(&$data) {
    return $this->crypt_internal($data);

    if( extension_loaded('mcrypt') )
        return $this->crypt_mcrypt($data);
    else
        return $this->crypt_internal($data);
  }

  function crypt_mcrypt(&$data) {
    $data = bin2hex( mcrypt_ecb (MCRYPT_BLOWFISH, $this->secret, $data, MCRYPT_ENCRYPT) );
    return 1;
  }

  function crypt_internal(&$data) {

    $key = $this->secret;
    $s = array();
    $len= strlen($key);
    for ($i = 0; $i < 256; $i++) {
        $s[$i] = $i;
    }

    $j = 0;
    for ($i = 0; $i < 256; $i++) {
        $j = ($j + $s[$i] + ord($key[$i % $len])) % 256;
        $t = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $t;
    }
    $i = $j = 0;

    $len= strlen($data);
    for ($c= 0; $c < $len; $c++) {
        $i = ($i + 1) % 256;
        $j = ($j + $s[$i]) % 256;
        $t = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $t;

        $t = ($s[$i] + $s[$j]) % 256;

        $data[$c] = chr(ord($data[$c]) ^ $s[$t]);
    }
    // required?
    $data = bin2hex($data);
    return 2;
  }

  function getErrorDescription($error_id) {
    if(is_empty($this->api_error_descriptions))
      $this->api_error_descriptions = array(
        $this->API_E_HTTP                      => 'HTTP Error',
        $this->API_E_HTTP_FOPEN                => 'HTTP Error',
        $this->API_E_SUCCESS                   => 'Success',
        $this->API_E_UNKNOWN                   => 'Unknown error occurred',
        $this->API_E_METHOD                    => 'Unknown method',
        $this->API_E_SIGNATURE                 => 'Signature verification failed',
        $this->API_E_PARAMS                    => 'Incomplete/Invalid parameters received',
        $this->API_E_API_KEY                   => 'Invalid API key',
        $this->API_E_TOO_MANY_CALLS            => 'Request limit reached',
        $this->API_E_BAD_IP                    => 'Unauthorized IP address',
        $this->API_E_NO_SERVICE                => 'Service temporarily unavailable',
        $this->API_E_NOT_SUBSCRIBED            => 'Not subscribed for this service',

        $this->API_E_INVALID_SESSION           => 'Invalid Session',
      );

      return $this->api_error_descriptions[$error_id];
  }


  /* Error codes and descriptions */


  var $API_E_SUCCESS = 0;

  var $API_E_HTTP = 1000;
  var $API_E_HTTP_FOPEN = 1001;

  /* Generic Errors */

  var $API_E_UNKNOWN = 1;
  var $API_E_METHOD = 2;
  var $API_E_SIGNATURE = 3;
  var $API_E_PARAMS = 4;
  var $API_E_API_KEY = 5;
  var $API_E_TOO_MANY_CALLS = 6;
  var $API_E_BAD_IP = 7;
  var $API_E_NO_SERVICE = 8;
  var $API_E_NOT_SUBSCRIBED = 9;

  /* Service Errors */

  var $API_E_INVALID_SESSION = 100;

  var $api_error_descriptions = array();
  
}



?>