<div class='header'>{lang_print id=11270177}</div>
<div class='portal_content'>

{section name=badgeassignment_loop loop=$badgeassignments}
  {assign var=badgeassignment_dateapproved value=$datetime->timezone($badgeassignments[badgeassignment_loop].badgeassignment->badgeassignment_info.badgeassignment_dateapproved, $global_timezone)}
  
  {if $smarty.section.badgeassignment_loop.first}
    <table cellspacing="0" cellpadding="0">
  {/if}
  
  {if $badgeassignments[badgeassignment_loop].badge->badge_info.badge_link_details}
    {assign var=userlink value=$url->url_create('badgeassignment', null, $badgeassignments[badgeassignment_loop].badgeassignment->badgeassignment_info.badgeassignment_id)}
  {else}
    {assign var=userlink value=$url->url_create('profile',$badgeassignments[badgeassignment_loop].user->user_info.user_username)}
  {/if}
  
   <tr>
     <td widht="60" style="padding: 5px 10px 5px 0;"><a href="{$url->url_create('profile',$badgeassignments[badgeassignment_loop].user->user_info.user_username)}"><img border="0" src="{$badgeassignments[badgeassignment_loop].user->user_photo('./images/nophoto.gif', TRUE)}" height="60" width="60" /></a></td>
     <td>
       <strong><a href="{$userlink}">{$badgeassignments[badgeassignment_loop].user->user_displayname|truncate:18:"...":true}</a></strong>
       <div class='badge_entry_name'>{lang_sprintf id=11270180 1=$url->url_create('badge', null, $badgeassignments[badgeassignment_loop].badge->badge_info.badge_id) 2=$badgeassignments[badgeassignment_loop].badge->badge_info.badge_title}</div>
       <div class='badge_entry_date'>{lang_sprintf id=11270181 1=$datetime->cdate($setting.setting_dateformat,$badgeassignment_dateapproved)}</div>
     </td>
   </tr>  
  
  {if $smarty.section.badgeassignment_loop.last}
    </table>
  {/if}  
  
{sectionelse}
  {lang_print id=11270182}
{/section}  
</div>
<div class='portal_spacer'></div>

