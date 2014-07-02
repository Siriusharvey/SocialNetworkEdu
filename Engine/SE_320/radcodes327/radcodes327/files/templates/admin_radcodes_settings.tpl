{include file='admin_header.tpl'}

<h2>{lang_print id=11000007}</h2>
{lang_print id=11000008}
<br><br>



{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_radcodes_settings.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11000009}</td></tr>
  <tr><td class='setting1'>{lang_print id=11000010}</td></tr>

  <tr><td class='setting2'>
  <select name='setting_radcodes_remote_type'>
  <option value='file_get_contents' {if $setting.setting_radcodes_remote_type == 'file_get_contents'}selected='selected'{/if}>Default</option>
  <option value='CURL' {if $setting.setting_radcodes_remote_type == 'CURL'}selected='selected'{/if}>CURL</option>

  </select>
  </td>
  </tr>
</table>
<br>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11000016}</td></tr>
  <tr><td class='setting1'>{lang_print id=11000017}</td></tr>
  <tr><td class='setting2'>
    <input type='text' class='text' name='setting_radcodes_google_map_api' value='{$setting.setting_radcodes_google_map_api}' style='width: 100%' />
  </td>
  </tr>
</table>
<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>



{include file='admin_footer.tpl'}
