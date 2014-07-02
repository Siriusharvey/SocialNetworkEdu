{include file='admin_header.tpl'}

<h2>{lang_print id=11270004}</h2>
{lang_print id=11270005}
<br />
<br />

{if $is_error}
  <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
{/if}

{if $result}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action="admin_badge_edit.php" method="POST" enctype="multipart/form-data" >
<input type="hidden" name="badge_id" value="{$badge->badge_info.badge_id}" />
<input type="hidden" name="task" value="dosave" />

<table cellspacing="0" cellpadding="0" width="640" id="badge_edit_form">
  <tr>
    <td class="form1">{lang_print id=11270087}:</td>
    <td class="form2"><input type="text" name="badge_title" value="{$badge->badge_info.badge_title}" class="text" size="40" /></td>
  </tr>
  <tr>
    <td class="form1">{lang_print id=11270010}:</td>
    <td class="form2">
 
      <script type="text/javascript" src="../include/fckeditor/fckeditor.js"></script>
		  <script type="text/javascript">
		  <!--
		  var sBasePath = "../include/fckeditor/" ;
		  var sToolbar = "badge_admin";
		  var oFCKeditor = new FCKeditor( 'badge_desc' ) ;
		  oFCKeditor.Config["CustomConfigurationsPath"] = "../../js/badge_fckconfig.js";
		  oFCKeditor.BasePath = sBasePath ;
		  oFCKeditor.Height = "250" ;
		  if ( sToolbar != null )
		    oFCKeditor.ToolbarSet = sToolbar ;
		  oFCKeditor.Value = '{$badge->badge_info.badge_desc}' ;
		  oFCKeditor.Create() ;
		  //-->
		  </script>
		  
    </td>
  </tr>
  
  
  <tr>
    <td class="form1">{lang_print id=11270173}:</td>
    <td class="form2">
    <select name='badge_badgecat_id'>
        <option value="0"></option>
       {foreach from=$cats item=cat}
        <option value="{$cat.cat_id}" style='font-weight: bold' {if $badge->badge_info.badge_badgecat_id == $cat.cat_id} selected='selected' {/if}>{lang_print id=$cat.cat_title}</option>
        {if !empty($cat.subcats)}
        {foreach from=$cat.subcats item=subcat}
          <option value="{$subcat.subcat_id}" {if $badge->badge_info.badge_badgecat_id  == $subcat.subcat_id} selected='selected' {/if}>&nbsp &nbsp &raquo; &nbsp &nbsp {lang_print id=$subcat.subcat_title}</option>
        {/foreach}
        {/if}
       {/foreach}
    </select>    
    </td>
  </tr>  
  
  <tr>
    <td class="form1">{lang_print id=11270018}:</td>
    <td class="form2"><input type="text" name="badge_cost" value="{$badge->badge_info.badge_cost}" class="text" size="10" /> {$setting.setting_epayment_currency_code}</td>
  </tr>
  
  <tr>
    <td class="form1">{lang_print id=11270016}:</td>
    <td class="form2">
      <input type="checkbox" id="badge_epayment" name="badge_epayment" value="1" {if $badge->badge_info.badge_epayment}checked='checked'{/if}>
      <label for="badge_epayment">{lang_print id=11270017}</label>
    </td>
  </tr>  
  
  <tr>
    <td class="form1">{lang_print id=11270014}:</td>
    <td class="form2">
      <input type="checkbox" id="badge_approved" name="badge_approved" value="1" {if $badge->badge_info.badge_approved}checked='checked'{/if}>
      <label for="badge_approved">{lang_print id=11270015}</label>
    </td>
  </tr>  
   
  <tr>
    <td class="form1">{lang_print id=11270183}:</td>
    <td class="form2">
      <input type="checkbox" id="badge_search" name="badge_search" value="1" {if $badge->badge_info.badge_search}checked='checked'{/if}>
      <label for="badge_search">{lang_print id=11270184}</label>
    </td>
  </tr>  
   
  <tr>
    <td class="form1"></td>
    <td class="form2" style="border-bottom: 1px solid #ccc">
    </td>
  </tr>  

  <tr>
    <td class="form1">{lang_print id=11270147}:</td>
    <td class="form2">
      <input type="radio" id="badge_link_details_1" name="badge_link_details" value="1" {if $badge->badge_info.badge_link_details}checked='checked'{/if}><label for="badge_link_details_1">{lang_print id=11270148}</label>
      <br />
      <input type="radio" id="badge_link_details_0" name="badge_link_details" value="0" {if !$badge->badge_info.badge_link_details}checked='checked'{/if}><label for="badge_link_details_0">{lang_print id=11270149}</label>   
    </td>
  </tr>  
   
  <tr>
    <td class="form1"></td>
    <td class="form2" style="border-bottom: 1px solid #ccc">
    </td>
  </tr> 

  <tr>
    <td class="form1">{lang_print id=11270011}:</td>
    <td class="form2">
      <input type="radio" id="badge_enabled_0" name="badge_enabled" value="0" {if !$badge->badge_info.badge_enabled}checked='checked'{/if}><label for="badge_enabled_0">{lang_print id=11270013}</label>  
      <br />    
      <input type="radio" id="badge_enabled_1" name="badge_enabled" value="1" {if $badge->badge_info.badge_enabled}checked='checked'{/if}><label for="badge_enabled_1">{lang_print id=11270012}</label>
      
			<table id="badge_permission_block">
			  <tr>
			    <td class="form1">{lang_print id=11270051}:</td>
			    <td class="form2">
			      <input type="radio" id="badge_level_all_1" name="badge_level_all" value="1" {if empty($badge->badge_info.badge_levels)}checked='checked'{/if}><label for="badge_level_all_1">{lang_print id=11270052}</label>
			      <br/>
			      <input type="radio" id="badge_level_all_0" name="badge_level_all" value="0" {if !empty($badge->badge_info.badge_levels)}checked='checked'{/if}><label for="badge_level_all_0">{lang_print id=11270053}</label>  
			      <div style='padding: 5px 20px;'>
			        <select name='badge_levels[]' size="5" multiple="multiple">
			        {foreach from=$levels item=level}
			          <option value="{$level.level_id}" {if $level.level_id|in_array:$badge->badge_info.badge_levels}selected='selected'{/if}>{$level.level_name}</option>
			        {/foreach}
			        </select>
			      </div>
			    </td>
			  </tr>
			  
			  <tr>
			    <td class="form1">{lang_print id=11270054}:</td>
			    <td class="form2">
			      <input type="radio" id="badge_subnet_all_1" name="badge_subnet_all" value="1" {if empty($badge->badge_info.badge_subnets)}checked='checked'{/if}><label for="badge_subnet_all_1">{lang_print id=11270055}</label>
			      <br/>
			      <input type="radio" id="badge_subnet_all_0" name="badge_subnet_all" value="0" {if !empty($badge->badge_info.badge_subnets)}checked='checked'{/if}><label for="badge_subnet_all_0">{lang_print id=11270056}</label>  
			      <div style='padding: 5px 20px;'>
			        <select name='badge_subnets[]' size="5" multiple="multiple">
			        {foreach from=$subnets item=subnet}
			          <option value="{$subnet.subnet_id}" {if $subnet.subnet_id|in_array:$badge->badge_info.badge_subnets}selected='selected'{/if}>{lang_print id=$subnet.subnet_name}</option>
			        {/foreach}
			        </select>
			      </div>
			    </td>
			  </tr>
			  
			  <tr>
			    <td class="form1">{lang_print id=11270057}:</td>
			    <td class="form2">
			      <input type="radio" id="badge_profilecat_all_1" name="badge_profilecat_all" value="1" {if empty($badge->badge_info.badge_profilecats)}checked='checked'{/if}><label for="badge_profilecat_all_1">{lang_print id=11270058}</label>
			      <br/>
			      <input type="radio" id="badge_profilecat_all_0" name="badge_profilecat_all" value="0" {if !empty($badge->badge_info.badge_profilecats)}checked='checked'{/if}><label for="badge_profilecat_all_0">{lang_print id=11270059}</label>  
			      <div style='padding: 5px 20px;'>
			        <select name='badge_profilecats[]' size="5" multiple="multiple">
			        {foreach from=$profilecats item=profilecat}
			          <option value="{$profilecat.profilecat_id}" {if $profilecat.profilecat_id|in_array:$badge->badge_info.badge_profilecats}selected='selected'{/if}>{lang_print id=$profilecat.profilecat_title}</option>
			        {/foreach}
			        </select>
			      </div>
			    </td>
			  </tr> 
			</table>
      
    </td>
  </tr>
  
  <tr>
    <td class="form1"></td>
    <td class="form2" style="border-bottom: 1px solid #ccc">
    </td>
  </tr>    
  
  <tr>
    <td class="form1">{lang_print id=11270019}:</td>
    <td class="form2">
      <img src="{$badge->badge_photo("../images/nophoto.gif", false, true)}" />
    </td>
  </tr>  
  
  <tr>
    <td class="form1">{lang_print id=11270020}:</td>
    <td class="form2">
      <input type="file" size="40" class="text" name="photo" />
    </td>
  </tr>   

  <tr>
    <td class="form1"></td>
    <td class="form2">
      <input type="submit" value="{lang_print id=173}" class="button" />    </td>
  </tr> 
</table>
  

  
</form>

<br><br>


{include file='admin_footer.tpl'}
