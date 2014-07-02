<?php

include_once "class_radcodes.php";


class se_badgeassignment
{
  var $user_id = NULL;
  var $badge_id = NULL;  

  var $badgeassignment_info = array();
  var $badgeassignment_exists = false;
  
  /**
   * @var se_user
   */
  var $badgeassignment_user;
  
  /**
   * @var se_badge
   */
  var $badgeassignment_badge;
  
	function se_badgeassignment($badgeassignment_id = NULL, $user_id = NULL, $badge_id = NULL)
	{
		global $database, $user, $owner;
		
		$this->user_id = $user_id;
		$this->badge_id = $badge_id;
		
		if ($badgeassignment_id)
		{
		  $sql = "SELECT * FROM se_badgeassignments  
		    JOIN se_badges ON se_badges.badge_id = se_badgeassignments.badgeassignment_badge_id
		    JOIN se_users ON se_users.user_id = se_badgeassignments.badgeassignment_user_id
		    LEFT JOIN se_badgecats ON se_badgecats.badgecat_id = se_badges.badge_badgecat_id
		    WHERE se_badgeassignments.badgeassignment_id = '$badgeassignment_id'
		  ";
		  $resource = $database->database_query($sql);
      if ($resource && $database->database_num_rows($resource))
      {
        $this->badgeassignment_exists = TRUE;
        $this->badgeassignment_info = $database->database_fetch_assoc($resource);
        
        $this->load_badgeassignment_user();
        $this->load_badgeassignment_badge();
      }  
  
		}
		
	}
  // se_badgeassignment
	
