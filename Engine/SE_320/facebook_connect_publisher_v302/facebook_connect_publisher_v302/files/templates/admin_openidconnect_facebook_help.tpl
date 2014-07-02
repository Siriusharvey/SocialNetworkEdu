{include file='admin_header.tpl'}
{literal}
<style>
div.faq_header {
  padding: 4px 5px 5px 6px;
  border: 1px solid #DDDDDD;
  font-weight: bold;
  background-image: url(../images/facebook_help/faq_header.gif);
  background-repeat: repeat-x;
  color: #333333;
  font-family: tahoma,"Trebuchet MS",arial,serif;
}
div.faq {
  margin: 10px;
  padding: 5px 5px 5px 10px;
  border-left: 3px solid #DDDDDD;
}
div.faq_questions {
  background: #FFFFFF;
  border: 1px solid #DDDDDD;
  border-top: none;
  padding: 5px 7px 5px 8px;
}

div.faq_questions A {
  text-decoration: none;
}
div.code {
  background:#FFFAEF none repeat scroll 0%;
  border:1px solid #FFE5A7;
  font-family:courier,courier new,tahoma,serif;
  margin-bottom:5px;
  margin-top:5px;
  padding:10px;
}
</style>
{/literal}

{* JAVASCRIPT FOR CHANGING FRIEND MENU OPTION *}
{literal}
<script type="text/javascript">
<!-- 
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
//-->
</script>
{/literal}


<h2>Facebook Connect / Publisher Help & FAQ</h2>
Here you can find information that will guide you on using the Facebook Connect / Publisher plugin. For more information please visit our <a href="http://community.socialenginemods.net">community forums</a>.

<br />
<br />
<br />


<div class='faq_header'>General</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_1');">Getting Started</a><br>
  <div class='faq' style='display: none;' id='faq_1'>

    <table cellpadding='0' cellspacing='0' style='margin-top: 5px;'>
    <tr>
    <td class='step'>1</td>
    <td><b><a href='javascript:void(0)' onclick="faq_show('faq_20')">Create Facebook Application</a></b><br>This will establish your brand on Facebook. You can upload your logo and allow your users to clearly associate your brand with your site.</td>
    </tr>
    <tr>
    <td class='step'>2</td>
    <td><b><a href='admin_openidconnect_facebook.php'>Enter API Key and Secret.</a></b><br>After creating Facebook application and receiving api_key and secret, you can enter them here.</td>
    </tr>
    <tr>
    <td class='step'>3</td>
    <td><b><a href='admin_openidconnect_facebook_stories.php'>Customize your Facebook news feed stories and easily create new ones</a></b><br>You can easily customize the story text and create new stories from your Social Engine Recent Activity Feed.</td>
    </tr>
    </table>

  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_2');">Inviting Friends via Facebook (or why I can only invite 2 friends?)</a><br>
  <div class='faq' style='display: none;' id='faq_2'>

    After signing up user can invite his Facebook Friends using Facebook invitation dialog. The invitations will appear as a notification in Facebook (The invitation will <u>not</u> reach user email or inbox). The amount of invitations is limited to a small number that will increase depending on user's friends' acceptance of the invitation requests. The maximum cap can be around 8 friends at once.
    
    <br><br>
      For professional invitation solution which does not have these limitations, please see our <a target="_blank" href="http://www.socialenginemods.net/social-engine/plugins/1/friends-inviter-contacts-importer">Friends Inviter plugin with Social Networks</a>, which allows inviting unlimited number of friends, in controlled manner, without spamming or being marked as a spammer.

  </div>
</div>
<br>

<div class='faq_header'>Facebook Application</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_20');">How can I create a Facebook Application?</a><br>
  <div class='faq' style='display: none;' id='faq_20'>
    To create a facebook application and receive api_key and secret pair: <br><br>
    
    Navigate to the Facebook page <a href="http://developers.facebook.com/get_started.php">http://developers.facebook.com/get_started.php</a> 
    
    and follow the steps  - you only need api_key and secret, so stop after step #2. <br><br>
    
    You only need to modify your app logo and in the "Connect" Tab, your logo as well. Leave the rest as is.
    
    <br><br>
      
      <strong>If you prefer us to setup the application for you, please purchase Facebook Application setup service <a target="_blank" href="http://www.socialenginemods.net/shop"> here <a/> </strong>
  </div>
</div>
<br>

