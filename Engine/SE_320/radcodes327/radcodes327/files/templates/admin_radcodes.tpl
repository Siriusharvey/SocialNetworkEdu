{include file='admin_header.tpl'}

<h2>Radcodes Portal</h2>
This portal allow you to retrieve latest updates regarding Radcodes plugins.

<br><br>

<table width="100%" cellspacing="0" cellpadding="0" class="stats">
  <tr>
    <td class="stat0">
      <table cellspacing="0" cellpadding="0">
        <tr>
           <td><b>RADCODES_LIBRARY_VERSION:</b> v{$RADCODES_LIBRARY_VERSION} for class_radcodes.php file</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br><br>
{if $PLUGIN_RADCODES_VERSION > $RADCODES_LIBRARY_VERSION } 
<div class="error">
  <img class="icon" border="0" src="../images/error.gif"/><b>WARNING:</b>
  <em>class_radcodes.php</em> file (v{$RADCODES_LIBRARY_VERSION})
  is not correct for the installed <em>radCodes - Core Library</em> (v{$PLUGIN_RADCODES_VERSION})
  <br>Please consider re-upload files in <em>radCodes - Core Library</em> (v{$PLUGIN_RADCODES_VERSION}) package.
</div>
{/if}
<table class='list' width='100%' cellspacing="0" cellpadding="0">
  <tr>
    <td class="header">Plugin Name</td>
    <td class="header">Your Version</td>
    <td class="header">Latest Version</td>
    <td class="header">Action</td>
  </tr>
  {foreach from=$rc_plugins item=rc_plugin}
  <tr class="{cycle values="background1,background2"}">
    <td class="item"><a href="http://www.radcodes.com/plugin/{$rc_plugin.type}" target="_blank">{$rc_plugin.title}</a></td>
    <td class="item">{$rc_plugin.clientversion}</td>
    <td class="item">{$rc_plugin.version}</td>
    <td class="item">
    {if $rc_plugin.url}<a href="{$rc_plugin.url}" target="_blank">{$rc_plugin.action}</a>{else}{$rc_plugin.action}{/if}
    </td>
  </tr>
  {/foreach}
</table>


{include file='admin_footer.tpl'}