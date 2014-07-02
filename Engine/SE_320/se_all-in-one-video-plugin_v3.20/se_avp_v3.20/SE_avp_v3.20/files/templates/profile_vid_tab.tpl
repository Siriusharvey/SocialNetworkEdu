{* BEGIN VIDEOS *}
{if $count_videos > 0}

  <div class='profile_headline'>
    {lang_print id=13500007} ({$count_videos})
  </div>

{* IF MORE THAN 6 ALBUMS, SHOW VIEW MORE LINKS *}
{if $count_videos > 6}&nbsp;[ <a href='{$url->url_create('vids', $owner->user_info.user_username, 1)}'>{lang_print id=13500092}</a> ]{/if}

  {* LOOP THROUGH USER VIDEOS *}
  {section name=test loop=$all_videos}
     <div class='vid_tab' style='width: 300px;'>
	        <table cellpadding='0' cellspacing='0'>
          <tr>
	        <td style='vertical-align: top;'>
	             <a href='{$url->url_create('vid_file', $owner->user_info.user_username, $all_videos[test].id)}'><img src='{$all_videos[test].img}{if $all_videos[test].type == 'self'}_thumb_0.jpg{else}.jpg{/if}' border='0' width='80' height='70'></a>
	        </td>
	        <td style='vertical-align: top; padding-left: 5px;'>
               <div class='vid_row_title'><a href='{$url->url_create('vid_file', $owner->user_info.user_username, $all_videos[test].id)}'>{$all_videos[test].title}</a></div>
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

    {cycle values=",<div style='clear: both; height: 0px;'></div>"}

  {/section}
  <div style='clear: both; height: 0px;'></div>

{/if}