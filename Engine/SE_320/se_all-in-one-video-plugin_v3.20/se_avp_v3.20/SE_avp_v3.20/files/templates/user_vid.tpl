{include file='header.tpl'}

{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var vid_id = 0;

function confirmDelete(id) {
  vid_id = id;
  TB_show('{/literal}{lang_print id=13500002}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

function deleteVideo() {
  window.location = 'user_vid.php?task=delete_vid&id='+vid_id;
}

function editVideo(id, title, desc, tags, privacy, comments, search) {
  var desc = desc.replace(/<br>/g,"\n")
  $('vid_title').defaultValue = title;  
  $('vid_title').value = title;  
  $('vid_desc').defaultValue = desc;  
  $('vid_desc').value = desc;
  $('vid_tags').defaultValue = tags;  
  $('vid_tags').value = tags;
  $('vid_id').defaultValue = id;  
  $('vid_id').value = id;
  if( $('vid_search_'+search) )
  {
    $('vid_search_'+search).checked = true;
    $('vid_search_'+search).defaultChecked = true;
  }
  if( $('privacy_'+privacy) )
  {
    $('privacy_'+privacy).checked = true;
    $('privacy_'+privacy).defaultChecked = true;
  }
  if( $('comments_'+comments) )
  {
    $('comments_'+comments).checked = true;
    $('comments_'+comments).defaultChecked = true;
  }
  TB_show('{/literal}{lang_print id=13500027}{literal}', '#TB_inline?height=390&width=450&inlineId=editvideo', '', '../images/trans.gif');
}

function okVideo(id) {
		var vid_ok_req = new Request({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'ok', 'id' : id },
                        onComplete: function() { $(id).dispose(); }
		}).send();
}
//-->
</script>
{/literal}

{include file='vid_buttons.tpl'}

<div class='page_header'>{lang_print id=13500077}</div>
<div>
{if $user->level_info.level_vid_allow == 3}
  {math assign="can" equation='c-t' c=$user->level_info.level_vid_maxnum t=$count_vids}
  {math assign="can_yt" equation='c-t' c=$user->level_info.level_vid_prov_maxnum t=$count_yt}
  {lang_sprintf id=13500108 1=$count_vids 2=$count_yt 3=$can 4=$can_yt 5=$allowed_providers}
{elseif $user->level_info.level_vid_allow == 2}
  {math assign="can" equation='c-t' c=$user->level_info.level_vid_maxnum t=$count_yt}
  {lang_sprintf id=13500119 1=$count_yt 2=$can 3=$allowed_providers}
{elseif $user->level_info.level_vid_allow == 1}
  {math assign="can" equation='c-t' c=$user->level_info.level_vid_maxnum t=$count_vids}
  {lang_sprintf id=13500078 1=$count_vids 2=$can}
{/if}
</div>

{if $user->level_info.level_vid_allow != 4}
  <div style='margin-top: 20px;'>
  {if ($user->level_info.level_vid_allow == 3 OR $user->level_info.level_vid_allow == 1) AND $count_vids < $user->level_info.level_vid_maxnum}
      <div class='button' style='float: left; margin-right:20px;'>
          <a href='user_vid_add.php'><img src='./images/icons/plus16.gif' border='0' class='button'>{lang_print id=13500079}</a>
      </div>
  {/if}
  {if ($user->level_info.level_vid_allow == 3 OR $user->level_info.level_vid_allow == 2) AND $count_yt < $user->level_info.level_vid_prov_maxnum}
     <div class='button' style='float:left;'>
      <a {if empty($provider2) or !$provider2}style='color: #D8E4F1'{/if} href='user_vid_add.php?task=youtube'><img src='./images/icons/plus16.gif' border='0' class='button'>{lang_print id=13500091}</a> {if empty($provider2) or !$provider2}{lang_print id=13500173}{/if}
     </div>
  {/if}
     <div style='clear: both; height: 0px;'></div>
  </div>
{/if}

<br>

{* IF THERE ARE NO VIDEOS, SHOW NOTE *}
{if $count_videos == 0}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=13500013} <a href='user_vid_add.php'>{lang_print id=13500014}</a></div>
  </td></tr>
  </table>
{/if}

{if $msg}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
      <div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_print id=$msg}</div>
  </td></tr></table>
  <br>
{/if}

{if $encode}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
      <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=$encode}</div>
  </td></tr></table>
  <br>
{/if}

{if $page_vars[7] > 0}
  <div style='text-align: center; padding-bottom: 10px;'>
  {if $page_vars[0] > 1}<a href='user_vid.php?p={math equation='p-1' p=$page_vars[0]}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $page_vars[5] == 1}
    <b>{lang_sprintf id=184 1=$page_vars[4] 2=$page_vars[1]}</b>
  {else}
    <b>{lang_sprintf id=185 1=$page_vars[4] 2=$page_vars[6] 3=$page_vars[1]}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $page_vars[0] < $page_vars[7]}<a href='user_vid.php?p={math equation='p+1' p=$page_vars[0]}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}

{if $failed_vids}
{section name=failed loop=$failed_vids}
<div id='{$failed_vids[failed].location}'>
<div class="vid_failed">
<div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_sprintf id=13500175 1=$failed_vids[failed].title} <a onClick="okVideo('{$failed_vids[failed].location}')" href="javascript:void(0);">{lang_print id=13500176}</a></div>
</div>
</div>
{/section}
{/if}

