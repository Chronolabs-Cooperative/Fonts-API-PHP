<?php
/**
 * Chronolabs Fontages API
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
require_once dirname(__DIR__).'/class/fontsmailer.php';
error_reporting(E_ERROR);
set_time_limit(7200);
$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
$result = $GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `uploaded` > '0' AND `converted` > '0'  AND `quizing` <= '0' ORDER BY RAND() LIMIT 99");
while($row = $GLOBALS['APIDB']->fetchArray($result))
{
	if ($row['scope'] == 'none')
	{
		$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `quizing` = UNIX_TIMESTAMP(), `expired` = UNIX_TIMESTAMP()+1831, `slotting` = 0, `needing` = 1, `finished` = 2, `surveys` = 2, `available` = 0 WHERE `id` = '".$row['id']."'";
		$GLOBALS['APIDB']->queryF($sql);
	} elseif (empty($row['font_id']))
	{
		$currently = $row['currently_path'];
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
	$datastore = json_decode($row['datastore'], true);
	list($emails) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT `emails` FROM `" . $GLOBALS['APIDB']->prefix('emails') . "` WHERE `id` = '".$row['cc']."'"));
	$cc = array_merge(json_decode($emails['emails'], true), cleanWhitespaces(file(dirname(__DIR__) . '/data/emails-default-cc.diz')));
	list($emails) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT `emails` FROM `" . $GLOBALS['APIDB']->prefix('emails') . "` WHERE `id` = '".$row['bcc']."'"));
	$bcc = array_merge(json_decode($emails['emails'], true), cleanWhitespaces(file(dirname(__DIR__) . '/data/emails-default-bcc.diz')));
	$nopass = -1;
	$tos = array();
	while(count($tos['to'])+count($tos['cc'])+count($tos['bcc'])==0 && $nopass < 5)
	{
		$nopass++;
		if( in_array('bcc', array_keys($datastore['scope'])) && (count($bcc)>0)){
			$ccs = mt_rand(7,12);
			$bccs = 18 - $ccs;
			while(count($tos['to'])<$ccs && count($tos['to']) <= count($cc))
			{
				shuffle($cc);
				$email = $cc[$idx = mt_rand(0,count($cc)-1)];
				list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) as `rc` from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` LIKE '$email' AND `available` <= '0'  OR `participate` = 'no'"));
				if ($count==0)
				{
					$tos['to'][] = $email;
				} else
					unset($cc[$idx]);
			}
			while(count($tos['bcc'])<$bccs && count($tos['bcc']) <= count($bcc))
			{
				shuffle($bcc);
				$email = $bcc[$idx = mt_rand(0,count($bcc)-1)];
				list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) as `rc` from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` LIKE '$email' AND `available` <= '0'  OR `participate` = 'no'"));
				if ($count==0)
				{
					$tos['bcc'][] = $email;
				} else
					unset($cc[$idx]);
			}
			if (in_array('to', array_keys($datastore['scope'])))
			{
				$tos['cc'][] = $row['email'];
			}
		} elseif(in_array('cc', array_keys($datastore['scope'])) && count($cc)>0)
		{
			shuffle($cc);
			$ccs = mt_rand(4,18);
			while(count($tos['to'])<$ccs && count($tos['to']) <= count($cc))
			{
				$email = $cc[$idx = mt_rand(0,count($cc)-1)];
				list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) as `rc` from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` LIKE '$email' AND `available` <= '0'  OR `participate` = 'no'"));
				if ($count==0)
				{
					$tos['to'][] = $email;
				} else
					unset($cc[$idx]);
			}
			if (in_array('to', array_keys($datastore['scope'])))
			{
				$email = $row['email'];
				list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) as `rc` from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` LIKE '$email' AND `available` <= '0'  OR `participate` = 'no'"));
				if ($count==0)
				{
					$tos['cc'][] = $email;
				}
			}
		} else {
			$tos['to'][0] = $row['email'];
		}
		$tos['to'] = array_unique($tos['to']);
		$tos['cc'] = array_unique($tos['cc']);
		$tos['bcc'] = array_unique($tos['bcc']);
	}

	$flowids = array();
	$sendmail = false;
	foreach($tos as $key => $values)
		foreach($values as $id => $email)
		{
			$rrow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` = '$email' AND `participate` = 'yes'"));
			if ($rrow['available']>0||!isset($rrow['available']))
			{
				if (!isset($rrow['flow_id'])||empty($rrow['flow_id']))
				{
					if (!$GLOBALS['APIDB']->queryF($sql =  ("INSERT INTO `" . $GLOBALS['APIDB']->prefix('flows') . "` (`email`, `name`, `participate`, `fonts`, `surveys`, `score`, `reminder`, `available`, `currently`, `code`) VALUES ('$email', '', 'yes', 0,0,0,".($remind = time() + mt_rand(3600*3*22, 3600*7*24)).",7,1,'".($code=substr(md5(microtime), mt_rand(0,32-6), 5)) . "')")))
						die("SQL Failed: $sql");
					if (!$rrow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `email` = '$email' AND `participate` = 'yes'")))
						die("SQL Failed: $sql");
				} elseif ($row['available']>0) {
					$GLOBALS['APIDB']->queryF($sql = ("UPDATE `" . $GLOBALS['APIDB']->prefix('flows') . "` SET `reminder` = ".($remind = time() + mt_rand(3600*3*22, 3600*7*24)).", `available` = `available`-1, `currently`=`currently`+1 WHERE `flow_id` = " . $rrow['flow_id']));
				}
				if (!$GLOBALS['APIDB']->queryF($sql = ("INSERT INTO `" . $GLOBALS['APIDB']->prefix('flows_history') . "` (`key`, `flow_id`, `upload_id`, `questions`, `reminding`, `expiring`, `step`) VALUES ('".$row['key']."', '".$rrow['flow_id'] . "','" .$row['id']."', '2', '$remind', '".($expiring = (time() + 3600 * mt_rand(3, 12) * mt_rand(7, 19) * 1.11223455665)))."','waiting')"))
					die("SQL Failed: $sql");
				$sendmail = true;
			} else {
				unset($tos[$key][$id]);
			}
		}
	if ($sendmail == true)
	{
		$mailer = new FontsMailer(API_EMAIL_ADDY, API_EMAIL_FROM);
		if (file_exists($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "SMTPAuth.diz"))
			$smtpauths = explode("\n", str_replace(array("\r\n", "\n\n", "\n\r"), "\n", file_get_contents($file)));
		if (count($smtpauths)>=1)
			$auth = explode("||", $smtpauths[mt_rand(0, count($smtpauths)-1)]);
		if (!empty($auth[0]) && !empty($auth[1]) && !empty($auth[2]))
			$mailer->multimailer->setSMTPAuth($auth[0], $auth[1], $auth[2]);
		$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'survey-email.html');
		$html = str_replace("{X_FONTSURVEYLINK}", API_URL . '/v2/survey/start/' . $row['key'] . '/html.api', $html);
		$previewimg = API_URL . '/v2/survey/preview/' . $row['key'] . '/image.jpg';
		$html = str_replace("{X_FONTUPLOADPREVIEW}", $previewimg, $html);
		if ($mailer->sendMail($tos['to'], $tos['cc'], $tos['bcc'], "Font cateloguing survery for " . $datastore['FontName'], $html, array(), NULL, true))
		{
			if (isset($row['callback']) && !empty($row['callback']))
				@setCallBackURI($row['callback'], 327, 331, array('action'=>'allocated', 'key' => $row['key'], 'fingerprint' => $fingerprint, 'emails' => $tos, 'expires' => $expiring, 'scope' => $datastore['scope'], 'subject' => "Font cateloguing survery for " . $datastore['FontName']));
			
			echo "Sent mail to: " . implode(", ", $tos['to']) . " ~ cc: " . implode(", ", $tos['cc']) . " ~ bcc: " . implode(", ", $tos['bcc'])."\n\n<br/>\n";
			$available = count($tos['to'])+count($tos['cc'])+count($tos['bcc']);
			if ($available <5 )
				$needing = mt_rand(1,2);
			else
				$needing = mt_rand(3,6);
			$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `available` = '$available', `needing` = '$needing', `quizing` = '" . (time() + (3600 * 24 * 3 * mt_rand(0.233453, 2.78647))) . "' where `id` = '" . $row['id'] . "'");
		}
		echo ".";
	}
}
$GLOBALS['APIDB']->queryF($sql = "COMMIT");
?>