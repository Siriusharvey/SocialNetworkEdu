{include file='header.tpl'}

<div class='content'>

{capture assign="owner_url"}{$url->url_create('profile', $owner->user_info.user_username)}{/capture}
<div class='page_header'>{lang_sprintf id=13500055 1=$owner_url 2=$owner->user_displayname}</div>

{if $page_vars[7] > 0}
  <div style='text-align: center; padding-bottom: 10px;'>
  {math assign="page_minus" equation='p-1' p=$page_vars[0]}
  {math assign="page_plus" equation='p+1' p=$page_vars[0]}
  {if $page_vars[0] > 1}<a href='{$url->url_create('vids', $owner->user_info.user_username, $page_minus)}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $page_vars[5] == 1}
    <b>{lang_sprintf id=184 1=$page_vars[4] 2=$page_vars[1]}</b>
  {else}
    <b>{lang_sprintf id=185 1=$page_vars[4] 2=$page_vars[6] 3=$page_vars[1]}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $page_vars[0] < $page_vars[7]}<a href='{$url->url_create('vids', $owner->user_info.user_username, $page_plus)}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}

<div>

{section name=test loop=$all_videos}
     <div class='vid_tab' style='width: 275px;'>
	        <table cellpadding='0' cellspacing='0'>
          <tr>
	        <td style='vertical-align: top;'>
	             <a href='{$url->url_create('vid_file', $owner->user_info.user_username, $all_videos[test].id)}'><img src='{$all_videos[test].img}{if $all_videos[test].type == "self"}_thumb_0.jpg{else}.jpg{/if}' border='0' width='{$vid_settings.thumb_width}' height='{$vid_settings.thumb_height}'></a>
	        </td>
	        <td style='vertical-align: top; padding-left: 5px;'>
               <div class='vid_row_title'><a href='{$url->url_create('vid_file', $owner->user_info.user_username, $all_videos[test].id)}'>{$all_videos[test].title|truncate:32:"...":true}</a></div>
               <div class='vid_row_info'><a href='browse_vids.php?c={$all_videos[test].cat_id}' style='margin-right: 5px;'>{lang_print id=$all_videos[test].cat_lang}</a></div>
               <div class='vid_row_info'>{$all_videos[test].views} view(s)</div>
               <div>
               {section name=full_stars start=0 loop=$all_videos[test].full}
                    <img src='./images/icons/star2.gif' border='0'>
               {/section}
               {section name=partial_stars start=0 loop=$all_videos[test].partial}
                    <img src='./images/icons/star2-half.gif' border='0'>
               {/section}
               {section name=empty_stars start=0 loop=$all_videos[test].empty}
                    <img src='./images/icons/star1.gif' border='0'>
               {/section}
  	    	     </div>
	         </td>
	         </tr>
	         </table>
     </div>
     {cycle values=',,<div style=\'clear: both; height: 0px;\'></div>'}
{/section}

</div>

</div>


{include file='footer.tpl'}