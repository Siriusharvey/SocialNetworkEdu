{include file='header.tpl'}

<script type="text/javascript" src="./include/js/album_ajax_mod.js"></script>

{* $Id: album_file.tpl 2 2009-01-10 20:53:09Z john $ *}

{* SET PAGE WIDTH *}
{assign var='page_width' value=$owner->level_info.level_album_width}
{assign var='menu_width' value=$page_width+32}
<script type="text/javascript">
var media_ids = [{section name=media_loop loop=$media_ids_array}{$media_ids_array[media_loop]}{if not $smarty.section.media_loop.last}, {/if}{/section}];
var cur_media_id = {$media_info.media_id};
var media_dir = '{$media_dir}';
var username = '{$owner->user_info.user_username}';
var album_id = {$media_info.media_album_id};

</script>



<div style='width: {$menu_width}px; margin-left: auto; margin-right: auto;'>

<div class='page_header'>
  {lang_sprintf id=1000141 1=$url->url_create('profile', $owner->user_info.user_username) 2=$owner->user_displayname 3=$url->url_create('albums', $owner->user_info.user_username)}
  &#187; <a href='{$url->url_create('album', $owner->user_info.user_username, $album_info.album_id)}'>{$album_info.album_title}</a>
</div>


{* SET MEDIA PATH *}
{assign var='media_dir' value=$url->url_userdir($owner->user_info.user_id)}
{assign var='media_path' value="`$media_dir``$media_info.media_id`.`$media_info.media_ext`"}


{* DISPLAY IMAGE *}
{if $media_info.media_ext == "jpg" || 
    $media_info.media_ext == "jpeg" || 
    $media_info.media_ext == "gif" || 
    $media_info.media_ext == "png" || 
    $media_info.media_ext == "bmp"}
  {assign var='file_src' value="<div id='photo_div_`$media_info.media_id`'  style='position:absolute; opacity: 1;'><img src='`$media_path`' id='media_photo' border='0'></div>"}
  {assign var='is_image' value=true}

{* DISPLAY AUDIO *}
{elseif $media_info.media_ext == "mp3" || 
        $media_info.media_ext == "mp4" || 
        $media_info.media_ext == "wav"}
  {capture assign='media_download'}[ <a href='{$media_path}'>{lang_print id=1000142}</a> ]{/capture}
  {assign var='file_src' value="<a href='`$media_path`'><img src='./images/icons/album_audio_big.gif' border='0'></a>"}
  {assign var='is_image' value=false}

