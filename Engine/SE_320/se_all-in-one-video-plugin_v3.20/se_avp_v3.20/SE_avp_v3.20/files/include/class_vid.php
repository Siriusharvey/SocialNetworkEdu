<?php
defined('SE_PAGE') or exit();
class se_vid
{

      // INITIALIZE VARIABLES
      var $video_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT

      var $_messages = array(1 => 13500109, 2 => 13500110, 3 => 13500111, 4 => 13500112, 5 => 13500113, 6 => 13500114, 7 => 13500122, 8 => 13500123, 9 => 13500165, 10 => 13500174, 11 => 13500177, 12 => 13500193); // AN ARRAY CONTAINING THE ERROR MESSAGES

      var $url = NULL;

      var $img = NULL;

      var $type = NULL;






      // THIS METHOD RETURNS AN ERROR MESSAGE
      // INPUT: $id REPRESENTINGAN AN ID OF THE ERROR
      // OUTPUT: A STRING REPRESENTING AN ERROR MESSAGE
      function vid_msg($id) {

        return $this->_messages[$id];

      } // END vid_msg() METHOD
      





      // THIS METHOD RETURNS AN ARRAY OF VIDEO SETTINGS
      // INPUT:
      // OUTPUT: AN ARRAY OF VIDEO SETTINGS
      function vid_settings() {
        global $database;

        $vid_settings = array();

        $data = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_vidsettings"));

        $vid_settings[skin] = $data['setting_vid_skin'];
        $vid_settings[ffmpeg] = $data['setting_vid_ffmpeg_path'];
        $vid_settings[flvtool2] = $data['setting_vid_flvtool2_path'];
        $vid_settings[permission] = $data['setting_permission_vid'];
        $vid_settings[width] = $data['vid_width'];
        $vid_settings[height] = $data['vid_height'];
        $vid_settings[thumb_width] = $data['vid_thumb_width'];
        $vid_settings[thumb_height] = $data['vid_thumb_height'];
        $vid_settings[mimes] = $data['setting_vid_mimes'];
        $vid_settings[exts] = $data['setting_vid_exts'];
        $vid_settings[yt] = $data['setting_yt_streaming'];
        $vid_settings[logo] = $data['vid_logo'];
        $vid_settings[disable] = $data['vid_prov_disable'];
        $vid_settings[embed] = $data['setting_vid_embed'];

        return $vid_settings;

      } // END vid_settings() METHOD






      // THIS METHOD RETURNS THE PATH TO THE GIVEN USER'S VIDEO DIRECTORY
      // INPUT: $user_id (OPTIONAL) REPRESENTING A USER'S USER_ID
      // OUTPUT: A STRING REPRESENTING THE RELATIVE PATH TO THE USER'S VIDEO DIRECTORY
      function video_dir($user_id = 0) {

        $subdir = $user_id+999-(($user_id-1)%1000);
        $videodir = "./uploads_vid/$subdir/$user_id/";
        return $videodir;

      } // END video_dir() METHOD






      // THIS METHOD AUTOMATICALLY CONVERTS URLS EMBEDDED IN A PIECE OF TEXT IN TO LINKS
      // INPUT: $text REPRESENTING PIECE OF TEXT TO BE PROCESSED
      // OUTPUT: A STRING REPRESENTING THE FINAL TEXT
      function convertLinks($text) {
        $text = preg_replace("/<br>/", " <br> ", $text);
        $text = preg_replace("/(\r\n|\n|\r)/", "\n", $text);
        $lines = explode("\n", $text);
        for ($x = 0, $y = count($lines); $x < $y; $x++) {
          $line = $lines[$x];
          $words = explode(' ', $line);
          for ($i = 0, $j = count($words); $i < $j; $i++) {
            $word = $words[$i];
            $punctuation = '.,\'")(<>;:'; // Links may not end in these
            if (substr($word, 0, 7) == 'http://' || substr($word, 0, 4) == 'www.') {
              $trailing = '';
              // Knock off ending punctuation
              $last = substr($word, -1);
              while (strpos($punctuation, $last) !== false) {
              // Last character is punctuation - eliminate it
              $trailing .= $last;
              $word = substr($word, 0, -1);
              $last = substr($word, -1);
              }
              // Make link, add trailing punctuation back afterwards
              $link = $word;
              if (substr($link, 0, 4) == 'www.') {
                // This link needs an http://
                $link = 'http://'.$link;
              }
              $word = '<a href="'.$link.'">'.$word.'</a>'.$trailing;
            }
            $words[$i] = $word;
          }
          $lines[$x] = implode(' ', $words);
        }
        return implode("\n", $lines);

      } // END convertLinks() METHOD






