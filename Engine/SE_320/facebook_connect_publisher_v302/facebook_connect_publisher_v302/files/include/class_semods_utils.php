<?php
/* 
 * SEMods Utils static class
 *
 * Version 0.2
 *
 * Copyright (c) 2008 SocialEngineMods.Net
 *
 */


class semods_utils {
 
  
  /* UTILITIES */

  function create_user_displayname_ex( &$user_info ) {
    static $user = null;
    if(is_null($user))
      $user = new se_user();

    // don't want - passed by ref
    //$user->user_info = $user_info;
    
    $user->user_info['user_id'] = $user_info['user_id'];
    $user->user_info['user_username'] = $user_info['user_username'];
    $user->user_info['user_fname'] = $user_info['user_fname'];
    $user->user_info['user_lname'] = $user_info['user_lname'];
    $user->user_info['user_displayname'] = $user_info['user_displayname'];

    $user->user_displayname();
    
    $user_info['user_displayname'] = $user->user_displayname;
    $user_info['user_displayname_short'] = $user->user_displayname_short;

    return $user->user_displayname;
  }
    

  function create_user_photo( &$user_info, $nophoto_image = './images/nophoto.gif', $thumb = FALSE ) {
    static $user = null;
    if(is_null($user))
      $user = new se_user();
    
    $user->user_info['user_id'] = $user_info['user_id'];
    $user->user_info['user_username'] = $user_info['user_username'];
    $user->user_info['user_photo'] = $user_info['user_photo'];

    $user_info['user_photo'] = $user->user_photo($nophoto_image,$thumb);
    return $user_info['user_photo'];
  }
    
}




/*** DATABASE HELPERS ***/




class semods_db_iterator {
  var $resource = null;

  function semods_db_iterator( $sql ) {
    $this->resource = semods::db_query( $sql );
  }

}


class semods_db_iterator_assoc extends semods_db_iterator {

  function semods_db_iterator_assoc ( $sql ) {
    parent::semods_db_iterator( $sql );
  }

  function next() {
    global $database;

    return $database->database_fetch_assoc( $this->resource );
  }

}


function semods_db_query_assoc_all_indexed($query, $index, $preload_language = '') {
  
    $items = array();
    $rows = new semods_db_iterator_assoc($query);
    while($row = $rows->next()) {
      $items[$row[$index]] = $row;
      if($preload_language != '') {
        SE_Language::_preload($row[$preload_language]);
      }
    }
    return $items;
}







/*
 * singleton base class
 * 
 */

// who said php5 who who
class semods_singleton {

  function &getInstance($class_name) {
	static $instance;

    if (!isset($instance[$class_name])) {
      $instance[$class_name] = new $class_name;
    }

    return $instance[$class_name];
  }
  
}


?>