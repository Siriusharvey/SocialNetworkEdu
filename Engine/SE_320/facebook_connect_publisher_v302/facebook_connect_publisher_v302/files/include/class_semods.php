<?php
/* 
 * SEMods static class
 *
 * Version 0.2
 *
 * Copyright (c) 2008 SocialEngineMods.Net
 *
 */

$semods_settings_cache = null;


// BACK TO SANE USER DELETION
SE_Hook::register("se_user_delete", "semods_hook_delete_user");

function semods_hook_delete_user( $params ) {
  global $global_plugins, $folder;

  // DELETE ALL PLUGIN OBJECTS RELATED TO THIS USER
  foreach($global_plugins as $key => $value) {

    // thanks for being uniform
    if( $folder == "admin" ) {
      $plugin_type = $value['plugin_type'];  
    } else {
      $plugin_type = $key;  
    }
    
    if(function_exists('deleteuser_'.$plugin_type)) {
      call_user_func_array('deleteuser_'.$plugin_type, array( $params ) ); 
    }
  }

}


class semods {
  // cached settings


  function g(&$var, $key, $default = null){ 
      return isset($var[$key]) ? $var[$key] : $default;
  }
  
  function get($key, $default = null)     { return semods::g($_GET, $key, $default);      }
  function session($key, $default = null) { return semods::g($_SESSION, $key, $default);  }
  function post($key, $default = null)    { return semods::g($_POST, $key, $default);     }
  function request($key, $default = null) { return semods::g($_REQUEST, $key, $default);  }
  
  function getpost($key, $default = null) { return isset($_GET[$key]) ? $_GET [$key] : (isset($_POST[$key]) ? $_POST[$key] : $default); }

  


  
  /* DATABASE */

  function db_exec($query) {
      global $database;
      
      return mysql_unbuffered_query($query, $database->database_connection);
  }

  function db_query($query) {
      global $database;
      
      return $database->database_query($query);
  }
  
  function db_query_array($query) {
      global $database;
      
      $result = $database->database_query($query);
      return $result ? $database->database_fetch_array($result) : false;
  }

  function db_query_array_all($query) {
      global $database;
    
      $items = array();
      $rows = $database->database_query($query);
      while($row = $database->database_fetch_array($rows)) {
        $items[] = $row;
      }
      return $rows ? $items : false;
  }
  
  function db_query_assoc($query) {
      global $database;
      
      $result = $database->database_query($query);
      return $result ? $database->database_fetch_assoc($result) : false;
  }

  function db_query_assoc_all($query) {
      global $database;
    
      $items = array();
      $rows = $database->database_query($query);
      while($row = $database->database_fetch_assoc($rows)) {
        $items[] = $row;
      }
      return $rows ? $items : false;
  }
  
  function db_query_count($query) {
      $dbr = semods::db_query_array($query );
      if($dbr === false)
          return 0;
      
      return $dbr[0];
  }

  function db_query_affected_rows($query) {
      global $database;

      $result = $database->database_query($query);
      return $result ? $database->database_affected_rows($database->database_connection) : false;
  }
  




  /* SETTINGS */


  function &get_settings() {
      global $semods_settings_cache;
      
      if(is_null($semods_settings_cache)) {
          $semods_settings_cache = semods::db_query_assoc( 'SELECT * FROM se_semods_settings' );
      }
      
      return $semods_settings_cache;
  }
  
  function get_setting($setting, $default_value = null) {
    $setting_key = 'setting_' . $setting;
    $settings = semods::get_settings();
    if($settings && isset($settings[$setting_key]))
      return $settings[$setting_key];
    
    return $default_value;
  }
  
  
  
  
  
  /* UTILITIES */

  function remove_array_empty_values($array, $remove_null_number = true) {
    $new_array = array();

    $null_exceptions = array();

    foreach ($array as $key => $value) {
      $value = trim($value);

      if($remove_null_number)
        $null_exceptions[] = '0';

      if(!in_array($value, $null_exceptions) && $value != "")
        $new_array[] = $value;
    }
    
    return $new_array;
  }
  
  function create_user_displayname( $user_id, $user_username, $user_fname, $user_lname ) {
    static $user = null;
    if(is_null($user))
      $user = new se_user();

    $user->user_info['user_id'] = $user_id;
    $user->user_info['user_username'] = $user_username;
    $user->user_info['user_fname'] = $user_fname;
    $user->user_info['user_lname'] = $user_lname;
    $user->user_displayname();
    
    return $user->user_displayname;
  }
  

  function get_language_text( $lang_var ) {
    if(!is_numeric($lang_var))
      return $lang_var;

    SE_Language::_preload( $lang_var );
    SE_Language::load();
    return SE_Language::_get( $lang_var );
  }
  
  function redirect( $location ) {
	header("Location: $location");
    exit();
  }
  
  function load_subnets() {
    global $database;
    
    // LOOP OVER SUBNETWORKS
    $subnets = $database->database_query("SELECT subnet_id, subnet_name FROM se_subnets ORDER BY subnet_name");
    $subnet_array[0] = Array('subnet_id' => 0, 'subnet_name' => 152);
    SE_Language::_preload(152);
    while($subnet_info = $database->database_fetch_assoc($subnets)) {
      $subnet_array[$subnet_info['subnet_id']] = array( 'subnet_id'   => $subnet_info[subnet_id],
                                                        'subnet_name' => $subnet_info[subnet_name]
                                                        );
      SE_Language::_preload( $subnet_info['subnet_name'] );
    }
    
    return $subnet_array;
    
  }
  
  function load_userlevels() {
    global $database;

    // LOOP OVER USER LEVELS
    $levels = $database->database_query("SELECT level_id, level_name FROM se_levels ORDER BY level_name");
    while($level_info = $database->database_fetch_assoc($levels)) {
      $level_array[$level_info['level_id']] = array( 'level_id'    => $level_info['level_id'],
                                                     'level_name'  => $level_info['level_name']
                                                    );
    }
    return $level_array;
  }
  
}

?>