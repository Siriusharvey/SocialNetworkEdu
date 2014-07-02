{include file='header.tpl'}
{$tep}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_settings.php'>SMS Settings</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_compose_sms.php'>Compose New SMS</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_address_smsbook.php'>Address Book</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_sms_history.php'>SMS History</a></td>
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

{* JAVASCRIPT FOR SHOWING DEP FIELDS *}
  {literal}
<script type="text/javascript">
function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

//Contador de caracteres.
function Contar(entrada,salida,texto,caracteres) {

  var entradaObj=getObject(entrada);
  var salidaObj=getObject(salida);
  var longitud=caracteres - entradaObj.value.length;
  if(longitud <= 0) {
    longitud=0;
    texto='<span class="disable"> '+texto+' </span>';
    entradaObj.value=entradaObj.value.substr(0,caracteres);
  }
  salidaObj.innerHTML = texto.replace("CHAR",longitud);
}


function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=720,height=200,left = 100,top = 100');");
}

function chk()
{
var d=document.sms;
if(d.receiver.value=="")
{
alert("Enter the To Address");
d.receiver.focus();
return false;
}
if(d.message.value=="")
{
alert("Enter the To message");
d.message.focus();
return false;
}
return true;

}
</script>
{/literal}
<form name="sms" action="user_compose_sms.php" method="post">
<div align="center">
<table id="Table_01" width="499" height="40" border="0" cellpadding="0" cellspacing="0">
<tr><td colspan="2" align="center" style="color:#FF0000">{$dmsg}</td></tr>

<tr>
<td class='form1' width="163">To:</td>
<td class='form2' valign="top">
<input name="receiver" type="text" class="o" size="26" value="{$tono}">&nbsp;&nbsp;<a href="javascript:popUp('format.php')">Format</a>
<input type="hidden" name="r_receiver" value="Receiver"></td>
</tr>
<tr>
<td width="163" class='form1'>&nbsp;</td>
<td class='form2'>
<a href="coverage_list.php" >View Coverage List</a></td>
</tr>

</table>
</div>
<table width="499" align="center" class="0" border="0" cellspacing="1" cellpadding="5" width="423">
 <tr>
<td align="right" class='form1' valign="top">SMS Message</td>
<td class='form2' width="293" valign="top">
<textarea name="message" cols="44" rows="6" id="eBann" maxlength="100" size="60" onKeyUp="Contar('eBann','sBann','CHAR characters left.',160);"></textarea>
	<DIV id="sBann" class="o" align="right">160 characters left.</DIV>
	
</td>
</tr>
</table>
<p align="center">
<input type='hidden' name='task1' value='next_task'>
<input type="submit" name="Submit" value="Submit" onclick="return chk();"></p>
</form>
{include file='footer.tpl'}


