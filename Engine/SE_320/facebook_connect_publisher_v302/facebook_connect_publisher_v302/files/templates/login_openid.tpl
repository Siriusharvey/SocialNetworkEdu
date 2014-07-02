{include file='header.tpl'}


{if $task == "confirmlink"}



  <img src='./images/icons/signup48.gif' border='0' class='icon_big' />
  <div class='page_header'>{lang_print id=100051033}</div>
  <div>{lang_print id=100051034}</div>
  <br><br>

  {lang_print id=100051048} {$user->user_displayname} <br>
  
  {lang_print id=100051049} {$openidservice_name|capitalize}? <br><br><br>
  
  <form action='login_openid.php' method='POST'>

  <input type='hidden' name='task' value='confirmlink'>

  <input type='hidden' name='openidsession' value='{$openidsession}'>
  <input type='hidden' name='openidservice' value='{$openidservice_name}'>
  <input type='hidden' name='next' value='{$next}'>

  <input type='submit' class='button' value='{lang_print id=100051050}'>  {lang_print id=100051054} <a href='user_home.php'>{lang_print id=100051052}</a>

  </form>
  




{elseif $task == "linkerror"}


  <img src='./images/icons/error48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=100051143}</div>
  <div>&nbsp;</div>
  <br><br>

  {lang_print id=100051144} {$openidservice_name|capitalize}!
  
  <br><br>
    
  {lang_print id=100051145}:
    
  <br><br>
  
  {lang_sprintf id=100051146 1=$openidservice_name|capitalize}
    

{else}





  <img src='./images/icons/signup48.gif' border='0' class='icon_big' />
  <div class='page_header'>{lang_print id=100051033}</div>
  <div>{lang_print id=100051034}</div>
  <br><br>

  {if $openid_signup_mode == 0}
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='form1' style="vertical-align:middle"><img src="./images/brands/{$openid_service.openidservice_logo_mini}"></td>
  <td class='form2' style="vertical-align:middle">
    {$openid_service.openidservice_displayname} {lang_print id=100051035}
  </td>
  </tr>
  </table>

  <br>
  {/if}

  {* SHOW ERROR MESSAGE *}
  {if $error_message != ""}
    <table cellpadding='0' cellspacing='0'>
    <tr><td class='result'>
      <div class='error'><img src='./images/error.gif' border='0' class='icon'> {if $error_message|@is_numeric}{lang_print id=$error_message}{else}{$error_message}{/if}</div>
    </td></tr></table>
    <br>
  {/if}


