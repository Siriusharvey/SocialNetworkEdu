<?php

/* $Id: profile_photos_file.php 42 2009-01-29 04:55:14Z john $ */

$page="profile_photos_file";

include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if ($user->user_exists == 0 && $setting['setting_permission_profile'] == 0)
    {
    $page="error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 656);
    $smarty->assign('error_submit', 641);
    include "footer.php";
    }

// DISPLAY ERROR PAGE IF NO OWNER
if ($owner->user_exists == 0)
    {
    $page="error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 828);
    $smarty->assign('error_submit', 641);
    include "footer.php";
    }

if (isset($_POST['task']))
    {
    $task=$_POST['task'];
    }
elseif (isset($_GET['task']))
    {
    $task=$_GET['task'];
    }
else
    {
    $task="main";
    }

if (isset($_POST['type']))
    {
    $type=$_POST['type'];
    }
elseif (isset($_GET['type']))
    {
    $type=$_GET['type'];
    }
else
    {
    $type="";
    }

if (isset($_POST['media_id']))
    {
    $media_id=$_POST['media_id'];
    }
elseif (isset($_GET['media_id']))
    {
    $media_id=$_GET['media_id'];
    }
else
    {
    $media_id=0;
    }

if ($task == 'ajax')
    $type='media';

// SET VARS
$media_per_page=20;

// CHECK PRIVACY
$privacy_max   =$owner->user_privacy_max($user);

if (!($owner->user_info['user_privacy'] & $privacy_max))
    {
    header ("Location: " . $url->url_create('profile', $owner->user_info['user_username']));
    exit();
    }


// START QUERY
$photo_query="";

$tag_query=Array();

// CALL TAG HOOK
($hook=SE_Hook::exists('se_mediatag')) ? SE_Hook::call($hook, array()) : NULL;

// GET TOTAL PHOTOS
$total_files=$database->database_num_rows($database->database_query($photo_query));

// ADD TO PHOTO QUERY
$photo_query.=" ORDER BY mediatag_date DESC";
$photo_query_tmp=$photo_query;
$photo_query.=" LIMIT 7";
// MAKE MEDIA PAGES
$page_vars=make_page($total_files, $media_per_page, $p);

// RUN TAG QUERY
$media    =$database->database_query($photo_query);

// GET MEDIA INTO AN ARRAY
$media_array=Array();

while ($media_info=$database->database_fetch_assoc($media))
    {
    $media_array[$media_info['type'] . $media_info['media_id']]=$media_info;
    }

$media_tmp=$database->database_query($photo_query_tmp);

$media_array_tmp=Array();

while ($media_info_tmp=$database->database_fetch_assoc($media_tmp))
    {
    $media_array_tmp[$media_info_tmp['type'] . $media_info_tmp['media_id']]=$media_info_tmp;
    }


// MAKE SURE MEDIA EXISTS
if (!array_key_exists($type . $media_id, $media_array_tmp))
    {
    header ("Location: profile_photos.php?user=" . $owner->user_info['user_username']);
    exit();
    }

$media_info_tmp=$media_array_tmp[$type . $media_id];


// UPDATE NOTIFICATIONS
if ($user->user_info['user_id'] == $owner->user_info['user_id'])
    {
    $type=str_replace("media", "", $media_info_tmp['type']);
    $database->database_query(
        "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$owner->user_info['user_id']}' AND se_notifytypes.notifytype_name='new{$type}tag' AND notify_object_id='{$media_info_tmp['media_id']}'");
    }


// GET ALBUM TAG PRIVACY
$allowed_to_tag    =(bool)$media_info_tmp['allowed_to_tag'];

// GET ALBUM COMMENT PRIVACY
$allowed_to_comment=(bool)$media_info_tmp['allowed_to_comment'];


// GET OWNER INFO
$page_owner        =$owner;
$owner             =new se_user(Array($media_info_tmp['owner_user_id']));


