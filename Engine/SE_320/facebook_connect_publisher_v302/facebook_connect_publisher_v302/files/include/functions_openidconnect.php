<?php

define('OPENIDCONNECT_SIGNUP_EXPRESS',0);
define('OPENIDCONNECT_SIGNUP_REGULAR',1);

// TODO: custom caching and fetch from db
$openid_service_map = array('facebook'    => 1,
                            'myspace'     => 2,
                            'google'      => 3,
                            'yahoo'       => 4,
                            'hyves'       => 5,
                            'friendster'  => 6,
                            'live'        => 9,
                            'twitter'     => 10
                            );



/* COMPATIBILITY */

if(!function_exists('file_put_contents')) {
  function file_put_contents($filename, $data, $file_append = false) {
   $fp = @fopen($filename, (!$file_append ? 'w+' : 'a+'));
   if(!$fp) {
     trigger_error('file_put_contents - can not write to file.', E_USER_ERROR);
     return false;
   }
   $total_written = fwrite($fp, $data);
   fclose($fp);
   return $total_written;
  }
}





/*********************** HOOKS *********************/







/*
 * Purpose - delete user data when user is deleted
 *
 */
function deleteuser_openidconnect($user_id) {

  // delete openid links
  semods::db_query("DELETE FROM se_semods_usersopenid WHERE openid_user_id = $user_id");

}






function openidconnect_hook_footer_login() {
  global $page;

  $page = "login_openidconnect";
}





