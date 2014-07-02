<?php
defined('SE_PAGE') or exit();
class se_vid_api
{

      // INITIALIZE VARIABLES
      var $video_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT

      var $type = NULL;

      var $url = NULL;






      // THIS FUNCTION CHECK WHETHER THE URL EXIST
      // INPUT: $url REPRESENTING THE URL
      // OUTPUT: TRUE OR FALSE
      function page_exists($url){
          $c = curl_init();
          $url = trim($url);
          curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($c, CURLOPT_URL, $url);
          $contents = curl_exec($c);
          curl_close($c);
          if($contents) {
              return true;
          } else {
              return false;
          }
      } // END page_exists() FUNCTION






      // THIS FUNCTION CATCHES FLV URL
      // INPUT: $url REPRESENTING THE URL
      // OUTPUT: TRUE OR FALSE
      function getAllProviders(){

        $providers = array('Youtube.com', 'Video.Google.com', 'Metacafe.com', 'Break.com', 'Blip.tv');
        $provider_types = array('youtube', 'google', 'metacafe', 'break', 'bliptv');
        $provider_ids = array('level_vid_prov0', 'level_vid_prov1', 'level_vid_prov2', 'level_vid_prov3', 'level_vid_prov4');
        $provider_examples = array('http://www.youtube.com/watch?v=0_fPV13lKm4', 'http://video.google.com/videoplay?docid=5733044300866646599', 'http://www.metacafe.com/watch/1306556/i_have_this_ball_funny_videos/', 'http://www.break.com/index/the-ultimate-parkour-jump.html', 'http://blip.tv/file/1854578/');
        $provider_imgs = array('./images/imp_youtube.jpg', './images/imp_gvideo.jpg', './images/imp_mcafe.jpg', './images/imp_break.jpg', './images/imp_blip.jpg');
        $provider_urls = array('http://www.youtube.com/', 'http://video.google.com/', 'http://www.metacafe.com/', 'http://www.break.com/', 'http://blip.tv/');

        return array($providers, $provider_types, $provider_ids, $provider_examples, $provider_imgs, $provider_urls);

      } // END getAllProviders FUNCTION






      // THIS FUNCTION CATCHES FLV URL
      // INPUT: $url REPRESENTING THE URL
      // OUTPUT: TRUE OR FALSE
      function getVideoType($location, $add = 0){
          if(preg_match('/http:\/\/www\.youtube\.com\/watch\?v=[^&]+/', $location, $vresult)) {
              $type= 'youtube';
          } elseif(preg_match('/http:\/\/blip\.tv\/file\/[0-9]+/', $location, $vresult)) {
              $type= 'bliptv';
          } elseif(preg_match('/http:\/\/(.*?)break\.com\/(.*?)\/(.*?)\.html/', $location, $vresult)) {
              $type= 'break';
          } elseif(preg_match('/http:\/\/www\.metacafe\.com\/watch\/(.*?)\/(.*?)\//', $location, $vresult)) {
              $type= 'metacafe';
          } elseif(preg_match('/http:\/\/video\.google\.com\/videoplay\?docid=[^&]+/', $location, $vresult)) {
              $type= 'google';
          }

          $this->url = $vresult[0];
          $this->type = $type;

          if ($this->url AND $this->catchURL() != '') {
              return TRUE;
          } else {
              return FALSE;
          }

      } // END getVideoType() FUNCTION






      // THIS FUNCTION CATCHES FLV URL
      // INPUT: $url REPRESENTING THE URL
      // OUTPUT: TRUE OR FALSE
      function getAdminType($level_info){

        $level_info[level_vid_prov] = NULL;

        $providers = $this->getAllProviders();

        for ($i=0; $i<count($providers[0]); $i++) {
            $provider_name = 'level_vid_prov'.$i;
            $level_info[$provider_name] = NULL;
            if (isset($_POST[$provider_name])) { $level_info[$provider_name] = $_POST[$provider_name]; $level_info[level_vid_prov] .= ','.$providers[1][$i]; }
        }

        return $level_info;

      } // END getType() FUNCTION






