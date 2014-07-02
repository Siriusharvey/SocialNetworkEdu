{include file='header.tpl'}

{literal}
<script type='text/javascript'>
window.addEvent('domready', function() {

    var addedFav = 0;

    var share = new Fx.Slide('vid_share', {
        duration: 400,
        transition: Fx.Transitions.linear
    }).hide();

    var report = new Fx.Slide('vid_report', {
        duration: 400,
        transition: Fx.Transitions.linear
    }).hide();

    var favorite = new Fx.Slide('vid_favorite', {
        duration: 400,
        transition: Fx.Transitions.linear
    }).hide()

    $('vid_share_handler').addEvent('click', function(e) {
        e.stop;
        if ($('vid_share_blind').getStyle('display') == 'none') {
            $('vid_share_blind').setStyle('display', 'inline');
        }
        if (report.wrapper.offsetHeight > 0) {
	    report.toggle();
        }
        if (favorite.wrapper.offsetHeight > 0) {
	    favorite.toggle();
        }
        share.toggle();
    });

    $('vid_report_handler').addEvent('click', function(e) {
        e.stop;
        if ($('vid_report_blind').getStyle('display') == 'none') {
            $('vid_report_blind').setStyle('display', 'inline');
        }
        if (share.wrapper.offsetHeight > 0) {
	    share.toggle();
        }
        if (favorite.wrapper.offsetHeight > 0) {
	    favorite.toggle();
        }
        report.toggle(); 
    });

    {/literal}{if $user->user_exists}{literal}
    $('report_button').addEvent('click', function(event) {
		event.stop();
                if ($('report_details').value != "") {
                    var report_reason = "";
		    var sel = $$('.report_reason');
	            sel.each(function(el,i) {
                        if ('input' == el.get('tag') && 'radio' == el.get('type') && el.checked) {
	                    report_reason = el.value;
                        }
		    });
		    var Report = new Request.HTML({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'dosend', 'return_url' : '{/literal}{$url->url_base}{literal}vid.php?video_id={/literal}{$vids_array.id}{literal}', 'report_details' : $('report_details').value, 'report_reason' : report_reason},
                        update: $('report_container'),
                        onComplete: function(el) {
                                if (share.wrapper.offsetHeight > 0) {
	                            share.toggle();
                                }
                                if (favorite.wrapper.offsetHeight > 0) {
                                    favorite.toggle();
                                }
                                report.toggle();
                        }.delay(1500)
		    }).send();
                }
    });
    {/literal}{/if}{literal}

    {/literal}{if $is_favorite}{literal}
    $('remove_button').addEvent('click', function(event) {
     	  event.stop();
	  var RemFav = new Request.HTML({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'remfav', 'id' : '{/literal}{$vids_array.id}{literal}'},
                        onRequest: function() {
                                $('favorite_element').set({html: '<a href="javascript:void(0)" id="favorite_button">{/literal}{lang_print id=13500198}{literal}</a>'});
                        },
                        update: $('favorite_container'),
                        onComplete: function() {
                                addedFav = 1;
                                $('vid_favorite_blind').setStyle('display', 'inline');
                                if (report.wrapper.offsetHeight > 0) {
                                    report.toggle();
                                }
                                if (share.wrapper.offsetHeight > 0) {
                                    share.toggle();
                                }
                                favorite.toggle();
                                $('favorite_button').addEvent('click', function(event) {
                                     event.stop();
                                     if (share.wrapper.offsetHeight > 0) {
                                          share.toggle();
                                     }
                                     if (report.wrapper.offsetHeight > 0) {
                                          report.toggle();
                                     }
                                     favorite.toggle();
                                });
                        }
	  }).send();
    });
    {/literal}{else}{literal}
    $('favorite_button').addEvent('click', function(event) {
     	  event.stop();
          {/literal}{if $user->user_exists}{literal}
          if (addedFav == 0) {
		var AddFavorite = new Request.HTML({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'addtofav', 'id' : '{/literal}{$vids_array.id}{literal}'},
                        update: $('favorite_container'),
                        onComplete: function() {
                                addedFav = 1;
                                $('vid_favorite_blind').setStyle('display', 'inline');
                                if (report.wrapper.offsetHeight > 0) {
                                    report.toggle();
                                }
                                if (share.wrapper.offsetHeight > 0) {
                                    share.toggle();
                                }
                                favorite.toggle();
                        }
		}).send();
          } else {
          {/literal}{/if}{literal}
                {/literal}{if !$user->user_exists}{literal}
                if ($('vid_favorite_blind').getStyle('display') == 'none') {
                    $('vid_favorite_blind').setStyle('display', 'inline');
                }
                {/literal}{/if}{literal}
     	        if (share.wrapper.offsetHeight > 0) {
     	                share.toggle();
     	        }
     	        if (report.wrapper.offsetHeight > 0) {
     	                report.toggle();
     	        }
     	        favorite.toggle();
          {/literal}{if $user->user_exists}{literal} 
          }
          {/literal}{/if}{literal}
    });
    {/literal}{/if}{literal}

    RemoveFav = function() {
          var RemFavorite = new Request.HTML({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'remfav', 'id' : '{/literal}{$vids_array.id}{literal}'},
                        update: $('favorite_container')
	  }).send();
    }

    AddToFav = function() {
          var AddToFavorite = new Request.HTML({
			method: 'get',
			url: 'vid_request.php',
			data: { 'task' : 'addtofav', 'id' : '{/literal}{$vids_array.id}{literal}'},
                        update: $('favorite_container')
	  }).send();
    }
});
</script>
{/literal}