	function load_badgeassignment_user()
	{
		global $user, $owner;

		if (!$this->badgeassignment_user->user_exists)
		{
  		if ($this->badgeassignment_info['badgeassignment_user_id'] == $user->user_info['user_id'])
  		{
  		  $this->badgeassignment_user =& $user;
  		}
  		elseif ($this->badgeassignment_info['badgeassignment_user_id'] == $owner->user_info['user_id'])
      {
        $this->badgeassignment_user =& $owner;
      }
      elseif ($this->badgeassignment_info['user_id'] && $this->badgeassignment_info['user_username'])
      {
        $this->badgeassignment_user = rc_toolkit::init_se_user_from_data($this->badgeassignment_info);
      }
      else {
        $this->badgeassignment_user = new se_user(array($this->badgeassignment_info['user_id']));
      }
		}
    return $this->badgeassignment_user;
	}
	// load_badgeassignment_user
	
	
	function load_badgeassignment_badge()
	{
	  if (!$this->badgeassignment_badge->badge_exists)
	  {
  		if ($this->badgeassignment_info['badge_id'])
  		{
  		  $this->badgeassignment_badge = new se_badge();
  		  $this->badgeassignment_badge->preload_badge_info($this->badgeassignment_info);
  		}
  		else
  		{
  		  $this->badgeassignment_badge = new se_badge(NULL, $this->badgeassignment_info['badgeassignment_badge_id']);
  		}
	  }
		return $this->badgeassignment_badge;
	}
	// load_badgeassignment_badge
	
	
	function badgeassignment_total($where="", $details=0)
	{
    global $database;
    
/*
    $sql = "SELECT COUNT(badgeassignment_id) as total FROM se_badgeassignments 
        JOIN se_badges ON se_badges.badge_id = se_badgeassignments.badgeassignment_badge_id
        JOIN se_users ON se_users.user_id = se_badgeassignments.badgeassignment_user_id
        LEFT JOIN se_badgecats ON se_badgecats.badgecat_id = se_badges.badge_badgecat_id
        LEFT JOIN se_epaymenttransactions ON se_epaymenttransactions.epaymenttransaction_item_type = 'badgeassignment'
                  AND se_epaymenttransactions.epaymenttransaction_item_id = se_badgeassignments.badgeassignment_id
    ";
 */
 
    $sql = "SELECT COUNT(badgeassignment_id) as total FROM se_badgeassignments 
        JOIN se_badges ON se_badges.badge_id = se_badgeassignments.badgeassignment_badge_id
        JOIN se_users ON se_users.user_id = se_badgeassignments.badgeassignment_user_id
        LEFT JOIN se_badgecats ON se_badgecats.badgecat_id = se_badges.badge_badgecat_id

    ";
    
    if ($details) {
      $sql .= "
        LEFT JOIN 
        (
           SELECT MAX(epaymenttransaction_id) as max_epaymenttransaction_id, epaymenttransaction_item_id
           FROM se_epaymenttransactions
           WHERE epaymenttransaction_item_type = 'badgeassignment'
           GROUP BY epaymenttransaction_item_id
        ) as t2
         ON se_badgeassignments.badgeassignment_id = t2.epaymenttransaction_item_id
        LEFT JOIN se_epaymenttransactions j
          ON se_badgeassignments.badgeassignment_id = j.epaymenttransaction_item_id 
          AND t2.max_epaymenttransaction_id = j.epaymenttransaction_id
      ";
    }
    
    
    
    $cs = array($where);
    if ($this->user_id)
    {
      $cs['user'] = "se_badgeassignments.badgeassignment_user_id = '$this->user_id'";
    }
    if ($this->badge_id)
    {
      $cs['badge'] = "se_badgeassignments.badgeassignment_badge_id = '$this->badge_id'";
    }
    
    $sql .= " ".rc_toolkit::criteria_builder($cs,'AND',true);
    
    //rc_toolkit::debug($sql, 'badgeassignment_total');
    
    $res = $database->database_query($sql);
    $result = $database->database_fetch_assoc($res);

    return $result['total'] ? $result['total'] : 0;
	}
	// badgeassignment_total
	
	
	function badgeassignment_list($start, $limit, $sort_by="badgeassignment_datecreated DESC", $where="", $details=0)
	{
	  global $database;
	  /*
		$sql = "SELECT * FROM se_badgeassignments  
        JOIN se_badges ON se_badges.badge_id = se_badgeassignments.badgeassignment_badge_id
        JOIN se_users ON se_users.user_id = se_badgeassignments.badgeassignment_user_id
        LEFT JOIN se_badgecats ON se_badgecats.badgecat_id = se_badges.badge_badgecat_id
        LEFT JOIN se_epaymenttransactions ON se_epaymenttransactions.epaymenttransaction_item_type = 'badgeassignment'
                  AND se_epaymenttransactions.epaymenttransaction_item_id = se_badgeassignments.badgeassignment_id
        ";
		*/
    $sql = "SELECT * FROM se_badgeassignments  
        JOIN se_badges ON se_badges.badge_id = se_badgeassignments.badgeassignment_badge_id
        JOIN se_users ON se_users.user_id = se_badgeassignments.badgeassignment_user_id
        LEFT JOIN se_badgecats ON se_badgecats.badgecat_id = se_badges.badge_badgecat_id
        ";	
		
    if ($details) {
    $sql .= "
        LEFT JOIN 
        (
           SELECT MAX(epaymenttransaction_id) as max_epaymenttransaction_id, epaymenttransaction_item_id
           FROM se_epaymenttransactions
           WHERE epaymenttransaction_item_type = 'badgeassignment'
           GROUP BY epaymenttransaction_item_id
        ) as t2
         ON se_badgeassignments.badgeassignment_id = t2.epaymenttransaction_item_id
        LEFT JOIN se_epaymenttransactions j
          ON se_badgeassignments.badgeassignment_id = j.epaymenttransaction_item_id 
          AND t2.max_epaymenttransaction_id = j.epaymenttransaction_id
        ";  
    }
    
		//rc_toolkit::debug($sql, 'badgeassignment_list');
		
		$cs = array($where);
    if ($this->user_id)
    {
      $cs['user'] = "se_badgeassignments.badgeassignment_user_id = '$this->user_id'";
    }
    if ($this->badge_id)
    {
      $cs['badge'] = "se_badgeassignments.badgeassignment_badge_id = '$this->badge_id'";
    }
    
    $sql .= " ".rc_toolkit::criteria_builder($cs,'AND',true);
    $sql .= " ORDER BY $sort_by LIMIT $start, $limit";
    
    //rc_toolkit::debug($sql,'badgeassignment_list sql');
    
    $res = $database->database_query($sql);
    
    $badgeassignment_array = array();
    while ($badgeassignment_info = $database->database_fetch_assoc($res))
    {
      $badgeassignment_object = new se_badgeassignment();
      $badgeassignment_object->badgeassignment_exists = true;
      $badgeassignment_object->badgeassignment_info = $badgeassignment_info;
      
      $badgeassignment_object->badgeassignment_info[badgeassignment_desc] = html_entity_decode($badgeassignment_object->badgeassignment_info[badgeassignment_desc], ENT_QUOTES);
      
      $badgeassignment_info['badgeassignment'] = $badgeassignment_object;
      $badgeassignment_info['badge'] = $badgeassignment_object->load_badgeassignment_badge();
      $badgeassignment_info['user'] = $badgeassignment_object->load_badgeassignment_user();
      
      $badgeassignment_array[] = $badgeassignment_info;
    }

    return $badgeassignment_array;
	}
	// badgeassignment_list
	
