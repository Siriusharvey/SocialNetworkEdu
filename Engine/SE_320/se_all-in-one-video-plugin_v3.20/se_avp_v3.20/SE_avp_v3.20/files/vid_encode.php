<?php
ob_start();
$page = "vid_encode";
include "header.php";

$vid_curlb_cmd = "SELECT vid_id FROM se_vids WHERE vid_is_converted='2'";
$vid_curlb_query = $database->database_query($vid_curl_cmd);

$vid_encode_cmd = "SELECT vid_id, vid_user_id, vid_location, vid_title, vid_privacy FROM se_vids WHERE vid_is_converted='3' ORDER BY vid_datecreated DESC LIMIT 0,10";
$vid_encode_query = $database->database_query($vid_encode_cmd);

if ($database->database_num_rows($vid_encode_query) > 0 && $database->database_num_rows($vid_curlb_query) < 4) {

     define('EZFFMPEG_BIN_PATH', $vid_settings[ffmpeg]);
     define('EZFFMPEG_FLVTOOL2_PATH', $vid_settings[flvtool2]);

     include './include/functions_vidffmpeg.php';
     include './include/class_cropcanvas.php';

     $cc = new canvasCrop();

     $tmp_dir = './uploads_vid/tmp/';
     $log_dir = './uploads_vid/log/';

     while ($vid_encode = $database->database_fetch_assoc($vid_encode_query)) {

          $video_output_dir = $video->video_dir($vid_encode[vid_user_id]);
          $output = $video_output_dir.$vid_encode[vid_location];
          $file = $tmp_dir.$vid_encode[vid_location];

          $database->database_query("UPDATE se_vids SET vid_is_converted='2' WHERE vid_location='".$vid_encode[vid_location]."' AND vid_is_converted='3'");

          if ($database->database_affected_rows() == 1) {

               $data = ezffmpeg_vdofile_infos($file);
               $res = explode("x", $data['vdo_res']);

               if ($res[0] > 0 && $res[1] > 0) {

                    $dimensions = $video->dimensions($vid_settings[width], $vid_settings[height], $res[0], $res[1]);

                    $widtha = explode('.', $dimensions['width']);
                    $heighta = explode('.', $dimensions['height']);
                  
                    $widtha = vid_round_nearest($widtha[0]);
                    $heighta = vid_round_nearest($heighta[0]);

                    $new_res = $widtha."x".$heighta;

                    if ($vid_settings[thumb_width]<=130) {
                         $max_width = 130;
                    } else {
                         $max_width = $vid_settings[thumb_width];
                    }

                    if ($vid_settings[thumb_height]<=97) {
                         $max_height = 97;
                    } else {
                         $max_height = $vid_settings[thumb_height];
                    }

                    $width = $max_width;
                    $height = $max_width/$widtha*$heighta;

                    if ($height < $max_height) {
                         $height = $max_height;
                         $width = $max_height/$heighta*$widtha;
                    }   

                    $width = explode('.', $width);
                    $height = explode('.', $height);
                    
                    $width = vid_round_nearest($width[0]);
                    $height = vid_round_nearest($height[0]);

                    $img_res = $width."x".$height;

                    $vid_position = $data['vdo_duration_seconds']/2;

                    if ($width > 0 && $height > 0) {

                         ezffmpeg_vdofile_capture_jpg($file, $output."_thumb_default.jpg", (int)$vid_position, $img_res);

                         $cc->loadImage($output.'_thumb_default.jpg');
                         $cc->cropBySize($width-$vid_settings[thumb_width], $height-$vid_settings[thumb_height], ccCENTER);
                         $cc->saveImage($output.'_thumb_0.jpg');

                         $cc->flushImages();

                         $cc->loadImage($output.'_thumb_default.jpg');
                         $cc->cropBySize($width-130, $height-97, ccCENTER);
                         $cc->saveImage($output.'_thumb_1.jpg');

                         $cc->flushImages();

                         $cmd = EZFFMPEG_BIN_PATH." -i ".$file." -sameq -ab 64k -ar 44100 -vcodec flv -f flv -r 25 -s ".$new_res." ".$output.".flv 2>&1";
                         exec($cmd, $vid_ffmpeg_result);

                         $cmd = EZFFMPEG_FLVTOOL2_PATH." -U ".$output.".flv";
                         exec($cmd);
     
                         if($vid_ffmpeg_result[0]!='') {
                              foreach($vid_ffmpeg_result as $key => $value){
                                   $to_be .= $value.' ';
                              }
                         }

                         if(!file_exists($output."_thumb_default.jpg") OR !file_exists($output."_thumb_1.jpg") OR !file_exists($output."_thumb_0.jpg") OR !file_exists($output.".flv") OR filesize($output.".flv") == 0) {

                              $database->database_query("UPDATE se_vids SET vid_is_converted = '4' WHERE vid_location = '".$vid_encode[vid_location]."'");

                              if($to_be!='' && (!file_exists($output.".flv") OR filesize($output.".flv") == 0)) {
                                   $log = $log_dir.$vid_encode[vid_location].'_ffmpeg.log';
                                   $handle = fopen($log, 'w') or die("can't open file");
                                   fwrite($handle, $to_be);
                                   fclose($handle);
                              }

                              unlink($file);
                              continue;

                         } else {

                              $query2 = "SELECT user_username FROM se_users WHERE user_id='".$vid_encode[vid_user_id]."'";
                              $query2 = $database->database_query($query2);
                              $video_data2 = $database->database_fetch_assoc($query2);
                              $user = new SEUser(Array('', $video_data2[user_username]));

                              $database->database_query("UPDATE se_vids SET vid_is_converted = '1' WHERE vid_location = '".$vid_encode[vid_location]."'");
        
                              $media_path = $output.'_thumb_1.jpg';

                              $vid_media[0] = Array('media_link' => $url->url_create('vid_file', $user->user_info[user_username], $vid_encode[vid_id]), 'media_path' => $media_path, 'media_width' => 130, 'media_height' => 10);

                              $actions->actions_add($user, "newvid", Array($user->user_info[user_username], $user->user_displayname, $vid_encode[vid_id], $vid_encode[vid_title]), $vid_media, 0, FALSE, "user", $user->user_info[user_id], $vid_encode[vid_privacy]);

                              unlink($file);
                              continue;

                         }
                    } else {

                         $database->database_query("UPDATE se_vids SET vid_is_converted = '4' WHERE vid_location = '".$vid_encode[vid_location]."'");

                         if($to_be!='' && (!file_exists($output.".flv") OR filesize($output.".flv") == 0)) {
                              $log = $log_dir.$vid_encode[vid_location].'_ffmpeg.log';
                              $handle = fopen($log, 'w') or die("can't open file");
                              fwrite($handle, $to_be);
                              fclose($handle);
                         }

                         unlink($file);
                         continue;

                    }
               } else {

                    $database->database_query("UPDATE se_vids SET vid_is_converted = '4' WHERE vid_location = '".$vid_encode[vid_location]."'");

                    if($to_be!='' && (!file_exists($output.".flv") OR filesize($output.".flv") == 0)) {
                         $log = $log_dir.$vid_encode[vid_location].'_ffmpeg.log';
                         $handle = fopen($log, 'w') or die("can't open file");
                         fwrite($handle, $to_be);
                         fclose($handle);
                    }

                    unlink($file);
                    continue;

               }
          } else {

               continue;

          }
     }
}
ob_end_clean();
exit();
?>