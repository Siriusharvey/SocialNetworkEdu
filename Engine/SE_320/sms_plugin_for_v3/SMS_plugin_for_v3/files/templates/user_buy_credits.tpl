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
<td class='tab2' NOWRAP><a href='user_view_smscredits.php'>View SMS Credits</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_buy_credits.php'>Buy SMS Credits</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
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
function amt(am,txt)
{

document.pay.amount.value=am;
document.pay.item_name.value="SMS Creidts - "+txt;
}
</script>
{/literal}


  <table cellpadding='0' cellspacing='0'>
  
  <tr>
  <td colspan="6" style="color:#FF0000" align="center">{$dmsg}</td>
  </tr>
  
  <tr>
  <td class='form1' colspan="2">Subscription Option</td>
   </tr>
  {* LIST SENT MESSAGES *} 
  {section name=pm_loop loop=$pms}
 
  <tr>
  <td class='form1'>
  {if $smarty.section.pm_loop.index=="0"}
  <input type="radio" name="fcredit" id="fcredit" checked="checked" value="{$pms[pm_loop].pm_value}" onclick="return amt({$pms[pm_loop].pm_value},'{$pms[pm_loop].pmconvo_destext}');"/>
  {assign var=first_amount value=$pms[pm_loop].pm_value}
  {assign var=first_destext value=$pms[pm_loop].pmconvo_destext}
  {else}
   <input type="radio" name="fcredit" id="fcredit"  value="{$pms[pm_loop].pm_value}" onclick="return amt({$pms[pm_loop].pm_value},'{$pms[pm_loop].pmconvo_destext}');"/>
   
   {/if}
  
  </td>
  <td class='form2'>{$pms[pm_loop].pmconvo_text}</td>
  </tr>
   
  
    {/section} 
  {if $tot !="0"}
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
  
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="pay">

<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="tx" value="{$userid}|{$fcredit}">
<input type="hidden" name="business" value="{$views.email}">
<input type="hidden" name="item_name" value="SMS Creidts">
<input type="hidden" name="item_number" value="{$userid}">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="{$views.currency_name}">
<!--input type="hidden" name="lc" value="US"-->
<input type="hidden" name="bn" value="PP-BuyNowBF">
<input type="hidden" name="amount" value="">


<input type="hidden" name="return" value="http://{$web_url}?success=yes">
<input type="hidden" name="notify_url" value="http://{$web_url2}pay-pal.php">
<input type="hidden" name="cancel_return" value="http://{$web_url}?success=no">

<br><br>
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="https://www.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1">

<br><br>
</form>
 <script >amt('{$first_amount}','{$first_destext}');</script>
 
  
  </td>
  </tr>
  {/if}
  </table>
 
  
{include file='footer.tpl'}