<?php
	
	ini_set('display_errors', false);
	ini_set('log_errors', false);
	error_reporting(0);
	ini_set('memory_limit', '245M');
	include_once dirname(dirname(__FILE__)).'/functions.php';
	include_once dirname(dirname(__FILE__)).'/class/fontages.php';
	error_reporting(E_ERROR);
	set_time_limit(7200);
	var_dump(getFontsListArray('all', 'raw'));
	var_dump(getNodesListArray('all', 'raw'));

?>