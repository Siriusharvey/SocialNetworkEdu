{include file='header.tpl'}

<div class='page_header'>

  {lang_sprintf id=5555056 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username) 3=$gstore_id}

</div>



    



{* JAVASCRIPT *}

{lang_javascript ids=861,5555121,5555123,5555142}

<script type="text/javascript" src="./include/js/class_gstore.js"></script>

<script type="text/javascript">

  SocialEngine.gstore = new SocialEngineAPI.gstore();

  SocialEngine.RegisterModule(SocialEngine.gstore);

</script>

{if isset($page_is_preview)}<table cellspacing='0' cellpadding='0' id='gstorepreview' style='width:100%'><tr><td>&nbsp;</td><td class='content' style='width:100%'>{/if}







{* SHOW THIS ENTRY *}

<div class='segstoreListing'>

  <table cellpadding='0' cellspacing='0' width='100%'>

    <tr>

      {assign var=gstore_photo value=$gstore->gstore_photo("./images/nophoto.gif")}

      {assign var=gstore_thumb value=$gstore->gstore_photo("./images/nophoto.gif", TRUE)}

      <td width="165px" class='segstoreLeft' style="border-right:1px solid #cccccc; padding-right:10px;">

	  

        <div class="segstorePhoto" style="width:145px;">

        {if $gstore_photo!="./images/nophoto.gif" && $gstore_photo!=$gstore_thumb}

          <a href="javascript:void(0);" class="segstorePhotoLink" onclick="SocialEngine.gstore.imagePreviewgstore('{$gstore_photo}', {$files[file_loop].gstoremedia_width|default:0}, {$files[file_loop].gstoremedia_height|default:0});">

            <img src='{$gstore_photo}' border='0' width='{$misc->photo_size($gstore_photo,"140","140","w")}' />

          </a>

        {else}

          <img src='{$gstore_photo}' border='0' width='{$misc->photo_size($gstore_photo,"140","140","w")}' />

        {/if}

        </div>

		

		<div style="clear:both"></div>

		

		{* SHOW PURCHACE DETAILS *}

		<div style="padding-top:5px;">

		<div style="font-size:12px; font-weight:bold; padding-bottom:5px;">{lang_print id=5555175}</div>

		

		Price: <span class="segstoreLargePrice">{lang_print id=$setting.gstore_currency}{$gstore->gstore_info.gstore_price}</span>

		</div>

		<div style="font-size:10px; padding-top:5px; white-space:nowrap;"><b>[{$item_sales}]</b> {lang_print id=5555176}</div>

		

		<div style="font-size:10px; padding-top:4px;">

		{if $gstore->gstore_info.gstore_stock > 0} <b>({$gstore->gstore_info.gstore_stock})</b> 

		{lang_print id=5555177}{else}<b>(<span style="color:red;">out of stock</span>)</b>{/if}

		</div>

		

		<br />

		

       <div align="left" style="white-space:nowrap;">

	 {if $gstore->gstore_info.gstore_privacy > 62}

	   {if $gstore->gstore_info.gstore_stock > 0}

		<form action="paypal_shipping.php" method="post" >

				<input type="hidden" name="business" value="{$paypal_email}">

				<input type="hidden" name="gstore_settings_user_id" value="{$owner->user_info.user_id}">

				<input type="hidden" name="apply_shipping_charges" value="{$gstore->gstore_info.apply_shipping_charges}">

				<input type="hidden" name="band_a" value="{$gstore->gstore_info.band_a_charge}">

				<input type="hidden" name="band_b" value="{$gstore->gstore_info.band_b_charge}">

				<input type="hidden" name="band_c" value="{$gstore->gstore_info.band_c_charge}">

				<input type="hidden" name="band_d" value="{$gstore->gstore_info.band_d_charge}">

				<input type="hidden" name="stockinhand" value="{$stockinhand}">

				<input type="hidden" name="seller_sales" value="{$seller_sales}">

				<input type="hidden" name="stock" value="{$gstore->gstore_info.gstore_stock}">

				<input type="hidden" name="item_id" value="{$gstore_id}">

				<input type="hidden" name="item_name" value="{$item_title}">

				<input type="hidden" name="item_sales" value="{$item_sales}">

				<input type="hidden" name="amount" value="{$price}">  

				<span style="vertical-align:middle;">{lang_print id=5555178}:<br />

				<input type="text" size="2" name="qty" value="1" ></span>  

				<input type='submit' class='button' value='{lang_print id=5555179}' name="bounce" alt="PayPal - The safer, easier way to pay online!" /><br />

            <img src="../images/icons/buy_now_button.png" border="0" alt="" style="padding-top:5px;">

        </form>

		{else}

		<form action="browse_gstores.php" method="post" style="white-space:nowrap;" > 

		<span style="vertical-align:middle;">{lang_print id=5555178}:<br />

				<input type="text" size="2" name="qty" value="1" ></span>  

				<input type='submit' class='button' value='{lang_print id=5555174}' name="bounce" alt="PayPal - The safer, easier way to pay online!" /><br />

            <img src="../images/icons/buy_now_button.png" border="0" alt="" style="padding-top:5px;">

        </form>			

		{/if}

						

		

	{else}

		<form action="login.php" method="post" style="white-space:nowrap;" >

		<span style="vertical-align:middle;">{lang_print id=5555178}:<br />

				<input type="text" size="2" name="qty" value="1" ></span>

				<input type='submit' class='button' value='Login' name="bounce" alt="PayPal - The safer, easier way to pay online!" /><br /> 

            <img src="../images/icons/buy_now_button.png" border="0" alt="" style="padding-top:5px;">

        </form>

	{/if}

	   </div>

	   

	   

	   

	   {* SHOW DELIVERY DETAILS *}

	   

	   	  

	   <div style="font-size:12px; font-weight:bold; padding-top:12px; padding-bottom:3px; white-space:nowrap;">Delivery Charges:</div>

	   

	    {if $gstore->gstore_info.apply_shipping_charges != "No shipping"}

	   

	   <table width="100%" border="1" cellspacing="0" cellpadding="3">

  <tr>

    <td colspan="2" align="left" style="background-color:#eeeeee;"><b>{$gstore->gstore_info.apply_shipping_charges}:</b></td>

  </tr>

  {if $gstore->gstore_info.band_a_charge != ""}

  <tr>

    <td>({lang_print id=$setting.gstore_currency}{$gstore->gstore_info.band_a_charge})</td>

    <td>{$setting.gstore_band_a}</td>

  </tr>

  {/if}

  {if $gstore->gstore_info.band_b_charge != ""}

  <tr>

    <td>({lang_print id=$setting.gstore_currency}{$gstore->gstore_info.band_b_charge})</td>

    <td>{$setting.gstore_band_b}</td>

  </tr>

  {/if}

  {if $gstore->gstore_info.band_c_charge != ""}

  <tr>

    <td>({lang_print id=$setting.gstore_currency}{$gstore->gstore_info.band_c_charge})</td>

    <td>{$setting.gstore_band_c}</td>

  </tr>

  {/if}

  {if $gstore->gstore_info.band_d_charge != ""}

  <tr>

    <td>({lang_print id=$setting.gstore_currency}{$gstore->gstore_info.band_d_charge})</td>

    <td>&nbsp;{$setting.gstore_band_d}</td>

  </tr>

  {/if}

