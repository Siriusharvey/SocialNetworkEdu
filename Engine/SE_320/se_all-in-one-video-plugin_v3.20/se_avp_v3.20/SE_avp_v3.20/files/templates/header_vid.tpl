<link rel="stylesheet" href="./templates/styles_vid.css" title="stylesheet" type="text/css">
<script type="text/javascript" src="./include/js/swfobject.js"></script>
<script type="text/javascript" src="SolmetraUploader.js"></script>
{literal}
<script type="text/javascript">
<!--
// ADD ABILITY TO MINIMIZE/MAXIMIZE MENU TABLES
var menu_minimized = new Hash.Cookie('menu_cookie', {duration: 3600});
menu_minimized.set('cookie', 0);

function VidValidate(thing) {
    var error = true;
    //prevent the page from changing
    if ($('vid_title').value=='') {
         error = false;
    }
    if ($('vid_desc').value=='') {
         error = false;
    }
    if ($('vid_tags').value=='') {
         error = false;
    }
    if ($(thing).value=='') {
         error = false;
    }
    if (error === true) {
         $('notvalid').style.display='none';
         $('bar').style.visibility='visible';
    } else {
         $('notvalid').style.display='inline';
    }
    return error;
}
//-->
</script>
{/literal}