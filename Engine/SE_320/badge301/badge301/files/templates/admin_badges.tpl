{include file='admin_header.tpl'}

<h2>{lang_print id=11270133}</h2>
{lang_print id=11270134}
<br />
<br />

{if $is_error}
  <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
{/if}

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=$result}</div>
{/if}


<a href="javascript:createBadge();">{lang_print id=11270035}</a>
|
<a  href="javascript:assignBadge('');">{lang_print id=11270104}</a>


{* JAVASCRIPT FOR CHECK ALL *}
{literal}
<script language='JavaScript'> 
<!---
var badge_id = 0;
function confirmDelete(id) {
  badge_id = id;
  TB_show('{/literal}{lang_print id=11270145}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

function deleteBadge() {
  window.location = 'admin_badges.php?task=deletebadge&badge_id='+badge_id+'&s={/literal}&s={$s}&p={$p}&f_title={$f_title}&f_catid={$f_catid}{literal}';
}

function assignBadge(badge_id) {
	$('assign_badge_id').defaultValue = badge_id;  
	$('assign_badge_id').value = badge_id;  

	TB_show('{/literal}{lang_print id=11270104}{literal}', '#TB_inline?height=200&width=300&inlineId=assign_badge_form', '', '../images/trans.gif');
}

function createBadge() {
	TB_show('{/literal}{lang_print id=11270035}{literal}', '#TB_inline?height=200&width=300&inlineId=create_badge_form', '', '../images/trans.gif');
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


{* HIDDEN DIV TO DISPLAY CREATE BADGE FORM *}
<div style='display: none;' id='create_badge_form'>
<form action="admin_badges.php" method="POST">
  <div style='margin: 10px auto;'>{lang_print id=11270077}</div>
  
  <table cellpadding='0' cellspacing='2'>
    <tr>
      <td align='right' valign='top'>{lang_print id=11270173}:&nbsp;</td>
      <td>
			  <select name='badge_badgecat_id'>
			      <option value="0"></option>
			     {foreach from=$cats item=cat}
			      <option value="{$cat.cat_id}" style='font-weight: bold' {if $f_catid == $cat.cat_id} selected='selected' {/if}>{lang_print id=$cat.cat_title}</option>
			      {if !empty($cat.subcats)}
			      {foreach from=$cat.subcats item=subcat}
			        <option value="{$subcat.subcat_id}" {if $f_catid == $subcat.subcat_id} selected='selected' {/if}>&nbsp &nbsp &raquo; &nbsp &nbsp {lang_print id=$subcat.subcat_title}</option>
			      {/foreach}
			      {/if}
			     {/foreach}
			  </select>
      </td>
    </tr>  
    <tr>
      <td align='right' valign='top'>{lang_print id=11270087}:&nbsp;</td>
      <td><input type="text" name="badge_title" class="text" size="24" /></td>
    </tr>
  </table>
  <br />  
  
  <input type="submit" value="{lang_print id=11270035}" class="button" />
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type="hidden" name="task" value="create" />
</form>
</div>

{* HIDDEN DIV TO DISPLAY ASSIGN BADGE FORM *}
<div style='display: none;' id='assign_badge_form'>
<form action="admin_badges.php" method="POST">
  <div style='margin: 10px auto;'>{lang_print id=11270115}</div>
  
  <table cellpadding='0' cellspacing='2'>
    <tr>
      <td align='right' valign='top'>{lang_print id=11270080}:&nbsp;</td>
      <td><input type="text" name="badge_id" id="assign_badge_id" class="text" size="20" /></td>
    </tr>  
    <tr>
      <td align='right' valign='top'>{lang_print id=11270083}:&nbsp;</td>
      <td><input type="text" name="username" class="text" size="20" /></td>
    </tr>
  </table>
  <br />  
  
  <input type="submit" value="{lang_print id=11270084}" class="button" />
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type="hidden" name="task" value="assign" />
</form>
</div>

<br>
<br>

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='1' cellspacing='0' align='center'>
<form action='admin_badges.php' method='POST'>
<tr>
<td>{lang_print id=11270087}<br><input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='50'></td>

<td>{lang_print id=11270173}<br>
  <select name='f_catid'><option value=''></option>

     {foreach from=$cats item=cat}
      <option value="{$cat.cat_id}" style='font-weight: bold' {if $f_catid == $cat.cat_id} selected='selected' {/if}>{lang_print id=$cat.cat_title}</option>
      {if !empty($cat.subcats)}
      {foreach from=$cat.subcats item=subcat}
        <option value="{$subcat.subcat_id}" {if $f_catid == $subcat.subcat_id} selected='selected' {/if}>&nbsp &nbsp &raquo; &nbsp &nbsp {lang_print id=$subcat.subcat_title}</option>
      {/foreach}
      {/if}
     {/foreach}
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

{if $total_badges == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=11270089}</b></div>
  </td>
  </tr>
  </table>
  <br>

{else}



  <div class='pages'>{lang_sprintf id=11270090 1=$total_badges} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_badges.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_catid={$f_catid}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'>B&amp;U</td>
  <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_badges.php?s=id&p={$p}&f_title={$f_title}&f_catid={$f_catid}'>{lang_print id=87}</a></td>
  <td class='header' width='200'><a class='header' href='admin_badges.php?s=title&p={$p}&f_title={$f_title}&f_catid={$f_catid}'>{lang_print id=11270087}</a></td>
  <td class='header' width='60'><a class='header' href='admin_badges.php?s=member&p={$p}&f_title={$f_title}&f_catid={$f_catid}'>{lang_print id=11270169}</a></td>
  <td class='header' width='60'>{lang_print id=11270018}</td>
  <td class='header' width='40'>{lang_print id=11270016}</td>
  <td class='header' width='40'>{lang_print id=11270014}</td>
  <td class='header' width='40'>{lang_print id=11270183}</td>
  <td class='header' width='40'>{lang_print id=11270007}</td>
  <td class='header' width='60'>{lang_print id=153}</td>
  </tr>
  {section name=badge_loop loop=$badges}
    {assign var='badge' value=$badges[badge_loop].badge}
    
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-right: 0px;'><input type='radio' name='badge_id' value='{$badge->badge_info.badge_id}' onclick="assignBadge({$badge->badge_info.badge_id});"></td>
    <td class='item' style='padding-left: 0px;'>{$badge->badge_info.badge_id}</td>
    <td class='item'><a href="{$url->url_create('badge', null, $badge->badge_info.badge_id)}" target="_blank">{$badge->badge_info.badge_title}</a></td>
    <td class='item'><a href="admin_badgeassignments.php?f_badgeid={$badge->badge_info.badge_id}">{lang_sprintf id=11270091 1=$badges[badge_loop].total_assignments}</a></td>
    <td class='item'>{$setting.setting_epayment_currency_symbol}{$badge->badge_info.badge_cost}</td>
    <td class='item'><img border='0' src='../images/icons/admin_checkbox{if $badge->badge_info.badge_epayment}2{else}1{/if}.gif' /></td>
    <td class='item'><img border='0' src='../images/icons/admin_checkbox{if $badge->badge_info.badge_approved}2{else}1{/if}.gif' /></td>
    <td class='item'><img border='0' src='../images/icons/admin_checkbox{if $badge->badge_info.badge_search}2{else}1{/if}.gif' /></td>
    <td class='item'><img border='0' src='../images/icons/admin_checkbox{if $badge->badge_info.badge_enabled}2{else}1{/if}.gif' /></td>
    <td class='item'>
      <a href='admin_badge_edit.php?badge_id={$badge->badge_info.badge_id}'>{lang_print id=187}</a>
    | <a href="javascript:void(0);" onClick="confirmDelete('{$badge->badge_info.badge_id}');">{lang_print id=155}</a>
    </td>
    </tr>
  {/section}
  </table>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>

  </td>
  <td align='right' valign='top'>
    <div class='pages2'>{lang_sprintf id=11270090 1=$total_badges} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_badges.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_catid={$f_catid}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}


{include file='admin_footer.tpl'}