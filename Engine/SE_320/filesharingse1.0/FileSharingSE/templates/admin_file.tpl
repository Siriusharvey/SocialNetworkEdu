{* $Id: admin_file.tpl *}

{include file='admin_header.tpl'}
{include file='admin_file_cat_js.tpl'}


<h2>{lang_print id=7800077}</h2>
{lang_print id=7800078}

<br><br>

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{elseif $error != 0}

	<div class='success'>
		{lang_print id=$errorMsg}
	</div>
{elseif $act != 0}

	<div class='success'>
		<img src='../images/success.gif' class='icon' border='0'>
		{lang_print id=$actionMsg}
	</div>
{/if}

<form action='admin_file.php' method='POST' name='uploadfrm'>


<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=192}</td>
</tr>
<td class='setting1'>
  {lang_print id=7800079}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_fileuploads' id='permission_file_1' value='1'{if $setting.setting_permission_fileuploads == 1} checked='checked'{/if}></td>
  <td><label for='permission_file_1'>{lang_print id=7800080}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_fileuploads' id='permission_file_0' value='0'{if $setting.setting_permission_fileuploads == 0} checked='checked'{/if}></td>
  <td><label for='permission_file_0'>{lang_print id=7800081}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>
<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=4500156}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4500157}</td>
  </tr>
  <tr>
    <td class='setting2'>
      
      {* SHOW ADD CATEGORY LINK *}
      <div style='font-weight: bold;'>
        &nbsp;
        Upload Categories - <a href='javascript:adduploadcat();'>[{lang_print id=104}]</a>
      </div>
      
      <div id='categories' style='padding-left: 5px; font-size: 8pt;'>
         {* LOOP THROUGH CATEGORIES *}
        {section name=cat_loop loop=$cats}

        {if $cats[cat_loop].fileuploadcat_name neq ''}
       {* CATEGORY DIV *}
        <div id='cat_{$cats[cat_loop].fileuploadcat_id}'>
        
          {* SHOW CATEGORY *}
          <div style='font-weight: bold;width:600px;padding-bottom:4px;float:left;'>
 	<div style='width:400px;float:left;' id='cat_{$cats[cat_loop].fileuploadcat_id}_span'>          
  	<img src='../images/folder_open_yellow.gif' border='0' class='handle_cat' style='vertical-align: middle; margin-right: 5px; cursor: move;' />
           <a href='javascript:edituploadcat("{$cats[cat_loop].fileuploadcat_id}", "0");' id='cat_{$cats[cat_loop].fileuploadcat_id}_title'>{$cats[cat_loop].fileuploadcat_name}</a></div>
	<div style='width:200px;float:left;'><a href="admin_file.php?action=del_cat&amp;cat={$cats[cat_loop].fileuploadcat_id}" title="delete" onclick="return confirm('Are you sure want to delete this ?');"><img border="none" src="../images/delete.gif" alt="delete" /></a>
	</div>
          </div>
     </div> {/if}
      {/section}
    </div>
   </td>
  </tr>
</table>
<br/>
<input type='submit' class='button' name='save_changes' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}