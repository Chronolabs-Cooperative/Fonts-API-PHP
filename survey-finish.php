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
	$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('flows_history') . "` WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'";
	if ($result = $GLOBALS['APIDB']->queryF($sql))
	{
		$history = $GLOBALS['APIDB']->fetchArray($result);
		$survey = json_decode($history['data'], true);
		$sql = "SELECT *, md5(concat(`key`, `flow_id`)) as `fingering` FROM `" . $GLOBALS['APIDB']->prefix('flows_history') . "` WHERE `flow_id` = '".$history['flow_id'] . "' AND `started` = '0'";
		if ($result = $GLOBALS['APIDB']->queryF($sql))
		{
			
			while( $row = $GLOBALS['APIDB']->fetchArray($result) ) 
			{
				$upload = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF('SELECT * from `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `id` = "' . $row['upload_id'].'"'));
				$data = json_decode($upload['datastore'], true);
				$others[$row['fingering']] = $data['FontName'];
			}
		}
		$flow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF('SELECT * from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `flow_id` = "' . $history['flow_id'].'"'));
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
    <h1><?php echo API_VERSION; ?> (<?php echo API_LICENSE_COMPANY;?>) ~ Font Survey finished <?php $flow['name']; ?>!</h1>
    	<p><font style="font-size: 132%">You have finished the survey for the font: <strong><em><?php echo $survey['reserves']['fontname']; ?></em></strong> You have <?php  echo (count($others) > 0 ? "More Survey's to do select one below to continue!":"No More Survey's!!!"); ?></font></p>
	    <?php if (count($others) > 0) { ?>
	    <h2><?php echo count($others); ?> Surveys left to do!!</h2>
	    <p>
	    	<ol>
	    		<?php foreach ($others as $finger => $name) { ?>
	    		<li>Survey Font: <a href="<?php echo API_URL . "/v2/survey/page-1/$finger/html.api"; ?>"><?php echo $name; ?></a></li>
	    		<?php } ?>
	    	</ol>
	    </p>
	    <?php } ?>
	</div>
</div>
</html>
<?php 
	}
	