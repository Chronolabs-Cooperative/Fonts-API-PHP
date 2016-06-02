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

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ALL);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '128M');
global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source, $ipid;
require __DIR__ . DIRECTORY_SEPARATOR . 'header.php';

$key = (isset($_REQUEST['key'])?$_REQUEST['key']:md5(NULL));
$mode = (isset($_POST['mode'])?$_POST['mode']:(isset($_REQUEST['mode'])?$_REQUEST['mode']:'start'));

$error = "";
switch($mode)
{
	case "verify":
		$name = (isset($_REQUEST['name'])?$_REQUEST['name']:'');
		$opt = (isset($_REQUEST['opt'])?$_REQUEST['opt']:'optin');
		if (empty($name))
			$error = "You must specify your name for the contributor notes!";
		
		$sql = "SELECT `flow_id` FROM `flows` WHERE `email` = '" . mysql_escape_string($_REQUEST['email']) . "'";
		if ($flowid = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql)) || empty($error))
		{
			$sql = "SELECT md5(concat(`key`, `flow_id`)) as `fingers` FROM `flows_history` WHERE `key` = '" . $key . "' AND  `flow_id` = '" . $flowid . "'";
			if ($history = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql)))
			{
				switch($opt)
				{
					case "optin":
						$GLOBALS['FontsDB']->queryF("UPDATE `flows` SET `name` = '" . mysql_escape_string($name) . "', `participate` = 'yes' where `flow_id` = '" . $flowid . "'");
						header("Location: " . API_URL . "/v2/survey/page-1/".$history['fingers']."/html.api");
						exit(0);
						break;
					case "optout":
						$GLOBALS['FontsDB']->queryF("UPDATE `flows` SET `name` = '" . mysql_escape_string($name) . "', `participate` = 'no' where `flow_id` = '" . $flowid . "'");
						header("Location: " . API_URL . "/v2/survey/optout/html.api");
						exit(0);
						break;
				}
			} else {
				$error = "The email address you specified is not on our records for this survey!";
			}
		} else {
			$error = "The email address you specified is not on our records!";
		}
		break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<?php 	
	$servicename = "Fonting Repository Services"; 
		$servicecode = "FRS"; ?>
	<meta property="og:url" content="<?php  echo (isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"]; ?>" />
	<meta property="og:site_name" content="<?php echo $servicename; ?> Open Services API's (With Source-code)"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="rating" content="general" />
	<meta http-equiv="author" content="wishcraft@users.sourceforge.net" />
	<meta http-equiv="copyright" content="Chronolabs Cooperative &copy; <?php  echo date("Y")-1; ?>-<?php echo date("Y")+1; ?>" />
	<meta http-equiv="generator" content="wishcraft@users.sourceforge.net" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="//labs.partnerconsole.net/execute2/external/reseller-logo">
	<link rel="icon" href="//labs.partnerconsole.net/execute2/external/reseller-logo">
	<link rel="apple-touch-icon" href="//labs.partnerconsole.net/execute2/external/reseller-logo">
	<meta property="og:image" content="//labs.partnerconsole.net/execute2/external/reseller-logo"/>
	<link rel="stylesheet" href="/style.css" type="text/css" />
	<link rel="stylesheet" href="//css.ringwould.com.au/3/gradientee/stylesheet.css" type="text/css" />
	<link rel="stylesheet" href="//css.ringwould.com.au/3/shadowing/styleheet.css" type="text/css" />
	<title><?php echo $servicename; ?> (<?php echo $servicecode; ?>) Cateloguing Survey || Chronolabs Cooperative</title>
	<meta property="og:title" content="<?php echo $servicecode; ?> API"/>
	<meta property="og:type" content="<?php echo strtolower($servicecode); ?>-api"/>
</head>
<body style="min-height: 100.99999999996%;">
<div class="main">
    <h1><?php echo $servicename; ?> (<?php echo $servicecode; ?>) Cateloguing Survey || Chronolabs Cooperative</h1>
    <p>Please enter the email address you where contacted on and your name to start the survey or check 'opt-out' to never recieve another survey again on this font repository.</p>
    <?php if (!empty($error)) { ?>
    <h2>Error Occured</h2>
    <p style="color: rgb(197,0, 0); font-size: 130%; font-weight: bold;"><?php echo $error; ?></p>
    <?php } ?>
    <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
	    <label for="email">Contact Email Address:</label>
	    <input type="text" maxlength="198" size="42" name="email" id="email" />
	    <br/>
	    <label for="name">Contact Name:</label>
	    <input type="text" maxlength="198" size="42" name="name" id="name" />
	    <br/>
	    <label for="optin">Opt-in for Suvery's</label>
	    <input type="radio" value="optin" checked="checked" name="opt" id="optin" />
	    &nbsp;&nbsp;&nbsp;&nbsp;
	    <label for="optout">Opt-out for Suvery's</label>
	    <input type="radio" value="optout" name="opt" id="optout" />
	    <br/><br/>
	    <input type="submit" value="Submit" name="submit" id="Submit" />
	    <input type="hidden" value="<?php echo $key; ?>" name="key" />
	    <input type="hidden" value="verify" name="mode" />
    </form>
    <h2>The Author</h2>
    <p>This was developed by Simon Roberts in 2015 and is part of the Chronolabs System and api's.<br/><br/>This is open source which you can download from <a href="https://sourceforge.net/projects/chronolabsapis/">https://sourceforge.net/projects/chronolabsapis/</a> contact the scribe  <a href="mailto:wishcraft@users.sourceforge.net">wishcraft@users.sourceforge.net</a></p></body>
</div>
</html>
<?php 