// GET ALBUM OWNER IF NECESSARY
if ($media_info_tmp['user_id'] != 0)
    {
    $media_info_tmp['user']                            =new se_user();
    $media_info_tmp['user']->user_exists               =1;
    $media_info_tmp['user']->user_info['user_id']      =$media_info_tmp['user_id'];
    $media_info_tmp['user']->user_info['user_username']=$media_info_tmp['user_username'];
    $media_info_tmp['user']->user_info['user_fname']   =$media_info_tmp['user_fname'];
    $media_info_tmp['user']->user_info['user_lname']   =$media_info_tmp['user_lname'];
    $media_info_tmp['user']->user_displayname();
    }


// GET MEDIA WIDTH/HEIGHT
$mediasize
                               =@getimagesize($media_info_tmp['media_dir'] . $media_info_tmp['media_id'] . '.'
                                                  . $media_info_tmp['media_ext']);
$media_info_tmp['media_width'] =$mediasize[0];
$media_info_tmp['media_height']=$mediasize[1];


// GET MEDIA COMMENTS
$comment                       =new se_comment($media_info_tmp['type'], $media_info_tmp['type_id'],
                                               $media_info_tmp['media_id']);
$total_comments                =$comment->comment_total();


// RETRIEVE TAGS FOR THIS PHOTO
$tag_array=Array();

$tags=$database->database_query(
          str_replace("[media_id]", $media_info_tmp['media_id'], $tag_query[$media_info_tmp['type']]));

while ($tag=$database->database_fetch_assoc($tags))
    {
    $taggeduser = new se_user();

    if ($tag['user_id'] != NULL)
        {
        $taggeduser->user_exists               =1;
        $taggeduser->user_info['user_id']      =$tag['user_id'];
        $taggeduser->user_info['user_username']=$tag['user_username'];
        $taggeduser->user_info['user_fname']   =$tag['user_fname'];
        $taggeduser->user_info['user_lname']   =$tag['user_lname'];
        $taggeduser->user_displayname();
        }
    else
        {
        $taggeduser->user_exists=0;
        }

    $tag['tagged_user']=$taggeduser;
    $tag_array[]       =$tag;
    }

