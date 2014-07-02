{if !empty($badges)}

    <div class="badge_profile_tab">
    {if $badges.level}
      <div class="badge_profile_entry"><a href="browse_badgeusers.php?type=level&type_id={$owner->user_info.user_level_id}" title="{$badges.level->badge_info.badge_title}"><img border="0" src="{$badges.level->badge_photo()}" /></a></div>
    {/if}
    {if $badges.subnet}
      <div class="badge_profile_entry"><a href="browse_badgeusers.php?type=subnet&type_id={$owner->user_info.user_subnet_id}" title="{$badges.subnet->badge_info.badge_title}"><img border="0" src="{$badges.subnet->badge_photo()}" /></a></div>
    {/if} 
    {if $badges.profilecat}
      <div class="badge_profile_entry"><a href="browse_badgeusers.php?type=profilecat&type_id={$owner->user_info.user_profilecat_id}" title="{$badges.profilecat->badge_info.badge_title}"><img border="0" src="{$badges.profilecat->badge_photo()}" /></a></div>
    {/if} 
    {if !empty($badges.assignments)}
      {foreach from=$badges.assignments item=badgeassignment}
        <div class="badge_profile_entry"><a href="{$url->url_create('badge', null, $badgeassignment.badgeassignment->badgeassignment_info.badgeassignment_badge_id)}" title="{$badgeassignment.badge->badge_info.badge_title}"><img border="0" src="{$badgeassignment.badge->badge_photo()}" /></a></div>
      {/foreach}
    {/if}
    </div>
  <div class="badge_clear"></div>

{/if}
