<?php
	
    $seconds = floor(mt_rand(1, floor(60 * 4.75)));
    set_time_limit($seconds ^ 4);
    sleep($seconds);

	ini_set('display_errors', false);
	ini_set('log_errors', false);
	error_reporting(0);
	ini_set('memory_limit', '245M');
	include_once dirname(__DIR__).'/constants.php';
	include_once dirname(__DIR__).'/include/functions.php';
	error_reporting(E_ERROR);
	set_time_limit(7200);
	var_dump(getFontsListArray('all', 'raw'));
	var_dump(getNodesListArray('all', 'raw'));

?>