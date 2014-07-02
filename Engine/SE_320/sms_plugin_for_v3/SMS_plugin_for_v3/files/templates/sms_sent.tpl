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
  <form  method='POST' action="sms_sent.php" name="f1">
  <tr>
  <td colspan="3" style="color:#FF0000" align="center">{$dmsg}</td>
  </tr>
   <tr>
  <td colspan="3" >&nbsp;</td>
  </tr>
  <tr>
   <tr>
   <td>Message</td>
   <td width="2%">&nbsp;</td>
  <td>
  
  <textarea name="sms_message"  class="text" rows="5" cols="50"></textarea>
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