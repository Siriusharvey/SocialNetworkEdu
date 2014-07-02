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
<td class='tab1' NOWRAP><a href='user_sms_history.php'>SMS History</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_view_addressbook.php'>View Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_view_smscredits.php'>View SMS Credits</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_buy_credits.php'>Buy SMS Credits</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
<br>
	<p style="margin-left: 5px; margin-right: 5px; margin-bottom:0">			

			<img border="0" src='./images/info2.jpg' style="vertical-align:middle;" alt=""><span class="txtn">&nbsp; 

			Message History</span></p></br></br></br></br></br></br></br></br></br></br></br></br></br></br></br>
			

			<form method="POST" action="user_sms_history.php">

				<p style="margin-left: 5px; margin-right: 5px; margin-bottom: 0">

				<input class="o" type="submit" name="download" value="Download CSV" name="B3" ></p>
                 <input type='hidden' name='p' value='{$p}'>
				 <table width="793" cellpadding='0' cellspacing='0' class='messages_table' style="border:1 #CCCCCC solid">
 <tr bgcolor="#CCCCCC">
<td width="169" class='form1' align="center">Date</td>
<td width="186" class='form1' align="center">From</td>
<td width="182" class='form1' align="center">To</td>
<td width="346" class='form1' align="center">Message</td>
</tr>
  {* LIST SENT MESSAGES *}
  {section name=pm_loop loop=$pms}
<tr bgcolor="#E5E5E5">
<td class='form1' align="center">{$pms[pm_loop].pm_date}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_fromno}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_tono}</td>
<td class='form1' align="center">{$pms[pm_loop].pm_message}</td>
</tr>	
  {/section}
  </table>
			</form>
{include file='footer.tpl'}