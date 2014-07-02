{include file='header.tpl'}































<img src='./images/icons/gstore_management.png' border='0' class='icon_big' style="margin-bottom: 15px;">







<div class='page_header'>{lang_print id=5555068}</div>







<div>







  {lang_print id=5555069}







</div>







<br />















{if !empty($paypal_email)}







{* SHOW BUTTONS *}







<div style='margin-top: 20px;'>







  <div class='button' style='float: left;'>







    <a href='user_gstore_listing.php'><img src='./images/icons/gstore_post16.gif' border='0' class='button' />{lang_print id=5555065}</a>







  </div>







  <div class='button' style='float: left; padding-left: 20px;'>







    <a href='user_gstore_settings.php'><img src='./images/icons/gstore_settings16.gif' border='0' class='button' />{lang_print id=5555066}</a>







  </div>







  <div class='button' style='float: left; padding-left: 20px;'>







    <a href="javascript:void(0);" onclick="$('gstore_search').style.display = ( $('gstore_search').style.display=='block' ? 'none' : 'block');this.blur();"><img src='./images/icons/search16.gif' border='0' class='button' />{lang_print id=5555067}</a>







  </div>







  <div style='clear: both; height: 0px;'></div>







</div>







{/if}























{* SEARCH FIELD *}







<div id='gstore_search' class="segstoreSearch" style='margin-top: 10px;{if empty($search)} display: none;{/if}'>







  <div style='padding: 10px;'>







    <form action='user_gstore.php' name='searchform' method='post'>







    <table cellpadding='0' cellspacing='0' align='center'>







    <tr>







    <td><b>{lang_print id=1500049}</b>&nbsp;&nbsp;</td>







    <td><input type='text' name='search' maxlength='100' size='30' value='{$search}' />&nbsp;</td>







    <td>{lang_block id=646 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}</td>







    </tr>







    </table>







    <input type='hidden' name='s' value='{$s}' />







    <input type='hidden' name='p' value='{$p}' />







    </form>







  </div>







</div>























{* JAVASCRIPT *}







{lang_javascript ids=861,5555121,5555123}







<script type="text/javascript" src="./include/js/class_gstore.js"></script>







<script type="text/javascript">







  







  SocialEngine.gstore = new SocialEngineAPI.gstore();







  SocialEngine.RegisterModule(SocialEngine.gstore);







  







</script>























{* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}







<div style='display: none;' id='confirmgstoredelete'>







  <div style='margin-top: 10px;'>







    {lang_print id=5555122 1=user_gstore_settings.php}







  </div>







  <br />







  {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();parent.SocialEngine.gstore.deletegstoreConfirm();' />{/lang_block}







  {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}







</div>























{* DISPLAY MESSAGE IF NO gstore ENTRIES *}







<div id="segstoreNullMessage"{if $total_gstores} style="display: none;"{/if}>







  <table cellpadding='0' cellspacing='0' align='center'>







    <tr>







      <td class='result'>







	    {if empty($paypal_email)}







		<img src='./images/icons/bulb16.gif' border='0' class='icon' />







		{lang_sprintf id=5555071 1='user_gstore_listing.php'}







        {elseif !empty($search)}







          <img src='./images/icons/bulb16.gif' border='0' class='icon' />







          {lang_print id=5555070}







        {else}







          <img src='./images/icons/bulb16.gif' border='0' class='icon' />







          You have not posted any items any items yet..!<br /><a href="user_gstore_listing.php">Get Started Here</a>







        {/if}







      </td>







    </tr>







  </table>







</div>























{* DISPLAY PAGINATION MENU IF APPLICABLE *}







{if $maxpage > 1}







  <div class='center'>







    {if $p != 1}







      <a href='user_gstore.php?search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>







    {else}







      <font class='disabled'>&#171; {lang_print id=182}</font>







    {/if}







    {if $p_start == $p_end}







      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_gstores} &nbsp;|&nbsp; 







    {else}







      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_gstores} &nbsp;|&nbsp; 







    {/if}







    {if $p != $maxpage}







      <a href='user_gstore.php?search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>







    {else}







      <font class='disabled'>{lang_print id=183} &#187;</font>







    {/if}







  </div>







  <br />







{/if}























{* DISPLAY gstore LISTINGS *}







{section name=gstore_loop loop=$gstores}







