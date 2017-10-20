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

	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source, $ipid;

	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';
	
	session_start();
	
	$key = (isset($_REQUEST['key'])?$_REQUEST['key']:md5(NULL));
	
	$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('flows_history') . "` WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'";
	if ($history = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql)))
	{
		$flow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF('SELECT * from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `flow_id` = "' . $history['flow_id'].'"'));
		$upload = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF('SELECT * from `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `id` = "' . $history['upload_id'].'"'));
		$survey = json_decode($history['data'], true);
		$data = json_decode($upload['datastore'], true);
		$survey['ipid'][$ipid['ip_id']] = $_SESSION['survey'][$key]['ipid'][$ipid['ip_id']] = $data['survey'][$key]['ipid'][$ipid['ip_id']] = $ipid;
		$history['longitude'] = $ipid['longitude'];
		$history['latitude'] = $ipid['latitude'];
		$GLOBALS['APIDB']->queryF("UDPATE `" . $GLOBALS['APIDB']->prefix('flows_history') . "` SET `ip_id` = '" . $ipid['ip_id'] . "', `latitude` = '" . $history['latitude'] . "', `longitude` = '" . $history['longitude'] . "', `data` = '" . mysql_escape_string(json_encode($survey)) . "' WHERE `history_id` = '" . $history['history_id'] . "'");
		$GLOBALS['APIDB']->queryF("UDPATE `" . $GLOBALS['APIDB']->prefix('flows') . "` SET `ip_id` = '" . $ipid['ip_id'] . "' WHERE `flow_id` = '" . $history['flow_id']."'");
		if ($history['questions']==0)
		{
			header("Location: $source/v2/survey/finish/$key/html.api");
			exit(0);
		} elseif ($history['questions']==2)
		{
			header("Location: $source/v2/survey/page-1/$key/html.api");
			exit(0);
		}
	
		
		$mode = (isset($_POST['mode'])?$_POST['mode']:(isset($_REQUEST['mode'])?$_REQUEST['mode']:'start'));
		
		$error = "";
		switch($mode)
		{
			case "finish":
				// Gets File List in Archive
				if (empty($upload['font_id']))
				{
					unlink($currently . DIRECTORY_SEPARATOR . "File.diz");
					$filez = getFileListAsArray($currently);
					foreach(getCompleteDirListAsArray($currently) as $path)
					{
						$filez[str_replace($currently.DIRECTORY_SEPARATOR, "", $path)] = getFileListAsArray($path);
					}
				
					$fingerprint = md5(NULL);
					$filecount = 0;
					$expanded = 0;
					foreach($filez as $path => $file)
					{
						if (is_array($file) && is_dir($currently .DIRECTORY_SEPARATOR . $path))
						{
							foreach($file as $dat)
							{
								$filecount++;
								$fingerprint = md5($fingerprint . sha1_file($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $dat));
								$expanded += filesize($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $dat);
							}
						} elseif( file_exists($currently .DIRECTORY_SEPARATOR . $file))
						{
							if (substr($file, strlen($file)-3)=='css'||substr($file, strlen($file)-4)=='json') {
								unlink($currently .DIRECTORY_SEPARATOR . $file);
								unset($filez[$path]);
							} else {
								$filecount++;
								$fingerprint = md5($fingerprint . sha1_file($currently .DIRECTORY_SEPARATOR . $file));
								$expanded += filesize($currently .DIRECTORY_SEPARATOR . $file);
							}
						}
					}
				} else
					$fingerprint = $upload['font_id'];
				$score = 2.55 + $history['score'];
				$survey = array();
				$nodes = (isset($_REQUEST['node'])?$_REQUEST['node']:array());
				$scores = (isset($_REQUEST['score'])?$_REQUEST['score']:array());
				foreach($scores as $type => $values)
					foreach($values as $naming => $value)
						$score = $score + $value;
				foreach($nodes as $type => $values)
					foreach($values as $naming => $value)
						if (empty($value)||trim($value)=="")
							unset($nodes[$type][$naming]);	
				$data['nodes']=$nodes;
				$_SESSION['survey'][$key]['nodes'] = $survey['nodes'] = $data['survey'][$key]['nodes'] = $nodes;
				$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `surveys` = `surveys` + 1, `finished` = `finished` + 1, `datastore` = '" . mysql_escape_string(json_encode($data)) . "' where `id` = '" . $upload['id'] . "'");
				$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows_history') . "` SET `step` = 'finished', `keys` = '".count($nodes['keys'])."',  `fixes` = '".count($nodes['fixes'])."',  `typal` = '".count($nodes['typal'])."',  `questions` = '0',  `expiring` = '0',  `reminders` = '0',  `reminding` = '0', `score` = '$score', `data` = '" . mysql_escape_string(json_encode($survey)) . "' WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'");
				$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows') . "` SET `last_history_id` = '".$history['history_id']. "', `last` = '".time(). "', `score` = `score` + '".$score. "', `surveys` = `surveys` +1, `currently` = `currently` - 1, `available` = `available` + 1 WHERE `flow_id` = '" . $history['flow_id'] . "'");
				if (isset($upload['callback']) && !empty($upload['callback']))
					@setCallBackURI($upload['callback'], 127, 131, array('action'=>'completed', 'key' => $key, 'fingerprint' => $fingerprint, 'email' => $flow['email'], 'name' => $flow['name'], 'expired' => $history['expiring'], 'data' => $survey));
				header("Location: " . API_URL . "/v2/survey/finish/".$key."/html.api");
				exit(0);
				break;
			default:
			case "start":
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
<body>
<div class="main">
	<img style="float: right; margin: 11px; width: auto; height: auto; clear: none;" src="<?php echo API_URL; ?>/assets/images/logo_350x350.png" />
    <h1><?php echo API_VERSION; ?> (<?php echo API_LICENSE_COMPANY; ?>) Survey Question 2/2 || Chronolabs Cooperative</h1>
	<?php if (!empty($error)) { ?>
    <h2>Error Occured</h2>
    <p style="color: rgb(197,0, 0); font-size: 130%; font-weight: bold;"><?php echo $error; ?></p>
    <?php } 
    $nodes = getNodesArray($survey['names'], $survey['types']);
    ?>
    <h2>Fontage Nodes</h2>
    <p>The following is the compiled nodes for the font, this means you will be able to specify typal, keys and fixes for the node list, these are all particular lengths and there is a precompiled list for them where you can add them as well!</p>
    <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
    	<h3>Typal's - 2/3 letter referee's</h3>
    	<?php foreach($nodes['typal'] as $type => $score) { ?>
	    <input type="text" maxlength="3" size="4" name="default[]" id="default[]" value="<?php echo $type; ?>" disabled="disabled" />
	    <input type="hidden" name="node[typal][]" id="node[typal][]" value="<?php echo $type; ?>"  />
	    <input type="hidden" name="score[typal][]" id="score[typal][]" value="<?php echo $score; ?>"  />
	    <?php } ?>
	    <?php for($n=0;$n<14;$n++) { ?>
	    <input type="text" maxlength="3" size="4" name="node[typal][]" id="node[typal][]" value="" />
	    <?php } ?>
	    <br/>
	    <h3>Keys's - 4/5 letter referee's</h3>
    	<?php foreach($nodes['keys'] as $type => $score) { ?>
	    <input type="text" maxlength="5" size="6" name="default[]" id="default[]" value="<?php echo $type; ?>" disabled="disabled" />
	    <input type="hidden" name="node[keys][]" id="node[keys][]" value="<?php echo $type; ?>"  />
	    <input type="hidden" name="score[keys][]" id="score[keys][]" value="<?php echo $score; ?>"  />
	    <?php } ?>
	    <?php for($n=0;$n<10;$n++) { ?>
	    <input type="text" maxlength="5" size="6" name="node[keys][]" id="node[fixes][]" value="" />
	    <?php } ?>
	    <br/>
	    <h3>Fixes - Full Length letter referee's</h3>
	    <?php foreach($nodes['fixes'] as $type => $score) { ?>
	    <input type="text" maxlength="28" size="16" name="default[]" id="default[]" value="<?php echo $type; ?>" disabled="disabled" />
	    <input type="hidden" name="node[fixes][]" id="node[fixes][]" value="<?php echo $type; ?>"  />
	    <input type="hidden" name="score[fixes][]" id="score[fixes][]" value="<?php echo $score; ?>"  />
	    <?php } ?>
	    <?php for($n=0;$n<8;$n++) { ?>
	    <input type="text" maxlength="28" size="16" name="node[fixes][]" id="node[fixes][]" value="" />
	    <?php } ?>
	    <br/><br/>
	    <input type="submit" value="Submit" name="submit" id="Submit" />
	    <input type="hidden" value="<?php echo $key; ?>" name="key" />
	    <input type="hidden" value="finish" name="mode" />
    </form>
    <?php 
    echo '<iframe style="margin: auto; clear: both; padding: 10px; border: 4px dash #000; height: 675px; min-width: 97%;" src="'.API_URL . '/v2/survey/preview/' . $upload['key'] . '/html.api.">&nbsp;</iframe>';
    ?>
</html>
<?php
			break;
		}
	} else
		die('Key Not Found!');
?>