      // THIS FUNCTION SOLVES IMAGE URL
      // INPUT: $real REPRESENTING THE VIDEO PAGE URL, $type REPRESENTING TYPE OF THE VIDEO AND $contents CONTAINS VIDEO PAGE DATA
      // OUTPUT: TRUE OR FALSE
      function imgURL(){

          $contents = trim(@file_get_contents($this->url));

          switch ($this->type) {
              case "youtube":
                  $location_img_url = str_replace('http://www.youtube.com/watch?v=', '', $this->url);
                  $img = 'http://img.youtube.com/vi/'.$location_img_url.'/0.jpg';
                  break;
              case "bliptv":
                  preg_match('/rel=\"image_src\" href=\"http:\/\/[^\"]+/', $contents, $result_img);
                  preg_match('/http:\/\/[^\"]+/', $result_img[0], $result_img);
                  $img = $result_img[0];
                  break;
              case "break":
                  preg_match('/meta name=\"embed_video_thumb_url\" content=\"http:\/\/[^\"]+/', $contents, $result_img);
                  preg_match('/http:\/\/[^\"]+/', $result_img[0], $result_img);
                  $img = $result_img[0];
                  break;
              case "metacafe":
                  preg_match('/thumb_image_src=http%3A%2F%2F(.*?)%2Fthumb%2F[0-9]+%2F[0-9]+%2F[0-9]+%2F(.*?)%2F[0-9]+%2F[0-9]+%2F(.*?)\.jpg/', $contents, $result_img);
                  preg_match('/http%3A%2F%2F(.*?)%2Fthumb%2F[0-9]+%2F[0-9]+%2F[0-9]+%2F(.*?)%2F[0-9]+%2F[0-9]+%2F(.*?)\.jpg/', $result_img[0], $result_img);
                  $img = urldecode($result_img[0]);
                  break;
              case "google":
                  preg_match('/http:\/\/[0-9]\.(.*?)\.com\/ThumbnailServer2%3Fapp%3D(.*?)%26contentid%3D(.*?)%26offsetms%3D(.*?)%26itag%3D(.*?)%26hl%3D(.*?)%26sigh%3D[^\\\\]+/', $contents, $result);
                  $img = urldecode($result[0]);
                  break;
          }

          return $img;

      } // END getType() FUNCTION






