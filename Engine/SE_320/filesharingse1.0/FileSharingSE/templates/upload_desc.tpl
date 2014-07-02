{include file='header.tpl'}

{* $Id: upload_dec.tpl  *}

{* JAVASCRIPT *}
{lang_javascript ids=861,7800121,7800123}
<!--
<script type="text/javascript" src="./include/js/class_fileupload.js"></script>
//-->
<script type="text/javascript">
  /*
  SocialEngine.File= new SocialEngineAPI.File();
  SocialEngine.RegisterModule(SocialEngine.File);
  */
</script>


<div class='page_header'>
{lang_sprintf id=7800143 2=$fname 3=$lname 1=$uname}
</div>
{* NO Files AT ALL *}
  	{if $exist eq 'not exist'}
   	 <br />
   	 <table cellpadding='0' cellspacing='0' align='center'>
   	   <tr>
    		    <td class='result'>
        	  <img src='./images/icons/bulb16.gif' border='0' class='icon' />
          		{lang_print id=7800134}
        	   </td>
          </tr>
  	 </table>
	{/if}

<div class='fileupload' style='width: auto;padding-bottom:10px;'>
 <table cellpadding='0' cellspacing='0' width='100%'>
   {section name=user loop=$uploads}
     <tr>

	<td valign="top">
	<div class="fileupload_photo">		
                	{if $uploads[user].userupload_userthumbs neq '' } 
			 <img alt='Thumbnail' border="0" src="./userthumbs/thumbnail/{$uploads[user].userupload_userthumbs}"/>
                	{else}
			<img width="100" height="100" border="0" src="./images/nophoto.gif"/>
			{/if}	
		</div>
	</td>
	<td width="100%" style='padding-left:10px;'>
        <div class="fileupload_title">
        {$uploads[user].userupload_title}
	  </div>
         <div>
         {$uploads[user].userupload_description|nl2br}
        </div>
      	<div>
		{lang_print id=7800043}{math equation="size / 1024" size=$uploads[user].userupload_filesize format="%.2f"} KB
        </div>
	<div class="fileupload_stats">
              	{* days or time ago *}

		 {assign var='upload_datecreated' value=$datetime->time_since($uploads[user].userupload_time)}

		{capture assign="created"}{lang_sprintf id=$upload_datecreated[0] 1=$upload_datecreated[1]}{/capture}
        	
		{assign var='upload_dateupdated' value=$datetime->time_since($uploads[user].modified_at)}
              	{capture assign="updated"}{lang_sprintf id=$upload_dateupdated[0] 1=$upload_dateupdated[1]}{/capture}
      	
		 {lang_sprintf id=7800135 1=$created}
		{if $uploads[user].modify != 0}
                - {lang_sprintf id=7800136 1=$updated}
            	  {/if}	
		
		</div>  
	<div>
	{* No. of downloads *}
	
	{ if $uploads[user].userfiledownload_count eq ''}
	0
	{else}
	{$uploads[user].userfiledownload_count}
	{/if}
	 downloads 
	</div>
{if $user_id eq ''}
	<div class='fileupload_download'>
<a  href='./login.php' style='text-decoration:none;' ><img class='fileupload_download' alt='{lang_print id=7800073}' title='{lang_print id=7800103}' src="./images/icons/download_icon_new.gif" border='0' /></a>
	</div>
{elseif $user_id neq $uploads[user].userupload_userid}
	<div class='fileupload_download'>
