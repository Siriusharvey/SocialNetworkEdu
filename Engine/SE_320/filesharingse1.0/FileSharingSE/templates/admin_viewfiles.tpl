{include file='admin_header.tpl'}

{* $Id: admin_viewfiles.tpl *}

<h2>{lang_print id=7800002}</h2>
{lang_print id=7800049}

<br><br>

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='0' cellspacing='0' align='center'>
<form action='admin_viewfiles.php' method='POST'>
<tr>
<td>Title<br><input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='50'>&nbsp;</td>
<td>Owner<br><input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50'>&nbsp;&nbsp;</td>
<td><input type='submit' class='button' value='{lang_print id=1002}'></td>
<input type='hidden' name='s' value='{$s}'>
</form>
</tr>
</table>
</div>
</td></tr></table>

<br>

{if $total_files == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=7800050}</b></div>
  </td>
  </tr>
  </table>
  <br>

{else}

  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }

  var file_id = 0;
  function confirmDelete(id) {
    file_id = id;
    TB_show('{/literal}{lang_print id=7800076}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteFile() {
    window.location = 'admin_viewfiles.php?task=deletefile&userupload_id='+file_id+'&s={/literal}{$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}{literal}';
  }
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=7800055}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteFile();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>

  <div class='pages'>{lang_sprintf id=7800051 1=$total_files} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewfiles.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

  <form action='admin_viewfiles.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewfiles.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
  <td class='header'><a class='header' href='admin_viewfiles.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=7800052}</a></td>
  <td class='header'><a class='header' href='admin_viewfiles.php?s={$u}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=7800053}</a></td>
    <td class='header' width='100'>{lang_print id=153}</td>
  </tr>
  {section name=file_loop loop=$files}
    {assign var='file_url' value=$url->url_create('file', $files[file_loop].file_author->user_info.user_username, $files[file_loop].userupload_id)}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_file_{$files[file_loop].userupload_id}' value='1'></td>
    <td class='item' style='padding-left: 0px;'>{$files[file_loop].userupload_id}</td>
    <td class='item'>{if $files[file_loop].userupload_title == ""}<i>{lang_print id=589}</i>{else}{$files[file_loop].userupload_title}{/if}&nbsp;</td>
    <td class='item'><a href='{$url->url_create('profile', $files[file_loop].file_author->user_info.user_username)}' target='_blank'>{$files[file_loop].file_author->user_displayname}</a></td>
    
     <td class='item'>[ <a href='admin_loginasuser.php?user_id={$files[file_loop].file_author->user_info.user_id}&return_url={$url->url_encode($file_url)}' target='_blank'>{lang_print id=7800054}</a> ] [ <a href="javascript:void(0);" onClick="confirmDelete('{$files[file_loop].userupload_id}');">{lang_print id=155}</a> ]</td>
    </tr>
  {/section}
  </table>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    <br>
    <input type='submit' class='button' value='{lang_print id=788}'>
    <input type='hidden' name='task' value='delete'>
    <input type='hidden' name='s' value='{$s}'>
    <input type='hidden' name='p' value='{$p}'>
    <input type='hidden' name='f_title' value='{$f_title}'>
    <input type='hidden' name='f_owner' value='{$f_owner}'>
    </form>
  </td>
  <td align='right' valign='top'>
    <div class='pages2'>{lang_sprintf id=7800051 1=$total_files} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewfiles.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}

{include file='admin_footer.tpl'}