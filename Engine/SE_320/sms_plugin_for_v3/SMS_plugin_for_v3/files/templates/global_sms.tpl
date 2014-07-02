{include file='admin_header.tpl'}

{* $Id: admin_viewreports.tpl 8 2009-01-11 06:02:53Z nico-izo $ *}

<h2>Global SMS Services - Clickatel Details</h2>

<br />
<br />

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <form  method='POST' action="global_sms.php" name="f1">
  <tr>
  <td colspan="3" style="color:#FF0000">{$dmsg}</td>
  </tr>
   <tr>
  <td colspan="3" >&nbsp;</td>
  </tr>
  <tr>
   <tr>
   <td>Clickatel User ID  </td>
   <td width="2%">&nbsp;</td>
  <td><input type='text' class='text' name='sms_userid' value='{$sel_qry.sms_userid}' size='15' maxlength='50'>&nbsp;</td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <td>Clickatel API ID  </td>
  <td width="2%">&nbsp;</td>
  <td><input type='text' class='text' name='apiid' value='{$sel_qry.apiid}' size='15' maxlength='50'> </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
   <td>Clickatel Password  </td>
   <td width="2%"></td>
  <td><input type="text" class='text' name='password' value='{$sel_qry.password}' size='15' maxlength='50'>&nbsp;</td>
  </tr>
   <tr><td>&nbsp;</td></tr>  
    <tr>
   <td>Paypal Email  </td>
   <td width="2%"></td>
  <td><input type="text" class='text' name='email' value='{$sel_qry.email}' size='30' maxlength='50'>&nbsp;</td>
  </tr>
  <tr><td>&nbsp;</td></tr>
   <tr>
   <td>Currency  </td>
   <td width="2%"></td>
  <td><input type="text" class='text' name='currency_sign' value='{$sel_qry.currency_sign}' size='2' maxlength='2'> - 
  <input type="text" class='text' name='currency_name' value='{$sel_qry.currency_name}' size='5' maxlength='5'>
  </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
  <td colspan="3" align="center"><input type='submit' class='button' name="submit" value="Save"> </td>
   </tr>
   </form>
  </table>
</div>
</td></tr></table>

<br>




{include file='admin_footer.tpl'}