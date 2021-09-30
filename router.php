<?php
include_once('config.php');
$p = (isset($_GET['p']) ? strtolower($_GET['p']) : '');
logDebug('router, p='.$p);
if(empty($p)){//default
	$p = 'home';
}elseif(substr($p, -4) === '.php'){//strip .php
	$p = (substr($p, 0, -4));
}

$queryArray = explode('/', $p);
$page = (!empty($queryArray) ? $queryArray[0] : 'home');//shouldn't be necessary, but maybe $_GET['p'] is NULL
logDebug('page='.$page);

if(file_exists(HTMLS_PATH.$page.'.php') !== true){
	logDebug('page doesnt exist: '.$page);
	$page = 'home';
}