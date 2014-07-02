{include file='header.tpl'}

{literal}
<script type="text/javascript">
<!-- 
var vid_id = 0;

function confirmRemove(id) {
  vid_id = id;
  TB_show('{/literal}{lang_print id=13500196}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmremove', '', '../images/trans.gif');
}

function removeFavorite() {
  window.location = 'user_vid_favs.php?task=remove_fav&id='+vid_id;
}
//-->
</script>
{/literal}

{include file='vid_buttons.tpl'}

<div class='page_header'>{lang_print id=13500194}</div>
<div style='clear: both; height: 20px;'></div>
<div>

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

<div>
{section name=test loop=$all_videos}
     <div class='vid_user'>
	 <table cellpadding='0' cellspacing='0'>
          <tr>
	        <td style='vertical-align: top;'>
	                   <div class='vid_photo' style='width: 140px; height: 140px;'>
        <table cellpadding='0' cellspacing='0' width='140' height='140'>
        <tr><td><a href='{$url->url_create('vid_file', $all_videos[test].username, $all_videos[test].id)}'><img src='{$all_videos[test].img}{if $all_videos[test].type == "self"}_thumb_1.jpg{else}.jpg{/if}' border='0' width='130' height='97'></a></td></tr>
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
                <div style='float: left; padding-right: 13px;'><a href='{$url->url_create('vid_file', $all_videos[test].username, $all_videos[test].id)}'><img src='./images/icons/vid_play16.gif' border='0' class='button'>{lang_print id=13500081}</a></div><div style='float: left; padding-right: 13px;'><a href='javascript:void(0);' onClick='confirmRemove("{$all_videos[test].id}");'><img src='./images/icons/vid_delete16.gif' border='0' class='button'>{lang_print id=13500195}</a></div>
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

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmremove'>
  <div style='margin-top: 10px;'>
    {lang_print id=13500197}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=13500182}' onClick='parent.TB_remove();parent.removeFavorite();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>

</div>


{include file='footer.tpl'}