      // THIS METHOD RETURNS THE ARRAY OF VIDEOS
      // INPUT:
      // OUTPUT: THE ARRAY OF VIDEOS
      function vid_list($userid = 0, $page, $exception = 0, $p = 0, $max = 0, $total = 0, $where = "", $user_exists = TRUE, $check_privacy = TRUE) {
        global $database, $owner, $user;

        if ($user_exists === TRUE) {
            if ($userid == 0){
                $user_part = " vid_user_id='".$owner->user_info[user_id]."' AND";
            } else {
                $user_part = " vid_user_id='".$userid."' AND";
            }
        }

        $query = "SELECT *, se_users.user_username FROM se_vids LEFT JOIN se_users ON se_vids.vid_user_id=se_users.user_id WHERE".$user_part." vid_is_converted=1 AND vid_id<>'".$exception."'";
        if ($where != "") {
            $query .= " ".$where;
        }
        $vids_query = $database->database_query($query);

        if ($p >= 1 AND $total <> 0) {
        
            $num = 0;  
             
            while ($items = $database->database_fetch_assoc($vids_query)) {
              if ($check_privacy === TRUE) {
                $vid_author = new se_user();
                $vid_author->user_exists = 1;
                $vid_author->user_info['user_id'] = $items['vid_user_id'];
                $vid_author->user_info['user_username'] = $items['user_username'];
                $vid_author->user_info['user_fname'] = $items['user_fname'];
                $vid_author->user_info['user_lname'] = $items['user_lname'];
                $vid_author->user_info['user_photo'] = $items['user_photo'];
                $vid_author->user_displayname();
                                 
                $vid_privacy_max = $vid_author->user_privacy_max($user);
                                                 
                if(!($items['vid_privacy'] & $vid_privacy_max)) {
                   continue;
                } else {
                   $num += 1;
                }      
              } else {
                $num += 1;
              }          
            }

            $page_vars = $this->vid_page_vars($p, $num, $total);

            $query .= " ORDER BY vid_datecreated DESC LIMIT $page_vars[3],$total";

            $vids_query = $database->database_query($query);
        }

        if ($max != 0) {

            $query .= " LIMIT 0,$max";

            $vids_query = $database->database_query($query);
        }

        $max_rating = 5;
  
        // SET PROFILE MENU VARS
        if($database->database_affected_rows($vids_query) != 0) {

          $jpallvideos_array = Array();

          while ($items = $database->database_fetch_assoc($vids_query)) {

            $category = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_vidcats WHERE vidcat_id='".$items['vid_cat']."'"));
               
            $rating_full = floor($items[vid_rating_value]);
               
            if($rating_full != $items[vid_rating_value]) { $rating_partial = 1; } else { $rating_partial = 0; }
            $rating_empty = $max_rating-($rating_full+$rating_partial);
               
            $video_dir = $this->video_dir($items[vid_user_id]);

            $vid_locations = explode(',', $items['vid_location']);
               
            if (count($vid_locations) == 2) {
              $vid_location = $vid_locations[0];
              $vid_img_location = $video_dir.$items[vid_id];
              $vid_type = 'youtube';
            } else {
              $vid_location = $video_dir.$vid_locations[0].'flv';
              $vid_img_location = $video_dir.$vid_locations[0];
              $vid_type = 'self';
            }

            $jpvideo_desc = $items['vid_desc'];

            if ($page === TRUE) {
                 if (preg_match('/<br>/', $jpvideo_desc)) {
                     $jpvideo_desc = preg_split('/<br>/', $jpvideo_desc);
                     $jpvideo_desc = $jpvideo_desc[0].'...';
                 }
            }
               
	          if ($check_privacy === TRUE) {
                 $vid_author = new se_user();
                 $vid_author->user_exists = 1;
                 $vid_author->user_info['user_id'] = $items['vid_user_id'];
                 $vid_author->user_info['user_username'] = $items['user_username'];
                 $vid_author->user_info['user_fname'] = $items['user_fname'];
                 $vid_author->user_info['user_lname'] = $items['user_lname'];
                 $vid_author->user_info['user_photo'] = $items['user_photo'];
                 $vid_author->user_displayname();
                 
                 $vid_privacy_max = $vid_author->user_privacy_max($user);
                 	          	   
                 if(!($items['vid_privacy'] & $vid_privacy_max)) {
                      continue;
                 } else {
                      $jpallvideos_array[] = array('user_id' => $items['vid_user_id'], 'username' => $items['user_username'], 'title' => $items['vid_title'], 'desc' => $items['vid_desc'], 'desc_user' => $jpvideo_desc, 'tags' => $items['vid_tags'], 'cat_id' => $category['vidcat_id'], 'cat_lang' => (int)$category['vidcat_languagevar_id'], 'location' => $vid_location, 'date' =>  $items['vid_datecreated'], 'img' => $vid_img_location, 'id' => $items['vid_id'], 'views' => $items['vid_views'], 'full' => $rating_full, 'empty' => $rating_empty, 'partial' => $rating_partial, 'type' => $vid_type, 'comments' => $items['vid_comments'], 'privacy' => $items['vid_privacy'], 'search' => $items['vid_search']);
                 }      
            } else {
                 $jpallvideos_array[] = array('user_id' => $items['vid_user_id'], 'username' => $items['user_username'], 'title' => $items['vid_title'], 'desc' => $items['vid_desc'], 'desc_user' => $jpvideo_desc, 'tags' => $items['vid_tags'], 'cat_id' => $category['vidcat_id'], 'cat_lang' => (int)$category['vidcat_languagevar_id'], 'location' => $vid_location, 'date' =>  $items['vid_datecreated'], 'img' => $vid_img_location, 'id' => $items['vid_id'], 'views' => $items['vid_views'], 'full' => $rating_full, 'empty' => $rating_empty, 'partial' => $rating_partial, 'type' => $vid_type, 'comments' => $items['vid_comments'], 'privacy' => $items['vid_privacy'], 'search' => $items['vid_search']);
            }          
          }
        }

        if ($p >= 1) {
            $jpvideos_whole_array['page_vars'] = $page_vars;
            $jpvideos_whole_array['videos'] = $jpallvideos_array;
        } else {
            $jpvideos_whole_array['videos'] = $jpallvideos_array;
        }

        return $jpvideos_whole_array;

      } // END vid_list() METHOD






