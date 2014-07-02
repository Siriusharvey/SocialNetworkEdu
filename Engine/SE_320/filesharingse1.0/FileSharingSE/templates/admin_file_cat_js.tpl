
{literal}
<script type="text/javascript">

//New functions add by iopl

function adduploadcat() {
	var catarea = $('categories');
	var newdiv = document.createElement('div');
	newdiv.id = 'cat_new';
	newdiv.innerHTML ='<div style="font-weight: bold;"><img src="../images/folder_open_yellow.gif" border="0" class="handle_cat" style="vertical-align: middle; margin-right: 5px; cursor: move;"><span id="cat_new_span"><input type="text" id="cat_new_input" name="cat_new_input" maxlength="100" ></span></div>';
	catarea.appendChild(newdiv);
	var catinput = $('cat_new_input');
	catinput.focus();
}

function saveuploadcat(catid, oldcat_title, cat_dependency) {
	var catinput = $('cat_'+catid+'_input'); 
	if(catinput.value == "" && catid == "new") {
	  removecat(catid);
	} else {
		 val=catinput.value;	
	  document.uploadfrm.action = 'admin_file.php?action=edit_cat&cat='+catid+'&name='+val;
	  document.uploadfrm.submit();	
	}
}

function edituploadcat(catid, cat_dependency) {
	var catspan = $('cat_'+catid+'_span'); 
	var cattitle = $('cat_'+catid+'_title');
	catspan.innerHTML = '<input type="text" id="cat_'+catid+'_input" maxlength="100" onBlur="saveuploadcat(\''+catid+'\', \''+cattitle.innerHTML.replace(/'/g, "&amp;#039;")+'\', \''+cat_dependency+'\')" onkeypress="return noenter_cat(\''+catid+'\', event)" value="'+cattitle.innerHTML+'">';
	var catinput = $('cat_'+catid+'_input'); 
	catinput.focus();
}
//---------------------xxxxxxxxxxxxxxxxxx------------------



</script>
{/literal}