{include file='vid_buttons.tpl'}

<div class='page_header'>{$vids_array.title}</div>
<div id="content" style="width: 100%">

<div style='clear:both;height:10px;'></div>

{* BEGIN LEFT COLUMN *}
<div style='float: left; width: 640px; padding: 0px 10px 0px 0px;'>
    <div class='album_vid'>
        <center><div name='mediaspace' id='mediaspace'>
        {if $vids_array.directly == 0 AND $vids_array.type != "self"}
        {$vids_array.location}
        {else}
        <div style='margin-top: 165px; margin-bottom: 165px;'>Get the latest Adobe Flash Player from <a href="http://get.adobe.com/flashplayer/">here</a> to view this video.</div>
        {/if}
        </div></center>
        {if ($vids_array.directly == 1 AND ($vids_array.type == "youtube_api" OR ($vids_array.type != "youtube_api" AND $vids_array.type != "self"))) OR $vids_array.type == "self"}
        {literal}
        <script type='text/javascript'>
        <!--

          var s1 = new SWFObject('player.swf','player','640','360','9');
          s1.addParam('allowfullscreen','true');
          s1.addParam('allowscriptaccess','always');
          s1.addParam('wmode','transparent');
          s1.addParam('flashvars','{/literal}{if $vid_settings.skin != 'default'}{literal}skin=include/vid_skins/{/literal}{$vid_settings.skin}{literal}.swf&{/literal}{/if}{literal}autostart=true&controlbar=over&file={/literal}{if $vids_array.type == "self"}{$vids_array.location}{literal}.flv&streamer=vid_uri.php&type=http{/literal}{elseif $vids_array.type == "youtube_api"}{$vids_array.location}{literal}&type=youtube{/literal}{else}{$vids_array.location}{literal}&type=video{/literal}{/if}{literal}&bufferlength=3&stretching=uniform');
          s1.write('mediaspace');

        //-->
        </script>
        {/literal}
        {/if}
    </div>

    <div class='portal_spacer'></div>

    <div style='border: 1px solid #DDDDDD; height: 25px; overflow: hidden; padding: 10px; background: #FFFFFF;'>
        <div style="float:left"><iframe name='rateframe' id='rateframe' src="vid_rate.php?object_table=se_vids&object_primary=vid_id&object_id={$vids_array.id}" scrolling='no' frameborder='0' style='width:300px; height:25px;'></iframe></div>
        <div style='float:right;font-size:18px;'>{lang_sprintf id=13500028 1=$vids_array.views}</div>
    </div>
    <div style="clear:both; border: 1px; border-top: 0; border-style: solid; border-color: #DDDDDD;">
	<table cellpadding='0' cellspacing='0'>
          <tr>
          <td style="padding: 5px; padding-right: 0px;">
          <img src="./images/icons/vid_favvid16.gif">
          </td>
          <td style="padding: 5px; border-right: 1px solid #DDDDDD;">
          <div id="favorite_element">{if !$is_favorite}<a href="javascript:void(0)" id="favorite_button">{/if}{lang_print id=13500198}{if !$is_favorite}</a>{/if}{if $is_favorite} (<a href="javascript:void(0)" id="remove_button">{lang_print id=13500201}</a>){/if}</div>
          </td>
          <td style="padding: 5px; padding-right: 0px;">
          <img src="./images/icons/vid_share16.gif">
          </td>
          <td style="padding: 5px; border-right: 1px solid #DDDDDD;">
          <a href="javascript:void(0)" id="vid_share_handler">{lang_print id=13500199}</a>
          </td>
          <td style="padding: 5px; padding-right: 0px;">
          <img src="./images/icons/vid_report16.gif">
          </td>
          <td style="padding: 5px; border-right: 1px solid #DDDDDD;">
          <a href="javascript:void(0)" id="vid_report_handler">{lang_print id=13500200}</a>
          </td>
          </tr>
        </table>
    </div>
    <div id="vid_share_blind" style="display: none;">
    <div id="vid_share" style="border: 1px solid #DDDDDD; border-top: 0;">
    <div class="vid_icons_container" style="background: #F7F7F7;">
        <div class="vid_icons"><a href="http://www.facebook.com/sharer.php?u={$share_url}&t={$share_title}" target="_blank" title="Share on Facebook"><img src="./images/icons/vid_facebook.gif"></a></div>
        <div class="vid_icons"><a href="http://twitter.com/home?status={$share_title}:+{$share_url}" target="_blank" title="Tweet it"><img src="./images/icons/vid_twitter.gif"></a></div>
        <div class="vid_icons"><a href="http://www.myspace.com/Modules/PostTo/Pages/?u={$share_url}&t={$share_title}" target="_blank" title="Post to MySpace"><img src="./images/icons/vid_myspace.gif"></a></div>
        <div class="vid_icons"><a href="http://delicious.com/save?url={$share_url}&title={$share_title}" target="_blank" title="Add to Delicious"><img src="./images/icons/vid_delicious.gif"></a></div>
        <div class="vid_icons" style="padding: 0;"><a href="http://www.digg.com/submit?url={$share_url}&title={$share_title}&bodytext={$share_title}" target="_blank" title="Digg it"><img src="./images/icons/vid_digg.gif"></a></div>
        <div style="clear: both; height: 5px;"></div>
        <div class="vid_icons"><a href="http://www.stumbleupon.com/url/{$share_url_raw}" target="_blank" title="Stumble!"><img src="./images/icons/vid_stumbleupon.gif"></a></div>
        <div class="vid_icons"><a href="http://reddit.com/submit?url={$share_url}&title={$share_title}" target="_blank" title="Reddit this"><img src="./images/icons/vid_reddit.gif"></a></div>
        <div class="vid_icons"><a href="http://www.furl.net/storeIt.jsp?u={$share_url}&title={$share_title}" target="_blank" title="Furl it!"><img src="./images/icons/vid_furl.gif"></a></div>
        <div class="vid_icons"><a href="http://www.mister-wong.com/addurl/?bm_url={$share_url}&bm_description={$share_desc}" target="_blank" title="Wong it"><img src="./images/icons/vid_misterwong.gif"></a></div>
        <div class="vid_icons" style="padding: 0;"><a href="http://www.bebo.com/c/share?Url={$share_url}&Title={$share_title}" target="_blank" title="Share on Bebo"><img src="./images/icons/vid_bebo.gif"></a></div>
        <div style="clear: both; height: 5px;"></div>
        <div class="vid_icons"><a href="http://blinklist.com/blink?t={$share_title}&u={$share_url}&d={$share_desc}" target="_blank" title="Add to BlinkList"><img src="./images/icons/vid_blinklist.gif"></a></div>
        <div class="vid_icons"><a href="http://www.bloglines.com/sub/{$share_url_raw_no}" target="_blank" title="Subscribe in Bloglines"><img src="./images/icons/vid_bloglines.gif"></a></div>
        <div class="vid_icons"><a href="http://slashdot.org/bookmark.pl?title={$share_title}&url={$share_url}" target="_blank" title="Slashdot it!"><img src="./images/icons/vid_slashdot.gif"></a></div>
        <div class="vid_icons"><a href="http://friendfeed.com/share/bookmarklet/frame#title={$share_title_space}&url={$share_url}" target="_blank" title="Share on FriendFeed"><img src="./images/icons/vid_friendfeed.gif"></a></div>
        <div class="vid_icons" style="padding: 0;"><a href="http://cgi.fark.com/cgi/fark/submit.pl?new_url={$share_url}" target="_blank" title="Fark it"><img src="./images/icons/vid_fark.gif"></a></div>
    </div>
    <div style='height: 70px; overflow: hidden; border-left: 1px solid #DDDDDD; padding: 10px; background: #F7F7F7;'>
	  <table cellpadding='0' cellspacing='0'>
          <tr>
	  <td style='vertical-align: middle; text-align: right; padding: 0px 5px 9px 0;'>{lang_print id=13500190}: </td><td><input type="text" onClick="javascript:this.focus();this.select();" class="text" size="80" value="{$share_url_raw}"></td>
          </tr>
          <tr>
          <td style='vertical-align: middle; text-align: right; padding: 0 5px 9px 0;'>{lang_print id=13500191}: </td><td><input type="text" onClick="javascript:this.focus();this.select();" class="text" size="80" value='{$share_embed}'></td>
          </tr>
          <tr>
          <td style='vertical-align: middle; text-align: right; padding: 0 5px 9px 0;'>{lang_print id=13500192}: </td><td><input type="text" onClick="javascript:this.focus();this.select();" class="text" size="80" value="[url={$share_url_raw}]{$share_title_raw}[/url]"></td>
          </tr>
          </table>
    </div>
    </div>
    </div>
    <div id="vid_report_blind" style="display: none;">
    <div id="vid_report" style="border: 1px solid #DDDDDD; border-top: 0;">
    <div id="report_container" style='text-align:left;overflow: hidden; padding: 10px; background: #F7F7F7;'>
    {if $user->user_exists}
  {lang_print id=858}
  <br />
  <br />

  <div><b>{lang_print id=859}</b></div>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' class='report_reason' id='report_type1' value='1' checked='checked'></td>
  <td><label for='report_type1'>{lang_print id=860}</td>
  </tr>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' class='report_reason' id='report_type2' value='2'></td>
  <td><label for='report_type2'>{lang_print id=861}</td>
  </tr>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' class='report_reason' id='report_type3' value='3'></td>
  <td><label for='report_type3'>{lang_print id=862}</td>
  </tr>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' class='report_reason' id='report_type0' value='0'></td>
  <td><label for='report_type0'>{lang_print id=863}</td>
  </tr>
  </table>
  
  <br>

  <div><b>{lang_print id=864}</b></div>
  <textarea id='report_details' name='report_details' rows='5' cols='50'></textarea>

  <br><br>


  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='button' id="report_button" class='button' value='{lang_print id=865}'>&nbsp;
  </td>
  </tr>
  </table>
    {else}
    {lang_sprintf id=13500185 1=$not_report}{/if}
    </div>
    </div>
    </div>
    <div id="vid_favorite_blind" style="display: none;">
    <div id="vid_favorite" style="border: 1px solid #DDDDDD; border-top: 0;">
    <div id="favorite_container" style='text-align:left;overflow: hidden; padding: 10px; background: #F7F7F7;'>
    {if !$user->user_exists}{lang_sprintf id=13500185 1=$not_favorite}{/if}
    </div>
    </div>
    </div>

    <div class='portal_spacer'></div>

  {* COMMENTS *}
  <div id="vid_{$vids_array.id}_postcomment"></div>
  <div id="vid_{$vids_array.id}_comments" style='margin-left: auto; margin-right: auto;'></div>

      
  {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
      
        <script type='text/javascript'>
        
    SocialEngine.vidComments = new SocialEngineAPI.Comments({ldelim}
      'canComment' : {if $allowed_to_comment}true{else}false{/if},
      'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
      'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},

      'type' : 'vid',
      'typeIdentifier' : 'vid_id',
      'typeID' : {$vids_array.id},
          
      'typeTab' : 'vids',
      'typeCol' : 'vid',
          
      'initialTotal' : {$total_comments|default:0}
    {rdelim});
        
    SocialEngine.RegisterModule(SocialEngine.vidComments);
       
    // Backwards
    function addComment(is_error, comment_body, comment_date)
    {ldelim}
      SocialEngine.vidComments.addComment(is_error, comment_body, comment_date);
    {rdelim}
        
    function getComments(direction)
    {ldelim}
      SocialEngine.vidComments.getComments(direction);
    {rdelim}

        </script>

</div>

{* BEGIN RIGHT COLUMN *}
<div style='float: right; width: 250px;'>
<div style="padding-bottom: 10px;">
    <div class='header'>{lang_print id=13500070}</div>
    <div class='portal_content'>
        <a href='{$url->url_create('profile', $owner->user_info.user_username)}'><div style='float: left; margin: 0px 10px 0px 0px;'><img src='{$owner->user_photo('./images/nophoto.gif', TRUE)}' class='photo' width='60' height='60' border='0'></div></a>
        <div>{lang_print id=13500095} <a href='{$url->url_create('profile', $owner->user_info.user_username)}'>{$owner->user_displayname}</a><br>
        {lang_print id=13500071} {$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $datetime->timezone($vids_array.date, $global_timezone))}<br>
        </div>
