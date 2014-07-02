{include file='admin_header.tpl'}

{* $Id: admin_levels_albumsettings.tpl 16 2009-01-13 04:01:31Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table  cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px; height:auto;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}' style='height:auto;'>

  <h2>{lang_print id=7800035}</h2>
  {lang_print id=7800034}

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
  <form action='admin_levels_filesettings.php' method='POST'>
  <!--tr><td class='header'>{lang_print id=7800042}</td></tr>
  <tr><td class='setting1'>
 				 {lang_print id=7800024}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_file_upload_allow' id='file_allow_1' value='1'{if $level_info.level_file_upload_allow == 1} checked='checked'{/if}>&nbsp;</td><td><label for='file_allow_1'>{lang_print id=7800025}</label></td></tr>
    <tr><td><input type='radio' name='level_file_upload_allow' id='file_allow_0' value='0'{if $level_info.level_file_upload_allow == 0} checked='checked'{/if}>&nbsp;</td><td><label for='file_allow_0'>{lang_print id=7800026}</label></td></tr>
    </table>
  </td></tr></table>

  <br>
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=7800039}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=7800033}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='text' name='level_file_upload_maxnum' value='{$level_info.level_file_upload_maxnum}' maxlength='3' size='5'>&nbsp;{lang_print id=7800032}</tr>
    </table>
  </td></tr></table-->

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=7800040}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=7800031}
  </td></tr><tr><td class='setting2'>
  <textarea name='level_file_upload_exts' rows='2' cols='40' class='text' style='width: 100%;'>{$level_info.level_file_upload_exts}</textarea>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=7800037}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=7800030}
  </td></tr><tr><td class='setting2'>
  <input type='text' class='text' size='5' name='level_file_upload_maxsize' maxlength='6' value='{$level_info.level_file_upload_maxsize}'> KB
  </td></tr>
  </table>

  

  <br>
  
  <input type='submit' class='button' value='{lang_print id=173}'>
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
  <div style='height: 220px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}