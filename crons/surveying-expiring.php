<?php
/**
 * Chronolabs Fontages API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers FROM this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         fonts
 * @since           1.0.2
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @version         $Id: functions.php 1000 2013-06-07 01:20:22Z mynamesnot $
 * @subpackage		cronjobs
 * @description		Screening API Service REST
 */

$seconds = floor(mt_rand(1, floor(60 * 4.75)));
set_time_limit($seconds ^ 4);
sleep($seconds);


$sql = array();
ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '300M');
require_once dirname(__DIR__).'/constants.php';
include_once dirname(__DIR__).'/include/functions.php';
require_once dirname(__DIR__).'/class/fontsmailer.php';
error_reporting(E_ERROR);
set_time_limit(7200);
$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
$result = $GLOBALS['APIDB']->queryF($sql[] = "SELECT *, md5(concat(`key`, `flow_id`)) as `fingering` FROM `" . $GLOBALS['APIDB']->prefix('flows_history') . "` WHERE `expiring` > '0' AND `expiring` <= '".time()."'  AND `step` = 'waiting' ORDER BY RAND() LIMIT 99");
while($history = $GLOBALS['APIDB']->fetchArray($result))
{
	$key = $history['fingering'];
	$flow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF('SELECT * FROM `' . $GLOBALS['APIDB']->prefix('flows') . '` WHERE `flow_id` = "' . $history['flow_id'].'"'));
	$upload = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF('SELECT * FROM `' . $GLOBALS['APIDB']->prefix('uploads') . '` WHERE `id` = "' . $history['upload_id'].'"'));
	$survey = json_decode($history['data'], true);
	$data = json_decode($upload['datastore'], true);
	$fontname = str_replace(" ", "", $fontspaces = $data["FontName"]);
	$reserves = getReserves($fontspaces);
	$nodevar = getNodesArray($reserves['fontname'], $reserves['parent']);
	$nodes = array();
	$score = 1015 + $history['score'];
	foreach($nodevar as $cause => $values) {
		foreach($values  as $type => $value)
		{
			$nodes[$cause][$type] = $type;
			$score = $score + $value;
		}
	}
	// Gets File List in Archive
	if (empty($upload['font_id']))
	{
		$currently = $upload['currently_path'];
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
	$survey['names'][$reserves['fontname']] = $data['survey'][$key]['names'][$reserves['fontname']] = $reserves['fontname'];
	$survey['types'][md5(json_encode($reserves['parent']))] = $data['survey'][$key]['types'][md5(json_encode($reserves['parent']))] = $reserves['parent'];
	$survey['reserves'] = $data['survey'][$key]['reserves'] = $reserves;
	$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `finished` = `finished` + 1, `datastore` = '" . mysql_real_escape_string(json_encode($data)) . "' where `id` = '" . $upload['id'] . "'");
	$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows_history') . "` SET `step` = 'expired', `keys` = '".count($nodes['keys'])."',  `fixes` = '".count($nodes['fixes'])."',  `typals` = '".count($nodes['typals'])."',  `questions` = '0',  `expiring` = '0',  `reminders` = '0',  `reminding` = '0', `score` = '$score', `data` = '" . mysql_real_escape_string(json_encode($survey)) . "' WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'");
	$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows') . "` SET `last_history_id` = '".$history['history_id']. "', `last` = '".time(). "', `score` = `score` + '".$score. "', `surveys` = `surveys` +1, `currently` = `currently` - 1, `available` = `available` + 1 WHERE `flow_id` = '" . $history['flow_id'] . "'");
	$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows_history') . "` SET `score` = `score` + '$score', `data` = '" . mysql_real_escape_string(json_encode($survey)) . "' WHERE md5(concat(`key`, `flow_id`)) LIKE '" . $key . "'");
	if (isset($upload['callback']) && !empty($upload['callback']))
		@setCallBackURI($upload['callback'], 127, 131, array('action'=>'expired', 'key' => $key, 'fingerprint' => $fingerprint, 'email' => $flow['email'], 'name' => $flow['name'], 'expired' => $history['expiring'], 'data' => $survey));
	
}
$GLOBALS['APIDB']->queryF($sql = "COMMIT");
?>