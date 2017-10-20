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
		
		$sql = "SELECT `flow_id` FROM `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` = '" . mysql_escape_string($_REQUEST['email']) . "'";
		if ($flowid = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql)) || empty($error))
		{
			$sql = "SELECT md5(concat(`key`, `flow_id`)) as `fingers` FROM `" . $GLOBALS['APIDB']->prefix('flows_history') . "` WHERE `key` = '" . $key . "' AND  `flow_id` = '" . $flowid . "'";
			if ($history = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql)))
			{
				switch($opt)
				{
					case "optin":
						$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows') . "` SET `name` = '" . mysql_escape_string($name) . "', `participate` = 'yes' where `flow_id` = '" . $flowid . "'");
						header("Location: " . API_URL . "/v2/survey/page-1/".$history['fingers']."/html.api");
						exit(0);
						break;
					case "optout":
						$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows') . "` SET `name` = '" . mysql_escape_string($name) . "', `participate` = 'no' where `flow_id` = '" . $flowid . "'");
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
    <meta property="og:title" content="<?php echo API_VERSION; ?>"/>
    <meta property="og:type" content="api<?php echo API_TYPE; ?>"/>
    <meta property="og:image" content="<?php echo API_URL; ?>/assets/images/logo_500x500.png"/>
    <meta property="og:url" content="<?php echo (isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>" />
    <meta property="og:site_name" content="<?php echo API_VERSION; ?> - <?php echo API_LICENSE_COMPANY; ?>"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="rating" content="general" />
    <meta http-equiv="author" content="wishcraft@users.sourceforge.net" />
    <meta http-equiv="copyright" content="<?php echo API_LICENSE_COMPANY; ?> &copy; <?php echo date("Y"); ?>" />
    <meta http-equiv="generator" content="Chronolabs Cooperative (<?php echo $place['iso3']; ?>)" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo API_VERSION; ?> || <?php echo API_LICENSE_COMPANY; ?></title>
    
    <link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/style.css" type="text/css" />
    <!-- Custom Fonts -->
    <link href="<?php echo API_URL; ?>/assets/media/Labtop/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Labtop Bold/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Labtop Bold Italic/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Labtop Italic/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Labtop Superwide Boldish/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Labtop Thin/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Labtop Unicase/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/LHF Matthews Thin/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Life BT Bold/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Life BT Bold Italic/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Prestige Elite/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Bold/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Normal/style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/gradients.php" type="text/css" />
    <link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/shadowing.php" type="text/css" />

</head>
<body style="min-height: 100.99999999996%;">
<div class="main">
    <img style="float: right; margin: 11px; width: auto; height: auto; clear: none;" src="<?php echo API_URL; ?>/assets/images/logo_350x350.png" />
    <h1><?php echo API_VERSION; ?> (<?php echo API_LICENSE_COMPANY; ?>) Cateloguing Survey || Chronolabs Cooperative</h1>
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
