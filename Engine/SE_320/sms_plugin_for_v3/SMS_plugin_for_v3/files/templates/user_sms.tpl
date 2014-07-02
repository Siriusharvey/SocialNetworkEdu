{include file='admin_header.tpl'}

{* $Id: admin_viewreports.tpl 8 2009-01-11 06:02:53Z nico-izo $ *}

<h2>User SMS Settings</h2>

<br />
<br />

<table cellpadding='0' cellspacing='0' width='400' align='center'>
 <tr>
 <form name="f1" method="post">
  <td colspan="6" align="center">Search USer:<input type="text" name="search_user"  />&nbsp;<input type="submit" value="Search" name="Search" /></td>
  </form>
  </tr>
  <tr><td>&nbsp;</td></tr>
  </table>

<div class='pages'>{lang_sprintf id=1004 1=$total_users} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='user_sms.php?s={$s}&p={$pages[page_loop].page}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_enabled={$f_enabled}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

<div class='box'>
  <table width="100%" cellpadding='0' cellspacing='0' align='center'>
 {if $msg !=""}
  <tr>
  <td colspan="6" style="color:#FF0000" align="center">{$msg}</td>
  </tr>
  {/if}
  
  <tr class='header' width='10' style='padding-left: 0px;'>
  <td  width="10%" align="center" class='header' width='10' style='padding-left: 0px;'>ID </td>
  <td  width="30%" align="center" class='header' width='10' style='padding-left: 0px;'> UserName </td>
  <td  width="20%" align="center" class='header' width='10' style='padding-left: 0px;'>Email </td>
   <td  width="20%" align="center" class='header' width='10' style='padding-left: 0px;'>SMS Credit </td>
   <td  width="20%" align="center" class='header' width='10' style='padding-left: 0px;'>Update </td>
    </tr>
  
{foreach from=$view_arr item=view_arr name=num}
<form method="post" name="sms{$smarty.foreach.num.iteration}">
  <tr class='{cycle values="background1,background2"}'>
  <td class='item' width="10%" align="center" style='padding-right: 0px;'>{$view_arr.id}</td>
  <td class='item'width="30%" align="center" style='padding-right: 0px;'>{$view_arr.username}  </td>
  <td class='item'  width="20%" align="center" style='padding-right: 0px;'>{$view_arr.email} </td>
  <td class='item' width="20%" align="center" style='padding-right: 0px;'>
  
  <input type="text" name="ssms_credits" value="{$view_arr.ssms_credits}" size="6" > 
  </td>
  <td class='item' width="20%" align="center" style='padding-right: 0px;'><a href="javascript:document.sms{$smarty.foreach.num.iteration}.submit();">Update</a>
   <input type="hidden" name="submit1" value="{$view_arr.id}"> 
  
   </td>
  
  </tr> </form>
  {foreachelse}
  <tr><td colspan="5" align="center" ><strong>No User Found</strong></td></tr>
  
{/foreach}
   
  </table>
</div>
<div class='pages2'>{lang_sprintf id=1004 1=$total_users} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='user_sms.php?s={$s}&p={$pages[page_loop].page}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_enabled={$f_enabled}'>{$pages[page_loop].page}</a>{/if} {/section}</div>



{include file='admin_footer.tpl'}