{include file='header.tpl'}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_settings.php'>SMS Settings</a></td>

<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_compose_sms.php'>Compose New SMS</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_address_smsbook.php'>Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_history.php'>SMS History</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_view_addressbook.php'>View Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_view_smscredits.php'>View SMS Credits</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_buy_credits.php'>Buy SMS Credits</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
<br>
{* JAVASCRIPT FOR SHOWING DEP FIELDS *}
  {literal}
<script type="text/javascript">
function ValidateForm(){

var checkFound = false;
for (var counter=0; counter < make_pay.length; counter++) {
if ((make_pay.elements[counter].name == "fcredit") && (make_pay.elements[counter].checked == true)) {
checkFound = true;
}
}
if (checkFound != true) {
alert ("Please check at least one checkbox.");
return false;
}
}
</script>
{/literal}

<form name="make_pay" id="make_pay" action='user_view_smscredits.php' method='POST' onSubmit="return ValidateForm()">
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='form1' colspan="2"><b>SMS Credits Available</b></td>
   </tr>
  <tr>
  <td class='form1'>Credits for SMS:</td>
  <td class='form2'>{$ssms_credits}</td>
  </tr>
<!-- <tr>
  <td class='form1'>Credits for receiving SMS:</td>
  <td class='form2'>{$rsms_credits}</td>
  </tr>  -->
  </table>
  <input type='hidden' name='task1' value='next_task'>
  </form>
  
{include file='footer.tpl'}