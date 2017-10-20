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
	
	if (!isset($_SESSION['survey'][$key]))
		$_SESSION['survey'][$key] = array();
	
	$sql = "SELECT * FROM `flows_history` WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'";
	if ($result = $GLOBALS['FontsDB']->queryF($sql))
	{
		$history = $GLOBALS['FontsDB']->fetchArray($result);
		$flow = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF('SELECT * from `flows` WHERE `flow_id` = "' . $history['flow_id'].'"'));
		$upload = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF('SELECT * from `uploads` WHERE `id` = "' . $history['upload_id'].'"'));
		$survey = json_decode($history['data'], true);
		$data = json_decode($upload['datastore'], true);
		$ipid = getIPIdentity();
		$survey['ipid'][$ipid['ip_id']] = $_SESSION['survey'][$key]['ipid'][$ipid['ip_id']] = $data['survey'][$key]['ipid'][$ipid['ip_id']] = $ipid;
		$history['longitude'] = $ipid['longitude'];
		$history['latitude'] = $ipid['latitude'];
		$GLOBALS['FontsDB']->queryF("UDPATE `flows_history` SET `latitude` = '" . $history['latitude'] . "', `longitude` = '" . $history['longitude'] . "', `data` = '" . mysql_escape_string(json_encode($survey)) . "' WHERE `history_id` = '" . $history['history_id'] . "'");
		$GLOBALS['FontsDB']->queryF("UDPATE `flows_history` SET `started` = '" . time() . "' WHERE `history_id` = '" . $history['history_id'] . "'");
		$fontname = str_replace(" ", "", $fontspaces = $data["FontName"]);
	
		if ($history['questions']==0)
		{
			header("Location: $source/v2/survey/finish/$key/html.api");
			exit(0);
		} elseif ($history['questions']==1)
		{
			header("Location: $source/v2/survey/page-2/$key/html.api");
			exit(0);
		}
		
		$mode = (isset($_POST['mode'])?$_POST['mode']:(isset($_REQUEST['mode'])?$_REQUEST['mode']:'start'));
		$name = (isset($_REQUEST['name'])?$_REQUEST['name']:array($fontspaces=>$fontspaces));
		$types = (isset($_REQUEST['types'])?$_REQUEST['types']:array('normal'=>'normal'));
		
		$reserves = getReserves($fontspaces + " " . implode(" ", $types));
		$error = "";
		switch($mode)
		{
			case "next":
				$score = 1.45;
				$survey = array();
				
				if (!empty($name))
				{
					foreach($name as $title)
					{
						if (!empty($title)||trim($title)!='')
						{
							$_SESSION['survey'][$key]['names'][$title] = $survey['names'][$title] = $data['survey'][$key]['names'][$title] = $title;
							$score = $score + 7.75;
							}
					}
				}
				if (!empty($types))
					$_SESSION['survey'][$key]['types'][md5(json_encode($types))] = $survey['types'][md5(json_encode($types))] = $data['survey'][$key]['types'][md5(json_encode($types))] = $types;
				$_SESSION['survey'][$key]['reserves'] = $survey['reserves'] = $data['survey'][$key]['reserves'] = $reserves;
				$GLOBALS['FontsDB']->queryF("UPDATE `uploads` SET `datastore` = '" . mysql_escape_string(json_encode($data)) . "' where `id` = '" . $upload['id'] . "'");
				$GLOBALS['FontsDB']->queryF("UPDATE `flows_history` SET `questions` = '1', `score` = `score` + '$score', `data` = '" . mysql_escape_string(json_encode($survey)) . "' WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'");
				header("Location: " . API_URL . "/v2/survey/page-2/".$key."/html.api");
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
    <h1><?php echo API_VERSION; ?> (<?php echo API_LICENSE_COMPANY; ?>) Survey Question 1/2 || Chronolabs Cooperative</h1>
	<?php if (!empty($error)) { ?>
    <h2>Error Occured</h2>
    <p style="color: rgb(197,0, 0); font-size: 130%; font-weight: bold;"><?php echo $error; ?></p>
    <?php } ?>
    <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
	    <label for="email">Current Font Name:</label>
	    <input type="text" maxlength="198" size="42" name="default" id="default" value="<?php echo $fontspaces; ?>" disabled="disabled" />
	    <input type="hidden" name="name[]" id="name[]" value="<?php echo $fontspaces; ?>"  />
	    <br/>
	    <label for="name">Your Font Name:</label>
	    <input type="text" maxlength="198" size="42" name="name[]" id="name[]" />&nbsp;&nbsp;<em style="font-size: 69%;">You do not have to define your own regional unique name you can leave this blank!</em>
	    <br/>
	    <?php foreach(array('normal','italic','bold','wide','condensed','ultra','extra','light','semi','book','body','header','heading','footer','graphic','system','quote','blocks','message','admin','logo','slogan','legal','script') as $reserve) { ?>
	    <label for="optin">Font is: <strong><?php echo ucwords($reserve); ?></strong></label>
	    <input type="checkbox" value="<?php echo $reserve; ?>" <?php if (in_array($reserve, $reserves['parent'])) { ?>checked="checked" <?php } ?>id="types[<?php echo $reserve; ?>]" name="types[<?php echo $reserve; ?>]" />
	    &nbsp;&nbsp;
	    <?php } ?>
	    <br/><br/>
	    <input type="submit" value="Submit" name="submit" id="Submit" />
	    <input type="hidden" value="<?php echo $key; ?>" name="key" />
	    <input type="hidden" value="next" name="mode" />
    </form>
    <?php 
    echo '<iframe style="margin: auto; clear: both; padding: 10px; border: 4px dash #000; height: 675px; min-width: 97%;" src="'.API_URL . '/v2/survey/preview/' . $upload['key'] . '/html.api.">&nbsp;</iframe>';
    ?>
</html>
<?php
			break;
		}
	}
?>