</table>

{else}

<div align="left">(<span style=" color:#006600;">Item delivery free</span>)</div><br />

	   {/if}

	   

      </td>

	  

	  

	  

	  

	  

	  

	  

      <td class='segstoreRight' width='100%' valign="top" style="padding-left:15px;">

			<div class="segstoreTitle">

				{if !$gstore->gstore_info.gstore_title}<i>{lang_print id=589}</i>{else}{$gstore->gstore_info.gstore_title|truncate:75:"...":true}{/if}

			</div>

        <div class='segstoreStats'>

          {assign var="gstore_datecreated" value=$datetime->time_since($gstore->gstore_info.gstore_date)}

          {capture assign="datecreated"}{lang_sprintf id=$gstore_datecreated[0] 1=$gstore_datecreated[1]}{/capture}

          {lang_sprintf id=5555057 1=$datecreated}

		  

		            {lang_sprintf id=5555072 1=$gstore->gstore_info.gstore_views}

          - {lang_sprintf id=507 1=$gstore->gstore_info.total_comments}

        </div>

        {* SHOW ENTRY CATEGORY *}

        {if $cat_info.gstorecat_title != ""}

          <div class='segstoreCategory'>

            {lang_sprintf id=5555058 1=$cat_info.gstorecat_title 2="browse_gstores.php?gstorecat_id=`$gstore->gstore_info.gstore_gstorecat_id`"}

          </div>

        {/if}

        {* SHOW gstore FIELDS *}

        <div class='segstoreFields'>

          {section name=cat_loop loop=$cats}

          <table cellpadding='0' cellspacing='0'>

          {section name=field_loop loop=$cats[cat_loop].fields}

            <tr>

              <td valign='top' style='padding-right: 10px;' nowrap='nowrap'>

                {lang_print id=$cats[cat_loop].fields[field_loop].field_title}:

              </td>

			  

              <td>

              <div class='profile_field_value'>{$cats[cat_loop].fields[field_loop].field_value_formatted}</div>

              {*

              <div class='profile_field_value'>{if $cats[cat_loop].fields[field_loop].field_format}{$cats[cat_loop].fields[field_loop].field_value|string_format:$cats[cat_loop].fields[field_loop].field_format}{else}{$cats[cat_loop].fields[field_loop].field_value}{/if}</div>

                {if $cats[cat_loop].fields[field_loop].field_special == 1 && $cats[cat_loop].fields[field_loop].field_value|substr:0:4 != "0000"} ({lang_sprintf id=852 1=$datetime->age($cats[cat_loop].fields[field_loop].field_value)}){/if}

              *}

              </td>

            </tr>

          {/section}

          </table>

          {/section}

        </div>

        <div class='segstoreBody'>

          {$gstore->gstore_info.gstore_body|choptext:75:"<br />"}

        </div>

