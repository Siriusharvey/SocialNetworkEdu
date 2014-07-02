{include file='admin_header.tpl'}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=11270046}</h2>
  {lang_print id=11270066}

  <br><br>

  {* SHOW SUCCESS MESSAGE *}
  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
  {/if}

  <table cellpadding='0' cellspacing='0' width='600'>
  <form action='admin_levels_badgesettings.php' method='POST'>
  <tr><td class='header'>{lang_print id=11270047}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11270048}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_badge_allow' id='badge_allow_3' value='3'{if $level_info.level_badge_allow == 3} checked='checked'{/if}>&nbsp;</td><td><label for='badge_allow_3'>{lang_print id=11270122}</label></td></tr>    
    <tr><td><input type='radio' name='level_badge_allow' id='badge_allow_1' value='1'{if $level_info.level_badge_allow == 1} checked='checked'{/if}>&nbsp;</td><td><label for='badge_allow_1'>{lang_print id=11270049}</label></td></tr>
    <tr><td><input type='radio' name='level_badge_allow' id='badge_allow_0' value='0'{if $level_info.level_badge_allow == 0} checked='checked'{/if}>&nbsp;</td><td><label for='badge_allow_0'>{lang_print id=11270050}</label></td></tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11270138}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11270150}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_badge_edit' id='badge_edit_1' value='1'{if $level_info.level_badge_edit == 1} checked='checked'{/if}>&nbsp;</td><td><label for='badge_edit_1'>{lang_print id=11270151}</label></td></tr>
    <tr><td><input type='radio' name='level_badge_edit' id='badge_edit_0' value='0'{if $level_info.level_badge_edit == 0} checked='checked'{/if}>&nbsp;</td><td><label for='badge_edit_0'>{lang_print id=11270152}</label></td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11270141}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_badge_delete' id='badge_delete_1' value='1'{if $level_info.level_badge_delete == 1} checked='checked'{/if}>&nbsp;</td><td><label for='badge_delete_1'>{lang_print id=11270142}</label></td></tr>
    <tr><td><input type='radio' name='level_badge_delete' id='badge_delete_0' value='0'{if $level_info.level_badge_delete == 0} checked='checked'{/if}>&nbsp;</td><td><label for='badge_delete_0'>{lang_print id=11270143}</label></td></tr>
    </table>
  </td></tr>

  </table>

  <br>
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11270060}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11270061}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='text' name='level_badge_maxnum' value='{$level_info.level_badge_maxnum}' maxlength='10' size='8'>&nbsp;{lang_print id=11270062}</tr>
    </table>
  </td></tr></table>

  <br>

  
  <input type='submit' class='button' value='{lang_print id=11270065}'>
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_info.level_id}'>
  </form>

</td>
</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{foreach from=$global_plugins key=plugin_k item=plugin_v}
{section name=level_page_loop loop=$plugin_v.plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $plugin_v.plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$plugin_v.plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$plugin_v.plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/foreach}

<tr>
<td class='vert_tab0'>
  <div style='height: 1650px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}