      // THIS METHOD RETURNS THE NUMBER OF TOTAL VIDEOS
      // INPUT: $id REPRESENTING THE USER ID
      // OUTPUT: THE NUMBER OF TOTAL VIDEOS
      function vid_total($id) {
        global $database;

        // BEGIN VIDEO QUERY
        $vid_query = "SELECT * FROM se_vids WHERE vid_user_id=$id AND vid_is_converted='1'";

        // GET AND RETURN TOTAL VIDEOS
        $vid_total = $database->database_num_rows($database->database_query($vid_query));
        return $vid_total;

      } // END vid_total METHOD






      // THIS METHOD RETURNS ARRAY OF THE VIDEO
      // INPUT: $video_id REPRESENTING AN ID OF THE VIDEO
      // OUTPUT: ARRAY OF THE VIDEO
      function info($video_id) {
        global $database, $owner, $video_api;

        $query = $database->database_query("SELECT * FROM se_vids WHERE vid_user_id='".$owner->user_info[user_id]."' AND vid_id='".$video_id."'");
     
        if ($database->database_affected_rows($query) == 0) {
          $this->video_error = 1;
        } else {
          while ($items = $database->database_fetch_assoc($query)) {

            $dir = $this->video_dir($owner->user_info[user_id]);
            $vid_locations = explode(',', $items['vid_location']);
            $vidset = $this->vid_settings();
            $direct = $vidset[yt];
            $directly = $vidset[embed];

            if (count($vid_locations) == 2) {
               $video_api->getVideoType($vid_locations[0]);
               $vid_type = $video_api->type;
               if ($directly == 1) {
                   $vid_location = urlencode($video_api->catchURL());
                   if($direct == 1) {
                       $vid_location = urlencode($vid_locations[0]);
                       $vid_type = 'youtube_api';
                   }
               } else {
                   $vid_location = $video_api->catchEmbed();
               }
               $vid_img_location = $dir.$items[vid_id].'.jpg';
            } else {
               $vid_location = urlencode($vid_locations[0]);
               $vid_img_location = $dir.$vid_locations[0].'_thumb_1.jpg';
               $vid_type = "self";
            }

            $max_rating = 5;

            $rating_query = $database->database_query("SELECT * FROM se_vidratings WHERE rating_object_table='se_vids' AND rating_object_primary='vid_id' AND rating_object_id='".$items['vid_id']."'");

            if($database->database_num_rows($rating_query) != 1) {
              $rating_info['rating_id'] = 0;
              $rating_info['rating_value'] = 0;
              $rating_info['rating_raters'] = "";
              $rating_info['rating_raters_num'] = 0;
            } else {
              $rating_info = $database->database_fetch_assoc($rating_query);
            }

            $rating_full = floor($rating_info[rating_value]);
   
            if($rating_full != $rating_info[rating_value]) { $rating_partial = 1; } else { $rating_partial = 0; }
            $rating_empty = $max_rating-($rating_full+$rating_partial);

            $tags = explode(" ", $items['vid_tags']);

            $cats = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_vidcats WHERE vidcat_id='".$items['vid_cat']."'"));

            if((strlen($items['vid_desc']) > 160) || (substr_count($items['vid_desc'], '<br>') > 3)) {
                $vid_desc_short = explode(" ", $items['vid_desc']);

                $i = 0;
                $j = 0;

                while($i < 160) {
                   $vid_desc .= $vid_desc_short[$j]." ";
                   $i = strlen($vid_desc);
                   $j++;
                }

                $vid_desc = $this->convertLinks($vid_desc);
                $vid_desc = preg_replace('/<br>/', '-', $vid_desc);
                $vid_desc = $vid_desc.'...';
            } else {
                $vid_desc = $this->convertLinks($items['vid_desc']);
            }

            $items['vid_desc'] = $this->convertLinks($items['vid_desc']);

            $vids_array = array('title' => $items['vid_title'], 'desc_short' => $vid_desc, 'desc' => $items['vid_desc'], 'cat_id' => $cats['vidcat_id'], 'cat_lang' => (int)$cats['vidcat_languagevar_id'], 'location' => $vid_location, 'date' =>  $items['vid_datecreated'], 'img' => $vid_img_location, 'type' => $vid_type, 'tags' => $tags, 'id' => $items['vid_id'], 'user_id' => $items['vid_user_id'], 'is_converted' => $items['vid_is_converted'], 'views' => $items['vid_views'], 'full' => $rating_full, 'empty' => $rating_empty, 'partial' => $rating_partial, 'vid_comments' => $items[vid_comments], 'vid_privacy' => $items[vid_privacy], 'directly' => $directly);

            return $vids_array;
          }
        }

      } // END info() METHOD






