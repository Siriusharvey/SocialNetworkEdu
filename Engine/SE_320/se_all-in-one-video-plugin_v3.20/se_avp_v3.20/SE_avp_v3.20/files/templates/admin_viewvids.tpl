{include file='admin_header.tpl'}

<h2>{lang_print id=13500103}</h2>
{lang_print id=13500104}

<br><br>

  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
var vid_id = 0;
var vid_user_id = 0;

function confirmDelete(id, id2) {
  vid_id = id;
  vid_user_id = id2;
  TB_show('{/literal}{lang_print id=13500002}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

function deleteVideo() {
  window.location = 'admin_viewvids.php?task=delete_vid&id='+vid_id+'&user_id='+vid_user_id;
}

  function handler() {
     $('c').addEvent('change', function(event){  
         window.location = 'admin_viewvids.php?q={/literal}{$q}{literal}&c='+$('c').value+'&s={/literal}{$s}{literal}&b={/literal}{$b}{literal}';
     });
     $('s').addEvent('change', function(event){  
         window.location = 'admin_viewvids.php?q={/literal}{$q}{literal}&c={/literal}{$c}{literal}&s='+$('s').value+'&b={/literal}{$b}{literal}';
     });
     $('b').addEvent('change', function(event){  
         window.location = 'admin_viewvids.php?q={/literal}{$q}{literal}&c={/literal}{$c}{literal}&s={/literal}{$s}{literal}&b='+$('b').value;
     });
  }
 
  window.addEvent('domready', handler);
  // -->
  </script>
  {/literal}

{if $pages > 0}
  <div style='text-align: center; padding-bottom: 10px;'>
  {if $p > 1}<a href='admin_viewvids.php?c={$c}&s={$s}&b={$b}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $p_end == 1}
    <b>{lang_sprintf id=184 1=$p_start_lang 2=$total_videos}</b>
  {else}
    <b>{lang_sprintf id=185 1=$p_start_lang 2=$p_end_lang 3=$total_videos}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $p < $pages}<a href='admin_viewvids.php?c={$c}&s={$s}&b={$b}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}

<form method="get" name="seBrowsevids">
<div style='padding: 7px 10px 7px 10px; background: #F2F2F2; border-top: 1px solid #BBBBBB; border-left: 1px solid #BBBBBB; border-right: 1px solid #BBBBBB; border-bottom: 0px; margin: 0; font-weight: bold;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td style='padding-right: 3px;'>
    Search:&nbsp;</td>
  <td style='padding-right: 3px;'>
       <input type="text" name="q" value="" class="text" style="width:120px;">
  </td>
  <td>
       <input type='submit' class='button' value='Search'>
  </td>
  <td style='padding-left: 20px;'>
    Category:&nbsp;
  </td>
  <td>
    <select id='c' class='small' name='c'>
          {section name=vidcats_loop loop=$vidcats}
    <option value='{$vidcats[vidcats_loop].vidcat_id}'{if $c == $vidcats[vidcats_loop].vidcat_id} SELECTED{/if}>{lang_print id=$vidcats[vidcats_loop].vidcat_languagevar_id}</option>
          {/section}
    </select>
  </td>
  <td style='padding-left: 20px;'>
    Show:&nbsp;
  </td>
  <td>
    <select id='s' class='small' name='s'>
    <option value='v'{if $s == "v"} SELECTED{/if}>Most Viewed</option>
    <option value='p'{if $s == "p"} SELECTED{/if}>Top Rated</option>
    <option value='c'{if $s == "c"} SELECTED{/if}>Recently Added</option>
    </select>
  </td>
  <td style='padding-left: 10px; padding-right: 10px;'>
    in
  </td>
  <td>
    <select id='b' class='small' name='b'>
    <option value='0'{if $b == 0} SELECTED{/if}>DESC</option>
    <option value='1'{if $b == 1} SELECTED{/if}>ASC</option>
    </select>
  </td>
  <td style='padding-left: 5px'>
    &nbsp;order.
  </td>
  </tr>
  </table>
</div>
</form>
  <form action='admin_viewvideos.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10' style='padding-left: 6px;'>{lang_print id=87}</td>
  <td class='header'>{lang_print id=13500093}</td>
  <td class='header'>{lang_print id=13500094}</td>
  <td class='header' width='100'>{lang_print id=153}</td>
  </tr>
  {section name=video_loop loop=$all_videos}
    {assign var='video_url' value=$url->url_create('vid_file', $all_videos[video_loop].username, $all_videos[video_loop].id)}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-left: 6px;'>{$all_videos[video_loop].id}</td>
    <td class='item'>
	        <table cellpadding='0' cellspacing='0'>
                <tr>
	        <td style='vertical-align: top;'>
	             <a href='{$url->url_create('vid_file', $all_videos[video_loop].username, $all_videos[video_loop].id)}'><img src='{$all_videos[video_loop].img}' border='0' width='80' height='70'></a>
	        </td>
	        <td style='vertical-align: top; padding-left: 5px;'>
               <div class='vid_row_title'><a href='{$url->url_create('vid_file', $all_videos[video_loop].username, $all_videos[video_loop].id)}'>{$all_videos[video_loop].title}</a></div>
               <div class='vid_row_info'><a href='../browse_vids.php?c={$all_videos[video_loop].cat_id}' style='margin-right: 5px;'>{lang_print id=$all_videos[video_loop].cat_lang}</a></div>
               <div class='vid_row_info'>{$all_videos[video_loop].views} view(s)</div>
               <div>
               {section name=full_stars start=0 loop=$all_videos[video_loop].full}
                    <img src='../images/icons/star2.gif' border='0'>
               {/section}
               {section name=partial_stars start=0 loop=$all_videos[video_loop].partial}
                    <img src='../images/icons/star2-half.gif' border='0'>
               {/section}
               {section name=empty_stars start=0 loop=$all_videos[video_loop].empty}
                    <img src='../images/icons/star1.gif' border='0'>
               {/section}
  	    	     </div>
	         </td>
	         </tr>
	         </table>
    </td>
    <td class='item'><a href='{$url->url_create('profile', $all_videos[video_loop].username)}' target='_blank'>{$all_videos[video_loop].username}</a></td>
    <td class='item'>[ <a href='admin_loginasuser.php?user_id={$all_videos[video_loop].userid}&return_url={$url->url_encode($video_url)}' target='_blank'>view</a> ] [ <a href="javascript:void(0);" onClick="confirmDelete('{$all_videos[video_loop].id}', '{$all_videos[video_loop].userid}');">{lang_print id=155}</a> ]</td>
    </tr>
  {/section}
  </table>

{if $pages > 0}
  <div style='text-align: center; padding-top: 10px;'>
  {if $p > 1}<a href='admin_viewvids.php?c={$c}&s={$s}&b={$b}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $p_end == 1}
    <b>{lang_sprintf id=184 1=$p_start_lang 2=$total_videos}</b>
  {else}
    <b>{lang_sprintf id=185 1=$p_start_lang 2=$p_end_lang 3=$total_videos}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $p < $pages}<a href='admin_viewvids.php?c={$c}&s={$s}&b={$b}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=13500003}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteVideo();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>

{include file='admin_footer.tpl'}