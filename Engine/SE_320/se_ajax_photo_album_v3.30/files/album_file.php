<?php

/* $Id: album_file.php 2 2009-01-10 20:53:09Z john $ */

$page="album_file";

include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if (!$user->user_exists && !$setting['setting_permission_album'])
    {
    $page="error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 656);
    $smarty->assign('error_submit', 641);
    include "footer.php";
    }

// DISPLAY ERROR PAGE IF NO OWNER
if (!$owner->user_exists)
    {
    $page="error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 828);
    $smarty->assign('error_submit', 641);
    include "footer.php";
    }

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if (!$owner->level_info['level_album_allow'])
    {
    header ("Location: " . $url->url_create('profile', $owner->user_info[user_username]));
    exit();
    }


// PARSE GET/POST
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

if (isset($_POST['album_id']))
    {
    $album_id=$_POST['album_id'];
    }
elseif (isset($_GET['album_id']))
    {
    $album_id=$_GET['album_id'];
    }
else
    {
    $album_id="";
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
    $type=NULL;
    }

if (isset($_POST['pagest']))
    {
    $pagest=$_POST['pagest'];
    }
elseif (isset($_GET['pagest']))
    {
    $pagest=$_GET['pagest'];
    }
else
    {
    $pagest='1';
    }

// MAKE SURE MEDIA EXISTS
$media_query=$database->database_query("SELECT * FROM se_media WHERE media_id='$media_id' LIMIT 1");

if ($database->database_num_rows($media_query) != 1)
    {
    header ("Location: " . $url->url_create('albums', $owner->user_info[user_username]));
    exit();
    }

$media_info=$database->database_fetch_assoc($media_query);


// BE SURE ALBUM BELONGS TO THIS USER
$album     =$database->database_query(
                "SELECT * FROM se_albums WHERE album_id='$media_info[media_album_id]' AND album_user_id='"
                    . $owner->user_info[user_id] . "'");

if ($database->database_num_rows($album) != 1)
    {
    header ("Location: " . $url->url_create('albums', $owner->user_info[user_username]));
    exit();
    }

$album_info =$database->database_fetch_assoc($album);

// CHECK PRIVACY
$privacy_max=$owner->user_privacy_max($user);

if (!($album_info[album_privacy] & $privacy_max))
    {
    $page="error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 1000125);
    $smarty->assign('error_submit', 641);
    include "footer.php";
    }

// GET CUSTOM ALBUM STYLE IF ALLOWED
if ($owner->level_info[level_album_style] != 0)
    {
    $albumstyle_info
               =$database->database_fetch_assoc(
                    $database->database_query("SELECT albumstyle_css FROM se_albumstyles WHERE albumstyle_user_id='"
                                                  . $owner->user_info[user_id] . "' LIMIT 1"));
    $global_css=$albumstyle_info[albumstyle_css];
    }

// GET MEDIA IN ALBUM FOR CAROUSEL
$media_array=Array();

$media_query=$database->database_query(
                 "SELECT media_id, media_ext, '{$owner->user_info[user_id]}' AS album_user_id FROM se_media WHERE media_album_id='$album_info[album_id]' ORDER BY media_order ASC LIMIT 7");

while ($thismedia=$database->database_fetch_assoc($media_query))
    {
    $media_array[$thismedia[media_id]]=$thismedia;
    }

$media_array_tmp=Array();

$media_query=$database->database_query(
                 "SELECT media_id, media_ext, '{$owner->user_info[user_id]}' AS album_user_id FROM se_media WHERE media_album_id='$album_info[album_id]' ORDER BY media_order ASC");

while ($thismedia=$database->database_fetch_assoc($media_query))
    {
    $media_array_tmp[$thismedia[media_id]]=$thismedia;
    }


// GET NUMBER OF MEDIA IN ALBUM
$media_count
    =$database->database_num_rows(
         $database->database_query(
             "SELECT media_id FROM se_media WHERE media_album_id='$album_info[album_id]' ORDER BY media_order ASC"));

// GET ALL MEDIA ID's FOR 'NEXT'/'PREV' FUNCTIONALITY
$media_ids_array=Array();

$media_result
    =$database->database_query(
         "SELECT media_id FROM se_media WHERE media_album_id='$album_info[album_id]' ORDER BY media_order ASC");

while ($thismedia=$database->database_fetch_assoc($media_result))
    {
    $media_ids_array[]=$thismedia['media_id'];
    }


// GET MEDIA WIDTH/HEIGHT
$mediasize
                         =@getimagesize(
                              $url->url_userdir(
                                  $owner->user_info[user_id]) . $media_info[media_id] . '.' . $media_info[media_ext]);
$media_info[media_width] =$mediasize[0];
$media_info[media_height]=$mediasize[1];


// GET ALBUM TAG PRIVACY
$allowed_to_tag          =1;

if (!($privacy_max & $album_info[album_tag]))
    {
    $allowed_to_tag=0;
    }

// GET ALBUM COMMENT PRIVACY
$allowed_to_comment=1;

if (!($privacy_max & $album_info[album_comments]))
    {
    $allowed_to_comment=0;
    }


