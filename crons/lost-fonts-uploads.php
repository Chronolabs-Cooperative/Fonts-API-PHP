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

use FontLib\Font;
require_once dirname(__DIR__).'/class/FontLib/Autoloader.php';

error_reporting(E_ERROR);
set_time_limit(1999);
require_once dirname(__DIR__).'/functions.php';
require_once dirname(__DIR__).'/class/fontages.php';

foreach(getDirListAsArray(FONT_RESOURCES_UNPACKING) as $dir)
	if (checkEmail($dir))
		foreach(getCompleteDirListAsArray(FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir) as $folder)
			foreach(getFontsListAsArray($folder) as $key => $file)
			{
				if (filectime($folder . DIRECTORY_SEPARATOR . $key) <= time() - (3 * 24 * 3600))
				{
					$fontfile = $folder . DIRECTORY_SEPARATOR . $key;
					
					$patheles = array_reverse(explode(DIRECTORY_SEPARATOR, $folder));
					$data = array();
					while(count($patheles)>0 || empty($data))
					{
						if (file_exists($jfile = implode(DIRECTORY_SEPARATOR, array_reverse($patheles)).DIRECTORY_SEPARATOR.'upload.json'))
						{
							$data = json_decode(file_get_contents($jfile), true);
						} else {
							$keys = array_keys($patheles);
							unset($patheles[$keys[0]]);
						}
							
					}
					
					$scope = (!isset($data['form']['scope']['bcc']) && !isset($data['form']['scope']['cc']) && !isset($data['form']['scope']['to'])?'none':(!isset($data['form']['scope']['bcc']) && !isset($data['form']['scope']['cc'])?'to':(!isset($data['form']['scope']['bcc']) || isset($data['form']['scope']['cc'])?'cc':(isset($data['form']['scope']['bcc']) && isset($data['form']['scope']['cc'])?'all':'bcc'))));
					$copypath = FONT_RESOURCES_UNPACKING . DIRECTORY_SEPARATOR . $data['form']['email'] . DIRECTORY_SEPARATOR . time();
					if (!is_dir($copypath))
						mkdir($copypath, 0777, true);
						
							
					$emailcc = cleanWhitespaces(explode("\n", str_replace(array(";",",",":","/","\\","||","|"), "\n", $data['form']['email-cc'])));
					foreach($emailcc as $key => $value)
						if (!checkEmail($value))
							unset($emailcc[$key]);
								
					$emailbcc = cleanWhitespaces(explode("\n", str_replace(array(";",",",":","/","\\","||","|"), "\n", $data['form']['email-bcc'])));
					foreach($emailbcc as $key => $value)
						if (!checkEmail($value))
							unset($emailbcc[$key]);
										
					if (!file_exists($copypath . DIRECTORY_SEPARATOR . basename($fontfile))&&filesize($fontfile)>199)
					{
						if (copy($fontfile, $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile))))
						{
							unlink($fontfile);
							$ffile++;
							$data['process'] = microtime(true);
							$data['mode'] = 'queuing';
							$data['current'] = $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile));
							file_put_contents(constant("FONT_RESOURCES_UNPACKING") . $data['path'] . DIRECTORY_SEPARATOR . "upload.json", json_encode($data));
	
								
							sort($emailcc);
							sort($emailbcc);
							$ccid = md5(json_encode($emailcc));
							$bccid = md5(json_encode($emailbcc));
								
							$sql = "SELECT count(*) FROM `emails` WHERE `id` = '$ccid'";
							list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
							if ($count == 0)
							{
								if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `emails` (`id`, `emails`) VALUES('$ccid', '".mysql_real_escape_string(json_encode($emailcc))."')"))
									die("SQL Failed: $sql;");
							}
								
							$sql = "SELECT count(*) FROM `emails` WHERE `id` = '$bccid'";
							list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
							if ($count == 0)
							{
								if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `emails` (`id`, `emails`) VALUES('$bccid', '".mysql_real_escape_string(json_encode($emailcc))."')"))
									die("SQL Failed: $sql;");
							}
								
							$queued[] = $fontfile;
							$sql = "INSERT INTO `uploads` (`ip_id`, `available`, `key`, `scope`, `email`, `uploaded_file`, `uploaded_path`, `uploaded`, `referee_uri`, `callback`, `bytes`, `batch-size`, `datastore`, `cc`, `bcc`, `frequency`, `elapses`, `longitude`, `latitude`) VALUES ('$ipid','" . $available = mt_rand(7,13) . "','" . mysql_real_escape_string(md5_file($copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile)))) . "','" . mysql_real_escape_string($scope) . "','" . mysql_real_escape_string($email = $data['form']['email']) . "','" . mysql_real_escape_string($filename = strtolower(basename($fontfile))) . "','" . mysql_real_escape_string($copypath) . "','" . time(). "','" . mysql_real_escape_string($_SERVER['HTTP_REFERER']) . "','" . mysql_real_escape_string($callback = $data['form']['callback']) . "'," . (filesize($fontfile)==''?0:filesize($fontfile)) . "," . $size . ",'" . mysql_real_escape_string(json_encode(array('scope' => $data['form']['scope'], 'ipsec' => $locality = json_decode(getURIData("https://lookups.ringwould.com.au/v1/country/".(in_array($ip = whitelistGetIP(true), array('127.0.0.1','10.1.1.1'))?'myself':$ip)."/json.api"), true), 'name' => $data['form']['name'], 'bizo' => $data['form']['bizo'], 'batch-size' => $size))) . "','$ccid','$bccid','" . mysql_real_escape_string($freq = mt_rand(2.76,6.75)*3600*24) . "','" . mysql_real_escape_string($elapse = mt_rand(9,27)*3600*24) . "','". $locality["location"]["coordinates"]["longitude"]."','". $locality["location"]["coordinates"]["latitude"]."')";
							if ($GLOBALS['FontsDB']->queryF($sql))
							{
								$uploadid = $GLOBALS['FontsDB']->getInsertId();
								if ($scope == 'none')
								{
									$sql = "UPDATE `uploads` SET `quizing` = UNIX_TIMESTAMP(), `expired` = UNIX_TIMESTAMP()+1831, `slotting` = 0, `needing` = 1, `finished` = 2, `surveys` = 2, `available` = 0 WHERE `id` = $uploadid";
									$GLOBALS['FontsDB']->queryF($sql);
								}
								echo "\nCreated Upload Identity: ".$uploadid;
								if (!empty($cullist[$finger]))
								{
									foreach($cullist[$finger] as $typeb => $fingers) {
										foreach($fingers as $fingerprint => $file)
										{
											$culled[$finger][$fingerprint][$typeb] = basename($file);
											$sql = "INSERT INTO `fonts_fingering` (`type`, `upload_id`, `fingerprint`) VALUES ('" . mysql_real_escape_string($typeb) . "','" . mysql_real_escape_string($uploadid) . "','" . mysql_real_escape_string($fingerprint) . "')";
											$GLOBALS['FontsDB']->queryF($sql);
										}
									}
								}
								$sql = "INSERT INTO `fonts_fingering` (`type`, `upload_id`, `fingerprint`) VALUES ('" . mysql_real_escape_string($type) . "','" . mysql_real_escape_string($uploadid) . "','" . mysql_real_escape_string($finger) . "')";
								$GLOBALS['FontsDB']->queryF($sql);
								$success[] = basename($fontfile);
								$data['success'][] = basename($fontfile);
								if (isset($data['form']['callback']) && !empty($data['form']['callback']))
									@setCallBackURI($data['form']['callback'], 145, 145, array('action'=>'uploaded', 'file-md5' => $finger, 'allocated' => $available, 'key' => $key, 'email' => $data['form']['email'], 'name' => $data['form']['name'], 'bizo' => $data['form']['bizo'], 'frequency' => $freq, 'elapsing' => $elapses, 'filename' => $filename, 'culled' => false));
								$GLOBALS["FontsDB"]->queryF('UPDATE `networking` SET `fonts` = `fonts` + 1 WHERE `ip_id` = "'.$ipid.'"');
								sleep(mt_rand(4,9));
							}
							else
								die('SQL Failed: ' . $sql);
						}
						echo ".";
					} else 
						echo "x";				
				}
			}