<div id='min1' style='margin-bottom: 10px;'><div id='min1_icon' name='min1_icon'>(<a href='javascript:void(0)' style="text-decoration: none; border-bottom: 1px dotted;">{lang_print id=13500072}</a>)</div></div>
<br>
        <div id='descshortbox' name='descshortbox' style='margin-bottom: 7px; display: inline;'><img src='./images/icons/start_quote.gif'> {$vids_array.desc_short} <img src='./images/icons/end_quote.gif'></div>
        <div id='box' name='box' style='display: none'>
            <div id='descbox' name='descbox' style='margin-bottom: 7px;'><img src='./images/icons/start_quote.gif'> {$vids_array.desc} <img src='./images/icons/end_quote.gif'></div>
            {if $vids_array.cat_lang}<div id='catbox' name='catbox' style='margin-bottom: 7px;'><font style='padding-right: 5px;'>{lang_print id=13500074}</font><a href='browse_vids.php?c={$vids_array.cat_id}' style='margin-right: 5px;'>{lang_print id=$vids_array.cat_lang}</a></div>{/if}
            <div id='tagbox' name='tagbox' style='margin-bottom: 7px;'><font style='padding-right: 5px;'>{lang_print id=13500075}</font>
            {section name=tag loop=$vids_array.tags}
                <a href='browse_vids.php?q={$vids_array.tags[tag]}&type=tag' style='margin-right: 5px;'>{$vids_array.tags[tag]}</a>
            {/section}
            </div>
        </div>

  {literal}
  <script type="text/javascript">
  <!-- 
  window.addEvent('domready', function() {
    if(menu_minimized.get('cookie') == 0) { 
      $('descshortbox').style.display='inline';
      $('box').style.display='none';
      $('min1_icon').innerHTML = "(<a href=\"javascript:void(0);\" style=\"text-decoration: none; border-bottom: 1px dotted;\">{/literal}{lang_print id=13500072}{literal}</a>)";
    }
    $('min1').addEvent('click', function(e){
	e = new Event(e);
	if(menu_minimized.get('cookie') == 1) {
	  menu_minimized.set('cookie', 0);
	  $('descshortbox').style.display='inline';
          $('box').style.display='none';
          $('min1_icon').innerHTML = "(<a href=\"javascript:void(0);\" style=\"text-decoration: none; border-bottom: 1px dotted;\">{/literal}{lang_print id=13500072}{literal}</a>)";
	} else {
	  menu_minimized.set('cookie', 1);
	  $('descshortbox').style.display='none';
          $('box').style.display='inline';
          $('min1_icon').innerHTML = "(<a href=\"javascript:void(0);\" style=\"text-decoration: none; border-bottom: 1px dotted;\">{/literal}{lang_print id=13500073}{literal}</a>)";
	}
	e.stop();
    });
  });
  //-->
  </script>
  {/literal}
    </div>