<div class='faq_header'>Facebook News Feed Stories</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_50');">What is a news feed story and how is it created from my website?</a><br>
  <div class='faq' style='display: none;' id='faq_50'>
    Facebook News Feed story is created on Facebook Wall and is very similar to the Social Engine Recent Activity Feed. Every story can have various tags / variables parameters the are replaced when the story is published. For example, the standard {literal}{*actor*}{/literal} variable is replaced by the current Facebook User Name; For "New Group", the {literal}{*group-title*}{/literal}, {literal}{*group-desc*}{/literal}, etc will be substituted with the actual group information.
    <br>
      The following variables can be used everywhere:
      <br>
        <ul>
          <li> {literal}{*site-name*}{/literal} - Your public site name, which you have entered in <a href="admin_openidconnect_facebook.php">General Facebook Connect / Publisher Settings</a>
          <li> {literal}{*site-link*}{/literal} - Link to your site root, this is the link: {$url->url_base}
        </ul>
    <br>
    When user on your website performs an activity that is registered in <a href="admin_openidconnect_facebook_stories.php">Facebook Publisher Feed Stories</a>, he will have an option to publish this activity to Facebook where his friends can react, comment and use the links to find your website, thus increasing interactivity and social participation.
  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_51');">News Feed Story Dialog and variables</a><br>
  <div class='faq' style='display: none;' id='faq_51'>

    <table cellpadding=0 cellspacing=0 width="100%">
      <tr>
        <td>
        <img src="../images/facebook_help/facebook_publish_dialog_1.png">
          <br><br>
        </td>
        <td style="padding-left: 10px">
          <strong>User Prompt</strong> for the Feed Story. You can leave it blank or enter a suggestion, like the one on the picture. This text will not appear on Facebook.
        </td>
      </tr>
      <tr>
        <td>
        <img src="../images/facebook_help/facebook_publish_dialog_2.png">
        </td>
        <td style="padding-left: 10px">
          <strong>User Message</strong> will appear as a "status" line of the story on Facebook. You can leave it blank or enter a suggestion, like the one on the picture.
        </td>
      </tr>
      <tr>
        <td>
        <img src="../images/facebook_help/facebook_publish_dialog_3.png">
        </td>
        <td style="padding-left: 10px">
          <strong>Feed Story Title</strong> - can include links and basic html tags. <u>Must</u> start with {literal}{*actor*}{/literal} tag
        </td>
      </tr>
      <tr>
        <td>
        <img src="../images/facebook_help/facebook_publish_dialog_4.png">
        </td>
        <td style="padding-left: 10px">
          <strong>Feed Story Body</strong> - can include links and basic html tags. <u>Must</u> start with {literal}{*actor*}{/literal} tag
        </td>
      </tr>
    </table>
  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_52');">How will the News Feed Story look on Facebook?</a><br>
  <div class='faq' style='display: none;' id='faq_52'>

    <table cellpadding=0 cellspacing=0 width="100%">
      <tr>
        <td>
        <img src="../images/facebook_help/facebook_wall.png">
          <br><br>
        </td>
        <td style="padding-left: 10px">
          This is a snapshot of the story as it will appear on Facebook Wall.
        </td>
      </tr>
    </table>
  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_53');">Creating a new Feed Story</a><br>
  <div class='faq' style='display: none;' id='faq_53'>
    
    You can create Facebook News Feed Stories based on your Social Engine "Recent Activity Feed" with "news feed actions" from official plugins or 3rd party plugins. The story will be precreated from the template and you can amend it for better phrasing, include links and better description.
    
    <br><br>
    
    To create new story, navigate to <a href="admin_openidconnect_facebook_stories.php">Facebook Feed Stories page</a> and click "Create New Story" button on the top right page corner.

  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_54');">Example: Creating a new Feed Story for action "Creating a Forum Topic"</a><br>
  <div class='faq' style='display: none;' id='faq_54'>
    
    <ol>
      <li> Navigate to <a target="_blank" href="admin_openidconnect_facebook_stories.php">Facebook Feed Stories</a> page, click "Create New Feed Story" (top right corner), choose "Creating a Forum Topic (forumtopic)" and click "Add Story"
      <br>
      </li>
      <li>
        <div>
          The story is created and prepopulated with some suggested text, which we will change a little:
          <ul>
          <li><strong>User Prompt</strong> - optional, can be set to "What about this forum post?"</li>
          <li><strong>User Message</strong> - optional, can be set to "See my forum post, who can help me resolve this issue?"</li>
          <li><strong>Feed Story title</strong> - can be changed to say "{literal}{*actor*}{/literal} created a new Forum Topic"</li>
          <li><strong>Feed Story Body</strong> - can be left as is</li>
          <li><strong>Action Link</strong> : Let's place a link to "browse our forums" - </li>
          <li><strong>Action Link - Link</strong>: set to "{literal}{*site-link*}{/literal}forum.php"</li>
          <li><strong>Action Link - Text</strong>: set to "Browse our forums"</li>
          </ul>
        </div>
        <br>
      </li>
      <li>Scroll to bottom of the page and click "Save"</li>
      <li>Now you can go post a forum topic and see what happens!</li>
      
    </ol>

  </div>
</div>
<br>
  
