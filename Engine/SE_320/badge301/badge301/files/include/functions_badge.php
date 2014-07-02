<?php


defined('SE_PAGE') or exit();


function badge_topmenu_items()
{
  global $database, $setting;
  
  $badge_ids = $setting['setting_badge_menu_badge_ids'];
  
  $items = array();
  
  if ($badge_ids) {
    $badge_array = explode(',', $setting['setting_badge_menu_badge_ids']);
    $sql = "SELECT badge_id, badge_title FROM se_badges WHERE badge_id IN ($badge_ids)";
    $res = $database->database_query($sql);
    $badges = array();
    while ($row = $database->database_fetch_assoc($res)) {
      $badges[$row['badge_id']] = $row['badge_title'];
    }
    foreach ($badge_array as $id) {
      if (isset($badges[$id])) {
        $items[$id] = $badges[$id];
      }
    }
  }
  
  return $items;
  
}


function badgeassignment_get_entries($limit=5, $badge_id=0, $sort="badgeassignment_dateapproved DESC", $where="")
{
  $cache_key = "badgeassignment_load_entries_{$limit}_{$badge_id}_{$sort}_{$where}";
  $cache_key = rc_toolkit::strip_text($cache_key);
  
  $badgeassignment_array = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $badgeassignment_array = $cache_object->get($cache_key);
  }  
  
  if( !is_array($badgeassignment_array) )
  { 
    $cs = array("badgeassignment_approved='1'");
    if ($where) {
      $cs['where'] = $where;
    }
    $where = rc_toolkit::criteria_builder($cs,'AND',false);
    
    $se_badgeassignment = new se_badgeassignment(null, null, $badge_id);
    $badgeassignment_array = $se_badgeassignment->badgeassignment_list(0, $limit, $sort, $where, 0);

    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($badgeassignment_array, $cache_key);
    }
    
  }
  
  return $badgeassignment_array;
}



function search_badge()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;

	// CONSTRUCT QUERY
	$badge_query = "SELECT 
			  se_badges.*
			FROM
			  se_badges
			WHERE
			    se_badges.badge_search='1'
			  AND
			  (
			    se_badges.badge_title LIKE '%$search_text%' OR
			    se_badges.badge_desc LIKE '%$search_text%'
			  )"; 

	// GET TOTAL RESULTS
	$total_badges = $database->database_num_rows($database->database_query($badge_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if($t == "badge") {

	  // MAKE BADGE PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;

	  // SEARCH BADGES
	  $badge = new se_badge();
	  $badges = $database->database_query($badge_query." ORDER BY badge_id DESC LIMIT $start, $limit");
	  while($badge_info = $database->database_fetch_assoc($badges)) {

	    // SET RESULT VARS
	    $result_url = $url->url_create("badge", null, $badge_info[badge_id]);
	    $result_name = 11270171;
        $result_desc = 11270176;
        
        $badge->badge_info = $badge_info;
        $badge->badge_exists = true;
        
	    $thumb_path = $badge->badge_photo('./images/badge_placeholder.gif', true);
	    
	    // IF NO TITLE
	    if($badge_info[badge_title] == "") { $badge_info[badge_title] = SE_Language::get(589); }

	    $badge_info[badge_desc] = strip_tags(html_entity_decode($badge_info[badge_desc], ENT_QUOTES));
	    // IF DESCRIPTION IS LONG
	    if(strlen($badge_info[badge_desc]) > 150) { $badge_info[badge_desc] = substr($badge_info[badge_desc], 0, 147)."..."; }

	    $results[] = Array('result_url' => $result_url,
				'result_icon' => $thumb_path,
				'result_name' => $result_name,
				'result_name_1' => $badge_info[badge_title],
				'result_desc' => $result_desc,
				'result_desc_1' => $badge_info[badge_desc]);
	  }

	  // SET TOTAL RESULTS
	  $total_results = $total_badges;

	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(11270171, 11270176, 11270172);
	if($total_badges > 200) { $total_badges = "200+"; }
	$search_objects[] = Array('search_type' => 'badge',
				'search_lang' => 11270172,
				'search_total' => $total_badges);


} // END search_badge() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 

function deleteuser_badge($user_id)
{
  global $database;

  $badgeassignment = new se_badgeassignment(null, $user_id, null);
  $badgeassignment->badgeassignment_delete();
    
}
// deleteuser_badge


function site_statistics_badge(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];

  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(badge_id) AS total FROM se_badges WHERE badge_search='1'"));
  $statistics['badges'] = array(
    'title' => 11270175,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
}
// site_statistics_badge



function epayment_process_badgeassignment($item_id)
{
  return epayment_checkout_badgeassignment($item_id);
}

function epayment_checkout_badgeassignment($item_id)
{
  global $user, $smarty;
  
  $badgeassignment = new se_badgeassignment($item_id);
  
  if ( !$badgeassignment->badgeassignment_exists || $badgeassignment->badgeassignment_info['badgeassignment_user_id'] != $user->user_info['user_id']) {
    $result = array('is_error' => 828);
  }
  else {
    $badgeassignment->badgeassignment_info['badgeassignment_desc'] = html_entity_decode($badgeassignment->badgeassignment_info['badgeassignment_desc'], ENT_QUOTES);
    
    $result = array(
     'item' => $badgeassignment,
     'item_type' => 'badgeassignment',
     'item_id' => $badgeassignment->badgeassignment_info['badgeassignment_id'],
     'item_name' => $badgeassignment->badgeassignment_info['badge_title'],
     'amount' => $badgeassignment->get_payment_amount()
    );
  }

  return $result;
}


function epayment_ipn_process_badgeassignment(&$handler, &$transaction)
{
  if ( !$transaction->epaymenttransaction_item_id || $transaction->epaymenttransaction_item_type != 'badgeassignment') {
    return;
  }
  
  $badgeassignment = new se_badgeassignment($transaction->epaymenttransaction_item_id);
  if ( !$badgeassignment->badgeassignment_exists) {
    return;
  }
  
  if ($transaction->epaymenttransaction_status == 'Completed') {
    if ($badgeassignment->badgeassignment_info['badge_approved'] && !$badgeassignment->badgeassignment_info['badgeassignment_approved']) {
      $badgeassignment->badgeassignment_approve(true);
    }
  }
  else {
    $badgeassignment->badgeassignment_approve(false);
  }

}

function epayment_normalize_data_badgeassignment(&$handler)
{
  $badgeassignment = new se_badgeassignment($handler->transactionData['item_id']);
  $handler->transactionData['user_id'] = $badgeassignment->badgeassignment_info['badgeassignment_user_id'];
  $handler->transactionData['item_name'] = $badgeassignment->badgeassignment_info['badge_title'];
  //$handler->transactionData['notes'] = print_r($badgeassignment,true);
  return true;
}

function epayment_transaction_item_url_badgeassignment(&$transaction)
{
  global $url;
  
  $item_url = $url->url_create('badgeassignment', null, $transaction->epaymenttransaction_item_id);
  //echo $item_url;
  return $item_url;
}


