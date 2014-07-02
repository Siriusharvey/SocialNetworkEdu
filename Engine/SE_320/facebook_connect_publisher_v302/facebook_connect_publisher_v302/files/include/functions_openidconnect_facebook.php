<?php

function openidconnect_facebook_get_linked_friends() {

  $openid_user = new se_user_openid('facebook');
  $openid_user->get_linked_friends();
   
}

function openidconnect_facebook_filter($source, &$smarty) {
  $s1 = "kzyaf='uggc://jjj.j3.bet/1999/kugzy'";
  $s2 = "kzyaf='uggc://jjj.j3.bet/1999/kugzy' kzyaf:so='uggc://jjj.snprobbx.pbz/2008/sozy'";
  $source = str_replace(str_rot13($s1), str_rot13($s2), $source);
  return $source;
}


function openidconnect_facebook_register_feed_story($story_type, $feedstory, $register_on_facebook = true) {
  global $database;

  $template_bundle_id = openidconnect_facebook_get_bundle_id($story_type);

  // metadata
  $feedstory['feedstory_title'] = semods::g($feedstory,'feedstory_title','');
  $feedstory['feedstory_body'] = semods::g($feedstory,'feedstory_body','');
  $feedstory['feedstory_link_link'] = semods::g($feedstory,'feedstory_link_link','');
  $feedstory['feedstory_link_text'] = semods::g($feedstory,'feedstory_link_text','');

  
  $feedstory['feedstory_enabled'] = (int)semods::g($feedstory,'feedstory_enabled',1);

  $feedstory['feedstory_usermessage'] = semods::g($feedstory,'feedstory_usermessage','');
  $feedstory['feedstory_userprompt'] = semods::g($feedstory,'feedstory_userprompt','');

  $feedstory['feedstory_service_id'] = openidconnect_get_service_id('facebook');
  
  $feedstory['feedstory_pagecheck'] = semods::g($feedstory,'feedstory_pagecheck','');

  $feedstory['feedstory_publishprompt'] = (int)semods::g($feedstory['feedstory_publishprompt'],0);

  $feedstory['feedstory_compiler'] = semods::g($feedstory,'feedstory_compiler','');
  $feedstory['feedstory_publishusing'] = semods::g($feedstory,'feedstory_publishusing','feed');
  $feedstory['feedstory_vars'] = semods::g($feedstory,'feedstory_vars','{*actor*},{*site-name*},{*site-link*}');

  $feedstory['feedstory_display'] = (int)semods::g($feedstory['feedstory_display'],1);
  $feedstory['feedstory_display_user'] = (int)semods::g($feedstory['feedstory_display_user'],1);

  $feedstory['feedstory_desc'] = semods::g($feedstory,'feedstory_desc','');


  $metadata = array('feedstory_title'     => $feedstory['feedstory_title'],
                    'feedstory_body'      => $feedstory['feedstory_body'],
                    'feedstory_link_link' => $feedstory['feedstory_link_link'],
                    'feedstory_link_text' => $feedstory['feedstory_link_text'],
                    'template_bundle_id'  => 0,
                    );

  $metadata_serialized = serialize($metadata);
  $metadata_serialized = $database->database_real_escape_string($metadata_serialized);


  semods::db_query("INSERT INTO se_semods_openidfeedstories (
                      feedstory_usermessage,
                      feedstory_userprompt,
                      feedstory_service_id,
                      feedstory_type,
                      feedstory_enabled,
                      feedstory_pagecheck,
                      feedstory_publishprompt,
                      feedstory_compiler,
                      feedstory_publishusing,
                      feedstory_vars,
                      feedstory_display,
                      feedstory_display_user,
                      feedstory_desc,
                      feedstory_metadata
                      ) VALUES (
                        '{$feedstory['feedstory_usermessage']}',
                        '{$feedstory['feedstory_userprompt']}',
                        {$feedstory['feedstory_service_id']},
                        '$story_type',
                        {$feedstory['feedstory_enabled']},
                        '{$feedstory['feedstory_pagecheck']}',
                        {$feedstory['feedstory_publishprompt']},
                        '{$feedstory['feedstory_compiler']}',
                        '{$feedstory['feedstory_publishusing']}',
                        '{$feedstory['feedstory_vars']}',
                        {$feedstory['feedstory_display']},
                        {$feedstory['feedstory_display_user']},
                        '{$feedstory['feedstory_desc']}',
                        '{$metadata_serialized}'
                      )
                      ON DUPLICATE KEY UPDATE
                      feedstory_usermessage = '{$feedstory['feedstory_usermessage']}',
                      feedstory_userprompt = '{$feedstory['feedstory_userprompt']}',
                      feedstory_enabled = {$feedstory['feedstory_enabled']},
                      feedstory_pagecheck = '{$feedstory['feedstory_pagecheck']}',
                      feedstory_publishprompt = {$feedstory['feedstory_publishprompt']},
                      feedstory_compiler = '{$feedstory['feedstory_compiler']}',
                      feedstory_publishusing = '{$feedstory['feedstory_publishusing']}',
                      feedstory_vars = '{$feedstory['feedstory_vars']}',
                      feedstory_display = {$feedstory['feedstory_display']},
                      feedstory_display_user = {$feedstory['feedstory_display_user']},
                      feedstory_desc = '{$feedstory['feedstory_desc']}',
                      feedstory_metadata = '{$metadata_serialized}'
                      ");




  openidconnect_facebook_deactivate_bundle_id($template_bundle_id);
  



  // Talk to FB
  if($feedstory['feedstory_enabled'] && $register_on_facebook) {

    $one_line_story_templates = array();
    $one_line_story_templates[] = $feedstory['feedstory_title'];
        
    $short_story_templates = array();
    $short_story_templates[] = array('template_title' => $feedstory['feedstory_title'],
                                     'template_body'  => $feedstory['feedstory_body']
                                    );
    
    $full_story_template = null;
    
    $action_links = array();
    
    if(!empty($feedstory['feedstory_link_link']) && !empty($feedstory['feedstory_link_text'])) {

      $action_links[] = array( 'text' => $feedstory['feedstory_link_text'],
                               'href' => $feedstory['feedstory_link_link']
                               );
      
    }

    $template_bundle_id = openidconnect_facebook_register_template_bundle($one_line_story_templates,$short_story_templates,$action_links);

    if($template_bundle_id != 0) {

      $metadata['template_bundle_id'] = $template_bundle_id;
      
      $metadata_serialized = serialize($metadata);
      $metadata_serialized = $database->database_real_escape_string($metadata_serialized);

      semods::db_query("UPDATE se_semods_openidfeedstories SET feedstory_metadata = '{$metadata_serialized}' WHERE feedstory_type = '{$story_type}'");
      
    }
    
  }
  
  // CACHING
  $cache_key = 'openidconnect_feed_actions_' . 'facebook';

  if(class_exists("SECache")) {
    $cache_object = SECache::getInstance();

    if( is_object($cache_object) ) {
      $cache_object->remove($cache_key);
    }
  }

}


