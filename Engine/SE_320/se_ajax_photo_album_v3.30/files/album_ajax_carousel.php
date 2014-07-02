<?php
include "header.php";

$page           ="album_ajax_carousel";
$pagest         =security($_GET['p']);
$owner_id       =security($_GET['user_id']);
$album_id       =security($_GET['album_id']);
$profile        =security($_GET['profile']);
$album_owner    =new se_user(Array($user_id));

$starting_number=($pagest - 1) * 7;

if ($profile == 'true')
    {
    $photo_query="";
    ($hook=SE_Hook::exists('se_mediatag')) ? SE_Hook::call($hook, array()) : NULL;
    $photo_query="SELECT DISTINCT * 
                    FROM (" . $photo_query
        . " ORDER BY mediatag_date DESC ) AS tmp  GROUP BY media_id ORDER BY mediatag_date DESC ";
    //   $photo_query .= " ORDER BY mediatag_date DESC";     
    $photo_query.=" LIMIT $starting_number, 7";
    $media_result=$database->database_query("$photo_query");
    }
else
    {
    $media_result=$database->database_query("
  SELECT media_id, media_ext, '" . $owner_id . "' AS album_user_id
  FROM se_media
  WHERE media_album_id='" . $album_id . "'
  ORDER BY media_order ASC 
  LIMIT " . $starting_number . ", 7
  ");
    }

$media_array=Array();

$tmp_array=Array();

$i     =0;
$my_url=new se_url();

while ($media=$database->database_fetch_assoc($media_result))
    {

    // settig path for images 
    if ($profile == 'true' and (($media['media_ext'] == "jpeg") or ($media['media_ext'] == "jpg")
                                   or ($media['media_ext'] == "gif") or ($media['media_ext'] == "png")
                                   or ($media['media_ext'] == "bmp")))
        {
        if (in_array($media['media_id'], $tmp_array))
            continue;

        $media['file_dir']  =$media['media_dir'];
        $media['file_thumb']=$media['file_dir'] . $media['media_id'] . '_thumb.jpg';
        $media['file_src']  =$media['file_dir'] . $media['media_id'] . '.jpg';
        $tmp_array[]        =$media['media_id'];
        }
    elseif (($media['media_ext'] == "jpeg") or ($media['media_ext'] == "jpg") or ($media['media_ext'] == "gif")
        or ($media['media_ext'] == "png") or ($media['media_ext'] == "bmp"))
        {
        $media['file_dir']  =$my_url->url_userdir($media['album_user_id']);
        $media['file_thumb']=$media['file_dir'] . $media['media_id'] . '_thumb.jpg';
        $media['file_src']  =$media['file_dir'] . $media['media_id'] . '.jpg';
        }
    // settig path for audio        
    elseif (($media['media_ext'] == "mp3") or ($media['media_ext'] == "mp4") or ($media['media_ext'] == "wav"))
        {
        $media['file_thumb']='./images/icons/audio_big.gif';
        $media['file_src']  ='./images/icons/audio_big.gif';
        }
    // settig path for video        
    elseif (($media['media_ext'] == "mpeg") or ($media['media_ext'] == "mpg") or ($media['media_ext'] == "mpa")
        or ($media['media_ext'] == "avi") or ($media['media_ext'] == "swf") or ($media['media_ext'] == "mov")
        or ($media['media_ext'] == "ram") or ($media['media_ext'] == "rm"))
        {
        $media['file_thumb']='./images/icons/video_big.gif';
        $media['file_src']  ='./images/icons/video_big.gif';
        }
    // settig path for unknown        
    else
        {
        $media['file_thumb']='./images/icons/file_big.gif';
        $media['file_src']  ='./images/icons/file_big.gif';
        }

    $media_array[$i]=$media;

    ++$i;
    }

$smarty->assign('album_id', $album_id);
$smarty->assign('media', $media_array);
$smarty->assign('owner_id', $album_owner->user_info['user_username']);
$smarty->assign('file_thumb', $media['file_thumb']);
$smarty->assign('profile', $profile);

include "footer.php";
?>