<br />

{if $total_files>0}<br />Click images below to enlarge:{/if}

<table width="100%" border="0" cellpadding="0" cellspacing="0">

  <tr>

    <td width="73%">

	        {* SHOW FILES IN THIS ALBUM *}

        {section name=file_loop loop=$files max=9}

          {* IF IMAGE, GET THUMBNAIL *}

          {if $files[file_loop].gstoremedia_ext == "jpeg" OR $files[file_loop].gstoremedia_ext == "jpg" OR $files[file_loop].gstoremedia_ext == "gif" OR $files[file_loop].gstoremedia_ext == "png" OR $files[file_loop].gstoremedia_ext == "bmp"}

            {assign var='file_dir' value=$gstore->gstore_dir($gstore->gstore_info.gstore_id)}

            {assign var='file_src_full' value="`$file_dir``$files[file_loop].gstoremedia_id`.`$files[file_loop].gstoremedia_ext`"}

            {assign var='file_src' value="`$file_dir``$files[file_loop].gstoremedia_id`_thumb.jpg"}

          {* SET THUMB PATH FOR AUDIO *}

          {elseif $files[file_loop].gstoremedia_ext == "mp3" OR $files[file_loop].gstoremedia_ext == "mp4" OR $files[file_loop].gstoremedia_ext == "wav"}

      {assign var='file_src' value='./images/icons/audio_big.gif'}

          {* SET THUMB PATH FOR VIDEO *}

          {elseif $files[file_loop].gstoremedia_ext == "mpeg" OR $files[file_loop].gstoremedia_ext == "mpg" OR $files[file_loop].gstoremedia_ext == "mpa" OR $files[file_loop].gstoremedia_ext == "avi" OR $files[file_loop].gstoremedia_ext == "swf" OR $files[file_loop].gstoremedia_ext == "mov" OR $files[file_loop].gstoremedia_ext == "ram" OR $files[file_loop].gstoremedia_ext == "rm"}

            {assign var='file_src' value='./images/icons/video_big.gif'}

          {* SET THUMB PATH FOR UNKNOWN *}

          {else}

            {assign var='file_src' value='./images/icons/file_big.gif'}

          {/if}

          {* START NEW ROW *}

          {cycle name="startrow" values="<table cellpadding='0' cellspacing='0'><tr>,,"}

          {* SHOW THUMBNAIL *}

          <td style='padding: 5px 20px 5px 0px; text-align: center; vertical-align: middle;'>

            {$files[file_loop].gstoremedia_title|truncate:20:"...":true}

            <div class='album_thumb2' style='text-align: center; vertical-align: middle;'>

              <a href="javascript:void(0);" class="segstorePhotoLink" onclick="SocialEngine.gstore.imagePreviewgstore('{$file_src_full}', {$files[file_loop].gstoremedia_width|default:0}, {$files[file_loop].gstoremedia_height|default:0});">

                <img src='{$file_src}' border='0'  width='{$misc->photo_size($file_src,"300","240","w")}' class='photo' />

              </a>

            </div>

          </td>

          {* END ROW AFTER 5 RESULTS *}

          {if $smarty.section.file_loop.last == true}

            </tr></table>

          {else}

            {cycle name="endrow" values=",,</tr></table>"}

          {/if}

          

        {/section}

	</td>

	

	

	

	

	

    <td valign="bottom">

<div  style="border:1px solid #cccccc; padding:10px; margin-bottom:8px;">

	<div style="position:relative;"><div style=" position:absolute; top:-18px; left:0px; white-space:nowrap; background-color:#f3f3f3; padding:0px 6px 0px 5px; width:51px;">{lang_print id=5555180}:</div></div>

	{lang_print id=5555181}: <a href="{$url->url_create("profile", $owner->user_info.user_username)}"><b>{$owner->user_displayname|truncate:20:"...":true}</b></a>

	<div style="padding-bottom:5px;">{lang_print id=5555182}:&nbsp; <b>[{$seller_sales}] {lang_print id=5555183}</b></div>

	{* BEGIN RATING *}

<table cellpadding='0' cellspacing='0' width='100%'>

<tr>

<td class='profile' align='center'>