function openidconnect_facebook_get_bundle_id($story_type) {

  $template_bundle_id = 0;

  $openidconnect_facebook_feed_actions = openidconnect_load_feed_actions();

  if(array_key_exists($story_type, $openidconnect_facebook_feed_actions)) {

    $feed_story_template = $openidconnect_facebook_feed_actions[$story_type];
    $template_bundle_id = $feed_story_template['feedstory_metadata']['template_bundle_id'];
    
  }
  
  return $template_bundle_id;

}


function openidconnect_facebook_unregister_feed_story($story_type) {
  $openidconnect_facebook_feed_actions = openidconnect_load_feed_actions();

  if(array_key_exists($story_type, $openidconnect_facebook_feed_actions)) {

    $feed_story_template = $openidconnect_facebook_feed_actions[$story_type];
    $template_bundle_id = $feed_story_template['feedstory_metadata']['template_bundle_id'];
    
    openidconnect_facebook_deactivate_bundle_id($template_bundle_id);
    
    semods::db_query("DELETE FROM se_semods_openidfeedstories WHERE feedstory_type = '$story_type'");
    
  }

  // CACHING
  $cache_key = 'openidconnect_feed_actions_' . 'facebook';

  if(class_exists("SECache")) {
    $cache_object = SECache::getInstance();

    if( is_object($cache_object) ) {
      $cache_object->remove($cache_key);
    }
  }
  
}

function openidconnect_facebook_deactivate_bundle_id($template_bundle_id) {

  if($template_bundle_id != 0) {

    // Talk to FB
    $openid_client = new se_user_openid('facebook');
    $openid_facebook = $openid_client->openidapi->api_client();
    
    // no need for session key
    $openid_facebook->api_client->session_key = null;

    try {

      $openid_facebook->api_client->feed_deactivateTemplateBundleByID($template_bundle_id);

    } catch (Exception $ex) {

    }
    
  }

}



function openidconnect_facebook_register_template_bundle($one_line_story_templates,$short_story_templates,$action_links, $var = null) {

  // Talk to FB
  $openid_client = new se_user_openid('facebook');
  $openid_facebook = $openid_client->openidapi->api_client();
  
  // no need for session key
  $openid_facebook->api_client->session_key = null;

  try {

    $template_bundle_id = $openid_facebook->api_client->feed_registerTemplateBundle($one_line_story_templates,$short_story_templates,$var,$action_links);

  } catch (Exception $ex) {

    $template_bundle_id = 0;
    
  }
  
  return $template_bundle_id;

}

?>