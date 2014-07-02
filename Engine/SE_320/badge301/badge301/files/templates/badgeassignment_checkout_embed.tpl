
{assign var='badgeassignment' value=$result.item}
{assign var='badge' value=$badgeassignment->badgeassignment_badge}

<div style="margin: 15px">

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td>
        <div class='seBadgePhoto' style='width: 180px' valign='top'>
          <a href='{$url->url_create("badge", NULL, $badge->badge_info.badge_id)}'><img src='{$badge->badge_photo("./images/badge_placeholder.gif")}' border='0' ></a>
        </div>
      </td>
      <td width="100%" style="padding-left: 10px;" valign='top'>
       
        <div class='seBadgeTitle'><a href='{$url->url_create("badgeassignment", NULL, $badgeassignment->badgeassignment_info.badgeassignment_id)}'>{$badge->badge_info.badge_title}</a></div>
        <div class='seBadgeStats'>
            {assign var=badgeassignment_datecreated value=$datetime->timezone($badgeassignment->badgeassignment_info.badgeassignment_datecreated, $global_timezone)}

            {lang_print id=11270024}
            {lang_sprintf id=11270026 1=$datetime->cdate($setting.setting_dateformat, $badgeassignment_datecreated) 2=$datetime->cdate($setting.setting_timeformat, $badgeassignment_datecreated)}

        </div>
        {if $badgeassignment->badgeassignment_info.badgeassignment_desc}
        <div class='seBadgeDesc'>
          {$badgeassignment->badgeassignment_info.badgeassignment_desc|truncate:165:"...":true}
        </div>
        {/if}
        
        <div style="color:#6FB305; font-family:Georgia,Times,Times New Roman,serif; font-size: 36px;">{$setting.setting_epayment_currency_symbol}{$result.amount}</div>
        
      {* end right column *}  
      </td>

    </tr>
  </table>
  
</div>

