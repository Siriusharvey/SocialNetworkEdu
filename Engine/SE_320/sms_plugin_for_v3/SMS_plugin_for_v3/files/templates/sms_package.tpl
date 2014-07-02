{include file='admin_header.tpl'}

{* $Id: admin_viewreports.tpl 8 2009-01-11 06:02:53Z nico-izo $ *}

<h2> SMS Package Manage</h2>

<br />
<br />

 

<table  cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
  <table  cellpadding='0' cellspacing='0' align='center'>
 

 
  <tr>
  <td colspan="6" style="color:#FF0000" align="center">{$msg}</td>
  </tr>
  
  <tr class='header' width='10' style='padding-left: 0px;'>
  <td width="20%" class='header' width='10' style='padding-left: 0px;'>S.No </td>
  <td width="30%" class='header' width='10' style='padding-left: 0px;'> Description </td>
  <td width="20%" class='header' width='10' style='padding-left: 0px;'>sms credit </td>
   <td width="20%" class='header' width='10' style='padding-left: 0px;'>Amount  </td>
   <td width="20%" class='header' width='10' style='padding-left: 0px;'>Edit </td>
    <td width="20%" class='header' width='10' style='padding-left: 0px;'>Delete </td>

  </tr>
  
{foreach from=$view_arr item=view_arr name=num}
  <tr class='{cycle values="background1,background2"}'>
  <td class='item' style='padding-right: 0px;'>{$smarty.foreach.num.iteration}</td>
  <td class='item' style='padding-right: 0px;'>{$view_arr.text}  </td>
  <td class='item' style='padding-right: 0px;'>{$view_arr.sms_credit} </td>
  <td class='item' style='padding-right: 0px;'>{$view_arr.value}  </td>
  <td class='item' style='padding-right: 0px;'><a href="sms_package.php?id={$view_arr.id}&act=edit">Edit</a>  </td>
  <td class='item' style='padding-right: 0px;'><a href="sms_package.php?id={$view_arr.id}&act=delete">Delete</a> </td>
  </tr>
{/foreach}
   
  </table>
</div>
</td></tr></table>



<table>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
</table>



<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <form  method='POST' name="f1">
  <tr>
  <td colspan="3" style="color:#FF0000">{$dmsg}</td>
  </tr>
   <tr>
  <td colspan="3" >&nbsp;</td>
  </tr>
  <tr>
  <td>Text</td>
  <td width="2%">&nbsp;</td>
  <td><input type='text' class='text' name='pac_text' value='{$views.text}' size='15' maxlength='50'> </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
   <td>Value  </td>
   <td width="2%"></td>
  <td><input type="text" class='text' name='pac_value' value='{$views.value}' size='15' maxlength='50'>&nbsp;</td>
  </tr>
   <tr><td>&nbsp;</td></tr>
  <tr>
   <td>SMS Credit  </td>
   <td width="2%">&nbsp;</td>
  <td><input type='text' class='text' name='sms_credit' value='{$views.sms_credit}' size='15' maxlength='50'>&nbsp;</td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
  <td colspan="3" align="center"><input type='submit' class='button' name="submit" value="{if $act=='edit'}Update{else}Save{/if}"> </td>
   </tr>
   </form>
  </table>
</div>
</td></tr></table>




{include file='admin_footer.tpl'}