<a  href='getDownload.php?fid={$uploads[user].userupload_id}&t={$uploads[user].userupload_filetype}&name={$uploads[user].userupload_userfiles}' style='text-decoration:none;' ><img style='width:auto;height:auto;' alt='{lang_print id=7800073}' title='{lang_print id=7800073}' src="./images/icons/download_icon_new.gif" border='0' /></a>

	</div>
{/if}

	<div id='star-rating'>
	<ul class='star-rating' >
	{if $current_rating neq ''}
	<li class='current-rating' style='width:{$current_rating}px;'></li>
	{/if}
{if $user_id neq $uploads[user].userupload_userid && $user_id neq ''}
	<li><a href='javascript:void(0);' onclick='return starRate(1,{$uploads[user].userupload_id},{$user_id});' title='1 star out of 5' class='one-star'>1</a></li>
	<li><a href='javascript:void(0);' onclick='return starRate(2,{$uploads[user].userupload_id},{$user_id});' title='2 stars out of 5' class='two-stars'>2</a></li>
	<li><a href='javascript:void(0);' onclick='return starRate(3,{$uploads[user].userupload_id},{$user_id});' title='3 stars out of 5' class='three-stars'>3</a></li>
	<li><a href='javascript:void(0);' onclick='return starRate(4,{$uploads[user].userupload_id},{$user_id});' title='4 stars out of 5' class='four-stars'>4</a></li>
	<li><a href='javascript:void(0);' onclick='return starRate(5,{$uploads[user].userupload_id},{$user_id});' title='5 stars out of 5' class='five-stars'>5</a></li>
{/if}
	</ul>
	</div>
         </td>
    </tr>
{/section}
</table>
   </div>
<br />


<div style='margin-bottom: 20px;'>
  <div class='button' style='float: left;'>

    <a href="{if $user->user_exists != 0}./{$smarty.session.back} {else} ./browse_upload.php {/if}"><img src='./images/icons/back16.gif' border='0' class='button' />{lang_print id=7800125}</a>

  </div>
  <div style='clear: both; height: 0px;'></div>
</div>
<br />

{* COMMENTS *}

{* Close Comments *}
{literal}


<script type="text/javascript">
function starRate(rate,uid,user_id){
//alert(rate);
var vote;
var id;
new Ajax2.Updater('star-rating', 'star_rate.php?vote='+rate+'&id='+uid+'&user_id='+user_id
, {evalScripts:true}); 
}

</script>
{/literal}
{literal}
<style language='css/text'>
/*             styles for the star rater                */	
	.star-rating{
		list-style:none;
		margin: 0px;
		padding:0px;
		width: 125px;
		height: 25px;
		position: relative;
		overflow:hidden;
		background: url(./images/icons/alt_star.gif) top left repeat-x;		
	}
	.star-rating li{
		padding:0px;
		margin:0px;
		width:25px;
		height:25px;
		float: left;
	}
	.star-rating li a{
		display:block;
		width:25px;
		height: 25px;
		line-height:25px;		
		text-decoration: none;
		text-indent: -9000px;
		z-index: 20;
		position: absolute;
		padding: 0px;
		overflow:hidden;
	}
	.star-rating li a:hover{
		background: url(./images/icons/alt_star.gif) left bottom;
		z-index: 2;
		left: 0px;
		border:none;
	}
	.star-rating a.one-star{
		left: 0px;
	}
	.star-rating a.one-star:hover{
		width:25px;
	}
	.star-rating a.two-stars{
		left:25px;
	}
	.star-rating a.two-stars:hover{
		width: 50px;
	}
	.star-rating a.three-stars{
		left: 50px;
	}
	.star-rating a.three-stars:hover{
		width: 75px;
	}
	.star-rating a.four-stars{
		left: 75px;
	}	
	.star-rating a.four-stars:hover{
		width: 100px;
	}
	.star-rating a.five-stars{
		left: 100px;
	}
	.star-rating a.five-stars:hover{
		width: 125px;
	}
	.star-rating li.current-rating{
		background: url(./images/icons/alt_star.gif) left center;
		position: absolute;
		height: 25px;
		display: block;
		text-indent: -9000px;
		z-index: 1;
	}
	
	/* remove halo effect in firefox   */
	a:active{
		outline: none;
	}	

</style>
{/literal}
{include file='footer.tpl'}
