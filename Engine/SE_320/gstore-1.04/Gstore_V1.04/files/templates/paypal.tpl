{include file='header.tpl'}
<img src='./images/icons/gstore_redgister.png' border='0' class='icon_big' style="margin-bottom: 15px;">
<div class='page_header'>{lang_print id=5555161} > Payment</div>
<div>
  {lang_print id=5555162}
</div>
<br />
<div style="border:1px solid #CCCCCC; padding:10px 30px 10px 30px; background-color:#f3f3f3;">
<div align="center" style=" border:1px solid #CCCCCC; background-color:#f8f8f8;">
<br /><br />
<div align="center" class="page_header">
Please check your order details before proceeding to payment
</div>
   <div align="center" style="display:">
    Please Check these details are correct then click proceed to be directed to the Paypal website
 
     <br /><br />
				<table width="50%" border="1" cellspacing="0" cellpadding="15" style="margin:auto;">
				  <tr bgcolor="#CCCCCC">
					<td align="center"><b>Items Title</b></td>
					<td width="10"><b>Qty</b></td>
					<td align="left" width="75"><b>Price</b></td>
				  </tr>
				  
				  <tr>
					<td align="center">{$item_title}</td>
					<td>{$qty}</td>
					<td align="left">{lang_print id=$setting.gstore_currency}{$price}</td>
				  </tr>
				  
				  <tr {$display}>
					<td align="center">&nbsp;</td>
					<td>&nbsp;</td>
					<td align="left">Shipping:<br /> {lang_print id=$setting.gstore_currency}{$shipping_charge}</td>
				  </tr>
				  
				   <tr bgcolor="#CCCCCC">
					<td align="center">{if $msg == '1' }<span style="color:red;">You have exeeded the sellers stock holding you have been allocated the sellers entire stock holding as Qty</span>{/if}&nbsp;</td>
					<td>&nbsp;</td>
					<td align="left" bgcolor="#FFD9D9"><b>TOTAL: {lang_print id=$setting.gstore_currency}{$total}</b></td>
				  </tr>
				
				  <tr bgcolor="#CCCCCC">
					<td align="center" height="6"></td>
					<td></td>
					<td></td>
				  </tr>
				</table>
 
    <br /><br />
	
    <img src="../images/icons/paypal.png" border="0" />
	
    <br />
     You will be asked for your delivery details if you have not already recorded them at the Paypal website
	<br /><br />
				
		<table width="440" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="45%" align="right">
					<form id="paypal" name="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
		
		
		
						<input type="hidden" name="cmd" value="_xclick">
		
		
		
						<input type="hidden" name="business" value="{$paypal_email}">
		
		
		
						<input type="hidden" name="lc" value="{$country_code}">
		
		
		
						<input type="hidden" name="item_name" value="{$item_title}">
		
						<input type="hidden" name="shipping" value="{$shipping_charge}">
		
						<input type="hidden" name="item_number" value="{$item_id}">
		
						<input type="hidden" name="quantity" value="{$qty}">
		
						<input type="hidden" name="amount" value="{$price}">
		
		
		
						<input type="hidden" name="currency_code" value="{$currency_code}">
		
		
		
						<input type="hidden" name="button_subtype" value="products">
		
		
						<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
		
		
						<input type='submit' class='button' value='Proceed to the Paypal website' name="submit" alt="PayPal - The safer, easier way to pay online!" /> 
		
		
		
				</form>
			</td>
			<td width="10%">&nbsp;</td>
			<td width="45%" align="left">
					<form id="return" action="browse_gstores.php" method="post">
						<input type='submit' class='button' value='Return to the gstore' /> 
				</form>
			</td>
		  </tr>
		</table>
				
				
       <br /><br />
</div>
</div>
</div>
<div style="height:30px;"></div>

{include file='footer.tpl'}