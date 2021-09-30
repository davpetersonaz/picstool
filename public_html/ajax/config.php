<?php
include_once('../../config.php');
logDebug('ajax/config, POST: '.var_export($_POST, true));

if(isset($_POST['browser_title'], $_POST['homepage_header'], $_POST['homepage_description'])){
	logDebug('updating config');
	$values = array('browser_title'=>$_POST['browser_title'], 'homepage_header'=>$_POST['homepage_header'], 'homepage_description'=>$_POST['homepage_description']);
	$config->updateConfig($values);
	?><script>	location.assign('/config.php');</script><?php
}

exit;