	function badgeassignment_delete($badgeassignment_id=null)
	{
	  global $database;
	  
		if (!$badgeassignment_id)
		{
		  $badgeassignment_id = $this->badgeassignment_info['badgeassignment_id'];
		}

		$cs = array();
		if ($badgeassignment_id)
		{
		  $cs['a'] = "badgeassignment_id = '$badgeassignment_id'";
		}
		if ($this->user_id)
		{
		  $cs['u'] = "badgeassignment_user_id='$this->user_id'";
		}
		if ($this->badge_id)
		{
		  $cs['b'] = "badgeassignment_badge_id='$this->badge_id'";
		}
		if (count($cs))
		{
		  $where = rc_toolkit::criteria_builder($cs, 'AND');
		  $sql = "DELETE FROM se_badgeassignments WHERE $where";
		  $database->database_query($sql);
		}
	}
	// badgeassignment_delete
	
	
	function create_user_badge($user, $badge)
	{
	  global $database;
	  
		$data = array();
		
		$data['badgeassignment_badge_id'] = $badge->badge_info['badge_id'];
		$data['badgeassignment_user_id'] = $user->user_info['user_id'];
		$data['badgeassignment_datecreated'] = time();
		
		$data['badgeassignment_epayment'] = $badge->badge_info['badge_epayment'];
		
		
		if ($badge->badge_info['badge_cost'] == 0) {
		  $data['badgeassignment_approved'] = $badge->badge_info['badge_approved'];
		}
		else {
		  $data['badgeassignment_approved'] = 0;
		}
        
    
		if ($data['badgeassignment_approved']) {
		  $data['badgeassignment_dateapproved'] = time();
		}
    
    $query_data = rc_toolkit::db_data_packer($data);
    $sql = "INSERT INTO se_badgeassignments SET $query_data";
    $resource = $database->database_query($sql);
    
    $this->badgeassignment_info['badgeassignment_id'] = $database->database_insert_id();
    if (! $database->database_affected_rows() || ! $this->badgeassignment_info['badgeassignment_id']){
      return FALSE;
    }
        
    $sql = "SELECT * FROM se_badgeassignments  
        JOIN se_badges ON se_badges.badge_id = se_badgeassignments.badgeassignment_badge_id
        JOIN se_users ON se_users.user_id = se_badgeassignments.badgeassignment_user_id
        LEFT JOIN se_badgecats ON se_badgecats.badgecat_id = se_badges.badge_badgecat_id
        WHERE se_badgeassignments.badgeassignment_id = '{$this->badgeassignment_info['badgeassignment_id']}'
      ";
    
    $resource = $database->database_query($sql);
    if (! $resource || ! $database->database_num_rows($resource))
      return FALSE;
      
    $this->badgeassignment_exists = TRUE;  
    $this->badgeassignment_info = $database->database_fetch_assoc($resource);
    $this->load_badgeassignment_user();
    $this->load_badgeassignment_badge();
    
    return $this->badgeassignment_info['badgeassignment_id'];
      
	}
	
	
	function badgeassignment_approve($value)
	{
	  global $database;
	  
    if ($value) {
      $date_approved = time();
      $approved = 1;
      $this->newbadgeassignment_action();
    }
    else {
      $date_approved = 0;
      $approved = 0;
    }
    
    $sql = "UPDATE se_badgeassignments SET 
           badgeassignment_dateapproved = '$date_approved',
           badgeassignment_approved = '$approved'
        WHERE
           badgeassignment_id = '{$this->badgeassignment_info['badgeassignment_id']}'   
        ";
           
    $database->database_query($sql);
	}
	
