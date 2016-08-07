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

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '300M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/fontages.php';
include_once dirname(dirname(__FILE__)).'/class/TwitterAPIExchange.php';
require_once dirname(__DIR__).'/class/fontsmailer.php';
set_time_limit(9999992);

$result = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `uploads` WHERE `uploaded` > '0' AND `converted` > '0' AND `quizing` > '0' AND (`storaged` > '0' OR (`storaged` = '0' AND `expired` < UNIX_TIMESTAMP())) AND `released` = 0 ORDER BY RAND() LIMIT " . mt_rand(7,37));
while($upload = $GLOBALS['FontsDB']->fetchArray($result))
{
	sleep(mt_rand(370,2799));
	$GLOBALS['FontsDB']->queryF($sql = "START TRANSACTION");
	$datastore = json_decode($upload['datastore'], true);
	if ($archive  = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts_archiving` WHERE `font_id` = '" . $upload['font_id'] . "'")))
	{
		$font  = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts` WHERE `id` = '" . $upload['font_id'] . "'"));
		$files  = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT count(*) as `count`, sum(`bytes`) as `bytes` from `fonts_files` WHERE `font_id` = '" . $upload['font_id'] . "'"));
		$naming = getRegionalFontName($upload['font_id']);
		$tos = array();
		$tos['to'][] = $upload['email'];
		$resultb = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts_contributors` WHERE `font_id` = '".$upload['font_id'] . "'");
		while($contrib = $GLOBALS['FontsDB']->fetchArray($resultb))
			if ($flow = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT * from `flows` WHERE `id` = '".$row['flow_id'] . "'")))
				$tos['cc'][] = $flow['email']; 
	
		$resultc = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `releases` ORDER BY RAND() LIMIT 40");
		while($release = $GLOBALS['FontsDB']->fetchArray($resultc))
			$tos['bcc'][] = $release['email'];
		
	
		$mailer = new FontsMailer(API_EMAIL_ADDY, API_EMAIL_FROM);
		if (file_exists($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "SMTPAuth.diz"))
			$smtpauths = explode("\n", str_replace(array("\r\n", "\n\n", "\n\r"), "\n", file_get_contents($file)));
		if (count($smtpauths)>=1)
			$auth = explode("||", $smtpauths[mt_rand(0, count($smtpauths)-1)]);
		if (!empty($auth[0]) && !empty($auth[1]) && !empty($auth[2]))
			$mailer->multimailer->setSMTPAuth($auth[0], $auth[1], $auth[2]);
		$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'font-release.html');
		$html = str_replace("{X_FONTDOWNLOADLINK}", $downloadurl = API_URL . '/v2/data/' . $upload['font_id'] . '/zip/download.api', $html);
		$html = str_replace("{X_FONTNAMING}", $naming, $html);
		$html = str_replace("{X_FONTNAMINGIMAGE}", API_URL . '/v2/font/' . $upload['font_id'] . '/naming/png.api', $html);
		$previewimg = API_URL . '/v2/font/' . $upload['font_id'] . '/preview/image.jpg';
		$previewurl = API_URL . '/v2/font/' . $upload['font_id'] . '/preview.api';
		$html = str_replace("{X_FONTRELEASEPREVIEW}", $previewimg, $html);
		$html = str_replace("{X_FONTRELEASEPREVIEWURL}", $previewurl, $html);
		$html = str_replace("{X_FILEDIZ}", $diz = getFileDIZ($upload['font_id'], $upload['id'], $upload['font_id'], $archive['filename'], filesize(FONT_RESOURCES_RESOURCE.$archive['path'].DIRECTORY_SEPARATOR.$archive['filename']), $datastore['json']['Files']), $html);
		if ($mailer->sendMail($tos['to'], $tos['cc'], $tos['bcc'], "Font release for " . $datastore['FontName'], $html, array($archive['filename'] => FONT_RESOURCES_RESOURCE.$archive['path'].DIRECTORY_SEPARATOR.$archive['filename']), NULL, true))
		{
			echo "Sent mail to: " . implode(", ", $tos['to']) . " ~ cc: " . implode(", ", $tos['cc']) . " ~ bcc: " . implode(", ", $tos['bcc'])."\n\n<br/>\n";
			$GLOBALS['FontsDB']->queryF("UPDATE `uploads` SET `released` = '" . time() . "' where `id` = '" . $upload['id'] . "'");
			// Does Callback's
			$download = array();
			foreach(getArchivingShellExec() as $type => $exec) 
				$download[$type] = API_URL . "/v2/data/".$upload['font_id']."/". $type ."/download.api";
			if (isset($upload['callback']) && !empty($upload['callback']))
				@setCallBackURI($upload['callback'], 120, 120, array('action'=>'release', 'name' =>$datastore['FontName'], 'key' => $upload['font_id'], 'fingerprint' => md5(FONT_RESOURCES_RESOURCE.$archive['path'].DIRECTORY_SEPARATOR.$archive['filename']), 'diz' => $diz, 'downloads' => $download, 'released' => time()));
			$resultb = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `releases` WHERE LENGTH(`callback`) > 0 AND `method` = 'subscribed' ORDER BY RAND()");
			while($release = $GLOBALS['FontsDB']->fetchArray($resultb))
				@setCallBackURI($release['callback'], 120, 120, array('action'=>'release', 'name' =>$datastore['FontName'], 'key' => $upload['font_id'], 'fingerprint' => md5(FONT_RESOURCES_RESOURCE.$archive['path'].DIRECTORY_SEPARATOR.$archive['filename']), 'diz' => $diz, 'downloads' => $download, 'released' => time()));
			
			if (!file_exists($font = FONTS_CACHE . DIRECTORY_SEPARATOR . md5($upload['font_id'].sha1(date('Y-m-d'))) . ".ttf"))
			{
				$ttf = getFontRawData($mode, $upload['font_id'], 'ttf', '');
				if (!is_dir(FONTS_CACHE))
					mkdir(FONTS_CACHE, 0777, true);
					writeRawFile($font, $ttf);
					$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `type` = 'ttf AND `font_id` = '".$upload['font_id']."'");
			}
			$tweeted = false;
			if (isset($font) && file_exists($font))
			{
				require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
				$img = WideImage::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-preview.png');
				if ($state == 'jpg')
				{
					$bg = $img->allocateColor(255, 255, 255);
					$img->fill(0, 0, $bg);
				}
				$height = $img->getHeight();
				$lsize = 66;
				$ssize = 14;
				$step = mt_rand(8,11);
				$canvas = $img->getCanvas();
				$i=0;
				while($i<$height)
				{
					$canvas->useFont($font, $point = $ssize + ($lsize - (($lsize  * ($i/$height)))), $img->allocateColor(0, 0, 0));
					$canvas->writeText(19, $i, "All Work and No Pay Makes Wishcraft a Dull Bored!");
					$i=$i+$point + $step;
				}
				$canvas->useFont(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'titles.ttf', 14, $img->allocateColor(0, 0, 0));
				$canvas->writeText('right', 'bottom', API_URL);
				$img->saveToFile($preview = FONTS_CACHE . DIRECTORY_SEPARATOR . $upload['font_id'] . '.png');
				unset($img);
				
				// Release Tweet Notice
				$twitter = new TwitterAPIExchange($GLOBALS['twitter']);
				$url = 'https://upload.twitter.com/1.1/media/upload.json';
				$requestMethod = 'GET';
				$getfields = array('command'=>'INIT', 'media_type' => 'image/png', 'total_bytes' => filesize($preview));
				$postfields = array();
				$twitter = new TwitterAPIExchange($setting);
				if(count($init = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(), true))>0)
				{
					$requestMethod = 'FILE';
					$getfields = array('command'=>'APPEND', 'media_id' => $init['media_id_string'], 'segment_index' => 0);
					$postfields = array('media' => '@'.$preview, 'file-field' => 'media');
					$twitter = new TwitterAPIExchange($setting);
					if(count($append = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields, $requestMethod)->performRequest(), true))>0)
					{
						$requestMethod = 'GET';
						$getfields = array('command'=>'FINALIZE', 'media_id' => $init['media_id_string']);
						$postfields = array();
						$twitter = new TwitterAPIExchange($setting);
						if(count($final = json_decode($twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(), true))>0)
						{
							$url = "https://api.twitter.com/1.1/statuses/update.json";
							$tweettxt = sprintf(API_TWITTER_RELEASES, $naming, $files['count'], number_format($files['bytes']/1024/1024,2), $downloadurl, $previewurl);
							$requestMethod = 'GET';
							$getfields = array('status' => $tweettxt, array('media_ids' => array($final['media_id_string']=>$final['media_id_string'])));
							$postfields = array();
							$twitter = new TwitterAPIExchange($setting);
							if(count($txt = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(), true))>0)
							{
								$tweeted = true;
								echo "Successfully announced tweet: " . $txt['id_str'] . ' ~ font announced zero-dat release for: ' . $naming . "\n";
							}
						} else 
							echo "Unsuccessfully announced font for zero-dat release on twitter: " . $naming . "\n";
					} else 
						echo "Unsuccessfully announced font for zero-dat release on twitter: " . $naming . "\n";
				} else 
						echo "Unsuccessfully announced font for zero-dat release on twitter: " . $naming . "\n";
				unlink($preview);
			} else 
				echo "Unsuccessfully announced font for zero-dat release on twitter: " . $naming . "\n";
			
			if ($tweeted != true)
			{
				$url = "https://api.twitter.com/1.1/statuses/update.json";
				$tweettxt = sprintf(API_TWITTER_RELEASES, $naming, $files['count'], number_format($files['bytes']/1024/1024,2), $downloadurl, $previewurl);
				$requestMethod = 'GET';
				$getfields = array('status' => $tweettxt);
				$postfields = array();
				$twitter = new TwitterAPIExchange($setting);
				if(count($txt = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(), true))>0)
				{
					$tweeted = true;
					echo "Successfully announced tweet: " . $txt['id_str'] . ' ~ font announced zero-dat release for: ' . $naming . "\n";
				}
			}
		}
		
	} else
		echo("SQL Failed: $sql;\n");
	$GLOBALS['FontsDB']->queryF($sql = "COMMIT");
}
exit(0);


?>