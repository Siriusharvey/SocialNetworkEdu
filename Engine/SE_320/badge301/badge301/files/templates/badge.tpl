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


<br><br>
<table cellpadding='0' cellspacing='0' width='100%'>
<tr>

<td valign='top'>



{* SHOW MESSAGE IF NO RESULTS FOUND *}
{if $total_badgeassignments == 0}
  <br>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr><td class='result'><img src='./images/icons/bulb22.gif' border='0' class='icon'> {lang_print id=1085}</td></tr>
  </table>


{* SHOW RESULTS *}
{elseif $total_badgeassignments != 0}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='browse_pages'>
      {if $p != 1}<a href='badge.php?badge_id={$badge_id}&s={$s}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_badgeassignments} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_badgeassignments} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='badge.php?badge_id={$badge_id}&s={$s}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

  {* DISPLAY BROWSE RESULTS IN THUMBNAIL FORM *}
  {section name=badgeassignment_loop loop=$badgeassignments}
    {assign var=badgeassignment_dateapproved value=$datetime->timezone($badgeassignments[badgeassignment_loop].badgeassignment->badgeassignment_info.badgeassignment_dateapproved, $global_timezone)}
    
    {if $badge->badge_info.badge_link_details}
    	{assign var=userlink value=$url->url_create('badgeassignment', null, $badgeassignments[badgeassignment_loop].badgeassignment->badgeassignment_info.badgeassignment_id)}
    {else}
    	{assign var=userlink value=$url->url_create('profile',$badgeassignments[badgeassignment_loop].user->user_info.user_username)}
    {/if}
    
    <div class='browse_result' style='float: left; padding: 5px; width: 100px; height: 100px; text-align: center;'>
      <a href='{$userlink}'><img src='{$badgeassignments[badgeassignment_loop].user->user_photo('./images/nophoto.gif', TRUE)}' style='display: block; margin-left: auto; margin-right: auto;' width='60' height='60' border='0'  class="photo">{$badgeassignments[badgeassignment_loop].user->user_displayname|truncate:20:"...":true}</a>
      <div class="badge_badgeassignment_date">{lang_sprintf id=11270100 1=$datetime->cdate($setting.setting_dateformat,$badgeassignment_dateapproved)}</div>
    </div>
    {cycle name="newrow" values=",,,,,<div style='clear: both; margin-top: 10px;'>&nbsp;</div>"}
  {/section}
  <div style='clear: both;'></div>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='browse_pages'>
      {if $p != 1}<a href='badge.php?badge_id={$badge_id}&s={$s}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_badgeassignments} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_badgeassignments} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='badge.php?badge_id={$badge_id}&s={$s}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

{/if}

</td>

<td style='width: 200px; vertical-align: top; text-align: left; padding-left: 10px;'>
  
  <div class="badge_browse_photo">
    <img src="{$badge->badge_photo('./images/badge_placeholder.gif')}" alt="{$badge->badge_info.badge_title}" />
    <br><strong><a href="{$url->url_create('badge', null, $badge->badge_info.badge_id)}">{$badge->badge_info.badge_title}</a></strong>
  </div>
  
  <div class="badge_browse_options">
    <table cellpadding='0' cellspacing='0'>
      <tr>  
      <td align="right" width="45">
        {lang_print id=11270174}&nbsp;
      </td>
      <td align="left">
        <select class='small' name='s' onchange="window.location.href='badge.php?badge_id={$badge->badge_info.badge_id}&s='+this.options[this.selectedIndex].value;">
          <option value='badgeassignment_dateapproved DESC'{if $s == "badgeassignment_dateapproved DESC"} SELECTED{/if}>{lang_print id=11270101} {lang_print id=1093}</option>
          <option value='badgeassignment_dateapproved ASC'{if $s == "badgeassignment_dateapproved ASC"} SELECTED{/if}>{lang_print id=11270101} {lang_print id=1094}</option>
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
  
{if $can_add_badge}

  <div class="badge_browse_add">
    <a href='javascript:void(0);' onClick="TB_show('{lang_print id=11270106}', '#TB_inline?height=200&width=400&inlineId=badge_add_form', '', '../images/trans.gif');">{lang_print id=11270106}</a>
  </div>
  

	{* HIDDEN DIV FOR ADD THIS BADGE *}
	<div id="badge_add_form" style="display: none">
	  <form method="POST" action="user_badge.php">
	  
	  <div style="margin-top: 10px">{lang_print id=11270158}</div>
  
  	  {if $badge->badge_info.badge_cost > 0}
  	  <div style="margin-top: 10px; color: red">{lang_print id=11270161}</div>
  	  {/if}	
  
    <table cellspacing="0" cellpadding="5" style="margin: 10px">
      <tr>
        <td align="right" width="80">{lang_print id=11270021}:</td>
        <td >{$badge->badge_info.badge_title}</td>
      </tr>
      <tr>
        <td align="right" width="80">{lang_print id=11270018}:</td>
        <td>
          {if $badge->badge_info.badge_cost > 0}
            {$setting.setting_epayment_currency_symbol}{$badge->badge_info.badge_cost}
          {else}
            {lang_print id=11270022}
          {/if}
        </td>
      </tr>
    </table>
    <div><b>{lang_print id=11270082}</b></div>
    <br>
    <input type="hidden" name="task" value="add" />
    <input type="hidden" name="badge_id" value="{$badge->badge_info.badge_id}" />
    <input type="submit" class="button" value="{lang_print id=11270106}" />
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
    </form>
	</div>  
  
{/if}
  
{if !empty($other_badges)}
  <div class="header">{lang_print id=11270140}</div>
  <div class="portal_content">
    <table cellspacing="0" cellpadding="0">
      {foreach from=$other_badges  item=other_badge}
        <tr>
          <td widht="60" style="padding: 5px 10px 5px 0;"><a href="{$url->url_create('badge', null, $other_badge.badge->badge_info.badge_id)}"><img border="0" src="{$other_badge.badge->badge_photo('./images/badge_placeholder.gif', true)}" height="60" width="60" /></a></td>
          <td><a href="{$url->url_create('badge', null, $other_badge.badge->badge_info.badge_id)}"><strong>{$other_badge.badge->badge_info.badge_title}</strong></a>
          <br>{lang_sprintf id=11270091 1=$other_badge.total_approved}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
{/if}  

</td>

</tr>
</table>

{include file='footer.tpl'}