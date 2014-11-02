<?php
require_once('config.inc.php');
require_once($framework);

$exceptions = array_unique(array_merge($exceptions, $roots));

$server = TeamSpeak3::factory('serverquery://'.$user.':'.$passwd.'@'.$host.':'.$queryport.'/?server_port='.$voiceport);

foreach($roots as $root) {
	$root = $server->channelGetByName($root);
	$subs = $root->subChannelList();
	foreach($subs as $sub) {
		if(catchExceptions($sub['channel_name'], $exceptions)) {
			continue;
		}

		$groups = $sub->subChannelList();
		$groupCount = count($groups);
		
		$availablechannel = false;
		$i = 0;
		foreach($groups as $group) {
			if(catchExceptions($group['channel_name'], $exceptions)) {
				$groupCount--;
				continue;
			}
			$i++;
			if($group['total_clients'] == 0) {
				$availablechannel = true;
			}
			if($i == $groupCount AND $availablechannel = false) {
				$regex = '#([0-9]{1,3})#e';
				$replacement = '("$1" + 1)';
				$newName = preg_replace($regex, $replacement, $group['channel_name']);
				createChannel($server, $newName, $sub, $options);
			}
		}
		if(empty($groups) OR $i == 0) {
			createChannel($server, $default, $sub, $options, array('channel_flag_permanent' => TRUE));
		}
	}
}

function createChannel($server, $name, $parent, $options, $addparam = array()) {
	$parameters = array(
		'channel_name' => $name, 
		'cpid' => $parent->getId()
	);
	$parameters = array_merge($parameters, $addparam);
	
	$id = $server->channelCreate($parameters);
	
	if($options['inherit_icons']) {
		$channel = $server->channelGetById($id);
		$channel->modify(array('channel_icon_id' => $parent->getProperty('channel_icon_id')));
	}
}
function catchExceptions($name, $excpetions) {
	foreach($excpetions as $exception) {
		if($name == $exception) {
			return true;
		}
	}
	return false;
}
