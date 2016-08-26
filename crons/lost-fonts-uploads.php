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
require_once dirname(__DIR__).'/class/fontsmailer.php';
$basefolders = getDirListAsArray(FONT_RESOURCES_UNPACKING);
mt_srand(mt_rand(-microtime(true), microtime(true)));
mt_srand(mt_rand(-microtime(true), microtime(true)));
mt_srand(mt_rand(-microtime(true), microtime(true)));
mt_srand(mt_rand(-microtime(true), microtime(true)));
while (mt_rand(-15,2)<-2)
{
	shuffle($basefolders);
	shuffle($basefolders);
	shuffle($basefolders);
}
foreach($basefolders as $dir)
	if (checkEmail($dir))
	{
		$secondary = getDirListAsArray(FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir);
		while (mt_rand(-15,2)<-2)
		{
			shuffle($secondary);
			shuffle($secondary);
			shuffle($secondary);
		}
		foreach($secondary as $ndd)
		{
			$listing = getCompleteDirListAsArray(FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$ndd);
			while (mt_rand(-15,2)>-5)
			{
				shuffle($listing);
				shuffle($listing);
				shuffle($listing);
			}
			$k=0;
			foreach($listing as $folder)
			{
				if ($k==0)
				{
					$found = false;
					$data = array();
					if ($folder != FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$ndd)
					{
						$patheles = array_reverse(explode(DIRECTORY_SEPARATOR, $folder));
						while(count($patheles) <= $k)
						{
							$k++;
							echo "\nLooking for Source: " . ($jfile = implode(DIRECTORY_SEPARATOR, array_reverse($patheles)).DIRECTORY_SEPARATOR.'upload.json');
							if (implode(DIRECTORY_SEPARATOR, array_reverse($patheles)).DIRECTORY_SEPARATOR.'upload.json'==DIRECTORY_SEPARATOR.'upload.json') {
								continue;
								continue;
							}
							else
								if (file_exists($jfile) && empty($data))
								{
									$found = true;
									$data = json_decode(file_get_contents($jfile), true);
									continue;
								} elseif (empty($data)) {
									$keys = array_keys($patheles);
									unset($patheles[$keys[0]]);
								}				
						}
					} elseif ($k==0) {
						$k++;
						echo "\nLooking for Source: " . ($jfile = FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$ndd.DIRECTORY_SEPARATOR.'upload.json');
						if (FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$ndd.DIRECTORY_SEPARATOR.'upload.json'==DIRECTORY_SEPARATOR.'upload.json')
						{
							continue;
							continue;
						}
						else
							if (file_exists($jfile) && empty($data) && filesize($jfile)>=96)
							{
								$found = true;
								$data = json_decode(file_get_contents($jfile), true);
								continue;
							}
						
					}
					if ($found == true)
					{
						echo(" (found)");
					}
					if ($found == true && $data['time'] < time() - (0.441 * 3600) && !empty($data))
					{
						$data['mode'] = 'culling';
						$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
						$uploader[$GLOBALS['peerid']][time()] = $data;
						file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
						echo ("\nReimported into Que: $jfile\n\n");
						if (unlink($jfile))
							echo (" (deleted)\n\n");			
					} elseif (($found == true && empty($data) || filesize($jfile)<=96) && strlen(dirname($jfile))>1) {
						echo(" -- Empty Resource Found!!\n\n");
						$packing = getArchivingShellExec();
						if (!is_dir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lost'))
							mkdir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lost', 0777, true);
						$packfile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lost' . DIRECTORY_SEPARATOR . $dir . '-' . $ndd . '.7z';
						$email = $dir;
						chdir(dirname($jfile));
						exec("cd " . dirname($jfile), $output, $resolve);
						$cmdb = str_replace("%pack", $packfile, str_replace("%folder", dirname($jfile), $packing['7z']));
						echo "Executing: $cmdb\n";
						exec($cmdb, $output, $resolve);
						echo implode("\n", $output);
						if (!file_exists($packfile))
							die("File not found: $packfile ~~ Failed: $cmda");
						unlink($jfile);
						$fontfilez = getCompleteFilesListAsArray(dirname($jfile));
						if (filesize($packfile)>501)
						{
							$mailer = new FontsMailer(API_EMAIL_ADDY, API_EMAIL_FROM);
							if (file_exists($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "SMTPAuth.diz"))
								$smtpauths = explode("\n", str_replace(array("\r\n", "\n\n", "\n\r"), "\n", file_get_contents($file)));
							if (count($smtpauths)>=1)
								$auth = explode("||", $smtpauths[mt_rand(0, count($smtpauths)-1)]);
							if (!empty($auth[0]) && !empty($auth[1]) && !empty($auth[2]))
								$mailer->multimailer->setSMTPAuth($auth[0], $auth[1], $auth[2]);
							$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'lost-fonts-uploads.html');
							$html = str_replace("{X_EMAIL}", $email, $html);
							$html = str_replace("{X_FONTFILE}", API_URL . '/lost/' . basename($packfile), $html);
							$html = str_replace("{X_FONTFILES}", "<li style='float:left; display: block; width: 24%;'>" . implode("</li><li style='float:left; display: block; width: 24%;'>", $fontfilez) . "</li>", $html);
							echo "\n";
							if ($mailer->sendMail(array($email=>$email), array(API_SYSTEMADMIN_EMAIL=>API_SYSTEMADMIN_EMAIL),  array(), "Lost uploading queued!!! Please Resubmit!! -:[ fonts.labs.coop ]:-", $html, array(), NULL, true))
							{
								echo "Sent mail to: " . $email . "\n\n<br/>\n";
								shell_exec("rm -Rf ".dirname($jfile));
							}
						} else {
							unlink($packfile);
							shell_exec("rm -Rf ".dirname($jfile));
						}
					} else continue;
				} else continue;
			}
		}
	}