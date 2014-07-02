{include file='admin_header.tpl'}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

{literal}
<script type="text/javascript">
<!-- 
function checkChecked() {
    if($('vid_allow_1').checked) {
         $('provtable1').style.display = 'inline';
         $('provtable2').style.display = 'none';
    }

    if($('vid_allow_2').checked) {
         $('provtable1').style.display = 'none';
         $('provtable2').style.display = 'inline';
    }

    if($('vid_allow_3').checked) {
         $('provtable1').style.display = 'inline';
         $('provtable2').style.display = 'inline';
    }

    if($('vid_allow_0').checked) {
         $('provtable1').style.display = 'none';
         $('provtable2').style.display = 'none';
    }
}
//-->
</script>
<script type="text/javascript">
<!-- 
window.addEvent('domready', function() {
    checkChecked();
    $('vid_allow_1').addEvent('click', function(e){
         $('provtable1').style.display = 'inline';
         $('provtable2').style.display = 'none';
    });
    $('vid_allow_2').addEvent('click', function(e){
         $('provtable1').style.display = 'none';
         $('provtable2').style.display = 'inline';
    });
    $('vid_allow_3').addEvent('click', function(e){
         $('provtable1').style.display = 'inline';
         $('provtable2').style.display = 'inline';
    });
    $('vid_allow_0').addEvent('click', function(e){
         $('provtable1').style.display = 'none';
         $('provtable2').style.display = 'none';
    });
});
//-->
</script>
{/literal}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation='x+5' x=$level_menu|@count}'>

  <h2>{lang_print id=13500021}</h2>
  {lang_print id=13500046}

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
  <form action='admin_levels_vidsettings.php' method='POST'>
  <tr><td class='header'>{lang_print id=13500037}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=13500038}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_vid_allow' id='vid_allow_1' value='1'{if $level_info.level_vid_allow == 1} checked='checked'{/if}>&nbsp;</td><td><label for='vid_allow_1'>{lang_print id=13500039}</label></td></tr>
    <tr><td><input type='radio' name='level_vid_allow' id='vid_allow_2' value='2'{if $level_info.level_vid_allow == 2} checked='checked'{/if}>&nbsp;</td><td><label for='vid_allow_2'>{lang_print id=13500117}</label></td></tr>
    <tr><td><input type='radio' name='level_vid_allow' id='vid_allow_3' value='3'{if $level_info.level_vid_allow == 3} checked='checked'{/if}>&nbsp;</td><td><label for='vid_allow_3'>{lang_print id=13500118}</label></td></tr>
    <tr><td><input type='radio' name='level_vid_allow' id='vid_allow_0' value='0'{if $level_info.level_vid_allow == 0} checked='checked'{/if}>&nbsp;</td><td><label for='vid_allow_0'>{lang_print id=13500040}</label></td></tr>
    </table>
  </td></tr></table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=13500136}</td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=13500139}</b><br>{lang_print id=13500137}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='level_vid_search' id='vid_search_1' value='1'{if $level_info.level_vid_search == 1} checked='checked'{/if}></td><td><label for='vid_search_1'>{lang_print id=13500140}</label>&nbsp;&nbsp;</td></tr>
      <tr><td><input type='radio' name='level_vid_search' id='vid_search_0' value='0'{if $level_info.level_vid_search == 0} checked='checked'{/if}></td><td><label for='vid_search_0'>{lang_print id=13500141}</label>&nbsp;&nbsp;</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=13500142}</b><br>{lang_print id=13500143}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$vid_privacy key=k item=v}
      <tr><td><input type='checkbox' name='level_vid_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_vid_privacy} checked='checked'{/if}></td><td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=13500144}</b><br>{lang_print id=13500145}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$vid_comments key=k item=v}
      <tr><td><input type='checkbox' name='level_vid_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_vid_comments} checked='checked'{/if}></td><td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  </table>

<div id='provtable1'>
  <br>
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=13500041}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=13500042}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='text' name='level_vid_maxnum' value='{$level_info.level_vid_maxnum}' maxlength='3' size='5'>&nbsp;{lang_print id=13500043}</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=13500045}
  </td></tr><tr><td class='setting2'>
  <input type='text' class='text' name='level_vid_maxsize' value='{$level_info.level_vid_maxsize}'> KB
  </td></tr>
  </table>
</div>

<div id='provtable2'>
  <br>
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=13500105}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=13500138}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='text' name='level_vid_prov_maxnum' value='{$level_info.level_vid_prov_maxnum}' maxlength='3' size='5'>&nbsp;{lang_print id=13500043}</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=13500106}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {section name=prov loop=$providers[0]}
    <tr><td><input type='checkbox' name='{$providers[2][prov]}' value='1' {if strstr($level_info.level_vid_prov, $providers[1][prov])}CHECKED{/if}>&nbsp;{$providers[0][prov]}</td></tr>
    {/section}
    </table>
  </td></tr>
  </table>
</div>


  <br>


  <input type='submit' class='button' value='{lang_print id=13500047}'>
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_info.level_id}'>
  </form>

</td>
</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{section name=level_plugin_loop loop=$global_plugins}
{section name=level_page_loop loop=$global_plugins[level_plugin_loop].plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/section}

<tr>
<td class='vert_tab0'>
  <div style='height: 1650px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}