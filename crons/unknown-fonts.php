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

use FontLib\Font;
require_once dirname(__DIR__).'/class/FontLib/Autoloader.php';

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
ini_set('memory_limit', '555M');
include_once dirname(__DIR__).'/constants.php';
include_once dirname(__DIR__).'/class/xcp.class.php';
set_time_limit(7200);
// Searches For Unrecorded Fonts
$folders = array_unique(array_merge(array('0','1','2','3','4','5','6','7','8','9','_','%','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'), getDirListAsArray(FONT_RESOURCES_RESOURCE)));
while(mt_rand(-4,7)<5)
{
	shuffle($folders);
	shuffle($folders);
	shuffle($folders);
}
foreach($folders as $folder)
{
	$zips = getCompleteZipListAsArray(FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . $folder);
	while(mt_rand(-14,7)<5)
	{
		shuffle($zips);
		shuffle($zips);
		shuffle($zips);
	}
	foreach($zips as $md5 => $file)
	{
		$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
		echo "File: $file\n";
		$data = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
		$sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts') . "` WHERE `id` LIKE '%s'";
		$count = -1;
		if (!empty($data["FontIdentity"]))
			list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF(sprintf($sql, $data["FontIdentity"])));
		if ($count==0 && !empty($data["FontIdentity"]))
		{
			$fingerprint = $data["FontIdentity"];
			if (!is_dir($currently = $unpackdir = FONT_RESOURCES_UNPACKING . DIRECTORY_SEPARATOR . $data["FontIdentity"]))
				mkdir ($unpackdir, 0777, true);
			chdir($unpackdir);
			$packing = getExtractionShellexec();
			$cmd = "rm -rfv ./*";
			echo "\n\n\nExecuting: $cmd\n";
			$output = array(); exec($cmd, $output);
			$cmd = str_replace("%path", "./", str_replace("%pack", $packfile = $file, (substr($packing['zip'],0,1)!="#"?$packing['zip']:substr($packing['zip'],1))));
			echo "\n\n\nExecuting: $cmd\n";
			$output = array(); exec($cmd, $output);
			echo implode("\n", $output);
			
			$fonts = getFontsListAsArray($currently);
			$totalmaking = count(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts-upload.pe"))-1;
			$output = array(); exec("cd $currently", $output, $return);
			$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts-upload.pe"));
			foreach($covertscript as $line => $value)
				foreach(getFontsListAsArray($currently) as $file => $values)
					if (strpos($value, $values['type']))
						unset($covertscript[$line]);
			writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).json_encode($fonts)).".pe", implode("\n", $covertscript));
			foreach($fonts as $font)
			{
				$output = array(); exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $font['file']), $output, $return);;
				echo "\n\n\nExecuting: $exe\n".implode("\n", $output);
			}
			if (!empty($data["Font"]))
			foreach(getFontsListAsArray($currently) as $filz)
				if (strpos($filz['file'], API_BASE))
				{
					writeFontResourceHeader($currently . DIRECTORY_SEPARATOR . $filz['file'], $data["Font"]['licence'], $data["Font"]);
					$fdata = getBaseFontValueStore($currently . DIRECTORY_SEPARATOR . $filz['file']);
				}
			deleteFilesNotListedByArray($currently, array(".".API_BASE));
			$fonts = getFontsListAsArray($currently);
			$totalmaking = count(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"))-1;
			$output = array(); exec("cd $currently", $output, $return);
			$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
			foreach($covertscript as $line => $value)
				foreach(getFontsListAsArray($currently) as $file => $values)
					if (strpos($value, $values['type']))
						unset($covertscript[$line]);
			writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).json_encode($fonts)).".pe", implode("\n", $covertscript));
			foreach($fonts as $font)
			{
				$output = array(); exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $font['file']), $output, $return);;
				echo "\n\n\nExecuting: $exe\n".implode("\n", $output);
			}
			unlink($script);
			while($filesold != count($fontfiles = getFontsListAsArray($currently)))
			{
				$filesold = count($fontfiles = getFontsListAsArray($currently));
				@$output = array(); exec("cd $currently", $output, $return);
				$covertscript = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
				$ttffile = $afmfile = '';
				foreach($covertscript as $line => $value)
					foreach($fontfiles as $file => $values)
					{
						if (strpos($value, $values['type']))
							unset($covertscript[$line]);
						switch($values['type'])
						{
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
						chdir($currently);
						@$output = array(); exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script %s %s", $script, $values['file']), $output, $return);
						echo "\n\n\nExecuting: $exe\n".implode("\n", $output);
					}
					unlink($script);
					sleep(2);
			}
			if (file_exists($currently . DIRECTORY_SEPARATOR . $ttffile) && file_exists($currently . DIRECTORY_SEPARATOR . $afmfile))
				MakePHPFont($currently . DIRECTORY_SEPARATOR . $ttffile, $currently . DIRECTORY_SEPARATOR . $afmfile, $currently, true);
			else
				echo "PHP Font File not possible!\n";
			
			$fingerprint = $data["FontIdentity"];
			$naming = $data["FontName"] = spacerName($data["FontName"]);
			
			foreach(getCompleteFilesListAsArray($currently) as $file => $filz)
				if (substr($file, strlen($file) - strlen(API_BASE), strlen(API_BASE)) == strlen(API_BASE) && !empty($data['Font']))
				{
					$fontdata = getBaseFontValueStore($currently . DIRECTORY_SEPARATOR . $file);
					continue;
				}
		
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
	
			// Builds naming table
			$names = array();
			foreach($data['survey'] as $key => $values)
			{
				foreach($values['names'] as $title => $title)
				{
					if (!isset($names[$title]))
					{
						$names[$title]['font_id'] = $upload['font_id'];
						$names[$title]['upload_id'] = -1;
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
	
	
			// gets networking
			$networking = array();
			$resultc = $GLOBALS['APIDB']->queryF("SELECT * from `networking` WHERE `ip_id` IN ('".implode("', '", $ipnet) . "')");
			while($net = $GLOBALS['APIDB']->fetchArray($resultc))
			{
				$networking[$net['ip_id']] = $net;
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
			if (count($files = getFontsListAsArray($currently))>=5)
			{
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
							$cssfiles[$title] = $upload['font_id'] . ".css";
						}
						$barcode = new xcp($fingerprint, mt_rand(0,254), 12);
						$referee = new xcp($fingerprint, mt_rand(0,254), mt_rand(4,7));
						$data = array(	"CSS"=>$cssfiles,'FontType' => $fontage->getFontType(), 'FontCopyright' => $fontage->getFontCopyright(), "FontName" => $fontage->getFontName(),
								'FontSubfamily' => $fontage->getFontSubfamily(), "FontSubfamilyID" => $fontage->getFontSubfamilyID(),
								'FontFullName' => $naming = spacerName($fontage->getFontFullName()), "FontVersion" => $fontage->getFontVersion(),
								'FontWeight' => $fontage->getFontWeight(), "FontPostscriptName" => $fontage->getFontPostscriptName(),
								"Files" => $filez, "Font" => $fdata, 'Table' => $fontage->getTable(), "UnicodeCharMap" => $fontage->getUnicodeCharMap(),
								"Reserves" => $reserves, 'Names' => removeIdentities($names), 'Nodes' => removeIdentities($nodes),
								"Contributors" => removeIdentities($contributors), 'FontIdentity' => $fingerprint, 'Bytes' => $expanded,
								"Networking" => $networking, "barcode" => $barcode->crc, "referee" => $referee->crc);
						unset($barcode);
						unset($referee);
						writeRawFile($currently . DIRECTORY_SEPARATOR . "font-resource.json", json_encode($data));
						$filecount++;
						$css[] = "/** " .$fontage->getFontFullName(). " (".$fontage->getFontSubfamily().") */";
						$css[] = "@font-face {";
						foreach($reserves['css'] as $tag => $value)
							$css[] = "\t$tag:\t\t'" .$value. "';";
							$css[] = "\tfont-family:\t\t'" .$upload['font_id']. "';";
							foreach($files as $type => $values)
								$css[] = ($keies[0]==$values['type']?"\tsrc:\t\t":"\t\t\t")."url('./".$values['file']."') format('".$values['type']."')" .($keies[count($keies)-1]==$values['type']?";":",") ."\t\t/* Filesize: ". filesize($currently . DIRECTORY_SEPARATOR . $values['file']) . " bytes, md5: " . md5_file($currently . DIRECTORY_SEPARATOR . $values['file']) . " */";
								$css[] = "}";
								writeRawFile($currently . DIRECTORY_SEPARATOR . $fingerprint . ".css", implode("\n", $css));
								$filecount++;
								unset($grader);
								continue;
					}
				}
				if ( $naming != 'fontnamespaced')
				{
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
								$canvas->writeText(19, $i, "All Work and No Pay Makes Wishcraft a Dull Bored!");
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
				}	
				
	
				$names[$naming]['font_id'] = $fingerprint;
				$names[$naming]['upload_id'] = -1;
				$names[$naming]['name'] = spacerName($naming);
				$names[$naming]['longitude'] = number_format(!isset($data['ipsec']['location']['coordinates']['longitude'])||empty($data['ipsec']['location']['coordinates']['longitude'])?0.00000001:$data['ipsec']['location']['coordinates']['longitude'], 8);
				$names[$naming]['latitude'] = number_format(!isset($data['ipsec']['location']['coordinates']['latitude'])||empty($data['ipsec']['location']['coordinates']['latitude'])?0.00000001:$data['ipsec']['location']['coordinates']['latitude'], 8);
				$names[$naming]['country'] = $GLOBALS['APIDB']->escape($data['ipsec']['country']['name']);
				$names[$naming]['region'] = $GLOBALS['APIDB']->escape($data['ipsec']['location']['region']);
				$names[$naming]['city'] = $GLOBALS['APIDB']->escape($data['ipsec']['location']['city']);
				
				$namings = 0;
				if (count($names)>0)
				{
					foreach($names as $key => $values)
					{
						if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `fonts_names` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')"))
							$namings++;
						elseif ($GLOBALS['APIDB']->errno()<>0)
							die("SQL Failed: $sql :: ".$GLOBALS['APIDB']->error());
					}
				}
				$nodings = 0;
				if (count($nodes)>0)
				{
					foreach($nodes as $node => $values)
					{
						$nodings++;
						if ($row = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('nodes') . "` WHERE `node` = '".$values['node']."' AND `type` = '".$values['type']."'"))) {
							$nodes[$node]['node_id'] = $row['id'];
							$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('nodes') . "` SET `usage` = `usage` + '" . $values['usage'] . "' WHERE `id` = '".$row['id']."'");
						} else {
							$GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('nodes') . "` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')");
							$nodes[$node]['node_id'] = $GLOBALS['APIDB']->getInsertId();
						}
					}
				}
				
				$filez = array("/"=>getFileListAsArray($currently));
				foreach(getCompleteDirListAsArray($currently) as $path)
				{
					$filez[str_replace($currently, "", $path)] = getFileListAsArray($path);
				}
	
				writeRawFile($currently . DIRECTORY_SEPARATOR . "file.diz", $comment = getFileDIZ(0, 0, $fingerprint, $filename = $packname.'.zip', $expanded, $filez));
				$comment = addslashes($comment);
	
				list($fingercount) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM  `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` WHERE `font_id` = '$fingerprint'"));
				if (file_exists($packfile))
				{
					if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` (`font_id`, `filename`, `path`, `repository`, `files`, `bytes`, `fingerprint`, `hits`, `packing`) VALUES ('$fingerprint', '".basename($packfile)."', '" . str_replace(FONT_RESOURCES_RESOURCE, '', dirname($packfile)) . "', '" . FONT_RESOURCES_STORE . "', '$filecount', '" . filesize($packfile) . "', '" . md5_file($packfile) . "', 0, 'zip')"))
						die("SQL Failed: $sql;");
					else
						$archive_id = $GLOBALS['APIDB']->getInsertId();
					if (!empty($archive_id))
					{
						foreach($filez as $path => $files)
						{
							foreach($files as $ky => $filz)
							{
								list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $fingerprint. "' AND  `archive_id` = '" . $archive_id. "' AND `path` = '$path' AND `filename` = '$filz'"));
								if ($count==0)
								{
									$exts = explode('.', $filz);
									$ext = $exts[count($exts)-1];
									$type = 'other';
									foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $fileext)
										if (strpos($filz, ".$fileext")>0)
											$type = $fileext;
									if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $fingerprint. "', '" . $archive_id. "','$type','$ext',".$GLOBALS['APIDB']->quote($filz).",".$GLOBALS['APIDB']->quote($path).",'".filesize($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $filz)."',0,unix_timestamp())"))
									{
										echo "\nFile Missing from Database added: $path/$filz for font";
										if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` (`upload_id`,`font_id`, `archive_id`, `type`, `file_id`, `fingerprint`, `created`) VALUES('-1','" . $fingerprint. "', '" . $archive_id. "','$type','".$GLOBALS['APIDB']->getInsertId()."','".md5_file($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $filz)."',unix_timestamp())"))
										{
											echo " (fingered!)";
											$fingercount++;
										} 
										$updated = false;
										if (substr($filz, strlen($filz)-5) == '.glif')
										{
											$glyph = getGlyphArrayFromXML(xml2array(file_get_contents($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $filz)));
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
															if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; } else { echo("SQL Failed: $sql"); }
															if ($values['smooth']!='-----') { $smoothers++; }
															$pointers++;
														}
													}
													$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` (`font_id`, `glyph_id`, `fingerprint`, `name`, `ufofile`, `unicode`, `format`, `width`, `contours`, `pointers`, `smoothers`, `created`, `occurences`, `addon`) VALUES('$fingerprint', '$glyphid', '".$glyph['fingerprint']."', '".$glyph['name']."', '" . basename($file) . "', '".$glyph['unicode']."', '".(empty($glyph['format'])?'1':$glyph['format'])."', '" . (empty($glyph['width'])?'0':$glyph['width']) . "', '$contours', '$pointers', '$smoothers', UNIX_TIMESTAMP(), 1, 'no')";
													if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; } else { echo("SQL Failed: $sql"); }
												} elseif (!empty($row)) {
													$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` (`font_id`, `glyph_id`, `fingerprint`, `name`, `ufofile`, `created`, `addon`, `addon_font_id`, `addon_glyph_id`) VALUES('$fingerprint', '$glyphid', '".$glyph['fingerprint']."', '".$glyph['name']."', '" . basename($filz) . "', UNIX_TIMESTAMP(), 'yes', '".$row['font_id'] . "', '" . $row['glyph_id'] . "')";
													if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; } else { echo("SQL Failed: $sql"); }
													$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_glyphs') . "` SET `occurences` = `occurences` + 1 WHERE `glyph_id` = '".$row['glyph_id']. "'";
													if ($GLOBALS['APIDB']->queryF($sql)) { $updated = true; } else { echo("SQL Failed: $sql"); }
												}
											}
										}
										if ($updated == true)
											echo " [glyped]";
									} else 
										echo("SQL Failed: $sql");
								}
							}
						}
						foreach($nodes as $type => $values)
						{
							if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('nodes_linking') . "` (`font_id`, `node_id`) VALUES ('$fingerprint', '".$values['node_id']."')"))
								die("Failed SQL: $sql;\n");
						}
						deleteFilesNotListedByArray($currently, array(API_BASE, 'file.diz', 'resource.json', 'LICENCE'));
						foreach(getCompleteFilesListAsArray($currently) as $file)
							if (substr($file, strlen($file)-strlen(API_BASE), strlen(API_BASE)) == API_BASE)
								writeFontRepositoryHeader($currently . DIRECTORY_SEPARATOR . $file, $data['Font']['licence'], $data['Font']);
						if (!file_exists($currently . DIRECTORY_SEPARATOR . 'LICENCE'))
							copy(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $data['Font']['licence'] . DIRECTORY_SEPARATOR . 'LICENCE', $currently . DIRECTORY_SEPARATOR . 'LICENCE');
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
						echo "Deleteing: $packfile ~ for regeneration\n";
						unlink($packfile);
						$stamping = getStampingShellexec();
						chdir($currently);
						$cmdb = str_replace("%pack", $packfile, str_replace("%comment", './file.diz', $stamping['zip']));
						echo "\n\n\nExecuting: $cmdb\n";
						$output = array(); exec($cmdb, $output, $resolve);
						echo implode("\n", $output);
						if (!file_exists($packfile))
							die("File not found: $packfile ~~ Failed: $cmda");
						echo implode("\n", $output);
						if (file_exists($packfile))
						{
							list($nodecount) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF("SELECT count(*) FROM  `" . $GLOBALS['APIDB']->prefix('nodes_linking') . "` WHERE `font_id` = '" . $fingerprint . "'"));
							if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts') . "` (`id`, `peer_id`, `archive_id`, `type`, `state`, `names`, `fingers`,`nodes`, `created`, `normal`, `italic`, `bold`, `wide`, `condensed`, `light`, `semi`, `book`, `body`, `header`, `heading`, `footer`, `graphic`, `system`, `quote`, `block`, `admin`, `logo`, `slogon`, `legal`, `script`, `medium`, `data`, `longitude`, `latitude`, `added`) VALUES ('$fingerprint', '".$GLOBALS['peer-id']."','$archive_id', 'local', 'online', '".$namings."', '".$fingercount."', '".$nodings."', '".time()."', '".(in_array('normal', $types)?'yes':'no')."', '".(in_array('italic', $types)?'yes':'no')."', '".(in_array('bold', $types)?'yes':'no')."', '".(in_array('wide', $types)?'yes':'no')."', '".(in_array('condensed', $types)?'yes':'no')."', '".(in_array('light', $types)?'yes':'no')."', '".(in_array('semi', $types)?'yes':'no')."', '".(in_array('book', $types)?'yes':'no')."', '".(in_array('body', $types)?'yes':'no')."', '".(in_array('header', $types)?'yes':'no')."', '".(in_array('heading', $types)?'yes':'no')."', '".(in_array('footer', $types)?'yes':'no')."', '".(in_array('graphic', $types)?'yes':'no')."', '".(in_array('system', $types)?'yes':'no')."', '".(in_array('quote', $types)?'yes':'no')."', '".(in_array('block', $types)?'yes':'no')."', '".(in_array('admin', $types)?'yes':'no')."', '".(in_array('logo', $types)?'yes':'no')."', '".(in_array('slogon', $types)?'yes':'no')."', '".(in_array('legal', $types)?'yes':'no')."', '".(in_array('script', $types)?'yes':'no')."', '".'FONT_RESOURCES_RESOURCE'."', \"" . $GLOBALS['APIDB']->escape(json_encode($data)) . "\", '" . (!isset($upload['longitude'])||empty($upload['longitude'])?'0.000001':$upload['longitude']) . "', '" . (!isset($upload['latitude'])||empty($upload['latitude'])?'0.000001':$upload['latitude']) . "', UNIX_TIMESTAMP())"))
								die("Failed SQL: $sql;\n");
							if (!$GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts') . "` SET `medium` = 'FONT_RESOURCES_RESOURCE', `version` = '" . number_format(floatval($data['Font']['version']), 3) . "', `date` = '" . $data['Font']['date'] . "', `uploaded` = UNIX_TIMESTAMP(), `licence` = '" . $data['Font']['licence'] . "', `company` = '" . $data['Font']['company'] . "', `matrix` = '" . $data['Font']['matrix'] . "', `bbox` = '" . $data['Font']['bbox'] . "', `painttype` = '" . $data['Font']['painttype'] . "', `info` = '" . $data['Font']['info'] . "', `family` = '" . $data['Font']['family'] . "', `weight` = '" . $data['Font']['weight'] . "', `fstype` = '" . $data['Font']['fstype'] . "', `italicangle` = '" . $data['Font']['italicangle'] . "', `fixedpitch` = '" . $data['Font']['fixedpitch'] . "', `underlineposition` = '" . $data['Font']['underlineposition'] . "', `underlinethickness` = '" . $data['Font']['underlinethickness'] . "', `barcode_id` = '" . $data['barcode'] . "', `referee_id` = '" . $data['referee'] . "' WHERE `id` = '$fingerprint'"))
														die("Failed SQL: $sql;\n");
							if (!empty($upload['callback']))
							{
								$cbid = md5($fingerprint.$archive_id.$upload['callback']);
								$GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_callbacks') . "` (`id`, `type`, `font_id`, `archive_id`, `upload_id`, `uri`, `email`) VALUES ('$cbid', 'upload', '$fingerprint', '$archive_id', '-1', '".$upload['callback']."','".$upload['email']."')");
							}
							$output = array();
							$output = array(); exec($cmd = "rm -Rfv $currently", $output);
							echo "\n\n\nExecuting: $cmd\n".implode("\n", $output);
			
	
							$path = str_replace(FONT_RESOURCES_RESOURCE, '', dirname($packfile));
							$subs = explode('/', $path);
							echo "\nIndexing: " . $subs[4] . ' ~~ ' . basename($file);
							$data = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP, $subs[1] . DIRECTORY_SEPARATOR . $subs[2], $subs[2]), 120, 120, array()), true)));
							if (empty($data[$subs[1]][$subs[2]][md5_file($file)]))
							{
								$data[$subs[1]][$subs[2]][md5_file($packfile)]['repo'] = FONT_RESOURCES_STORE;
								$data[$subs[1]][$subs[2]][md5_file($packfile)]['file'] = basename($packfile);
								$data[$subs[1]][$subs[2]][md5_file($packfile)]['path'] = str_replace(FONT_RESOURCES_RESOURCE, "", dirname($file));
								$data[$subs[1]][$subs[2]][md5_file($packfile)]['files'] = getArchivedZIPContentsArray($file);
								$data[$subs[1]][$subs[2]][md5_file($packfile)]['resource'][$subs[2]] = $data;
								$data[$subs[1]][$subs[2]][md5_file($packfile)]['resource'][$subs[1]] = $data;
								writeRawFile(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json", json_encode($ids));
							}
							$alpha = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP, $subs[1], $subs[1], 120, 120, array())), true)));
							if (empty($alpha[$subs[1][md5_file($file)]]))
							{
					
								$alpha[$subs[1]]['Identity'][$alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity']] = $alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity'];
								$alpha[$subs[1]][md5_file($packfile)]['resource'] = $data;
								writeRawFile(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json", json_encode($alpha));
								$ids = json_decode(getURIData(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP), 120, 120, array()), true);;
								$ids[$subs[1]][$subs[2]][$subs[3]][$alpha[$subs[1]][md5_file($packfile)]['FontIdentity']] = $alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity'];
								writeRawFile(FONT_RESOURCES_RESOURCE. "/".basename(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP)), json_encode($ids));
							}
						}
					}
				}
				$packfile = $fingerprint = '';
				echo ".";
			} else {
				echo "x";
			}
		}
		$GLOBALS['APIDB']->queryF($sql = "COMMIT");
		sleep(mt_rand(5,17));
	}
}
exit(0);


?>
