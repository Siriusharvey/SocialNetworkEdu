{include file='admin_header.tpl'}

<h2>{lang_print id=11270085}</h2>
{lang_print id=11270008}
<br />
<br />

{if $is_error}
  <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
{/if}

{if $result}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}


{* JAVASCRIPT FOR CHECK ALL *}
{literal}
<script language='JavaScript'> 
<!---
var badgeassignment_id = 0;
function confirmDelete(id) {
  badgeassignment_id = id;
  TB_show('{/literal}{lang_print id=11270145}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

function deleteBadge() {
  window.location = 'admin_badgeassignments.php?task=deletebadgeassignment&badgeassignment_id='+badgeassignment_id+'&s={/literal}&s={$s}&p={$p}&f_badgeid={$f_badgeid}&f_username={$f_username}&f_approved={$f_approved}&f_epayment={$f_epayment}&f_transaction={$f_transaction}{literal}';
}

function editBadgeassignment(id, desc)
{
	$('badgeassignment_id').defaultValue = id; 
  $('badgeassignment_id').value = id;  
   
  
  $('badgeassignment_desc').defaultValue = desc;  
  $('badgeassignment_desc').value = desc;

  TB_show('{/literal}{lang_print id=11270076}{literal}', '#TB_inline?height=260&width=500&inlineId=edit_badgeassignment_form', '', '../images/trans.gif'); 
}

// -->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=11270146}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteBadge();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY EDIT BADGEASSIGNMENT FORM *}
<div style='display: none;' id='edit_badgeassignment_form'>
<form action="admin_badgeassignments.php" method="POST">
  <div style='margin: 10px auto;'>{lang_print id=11270081}</div>
  
  <table cellpadding='0' cellspacing='2'>
    <tr>
      <td align='right' valign='top'>{lang_print id=11270112}:&nbsp;</td>
      <td>
			  <input type="text" name="badgeassignment_id" id="badgeassignment_id" class="text" size="24" />
      </td>
    </tr>  
    <tr>
      <td align='right' valign='top'>{lang_print id=11270111}:&nbsp;</td>
      <td><textarea class='text' cols='60' rows='5' name='badgeassignment_desc' id='badgeassignment_desc' /></textarea></td>
    </tr>
  </table>
  <br />  
  
  <input type="submit" value="{lang_print id=173}" class="button" />
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type="hidden" name="task" value="update" />
</form>
</div>


<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='1' cellspacing='0' align='center'>
<form action='admin_badgeassignments.php' method='POST'>
<tr>
<td>{lang_print id=11270080}<br><input type='text' class='text' name='f_badgeid' value='{$f_badgeid}' size='15' maxlength='50'></td>
<td>{lang_print id=11270083}<br><input type='text' class='text' name='f_username' value='{$f_username}' size='15' maxlength='50'></td>
            <td>
              {lang_print id=11270014}<br />
              <select name="f_approved">
                <option value=""></option>
                <option value="y" {if $f_approved=='y'}selected='selected'{/if}>{lang_print id=11270063}</option>
                <option value="n" {if $f_approved=='n'}selected='selected'{/if}>{lang_print id=11270064}</option>
              </select>
            </td>
            <td>
              {lang_print id=11270016}<br />
              <select name="f_epayment">
                <option value=""></option>
                <option value="y" {if $f_epayment=='y'}selected='selected'{/if}>{lang_print id=11270063}</option>
                <option value="n" {if $f_epayment=='n'}selected='selected'{/if}>{lang_print id=11270064}</option>
              </select>
            </td>
            <td>
              {lang_print id=11270023}<br />
              <select name="f_transaction">
                <option value=""></option>
                <option value="y" {if $f_transaction=='y'}selected='selected'{/if}>{lang_print id=11270063}</option>
                <option value="n" {if $f_transaction=='n'}selected='selected'{/if}>{lang_print id=11270064}</option>
              </select>
            </td>
<td><input type='submit' class='button' value='{lang_print id=1002}'></td>
<input type='hidden' name='s' value='{$s}'>
</form>
</tr>
</table>
</div>
</td></tr></table>

<br>

{if $total_badgeassignments == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=11270105}</b></div>
  </td>
  </tr>
  </table>
  <br>

{else}



  <div class='pages'>{lang_sprintf id=11270009 1=$total_badgeassignments} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_badgeassignments.php?s={$s}&p={$pages[page_loop].page}&f_badgeid={$f_badgeid}&f_username={$f_username}&f_approved={$f_approved}&f_epayment={$f_epayment}&f_transaction={$f_transaction}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'><a class='header' href='admin_badgeassignments.php?s=id&p={$p}&f_badgeid={$f_badgeid}&f_username={$f_username}&f_approved={$f_approved}&f_epayment={$f_epayment}&f_transaction={$f_transaction}'>{lang_print id=87}</a></td>
  <td class='header' width='150'><a class='header' href='admin_badgeassignments.php?s=badge&p={$p}&f_badgeid={$f_badgeid}&f_username={$f_username}&f_approved={$f_approved}&f_epayment={$f_epayment}&f_transaction={$f_transaction}'>{lang_print id=11270021}</a></td>
  <td class='header' width='100'><a class='header' href='admin_badgeassignments.php?s=user&p={$p}&f_badgeid={$f_badgeid}&f_username={$f_username}&f_approved={$f_approved}&f_epayment={$f_epayment}&f_transaction={$f_transaction}'>{lang_print id=11270028}</td>
  <td class='header' width='60'>{lang_print id=11270044}</td>
  <td class='header app' width='80'>{lang_print id=11270014}</td>
  <td class='header epay' width='40'>{lang_print id=11270016}</td>
  <td class='header tran' width='100'>{lang_print id=11270023}</td>
  <td class='header' width='100'>{lang_print id=153}</td>
  </tr>
  {section name=badgeassignment_loop loop=$badgeassignments}
    {assign var='badgeassignment' value=$badgeassignments[badgeassignment_loop].badgeassignment}
    {assign var='badge' value=$badgeassignments[badgeassignment_loop].badge}
    {assign var='user' value=$badgeassignments[badgeassignment_loop].user}
    
    <tr class='{cycle values="background1,background2"}'>
    <td class='item'>{$badgeassignment->badgeassignment_info.badgeassignment_id}</td>
    <td class='item'><a href="{$url->url_create('badge', null, $badgeassignment->badgeassignment_info.badgeassignment_badge_id)}" target="_blank">{$badge->badge_info.badge_title}</a></td>
    <td class='item'><a href="{$url->url_create('profile', $user->user_info.user_username)}">{$user->user_info.user_displayname}</a></td>
    <td class='item'>
        {assign var=badgeassignment_datecreated value=$datetime->timezone($badgeassignment->badgeassignment_info.badgeassignment_datecreated, $global_timezone)}
        {$datetime->cdate("`$setting.setting_dateformat`", $badgeassignment_datecreated)}
    </td>
    <td class='item'>
      <a href="admin_badgeassignments.php?task=update_approved&badgeassignment_id={$badgeassignment->badgeassignment_info.badgeassignment_id}&value={if $badgeassignment->badgeassignment_info.badgeassignment_approved}0{else}1{/if}"><img border='0' src='../images/icons/admin_checkbox{if $badgeassignment->badgeassignment_info.badgeassignment_approved}2{else}1{/if}.gif' /></a>
      {if $badgeassignment->badgeassignment_info.badgeassignment_approved}
        {assign var=badgeassignment_dateapproved value=$datetime->timezone($badgeassignment->badgeassignment_info.badgeassignment_dateapproved, $global_timezone)}
        {$datetime->cdate("`$setting.setting_dateformat`", $badgeassignment_dateapproved)}
      {/if}  
    </td>
    <td class='item'><a href="admin_badgeassignments.php?task=update_epayment&badgeassignment_id={$badgeassignment->badgeassignment_info.badgeassignment_id}&value={if $badgeassignment->badgeassignment_info.badgeassignment_epayment}0{else}1{/if}"><img border='0' src='../images/icons/admin_checkbox{if $badgeassignment->badgeassignment_info.badgeassignment_epayment}2{else}1{/if}.gif' /></a></td>
    <td class='item'>
      {if $badgeassignment->badgeassignment_info.epaymenttransaction_id}  
        <a href="javascript:TB_show('{lang_print id=11260069}', 'admin_epayment_transaction_view.php?epaymenttransaction_id={$badgeassignment->badgeassignment_info.epaymenttransaction_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{$badgeassignment->badgeassignment_info.epaymenttransaction_id}</a>
        |
        {$badgeassignment->badgeassignment_info.epaymenttransaction_status} 
        <br>{assign var=epaymenttransaction_createdat value=$datetime->timezone($badgeassignment->badgeassignment_info.epaymenttransaction_createdat, $global_timezone)}
          {$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $epaymenttransaction_createdat)}
      {else}&nbsp;
      {/if} 
      
    </td>
    <td class='item'>
    <a href="{$url->url_create('badgeassignment', null, $badgeassignment->badgeassignment_info.badgeassignment_id)}" target="_blank">{lang_print id=11270168}</a>
    | <a href="admin_badgeassignment_edit.php?badgeassignment_id={$badgeassignment->badgeassignment_info.badgeassignment_id}">{lang_print id=187}</a>
    {*  <a href="javascript:editBadgeassignment('{$badgeassignment->badgeassignment_info.badgeassignment_id}', '{$badgeassignment->badgeassignment_info.badgeassignment_desc|replace:"&#039;":"\&#039;"|replace:"\r\n":"\\r\\n"}');">{lang_print id=187}</a> *}
    | <a href="javascript:void(0);" onClick="confirmDelete('{$badgeassignment->badgeassignment_info.badgeassignment_id}');">{lang_print id=155}</a>
    </td>
    </tr>
  {/section}
  </table>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>

  </td>
  <td align='right' valign='top'>
    <div class='pages2'>{lang_sprintf id=11270009 1=$total_badgeassignments} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_badgeassignments.php?s={$s}&p={$pages[page_loop].page}&f_badgeid={$f_badgeid}&f_username={$f_username}&f_approved={$f_approved}&f_epayment={$f_epayment}&f_transaction={$f_transaction}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}


{include file='admin_footer.tpl'}