	function newbadgeassignment_action()
	{
	  $actions = new se_actions();

	  $user = $this->load_badgeassignment_user();
	  $badge = $this->load_badgeassignment_badge();
	  
    $actions->actions_add($user, "newbadgeassignment", 
      array(
        $user->user_info['user_username'], 
        $user->user_displayname, 
        $badge->badge_info['badge_id'], 
        $badge->badge_info['badge_title']
      ),
      $action_media, 
      0, 
      false, 
      "user", 
      $user->user_info['user_id'], $user->user_info['user_privacy']
    );
	}
	
	function get_payment_amount()
	{
		return $this->badgeassignment_info['badge_cost'];
	}
	
	
}


class se_badge
{
  var $badge_info = array();
  var $is_error = NULL;
  var $user_id = NULL;
  var $badge_exists = false;

  function se_badge ($user_id = NULL, $badge_id = NULL)
  {
    global $database, $user;
    
    $this->user_id = $user_id;
    
    if ($badge_id)
    {
      $sql = "SELECT * FROM se_badges LEFT JOIN se_badgecats ON badgecat_id = badge_badgecat_id WHERE badge_id='{$badge_id}' LIMIT 1";
      $resource = $database->database_query($sql);
      if ($resource && $database->database_num_rows($resource))
      {
        $this->badge_exists = TRUE;
        $this->badge_info = $database->database_fetch_assoc($resource);
        
        if ($this->badge_info['badgecat_title'])
        {
          SELanguage::_preload($this->badge_info['badgecat_title']);
        }
        
        $this->unpack_ids_fields();
      }
    }
  }
  // se_badge

  
  function preload_badge_info($data)
  {
  	foreach ($data as $key => $value)
  	{
  	  if (strpos($key, 'badge') === 0 || strpos($key, 'badgecat') === 0)
  	  {
  	    $this->badge_info[$key] = $value;
  	  }
  	}
  	$this->unpack_ids_fields();
  	
  	if ($this->badge_info['badge_id'])
  	{
  	  $this->badge_exists = TRUE;
  	  
  	  if ($this->badge_info['badgecat_title'])
  	  {
  	    SELanguage::_preload($this->badge_info['badgecat_title']);
  	  }
  	}
  }
  
  
  
  function unpack_ids_fields() {
     $this->badge_info['badge_levels'] = $this->unpack_ids($this->badge_info['badge_level_ids']);
     $this->badge_info['badge_subnets'] = $this->unpack_ids($this->badge_info['badge_subnet_ids']);
     $this->badge_info['badge_profilecats'] = $this->unpack_ids($this->badge_info['badge_profilecat_ids']);   
  }
  
  
  function unpack_ids($value)
  {
    return ($value == 0) ? array() : explode(',', $value);
  }

  function badge_categories()
  {
    $field = new se_field("badge");
    $field->cat_list();
    return $field->cats;
  }
  