      // THIS METHOD RETURNS A DIMENSION OF THE VIDEO
      // INPUT: $max_width REPRESENTING MAX WIDTH OF THE VIDEO
      //        $max_height REPRESENTING MAX HEIGHT OF THE VIDEO
      //        $width REPRESENTING AN ORIGINAL WIDTH OF THE VIDEO
      //        $height REPRESENTING AN ORIGINAL HEIGHT OF THE VIDEO
      // OUTPUT: AN ARRAY OF DIMENSION FOR VIDEO CONVERION
      function dimensions($max_width, $max_height, $width, $height) {
          $dimension = $max_height/$height;
          $width *= $dimension;
          $height = $max_height;
          
          if($width > $max_width) {
            $dimension = $max_width/$width;
            $width = $max_width;
            $height *= $dimension;
          }
          
          $dimensions = array('width' => $width, 'height' => $height);
          
          return $dimensions;

      } // END dimensions() METHOD

        




      // THIS METHOD VALIDATES A VIDEOS DATA
      // INPUT: $title REPRESENTING A TITLE OF THE VIDEO
      //	  $desc REPRESENTING A DECRIPTION OF THE VIDEO
      //	  $tag REPRESENTING TAGS OF THE VIDEO
      //	  $location REPRESENTING A LOCATION OF THE VIDEO
      // OUTPUT: AN ERROR IF NOT VALID
      function validation($title, $desc, $tag, $location = '', $task = 0) {
          global $database, $video_api;
          if(trim($title) != '' && trim($desc) != '' && trim($tag) != '') {
              if($task == 1 AND $location != '') {
                $video_data = $video_api->getVideoType($location);
                if ($video_data === TRUE) {
                    $this->url = $video_api->url;
                    $this->type = $video_api->type;
                    if ($this->type != 'youtube') {
                        $c = curl_init();
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($c, CURLOPT_URL, $this->url);
                        $contents = curl_exec($c);
                        curl_close($c);
                    } else {
                        $url_test = explode("=", $this->url);
                        $url_test = "http://gdata.youtube.com/feeds/api/videos/".$url_test[1];
                        $c = curl_init();
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($c, CURLOPT_URL, $url_test);
                        $contents = curl_exec($c);
                        curl_close($c);
                    }
                    if ($contents) {
                        $this->img = $video_api->imgURL();
                        $location_video = $database->database_query("SELECT * FROM se_vids WHERE vid_location like \"%$this->url%\"");
                        if ($database->database_affected_rows($location_video) == 0) {
                            $this->video_error = 0;
                        } else {
                            $this->video_error = 2;
                        }
                    } else {
                        $this->video_error = 3;
                    }
                 } else {
                    $this->video_error = 3;
                 }
              } elseif ($task == 0) {
                  $this->video_error = 0;
              } else {
                  $this->video_error = 7;  
              }    
          } else {
              $this->video_error = 7;
          }

      } // END validation() METHOD






