{include file='header.tpl'}


<div class='page_header'>{$badge->badge_info.badge_title}</div>

<div>{$badge->badge_info.badge_desc|strip_tags|truncate:130:"...":true}
    {if $badge->badge_info.badge_desc|strip_tags|count_characters:true > 130}
      <a href='javascript:void(0);' onClick="TB_show('{$badge->badge_info.badge_title}', '#TB_inline?height=200&width=400&inlineId=badge_full_desc', '', '../images/trans.gif');">{lang_print id=11230101}</a>
    {/if}
</div>


{* HIDDEN DIV FOR BADGE DESCRIPTION *}
<div id="badge_full_desc" style="display: none">
{$badge->badge_info.badge_desc}
</div>

{*
<div>
{if $type == 'level'}{lang_sprintf id=11270092 1=$type_title}
{elseif $type == 'subnet'}{lang_sprintf id=11270093 1=$type_title}
{elseif $type == 'profilecat'}{lang_sprintf id=11270094 1=$type_title}
{/if}
</div>
*}

<br><br>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>

<td valign='top'>


{* SHOW MESSAGE IF NO RESULTS FOUND *}
{if $total_users == 0}
  <br>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr><td class='result'><img src='./images/icons/bulb22.gif' border='0' class='icon'> {lang_print id=1085}</td></tr>
  </table>


{* SHOW RESULTS *}
{elseif $total_users != 0}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='browse_pages'>
      {if $p != 1}<a href='badgeusers.php?s={$s}&type={$type}&type_id={$type_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_users} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_users} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='badgeusers.php?s={$s}&type={$type}&type_id={$type_id}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

  {* DISPLAY BROWSE RESULTS IN THUMBNAIL FORM *}
  {section name=user_loop loop=$users}
    <div class='browse_result' style='float: left; padding: 5px; width: 100px; height: 100px; text-align: center;'>
      <a href='{$url->url_create('profile',$users[user_loop]->user_info.user_username)}'><img src='{$users[user_loop]->user_photo('./images/nophoto.gif', TRUE)}' class='photo' style='display: block; margin-left: auto; margin-right: auto;' width='60' height='60' border='0' alt="{lang_sprintf id=509 1=$users[user_loop]->user_displayname_short}">{$users[user_loop]->user_displayname|truncate:20:"...":true}</a>
    </div>
    {cycle name="newrow" values=",,,,,<div style='clear: both; margin-top: 10px;'>&nbsp;</div>"}
  {/section}
  <div style='clear: both;'></div>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='browse_pages'>
      {if $p != 1}<a href='badgeusers.php?s={$s}&type={$type}&type_id={$type_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_users} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_users} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='badgeusers.php?s={$s}&type={$type}&type_id={$type_id}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

{/if}

</td>

<td style='width: 200px; vertical-align: top; text-align: left; padding-left: 10px;'>
  
  <div class="badge_browse_photo">
    <img src="{$badge->badge_photo()}" />
    <br><strong>{$type_title}</strong>
  </div>
  
  <div class="badge_browse_options">
    <table cellpadding='0' cellspacing='0'>
      <tr>  
      <td align="right" width="45">
        {lang_print id=11270174}&nbsp;
      </td>
      <td align="left">
        <select class='small' name='s' onchange="window.location.href='badgeusers.php?type={$type}&type_id={$type_id}&s='+this.options[this.selectedIndex].value;">
          <option value='user_dateupdated DESC'{if $s == "user_dateupdated DESC"} SELECTED{/if}>{lang_print id=1092} {lang_print id=1093}</option>
          <option value='user_dateupdated ASC'{if $s == "user_dateupdated ASC"} SELECTED{/if}>{lang_print id=1092} {lang_print id=1094}</option>
          <option value='user_lastlogindate DESC'{if $s == "user_lastlogindate DESC"} SELECTED{/if}>{lang_print id=1095} {lang_print id=1093}</option>
          <option value='user_lastlogindate ASC'{if $s == "user_lastlogindate ASC"} SELECTED{/if}>{lang_print id=1095} {lang_print id=1094}</option>
          <option value='user_signupdate DESC'{if $s == "user_signupdate DESC"} SELECTED{/if}>{lang_print id=1096} {lang_print id=1093}</option>
          <option value='user_signupdate ASC'{if $s == "user_signupdate ASC"} SELECTED{/if}>{lang_print id=1096} {lang_print id=1094}</option>
        </select>
      </td>
      </tr>
    </table>
  </div>
  
{if $type_badges|@count > 1}
  <div class="header">
{if $type == 'level'}{lang_print id=11270095}
{elseif $type == 'subnet'}{lang_print id=11270096}
{elseif $type == 'profilecat'}{lang_print id=11270097}
{/if}
  </div>
  <div class="portal_content">
	  <table cellspacing="0" cellpadding="0">
	  {foreach from=$type_badges key=type_badge_id item=type_badge}
	    {if $type_badge_id != $type_id}
	      <tr>
	        <td widht="60" style="padding: 5px 10px 5px 0;"><a href="badgeusers.php?s={$s}&type={$type}&type_id={$type_badge_id}"><img border="0" src="{$type_badge.badge->badge_photo('./images/badge_placeholder.gif', true)}" height="60" width="60" /></a></td>
	        <td><a href="badgeusers.php?s={$s}&type={$type}&type_id={$type_badge_id}"><strong>{$type_badge.badge->badge_info.badge_title}</strong></a>
	        <br>{lang_sprintf id=11270091 1=$type_badge.total_users}
	        </td>
	      </tr>
	    {/if}
	  {/foreach}
	  </table>
  </div>
{/if}  
  
</td>

</tr>
</table>

{include file='footer.tpl'}