</div>


{if $vid_settings.logo != 0 AND $vids_array.type != 'self'}
<div style="padding-bottom: 10px;">
    <div class='header'>{lang_print id=13500160}</div>
    <div class='portal_content'>
        <center><img src="./images/vid_{if $vids_array.type == 'youtube_api'}youtube{else}{$vids_array.type}{/if}.gif"></center>
    </div>
</div>
{/if}

{if $all_videos[0]}
<div style="padding-bottom: 10px;">
    <div class='header'>{lang_sprintf id=13500056 1=$owner->user_displayname}</div>
    <div class='vid_scroll' style='height: 170px;'>

  {* LOOP THROUGH USER VIDEOS *}
  {section name=test loop=$all_videos}
     <div class='vid_scroll_tab' style='width: 205px;'>
	        <table cellpadding='0' cellspacing='0'>
          <tr>
	        <td style='vertical-align: top;'>
	             <a href='{$url->url_create('vid_file', $owner->user_info.user_username, $all_videos[test].id)}'><img src='{$all_videos[test].img}{if $all_videos[test].type == "self"}_thumb_0.jpg{else}.jpg{/if}' border='0' width='80' height='70'></a>
	        </td>
	        <td style='vertical-align: top; padding-left: 5px;'>
               <div class='vid_row_title'><a href='{$url->url_create('vid_file', $owner->user_info.user_username, $all_videos[test].id)}' title='{$all_videos[test].title}'>{$all_videos[test].title|truncate:20:"...":true}</a></div>
               {if $all_videos[test].cat_lang}<div class='vid_row_info'><a href='browse_vids.php?c={$all_videos[test].cat_id}' style='margin-right: 5px;'>{lang_print id=$all_videos[test].cat_lang}</a></div>{/if}
               <div class='vid_row_info'>{$all_videos[test].views} view(s)</div>
               <div>
               {section name=full_stars start=0 loop=$all_videos[test].full}
                    <img src='./images/icons/star2.gif' border='0'>
               {/section}
               {section name=partial_stars start=0 loop=$all_videos[test].partial}
                    <img src='./images/icons/star2-half.gif' border='0'>
               {/section}
               {section name=empty_stars start=0 loop=$all_videos[test].empty}
                    <img src='./images/icons/star1.gif' border='0'>
               {/section}
  	    	     </div>
	         </td>
	         </tr>
	         </table>
     </div>

    <div style='clear: both; height: 0px;'></div>

  {/section}