function openidconnect_load_services() {

  // CACHING
  $cache_key = 'openidconnect_services';

  if(class_exists("SECache")) {
    $cache_object = SECache::getInstance();

    if( is_object($cache_object) ) {
      $openidconnect_services = $cache_object->get($cache_key);
    }
  }

  if( !is_array($openidconnect_services) ) {

    $openidconnect_services = semods::db_query_assoc_all("SELECT *
                                                          FROM se_semods_openidservices
                                                          WHERE openidservice_enabled = 1
                                                            AND openidservice_customlogo = 0
                                                          ORDER BY openidservice_showorder DESC");
    

    // CACHE
    if( is_object($cache_object) ) {
      $cache_object->store($openidconnect_services, $cache_key);
    }
    
  }
  
  return $openidconnect_services;

}





/***** FEED STORIES *****/







/*
 * New Group
 *
 */
function openidconnect_feedstory_newgroup($user, $action_params) {
  global $url;
  
  $group_id = $action_params[2];
  $group_title_short = $action_params[3];
  
  $feed_params = openidconnect_feedstory_compose_newgroup($group_id);

  return array('feed_params'  => $feed_params,
               'story_params' => array('group_id' => $group_id)
              );
}


function openidconnect_feedstory_compose_newgroup($group_id) {
  global $url;
  
  $group = new se_group(0,$group_id);

  $group_photo = $url->url_base . $group->group_photo('./images/nophoto.gif', TRUE);
  $group_title = $group->group_info['group_title'];
  $group_url = $url->url_create('group','',$group_id);
  
  $feed_params = array('group-title'      => $group_title,
                       'group-id'   => $group_id,
                       'group-desc' => $group->group_info['group_desc'],
                       'group-link' => $group_url,
                       'images'     => array(
                                             array('src'  => $group_photo,
                                                   'href' => $group_url
                                                  )
                                             )
                       );
  
  return $feed_params;
}



/*
 * New Classified
 *
 */
function openidconnect_feedstory_postclassified($user, $action_params) {

  $classified_id = $action_params[2];
  $classified_title_short = $action_params[3];

  $feed_params = openidconnect_feedstory_compose_postclassified($classified_id);

  return array('feed_params'  => $feed_params,
               'story_params' => array('classified_id' => $classified_id)
              );
}


function openidconnect_feedstory_compose_postclassified($classified_id) {
  global $url;
  
  $classified = new se_classified(0,$classified_id);

  // emotional expression can't be materialized into words
  $owner_username = semods::db_query_count("SELECT user_username FROM se_users WHERE user_id = {$classified->classified_info['classified_user_id']}");

  $classified_photo = $url->url_base . $classified->classified_photo('./images/nophoto.gif', TRUE);
  $classified_title = $classified->classified_info['classified_title'];
  $classified_url = $url->url_create('classified',$owner_username,$classified_id);
  
  
  $feed_params = array('classified-title'      => $classified_title,
                       'classified-id'   => $classified_id,
                       'classified-body' => htmlspecialchars_decode($classified->classified_info['classified_body'], ENT_QUOTES),
                       'classified-link'  => $classified_url,
                       'images'     => array(
                                             array('src'  => $classified_photo,
                                                   'href' => $classified_url
                                                  )
                                             )
                       );
  
  return $feed_params;
  
}



/*
 * New Event
 *
 */
function openidconnect_feedstory_newevent($user, $action_params) {
  
  $event_id = $action_params[2];
  $event_title_short = $action_params[3];

  $feed_params = openidconnect_feedstory_compose_newevent($event_id);

  return array('feed_params'  => $feed_params,
               'story_params' => array('event_id' => $event_id)
              );

}



function openidconnect_feedstory_compose_newevent($event_id) {
  global $url, $datetime, $global_timezone, $setting;
  
  $event = new se_event(0,$event_id);

  $event_photo = $url->url_base . $event->event_photo('./images/nophoto.gif', TRUE);
  $event_title = $event->event_info['event_title'];
  $event_url = $url->url_create('event','',$event_id);

  $event_date_start = $datetime->timezone($event->event_info['event_date_start'], $global_timezone);
  $event_date_end = $datetime->timezone($event->event_info['event_date_end'], $global_timezone);
  
  
  // NO END DATE 
  if (!$event->event_info['event_date_end']) {
    $event_date = sprintf( semods::get_language_text(3000203),
                           $datetime->cdate($setting['setting_dateformat'], $event_date_start),
                           $datetime->cdate($setting['setting_timeformat'], $event_date_start)
                         );

    // SAME-DAY EVENT
  } elseif ($datetime->cdate("F j, Y", $event_date_start) == $datetime->cdate("F j, Y", $event_date_end)) {
    $event_date = sprintf( semods::get_language_text(3000202),
                           $datetime->cdate($setting['setting_dateformat'], $event_date_start),
                           $datetime->cdate($setting['setting_timeformat'], $event_date_start),
                           $datetime->cdate($setting['setting_timeformat'], $event_date_end)
                         );

    // MULTI-DAY EVENT 
  } else {
    $event_date = sprintf( semods::get_language_text(3000204),
                           $datetime->cdate("{$setting['setting_dateformat']} {$setting['setting_timeformat']}", $event_date_start),
                           $datetime->cdate("{$setting['setting_dateformat']} {$setting['setting_timeformat']}", $event_date_end)
                         );
  }

  
  $feed_params = array('event-title'      => $event_title,
                       'event-id'   => $event_id,
                       'event-desc' => htmlspecialchars_decode($event->event_info['event_desc'], ENT_QUOTES),
                       'event-location' => $event->event_info['event_location'],
                       'event-host' => $event->event_info['event_host'],
                       //'event-date-start' => $event_date_start,
                       //'event-date-end' => $event_date_end,
                       'event-date' => $event_date,
                       'event-link' => $event_url,
                       'images'     => array(
                                             array('src'  => $event_photo,
                                                   'href' => $event_url
                                                  )
                                             )
                       );
  
  return $feed_params;
}






/*
 * New Blog
 *
 */
function openidconnect_feedstory_postblog($user, $action_params) {
  
  $blog_id = $action_params[2];
  $blog_title_short = $action_params[3];

  $feed_params = openidconnect_feedstory_compose_postblog($blog_id);

  return array('feed_params'  => $feed_params,
               'story_params' => array('blog_id' => $blog_id)
              );
}





function openidconnect_feedstory_compose_postblog($blog_id) {
  global $url;
  
  $blog_info = semods::db_query_assoc("SELECT *
                                 FROM se_blogentries B
                                 LEFT JOIN se_users U
                                   ON B.blogentry_user_id = U.user_id
                                 WHERE blogentry_id = $blog_id");

  
  if($blog_info == false) {
    return false;
  }
  
  $blog_title = $blog_info['blogentry_title'];
  $blog_url = $url->url_create('blog_entry',$blog_info['user_username'],$blog_id);
  
  $blog_body = htmlspecialchars_decode($blog_info['blogentry_body'], ENT_QUOTES);
  $blog_body = openidconnect_html2txt($blog_body);
  
  if(strlen($blog_body) > 100) {
    $blog_body = substr($blog_body, 0, 100);
  }
  
  $feed_params = array('blog-title'      => $blog_title,
                       'blog-id'   => $blog_id,
                       'blog-user'   => $blog_info['user_username'],
                       'blog-body' => $blog_body,
                       'blog-link' => $blog_url
                       );
  
  return $feed_params;
}















/*
 * New Poll
 *
 */
function openidconnect_feedstory_newpoll($user, $action_params) {
  
  $poll_id = $action_params[2];
  $poll_title_short = $action_params[3];

  $feed_params = openidconnect_feedstory_compose_newpoll($poll_id);

  return array('feed_params'  => $feed_params,
               'story_params' => array('poll_id' => $poll_id)
              );
  
}



function openidconnect_feedstory_compose_newpoll($poll_id) {
  global $url;
  
  $poll = new se_poll(0,$poll_id);

  $poll_title = $poll->poll_info['poll_title'];
  $poll_url = $url->url_create('poll',$poll->poll_owner->user_info['user_username'],$poll_id);
  
  $feed_params = array('poll-title'      => $poll_title,
                       'poll-id'   => $poll_id,
                       'poll-desc' => $poll->poll_info['poll_desc'],
                       'poll-link'  => $poll_url
                       );
  
  return $feed_params;
}







/*
 * New Album
 *
 */
function openidconnect_feedstory_newalbum($user, $action_params) {
  
  $album_id = $action_params[2];
  $album_title_short = $action_params[3];

  $feed_params = openidconnect_feedstory_compose_newalbum($album_id);

  return array('feed_params'  => $feed_params,
               'story_params' => array('album_id' => $album_id)
              );
  
}



function openidconnect_feedstory_compose_newalbum($album_id) {
  global $url;
  
  //$album = new se_album(0,$album_id);
  $album_info = semods::db_query_assoc("SELECT *
                                 FROM se_albums A
                                 LEFT JOIN se_users U
                                   ON A.album_user_id = U.user_id
                                 WHERE album_id = $album_id");

  
  if($album_id == false) {
    return false;
  }

  $album_title = $album_info['album_title'];
  $album_url = $url->url_create('album',$album_info['user_username'],$album_id);

  if($album_info['album_cover_id'] == 0) {
    $album_cover_src = './images/icons/folder_big.gif';
  } elseif(in_array($album_info['album_cover_ext'], array("jpeg","jpg","gif","png","bmp"))) {
    $album_cover_dir = $url->url_userdir($album_info['user_id']);
    $album_cover_src = $album_cover_dir . $album_info['album_cover_id'] . '_thumb.jpg';
  } elseif(in_array($album_info['album_cover_ext'], array("mp3","mp4","wav"))) {
    $album_cover_src = './images/icons/audio_big.gif';
  } elseif(in_array($album_info['album_cover_ext'], array("mpeg","mpg","mpa","avi","swf","mov","ram","rm"))) {
    $album_cover_src = './images/icons/video_big.gif';
  } else {
    $album_cover_src = './images/icons/file_big.gif';
  }
  
  $feed_params = array('album-title'      => $album_title,
                       'album-id'   => $album_id,
                       'album-desc' => $album_info['album_desc'],
                       'album-link'  => $album_url
                       );

  if(file_exists($album_cover_src)) {
    $feed_params['images'] = array(
                                    array('src'  => $url->url_base . $album_cover_src,
                                          'href' => $album_url
                                         )
                                    );
  }
  
  return $feed_params;
}





/*
 * New Video
 *
 * TODO: add "video" to template data
 *
 */
function openidconnect_feedstory_newyoutubevideo($user, $action_params) {
  global $url;
  
  $video_id = $action_params[2];
  $video_title_short = $action_params[3];
  
  $video = new se_video(0,$video_id);

  $owner_username = semods::db_query_count("SELECT user_username FROM se_users WHERE user_id = {$video->video_info['video_user_id']}");

  $video_title = $video->video_info['video_title'];
  $video_url = $url->url_create('video',$owner_username,$video_id);

  $video_thumb = '';

  $thumb_path = $video->video_dir($video->video_info['video_user_id']) . $video->video_info['video_id'] . "_thumb.jpg";
  if(file_exists($thumb_path)) {
    $video_thumb = $thumb_path;
  }


  $minutes = floor($video->video_info['video_duration_in_sec']/60);
  $seconds = $video->video_info['video_duration_in_sec']-60*$minutes;
  $video_duration_in_min = sprintf("%02d:%02d", $minutes, $seconds);
  
  $feed_params = array('uservideo-title'        => $video_title,
                       'uservideo-id'           => $video_id,
                       'uservideo-desc'         => $video->video_info['video_desc'],
                       'uservideo-duration'     => $video_duration_in_min,
                       'uservideo-duration-sec' => $video->video_info['video_duration_in_sec'],
                       'uservideo-link'         => $video_url
                       );
  
  if(file_exists($thumb_path)) {
    
    // youtube vid
    if($video->video_info['video_type'] == 1) {
      $video_src = "http://www.youtube.com/v/{$video->video_info['video_youtube_code']}";
    } else {
      $video_src = $url->url_base . str_replace('./', '', $video_url);
    }
    
    // TBD: video-src needs to be direct link to youtube?
    $feed_params['video'] = array('preview_img' => $url->url_base . $thumb_path,
                                  'video_src'   => $video_src
                                    );

  }

  return array('feed_params'  => $feed_params,
               'story_params' => array('video_id' => $video_id)
              );

}





/*
 * New Photos to Album
 *
 */
function openidconnect_feedstory_newmedia($user, $action_params, $action_media) {
  global $url;
  
  $album_id = $action_params[2];
  $album_title_short = $action_params[3];

  $resume = false;

  // check if there's a pending newmedia feed action of the same type and add more images
  $openidconnect_feed_story = openidconnect_get_session_feed_story('newmedia');
  
  if(!is_null($openidconnect_feed_story) && (semods::g($openidconnect_feed_story,'story_type','') == 'newmedia')) {
    $story_params = semods::g($openidconnect_feed_story,'story_params_',null);
    
    if(!is_null($story_params) && (semods::g($story_params,'album_id',0) == $album_id)) {
      $resume = true;
    }
  }

  if($resume) {
    
    $feed_params = semods::g($openidconnect_feed_story,'feed_params',array());
    $feed_params_images = semods::g($feed_params,'images',array());
    
  } else {
    
    $feed_params = openidconnect_feedstory_compose_newmedia($album_id);
    $feed_params_images = array();
    
  }


  $feed_images = array();

  // add photos - up to 4
  foreach($action_media as $action_media_item) {
    $feed_images[] =  array('src'  => $action_media_item['media_path'],
                            'href' => $action_media_item['media_link']
                          );
  }
  
  if(!empty($feed_images)) {

    $feed_params_images = array_merge($feed_params_images,$feed_images);
    $feed_params['images'] = $feed_params_images;

  }

  
  return array('feed_params'  => $feed_params,
               'story_params' => array('album_id' => $album_id)
              );
  
}



function openidconnect_feedstory_compose_newmedia($album_id) {
  return openidconnect_feedstory_compose_newalbum($album_id);
}







/*
 * New Music Files
 * Unfortunately only one file is supported
 *
 * "mp3":{"src":"http://Sample.mp3","album":"My Album", "title":"My Title", "artist":"My Artist" }
 *
 */
function openidconnect_feedstory_newmusic($user, $action_params, $action_media) {
  global $url;

  // get last song
  $music_info = semods::db_query_assoc("SELECT * FROM se_music WHERE music_user_id = {$user->user_info['user_id']} ORDER BY music_id DESC LIMIT 1");

  if($music_info == false) {
    return false;
  }

  // only mp3
  if($music_info['music_ext'] != 'mp3') {
    return false;
  }

  $media_dir = $url->url_userdir($music_info['music_user_id']);
  $media_path = $url->url_base . $media_dir . $music_info['music_id'] . '.' . $music_info['music_ext'];
  
  $feed_params = array( 'mp3' => array('src'    => $media_path,
                                       'title'  => $music_info['music_title']
                                      )
                      );


  return array('feed_params'  => $feed_params,
               'story_params' => array('music_id' => $music_id)
              );
  
}






/*
 * Edit Status
 *
 */
function openidconnect_feedstory_editstatus($user, $action_params, $action_media) {
  global $url;
  
  $status = $action_params[2];
  
  // strip junk
  $status = str_replace("&shy;","",$status);
  $status = openidconnect_html2txt($status);

  $public_site_name = semods::get_setting('openidconnect_feed_public_site_name', 'My Site');
  $public_site_link = $url->url_base;
  
  // TODO - settings
  //$status_append = ' from <a href="' . $public_site_link . '">@' . $public_site_name . '</a>';
  //$status .= $status_append;
  
  // Langerize ? link --> admin ?
  $link_title = 'Join me on ' . $public_site_name;
  
  $feed_params = array( 'links'  => array( array( 'text'  => $link_title,
                                                  'href'   => $public_site_link,
                                                 ),
                                          ),
                      );

  // TODO LANGERIZE
  return array('feed_params'  => $feed_params,
               'story_params' => array('status' => $status),
               'user_message' => $status,
               //'user_prompt'  => 'Update your status?',
               //'publish_using'=> 'feed
               'publish_using'=> 'stream'
              );
  
}






/*
 * Signup
 *
 */
function openidconnect_feedstory_signup($user, $action_params, $action_media) {
  global $url;

  $user_username = $action_params[0];
  
  $feed_params = array('signup-link'      => $url->url_base . 'signup.php?signup_referer='.$user_username,
                       );
  
  return array('feed_params'  => $feed_params,
               'story_params' => array('signup_username' => $user_username),
              );
  
}





/*
 * Generic Action
 *
 */
function openidconnect_feedstory_genericaction($user, $action_params, $action_media, $actiontype_name) {
  global $url;

  $feed_params = array();

  $sql = "SELECT *
          FROM se_actiontypes
          WHERE actiontype_name = '{$actiontype_name}'
            AND actiontype_enabled = 1
          ";
  
  $actiontype = semods::db_query_assoc($sql);
  
  if($actiontype) {
    
    $feedstory_vars = explode(",",$actiontype['actiontype_vars']);
    
    for($i = 0; $i < count($feedstory_vars); $i++) {
      // assumption - only bracket squares
      $feedstory_key = $feedstory_vars[$i];
      $feedstory_key = substr($feedstory_key,1,strlen($feedstory_key)-2);
      $feed_params[$feedstory_key] = $action_params[$i];
    }

  }

  
  return array('feed_params'  => $feed_params,
               'story_params' => $feed_params,
              );
  
}







function openidconnect_load_feed_actions($service_name = 'facebook' /* default - facebook */) {

  // CACHING
  $cache_key = 'openidconnect_feed_actions_' . $service_name;

  if(class_exists("SECache")) {
    $cache_object = SECache::getInstance();

    if( is_object($cache_object) ) {
      $openidconnect_facebook_feed_actions = $cache_object->get($cache_key);
    }
  }

  if( !is_array($openidconnect_facebook_feed_actions) ) {
  
    $sql = "SELECT *
            FROM se_semods_openidfeedstories
            WHERE feedstory_service_id = (SELECT openidservice_id FROM se_semods_openidservices WHERE openidservice_name = '$service_name' AND openidservice_enabled = 1)
              AND feedstory_enabled = 1
            ";
            
    $openidconnect_facebook_feed_actions = array();
    
    $rows = new semods_db_iterator_assoc($sql);
    while($row = $rows->next()) {
      $row['feedstory_metadata'] = !empty($row['feedstory_metadata']) ? unserialize($row['feedstory_metadata']) : array();
      $openidconnect_facebook_feed_actions[$row['feedstory_type']] = $row;
    }

    // CACHE
    if( is_object($cache_object) ) {
      $cache_object->store($openidconnect_facebook_feed_actions, $cache_key);
    }

  }
  
  return $openidconnect_facebook_feed_actions;
}






// TODO: refactor per service
function openidconnect_hook_action( $arguments = array() ) {
  global $smarty, $url;
  
  $user = $arguments[0];
  $actiontype_name = $arguments[1];
  $replace = $arguments[2];
  $action_media = $arguments[3];

  // see if user opted out of this story
  $user_feedstories_keys = !empty($user->usersetting_info['usersetting_openidconnect_publishfeeds_keys']) ? explode(',', $user->usersetting_info['usersetting_openidconnect_publishfeeds_keys']) : array();
  if(in_array($actiontype_name, $user_feedstories_keys)) {
    return ;
  }
  
  $openidconnect_facebook_feed_actions = openidconnect_load_feed_actions();

  // for all networks, find primary and see if connected
  $primary_network = 'facebook';
  
  // signup race condition
  if(($actiontype_name != 'signup') && !se_user_openid::user_openid_is_connected($user->user_info['user_id'],$primary_network) ) {
    return;
  }

  $feed_story_template = semods::g($openidconnect_facebook_feed_actions, $actiontype_name);
  
  if( !is_null($feed_story_template) ) {

    $public_site_name = semods::get_setting('openidconnect_feed_public_site_name', '');

    $function_name = semods::g($feed_story_template,'feedstory_compiler','');

    if($function_name == '') {
      $function_name = 'openidconnect_feedstory_'.$actiontype_name;
    }

    // no compiler - try generic
    if(!function_exists($function_name)) {
      //return false;
      $function_name = 'openidconnect_feedstory_genericaction';
    }
    
    $feed_story_params = call_user_func($function_name, $user, $replace, $action_media, $actiontype_name);
    
    // can't publish
    if($feed_story_params === false) {
      return false;
    }
    
    $require_permission = 1;
    
    $feed_story_params['feed_params']['site-name'] = $public_site_name;
    $feed_story_params['feed_params']['site-link'] = $url->url_base;

    $story_preview_search = array_keys($feed_story_params['feed_params']);
    $story_preview_replace = array_values($feed_story_params['feed_params']);

    foreach($story_preview_search as $key => $value) {
      $story_preview_search[$key] = '{*' . $value . '*}';
    }
    
    $story_preview_search[] = '{*actor*}';
    $story_preview_replace[] = $user->user_displayname;
    
    $story_preview = str_replace( $story_preview_search, $story_preview_replace, $feed_story_template['feedstory_metadata']['feedstory_title'] );
    
    $openidconnect_feed_story = array(
                                      'page_check'          => $feed_story_template['feedstory_page_check'],
                                      'publish_prompt'      => $feed_story_template['feedstory_publishprompt'],

                                      'user_message'        => semods::g($feed_story_params,'user_message', semods::g($feed_story_template,'feedstory_usermessage','') ),
                                      'user_prompt'         => semods::g($feed_story_params,'user_prompt', semods::g($feed_story_template,'feedstory_userprompt','') ),
                                      
                                      // Network specific
                                      'template_bundle_id'  => $feed_story_template['feedstory_metadata']['template_bundle_id'],


                                      'publish_using'       => semods::g($feed_story_params,'publish_using', semods::g($feed_story_template,'feedstory_publishusing','feed') ),

                                      'data'                => json_encode($feed_story_params['feed_params']),
                                      'story_params'        => json_encode($feed_story_params['story_params']),
                                      'story_params_'       => $feed_story_params['story_params'],
                                      'feed_params'         => $feed_story_params['feed_params'],
                                      'story_type'          => $actiontype_name,
                                      'require_permission'  => $require_permission,
                                      'story_preview'       => $story_preview

                                      );
    

      openidconnect_queue_session_feed_story($openidconnect_feed_story);
    
  }

}





function openidconnect_html2txt($document){
  $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                 '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                 '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                 '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
  );
  $text = preg_replace($search, '', $document);
  return $text;
}


function openidconnect_get_simple_cookie_domain($host = null) {
  // Quick config
  if( defined('SE_COOKIE_DOMAIN') )
  {
    return SE_COOKIE_DOMAIN;
  }
  
  if( !$host )
  {
    $host = $_SERVER["HTTP_HOST"];
  }
  
  $host = parse_url($host);
  $host = $host['path'];
  $parts = explode('.', $host);
  
  switch( TRUE )
  {
    // Do not use custom for these:
    // IP Address
    case ( preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $host) ):
    // Intranet host
    case ( count($parts) === 1 ):
      return null;
      break;
    
    // Second level ccld
    case ( strlen($parts[count($parts)-1]) == 2 && strlen($parts[count($parts)-2]) <= 3 ):
      array_splice($parts, 0, count($parts) - 3);
      return join('.', $parts);
      break;
    
    // tld or first-level ccld
    default:
      array_splice($parts, 0, count($parts) - 2);
      return join('.', $parts);
  }
  
  return null;
}


function openidconnect_get_session_feed_story($type = 'all') {

  if(is_null(semods::g($_SESSION,'openidconnect_feed_story'))) {
    return null;
  }

  $openidconnect_feed_story = semods::g($_SESSION,'openidconnect_feed_story');
  
  return $openidconnect_feed_story;
}


function openidconnect_queue_session_feed_story($openidconnect_feed_story, $type = 'all') {
  
  $_SESSION['openidconnect_feed_story'] = $openidconnect_feed_story;

  //$session_object =& SESession::getInstance();
  //$session_object->set('openidconnect_feed_story', $openidconnect_feed_story);
  
}

function openidconnect_destroy_session_feed_story($type = 'all') {
  unset($_SESSION['openidconnect_feed_story']);
}

function openidconnect_debuglog($message) {
  static $log = null;
  
  if($log == null) {
    $log = fopen('openidlog.txt',"a+");
  }
  
  fwrite($log, $message . "\n");
}



function openidconnect_ensure_connect() {
  global $user, $smarty;  

  // Singleton + setting
  $primary_network = 'facebook';

  $openidconnect_request_connect = false;
  
  if( $user->user_exists ) {

    $suppress_connect = semods::g($_SESSION,'openidconnect_suppress_connect',false);
    
    if(!$suppress_connect) {
    
      if(se_user_openid::user_openid_is_connected($user->user_info['user_id'],$primary_network) ) {
        $openidconnect_request_connect = true;
      }
    }
  
  }
  
  $smarty->assign('openidconnect_request_connect',(int)$openidconnect_request_connect);
  
}


// TODO: custom caching and fetch from db
function openidconnect_get_service_id($service) {
  global $openid_service_map;
  
  if(!is_numeric($service)) {
    return semods::g($openid_service_map, $service,0);
  }
  
  return $service;
}




function openidconnect_destroy_session() {
  openidconnect_destroy_session_feed_story();
  unset($_SESSION['openidconnect_suppress_connect']);
  unset($_SESSION['openidconnect_user_connected']);
  unset($_SESSION['openid_imported_fields']);
  unset($_SESSION['openidconnect_suppress_autologin']);
}
?>