      // THIS FUNCTION CATCHES FLV URL
      // INPUT: $url REPRESENTING THE VIDEO PAGE URL
      // OUTPUT: ARRAY CONTAINING $location AND $type
      function catchData(){

          $newInfo = trim(@file_get_contents($this->url));

          switch ($this->type) {
              case "youtube":
                   $feed = explode("=", $this->url);
                   $feed = "http://gdata.youtube.com/feeds/api/videos/".$feed[1];
                   $newInfo = trim(@file_get_contents($feed));

                   preg_match('/<media:title(.*?)<\/media:title>/', $newInfo, $result);
                   $title = strip_tags($result[0]);

                   preg_match('/<media:description(.*?)<\/media:description>/', $newInfo, $result);
                   $desc = strip_tags($result[0]);

                   preg_match('/<media:keywords(.*?)<\/media:keywords>/', $newInfo, $result);
                   $tags = strip_tags(str_replace(",", "", $result[0]));

                   break;
              case "bliptv":
                   preg_match('/div id=\"EpisodeTitle\">(.*?)<\/div>/', $newInfo, $result);
                   $title = str_replace('div id="EpisodeTitle">', '', $result[0]);
                   $title = stripslashes(str_replace('</div>', '', $title));

                   preg_match('/div class=\'BlipDescription\'><p>(.*?)<\/p><\/div>/', $newInfo, $result);
                   $desc = str_replace('div class=\'BlipDescription\'><p>', '', $result[0]);
                   $desc = stripslashes(str_replace('</p></div>', '', $desc));
                   $desc = strip_tags(preg_replace("/<br(.*?)>/", "\n", $desc));

                   preg_match('/<a href=\'http:\/\/blip\.tv\/topics\/view\/(.*?)<\/a>\s/', $newInfo, $result);
                   $tags = strip_tags(str_replace(",", "", $result[0]));

                   break;
              case "metacafe":
                   $new_string = preg_replace("/\n|\r\n|\r$/", "", $newInfo);
                   $new_string = preg_replace("/>\s{2,}</", "> <", $new_string);

                   preg_match('/<h1 id=\"ItemTitle\">(.*?)<\/h1>/', $new_string, $result);
                   $title = preg_replace("/<br(.*?)>/", "\n", $result[0]);
                   $title = trim(strip_tags($title));

                   preg_match('/<div id=\"Desc\">(.*?)<\/div>/', $new_string, $result);
                   $desc = preg_replace("/<br(.*?)>/", "\n", $result[0]);
                   $desc = trim(strip_tags($desc));

                   preg_match('/<dd>(.*?)<\/dd>/', $new_string, $result);
                   $tags = preg_replace("/<br(.*?)>/", "\n", $result[0]);
                   $tags = trim(strip_tags($tags));

                   break;
              case "break":
                   $new_string = preg_replace("/\n|\r\n|\r$/", "", $newInfo);
                   $new_string = preg_replace("/>\s{2,}</", "> <", $new_string);

                   preg_match('/meta name="title" content="[^\"]+/', $new_string, $result);
                   $pos = strrpos($result[0], "\"");
                   $title = substr($result[0], $pos+1);

                   preg_match('/meta name=\"embed_video_description\" id=\"vid_desc\" content="[^\"]+/', $new_string, $result);
                   $pos = strrpos($result[0], "\"");
                   $desc = substr($result[0], $pos+1);

                   preg_match('/meta name="keywords" content="[^\"]+/', $new_string, $result);
                   $pos = strrpos($result[0], "\"");
                   $tags = str_replace(",", "", substr($result[0], $pos+1));

                   break;
              case "google":
                   $new_string = preg_replace("/\n|\r\n|\r$/", "", $newInfo);
                   $new_string = preg_replace("/>\s{2,}</", "> <", $new_string);
                   
                   preg_match('/<span id=details-title>(.*?)<\/span>/', $new_string, $result);
                   $title = trim(strip_tags($result[0]));                   

                   preg_match('/<p id=details-desc>(.*?)<p id=share-report>/', $new_string, $result);
                   $desc = trim(strip_tags($result[0]));
                   if (substr($desc, -7) == '&laquo;') {
                         $desc = substr($desc, 0, -7);
                   }

                   $tags = "";

                   break;
          }

          return array($title, $desc, $tags);

      } // END catchData() FUNCTION