{if $total_videos > 10}<div style='text-align: center;'>&nbsp;[ <a href='{$url->url_create('vids', $owner->user_info.user_username, 1)}'>{lang_print id=13500092}</a> ]</div>
<div class='portal_spacer'></div>{/if}

    </div>
</div>
{/if}

{if $all_rel_videos[0]}
<div style="padding-bottom: 10px;">
    <div class='header'>{lang_print id=13500076}</div>
    <div class='vid_scroll' style='height: 170px;'>

  {* LOOP THROUGH RELATED VIDEOS *}
  {section name=test2 loop=$all_rel_videos}
     <div class='vid_scroll_tab' style='width: 205px;'>
	        <table cellpadding='0' cellspacing='0'>
          <tr>
	        <td style='vertical-align: top;'>
	             <a href='{$url->url_create('vid_file', $all_rel_videos[test2].username, $all_rel_videos[test2].id)}'><img src='{$all_rel_videos[test2].img}{if $all_rel_videos[test2].type == "self"}_thumb_0.jpg{else}.jpg{/if}' border='0' width='80' height='70'></a>
	        </td>
	        <td style='vertical-align: top; padding-left: 5px;'>
               <div class='vid_row_title'><a href='{$url->url_create('vid_file', $all_rel_videos[test2].username, $all_rel_videos[test2].id)}' title='{$all_rel_videos[test2].title}'>{$all_rel_videos[test2].title|truncate:20:"...":true}</a></div>
               {if $all_rel_videos[test2].cat_id}<div class='vid_row_info'><a href='browse_vids.php?c={$all_rel_videos[test2].cat_id}' style='margin-right: 5px;'>{lang_print id=$all_rel_videos[test2].cat_lang}</a></div>{/if}
               <div class='vid_row_info'>{$all_rel_videos[test2].views} view(s)</div>
               <div>
               {section name=full_stars start=0 loop=$all_rel_videos[test2].full}
                    <img src='./images/icons/star2.gif' border='0'>
               {/section}
               {section name=partial_stars start=0 loop=$all_rel_videos[test2].partial}
                    <img src='./images/icons/star2-half.gif' border='0'>
               {/section}
               {section name=empty_stars start=0 loop=$all_rel_videos[test2].empty}
                    <img src='./images/icons/star1.gif' border='0'>
               {/section}
  	    	     </div>
	         </td>
	         </tr>
	         </table>
     </div>

    <div style='clear: both; height: 0px;'></div>

  {/section}

    </div>
</div>
{/if}

</div>

</div>

{include file='footer.tpl'}