{if $task == "step1"}

  <table cellpadding='0' cellspacing='0' width="100%">
  <tr>
  <td style="vertical-align:top">

  <form action='login_openid.php' method='POST'>
  <div class='signup_header'>{lang_print id=100051036}</div>
  <table cellpadding='0' cellspacing='0'>

  {if "email"|in_array:$openid_signup_required_fields}
  <tr>
  <td class='form1'>{lang_print id=100051037}</td>
  <td class='form2'>
    <input name='signup_email' type='text' class='text' maxlength='50' size='40' value='{$signup_email}'>
    <div class='form_desc'>{lang_print id=100051038}</div>
  </td>
  </tr>
  {/if}

  {if $setting.setting_username}
    <tr>
      <td class='form1'>{lang_print id=28}:</td>
      <td class='form2'>
        <input name='signup_username' type='text' class='text' maxlength='50' size='40' value='{$signup_username}'>
        {capture assign=tip}{lang_print id=685}{/capture}
        <img src='./images/icons/tip.gif' border='0' class='Tips1' title="{$tip|escape:quotes}">
        <div class='form_desc'>{lang_print id=686}</div>
      </td>
    </tr>
  {/if}

  {if $openid_signup_mode == 1 OR "profilecat"|in_array:$openid_signup_required_fields}

    {if $cats|@count > 1}
      <tr>
        <td class='form1'>{lang_print id=709}:</td>
        <td class='form2'>
          <select name='signup_cat'>
          {section name=cat_loop loop=$cats}
            <option value='{$cats[cat_loop].cat_id}'{if $signup_cat == $cats[cat_loop].cat_id} selected='selected'{/if}>{lang_print id=$cats[cat_loop].cat_title}</option>
          {/section}
          </select>
        </td>
      </tr>
    {/if}
  
  {/if}

  {if $openid_signup_mode == 1 OR "timezone"|in_array:$openid_signup_required_fields}
  
    <tr>
      <td class='form1' width='100'>{lang_print id=206}:</td>
      <td class='form2'>
        <select name='signup_timezone'>
        <option value='-8'{if $signup_timezone == "-8"} SELECTED{/if}>Pacific Time (US & Canada)</option>
        <option value='-7'{if $signup_timezone == "-7"} SELECTED{/if}>Mountain Time (US & Canada)</option>
        <option value='-6'{if $signup_timezone == "-6"} SELECTED{/if}>Central Time (US & Canada)</option>
        <option value='-5'{if $signup_timezone == "-5"} SELECTED{/if}>Eastern Time (US & Canada)</option>
        <option value='-4'{if $signup_timezone == "-4"} SELECTED{/if}>Atlantic Time (Canada)</option>
        <option value='-9'{if $signup_timezone == "-9"} SELECTED{/if}>Alaska (US & Canada)</option>
        <option value='-10'{if $signup_timezone == "-10"} SELECTED{/if}>Hawaii (US)</option>
        <option value='-11'{if $signup_timezone == "-11"} SELECTED{/if}>Midway Island, Samoa</option>
        <option value='-12'{if $signup_timezone == "-12"} SELECTED{/if}>Eniwetok, Kwajalein</option>
        <option value='-3.3'{if $signup_timezone == "-3.3"} SELECTED{/if}>Newfoundland</option>
        <option value='-3'{if $signup_timezone == "-3"} SELECTED{/if}>Brasilia, Buenos Aires, Georgetown</option>
        <option value='-2'{if $signup_timezone == "-2"} SELECTED{/if}>Mid-Atlantic</option>
        <option value='-1'{if $signup_timezone == "-1"} SELECTED{/if}>Azores, Cape Verde Is.</option>
        <option value='0'{if $signup_timezone == "0"} SELECTED{/if}>Greenwich Mean Time (Lisbon, London)</option>
        <option value='1'{if $signup_timezone == "1"} SELECTED{/if}>Amsterdam, Berlin, Paris, Rome, Madrid</option>
        <option value='2'{if $signup_timezone == "2"} SELECTED{/if}>Athens, Helsinki, Istanbul, Cairo, E. Europe</option>
        <option value='3'{if $signup_timezone == "3"} SELECTED{/if}>Baghdad, Kuwait, Nairobi, Moscow</option>
        <option value='3.3'{if $signup_timezone == "3.3"} SELECTED{/if}>Tehran</option>
        <option value='4'{if $signup_timezone == "4"} SELECTED{/if}>Abu Dhabi, Kazan, Muscat</option>
        <option value='4.3'{if $signup_timezone == "4.3"} SELECTED{/if}>Kabul</option>
        <option value='5'{if $signup_timezone == "5"} SELECTED{/if}>Islamabad, Karachi, Tashkent</option>
        <option value='5.5'{if $signup_timezone == "5.5"} SELECTED{/if}>Bombay, Calcutta, New Delhi</option>
        <option value='6'{if $signup_timezone == "6"} SELECTED{/if}>Almaty, Dhaka</option>
        <option value='7'{if $signup_timezone == "7"} SELECTED{/if}>Bangkok, Jakarta, Hanoi</option>
        <option value='8'{if $signup_timezone == "8"} SELECTED{/if}>Beijing, Hong Kong, Singapore, Taipei</option>
        <option value='9'{if $signup_timezone == "9"} SELECTED{/if}>Tokyo, Osaka, Sapporto, Seoul, Yakutsk</option>
        <option value='9.3'{if $signup_timezone == "9.3"} SELECTED{/if}>Adelaide, Darwin</option>
        <option value='10'{if $signup_timezone == "10"} SELECTED{/if}>Brisbane, Melbourne, Sydney, Guam</option>
        <option value='11'{if $signup_timezone == "11"} SELECTED{/if}>Magadan, Soloman Is., New Caledonia</option>
        <option value='12'{if $signup_timezone == "12"} SELECTED{/if}>Fiji, Kamchatka, Marshall Is., Wellington</option>
        </select>
      </td>
    </tr>
  {/if}

  {if $openid_signup_mode == 1 OR "language"|in_array:$openid_signup_required_fields}
    {if $setting.setting_lang_allow == 1}
      <tr>
        <td class='form1'>{lang_print id=687}:</td>
        <td class='form2'>
          <select name='signup_lang'>
            {section name=lang_loop loop=$lang_packlist}
              <option value='{$lang_packlist[lang_loop].language_id}'{if $lang_packlist[lang_loop].language_default == 1} selected='selected'{/if}>{$lang_packlist[lang_loop].language_name}</option>
            {/section}
          </select>
        </td>
      </tr>
    {/if}
  {/if}


  {if $setting.setting_signup_tos || ($setting.setting_signup_invite AND !$hide_signup_invite)}
  </table>
  <br />
  
  <div class='signup_header'>{lang_print id=688}</div>
  <table cellpadding='0' cellspacing='0'>
  {/if}

  {if $setting.setting_signup_invite AND !$hide_signup_invite}
  <tr>
    <td class='form1' width='100'>{lang_print id=689}</td>
    <td class='form2'><input type='text' name='signup_invite' value='{$signup_invite}' class='text' size='10' maxlength='10'></td>
  </tr>
  {/if}

  {if $setting.setting_signup_tos}
  <tr>
    <td class='form1' width='100'>&nbsp;</td>
    <td class='form2'>
      <input type='checkbox' name='signup_agree' id='tos' value='1'{if $signup_agree == 1} CHECKED{/if}>
      <label for='tos'> {lang_print id=692}</label>
    </td>
  </tr>
  {/if}



  <tr><td colspan='2'>&nbsp;</td></tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'><input type='submit' class='button' value='{lang_print id=100051039}'></td>
  </tr>
  </table>
    
  <input type='hidden' name='task' value='step1do'>
  <input type='hidden' name='openidsession' value='{$openidsession}'>
  <input type='hidden' name='openidservice' value='{$openidservice_name}'>
  <input type='hidden' name='openid_user_id' value='{$openid_user_id}'>
  <input type='hidden' name='step' value='{$step}'>
  </form>
  
  </td>
  
  <td style="vertical-align:top; padding-left: 40px; padding-right: 10px;Xwidth: 300px">
    
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td style="vertical-align:top">
      {if $openid_user_thumb != ''}
      <img border='0' src="{$openid_user_thumb}" style="max-width: 50px; ">
      {/if}
    </td>
    <td style="vertical-align:top; padding-left: 10px; font-size: 15pt; text-align: left">
      {lang_print id=100051053} {$openid_user_displayname}!
    </td>
    </tr>
    <tr>
    <td colspan=2 style="padding: 10px">
      {lang_print id=100051051}
    </td>
    <tr>
    </table>
    
    
    
  </td>
  </tr>
  </table>

