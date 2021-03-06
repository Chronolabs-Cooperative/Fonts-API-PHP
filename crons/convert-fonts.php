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

use FontLib\Font;
require_once dirname(__DIR__).'/class/FontLib/Autoloader.php';

error_reporting(E_ERROR);
set_time_limit(1999);
require_once dirname(__DIR__).'/constants.php';
include_once dirname(__DIR__).'/include/functions.php';
$result = $GLOBALS['APIDB']->queryF("SELECT * FROM `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `uploaded` > '0' AND `converted` = '0' AND `storaged` = 0 ORDER BY RAND() LIMIT ".mt_rand(17,77));
while($row = $GLOBALS['APIDB']->fetchArray($result))
{
	sleep(mt_rand(7,19));
	$skip = false;
	$upldata = json_decode($row['datastore'], true);
	if (file_exists($row['uploaded_path'] . DIRECTORY_SEPARATOR . $row['uploaded_file']))
	{
		$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
		if (substr($fontfile = $row['uploaded_file'], strlen($row['uploaded_file']) - strlen(API_BASE), strlen(API_BASE)) != API_BASE || !is_array($upldata['font']))
		{
			$copypath = $row['uploaded_path'];
			$uploadfile = $copypath . DIRECTORY_SEPARATOR . $row['uploaded_file'];
			$fingerprint = md5_file($uploadfile);
			$sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` WHERE `fingerprint` LIKE '" . $fingerprint . "'";
			list($fingers) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql));
			if ($fingers>0)
			{
				$skip==true;
			}
		}
		
		if ($skip==false)
		{
			
			mkdir($currently = FONT_RESOURCES_CONVERTING . DIRECTORY_SEPARATOR . md5_file($row['uploaded_path'] . DIRECTORY_SEPARATOR . $row['uploaded_file']), 0777, true);
			foreach(cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'font-preferences.diz')) as $type)
			{
				$type = substr($type, 0, strlen($type)-1);
				if (strpos(strtolower($row['uploaded_file']), $type))
				{
					$fonttypes[$type] = $type;
					exec($exe = "mv -fv ".$row['uploaded_path'] . DIRECTORY_SEPARATOR . $row['uploaded_file'] ." ". ($font = $currently . DIRECTORY_SEPARATOR . $row['uploaded_file']), $output, $return);
					echo "Executed: $exe<br/>\n\n$output\n\n<br/><br/>";
					continue;
					continue;
				}
			}
			
			// Generates All Font Files For Fingerprinting
			$fonts = getFontsListAsArray($currently);
			$totalmaking = count(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"))-1;
			exec("cd $currently", $output, $return);
			$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
			foreach($covertscript as $line => $value)
				foreach($fonts as $file => $values)
					if (strpos($value, $values['type'])&&$values['type']!='ttf')
						unset($covertscript[$line]);
			writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).json_encode($fonts)).".pe", implode("\n", $covertscript));
			exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $font), $output, $return);;
			echo "Executed: $exe<br/>\n\n$output\n\n<br/><br/>";
			unlink($script); 
			while($filesold != count($fontfiles = getFontsListAsArray($currently)))
			{
				$filesold = count($fontfiles = getFontsListAsArray($currently));
				@exec("cd $currently", $output, $return);
				$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
				foreach($covertscript as $line => $value)
					foreach($fontfiles as $file => $values)
						if (strpos($value, $values['type'])&&$values['type']!='ttf')
							unset($covertscript[$line]);
				writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).json_encode($fonts)).'.pe', implode("\n", $covertscript));
				foreach($fontfiles as $file => $values)
				{
					@exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $values['file']), $output, $return);
					echo "Executed: $exe<br/>\n\n$output\n\n<br/><br/>";
				}
				unlink($script);
			}
			$parts = explode('.', $row['uploaded_file']);
			unset($parts[count($parts)-1]);
			$fbase = implode(".", $parts);
			if (file_exists($currently . DIRECTORY_SEPARATOR . $fbase . '.ttf') && file_exists($currently . DIRECTORY_SEPARATOR . $fbase . '.afm'))
				MakePHPFont($currently . DIRECTORY_SEPARATOR . $fbase . '.ttf', $currently . DIRECTORY_SEPARATOR . $fbase . '.afm', $currently, true);
			sleep(1);
			$grader = array();
			if (count($files = getFontsListAsArray($currently))>=5)
			{
				$fingerprint = md5(NULL);
				$filecount = 0;
				$expanded = 0;
				foreach(getFileListAsArray($currently) as $path => $file)
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
				$fingerprint = $row['prefix'] . substr($fingerprint, mt_rand(0, strlen($row['prefix'])-1), 32 - strlen($row['prefix']));
				$grader = array();
				foreach($files as $id => $values)
				{
					if (filesize($currently . DIRECTORY_SEPARATOR . $values['file']) > 0)
					{
						$grader[$values['type']] = $currently . DIRECTORY_SEPARATOR . $values['file'];
						$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` (`type`, `upload_id`, `fingerprint`) VALUES ('" . $GLOBALS['APIDB']->escape($values['type']) . "','" . $GLOBALS['APIDB']->escape($row['id']) . "','" . $GLOBALS['APIDB']->escape($md5finger = md5_file($currently . DIRECTORY_SEPARATOR . $values['file'])) . "')";
						$GLOBALS['APIDB']->queryF($sql);
					} else {
						unlink($currently . DIRECTORY_SEPARATOR . $values['file']);
						unset($files[$id]);
					}
				}
				echo "\n\n\n\Font file " . $row['uploaded_file'] . ' - converted to ' . count($files) . " font files!<br/>\n\n\n\n";
				$keies = array_keys($grader);
				foreach(array("ttf", "otf", "woff") as $type)
				{
					if (file_exists($grader[$type]))
					{
						
						$fontage = Font::load($grader[$type]);
						$reserves = getReserves($fontage->getFontFullName());
						$data = array(	"CSS"=>array($fontage->getFontFullName() => $fontage->getFontFullName() . ".css"),'FontType' => $fontage->getFontType(), 'FontCopyright' => $fontage->getFontCopyright(), "FontName" => $fontage->getFontFullName(),
										'FontSubfamily' => $fontage->getFontSubfamily(), "FontSubfamilyID" => $fontage->getFontSubfamilyID(),
										'FontFullName' => $fontage->getFontFullName(), "FontVersion" => $fontage->getFontVersion(),
										'FontWeight' => $fontage->getFontWeight(), "FontPostscriptName" => $fontage->getFontPostscriptName(),'Table' => $fontage->getTable(),
										"UnicodeCharMap" => $fontage->getUnicodeCharMap(),"Reserves" => $reserves, "Files" => getCompleteFilesListAsArray($currently),
										"Font" => array_merge($upldata, array('licence' => API_LICENCE)), "Licences" => array(API_LICENCE => cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . API_LICENCE . DIRECTORY_SEPARATOR . 'LICENCE')))
						);
						writeRawFile($currently . DIRECTORY_SEPARATOR . "font-resource.json", json_encode($data));
						$css[] = "/** " .$fontage->getFontFullName(). " (".$fontage->getFontSubfamily().") */";
						$css[] = "@font-face {";
						foreach($reserves['css'] as $tag => $value)
							$css[] = "\t$tag:\t\t'" .$value. "';";
						$css[] = "\tfont-family:\t\t'" .$fontage->getFontFullName(). "';";
						foreach($files as $type => $values)
							$css[] = ($keies[0]==$values['type']?"\tsrc:\t\t":"\t\t\t")."url('./".$values['file']."') format('".$values['type']."')" .($keies[count($keies)-1]==$values['type']?";":",") ."\t\t/* Filesize: ". filesize($currently . DIRECTORY_SEPARATOR . $values['file']) . " bytes, md5: " . md5_file($currently . DIRECTORY_SEPARATOR . $values['file']) . " */";
						$css[] = "}";
						writeRawFile($currently . DIRECTORY_SEPARATOR . $fontage->getFontFullName() . ".css", implode("\n", $css));
						$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `currently_path` = '".$currently."', `uploaded_file` = '".$row['uploaded_file']."', `converted` = '".time()."', `font_id` = '$fingerprint', `datastore` = '" . json_encode(array_merge($upldata, array("files" => $data['Files'], "FontName" => $fontage->getFontFullName(), "FontFamily" => $fontage->getFontSubfamily()))) . "' WHERE `id` = " . $row['id']);
						unset($fontage);
						unset($grader);
						continue;
					}
				}
				if (!empty($row['callback']))
				{
					@setCallBackURI($row['callback'], 145, 145, array('action'=>'convert', 'key' => $row['key'], 'email' => $row['email'], 'name' => $row['name'], 'bizo' => $row['bizo'], 'files' => $files, 'finish' => microtime(true)));
				}
			} else 
				$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `currently_path` = '".$currently."', `converted` = `converted` - 1 WHERE `id` = " . $row['id']);
		} else
			$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `converted` = `converted` - 1 WHERE `id` = " . $row['id']);
		$GLOBALS['APIDB']->queryF($sql = "COMMIT");
		sleep(mt_rand(10,23));
	} else {
		echo "Doesn't existing killing record: " . ($row['uploaded_path'] . DIRECTORY_SEPARATOR . $row['uploaded_file']) . "\n";
		$GLOBALS['APIDB']->queryF("DELETE FROM `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `id` = " . $row['id']);
	}
}