      // THIS FUNCTION CATCHES FLV URL
      // INPUT: $url REPRESENTING THE VIDEO PAGE URL
      // OUTPUT: ARRAY CONTAINING $location AND $type
      function catchURL(){

          $url = $this->url;

          switch ($this->type) {
              case "youtube":
                   $urlArray = split("=", $url);
                   $videoid = trim($urlArray[1]);

                   $pageurl = $_SERVER["HTTP_REFERER"];
                   $newAPIurl = "http://www.youtube.com/get_video_info?&video_id=$videoid";
                   $newAPIurl .= "&el=embedded&ps=chromeless&eurl=$pageurl";

                   $newInfo = trim(@file_get_contents($newAPIurl));
                   $infoArray = split("&", $newInfo);
                   for ($i=0; $i < count($infoArray); $i++) {
                       $tmp = split("=", $infoArray[$i]);
                       $key = urldecode($tmp[0]);
                       $val = urldecode($tmp[1]);
                       $paramArray["$key"] = "$val";
                   }

                   if (array_key_exists("token", $paramArray)) {
                       $t = $paramArray["token"];
                   } else {
                       $legacyAPIurl="http://www.youtube.com/api2_rest?method=youtube.videos.get_video_token&video_id=$videoid";
                       $t = trim(strip_tags(@file_get_contents($legacyAPIurl)));
                   }

                   $vid_location = "http://www.youtube.com/get_video.php?video_id=$videoid&t=$t";

                   //$headers = get_headers($uri);
                   //print "<pre>\n";
                   //print " uri: $uri\n" ;
                   //print "videoid: $videoid\n";
                   //print " token: $token\n";
                   //print " fmt: $fmt\nheaders: ";
                   //print_r($headers);
                   //print "\n</pre>\n";
                   //exit;

                   //...debug

                   $response=http_test_existance($vid_location);
                   $uri=$response["location"];

                   $vid_location = $uri;

                   break;
              case "bliptv":
                   $newInfo = trim(@file_get_contents($url));
                   preg_match('/http:\/\/blip\.tv\/file\/get\/(.*?)\.flv/', $newInfo, $result);

                   $vid_location = $result[0];

                   break;
              case "break":
                   $newInfo = trim(@file_get_contents($url));
                   preg_match('/sGlobalFileName=\'[^\']+/', $newInfo, $resulta);
                   $resulta = str_replace('sGlobalFileName=\'', '', $resulta[0]);
                   preg_match('/sGlobalContentFilePath=\'[^\']+/', $newInfo, $resultb);
                   $resultb = str_replace('sGlobalContentFilePath=\'', '', $resultb[0]);

                   $vid_location = 'http://media1.break.com/dnet/media/'.$resultb.'/'.$resulta.'.flv';

                   break;
              case "metacafe":
                   $newInfo = trim(@file_get_contents($url));
                   preg_match('/mediaURL=http%3A%2F%2F(.*?)%2FItemFiles%2F%255BFrom%2520www.metacafe.com%255D%25(.*?)\.flv&gdaKey=[^&]+/', $newInfo, $result);
                   preg_match('/http%3A%2F%2F(.*?)%2FItemFiles%2F%255BFrom%2520www.metacafe.com%255D%25(.*?)\.flv&gdaKey=[^&]+/', $result[0], $result);

                   $vid_location = urldecode(str_replace('&gdaKey', '?__gda__', $result[0]));

                   break;
              case "google":
                   $newInfo = trim(@file_get_contents($url));
                   preg_match('/http:\/\/(.*?)googlevideo.com\/videoplayback%3F[^\\\\]+/', $newInfo, $result);
                   
                   $vid_location = urldecode($result[0]);

                   break;
          }

          return $vid_location;

      } // END catchURL() FUNCTION