<div>
{section name=test loop=$all_videos}
     <div class='vid_user'>
	 <table cellpadding='0' cellspacing='0'>
          <tr>
	        <td style='vertical-align: top;'>
	                   <div class='vid_photo' style='width: 140px; height: 140px;'>
        <table cellpadding='0' cellspacing='0' width='140' height='140'>
        <tr><td><a href='{$url->url_create('vid_file', $user->user_info.user_username, $all_videos[test].id)}'><img src='{$all_videos[test].img}{if $all_videos[test].type == "self"}_thumb_1.jpg{else}.jpg{/if}' border='0' width='130' height='97'></a></td></tr>
        </table>
      </div>
	        </td>
	        <td style='vertical-align: top; padding-left: 5px;'>
            <div class='vid_title'>
              <a>{$all_videos[test].title|truncate:37:"...":true}</a>
            </div>
            <div style='margin-bottom: 8px;'>{$all_videos[test].desc_user|truncate:50:"...":true}</div>
            <div class='vid_stats'>
              {lang_print id=13500071} {$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $datetime->timezone($all_videos[test].date, $global_timezone))}<br>
              {if $all_videos[test].cat_lang != ''}{lang_print id=13500074} {lang_print id=$all_videos[test].cat_lang}{/if}
                <table cellspacing='0' cellpadding='0'>
                <tr>
                     <td style='padding-right: 2px;'><font style='font-size: 7pt; color: #777777;'>{lang_print id=13500080} </font></td>
                     <td>
                     {section name=full_stars start=0 loop=$all_videos[test].full}
                          <img src='./images/icons/star2.gif' border='0'>
                     {/section}
                     {section name=partial_stars start=0 loop=$all_videos[test].partial}
                          <img src='./images/icons/star2-half.gif' border='0'>
                     {/section}
                     {section name=empty_stars start=0 loop=$all_videos[test].empty}
                          <img src='./images/icons/star1.gif' border='0'>
                     {/section}
                     </td>
                </tr>
                </table>
              {lang_sprintf id=13500028 1=$all_videos[test].views}
              <div class='vid_options'>
                <div style='float: left; padding-right: 13px;'><a href='{$url->url_create('vid_file', $user->user_info.user_username, $all_videos[test].id)}'><img src='./images/icons/vid_play16.gif' border='0' class='button'>{lang_print id=13500081}</a></div>
                <div style='float: left; padding-right: 13px;'><a href='javascript:void(0);' onClick='confirmDelete("{$all_videos[test].id}");'><img src='./images/icons/vid_delete16.gif' border='0' class='button'>{lang_print id=13500082}</a></div>
                <div style='float: left;'><a href='javascript:void(0);' onClick='editVideo("{$all_videos[test].id}", "{$all_videos[test].title|replace:"&":"\&"}", "{$all_videos[test].desc|replace:"&":"\&"}", "{$all_videos[test].tags|replace:"&":"\&"}", "{$all_videos[test].privacy}", "{$all_videos[test].comments}", "{$all_videos[test].search}");'><img src='./images/icons/vid_edit16.gif' border='0' class='button'>{lang_print id=13500083}</a></div>
              </div>
            </div>
	         </td>
	         </tr>
	         </table>
     </div>
     {cycle values=',<div style=\'clear: both; height: 0px;\'></div>'}
{/section}
</div>

{if $page_vars[7] > 0}
  <div style='clear: both; text-align: center; padding-top: 10px;'>
  {if $page_vars[0] > 1}<a href='user_vid.php?p={math equation='p-1' p=$page_vars[0]}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $page_vars[5] == 1}
    <b>{lang_sprintf id=184 1=$page_vars[4] 2=$page_vars[1]}</b>
  {else}
    <b>{lang_sprintf id=185 1=$page_vars[4] 2=$page_vars[6] 3=$page_vars[1]}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $page_vars[0] < $page_vars[7]}<a href='user_vid.php?p={math equation='p+1' p=$page_vars[0]}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}


{* HIDDEN DIV TO DISPLAY EDIT VIDEO *}
<div style='display: none;' id='editvideo'>
  <form action='user_vid.php' name='editForm' method='post' target='_parent'>
  <div style='margin-top: 10px;'>{lang_print id=13500023}</div>
  <br>

  <b>{lang_print id=13500024}</b><br>
  <input name='vid_title' id='vid_title' type='text' maxlength='50' class='text' size='30' value=''>

  <br><br>

  <b>{lang_print id=13500025}</b><br>
  <textarea name='vid_desc' id='vid_desc' rows='6' cols='50'></textarea>

  <br><br>

  <b>{lang_print id=13500026}</b><br>
  <textarea name='vid_tags' id='vid_tags' rows='6' cols='50'></textarea>

  <br><br>

  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_vid_search == 1}
    <b>{lang_print id=13500146}</b><br>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td><input type='radio' name='vid_search' id='vid_search_1' value='1'></td>
        <td><label for='vid_search_1'>{lang_print id=13500147}</label></td>
      </tr>
      <tr>
        <td><input type='radio' name='vid_search' id='vid_search_0' value='0'></td>
        <td><label for='vid_search_0'>{lang_print id=13500148}</label></td>
      </tr>
    </table>
    <br />
  {/if}

  {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $privacy_options|@count > 1}
    <b>{lang_print id=13500149}</b><br>
    <table cellpadding='0' cellspacing='0'>
      {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
        <td><input type='radio' name='vid_privacy' id='privacy_{$k}' value='{$k}'></td>
        <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
      {/foreach}
    </table>
    <br />
  {/if}

  {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
  {if $comment_options|@count > 1}
    <b>{lang_print id=13500150}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='vid_comments' id='comments_{$k}' value='{$k}'></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <br />
  {/if}

  <input type='submit' class='button' value='{lang_print id=173}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='update_vid'>
  <input type='hidden' name='vid_id' id='vid_id' value='0'>
  </form>
  <br><br>
</div>

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=13500003}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteVideo();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>

{include file='footer.tpl'}