{* DISPLAY WINDOWS VIDEO *}
{elseif $media_info.media_ext == "mpeg" || 
	$media_info.media_ext == "mpg" || 
	$media_info.media_ext == "mpa" || 
	$media_info.media_ext == "avi" || 
	$media_info.media_ext == "ram" || 
	$media_info.media_ext == "rm"}
  {capture assign='media_download'}[ <a href='{$media_path}'>{lang_print id=1000143}</a> ]{/capture}
  {assign var='file_src' value="
    <object id='video'
      classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6'
      type='application/x-oleobject'>
      <param name='url' value='`$media_path`'>
      <param name='sendplaystatechangeevents' value='True'>
      <param name='autostart' value='true'>
      <param name='autosize' value='true'>
      <param name='uimode' value='mini'>
      <param name='playcount' value='9999'>
    </OBJECT>
  "}
  {assign var='is_image' value=false}

{* DISPLAY QUICKTIME FILE *}
{elseif $media_info.media_ext == "mov" || 
	$media_info.media_ext == "moov" || 
	$media_info.media_ext == "movie" || 
	$media_info.media_ext == "qtm" || 
	$media_info.media_ext == "qt"}
  {capture assign='media_download'}[ <a href='{$media_path}'>{lang_print id=1000143}</a> ]{/capture}
  {assign var='file_src' value="
    <embed src='`$media_path`' controller='true' autosize='1' scale='1' width='550' height='350'>
  "}
  {assign var='is_image' value=false}

{* EMBED FLASH FILE *}
{elseif $media_info.media_ext == "swf"}
  {assign var='file_src' value="
	<object width='350' height='250' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0' id='myMovieName'>
	  <param name=movie value=$media_path>
	  <param name=quality value=high>
	  <param name=bgcolor value=#FFFFFF>
	  <embed src=$media_path quality=high bgcolor=#FFFFFF width='350' height='250' name='myMovieName' align='' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'>
	  </embed>
	</object>
  "}
  {assign var='is_image' value=false}

{* DISPLAY UNKNOWN FILETYPE *}
{else}
  {capture assign='media_download'}[ <a href='{$media_path}'>{lang_print id=1000144}</a> ]{/capture}
  {assign var='file_src' value="<a href='`$media_path`'><img src='./images/icons/file_big.gif' border='0'></a>"}
  {assign var='is_image' value=false}
{/if}


{* ASSIGN INDICES *}
{assign var="current_index" value=$media_info.media_id|array_search:$media_keys}
{capture assign="previous_index"}{if $current_index == 0}{math equation="x-1" x=$media_count}{else}{math equation="x-1" x=$current_index}{/if}{/capture}
{capture assign="next_index"}{if $current_index+1 == $media_count}0{else}{math equation="x+1" x=$current_index}{/if}{/capture}
{capture assign="current_num"}{math equation="x+1" x=$current_index}{/capture}


<br>

{* SHOW PAGE NAVIGATION *}
<div style='margin-bottom: 6px;'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td id="current_num">
    {lang_sprintf id=1000145 1=$current_num 2=$media_count 3=$url->url_create('album', $owner->user_info.user_username, $album_info.album_id) 4=$album_info.album_title}
  </td>
  <td style='text-align: right;'>

  <a id="last" href="javascript:void(0);" onclick="nextpic({$album_info.album_id},{$media_keys.$previous_index},'{$owner->user_info.user_username}');">{lang_print id=1000146}</a>
    &nbsp;&nbsp;&nbsp;
    <a id="next" href="javascript:void(0);" onclick="nextpic({$album_info.album_id},{$media_keys.$next_index},'{$owner->user_info.user_username}');">{lang_print id=1000147}</a>

  </td>
  </tr>
  </table>
</div>

{* SHOW IMAGE *}
<div class='media'><div  id='loading' style='height:32px; z-index:1000; text-align:center; margin-bottom:-10px; position:absolute; width:{$page_width}px; display:none'><img src='./images/icons/loading2.gif' border='0'></div>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td style='text-align: center;'>

    {* CREATE WRAPPER DIV *}
    <div id='media_photo_div' align='left' class='media_photo_div' style='{if $is_image}width:{$media_info.media_width}px;height:{$media_info.media_height}px;{/if}'>
     
      {* DISPLAY FILE/IMAGE *}
      {$file_src}
    
    </div>

    {* SHOW MEDIA DOWNLOAD LINK FOR NON-IMAGES *}
    {if $media_download != ""}
      <div id="media_download" style='font-weight: bold; margin-left: auto; margin-right: auto;'>{$media_download}</div>  
    {/if}

    {* SHOW DIV WITH TITLE, DESC, TAGS, ETC *}
    <div id="media_caption" class='album_media_caption' style='width: {if $media_info.media_width > 300}{$media_info.media_width}{else}300{/if}px;'>
      <div id='title' class='album_media_title' {if $media_info.media_title == ""}style='display:none;'{/if}>{if $media_info.media_title != ""}{$media_info.media_title}{/if} </div>
      <div id='desc' id="media_desc" {if $media_info.media_desc == ""}style='display:none;'{/if}>{if $media_info.media_desc != ""}{$media_info.media_desc}{/if}</div>
	  
      <div id='media_tags' style='display: none; margin-top: 10px;'>{lang_print id=1000162}</div>
      {if $is_image && $allowed_to_tag}
        <a id="addtag" href='javascript:void(0);' onClick="SocialEngine.MediaTag.addTag();">{lang_print id=1000163}</a>
      {/if}
      <div class='album_media_date'>
      <span id="posted_date">{lang_print id=1000126} {assign var="uploaddate" value=$datetime->time_since($media_info.media_date)}{lang_sprintf id=$uploaddate[0] 1=$uploaddate[1]}</span>
        -
        <a href="javascript:TB_show('{lang_print id=1000164}', '#TB_inline?height=400&width=400&inlineId=sharethis', '', '../images/trans.gif');">{lang_print id=1000164}</a>
        {if $user->user_exists}-{/if}
        <a id="report_content" {if !$user->user_exists}style='display:none;'{/if} href="javascript:TB_show('{lang_print id=1000148}', 'user_report.php?return_url={$url->url_current()}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=1000148}</a>
      </div>
    </div>
  </td>
  </tr>
  </table>
</div>

{* DIV FOR SHARE THIS WINDOW *}
<div style='display: none;' id='sharethis'>
  <div style='margin: 10px 0px 10px 0px;'>{lang_print id=1000165}</div>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1000166}</div>
  <textarea id="direct_link" readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 7pt;'>{$url->url_base}{$media_path|replace:"./":""}</textarea>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1000167}</div>
  <textarea id="embedded_image" readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 7pt;'><a href='{$url->url_base}{$media_path|replace:"./":""}'><img src='{$url->url_base}{$media_path|replace:"./":""}' border='0'></a></textarea>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1000168}</div>
  <textarea id="text_link" readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 7pt;'><a href='{$url->url_base}{$media_path|replace:"./":""}'>{if $media_info.media_title != ""}{$media_info.media_title}{else}{lang_print id=589}{/if}</a></textarea>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1000169}</div>
  <textarea id="ubb_code" readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 7pt;'>[url={$url->url_base}{$media_path|replace:"./":""}][img]{$url->url_base}{$media_path|replace:"./":""}[/img][/url]</textarea>
  <div style='margin-top: 10px;'>
    <input type='button' class='button' value='{lang_print id=1000170}' onClick='parent.TB_remove();'>
  </div>
</div>

{* TAGGING *}
{lang_javascript ids=39,1212,1213,1214,1215,1228}
      
<script type="text/javascript">
        
  SocialEngine.MediaTag = new SocialEngineAPI.Tags({ldelim}
      'canTag' : {if $allowed_to_tag}true{else}false{/if},
      'type' : '',
      'media_id' : {$media_info.media_id},
      'media_dir' : '{$media_dir}'

    {rdelim});
        
    SocialEngine.RegisterModule(SocialEngine.MediaTag);
       
    {section name=tag_loop loop=$tags}
      insertTag('{$tags[tag_loop].mediatag_id}', '{if $tags[tag_loop].tagged_user->user_exists}{$url->url_create("profile", $tags[tag_loop].tagged_user->user_info.user_username)}{/if}', '{if $tags[tag_loop].tag_user->user_exists}{$tags[tag_loop].tagged_user->user_displayname}{else}{$tags[tag_loop].mediatag_text}{/if}', '{$tags[tag_loop].mediatag_x}', '{$tags[tag_loop].mediatag_y}', '{$tags[tag_loop].mediatag_width}', '{$tags[tag_loop].mediatag_height}', '{$tags[tag_loop].tagged_user->user_info.user_username}')
    {/section}

    // Backwards
    function insertTag(tag_id, tag_link, tag_text, tag_x, tag_y, tag_width, tag_height, tagged_user)
    {ldelim}
      SocialEngine.MediaTag.insertTag(tag_id, tag_link, tag_text, tag_x, tag_y, tag_width, tag_height, tagged_user);
    {rdelim}
    
 
  </script>


</div>


           {* SHOW FILES IN THIS ALBUM *}
<table cellpadding='0' cellspacing='0' align='center' style='margin-top:15px; width:632px;'>
<tr>
<td style="background:#F2F2F2; border:1px solid #DDDDDD; padding:5px">


  
  <div style="float:left">{if $media_count>7 }<a href="javascript:void();" onClick="javascript:updateCarousel('prev', {$album_info.album_id}, {$owner->user_info.user_id}); event.returnValue=false; return false"><img src="images/icons/media_moveleft.gif" border='0' onMouseOver="this.src='images/icons/media_moveleft2.gif';" onMouseOut="this.src='images/icons/media_moveleft.gif';"></a>{/if}</div>
  <div id='album_carousel' style="width: 562px; text-align: center; float:left; margin-left:15px">
 
  </div>
  <div style="float:right">{if $media_count>7 }<a href="javascript:void();" onClick="javascript:updateCarousel('next', {$album_info.album_id}, {$owner->user_info.user_id}); event.returnValue=false; return false"><img src="images/icons/media_moveright.gif" border='0' onMouseOver="this.src='./images/icons/media_moveright2.gif';" onMouseOut="this.src='./images/icons/media_moveright.gif';"></a>{/if}</div>
  <span id="carousel_page_num" style="display: none">0</span>
  <span id="carousel_count" style="display: none">{$media_count_pages}</span>

</td>
</tr>
</table>

<div style='width: {$menu_width}px; margin-left: auto; margin-right: auto;'>


{* JAVASCRIPT FOR CAROUSEL *}
{literal}
<script type='text/javascript'>
<!--

  var visiblePhotos = 7;
  var current_id = 0;
  var myFx;

  window.addEvent('domready', function() {
//    myFx = new Fx.Scroll('album_carousel');
    current_id = parseInt({/literal}{math equation="x-2" x=$current_index}{literal});
    updateCarousel('next', {/literal}{$album_info.album_id}{literal}, {/literal}{$owner->user_info.user_id}{literal} )     ;        
    block_val = false;  
   
  });



 
 
  
//-->
</script>
{/literal}

<br>


{* DISPLAY POST COMMENT BOX *}
<div id="post_comment_box" style='margin-left: auto; margin-right: auto;'>

  {* COMMENTS *}
  <div id="media_{$media_info.media_id}_postcomment"></div>
  <div id="media_{$media_info.media_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
      
  {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
      
  <script type="text/javascript">
     
    SocialEngine.MediaComments = new SocialEngineAPI.Comments({ldelim}
      'canComment' : {if $allowed_to_comment}true{else}false{/if},
      'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
      'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},

      'type' : 'media',
      'typeIdentifier' : 'media_id',
      'typeID' : {$media_info.media_id},
          
      'typeTab' : 'media',
      'typeCol' : 'media',
      'typeTabParent' : 'albums',
      'typeColParent' : 'album',
      'typeChild' : true,
          
      'initialTotal' : {$total_comments|default:0}
    {rdelim});
        
    SocialEngine.RegisterModule(SocialEngine.MediaComments);
       
    // Backwards
    function addComment(is_error, comment_body, comment_date)
    {ldelim}
      SocialEngine.MediaComments.addComment(is_error, comment_body, comment_date);
    {rdelim}
        
    function getComments(direction)
    {ldelim}
      SocialEngine.MediaComments.getComments(direction);
    {rdelim}
   
  </script>

</div>




</div>


{include file='footer.tpl'}