      // THIS METHOD ADDS VIDEO TO THE DATABASE AND RETURN LAST INSERT ID
      // INPUT: $datecreated REPRESENTING A DATE THE VIDEO WAS CREATED
      //	  $title REPRESENTING A TITLE OF THE VIDEO
      //	  $desc REPRESENTING A DECRIPTION OF THE VIDEO
      //	  $CAT REPRESENTING A CATEGORY OF THE VIDEO
      //	  $location REPRESENTING A LOCATION OF THE VIDEO
      //	  $tag REPRESENTING TAGS OF THE VIDEO
      // OUTPUT: LAST INSERT ID
      function add_video($datecreated, $title, $desc, $cat, $location, $tag, $task = '', $comments, $privacy, $search) {
          global $database, $user;

          $v1 = explode(",", $tag);
          $val = "";
          foreach ($v1 as $t)
          {
              $val .= $t." ";
          }

          $tag_clean = preg_replace('#\s{2,}#', ' ', $val);
          $tag_lower = strtolower($tag_clean);
          $tags = explode(" ", trim($tag_lower));
          $tags = array_unique($tags);

          $key_value = 0;
          $tags_final = array();

          foreach ($tags as $val) {
              $tags_final[$key_value] = $val;
              $key_value += 1;
          }

          $tags_clean = implode(" ", $tags_final);

          for($i = 0; $i < count($tags_final); $i++) {
              $sql = "UPDATE se_vidtags SET value = value+1 WHERE tag = '".$tags_final[$i]."'";
              $result = $database->database_query($sql);
              if ($database->database_affected_rows($result) == 0) {
                $sql = "INSERT INTO se_vidtags (tag, value) VALUES ('".$tags_final[$i]."', 1)";
                $result = $database->database_query($sql);
              }
          }

          $views = 0;

          if ($task == "add_vid_youtube") {
               $is_converted = 1;
          } else {
               $is_converted = 3;
          }

          $database->database_query("INSERT INTO se_vids (
            vid_user_id,
            vid_datecreated,
            vid_title,
            vid_desc,
            vid_cat,
            vid_tags,
            vid_location,
            vid_views,
            vid_is_converted,
            vid_comments,
            vid_privacy,
            vid_search
            ) VALUES (
            '".$user->user_info[user_id]."',
            '".$datecreated."',
            '".trim($title)."',
            '".trim($desc)."',
            '".$cat."',
            '".$tags_clean."',
            '".trim($location)."',
            '".$views."',
            '".$is_converted."',
            '".$comments."',
            '".$privacy."',
            '".$search."')
            ");

      } // END add_video() METHOD






