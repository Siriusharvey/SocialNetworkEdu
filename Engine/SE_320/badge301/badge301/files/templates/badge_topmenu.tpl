
{if $badge_topmenu_items|@count > 0}

    <div class='top_menu_link_container top_menu_main_link_container'>
      <div class='top_menu_link top_menu_main_link'>
        <a href="javascript:void(0);" onclick="$('menu_badge_dropdown').style.display = ( $('menu_badge_dropdown').style.display=='none' ? 'inline' : 'none' ); this.blur(); return false;" class='top_menu_item'>
          {lang_print id=11270165}
        </a>
      </div>
      <div class='menu_main_dropdown' id='menu_badge_dropdown' style='display: none;'>
        <div>
          {foreach from=$badge_topmenu_items key=badge_topmenu_item_id item=badge_topmenu_item_title}
          <div class='menu_main_item_dropdown'>
            <a href='{$url->url_create('badge', null, $badge_topmenu_item_id)}' class='menu_main_item' style="text-align: left;">
              {$badge_topmenu_item_title}
            </a>
          </div>
          {/foreach}
        </div>
      </div>
    </div>

{/if}