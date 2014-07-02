{include file='header.tpl'}

{assign var=badgeassignment_dateapproved value=$datetime->timezone($badgeassignment->badgeassignment_info.badgeassignment_dateapproved, $global_timezone)}
{assign var=badge value=$badgeassignment->badgeassignment_badge}
{assign var=owner value=$badgeassignment->badgeassignment_user}

<div class='page_header'>
{$badge->badge_info.badge_title}: {$owner->user_displayname}
</div>
<div>{lang_print id=11270036}</div>

{*
<div class='page_header'>{lang_print id=11270006}</div>
*}

<br>
<br>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>

<td valign='top'>

  <div id="badge_assignment_info">
    <div class="badge_assignment_photo" style="float: left; margin-right: 10px; text-align: center;">
      <a href="{$url->url_create('profile', $owner->user_info.user_username)}"><img class='photo' src='{$owner->user_photo("./images/nophoto.gif")}' border='0'></a>
      <br><a href="{$url->url_create('profile', $owner->user_info.user_username)}">{$owner->user_displayname}</a>
    </div>  
    <div class="badge_assignment_desc">
      {$badgeassignment->badgeassignment_info.badgeassignment_desc}
    </div>  
  </div>

</td>

<td style='width: 200px; vertical-align: top; text-align: left; padding-left: 10px;'>

  <div class="badge_browse_photo">
    <a href="{$url->url_create('badge', null, $badge->badge_info.badge_id)}"><img src="{$badge->badge_photo('./images/badge_placeholder.gif')}" alt="{$badge->badge_info.badge_title}" border="0" /></a>
    <br><strong><a href="{$url->url_create('badge', null, $badge->badge_info.badge_id)}">{$badge->badge_info.badge_title}</a></strong>
  </div>
  <div class="badge_browse_options" style="text-align: center">
    {lang_print id=11270073} {$datetime->cdate($setting.setting_dateformat,$badgeassignment_dateapproved)}
  </div>
  
  <table class='profile_menu' cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='profile_menu1' nowrap='nowrap'>
        <a href='{$url->url_create('profile', $owner->user_info.user_username)}'>
          <img src='./images/icons/profile16.gif' class='icon' border='0' />
          {lang_sprintf id=11270154 1=$owner->user_displayname_short}
        </a>
      </td>
    </tr>
    <tr>  
      <td class='profile_menu1' nowrap='nowrap'>
        <a href='{$url->url_create('badge', null, $badge->badge_info.badge_id)}'>
          <img src='./images/icons/admin_levels16.gif' class='icon' border='0' />
          {lang_print id=11270155}
        </a>
      </td>      
    </tr>  
  </table>
</td>
</tr>
</table>

{include file='footer.tpl'}