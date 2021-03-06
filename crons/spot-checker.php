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

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '315M');
include_once dirname(__DIR__).'/include/functions.php';
include_once dirname(__DIR__).'/class/fontages.php';
include_once dirname(__DIR__).'/class/xcp.class.php';
set_time_limit(7200);

$pool = $GLOBALS['APIDB']->queryF($sql = "SELECT `a`.* FROM `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` as `a` INNER JOIN `" . $GLOBALS['APIDB']->prefix('fonts') . "` as `b` ON `a`.`font_id` = `b`.`id` WHERE `b`.`medium` IN ('FONT_RESOURCES_RESOURCE', 'FONT_RESOURCES_CACHE') ORDER BY `a`.`checked` ASC LIMIT 12");
// Searches For Unrecorded Fonts
while($archive = $GLOBALS['APIDB']->fetchArray($pool))
{
	sleep(mt_rand(20,90));
	$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
	$sortpath = FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . $archive['path'];
	$packfile = $sortpath . DIRECTORY_SEPARATOR . $archive['filename'];
	$updated = false;
	$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('fonts') . "` WHERE `id` = '" . $archive['font_id'] . "'";
	$font = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql));
	$upload = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `font_id` = '" . $archive['font_id'] . "'"));
	$datastore = json_decode($upload['datastore'], true);
	
	if (empty($font['barcode_id']))
	{
		$barcode = new xcp($archive['font_id'], mt_rand(0,254), 12);
		$font['barcode_id'] = $barcode->crc;
		unset($barcode);
	}

	if (empty($font['referee_id']))
	{
		$referee = new xcp($archive['font_id'], mt_rand(0,254), mt_rand(4,7));
		$font['referee_id'] = $referee->crc;
		unset($referee);
	}					
	$file = '';
	switch($font['medium'])
	{
		case 'FONT_RESOURCES_CACHE':
		case 'FONT_RESOURCES_RESOURCE':
			if ($font['medium'] == 'FONT_RESOURCES_CACHE')
			{
				$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
				if (!file_exists(constant($font['medium']) . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename']) && !isset($sessions[md5($archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])]))
				{
					mkdir(constant("FONT_RESOURCES_CACHE") . $archive['path'], 0777, true);
					writeRawFile($file = constant("FONT_RESOURCES_CACHE") . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])));
					$sessions[md5($archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename']);
				} else {
					if ($sessions[md5($archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
						$sessions[md5($archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])]['dropped'] = $sessions[md5($archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])]['dropped'] + $next;
				}
				writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
			} elseif ($font['medium'] == 'FONT_RESOURCES_RESOURCE')
			{
				if (!file_exists(constant($font['medium']) . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename']) && !isset($sessions[md5($archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])]))
				{
					mkdir(constant($font['medium']) . $archive['path'], 0777, true);
					writeRawFile(constant($font['medium']) . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'])));
				}
			}
			if (empty($file))
				$file = FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'];
			$json = json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename'], 'font-resource.json'), true);
			$skip = false;
			break;
		case 'FONT_RESOURCES_PEER':
			$skip = true;
			break;
	}
	
	if ($skip != true)
	{
		echo "File: $file\n";
		try {
			$data = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
		}
		catch (Exception $e)
		{
			$data = array();
		}
		if (!isset($data['Files'])||!isset($data['Licences'])||!isset($data['Files']))
			$updated=true;
		$fingerprint = $archive['font_id'];
		$naming = (!isset($data["FontName"])?getRegionalFontName($archive['font_id']):$data["FontName"]);
		if (!is_dir($currently = $unpackdir = FONT_RESOURCES_UNPACKING . DIRECTORY_SEPARATOR . sha1($file.$archive['font_id'])))
			mkdir ($unpackdir, 0777, true);
		chdir($unpackdir);
		$packing = getExtractionShellExec();
		$cmd = "rm -rfv ./*";
		echo "Executing: $cmd\n";
		exec($cmd, $output);
		$cmd = str_replace("%path", "./", str_replace("%pack", $packfile = $file, (substr($packing['zip'],0,1)!="#"?$packing['zip']:substr($packing['zip'],1))));
		echo "Executing: $cmd\n";
		exec($cmd, $output);
		echo implode("\n", $output);
		foreach(getCompleteFilesListAsArray($currently) as $file => $filz)
			if (substr($file, strlen($file) - strlen(API_BASE), strlen(API_BASE)) == strlen(API_BASE) && !empty($data['Font']))
			{
				writeFontResourceHeader($currently . DIRECTORY_SEPARATOR . $file, $data['Font']['licence'], $data['Font']);
				$fontdata = getBaseFontValueStore($currently . DIRECTORY_SEPARATOR . $file);
				continue;
			}
		
		// Generates All Font Files For Fingerprinting
		$numstarting = count(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
		$totalmaking = count(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"))-1;
		exec("cd $currently", $output, $return);
		$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
		foreach($covertscript as $line => $value)
			foreach($fonts = getFontsListAsArray($currently) as $file => $values)
				if (strpos($value, $values['type']))
					unset($covertscript[$line]);
		writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).json_encode($fonts)).".pe", implode("\n", $covertscript));
		foreach($fonts as $font)
		{
			exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $font['file']), $output, $return);;
			echo "Executed: $exe<br/>\n\n$output\n\n<br/><br/>";
		}
		unlink($script);
		while($filesold != count($fontfiles = getFontsListAsArray($currently)))
		{
			$filesold = count($fontfiles = getFontsListAsArray($currently));
			@exec("cd $currently", $output, $return);
			$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
			$ttffile = $afmfile = '';
			foreach($covertscript as $line => $value)
				foreach($fontfiles as $file => $values)
				{
					if (strpos($value, $values['type']))
						unset($covertscript[$line]);
					switch($values['type'])
					{
						case API_BASE:
							if (!isset($data['font']))
							{
								$data['Font'] = getBaseFontValueStore($currently . DIRECTORY_SEPARATOR . $file);
								$data['Font']['person'] = $datastore['name'];
								$data['Font']['company'] = $datastore['bizo'];
								$data['Font']['uploaded'] = $upload['uploaded'];
								$data['Font']['licence'] = API_LICENCE;
							}
							break;
						case "ttf":
							$ttffile = $file;
							break;
						case "afm":
							$afmfile = $file;
							break;
					}
				}
			writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).json_encode($fonts)).'.pe', implode("\n", $covertscript));
			foreach($fontfiles as $file => $values)
			{
				@exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script %s %s", $script, $values['file']), $output, $return);
				echo "Executed: $exe<br/>\n\n$output\n\n<br/><br/>";
			}
			unlink($script);
			sleep(2);
		}
		if (file_exists($currently . DIRECTORY_SEPARATOR . $ttffile) && file_exists($currently . DIRECTORY_SEPARATOR . $afmfile))
			MakePHPFont($currently . DIRECTORY_SEPARATOR . $ttffile, $currently . DIRECTORY_SEPARATOR . $afmfile, $currently, true);
		else
			echo "PHP Font File not possible!\n";
						
		$fingerprint = $archive['font_id'];
		$naming = $data["FontName"] = spacerName($data["FontName"]);
		
		if (!isset($data['Files']))
			$updated==true;
		if (!isset($data['Font']))
			$updated==true;
		if (!isset($data['Licenses']))
			$updated==true;
		// Builds types table
		$types = array();
		foreach($data['survey'] as $key => $values)
			foreach($values['types'] as $hash => $typals)
				foreach($typals as $hsh => $type)
					$types[$type] = $type;
	
		// Builds nodes table
		$nodes = array();
		if (isset($data['survey']['nodes']))
			foreach($data['survey'] as $key => $values)
			{
				foreach($values['nodes'] as $type => $node)
				{
					$nodes[$node]['type'] = $type;
					$nodes[$node]['node'] = $node;
					if (isset($nodes[$node]['usage']))
						$nodes[$node]['usage']++;
					else
						$nodes[$node]['usage'] = 1;
						$nodes[$node]['weight'] = 1;
				}
			}
		if (count($nodes)==0)
			foreach(getNodesArray(array($naming), array($data['FontFamily'])) as $type => $dnodes)
			{
				foreach($dnodes as $node => $weight)
				{
					$nodes[$node]['type'] = $type;
					$nodes[$node]['node'] = $node;
					if (isset($nodes[$node]['usage']))
						$nodes[$node]['usage']++;
					else
						$nodes[$node]['usage'] = 1;
							$nodes[$node]['weight'] = $weight;
					}
				}
		// Build Contributor List
		$contributors = array();
		$resultb = $GLOBALS['APIDB']->queryF("SELECT * FROM `" . $GLOBALS['APIDB']->prefix('fonts_history') . "` WHERE `upload_id` = '".$upload['id']."' AND `step` LIKE 'finished' ORDER BY RAND()");
		while($contributor = $GLOBALS['APIDB']->fetchArray($resultb))
		{
			$flow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF("SELECT * FROM `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `flow_id` = '".$contributor['flow_id']."'"));
			$contributors[] = array('ip_id' => (empty($contributor['ip_id'])?$flow['ip_id']:$contributor['ip_id']), 'flow_id'=>$contributor['flow_id'], 'history_id' => $contributor['history_id'],
					'upload_id' => $contributor['upload_id'], 'when'=>time(), 'name'=>$GLOBALS['APIDB']->escape($flow['name']));
			$ipnet[(empty($contributor['ip_id'])?$flow['ip_id']:$contributor['ip_id'])] = (empty($contributor['ip_id'])?$flow['ip_id']:$contributor['ip_id']);
		}
		if (count($contributors)>0)
		{
			foreach($names as $key => $values)
			{
				$where = array();
				foreach($contributors as $field => $value)
					$where[] = "`field` LIKE '" . $GLOBALS['APIDB']->escape($value);
				list($count) = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_contributors') . "` WHERE " . implode(" AND ",$where)));
				if ($count==0)
					if ($GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_contributors') . "` (`" . implode('`, `', array_keys($contributors)) . "`) VALUES('" . implode("', '", $contributors) . "')"))
						$updated = true;
				
			}
		}
	
		// Builds naming table
		$names = array();
		foreach($data['survey'] as $key => $values)
		{
			foreach($values['names'] as $title => $title)
			{
				if (!isset($names[$title]))
				{
					$names[$title]['font_id'] = $fingerprint;
					$names[$title]['upload_id'] = $upload['id'];
					$names[$title]['name'] = $title;
					foreach($values['ipid'] as $ip_id => $ipdata)
					{
						$ipnet[$ip_id] = $ip_id;
						if (!isset($names[$title]['longitude']) && !isset($names[$title]['latitude']))
						{
							$names[$title]['longitude'] = $ipdata['longitude'];
							$names[$title]['latitude'] = $ipdata['latitude'];
							$names[$title]['country'] = $GLOBALS['APIDB']->escape($ipdata['country']);
							$names[$title]['region'] = $GLOBALS['APIDB']->escape($ipdata['region']);
							$names[$title]['city'] = $GLOBALS['APIDB']->escape($ipdata['city']);
						} else {
							$names[$title]['longitude'] = $names[$title]['longitude'] + $ipdata['longitude'] / 2;
							$names[$title]['latitude'] = $names[$title]['latitude'] + $ipdata['latitude'] / 2;
						}
					}
				} else {
					foreach($values['ipid'] as $ip_id => $ipdata)
					{
						$ipnet[$ip_id] = $ip_id;
						if (isset($names[$title]['longitude']) && isset($names[$title]['latitude']))
						{
							$names[$title]['longitude'] = $names[$title]['longitude'] + $ipdata['longitude'] / 2;
							$names[$title]['latitude'] = $names[$title]['latitude'] + $ipdata['latitude'] / 2;
						}
					}
				}
			}
		}
	
		$names[$naming]['font_id'] = $fingerprint;
		$names[$naming]['upload_id'] = $upload['id'];
		$names[$naming]['name'] = spacerName($naming);
		$names[$naming]['longitude'] = $data['ipsec']['location']['coordinates']['longitude'];
		$names[$naming]['latitude'] = $data['ipsec']['location']['coordinates']['latitude'];
		$names[$naming]['country'] = $GLOBALS['APIDB']->escape($data['ipsec']['country']['name']);
		$names[$naming]['region'] = $GLOBALS['APIDB']->escape($data['ipsec']['location']['region']);
		$names[$naming]['city'] = $GLOBALS['APIDB']->escape($data['ipsec']['location']['city']);
	
		if (count($names)>0)
		{
			foreach($names as $key => $values)
			{
				$where = array();
				foreach($contributors as $field => $value)
					$where[] = "`field` LIKE '" . $GLOBALS['APIDB']->escape($value);
				list($count) = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF("SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_contributors') . "` WHERE " . implode(" AND ",$where)));
				if ($count==0)
					if ($GLOBALS['APIDB']->queryF("INSERT INTO `fonts_names` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')"))
						$updated = true;
					else 
						unset($names[$key]);
				else
					unset($names[$key]);
			}
		}
	
		if (count($nodes)>0)
		{
			foreach($nodes as $node => $values)
			{
				if (!$nnd = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF("SELECT * FROM `" . $GLOBALS['APIDB']->prefix('nodes') . "` WHERE `node` = '".$values['node']."' AND `type` = '".$values['type']."'"))) {
					if ($GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('nodes') . "` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')"))
						$updated = true;
					$nodes[$node]['node_id'] = $GLOBALS['APIDB']->getInsertId();
				} else 
					unset($nodes[$node]);
			}
		}
	
		// Gets File List in Archive
		if (!empty($fingerprint))
		{
			unlink($currently . DIRECTORY_SEPARATOR . "file.diz");
			$filez = getFileListAsArray($currently);
			foreach(getCompleteDirListAsArray($currently) as $path)
			{
				$filez[str_replace($currently, "", $path)] = getFileListAsArray($path);
			}
	
			$fingerprint = md5($GLOBALS['peer-id']);
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
						$expanded  = $expanded + filesize($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $dat);
					}
				} elseif( file_exists($currently .DIRECTORY_SEPARATOR . $file))
				{
					if (substr($file, strlen($file)-3)=='css'||substr($file, strlen($file)-4)=='json') {
						unlink($currently .DIRECTORY_SEPARATOR . $file);
						unset($filez[$path]);
					} else {
						$filecount++;
						$fingerprint = md5($fingerprint . sha1_file($currently .DIRECTORY_SEPARATOR . $file));
						$expanded = $expanded + filesize($currently .DIRECTORY_SEPARATOR . $file);
					}
				}
			}
		}
	
		$grader = array();
		foreach(getFontsListAsArray($currently) as $id => $values)
		{
			if (filesize($currently . DIRECTORY_SEPARATOR . $values['file']) > 0)
			{
				$grader[$values['type']] = $currently . DIRECTORY_SEPARATOR . $values['file'];
			}
		}
		$keies = array_keys($grader);
		foreach(array("ttf", "otf", "woff") as $type)
		{
			if (file_exists($grader[$type]))
			{
				$fontage = Font::load($grader[$type]);
				$cssfiles = array($fontage->getFontFullName() => ($packname = urlencode($fontage->getFontFullName())) . ".css");
				foreach($names as $title => $datab)
				{
					$cssfiles[$title] = $fingerprint . ".css";
				}
				$data = array(	"CSS"=>$cssfiles,'FontType' => $fontage->getFontType(), 'FontCopyright' => $fontage->getFontCopyright(), "FontName" => $naming = $fontage->getFontName(),
					'FontSubfamily' => $fontage->getFontSubfamily(), "FontSubfamilyID" => $fontage->getFontSubfamilyID(),
					'FontFullName' => $naming = spacerName($fontage->getFontFullName()), "FontVersion" => $fontage->getFontVersion(),
					'FontWeight' => $fontage->getFontWeight(), "FontPostscriptName" => $fontage->getFontPostscriptName(),
					"Files" => getCompleteFilesListAsArray($currently),
					"Font" => $fontdata, "Licences" => array($datastore['Font']['licence'] => cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $datastore['Font']['licence'] . DIRECTORY_SEPARATOR . 'LICENCE')))
					,'Table' => $fontage->getTable(), "UnicodeCharMap" => $fontage->getUnicodeCharMap(),
					"Reserves" => $reserves, 'Names' => removeIdentities($names), 'Nodes' => removeIdentities($nodes),
					"Contributors" => removeIdentities($contributors), 'FontIdentity' => $fingerprint, 'Bytes' => $expanded,
					"Networking" => $networking, "barcode" => $font['barcode_id'], "referee" => $font['referee_id']);
				writeRawFile($currently . DIRECTORY_SEPARATOR . "font-resource.json", json_encode($data));
				$filecount++;
				$css[] = "/** " .$fontage->getFontFullName(). " (".$fontage->getFontSubfamily().") */";
				$css[] = "@font-face {";
				foreach($reserves['css'] as $tag => $value)
					$css[] = "\t$tag:\t\t'" .$value. "';";
				$css[] = "\tfont-family:\t\t'" .$fingerprint. "';";
				foreach($files as $type => $values)
					$css[] = ($keies[0]==$values['type']?"\tsrc:\t\t":"\t\t\t")."url('./".$values['file']."') format('".$values['type']."')" .($keies[count($keies)-1]==$values['type']?";":",") ."\t\t/* Filesize: ". filesize($currently . DIRECTORY_SEPARATOR . $values['file']) . " bytes, md5: " . md5_file($currently . DIRECTORY_SEPARATOR . $values['file']) . " */";
				$css[] = "}";
				writeRawFile($currently . DIRECTORY_SEPARATOR . $fingerprint . ".css", implode("\n", $css));
				$filecount++;
				unset($grader);
				continue;
			}
		}
		
		$fontfiles = getCompleteFontsListAsArray($currently);
		foreach($fontfiles['ttf'] as $md5 => $preview)
		{
			if (isset($preview) && file_exists($preview))
			{
				require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
				$img = WideImage::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-preview.png');
				$height = $img->getHeight();
				$lsize = 66;
				$ssize = 14;
				$step = mt_rand(8,11);
				$canvas = $img->getCanvas();
				$i=0;
				while($i<$height)
				{
					$canvas->useFont($preview, $point = $ssize + ($lsize - (($lsize  * ($i/$height)))), $img->allocateColor(0, 0, 0));
					$canvas->writeText(19, $i, getFontPreviewText());
					$i=$i+$point + $step;
				}
				$canvas->useFont($preview, 14, $img->allocateColor(0, 0, 0));
				$canvas->writeText('right', 'bottom', API_URL);
				$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'Font Preview for '.$naming.'.png');
				unset($img);
				if (strlen($naming)<=9)
				{
					$img = WideImage::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-small.png');
				} elseif (strlen($naming)<=12)
				{
					$img = WideImage::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-medium.png');
				}elseif (strlen($naming)<=21)
				{
					$img = WideImage::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-large.png');
				} else
				{
					$img = WideImage::load(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-extra.png');
				}
				$height = $img->getHeight();
				$point = $height * (32/99);
				$canvas = $img->getCanvas();
				$canvas->useFont($font, $point, $img->allocateColor(0, 0, 0));
				$canvas->writeText('center', 'center', $naming);
				$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'font-name-banner.png');
				unset($img);
			}
		}
	
		foreach(getCompleteFilesListAsArray($currently) as $file) 
			if (substr($file, strlen($file)-5) == '.glif')
			{
				$glyph = getGlyphArrayFromXML(xml2array(file_get_contents($file)));
				if (!empty($glyph))
				{
					$sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` WHERE `fingerprint` LIKE '" . $glyph['fingerprint'] . "'";
					$glyphid = md5($fingerprint.$glyph['fingerprint'].json_encode($glyph));
					if (!$row = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql)))
					{
						$contours = $pointers = $smoothers = 0;
						foreach($glyph['contours'] as $contour => $points)
						{
							$contours++;
							foreach($points as $weight => $values)
							{
								$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_glyphs_contours') . "` (`font_id`, `glyph_id`, `contour`, `weight`, `x`, `y`, `type`, `smooth`, `created`) VALUES('$fingerprint', '$glyphid', '$contour', '$weight', '" . $values['x'] . "', '" . $values['y'] . "', '" . $values['type'] . "', '" . $values['smooth'] . "', UNIX_TIMESTAMP())";
								if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; }
								if ($values['smooth']!='-----') { $smoothers++; }
								$pointers++;
							}
						}
						$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` (`font_id`, `glyph_id`, `fingerprint`, `name`, `ufofile`, `unicode`, `format`, `width`, `contours`, `pointers`, `smoothers`, `created`, `occurences`, `addon`) VALUES('$fingerprint', '$glyphid', '".$glyph['fingerprint']."', '".$glyph['name']."', '" . basename($file) . "', '".$glyph['unicode']."', '".$glyph['format']."', '" . $glyph['width'] . "', '$contours', '$pointers', '$smoothers', UNIX_TIMESTAMP(), 1, 'no')";
						if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; }
					} elseif (!empty($row)) {
						$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` (`font_id`, `glyph_id`, `fingerprint`, `name`, `ufofile`, `created`, `addon`, `addon_font_id`, `addon_glyph_id`) VALUES('$fingerprint', '$glyphid', '".$glyph['fingerprint']."', '".$glyph['name']."', '" . basename($file) . "',  UNIX_TIMESTAMP(), 'yes', '".$row['font_id'] . "', '" . $row['glyph_id'] . "')";
						if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; }
						$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` SET `occurences` = `occurences` + 1 WHERE `glyph_id` = '".$row['glyph_id']. "'";
						if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; }
					}
				}
			}
		
		$filez = array();
		foreach(getCompleteDirListAsArray($currently) as $path)
		{
			$filez[(strlen($bpath = str_replace($currently, "", $path))==0?"/":$bpath)] = getFileListAsArray($path);
		}
		
		$ffls = 0;
		foreach($filez as $path => $files)
		{
			if (!is_array($files))
			{
				$ffls++;
				echo "Checking file $ffls missing in files index: $path/$files\n";
				list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $fingerprint. "' AND  `archive_id` = '" . $archive_id. "' AND `path` LIKE '/' AND `filename` LIKE '$files'"));
				if ($count==0)
				{
					$exts = explode('.', $files);
					$ext = $exts[count($exts)-1];
					$type = 'other';
					foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $filetype)
						if (strpos($ext, ".$filetype")>0)
							$type = $filetype;
					if (empty($type))
						$type = 'other';
					if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $fingerprint. "', '" . $archive_id. "','$type','$ext','$files','/','" .filesize($currently . DIRECTORY_SEPARATOR . $files) . "',0,unix_timestamp())"))
						$updated = true;
				}
			} elseif (is_array($files))
			{
				foreach($files as $ky => $filz)
				{
					$ffls++;
					echo "Checking file $ffls missing in files index: $path/$filz\n";
					list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $fingerprint. "' AND  `archive_id` = '" . $archive_id. "' AND `path` LIKE '$path' AND `filename` LIKE '$filz'"));
					if ($count==0)
					{
						$exts = explode('.', $filz);
						$ext = $exts[count($exts)-1];
						$type = 'other';
						foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $filetype)
							if (strpos($ext, ".$filetype")>0)
								$type = $filetype;
						if (empty($type))
							$type = 'other';
						if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $fingerprint. "', '" . $archive_id. "','$type','$ext','$filz','$path','" .filesize($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $filz) . "',0,unix_timestamp())"))
							$updated = true;
					}
				}
			}
		}
		
		writeRawFile($comment = $currently . DIRECTORY_SEPARATOR . "file.diz", getFileDIZ(0, $upload['id'], $fingerprint, $filename = $packname.'.zip', $expanded, $filez));
		
		$filez = array();
		foreach(getCompleteDirListAsArray($currently) as $path)
		{
			$filez[(strlen($bpath = str_replace($currently, "", $path))==0?"/":$bpath)] = getFileListAsArray($path);
		}
		
		foreach($filez as $path => $files)
		{
			if (!is_array($files))
			{
				$ffls++;
				echo "Checking file $ffls missing in files index: $path/$files\n";
				$zipfil = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $fingerprint. "' AND  `archive_id` = '" . $archive_id. "' AND `path` LIKE '/' AND `filename` LIKE '$files'"));
				if (($bytes = filesize($currently . DIRECTORY_SEPARATOR . $zipfil['path'] . DIRECTORY_SEPARATOR . $zipfil['filename']))!=$zipfil['bytes'])
				{
					$exts = explode('.', $zipfil['filename']);
					$ext = $exts[count($exts)-1];
					$type = 'other';
					foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $filetype)
						if (strpos($ext, ".$filetype")>0)
							$type = $filetype;
					if (empty($type))
						$type = 'other';
					if ($GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` SET  `updated` = UNIX_TIMESTAMP(), `updates` = `updates` + 1, `bytes` = '$bytes', `type` = '$type'  WHERE `id` = " . $zipfil['id']))
						$updated = true;
				}
			} elseif (is_array($files))
			{
				foreach($files as $ky => $filz)
				{
					$zipfil = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * FROM `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $fingerprint. "' AND  `archive_id` = '" . $archive_id. "' AND `path` LIKE '$path' AND `filename` LIKE '$filz'"));
					if (($bytes = filesize($currently . DIRECTORY_SEPARATOR . $zipfil['path'] . DIRECTORY_SEPARATOR . $zipfil['filename']))!=$zipfil['bytes'])
					{
						$exts = explode('.', $zipfil['filename']);
						$ext = $exts[count($exts)-1];
						$type = 'other';
						foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $filetype)
							if (strpos($ext, ".$filetype")>0)
								$type = $filetype;
						if (empty($type))
							$type = 'other';
						if ($GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` SET `updated` = UNIX_TIMESTAMP(), `updates` = `updates` + 1, `type` = '$type' , bytes` = '$bytes' WHERE `id` = " . $zipfil['id']))
							$updated = true;
					}
				}
			}
		}
		
		if ($updated==true)	
		{
			deleteFilesNotListedByArray($currently, array(API_BASE, 'file.diz', 'resource.json', 'LICENCE'));
			foreach(getCompleteFilesListAsArray($currently) as $file)
				if (substr($file, strlen($file)-strlen(API_BASE), strlen(API_BASE)) == API_BASE)
					writeFontRepositoryHeader($currently . DIRECTORY_SEPARATOR . $file, $data['Font']['licence'], $data['Font']);
			if (!file_exists($currently . DIRECTORY_SEPARATOR . 'LICENCE'))
				copy(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $data['Font']['licence'] . DIRECTORY_SEPARATOR . 'LICENCE', $currently . DIRECTORY_SEPARATOR . 'LICENCE');
			foreach(getFontsListAsArray($currently.$currently) as $filez)
			{
				if (file_exists($currently.DIRECTORY_SEPARATOR.$filez['file']))
					unlink($currently.DIRECTORY_SEPARATOR.$filez['file']);
				copy($currently.$currently.DIRECTORY_SEPARATOR.$filez['file'], $currently.DIRECTORY_SEPARATOR.$filez['file']);
				if (file_exists($currently.DIRECTORY_SEPARATOR.$filez['file']) && file_exists($currently.$currently.DIRECTORY_SEPARATOR.$filez['file']))
					unlink($currently.$currently.DIRECTORY_SEPARATOR.$filez['file']);
				$base = $currently.$currently.DIRECTORY_SEPARATOR;
				foreach(explode(DIRECTORY_SEPARATOR, $currently.$currently.DIRECTORY_SEPARATOR) as $path)
				{
					rmdir($base);
					$base=dirname($base);
				}
			}
			foreach(getCompleteFilesListAsArray($currently) as $file)
				if (substr($file, strlen($file)-strlen(API_BASE), strlen(API_BASE)) == API_BASE)
				{
					echo "Deleteing: $packfile ~ for regeneration\n";
					unlink($packfile);
					$packing = getArchivingShellExec();
					$stamping = getStampingShellExec();
					if (!is_dir($sortpath))
						mkdir($sortpath, 0777, true);
					chdir($currently);
					$cmda = str_replace("%folder", "./", str_replace("%pack", $packfile, str_replace("%comment", $comment, (substr($packing['zip'],0,1)!="#"?$packing['zip']:substr($packing['zip'],1)))));
					echo "Executing: $cmda\n";
					exec($cmda, $output, $resolv);
					if (isset($stamping['zip']))
					{
						$cmdb = str_replace("%pack", $packfile, str_replace("%comment", './file.diz', $stamping['zip']));
						echo "Executing: $cmdb\n";
						exec($cmdb, $output, $resolve);
						echo implode("\n", $output);
					}
					if (!file_exists($packfile))
						die("File not found: $packfile ~~ Failed: $cmda");
					echo implode("\n", $output);
					if (file_exists($packfile))
					{
						$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `cleaned` = '".time()."', `datastore` = \"".$GLOBALS['APIDB']->escape(json_encode($data))."\" WHERE `id` = " . $upload['id']);
						foreach($nodes as $type => $values)
						{
							if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('nodes_linking') . "` (`font_id`, `node_id`) VALUES ('$fingerprint', '".$values['node_id']."')"))
								die("Failed SQL: $sql;\n");
						}
						list($nodecount) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM  `" . $GLOBALS['APIDB']->prefix('nodes_linking') . "` WHERE `font_id` = '" . $fingerprint . "'"));
						if (!$GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts') . "` SET `nodes` = '$nodecount', `medium` = 'FONT_RESOURCES_RESOURCE', `version` = '" . $data['Font']['version'] . "', `date` = '" . $data['Font']['date'] . "', `uploaded` = '" . $data['Font']['uploaded'] . "', `licence` = '" . $data['Font']['licence'] . "', `company` = '" . $data['Font']['company'] . "', `matrix` = '" . $data['Font']['matrix'] . "', `bbox` = '" . $data['Font']['bbox'] . "', `painttype` = '" . $data['Font']['painttype'] . "', `info` = '" . $data['Font']['info'] . "', `family` = '" . $data['Font']['family'] . "', `weight` = '" . $data['Font']['weight'] . "', `fstype` = '" . $data['Font']['fstype'] . "', `italicangle` = '" . $data['Font']['italicangle'] . "', `fixedpitch` = '" . $data['Font']['fixedpitch'] . "', `underlineposition` = '" . $data['Font']['underlineposition'] . "', `underlinethickness` = '" . $data['Font']['underlinethickness'] . "', `barcode_id` = '" . $data['barcode'] . "', `referee_id` = '" . $data['referee'] . "' WHERE `id` = '$fingerprint'"))
							die("Failed SQL: $sql;\n");
						if (!$GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` SET `repacked` = UNIX_TIMESTAMP() WHERE `id` = '" . $archive['id'] . "'"))
							die("Failed SQL: $sql;\n");
					}
		
					if (in_array('svn', explode(",", API_REPOSITORY)))
					{
						if (!file_exists(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-update.sh'))
						{
							$bash=array();
							$bash[] = "#! bash";
							$bash[] = "cd ".FONT_RESOURCES_RESOURCE;
							$bash[] = "svn cleanup";
							$bash[] = "svn update";
						} else {
							echo "Setting Memory Limit To: " .(floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-update.sh')) / (1024) + 50 . "M") . "/n";
							ini_set('memory_limit', floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-update.sh') / (1024) + 50) . "M");
							$bash = cleanWhitespaces(file(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-update.sh'));
							unset($bash[count($bash)-1]);
						}
						$bash[] = "cd " . dirname($packfile);
						$bash[] = "svn cleanup";
						$bash[] = "rm -fv ./zi*";
						$bash[] = "svn add . --force";
						$bash[] = "svn commit -m \"Updating Repository for the font: $naming\"";
						$bash[] = "unlink " . dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-update.sh';
						writeRawFile(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-update.sh', implode("\n", $bash));
						unset($bash);
					}
					if (in_array('git', explode(",", API_REPOSITORY)))
					{
						if (!file_exists(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-update.sh'))
						{
							$bash=array();
							$bash[] = "#! bash";
							$bash[] = "cd ".FONT_RESOURCES_RESOURCE;
						} else {
							echo "Setting Memory Limit To: " .(floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-update.sh')) / (1024) + 50 . "M") . "/n";
							ini_set('memory_limit', floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-update.sh') / (1024) + 50) . "M");
							$bash = cleanWhitespaces(file(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-update.sh'));
							unset($bash[count($bash)-1]);
						}
						$bash[] = "cd " . dirname($packfile);
						$bash[] = "unlink .gitignore";
						$bash[] = "rm -fv ./zi*";
						$bash[] = "git add ".basename($packfile)."";
						$bash[] = "git commit -m \"Updating Repository for 1st time; the font: $naming\"";
						$bash[] = "git push origin master";
						$bash[] = "unlink " . dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-update.sh';
						writeRawFile(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-update.sh', implode("\n", $bash));
						unset($bash);
					}
				continue;
				
			}
		}
		sleep(22);
		exec($cmd = "rm -Rfv $currently", $output);
		echo "Executing: $cmd\n".implode("\n", $output);
		$packfile = $fingerprint = '';
		if (!$GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` SET `checked` = UNIX_TIMESTAMP() WHERE `id` = '" . $archive['id'] . "'"))
			die("Failed SQL: $sql;\n");
	}
	if ($updated == true)
		echo ".";
	else
		echo 'x';
	$GLOBALS['APIDB']->queryF($sql = "COMMIT");	
}
exit(0);


?>