  function badge_total ($where = "")
  {
    global $database;
    
    $sql = "SELECT COUNT(badge_id) as total FROM se_badges LEFT JOIN se_badgecats ON badgecat_id = badge_badgecat_id ";

    $cs = array($where);
    $sql .= " ".rc_toolkit::criteria_builder($cs,'AND',true);
    
    $resource = $database->database_query($sql);
    $result = $database->database_fetch_assoc($resource);

    return $result['total'] ? $result['total'] : 0;
  }
  // badge_total
  
  
  function can_add_badge($user)
  {
    $se_badgeassignment = new se_badgeassignment(null, $user->user_info['user_id'], null);
    
  	$result = ($user->user_exists && $this->badge_exists
  	 && $user->level_info['level_badge_allow'] == 3
  	 && $this->badge_info['badge_enabled']
  	 && (empty($this->badge_info['badge_levels']) || in_array($user->user_info['user_level_id'], $this->badge_info['badge_levels']))
  	 && (empty($this->badge_info['badge_subnets']) || in_array($user->user_info['user_subnet_id'], $this->badge_info['badge_subnets']))
  	 && (empty($this->badge_info['badge_profiles']) || in_array($user->user_info['user_profile_id'], $this->badge_info['badge_profiles']))
  	 && ( ! $this->has_badge($user->user_info['user_id']) )
  	 && $se_badgeassignment->badgeassignment_total() < $user->level_info['level_badge_maxnum']
  	 );
  	 return $result;
  }
  
  
  function has_badge($user_id)
  {
  	$se_badgeassignment = new se_badgeassignment(null, $user_id, $this->badge_info['badge_id']);
  	$total_badgeassignments = $se_badgeassignment->badgeassignment_total();
  	return $total_badgeassignments > 0;
  }
  

  
  
  function badge_list ($start, $limit, $sort_by = "badge_id DESC", $where = "", $details = 0)
  {
    global $database, $user, $owner;

    $badge_query = "SELECT se_badges.*, se_badgecats.*, 
    count(badgeassignment_id) as total_assignments, 
	SUM(badgeassignment_approved) as total_approved
    FROM se_badges 
    LEFT JOIN se_badgecats ON badgecat_id = badge_badgecat_id 
    LEFT JOIN se_badgeassignments ON badgeassignment_badge_id = badge_id
    ";

    $cs = array($where);
    
    $badge_query .= " ".rc_toolkit::criteria_builder($cs,'AND',true);
    
    $badge_query .= " GROUP BY badge_id ORDER BY $sort_by LIMIT $start, $limit";
    
    $res = $database->database_query($badge_query);
    
    $badge_array = array();
    while ($badge_info = $database->database_fetch_assoc($res))
    {
      
      $badge_object = new se_badge();
      $badge_object->badge_exists = true;
      $badge_object->badge_info = $badge_info;
      
      $badge_object->badge_info[badge_desc] = html_entity_decode($badge_object->badge_info[badge_desc], ENT_QUOTES);
      
      $badge_info['badge'] = $badge_object;
      
      $badge_array[] = $badge_info;
    }

    return $badge_array;
  }
  // badge_list
  

  function get_type_badges($type, $sort_by="badge_title DESC")
  {
    global $database, $setting;
    
  	$maps = unserialize($setting["setting_badge_{$type}s"]);
  	$type_badges = array();
  	
  	if (!empty($maps))
  	{
  	  $where = "badge_id IN (". join(",",$maps) .")";
      $badge_array = $this->badge_list(0, count($maps), $sort_by, $where);
      //rc_toolkit::debug($badge_array,'$badge_array');
        
        $counters = array();
        $sql = "SELECT user_{$type}_id as type_id, COUNT(user_{$type}_id) as total FROM se_users WHERE se_users.user_enabled='1' GROUP BY user_{$type}_id";
        $res = $database->database_query($sql);
        while ($row = $database->database_fetch_assoc($res)) {
          $counters[$row['type_id']] = $row['total'];
        }
        //rc_toolkit::debug($counters,'counters');
        
        foreach ($maps as $type_id => $badge_id) {
          foreach ($badge_array as $k => $badge_detail) {
            //rc_toolkit::debug($badge_detail['badge'], $badge_id);
            if ($badge_detail['badge']->badge_info['badge_id'] == $badge_id) {
              
              $type_badges[$type_id]['badge'] = $badge_detail['badge'];
              $type_badges[$type_id]['total_users'] = $counters[$type_id] ? $counters[$type_id] : 0;
              
              break;
            }
          }
        
        }
  	}
  	return $type_badges;
  }
  
  
  