      // THIS METHOD DELETES THE VIDEO AND ALL OF IT'S DATA FROM THE DATABASE AND DIR
      // INPUT: $id REPRESENTING THE ID OF THE VIDEO
      // OUTPUT:
      function delete_video($id, $user_id = '', $admin = FALSE)
      {
          global $database, $user;

          if ($user_id == '') {
               $user_id = $user->user_info[user_id];
          }

          $sql = "SELECT vid_tags FROM se_vids WHERE vid_id=$id";
          $result = $database->database_query($sql);
          $tag_clean = $database->database_fetch_assoc($result);
          $tag_clean = $tag_clean['vid_tags'];

          $actiontype_info = $database->database_fetch_assoc($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='newvid'"));
          $action_info = $database->database_query("DELETE FROM se_actions WHERE action_actiontype_id='".$actiontype_info['actiontype_id']."' AND action_user_id='".$user_id."' AND action_text LIKE '%\"$id\"%'");

          $commenttype_info = $database->database_fetch_assoc($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='vidcomment'"));
          $comment_info = $database->database_query("DELETE FROM se_actions WHERE action_actiontype_id='".$commenttype_info['actiontype_id']."' AND action_user_id='".$user_id."' AND action_text LIKE '%\"$id\"%'");

          // DELETE VIDEO AND THUMBNAILS
          $data = $database->database_fetch_assoc($database->database_query("SELECT vid_location, vid_id FROM se_vids WHERE vid_id=$id"));

          $vid_id = $data['vid_id'];
          $data = explode(',', $data['vid_location']);

          if (!$data[1]) {
              if ($admin === FALSE) {
                  $video_path = $this->video_dir($user_id).$data[0].".flv";
                  if(file_exists($video_path)) { unlink($video_path); }
                  $thumb_path = $this->video_dir($user_id).$data[0]."_thumb_0.jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
                  $thumb_path = $this->video_dir($user_id).$data[0]."_thumb_1.jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
                  $thumb_path = $this->video_dir($user_id).$data[0]."_thumb_default.jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
              } else {
                  $video_path = ".".$this->video_dir($user_id).$data[0].".flv";
                  if(file_exists($video_path)) { unlink($video_path); }
                  $thumb_path = ".".$this->video_dir($user_id).$data[0]."_thumb_0.jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
                  $thumb_path = ".".$this->video_dir($user_id).$data[0]."_thumb_1.jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
                  $thumb_path = ".".$this->video_dir($user_id).$data[0]."_thumb_default.jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
              }
          } else {
              if ($admin === FALSE) {
                  $thumb_path = $this->video_dir($user_id).$vid_id.".jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
              } else {
                  $thumb_path = ".".$this->video_dir($user_id).$vid_id.".jpg";
                  if(file_exists($thumb_path)) { unlink($thumb_path); }
              }
          }
          
          $database->database_query("DELETE FROM se_vids WHERE vid_id=$id AND vid_user_id='".$user_id."'");
          $database->database_query("DELETE FROM se_vidcomments WHERE vidcomment_vid_id=$id");

          $tags = explode(" ", $tag_clean);

          for($i = 0; $i < count($tags); $i++) {
            $database->database_query("UPDATE se_vidtags SET value = value-1 WHERE tag = '".$tags[$i]."'");
          }

          $database->database_query("DELETE FROM se_vidtags WHERE value=0");

      } // END delete_video() METHOD






      // THIS METHOD CREATES AN ARRAY CONTAINING THE DATA FOR THE TAG CLOUD
      // INPUT:
      // OUTPUT: AN ARRAY CONTAINING DATA FOR THE TAG CLOUD
      function CreateTagArray() {
            global $database;

            $sql = "SELECT * FROM se_vidtags ORDER BY value DESC LIMIT 0, 50";
            $result = $database->database_query($sql);
            if ($database->database_affected_rows($result) != 0) {
                 $tag_cloud = array();
                 while($row = mysql_fetch_array($result)) {
                     $tag_cloud[$row['tag']] = $row['value'];
                 }
                 $tags_array = $tag_cloud;
            }
            return $tags_array;

      } // END CreateTagArray() METHOD






      // THIS METHOD RETURNS ARRAY CONTAINING THE TAG CLOUD
      // INPUT: $tags REPRESENTING AN ARRAY CONTAINING THE DATA FOR THE TAG CLOUD
      // OUTPUT: AN ARRAY CONTAINING THE TAG CLOUD
      function printTagCloud($tags)
      {
          // $tags is the array
         
          arsort($tags);
         
          $max_size = 32; // max font size in pixels
          $min_size = 12; // min font size in pixels
         
          // largest and smallest array values
          $max_qty = max(array_values($tags));
          $min_qty = min(array_values($tags));
         
          // find the range of values
          $spread = $max_qty - $min_qty;
          if ($spread == 0) { // we don't want to divide by zero
                  $spread = 1;
          }
         
          // set the font-size increment
          $step = ($max_size - $min_size) / ($spread);
         
          // loop through the tag array
          foreach ($tags as $key => $value) {
                  // calculate font-size
                  // find the $value in excess of $min_qty
                  // multiply by the font-size increment ($size)
                  // and add the $min_size set above
                  $size = round($min_size + (($value - $min_qty) * $step));
                  $cloud[] = '<a href="browse_vids.php?q='.$key.'&type=tag" style="font-size: ' . $size . 'px" title="' . SE_Language::get('13500161', array($value, $key)) . '">' . $key . '</a>';
          }
          shuffle($cloud);
          return $cloud;

      } // END printTagCloud() METHOD






      // THIS METHOD RETURNS AN ARRAY CONTAINING CATEGORIES
      // INPUT: $admin REPRESENTING TRUE OR FALSE
      // OUTPUT: AN ARRAY CONTAINING CATEGORIES
      function vid_category_list($admin) {
            global $database;
      
            $sql = "SELECT * FROM se_vidcats";
            
            if($admin == 'true') {
                 $sql .= " WHERE vidcat_id>1";
            }
            
            $sql .= " ORDER BY vidcat_id ASC";
            
            $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

            $vidcats_array = array();
            while($result = $database->database_fetch_assoc($resource)) {  
                 $vidcats_array[] = array('vidcat_id' => $result['vidcat_id'], 'vidcat_title' => $result['vidcat_title'], 'vidcat_languagevar_id' => $result['vidcat_languagevar_id'], 'vidcat_parentcat_id' => $result['vidcat_parentcat_id']);
            }
      
            return $vidcats_array;

      } // END vid_category_list() METHOD






      // 
      // INPUT:
      // OUTPUT:
      function vid_page_vars($p, $total, $per) {

        $num_pages = $total/$per;
        $round_down = explode(".",$num_pages);

        if ($this->is_int($num_pages) === TRUE) {
            $pages = $round_down[0];
        } else {
            $pages = $round_down[0]+1;
        }

        if($p > $pages) {
             $p = $pages;
        } elseif($p < 1) {
             $p = 1;
        }

        $p_start = ($p-1)*$per;

        $p_start_lang = $p_start+1;

        if($p == $pages) {
             $p_end = $per-($p*$per-$total);
        } else {
             $p_end = $per;
        }

        if($p == 1) {
             $p_end_lang = $p_end;
        } else {
             $p_end_lang = ($p-1)*$per+$p_end;
        }
        
        $page_vars = array($p, $total, $per, $p_start, $p_start_lang, $p_end, $p_end_lang, $pages);
        return $page_vars;

      } // END vid_page_vars() METHOD






      // THIS METHOD CHECKS IF THE VARIABLE IS AN INTEGER AND RETURNS TRUE OR FALSE
      // INPUT: $num REPRESENTING THE DATA TO BE PROCESSED
      // OUTPUT: TRUE OR FALSE
      function is_int($num) {

            return (is_numeric($num) && intval($num) == $num) ? true : false;

      } // END is_int() METHOD






      // THIS METHOD RETURNS HMS CONVERTED FROM SECONDS
      // INPUT: $sec REPRESENTING SECONDS
      // OUTPUT: HMS
      function sec2hms($sec, $padHours = true) {
            $hms = "";
            $hours = intval(intval($sec) / 3600); 
            $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':' : $hours. ':';
            $minutes = intval(($sec / 60) % 60); 
            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
            $seconds = intval($sec % 60); 
            $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
            return $hms;
      }   // END sec2hms() METHOD

}
?>