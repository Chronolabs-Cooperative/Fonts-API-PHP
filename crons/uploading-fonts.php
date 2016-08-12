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
ini_set('memory_limit', '315M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/fontages.php';
require_once dirname(__DIR__).'/class/fontsmailer.php';
set_time_limit(7200*99*25);
$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
$keys = array_keys($uploader);
while(!count($uploader[$ipid = $keys[mt_rand(0, count($keys)-1)]]) && count($uploader) > 0)
{
	unset($uploader[$ipid]);
	$keys = array_keys($uploader);
}
if (count($uploader)>0)
foreach($uploader[$ipid] as $time => $data) {
	if (!isset($data['success'])||!is_array($data['success']))
		$data['success'] = array();
	if (!isset($data['files'])||!is_array($data['files']))
		$data['files'] = array();
	if (!isset($data['start'])||!is_array($data['start']))
		$data['start'] = array();
	$data['ipid'] = $ipid;
	$data['time'] = time();
	$data['start'][] = microtime(true);
	
	foreach($data['files'] as $zipii)
	{
		if (!isset($data['path']) || empty($data['path']))
		{
			if (is_array($zipii))
				foreach($zipii as $zipid => $zipe)
					$data['path'] = dirname($zipe);
			else
				$data['path'] = dirname($zipii);
							
			if (is_dir($data['path']))
			{
				$data['path'] = str_replace(array(constant("FONT_RESOURCES_UNPACKING"),constant("FONT_UPLOAD_PATH")), '' , $data['path']);
			}
		}
	}
	
	unset($uploader[$ipid][$time]);
	file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
	file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
	foreach($data['files'] as $zipii)
	{
		if (is_array($zipii))
		foreach($zipii as $zipid => $zipe)
		foreach(array_keys(getArchivingShellExec()) as $type => $cmd)
		if ($data['type'] == 'pack' && !isset($data['mode']) || substr(strtolower($zipe), strlen($type), strlen($zipe) - strlen($type)) == strtolower($type))
		{
			echo "Unpacking: " . basename($zipe)."\n";
			$data['process'] = microtime(true);
			$data['mode'] = 'assessments';
			file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
		
			$data['process'] = microtime(true);
			$data['mode'] = 'unpacking';
			$data['current'] = $zipe;
			file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
			echo "\nUnpacking archive: ".basename($zipe);
			$cmds = getExtractionShellExec();
			@shell_exec($cmd = (substr($cmds[$data['packtype']],0,1)!="#"?DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR:'') . str_replace('%path', constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR, str_replace('%pack', $zipe, (substr($cmds[$data['packtype']],0,1)!="#"?$cmds[$data['packtype']]:substr($cmds[$data['packtype']],1)))));
			unlink($zipe);
			$packs = true;
			while($packs == true)
			{
				$packs = false;
				foreach(getCompletePacksListAsArray(constant("FONT_RESOURCES_UNPACKING") . $data['path']) as $packtype => $packs)
				{
					foreach($packs as $hashinfo => $packfile)
					{
						$data['process'] = microtime(true);
						$data['mode'] = 'unpacking';
						$data['current'] = $packfile;
						file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
							
						echo "\nUnpacking archive: ".$packfile;
						if (!is_dir(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . $hashinfo))
							mkdir(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . $hashinfo, 0777, true);
							@shell_exec($cmd = (substr($cmds[$packtype],0,1)!="#"?DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR:'') . str_replace('%path', constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . $hashinfo, str_replace('%pack', $packfile, (substr($cmds[$packtype],0,1)!="#"?$cmds[$packtype]:substr($cmds[$packtype],1)))));
							$packs=true;
							unlink($packfile);
					}
				}
			}
			$packs=true;
		} elseif (substr($zipe, 0, strlen(constant("FONT_RESOURCES_UNPACKING"))-1)!=constant("FONT_RESOURCES_UNPACKING")) {
			if (!is_dir(constant("FONT_RESOURCES_UNPACKING") . $data['path']))
				mkdir(constant("FONT_RESOURCES_UNPACKING") . $data['path'], 0777, true);
			copy($zipe, constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . basename($zipe));
			unlink($zipe);
		}
	}
	
	if ($packs==true)
	{
		$data['process'] = microtime(true);
		$data['mode'] = 'unpacking';
		$data['current'] = $packfile;
		$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
		$uploader[$ipid][$time] = $data;
		file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
		shell_exec("rm -Rf " . constant("FONT_UPLOAD_PATH") . $data['path'] . DIRECTORY_SEPARATOR . '*');
		$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
		$uploader[$ipid][$time] = $data;
		file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
		die("Finished Unpacking this Upload from: $ipid at the time of " . date("Y-m-d H:i:s", $time));
	} else {
		$data['mode'] = ($data['mode']!='pack'?'store':'unpacking');
	}
	
	if (isset($data['mode']) && $data['mode'] == 'unpacking' || $data['mode'] == 'culling')
	{
		echo "Looking for Culling: " . constant("FONT_RESOURCES_UNPACKING") . $data['path']."\n";
		@shell_exec($cmd = DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fdupes -R -N " . constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . $hashinfo);
		foreach($cullist = getFontsCullList($fonts = getCompleteFontsListAsArray(constant("FONT_RESOURCES_UNPACKING") . $data['path'])) as $finger => $culls)
			foreach($culls as $finged => $fingering)
				foreach($fingering as $file)
				{
					$data['process'] = microtime(true);
					$data['mode'] = 'culling';
					$data['current'] = $file;
					file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
						
					echo "\nCulling file: ".$file;
					unlink($file);
					if (isset($data['form']['callback']) && !empty($data['form']['callback']))
						@setCallBackURI($data['form']['callback'], 127, 131, array('action'=>'ignored', 'file-md5' => $finged, 'allocated' => true, 'email' => $data['form']['email'], 'name' => $data['form']['name'], 'bizo' => $data['form']['bizo'], 'filename' => basename($file), 'culled' => true));
				}
			$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
		$data['mode'] = 'store';
		$uploader[$ipid][$time] = $data;
		file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
		die("Finished checking Cull Listing's for Upload from: $ipid at the time of " . date("Y-m-d H:i:s", $time));
	}
	
	if (isset($data['mode']) && $data['mode'] == 'store' || ($data['type'] == 'file' && !isset($data['mode'])) && !empty($data['path']))
	{
		echo "Looking for Fonts: " . constant("FONT_RESOURCES_UNPACKING") . $data['path']."\n";
		if (is_dir(constant("FONT_RESOURCES_UNPACKING") . $data['path']))
		{
			$files = getCompleteFontsListAsArray(constant("FONT_RESOURCES_UNPACKING") . $data['path']);
			$data['files'] = array();
			foreach($files as $type => $fontfiles)
			{
				$keys = array_keys($fontfiles);
				shuffle($keys); shuffle($keys); shuffle($keys);
				foreach($keys as $key)
					$data['files'][$type][$key] = $fontfiles[$key];
			}
			$files = $data['files'];
			$culled = array();
			$scope = (!isset($data['form']['scope']['bcc']) && !isset($data['form']['scope']['cc']) && !isset($data['form']['scope']['to'])?'none':(!isset($data['form']['scope']['bcc']) && !isset($data['form']['scope']['cc'])?'to':(!isset($data['form']['scope']['bcc']) || isset($data['form']['scope']['cc'])?'cc':(isset($data['form']['scope']['bcc']) && isset($data['form']['scope']['cc'])?'all':'bcc'))));
			if (empty($scope))
				$scope = 'none';
			if (!is_dir($copypath))
				mkdir($copypath, 0777, true);
			$size = 0;
			foreach($files as $type => $fontfiles)
			{
				foreach($fontfiles as $finger => $fontfile)
				{
					$size += count($fontfile);
				}
			}
			
			$emailcc = cleanWhitespaces(explode("\n", str_replace(array(";",",",":","/","\\","||","|"), "\n", $data['form']['email-cc'])));
			foreach($emailcc as $key => $value)
				if (!checkEmail($value))
					unset($emailcc[$key]);
			
			$emailbcc = cleanWhitespaces(explode("\n", str_replace(array(";",",",":","/","\\","||","|"), "\n", $data['form']['email-bcc'])));
			foreach($emailbcc as $key => $value)
				if (!checkEmail($value))
					unset($emailbcc[$key]);
			$ffile = 0;
			$queued = array();
			$GLOBALS['FontsDB']->queryF($sql = "START TRANSACTION");
			foreach($files as $type => $fontfiles)
			{
				foreach($fontfiles as $finger => $fontfile)
				{
					$copypath = FONT_RESOURCES_SORTING . DIRECTORY_SEPARATOR . $data['form']['email'] . DIRECTORY_SEPARATOR . microtime(true);
					if (!is_dir($copypath))
						mkdir($copypath, 0777, true);
					
					if (!file_exists($copypath . DIRECTORY_SEPARATOR . basename($fontfile))&&filesize($fontfile)>199)
					{
						if (copy($fontfile, $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile))))
						{
							if (file_exists($uploadfile = $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile))))
							{
								@exec("cd $copypath", $out, $return);
								@exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", dirname(__DIR__ ) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts-upload.pe", $uploadfile), $out, $return);
								deleteFilesNotListedByArray($copypath, array(API_BASE=>API_BASE));
								unlink($fontfile);
								foreach(getFontsListAsArray($copypath) as $file)
									if ($file['type']==API_BASE)
										$uploadfile = $copypath . DIRECTORY_SEPARATOR . $file['file'];
								$fontdata = getBaseFontValueStore($uploadfile);
								if (isset($fontdata['version']))
									$fontdata['version'] = $fontdata['version'] + 1.001;
								$fontdata['person'] = $data['form']['name'];
								$fontdata['company'] = $data['form']['bizo'];
								$fontdata['uploaded'] = microtime(true);
								$fontdata['licence'] = API_LICENCE;
								writeFontRepositoryHeader($uploadfile, API_LICENCE, $fontdata);
								$fingerprint = md5_file($uploadfile);
								$sql = "SELECT count(*) FROM `fonts_fingering` WHERE `fingerprint` LIKE '" . $fingerprint . "'";
								list($fingers) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
								if ($fingers==0)
								{
									$ffile++;
									$data['process'] = microtime(true);
									$data['mode'] = 'queuing';
									$data['current'] = $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($uploadfile));
									file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
									sort($emailcc);
									sort($emailbcc);
									$ccid = md5(json_encode($emailcc));
									$bccid = md5(json_encode($emailbcc));
									$sql = "SELECT count(*) FROM `emails` WHERE `id` = '$ccid'";
									list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
									if ($count == 0)
									{
										if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `emails` (`id`, `emails`) VALUES('$ccid', '".$GLOBALS['FontsDB']->escape(json_encode($emailcc))."')"))
											die("SQL Failed: $sql;");
									}
									$sql = "SELECT count(*) FROM `emails` WHERE `id` = '$bccid'";
									list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
									if ($count == 0)
									{
										if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `emails` (`id`, `emails`) VALUES('$bccid', '".$GLOBALS['FontsDB']->escape(json_encode($emailcc))."')"))
											die("SQL Failed: $sql;");
									}
									$queued[] = $fontfile;
									$sql = "INSERT INTO `uploads` (`ip_id`, `available`, `key`, `scope`, `prefix`, `email`, `uploaded_file`, `uploaded_path`, `uploaded`, `referee_uri`, `callback`, `bytes`, `batch-size`, `datastore`, `cc`, `bcc`, `frequency`, `elapses`, `longitude`, `latitude`) VALUES ('$ipid','" . $available = mt_rand(7,13) . "','" . $GLOBALS['FontsDB']->escape(md5_file($copypath . DIRECTORY_SEPARATOR .  strtolower(basename($uploadfile)))) . "','" . $GLOBALS['FontsDB']->escape($scope) . "','" . $GLOBALS['FontsDB']->escape($prefix = $data['form']['prefix']) . "','" . $GLOBALS['FontsDB']->escape($email = $data['form']['email']) . "','" . $GLOBALS['FontsDB']->escape($filename = strtolower(basename($uploadfile))) . "','" . $GLOBALS['FontsDB']->escape($copypath) . "','" . time(). "','" . $GLOBALS['FontsDB']->escape($_SERVER['HTTP_REFERER']) . "','" . $GLOBALS['FontsDB']->escape($callback = $data['form']['callback']) . "'," . (filesize($uploadfile)==''?0:filesize($uploadfile)) . "," . $size . ",'" . $GLOBALS['FontsDB']->escape(json_encode(array('scope' => $data['form']['scope'], 'ipsec' => $locality = json_decode(getURIData("https://lookups.labs.coop/v1/country/".(in_array($ip = whitelistGetIP(true), array('127.0.0.1','10.1.1.1'))?'myself':$ip)."/json.api"), true), 'name' => $data['form']['name'], 'bizo' => $data['form']['bizo'], 'batch-size' => $size, 'font' => $fontdata))) . "','$ccid','$bccid','" . $GLOBALS['FontsDB']->escape($freq = mt_rand(2.76,6.75)*3600*24) . "','" . $GLOBALS['FontsDB']->escape($elapse = mt_rand(9,27)*3600*24) . "','". (!isset($_SESSION['locality']['location']["coordinates"]["longitude"])?"0.0001":$_SESSION['locality']['location']["coordinates"]["longitude"])."','". (!isset($_SESSION['locality']['location']["coordinates"]["latitude"])?"0.0001":$_SESSION['locality']['location']["coordinates"]["latitude"])."')";
									if ($GLOBALS['FontsDB']->queryF($sql))
									{
										$uploadid = $GLOBALS['FontsDB']->getInsertId();
										if ($scope == 'none')
										{
											$sql = "UPDATE `uploads` SET `quizing` = UNIX_TIMESTAMP(), `expired` = UNIX_TIMESTAMP()+1831, `slotting` = 0, `needing` = 1, `finished` = 2, `surveys` = 2, `available` = 0 WHERE `id` = $uploadid";
											$GLOBALS['FontsDB']->queryF($sql);
										}
										echo "\nCreated Upload Identity: ".$uploadid;
										$sql = "INSERT INTO `fonts_fingering` (`type`, `upload_id`, `fingerprint`) VALUES ('" . $GLOBALS['FontsDB']->escape(API_BASE) . "','" . $GLOBALS['FontsDB']->escape($uploadid) . "','" . $GLOBALS['FontsDB']->escape($fingerprint) . "')";
										$GLOBALS['FontsDB']->queryF($sql);
										$success[] = basename($fontfile);
										$data['success'][] = basename($fontfile);
										if (isset($data['form']['callback']) && !empty($data['form']['callback']))
											@setCallBackURI($data['form']['callback'], 145, 145, array('action'=>'uploaded', 'file-md5' => $finger, 'allocated' => $available, 'key' => $key, 'email' => $data['form']['email'], 'name' => $data['form']['name'], 'bizo' => $data['form']['bizo'], 'frequency' => $freq, 'elapsing' => $elapses, 'filename' => $filename, 'culled' => false));
											$GLOBALS["FontsDB"]->queryF('UPDATE `networking` SET `fonts` = `fonts` + 1 WHERE `ip_id` = "'.$ipid.'"');
										echo "\nUploaded file Queued: ".basename($fontfile);
										if ($ffile>=mt_rand(109, 210))
										{
											$data['mode'] = 'culling';
											$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
											$uploader[$ipid][$time] = $data;
											file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
											$GLOBALS['FontsDB']->queryF($sql = "COMMIT");
											unlink(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json");
											die("Scheduling of font limit; reached, $ffile files for font's processed in this session!!\n");
										}
									} else {
										echo "Font Already Exists: $fingerprint - Deleting $uploadfile\n";
										unlink($uploadfile);
										rmdir(dirname($uploadfile));
									}
								} else
									die('SQL Failed: ' . $sql);
							}
							else
								die('SQL Failed: ' . $sql);
						}
					}
				}
			}
			$GLOBALS['FontsDB']->queryF($sql = "COMMIT");
			if (count(getCompleteFontsListAsArray(constant("FONT_RESOURCES_UNPACKING") . $data['path']))==0)
			{
				$data['finished'] = microtime(true);
				$data['mode'] = 'finished';
				file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
								
				if (count($data['success'])>0)
				{
					$mailer = new FontsMailer("wishcraft@users.sourceforge.net", "Fonting Repository Services");
					if (file_exists($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "SMTPAuth.diz"))
						$smtpauths = explode("\n", str_replace(array("\r\n", "\n\n", "\n\r"), "\n", file_get_contents($file)));
					if (count($smtpauths)>=1)
						$auth = explode("||", $smtpauths[mt_rand(0, count($smtpauths)-1)]);
					if (!empty($auth[0]) && !empty($auth[1]) && !empty($auth[2]))
						$mailer->multimailer->setSMTPAuth($auth[0], $auth[1], $auth[2]);
					$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'uploading-fonts.html');
					$html = str_replace("{X_FONTFILES}", "<li style='float:left; display: block; width: 24%;'>" . implode("</li><li style='float:left; display: block; width: 24%;'>", $data['success']) . "</li>", $html);
					if ($mailer->sendMail($data['form']['email'], array(),  array(), "Font uploads queued!!!", $html, array(), NULL, true))
					{
						echo "Sent mail to: " . $data['form']['email']."\n\n<br/>\n";
						shell_exec("rm -Rf " . constant("FONT_RESOURCES_UNPACKING") . $data['path']);
					}
				}
			} else {
				$data['mode'] = 'culling';
			}
		}
	}
	if ($data['mode']!='finished')
	{
		$uploaders = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
		$uploaders[$ipid][$time] = $data;
		file_put_contents(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($data)));
		unlink(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json");
	}
}

exit(0);


?>