  function remove_cached_badge($badge_id)
  {
    // CACHING
    $cache_key = "badge_object_$badge_id";
    $cache_object = SECache::getInstance('serial');
    if( is_object($cache_object) )
    {
      $badge = $cache_object->get($cache_key);
      if ( $badge ) {
        $cache_object->remove($cache_key);
      }
    } 
  }
  // remove_cached_badge
  
  
  function get_cached_badge($badge_id)
  {
    $badge = null;
    
    // CACHING
    $cache_key = "badge_object_$badge_id";
    $cache_object = SECache::getInstance('serial');
    if( is_object($cache_object) )
    {
      $badge = $cache_object->get($cache_key);
    } 
    if ( !is_a($badge, 'se_badge') )
    {
      $badge = new se_badge(null, $badge_id);
      
      if( is_object($cache_object) )
      {
        $cache_object->store($badge, $cache_key);
      }
    }
    
    return $badge;
  }
  // get_cached_badge
  
  
  function get_level_badge($level_id)
  {
  	global $setting;
  	$maps = unserialize($setting['setting_badge_levels']);
  	if (!is_array($maps)) $maps = array();
  	$badge_id = $maps[$level_id];
  	if ($badge_id > 0) {
  	  return $this->get_cached_badge($badge_id);
  	}
  	return null;
  }
  // get_level_badge
  
  
  function get_subnet_badge($subnet_id)
  {
    global $setting;
    $maps = unserialize($setting['setting_badge_subnets']);
    if (!is_array($maps)) $maps = array();
    $badge_id = $maps[$subnet_id];
    if ($badge_id > 0) {
      return $this->get_cached_badge($badge_id);
    }
    return null;
  }
  // get_subnet_badge


  function get_profilecat_badge($profilecat_id)
  {
    global $setting;
    $maps = unserialize($setting['setting_badge_profilecats']);
    if (!is_array($maps)) $maps = array();
    $badge_id = $maps[$profilecat_id];
    if ($badge_id > 0) {
      return $this->get_cached_badge($badge_id);
    }
    return null;
  }
  // get_profilecat_badge


  
  function badge_dir ($badge_id = 0, $admin=false)
  {
    if ($badge_id == 0 & $this->badge_exists)
    {
      $badge_id = $this->badge_info[badge_id];
    }
    $subdir = $badge_id + 999 - (($badge_id - 1) % 1000);
    $badgedir = "./uploads_badge/$subdir/$badge_id/";
    if ($admin) $badgedir = ".".$badgedir;
    
    return $badgedir;
  }
  // badge_dir
  
  

