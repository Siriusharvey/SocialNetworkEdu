{if !empty($badges)}
    <div class="badge_profile_embed">
	  {if $badges.level}
	    <div class="badge_profile_entry"><a href="{$url->url_create('badgeusers', 'level', $owner->user_info.user_level_id)}" title="{$badges.level->badge_info.badge_title}"><img border="0" src="{$badges.level->badge_photo()}" alt="{$badges.level->badge_info.badge_title}" /></a></div>
	  {/if}
	  {if $badges.subnet}
	    <div class="badge_profile_entry"><a href="{$url->url_create('badgeusers', 'subnet', $owner->user_info.user_subnet_id)}" title="{$badges.subnet->badge_info.badge_title}"><img border="0" src="{$badges.subnet->badge_photo()}" alt="{$badges.subnet->badge_info.badge_title}" /></a></div>
	  {/if} 
	  {if $badges.profilecat}
	    <div class="badge_profile_entry"><a href="{$url->url_create('badgeusers', 'profilecat', $owner->user_info.user_profilecat_id)}" title="{$badges.profilecat->badge_info.badge_title}"><img border="0" src="{$badges.profilecat->badge_photo()}" alt="{$badges.profilecat->badge_info.badge_title}" /></a></div>
	  {/if} 
	  {if !empty($badges.assignments)}
	    {foreach from=$badges.assignments item=badgeassignment}
	      {if $badgeassignment.badge->badge_info.badge_link_details}
	      	{assign var=badgelink value=$url->url_create('badgeassignment', null, $badgeassignment.badgeassignment->badgeassignment_info.badgeassignment_id)}
	      {else}
	        {assign var=badgelink value=$url->url_create('badge', null, $badgeassignment.badgeassignment->badgeassignment_info.badgeassignment_badge_id)}
	      {/if}
	      <div class="badge_profile_entry"><a href="{$badgelink}" title="{$badgeassignment.badge->badge_info.badge_title}"><img border="0" src="{$badgeassignment.badge->badge_photo()}" alt="{$badgeassignment.badge->badge_info.badge_title}" /></a></div>
	    {/foreach}
	  {/if}
    </div>
{/if}