if ($task == 'ajax')
    {
    $media_keys   =array_keys($media_array_tmp);
    $current_index=(array_search('media' . $media_info_tmp['media_id'], $media_keys));

    if ($current_index == 0)
        {
        $previous_index=$media_keys[count($media_array_tmp) - 1];
        }
    else
        {
        $previous_index=$media_keys[$current_index - 1];
        }

    if ($current_index + 1 == count($media_array_tmp))
        $next_index=$media_keys[0];
    else
        $next_index=$media_keys[$current_index + 1];

    $current_num=$current_index + 1;

    $tag_array_temp=Array();

    for ($i=0; $i < count($tag_array); $i++)
        {
        $tag_array_temp[$i]['tag_id'] = $tag_array[$i]['mediatag_id'];

        if ($tag_array[$i]['tagged_user']->user_exists)
            {
            $tag_array_temp[$i]['tag_link']=$url->url_create("profile",
                                                             $tag_array[$i]['tagged_user']->user_info['user_username']);
            }

        if ($value['tag_user']->user_exists)
            {
            $tag_array_temp[$i]['tag_text']=$tag_array[$i]['tagged_user']->user_displayname;
            }
        else
            {
            $tag_array_temp[$i]['tag_text']=$tag_array[$i]['mediatag_text'];
            }

        $tag_array_temp[$i]['tag_x']      =$tag_array[$i]['mediatag_x'];
        $tag_array_temp[$i]['tag_y']      =$tag_array[$i]['mediatag_y'];

        $tag_array_temp[$i]['tag_width']  =$tag_array[$i]['mediatag_width'];
        $tag_array_temp[$i]['tag_height'] =$tag_array[$i]['mediatag_height'];
        $tag_array_temp[$i]['tagged_user']=$tag_array[$i]['tagged_user']->user_info['user_username'];
        }

    $posted_date_array=$datetime->time_since($media_info_tmp['media_date']);
    $posted_date      =SELanguage::get($posted_date_array[0], Array($posted_date_array[1]));
    $media_path       =$url->url_userdir($owner->user_info['user_id']) . $media_info_tmp['media_id'] . "."
        . $media_info_tmp['media_ext'];
    $mediy_title      =($media_info_tmp['media_title'] != "") ? $media_info_tmp['media_title'] : SELanguage::get(589);
    $report_content   ="javascript:TB_show('" . SELanguage::get(1000148)
        . "', 'user_report.php?return_url={$url->url_create('album_file', $owner->user_info['user_username'], $album_info['album_id'], $current_num)}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');";
    $parent_link      =str_replace("[media_parent_id]", $media_info_tmp['media_parent_id'],
                                   $media_info_tmp['media_parent_url']);

    if ($media_info_tmp['user_id'] != 0)
        {
        $from=SELanguage::get(1216, Array
            (
            $parent_link,
            $media_info_tmp['media_parent_title'],
            $url->url_create("profile", $media_info_tmp['user']->user_info['user_username']),
            $media_info_tmp['user']->user_displayname
            ));
        }
    else
        {
        $from=SELanguage::get(1217, Array
            (
            $parent_link,
            $media_info_tmp['media_parent_title']
            ));
        }

    $json_array=Array
        (
        "result"         => "success",
        "img_src"        => $url->url_userdir($owner->user_info['user_id']) . $media_info_tmp['media_id'] . '.'
            . $media_info_tmp['media_ext'],
        "last"           => $media_array_tmp[$previous_index]['media_id'],
        "next"           => $media_array_tmp[$next_index]['media_id'],
        "user_name"      => $page_owner->user_info['user_username'],
        "current_num"    => $current_num,
        "media_dir"      => $url->url_userdir($owner->user_info['user_id']),
        "current_index"  => $current_index,
        "tags"           => $tag_array_temp,
        "album_id"       => $album_info['album_id'],
        "media_id"       => $media_info_tmp['media_id'],
        "posted_date"    => $posted_date,
        "direct_link"    => $url->url_base . str_replace("./", "", $media_path),
        "embedded_image" => "<a href='" . $url->url_base . str_replace("./",
                                                                       "",
                                                                       $media_path) . "'><img src='" . $url->url_base
            . str_replace(
                  "./",
                  "",
                  $media_path) . "' border='0'></a>",
        "text_link"      => "<a href='" . $url->url_base . str_replace("./", "",
                                                                       $media_path) . "'>" . $mediy_title . "</a>",
        "ubb_code"       => "[url=" . $url->url_base . str_replace("./",
                                                                   "",
                                                                   $media_path) . '][img]' . $url->url_base
            . str_replace(
                  "./",
                  "", $media_path) . '[/img][/url]',
        "report_content" => $report_content,
        "media_width"    => $media_info_tmp['media_width'],
        "media_height"   => $media_info_tmp['media_height'],
        "media_caption"  => ($media_info_tmp['media_width'] > 300) ? $media_info_tmp['media_width'] : 300,
        "initialTotal"   => ($total_comments > 0) ? $total_comments : 0,
        "user_owner"     => $owner->user_info,
        "from"           => $from,
        "title" =>  $media_info_tmp['media_title'],
        "desc" =>  $media_info_tmp['media_desc']
        );

    $json_str=json_encode($json_array);

    echo $json_str;
    exit();
    }


// SET GLOBAL PAGE TITLE
$global_page_title[0]      =1204;
$global_page_title[1]      =$page_owner->user_displayname;
$global_page_title[2]      =count($media_array);
$global_page_description[0]=1204;
$global_page_description[1]=$page_owner->user_displayname;
$global_page_description[2]=count($media_array_tmp);

// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('page_owner', $page_owner);
$smarty->assign('album_info', $album_info);
$smarty->assign('media_info', $media_info_tmp);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_tag', $allowed_to_tag);
$smarty->assign('media', $media_array_tmp);
$smarty->assign('media_keys', array_keys($media_array_tmp));
$smarty->assign('tags', $tag_array);
$smarty->assign('media_count', count($media_array_tmp));
$smarty->assign('media_count_pages', ceil(count($media_array_tmp) / 7));
include "footer.php";
?>