  function badge_edit ($data)
  {
    global $database, $url, $setting, $user;

    $time = time();
    
    $filtered_data['badge_title'] = $data['badge_title'];
    $filtered_data['badge_desc'] = $data['badge_desc'];
    $filtered_data['badge_badgecat_id'] = $data['badge_badgecat_id'];
    
    $filtered_data['badge_search'] = $data['badge_search'];
    
    $filtered_data['badge_cost'] = $data['badge_cost'];
    $filtered_data['badge_epayment'] = $data['badge_epayment'] ? 1 : 0;
    if ($filtered_data['badge_cost'] == 0) {
      $filtered_data['badge_epayment'] = 0;
    }
    $filtered_data['badge_approved'] = $data['badge_approved'] ? 1 : 0;
    $filtered_data['badge_enabled'] = $data['badge_enabled'] ? 1 : 0;
    $filtered_data['badge_link_details'] = $data['badge_link_details'] ? 1 : 0;
    
    if (empty($data['badge_levels'])) {
      $filtered_data['badge_level_ids'] = '0';
    }
    else {
      $filtered_data['badge_level_ids'] = join(',',$data['badge_levels']);
    }
    if (empty($data['badge_subnets'])) {
      $filtered_data['badge_subnet_ids'] = '0';
    }
    else {
      $filtered_data['badge_subnet_ids'] = join(',',$data['badge_subnets']);
    }
    if (empty($data['badge_profilecats'])) {
      $filtered_data['badge_profilecat_ids'] = '0';
    }
    else {
      $filtered_data['badge_profilecat_ids'] = join(',',$data['badge_profilecats']);
    }    
    
    if ($is_error)
    {
      return array('is_error' => $is_error);
    }
    
    if (! $this->badge_exists)
    {        
      $filtered_data['badge_datecreated'] = $time;
      $filtered_data['badge_dateupdated'] = $time;
      
      $query_data = rc_toolkit::db_data_packer($filtered_data);
      $sql = "INSERT INTO se_badges SET $query_data";
     //   rc_toolkit::debug($sql);
      $resource = $database->database_query($sql);
      $this->badge_info['badge_id'] = $database->database_insert_id();
      if (! $database->database_affected_rows() || ! $this->badge_info['badge_id']){
        return FALSE;
      }
        
      $this->badge_exists = TRUE;
    }
    else
    {
      $filtered_data['badge_dateupdated'] = $time;
      $sql = "UPDATE se_badges SET ".rc_toolkit::db_data_packer($filtered_data). " WHERE badge_id='{$this->badge_info['badge_id']}' LIMIT 1";
      $resource = $database->database_query($sql);
      if (! $resource)
        return FALSE;
    }
    // GET UPDATED BADGE INFO
    $sql = "SELECT * FROM se_badges WHERE badge_id='{$this->badge_info['badge_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    if (! $resource || ! $database->database_num_rows($resource))
      return FALSE;
      
    $this->badge_info = $database->database_fetch_assoc($resource);
    $this->unpack_ids_fields();
    
    return $this->badge_info['badge_id'];
  }
  // badge_edit
  


  function badge_delete ($badge_id)
  {
    global $database, $url;
    $badge_query = $database->database_query("SELECT * FROM se_badges WHERE badge_id='$badge_id'");
    if ($database->database_num_rows($badge_query) != 1)
    {
      return;
    }
    
    $badge_info = $database->database_fetch_assoc($badge_query);

    $admin_dir = true;
    
    // DELETE BADGE AND PHOTO
    $photo_path = $this->badge_dir($badge_id, $admin_dir) . $badge_info[badge_photo];
    if (file_exists($photo_path))
    {
      unlink($photo_path);
    }
    $thumb_path = $this->badge_dir($badge_id, $admin_dir) . substr($badge_info[badge_photo], 0, strrpos($badge_info[badge_photo], ".")) . "_thumb" . substr($badge_info[badge_photo], strrpos($badge_info[badge_photo], "."));

    if (file_exists($thumb_path))
    {
      unlink($thumb_path);
    }
    $database->database_query("DELETE FROM se_badges, se_badgeassignments USING se_badges LEFT JOIN se_badgeassignments ON se_badges.badge_id=se_badgeassignments.badgeassignment_badge_id WHERE se_badges.badge_id='$badge_id'");
  
    $badgeassignment = new se_badgeassignment(null, null, $badge_id);
    $badgeassignment->badgeassignment_delete();
    
  }
  // badge_delete
  

  function badge_delete_selected ($start, $limit, $sort_by = "badge_id DESC", $where = "")
  {
    global $database;

    $badge_query = "SELECT se_badges.badge_id FROM se_badges";
    
    $cs = array($where);
    $badge_query .= " ".rc_toolkit::criteria_builder($cs,'AND',true);
    $badge_query .= " GROUP BY badge_id ORDER BY $sort_by LIMIT $start, $limit";

    $res = $database->database_query($badge_query);
    while ($badge_info = $database->database_fetch_assoc($res))
    {
      if ($_POST["delete_badge_" . $badge_info[badge_id]] == 1)
      {
        $this->badge_delete($badge_info[badge_id]);
      }
    }
  }
  // badge_delete_selected
  