{else}

  {*STEP 2*}

  {* JAVASCRIPT FOR SHOWING DEP FIELDS *}
  {literal}
  <script type="text/javascript">
  <!-- 
  function ShowHideDeps(field_id, field_value, field_type) {
    if(field_type == 6) {
      if($('field_'+field_id+'_option'+field_value)) {
        if($('field_'+field_id+'_option'+field_value).style.display == "block") {
	  $('field_'+field_id+'_option'+field_value).style.display = "none";
	} else {
	  $('field_'+field_id+'_option'+field_value).style.display = "block";
	}
      }
    } else {
      var divIdStart = "field_"+field_id+"_option";
      for(var x=0;x<$('field_options_'+field_id).childNodes.length;x++) {
        if($('field_options_'+field_id).childNodes[x].nodeName == "DIV" && $('field_options_'+field_id).childNodes[x].id.substr(0, divIdStart.length) == divIdStart) {
          if($('field_options_'+field_id).childNodes[x].id == 'field_'+field_id+'_option'+field_value) {
            $('field_options_'+field_id).childNodes[x].style.display = "block";
          } else {
            $('field_options_'+field_id).childNodes[x].style.display = "none";
          }
        }
      }
    }
  }
  //-->
  </script>
  {/literal}

  <form action='login_openid.php' method='POST'>

  {* LOOP THROUGH TABS *}
  {section name=cat_loop loop=$cats}
  {section name=subcat_loop loop=$cats[cat_loop].subcats}
    {if ($openid_signup_mode == 1 OR $cats[cat_loop].subcats[subcat_loop].subcat_openid_required) AND $cats[cat_loop].subcats[subcat_loop].fields|@count != 0}
    <div class='signup_header'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</div>
    <table cellpadding='0' cellspacing='0'>

    {* LOOP THROUGH FIELDS IN TAB *}
    {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
      {if $openid_signup_mode == 1 OR $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_openid_required}
      <tr>
      <td class='form1' width='150'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_required != 0}*{/if}</td>
      <td class='form2'>



      {* TEXT FIELD *}
      {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 1}
        <div><input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_maxlength}'></div>

        {* JAVASCRIPT FOR CREATING SUGGESTION BOX *}
        {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options != "" && $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options|@count != 0}
        {literal}
        <script type="text/javascript">
        <!-- 
        window.addEvent('domready', function(){
	  var options = {
		script:"misc_js.php?task=suggest_field&limit=5&{/literal}{section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}options[]={$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}&{/section}{literal}",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:5,
		multisuggest:false,
		callback: function (obj) {  }
	  };
	  var as_json{/literal}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}{literal} = new bsn.AutoSuggest('field_{/literal}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}{literal}', options);
        });
        //-->
        </script>
        {/literal}
        {/if}


      {* TEXTAREA *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 2}
        <div><textarea rows='6' cols='50' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value}</textarea></div>



      {* SELECT BOX *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 3}
        <div><select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' onchange="ShowHideDeps('{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}', this.value);" style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        <option value='-1'></option>
        {* LOOP THROUGH FIELD OPTIONS *}
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</option>
        {/section}
        </select>
        </div>
        {* LOOP THROUGH DEPENDENT FIELDS *}
        <div id='field_options_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}
        {/section}
        </div>
    


      {* RADIO BUTTONS *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 4}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          <div>
          <input type='radio' class='radio' onclick="ShowHideDeps('{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}');" style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' id='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}

        {/section}
        </div>



      {* DATE FIELD *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 5}
        <div>
        <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_1' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        {section name=date1 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1}
          <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}[ {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].name} ]{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].name}{/if}</option>
        {/section}
        </select>

        <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_2' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        {section name=date2 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2}
          <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}[ {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].name} ]{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].name}{/if}</option>
        {/section}
        </select>

        <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_3' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        {section name=date3 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3}
          <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}[ {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].name} ]{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].name}{/if}</option>
        {/section}
        </select>
        </div>



      {* CHECKBOXES *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 6}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          <div>
          <input type='checkbox' onclick="ShowHideDeps('{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}', '{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type}');" style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}[]' id='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value|in_array:$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dependency == 1}
	    {* SELECT BOX *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}
          {/if}

        {/section}
        </div>

      {/if}

      <div class='form_desc'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_desc}</div>
      {capture assign='field_error'}{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_error}{/capture}
      {if $field_error != ""}<div class='form_error'><img src='./images/icons/error16.gif' border='0' class='icon'> {$field_error}</div>{/if}
    </td>
    </tr>
      {/if}
    {/section}
  </table>
  <br>
  {/if}
  {/section}
  {/section}

  <table cellpadding='0' cellspacing='0'>
  <tr><td colspan='2'>&nbsp;</td></tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'><input type='submit' class='button' value='{lang_print id=100051040}'></td>
  </tr>
  </table>
    
  <input type='hidden' name='task' value='step2do'>
  <input type='hidden' name='signup_email' value='{$signup_email}'>
  <input type='hidden' name='signup_username' value='{$signup_username}'>
  <input type='hidden' name='signup_timezone' value='{$signup_timezone}'>
  <input type='hidden' name='signup_lang' value='{$signup_lang}'>
  <input type='hidden' name='signup_invite' value='{$signup_invite}'>
  <input type='hidden' name='signup_agree' value='{$signup_agree}'>
  <input type='hidden' name='signup_cat' value='{$signup_cat}'>
  <input type='hidden' name='openidsession' value='{$openidsession}'>
  <input type='hidden' name='openidservice' value='{$openidservice_name}'>
  <input type='hidden' name='step' value='{$step}'>
  </form>

{/if}

{/if} {* confirmlink if *}

{include file='footer.tpl'}