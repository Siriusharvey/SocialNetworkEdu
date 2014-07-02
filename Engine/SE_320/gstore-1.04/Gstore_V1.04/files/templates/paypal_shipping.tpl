{include file='header.tpl'}
<img src='./images/icons/parcel.png' border='0' class='icon_big' style="margin-bottom: 15px;">
<div class='page_header'>{lang_print id=5555161} > Shipping</div>

<div>
  Shipping details
</div>

<br />

<div style="border:1px solid #CCCCCC; padding:10px 30px 10px 30px; background-color:#f3f3f3;">
<div align="center" style=" border:1px solid #CCCCCC; background-color:#f8f8f8;">

<br /><br />
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <div class='error'>
          <img src='./images/error.gif' border='0' class='icon' />
          {lang_print id=$is_error}
        </div>
      </td>
    </tr>
  </table>
  <br /><br />
{/if}

<div align="center" class="page_header">
{if $apply_shipping_charges != 'No shipping'}
The Seller Needs to Calculate Your Shipping Charges
{else}
The Seller Has Specified  Free Shipping on This Item
{/if}
</div>

<br>
<form id="paypal" name="paypal" action="paypal.php" method="post">
<div align="center">
{if $apply_shipping_charges != 'No shipping'}
    Please Choose Your Location Below
	
	<br>
	
	
					          <select class='store_small' name='buyer_location' onchange="document.seBrowseStores.submit();">
            {if $band_a != ""}<option value='1'>{if $setting.gstore_band_a == ""}United Kingdom{else}{$setting.gstore_band_a}{/if} Delivery charge: {lang_print id=$setting.gstore_currency}{$band_a}</option>{/if}
            {if $band_b != ""}<option value='2'>{if $setting.gstore_band_b == ""}United Kingdom{else}{$setting.gstore_band_b}{/if} Delivery charge: {lang_print id=$setting.gstore_currency}{$band_b}</option>{/if}
            {if $band_c != ""}<option value='3'>{if $setting.gstore_band_c == ""}United Kingdom{else}{$setting.gstore_band_c}{/if} Delivery charge: {lang_print id=$setting.gstore_currency}{$band_c}</option>{/if}
            {if $band_d != ""}<option value='4'>{if $setting.gstore_band_d == ""}United Kingdom{else}{$setting.gstore_band_d}{/if} Delivery charge: {lang_print id=$setting.gstore_currency}{$band_d}</option>{/if}
          </select>
		  
     {if $field_error != ""}<div class='form_error'><img src='./images/icons/error16.gif' border='0' class='icon'> {$field_error}</div>{/if}
	 
     <br />
	 {else}
	 <img src="./images/icons/store_tick.png" border="0" />
	 {/if}
	 <br /><br>
	 
	 
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
 
    <br /><br /><br /><br />

	
	
				<input type="hidden" size="2" name="qty" value="{$qty}" >
				<input type="hidden" name="business" value="{$paypal_email}">
				<input type="hidden" name="gstore_settings_user_id" value="{$gstore_settings_user_id}">
				<input type="hidden" name="stockinhand" value="{$stockinhand}">
				<input type="hidden" name="seller_sales" value="{$seller_sales}">
				<input type="hidden" name="stock" value="{$stock}">
				<input type="hidden" name="item_id" value="{$item_id}">
				<input type="hidden" name="item_name" value="{$item_title}">
				<input type="hidden" name="item_sales" value="{$item_sales}">
				<input type="hidden" name="amount" value="{$price}">
				<input type="hidden" name="band_a" value="{$band_a}">
				<input type="hidden" name="band_b" value="{$band_b}">
				<input type="hidden" name="band_c" value="{$band_c}">
				<input type="hidden" name="band_d" value="{$band_d}">   
				<input type="hidden" name="apply_shipping_charges" value="{$apply_shipping_charges}">
			
	
	 
		
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>
						<input type='submit' class='button' value='Proceed' name="submit" /> 
				</form>
			</td>
			
			<td style="padding-left:20px;">
					<form id="return" action="browse_gstores.php" method="post">
						<input type='submit' class='button' value='Return to the store' /> 
				</form>
			</td>
		  </tr>
		</table>
		</div>		
				
       <br />
	   <div style="font-size:10px;">
	   Please be aware if you enter inacurate details here the seller has the right to return you payment and refuse the sale
	   </div>
	   <br>
</div>
</div>
<div style="height:30px;"></div>
{include file='footer.tpl'}