// GET MEDIA COMMENTS
$comment       =new se_comment('media', 'media_id', $media_info[media_id]);
$total_comments=$comment->comment_total();


// UPDATE ALBUM VIEWS
if ($user->user_info[user_id] != $owner->user_info[user_id])
    {
    $album_views_new=$album_info[album_views] + 1;
    $database->database_query(
        "UPDATE se_albums SET album_views='$album_views_new' WHERE album_id='$album_info[album_id]' LIMIT 1");
    }

// UPDATE NOTIFICATIONS
if ($user->user_info[user_id] == $owner->user_info[user_id])
    {
    $database->database_query(
        "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='"
            . $owner->user_info[user_id]
            . "' AND (se_notifytypes.notifytype_name='mediacomment' OR se_notifytypes.notifytype_name='mediatag' OR se_notifytypes.notifytype_name='newtag') AND notify_object_id='"
            . $media_info[media_id] . "'");
    }


// RETRIEVE TAGS FOR THIS PHOTO
$tag_array=Array();

$tags=$database->database_query(
          "SELECT se_mediatags.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_mediatags LEFT JOIN se_users ON se_mediatags.mediatag_user_id=se_users.user_id WHERE mediatag_media_id='$media_info[media_id]' ORDER BY mediatag_id ASC");

while ($tag=$database->database_fetch_assoc($tags))
    {
    $taggeduser = new se_user();

    if ($tag[user_id] != NULL)
        {
        $taggeduser->user_exists             =1;
        $taggeduser->user_info[user_id]      =$tag[user_id];
        $taggeduser->user_info[user_username]=$tag[user_username];
        $taggeduser->user_info[user_fname]   =$tag[user_fname];
        $taggeduser->user_info[user_lname]   =$tag[user_lname];
        $taggeduser->user_displayname();
        }
    else
        {
        $taggeduser->user_exists=0;
        }

    $tag[tagged_user]=$taggeduser;
    $tag_array[]     =$tag;
    }

if ($task == 'ajax')
    {
    $media_keys   =array_keys($media_array_tmp);
    $current_index=(array_search($media_info['media_id'], $media_keys));

    if ($current_index == 0)
        {
        $previous_index=$media_count - 1;
        }
    else
        {
        $previous_index=$current_index - 1;
        }

    if ($current_index + 1 == $media_count)
        $next_index=0;
    else
        $next_index=$current_index + 1;

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

    $posted_date_array=$datetime->time_since($media_info['media_date']);
    $posted_date      =SELanguage::get($posted_date_array[0], Array($posted_date_array[1]));
    $media_path       =$url->url_userdir(
                           $owner->user_info['user_id']) . $media_info['media_id'] . "." . $media_info['media_ext'];
    $mediy_title      =($media_info['media_title'] != "") ? $media_info['media_title'] : SELanguage::get(589);
    $report_content   ="javascript:TB_show('" . SELanguage::get(1000148)
        . "', 'user_report.php?return_url={$url->url_create('album_file', $owner->user_info['user_username'], $album_info['album_id'], $current_num)}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');";

    $json_array=Array
        (
        "result"         => "success",
        "img_src"        => $url->url_userdir($owner->user_info['user_id']) . $media_info['media_id'] . '.'
            . $media_info['media_ext'],
        "last"           => $media_keys[$previous_index],
        "next"           => $media_keys[$next_index],
        "user_name"      => $owner->user_info['user_username'],
        "current_num"    => $current_num,
        "media_dir"      => $url->url_userdir($owner->user_info['user_id']),
        "current_index"  => $current_index,
        "tags"           => $tag_array_temp,
        "album_id"       => $album_info['album_id'],
        "media_id"       => $media_info['media_id'],
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
        "media_width"    => $media_info['media_width'],
        "media_height"   => $media_info['media_height'],
        "media_caption"  => ($media_info['media_width'] > 300) ? $media_info['media_width'] : 300,
        "initialTotal"   => ($total_comments > 0) ? $total_comments : 0,
        "title" =>  $media_info['media_title'],
        "desc" =>  $media_info['media_desc']
        );

    $json_str=json_encode($json_array);

    echo $json_str;
    exit();
    }

// SET GLOBAL PAGE TITLE
$global_page_title[0]      =1000158;
$global_page_title[1]      =$owner->user_displayname;
$global_page_title[2]      =$media_info[media_title];
$global_page_description[0]=1000159;
$global_page_description[1]=$media_info[media_desc];

// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('album_info', $album_info);
$smarty->assign('media_array_tmp', $media_array_tmp);
$smarty->assign('media_info', $media_info);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_tag', $allowed_to_tag);
$smarty->assign('media', $media_array);
$smarty->assign('media_keys', array_keys($media_array_tmp));
$smarty->assign('tags', $tag_array);
$smarty->assign('media_count_pages', ceil($media_count / 7));
$smarty->assign('media_count', $media_count);
$smarty->assign('media_ids_array', $media_ids_array);
$smarty->assign('media_count', count($media_array_tmp));
include "footer.php";
?>