<iframe name='rateframe' id='rateframe' src="{$url->url_base}/rate.php?object_table=se_users&object_primary=user_id&object_id={$owner->user_info.user_id}" scrolling='no' frameborder='0' style='width:120px;height:50px;'></iframe>

</td>

</tr>

</table>

{* END RATING *}

</div>

	</td>

  </tr>

</table>

        

		

        

		

											

        

        

      </td>

    </tr>

  </table>

</div>

<br />

<div style='margin-bottom: 20px;'>



  <div class='button' style='float: left;'>

  <a href='browse_gstores.php'><img src='./images/icons/back16.gif' border='0' class='button' />Back to store front</a>

  </div>

  

  <div class='button' style='float: left; padding-left: 20px;'>

    <a href="javascript:TB_show(SocialEngine.Language.Translate(861), 'user_report.php?return_url={$url->url_current()}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/report16.gif' border='0' class='button'>{lang_print id=861}</a>

  </div>

  

    <div class='button' style='float: left; padding-left: 20px;'>

    <a href='{$url->url_create("gstores", $owner->user_info.user_username)}'><img src='./images/icons/back16.gif' border='0' class='button' />{lang_sprintf id=5555059 1=$owner->user_displayname}</a>

  </div>

  

  <div style='clear: both; height: 0px;'></div>

</div>

<br />

  {* POPULAP ITEMS FROM THIS SELLER *}

  <div class="header">{lang_print id=5555191}</div>

  <div class="portal_content" style="padding-right:none; background-color:#f8f8f8;">

  {section name=other_items_loop loop=$other_items max=4}

  

    <div style='padding: 10px; float:left; border: 1px solid #CCCCCC; margin-right: 10px; width:190px; background-color:#FFFFFF;'>

      <table cellpadding='0' cellspacing='0'>

        <tr>

          <td>

            <a href='{$url->url_create("gstore", $other_items[other_items_loop].gstore_author->user_info.user_username, $other_items[other_items_loop].gstore->gstore_info.gstore_id)}'>

              <img src='{$other_items[other_items_loop].gstore->gstore_photo("./images/nophoto.gif", TRUE)}' border='0' width='40' height='40' />

            </a>

          </td>

          <td style='vertical-align: top; padding-left: 10px;'>

            <div style='font-weight: bold; font-size: 10pt;'>

              <a href='{$url->url_create("gstore", $other_items[other_items_loop].gstore_author->user_info.user_username, $other_items[other_items_loop].gstore->gstore_info.gstore_id)}'>

                {$other_items[other_items_loop].gstore->gstore_info.gstore_title|truncate:15:"...":true}

              </a>

            </div>

			

			 <div style='margin-bottom: 5px;'>

                Price: <span class="segstoreLargePrice" style="font-size:10px;">{lang_print id=$setting.gstore_currency}{$other_items[other_items_loop].gstore->gstore_info.gstore_price}</span>

            </div>

			

          </td>

        </tr>

      </table>

    </div>

  {/section}

  <div style="clear:both;"></div>

  </div>

  

  <br />

{* COMMENTS *}

<div id="gstore_{$gstore->gstore_info.gstore_id}_postcomment"></div>

<div id="gstore_{$gstore->gstore_info.gstore_id}_comments" style='margin-left: auto; margin-right: auto;'></div>

{lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}

<script type="text/javascript">

  

  SocialEngine.gstoreComments = new SocialEngineAPI.Comments({ldelim}

    'canComment' : {if $allowed_to_comment}true{else}false{/if},

    'commentHTML' : '{$setting.setting_comment_html}',

    'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},

    

    'type' : 'gstore',

    'typeIdentifier' : 'gstore_id',

    'typeID' : {$gstore->gstore_info.gstore_id},

    'typeTab' : 'gstores',

    'typeCol' : 'gstore',

    

    'initialTotal' : {$total_comments|default:0},

    'paginate' : true,

    'cpp' : 5

  {rdelim});

  

  SocialEngine.RegisterModule(SocialEngine.gstoreComments);

  

  // Backwards

  function addComment(is_error, comment_body, comment_date)

  {ldelim}

    SocialEngine.gstoreComments.addComment(is_error, comment_body, comment_date);

  {rdelim}

  

  function getComments(direction)

  {ldelim}

    SocialEngine.gstoreComments.getComments(direction);

  {rdelim}

  

</script>

<div style="width:1px; height:1px; visibility: hidden; overflow:hidden;" id="segstoreImagePreview">

  <table cellpadding='0' cellspacing='0'  style="width: 100%; height: 100%; padding-top: 5px;"><tr>

    <td valign="middle" align="center"><img id="segstoreImageFull" src="./images/icons/file_big.gif" style="vertical-align: middle;" valign="middle" align="center" /></td>

  </tr></table>

</div>

{include file='footer.tpl'}