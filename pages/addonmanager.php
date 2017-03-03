<?php
if (!defined('BLARG')) die();

$title = "Add-on Manager";

CheckPermission('admin.editsettings');

MakeCrumbs(array(actionLink("admin") => __("Admin"), actionLink("addonmanager") => __("Add-on Manager")));


$cell = 0;
$pluginsDir = @opendir(BOARD_ROOT."add-ons");

$enabledplugins = array();
$disabledplugins = array();
$pluginDatas = array();

if($pluginsDir !== FALSE) {
	while(($plugin = readdir($pluginsDir)) !== FALSE) {
		if($plugin == "." || $plugin == "..") continue;
		if(is_dir(BOARD_ROOT."plugins/".$plugin)) {
			try {
				$plugindata = getPluginData($plugin, false);
			}
			catch(BadPluginException $e) {
				continue;
			}

			$pluginDatas[$plugin] = $plugindata;
			if(isset($plugins[$plugin]))
				$enabledplugins[$plugin] = $plugindata['name'];
			else
				$disabledplugins[$plugin] = $plugindata['name'];
		}
	}

}

asort($enabledplugins);
asort($disabledplugins);

$ep = array();
$dp = array();

foreach($enabledplugins as $plugin => $pluginname)
	$ep[] = listPlugin($plugin, $pluginDatas[$plugin]);

foreach($disabledplugins as $plugin => $pluginname)
	$dp[] = listPlugin($plugin, $pluginDatas[$plugin]);

RenderTemplate('pluginlist', array('enabledPlugins' => $ep, 'disabledPlugins' => $dp));


function listPlugin($plugin, $plugindata) {
	global $plugins, $loguser;

	$pdata = $plugindata;

	$hasperms = false;
	if (!isset($plugins[$plugin]) && file_exists(BOARD_ROOT.'plugins/'.$plugin.'/permStrings.php'))
		$hasperms = true;

	if ($hasperms)
		$pdata['description'] .= '<br><strong>This plugin has permissions. After enabling it, make sure to configure them properly.</strong>';


	$text = __("Enable");
	$act = "enable";
	if(isset($plugins[$plugin])) {
		$text = __("Disable");
		$act = "disable";
	}
	$pdata['actions'] = '<ul class="pipemenu">'.actionLinkTagItem($text, "pluginmanager", $plugin, "action=".$act."&key=".$loguser['token']);

	if(in_array("settingsfile", $plugindata['buckets'])) {
		if(isset($plugins[$plugin]))
			$pdata['actions'] .= actionLinkTagItem(__("Settings&hellip;"), "editsettings", $plugin);
	}
	$pdata['actions'] .= '</ul>';

	return $pdata;
}

$enabledfile = BOARD_ROOT.'plugins/'.$plugin.'/enabled.txt';

if($_REQUEST['action'] == "enable") {
	if($_REQUEST['key'] != $loguser['token'])
		Kill("No.");

	Query("insert into {enabledplugins} values ({0})", $_REQUEST['id']);
	require(BOARD_ROOT.'db/functions.php');
	Upgrade();

	die(header("location: ".actionLink("pluginmanager")));

	//Make a new file for easier detecting that it is enabled
	if (!file_put_contents($enabledfile, 'This is a holdertext file that signifies that this add-on is enabled. Don\'t delete this file.')){
		Report("[b]".$loguser['name']."[/] tried to add a add-on called "$_REQUEST['id']" but failed.", false);
		Alert(__("Sorry, but the add-on couldn't be added by our file detection usage. Please report this to the website's owner."), __("Error"));
	} else {
		Report("[b]".$loguser['name']."[/] successfully added an add-on called "$_REQUEST['id']".", false);
		Alert(__("You have successfully added the add-on."), __("Success"));
	}
}

if($_REQUEST['action'] == "disable") {
	if($_REQUEST['key'] != $loguser['token'])
		Kill("No.");

	Query("delete from {enabledplugins} where plugin={0}", $_REQUEST['id']);
	die(header("location: ".actionLink("pluginmanager")));

	$pluginsDir = @opendir(BOARD_ROOT."plugins/".$plugin);

	//Delete the enabled text.
	if (file_exists($enabledfile)) {
		if(!unlink($enabledfile)) {
			Report("[b]".$loguser['name']."[/] tried to remove a add-on called "$_REQUEST['id']" but failed.", false);
			Alert(__("Sorry, but the add-on couldn't be removed by our file detection usage. Please report this to the website's owner."), __("Error"));
		} else {
			Report("[b]".$loguser['name']."[/] successfully removed an add-on called "$_REQUEST['id']".", false);
			Alert(__("You have successfully removed the add-on."), __("Success"));
		}
	}
}