<div class='faq_header'>Integration</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_60');">What is Integration and why do I need it?</a><br>
  <div class='faq' style='display: none;' id='faq_60'>
    Integration allows you to add various features to your website by directly modifying small parts of code. For example, if you want to display the Facebook Connect Button on your homepage, easy instructions below will help you to accomplish this.
    
    <br>
        
    
  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_61');">Integrating Facebook Connect button on your website homepage</a><br>
  <div class='faq' style='display: none;' id='faq_61'>
    <ul>
      <li>Open templates/home.tpl (or read instructions and <a href="javascript: editTemplate('home.tpl');">click here</a> to open the editor)
      <li> Find (around line 63)
        {literal}
        <div class="code">
        <pre>
          &lt;/div&gt;
          &lt;div class='portal_spacer'&gt;&lt;/div&gt;
      
        {* SHOW HELLO MESSAGE IF USER IS LOGGED IN *}
        </pre>
        </div>
        {/literal}
      </li>
      <li> Insert on the line before (immediately BEFORE the &lt;/div&gt;)
        {literal}
        <div class="code">
        <pre>
        {* OPENIDCONNECT 1/1 START *}
          {include file="openidconnect_home_mini.tpl"}
        {* OPENIDCONNECT 1/1 END *}
        </pre>
        </div>
        {/literal}
      </li>
      <li> That's it!
      </li>
    </ul>
  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_62');">Integrating Facebook Connect button on your signup page</a><br>
  <div class='faq' style='display: none;' id='faq_62'>
    <ul>
      <li>Open templates/signup.tpl (or read instructions and <a href="javascript: editTemplate('signup.tpl');">click here</a> to open the editor)
      <li> Find (around line 441)
        {literal}
        <div class="code">
          <pre>
          
          &lt;form action='signup.php' method='POST'&gt;
          &lt;div class='signup_header'>{lang_print id=681}&lt;/div&gt;
          
          </pre>
        </div>
        {/literal}
      </li>
      <li> Insert on the line before (immediately BEFORE) 
        {literal}
        <div class="code">
        <pre>
          {* OPENIDCONNECT 1/1 START *}
            {include file="openidconnect_signup_top.tpl"}
          {* OPENIDCONNECT 1/1 END *}
        </pre>
        </div>
        {/literal}
      </li>
      <li> That's it!
      </li>
    </ul>
  </div>
</div>
<br
  
<div class='faq_header'>Troubleshooting</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_40');">The facebook connect button doesn't work anywhere when I click on it</a><br>
  <div class='faq' style='display: none;' id='faq_40'>
    If you have a custom template or have modified your footer.tpl, please make sure the following appears at the bottom of your footer.tpl file and ad as necessary, as it should appear with official SocialEngine distribution.

    <ul>
      <li>Open templates/footer.tpl (or <a href="javascript: editTemplate('footer.tpl');">click here</a> to open the editor)
      <li> Make sure the following lines appear at the bottom of the file:
        {literal}
        <div class="code">
          <pre>
          
          {* INCLUDE ANY FOOTER TEMPLATES NECESSARY *}
          {hook_include name=footer}
          
          &lt;/body&gt;
          &lt;/html&gt;          

          </pre>
        </div>
        {/literal}
      </li>
      <li> That's it!
      </li>
    </ul>

  </div>
</div>
<div class='faq_questions'>
  <a href="javascript:void(0);" onClick="faq_show('faq_43');">I have some other problem...</a><br>
  <div class='faq' style='display: none;' id='faq_43'>
    Please visit our <a target="_blank" href="http://www.socialenginemods.net/clients">client area</a> and open a support ticket.
    <br><br>
    You can also visit our <a href="http://community.socialenginemods.net">community support forums</a>.
  </div>
</div>






{* EDITING TEMPLATE *}

{literal}
<script type="text/javascript">
<!-- 
  function editTemplate(t) {
    $('t').value = t;
    var url = 'admin_templates.php?task=gettemplate&t='+t;
	var request = new Request.JSON({secure: false, url: url,
		onComplete: function(jsonObj) {
			if(jsonObj.is_error == 0) {
			  edit(jsonObj.template);
			} else {
			  alert(jsonObj.error_message);
			}
		}
	}).send();
  }
  function edit(template) {
    TB_show('{/literal}{lang_print id=471}{literal}', '#TB_inline?height=600&width=700&inlineId=template', '', '../images/trans.gif');
    $("TB_window").getElements('textarea[id=template_code]').each(function(el) { el.value = template; });
  }

//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY TEMPLATE EDITING FIELD *}
<div style='display: none;' id='template'>
  <form action='admin_templates.php' method='post' name='editform' target='ajaxframe' onSubmit='parent.TB_remove();'>
  <div style='margin-top: 10px; margin-bottom: 10px;'>{lang_print id=472}</div>
  <textarea name='template_code' id='template_code' rows='20' style='width: 100%; font-size: 8pt; height: 485px; font-family: verdana, serif;'>{$template_code}</textarea>
  <br><br>
  <input type='submit' class='button' value='{lang_print id=173}'> <input type='button' class='button' value='{lang_print id=466}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='save'>
  <input type='hidden' name='t' id='t' value=''>
  </form>
</div>

{if $openid_facebook_show_faq != ''}
<script>
faq_show('faq_{$openid_facebook_show_faq}');
</script>
{/if}

{include file='admin_footer.tpl'}