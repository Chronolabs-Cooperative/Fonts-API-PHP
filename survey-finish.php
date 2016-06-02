<?php
/**
 * Chronolabs Fonting Repository Services REST API API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         fonts
 * @since           2.1.9
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Fonting Repository Services REST API
 * @link			http://sourceforge.net/projects/chronolabsapis
 * @link			http://cipher.labs.coop
 */

	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source;
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';
	
	session_start();
	
	$key = (isset($_REQUEST['key'])?$_REQUEST['key']:md5(NULL));
	$_SESSION['survey'][$key] = array();
	$others = array();
	$sql = "SELECT * FROM `flows_history` WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'";
	if ($result = $GLOBALS['FontsDB']->queryF($sql))
	{
		$history = $GLOBALS['FontsDB']->fetchArray($result);
		$survey = json_decode($history['data'], true);
		$sql = "SELECT *, md5(concat(`key`, `flow_id`)) as `fingering` FROM `flows_history` WHERE `flow_id` = '".$history['flow_id'] . "' AND `started` = '0'";
		if ($result = $GLOBALS['FontsDB']->queryF($sql))
		{
			
			while( $row = $GLOBALS['FontsDB']->fetchArray($result) ) 
			{
				$upload = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF('SELECT * from `uploads` WHERE `id` = "' . $row['upload_id'].'"'));
				$data = json_decode($upload['datastore'], true);
				$others[$row['fingering']] = $data['FontName'];
			}
		}
		$flow = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF('SELECT * from `flows` WHERE `flow_id` = "' . $history['flow_id'].'"'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<?php 	$servicename = "Fonting Repository Services"; 
		$servicecode = "FRS"; ?>
	<meta property="og:url" content="<?php echo (isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"]; ?>" />
	<meta property="og:site_name" content="<?php echo $servicename; ?> Open Services API's (With Source-code)"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="rating" content="general" />
	<meta http-equiv="author" content="wishcraft@users.sourceforge.net" />
	<meta http-equiv="copyright" content="Chronolabs Cooperative &copy; <?php echo date("Y")-1; ?>-<?php echo date("Y")+1; ?>" />
	<meta http-equiv="generator" content="wishcraft@users.sourceforge.net" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="//labs.partnerconsole.net/execute2/external/reseller-logo">
	<link rel="icon" href="//labs.partnerconsole.net/execute2/external/reseller-logo">
	<link rel="apple-touch-icon" href="//labs.partnerconsole.net/execute2/external/reseller-logo">
	<meta property="og:image" content="//labs.partnerconsole.net/execute2/external/reseller-logo"/>
	<link rel="stylesheet" href="/style.css" type="text/css" />
	<link rel="stylesheet" href="//css.ringwould.com.au/3/gradientee/stylesheet.css" type="text/css" />
	<link rel="stylesheet" href="//css.ringwould.com.au/3/shadowing/styleheet.css" type="text/css" />
	<title><?php echo $servicename; ?> (<?php echo $servicecode; ?>) Survey Finished || Chronolabs Cooperative</title>
	<meta property="og:title" content="<?php echo $servicecode; ?> API"/>
	<meta property="og:type" content="<?php echo strtolower($servicecode); ?>-api"/>
</head>
<body>
<div class="main">
    <div style="margin-top: 19px; clear: both; padding: 8px;">
    	<h1>Font Survey finished <?php $flow['name']; ?>!</h1>
    	<p><font style="font-size: 132%">You have finished the survey for the font: <strong><em><?php echo $survey['reserves']['fontname']; ?></em></strong> You have <?php  echo (count($others) > 0 ? "More Survey's to do select one below to continue!":"No More Survey's!!!"); ?></font></p>
	    <?php if (count($others) > 0) { ?>
	    <h2><?php echo count($others); ?> Surveys left to do!!</h2>
	    <p>
	    	<ol>
	    		<?php foreach ($others as $finger => $name) { ?>
	    		<li>Survey Font: <a href="<?php echo "$source/v2/survey/page-1/$finger/html.api"; ?>"><?php echo $name; ?></a></li>
	    		<?php } ?>
	    	</ol>
	    </p>
	    <?php } ?>
	</div>
</div>
</html>
<?php 
	}
	