{include file='header.tpl'}
{$tep}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_sms_settings.php'>SMS Settings</a></td>
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
if(d.mobile_no.value=="")
{
alert("Enter the To Mobile Number");
d.mobile_no.focus();
return false;
}
return true;

}
</script>
{/literal}
<form name="sms" action="user_sms_settings.php" method="post">
<div  align="center">
<table id="Table_01" width="499" height="80" border="0" cellpadding="0" cellspacing="0">

<tr><td align="center" style="color:#FF0000">{$dmsg}</td></tr>
<tr>
      
      <td class='form2'>Mobile No:
        <input name='mobile_no' id="mobile_no" type='text' class='text' maxlength='50' size='40' value='{$smsexp_result.mobile_no}'>
      </td>
    </tr>
<tr>
 <td class='form2'>
        <input type='checkbox' name='member_sms' id='tos' value='1'{if $smsexp_result.member_sms == 1} CHECKED{/if}>
        <label for='tos'>Would you like to recive SMS from your friends while you are off line ?</label>
</td>
</tr>
<tr>
 <td class='form2'>
        <input type='checkbox' name='admin_sms' id='tos' value='1'{if $smsexp_result.admin_sms == 1} CHECKED{/if}>
        <label for='tos'>Would you like to recive SMS from admin regarding updates and news ?</label>
</td>
</tr>

<tr>
<td class='form2' colspan="2" align="center">
<input type="submit" name="Submit" value="Submit" onclick="return chk();">
</td>
</tr>

</table>
</div>
</form>
{include file='footer.tpl'}


