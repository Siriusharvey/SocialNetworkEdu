{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='sms_settings.php'>SMS Settings</a></td>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='compose_sms.php'>Compose New SMS</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='address_smsbook.php'>Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='sms_history.php'>SMS History</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='view_addressbook.php'>View Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='view_smscredits.php'>View SMS Credits</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='buy_credits.php'>Buy SMS Credits</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>


<table width="100%">
<tr>
<td width="50%" align="left"><strong><h3>Country/Network</h3></strong></td>
</tr>
{foreach from=$coverage item=coverage}
<tr>
<td align="left">{if $coverage.cov !=""}<strong>{$coverage.cov}</strong>{else}{$coverage.net}{/if}</td>
</tr>
{/foreach}

</table>


{include file='footer.tpl'}