      // THIS FUNCTION CREATES EMBED
      // INPUT:
      // OUTPUT: VIDEO EMBED TAG
      function catchEmbed(){

          switch ($this->type) {
              case "youtube":
                   $vid_embed_id = substr($this->url, strpos($this->url, "=")+1);
                   $vid_embed = "<object width=\"640\" height=\"360\">
                                 <param name=\"wmode\" value=\"transparent\"></param>
                                 <param name=\"movie\" value=\"http://www.youtube.com/v/fGLm2gcCl74&hl=en&fs=1\"></param>
                                 <param name=\"allowFullScreen\" value=\"true\"></param>
                                 <param name=\"allowscriptaccess\" value=\"always\"></param>
                                 <embed src=\"http://www.youtube.com/v/".$vid_embed_id."&fs=1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"640\" height=\"360\" wmode=\"transparent\"></embed></object>";
                   break;
              case "bliptv":
                   $newInfo = trim(@file_get_contents($this->url));
                   preg_match('/link rel=\"video_src\" href=\"[^\"]+/', $newInfo, $result);
                   preg_match('/http:\/\/[^\"]+/', $result[0], $final_result);
                   $vid_embed = "<object width=\"640\" height=\"360\">
                                 <param name=\"wmode\" value=\"transparent\"></param>
                                 <param name=\"movie\" value=\"".$final_result[0]."\"></param>
                                 <param name=\"allowFullScreen\" value=\"true\"></param>
                                 <param name=\"allowscriptaccess\" value=\"always\"></param>
                                 <embed src=\"".$final_result[0]."\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"640\" height=\"360\" wmode=\"transparent\"></embed></object>";
                   break;
              case "break":
                   $newInfo = trim(@file_get_contents($this->url));
                   preg_match('/meta name=\"embed_video_url\" content=\"[^\"]+/', $newInfo, $result);
                   preg_match('/http:\/\/[^\"]+/', $result[0], $final_result);
                   $vid_embed = "<object width=\"640\" height=\"360\">
                                 <param name=\"wmode\" value=\"transparent\"></param>
                                 <param name=\"movie\" value=\"".$final_result[0]."\"></param>
                                 <param name=\"allowScriptAccess\" value=\"always\"></param>
                                 <param name=\"allowFullScreen\" value=\"true\"></param>
                                 <embed src=\"".$final_result[0]."\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"always\" width=\"640\" height=\"360\" wmode=\"transparent\"></embed></object>";
                   break;
              case "metacafe":
                   $newInfo = trim(@file_get_contents($this->url));
                   preg_match('/embed quality=\"high\" name=\"fpObj\" src=\"[^\"]+/', $newInfo, $result);
                   preg_match('/http:\/\/[^\"]+/', $result[0], $final_result);
                   preg_match('/flashvars=\"[^\"]+/', $newInfo, $result2);
                   $final_result2 = str_replace('flashvars="', '', $result2[0]);

                   $vid_embed = "<object width=\"640\" height=\"360\">
                                 <param name=\"wmode\" value=\"transparent\"></param>
                                 <param name=\"movie\" value=\"".$final_result[0]."\"></param>
                                 <param name=\"flashvars\" value=\"".$final_result2."\"></param>
                                 <param name=\"allowFullScreen\" value=\"true\"></param>
                                 <param name=\"allowscriptaccess\" value=\"always\"></param>
                                 <embed id=\"VideoPlayback\" src=\"".$final_result[0]."\" style=\"width:640px;height:360px\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" flashvars=\"".$final_result2."\" wmode=\"transparent\"></embed></object>";
                   break;
              case "google":
                   $vid_embed_id = substr($this->url, strpos($this->url, "=")+1);
                   $vid_embed = "<object width=\"640\" height=\"360\">
                                 <param name=\"wmode\" value=\"transparent\"></param>
                                 <param name=\"movie\" value=\"http://video.google.com/googleplayer.swf?docid=".$vid_embed_id."\"></param>
                                 <param name=\"allowFullScreen\" value=\"true\"></param>
                                 <param name=\"allowscriptaccess\" value=\"always\"></param>
                                 <embed id=\"VideoPlayback\" src=\"http://video.google.com/googleplayer.swf?docid=".$vid_embed_id."\" style=\"width:640px;height:360px\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" wmode=\"transparent\"></embed></object>";
                   break;
          }

          return $vid_embed;

      } // END catchEmbed() FUNCTION






      // THIS FUNCTION CATCHES FLV URL BY USING KEEPVID
      // INPUT: $url REPRESENTING THE VIDEO PAGE URL
      // OUTPUT: ARRAY CONTAINING $location AND $type
      function catchURLkeepvid($url){

          $url = 'http://keepvid.com/?url='.$url;
          $keepvid = trim(@file_get_contents($url));
          preg_match('/a href="[^\"]+" class="link" target="_blank"/', $keepvid, $keepvid_result);
          preg_match('/http[^\"]+/', $keepvid_result[0], $keepvid_final);

          return $keepvid_final[0];

      } // END catchURL() FUNCTION
}
?>