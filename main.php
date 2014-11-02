<?php
$exceptions = array_unique(array_merge($exceptions, $roots));

foreach($roots as $root) {
	$root = $server->channelGetByName($root);
	$subs = $root->subChannelList();
	foreach($subs as $sub) {
		if(catchExceptions($sub['channel_name'], $exceptions)) {
			continue;
		}

		$groups = $sub->subChannelList();
		$groupCount = count($groups);
	
		$delete = false;
		$i = 0;
		foreach($groups as $group) {
			if(catchExceptions($group['channel_name'], $exceptions)) {
				$groupCount--;
				continue;
			}
			$i++;
			if($delete == true AND $group['total_clients'] == 0) {
				$group->delete();
			}
			if($group['total_clients'] == 0) {
				$delete = true;
			}
			if($i == $groupCount AND $delete == false) {
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
