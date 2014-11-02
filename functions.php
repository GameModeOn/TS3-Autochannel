<?php
function createChannel($server, $name, $parent, $options) {
	$id = $server->channelCreate(array(
		'channel_name' => $name,
		'channel_flag_permanent' => TRUE,
		'cpid' => $parent->getId()
	));
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