  function badge_photo ($nophoto_image = NULL, $thumb = FALSE, $admin=FALSE)
  {
    if (empty($this->badge_info['badge_photo']))
    {
      return $nophoto_image;
    }
      
    $badge_dir = $this->badge_dir($this->badge_info['badge_id'], $admin);
    $badge_photo = $badge_dir . $this->badge_info['badge_photo'];
    if ($thumb)
    {
      $badge_thumb = substr($badge_photo, 0, strrpos($badge_photo, ".")) . "_thumb" . substr($badge_photo, strrpos($badge_photo, "."));
      if (file_exists($badge_thumb))
      {
        return $badge_thumb;
      }
        
    }
    if (file_exists($badge_photo))
    {
      return $badge_photo;
    }
      
    return $nophoto_image;
  }
  // badge_photo
  
  
  function badge_photo_upload ($photo_name)
  {
    global $database, $url, $setting;
    // Check exists and owner
    if (! $this->badge_exists)
    {
      return FALSE;
    }
    // SET KEY VARIABLES
    $file_maxsize = 10485760;
    $file_exts = explode(",", str_replace(" ", "", strtolower($setting['setting_badge_exts'])));
    $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
    $file_maxwidth = $setting['setting_badge_width'];
    $file_maxheight = $setting['setting_badge_height'];
    
    $this->badge_mkdir(true);
    
    $photo_newname = "0_" . rand(1000, 9999) . ".jpg";
    $file_dest = $this->badge_dir($this->badge_info[badge_id], true) . $photo_newname;
    $thumb_dest = substr($file_dest, 0, strrpos($file_dest, ".")) . "_thumb" . substr($file_dest, strrpos($file_dest, "."));
    $new_photo = new se_upload();
    $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    // UPLOAD AND RESIZE PHOTO IF NO ERROR
    if (! $new_photo->is_error)
    {
      $this->badge_photo_delete();
      
      $new_photo->upload_thumb($thumb_dest);
      if ($new_photo->is_image)
        $new_photo->upload_photo($file_dest);
      else
        $new_photo->upload_file($file_dest);
      if (! $new_photo->is_error)
      {
        $sql = "UPDATE se_badges SET badge_photo='{$photo_newname}' WHERE badge_id='{$this->badge_info['badge_id']}'";
        $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);;
        $this->badge_info['badge_photo'] = $photo_newname;
      }
    }

    $file_result = Array('is_error' => $new_photo->is_error);
    return $file_result;

  }
  // badge_photo_upload  
  
  
  function badge_mkdir($admin=false)
  {
      // CHECK THAT UPLOAD DIRECTORY EXISTS, IF NOT THEN CREATE
    $badge_directory = $this->badge_dir($this->badge_info['badge_id'], $admin);
    $badge_path_array = explode("/", $badge_directory);
    array_pop($badge_path_array);
    array_pop($badge_path_array);
    $subdir = implode("/", $badge_path_array) . "/";
    if (! is_dir($subdir))
    {
      mkdir($subdir, 0777);
      chmod($subdir, 0777);
      $handle = fopen($subdir . "index.php", 'x+');
      fclose($handle);
    }
    if (! is_dir($badge_directory))
    {
      mkdir($badge_directory, 0777);
      chmod($badge_directory, 0777);
      $handle = fopen($badge_directory . "/index.php", 'x+');
      fclose($handle);
    }
  }
  
  
  
  function badge_photo_delete ()
  {
    global $database;
    
    if ($badge_photo = $this->badge_photo(null, false, true))
    {
      $sql = "UPDATE se_badges SET badge_photo='' WHERE badge_id='{$this->badge_info[badge_id]}'";
      $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
      
      if (file_exists($badge_photo))
      {
        unlink($badge_photo);
      }
      
      $badge_thumb = $this->badge_photo(null, true, true);
      if (file_exists($badge_thumb))
      {
        unlink($badge_thumb);
      }
      
      $this->badge_info['badge_photo'] = NULL;
    }
  }
  // badge_photo_delete  
  
}