<div id='segstore_{$gstores[gstore_loop].gstore->gstore_info.gstore_id}' class="segstore {cycle values='segstore1,segstore2'}">















  <table cellpadding='0' cellspacing='0' width='100%'>







    <tr>







      <td class='segstoreLeft' width='1'>







        <div class='segstorePhoto' style='width: 140px;'>







          <table cellpadding='0' cellspacing='0' width='140'>







            <tr>







              <td>







                <a href='{$url->url_create("gstore", $user->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>







                  <img src='{$gstores[gstore_loop].gstore->gstore_photo("./images/nophoto.gif")}' border='0' width='{$misc->photo_size($gstores[gstore_loop].gstore->gstore_photo("./images/nophoto.gif"),"140","140","w")}' />







                </a>







              </td>







            </tr>







          </table>







        </div>







      </td>







      <td class='segstoreRight' width='100%'>







      







        {* SHOW gstore TITLE *}







        <div class='segstoreTitle'>







          {if !$gstores[gstore_loop].gstore->gstore_info.gstore_title}<i>{lang_print id=589}</i>{else}{$gstores[gstore_loop].gstore->gstore_info.gstore_title|truncate:70:"...":false|choptext:40:"<br>"}{/if}







        </div>







        







        {* SHOW gstore CATEGORY *}







        {if !empty($gstores[gstore_loop].gstore->gstore_info.main_category_title)}







        <div class='segstoreCategory'>







          {lang_print id=5555058}







          {* SHOW PARENT CATEGORY *}







          {if !empty($gstores[gstore_loop].gstore->gstore_info.parent_category_title)}







            <a href="browse_gstores.php?gstorecat_id={$gstores[gstore_loop].gstore->gstore_info.parent_category_id}">{lang_print id=$gstores[gstore_loop].gstore->gstore_info.parent_category_title}</a>







            -







          {/if}







          <a href="browse_gstores.php?gstorecat_id={$gstores[gstore_loop].gstore->gstore_info.main_category_id}">{lang_print id=$gstores[gstore_loop].gstore->gstore_info.main_category_title}</a>







        </div>







        {/if}







        







        {* SHOW gstore STATS *}







        <div class='segstoreStats'>







          {assign var='gstore_datecreated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_date)}







          {capture assign="created"}{lang_sprintf id=$gstore_datecreated[0] 1=$gstore_datecreated[1]}{/capture}







          {assign var='gstore_dateupdated' value=$datetime->time_since($gstores[gstore_loop].gstore->gstore_info.gstore_dateupdated)}







          {capture assign="updated"}{lang_sprintf id=$gstore_dateupdated[0] 1=$gstore_dateupdated[1]}{/capture}







          







          {lang_sprintf id=5555072 1=$gstores[gstore_loop].gstore->gstore_info.gstore_views}







          - {lang_sprintf id=507 1=$gstores[gstore_loop].gstore->gstore_info.total_comments}







          - {lang_sprintf id=5555135 1=$created}







          {if $gstores[gstore_loop].gstore->gstore_info.gstore_dateupdated && $created!=$updated}







            - {lang_sprintf id=5555136 1=$updated}







          {/if}







        </div>







        











		



		



		



		{* SHOW sales details *}



		<div style="padding:15px 0px 5px 0px; border-bottom:1px solid #cccccc;"><b>Sales Details:</b></div>



				<table width="100%" border="0" cellspacing="5" cellpadding="0">



  <tr>



    <td align="left">



	Unit Price: <span class="segstoreLargePrice">{lang_print id=$setting.gstore_currency}{$gstores[gstore_loop].gstore->gstore_info.gstore_price}</span>



	</td>



    <td align="right">



	Total item sales: <b>({$gstores[gstore_loop].gstore->gstore_info.item_sales})</b>



	</td>



  </tr>



  <tr>



    <td align="left">



	Item id#:<b>({$gstores[gstore_loop].gstore->gstore_info.gstore_id})</b>



	</td>



    <td align="right">



	{if $gstores[gstore_loop].gstore->gstore_info.gstore_stock > 0} Number of items left in stock:<b>({$gstores[gstore_loop].gstore->gstore_info.gstore_stock})</b>{else}<b>(<span style="color:red;">out of stock</span>)</b>{/if}



	</td>



  </tr>



    <tr>



    <td align="left">&nbsp;



	



	</td>



    <td align="right">



        	<form action='user_gstore.php' method='post'>



			Update your stock levels: 



			<input type='text' class='text' name='stock'  maxlength='10' size='2'>



			



			<input type="hidden" name='id' value='{$gstores[gstore_loop].gstore->gstore_info.gstore_id}' maxlength='10' size='2'>



			



			 <input type='submit' class='button' value='Update Stock' style="font-size:10px;" />



			 </form>



	</td>



  </tr>



</table>



		



		







        







        {* SHOW gstore OPTIONS *}







        <div class='segstoreOptions'>







          {* VIEW *}







          <div class="segstoreOption1">







            <a href='{$url->url_create("gstore", $user->user_info.user_username, $gstores[gstore_loop].gstore->gstore_info.gstore_id)}'>







              <img src='./images/icons/gstore_gstore16.gif' border='0' class='button' />







              {lang_print id=5555073}







            </a>







          </div>







          







          {* EDIT *}







          <div class="segstoreOption2">







            <a href='user_gstore_listing.php?gstore_id={$gstores[gstore_loop].gstore->gstore_info.gstore_id}'>







              <img src='./images/icons/gstore_edit16.gif' border='0' class='button' />







              {lang_print id=5555074}







            </a>







          </div>







          







          {* MEDIA *}







          <div class="segstoreOption2">







            <a href='user_gstore_media.php?gstore_id={$gstores[gstore_loop].gstore->gstore_info.gstore_id}'>







              <img src='./images/icons/gstore_editmedia16.gif' border='0' class='button' />







              {lang_print id=5555075}







            </a>







          </div>







          







          {* DELETE *}







          <div class="segstoreOption2">







            <a href='javascript:void(0);' onclick="SocialEngine.gstore.deletegstore({$gstores[gstore_loop].gstore->gstore_info.gstore_id});">







              <img src='./images/icons/gstore_delete16.gif' border='0' class='button' />







              {lang_print id=5555076}







            </a>







          </div>







        </div>



		



		











		



		



		



		







      </td>







    </tr>







  </table>















  







</div>







{/section}















<div style='clear: both; height: 0px;'></div>































{* DISPLAY PAGINATION MENU IF APPLICABLE *}







{if $maxpage > 1}







  <div class='center'>







    {if $p != 1}







      <a href='user_gstore.php?search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>







    {else}







      <font class='disabled'>&#171; {lang_print id=182}</font>







    {/if}







    {if $p_start == $p_end}







      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_gstores} &nbsp;|&nbsp; 







    {else}







      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_gstores} &nbsp;|&nbsp; 







    {/if}







    {if $p != $maxpage}







      <a href='user_gstore.php?search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>







    {else}







      <font class='disabled'>{lang_print id=183} &#187;</font>







    {/if}







  </div>







  <br />







{/if}















{include file='footer.tpl'}