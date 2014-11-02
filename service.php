<?php
require_once('config.inc.php');
require_once('functions.php');
require_once($framework);

$server = TeamSpeak3::factory('serverquery://'.$user.':'.$passwd.'@'.$host.':'.$queryport.'/?server_port='.$voiceport);

while(true) {
	require_once('main.php');
}
