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


ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '128M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/fontages.php';
set_time_limit(7200);

$result = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `uploads` WHERE `uploaded` > '0' AND `converted` > '0' AND `quizing` > '0' AND `storaged` <= '0'  AND (`finished` >= `needing` OR `expired` < UNIX_TIMESTAMP()) ORDER BY RAND() LIMIT 3");
while($upload = $GLOBALS['FontsDB']->fetchArray($result))
{
	$datastore = json_decode($upload['datastore'], true);
	echo "Packing Font: " . ($datastore["FontName"] = spacerName($datastore["FontName"])) . "\n";
	$ipnet = array();
	$reserves = getReserves($datastore["FontName"]);
	$currently = $upload['currently_path'];
	$packname = urlencode($datastore["FontName"]);
	$sortpath = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . urlencode(substr(strtolower($datastore["FontName"]),0, 1)) . DIRECTORY_SEPARATOR . urlencode(substr(strtolower($datastore["FontName"]),0, 2)) . DIRECTORY_SEPARATOR . urlencode(substr(strtolower($datastore["FontName"]),0, 3)) . DIRECTORY_SEPARATOR . urlencode($datastore["FontName"]) . (count($reserves['parent'])>0?DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $reserves['parent']):"")));;
	$packfile = $sortpath . (substr($sortpath, strlen($sortpath)-1, 1)!=DIRECTORY_SEPARATOR?DIRECTORY_SEPARATOR:"") . $packname . '.zip';
	
	// Builds types table
	$types = array();
	foreach($datastore['survey'] as $key => $values)
		foreach($values['types'] as $hash => $typals)
			foreach($typals as $hsh => $type)
				$types[$type] = $type;
	
				// Builds nodes table
	$nodes = array();
	if (isset($datastore['survey']['nodes']))
		foreach($datastore['survey'] as $key => $values)
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
		foreach(getNodesArray(array($datastore["FontName"]), array($datastore['FontFamily'])) as $type => $dnodes)
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
	$resultb = $GLOBALS['FontsDB']->queryF("SELECT * from `flows_history` WHERE `upload_id` = '".$upload['id']."' AND `step` LIKE 'finished' ORDER BY RAND()");
	while($contributor = $GLOBALS['FontsDB']->fetchArray($resultb))
	{
		$flow = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `flows` WHERE `flow_id` = '".$contributor['flow_id']."'"));
		$contributors[] = array('ip_id' => (empty($contributor['ip_id'])?$flow['ip_id']:$contributor['ip_id']), 'flow_id'=>$contributor['flow_id'], 'history_id' => $contributor['history_id'],
								'upload_id' => $contributor['upload_id'], 'when'=>time(), 'name'=>$GLOBALS['FontsDB']->escape($flow['name']));
		$ipnet[(empty($contributor['ip_id'])?$flow['ip_id']:$contributor['ip_id'])] = (empty($contributor['ip_id'])?$flow['ip_id']:$contributor['ip_id']);	
	}
	if (count($contributors)>0)
	{
		foreach($names as $key => $values)
		{
			$GLOBALS['FontsDB']->queryF("INSERT INTO `fonts_contributors` (`" . implode('`, `', array_keys($contributors)) . "`) VALUES('" . implode("', '", $contributors) . "')");
		}
	}
	
	// Builds naming table
	$names = array();
	foreach($datastore['survey'] as $key => $values)
	{
		foreach($values['names'] as $title => $title)
		{
			if (!isset($names[$title]))
			{
				$names[$title]['font_id'] = $upload['font_id'];
				$names[$title]['upload_id'] = $upload['id'];
				$names[$title]['name'] = $title;
				foreach($values['ipid'] as $ip_id => $ipdata)
				{
					$ipnet[$ip_id] = $ip_id;
					if (!isset($names[$title]['longitude']) && !isset($names[$title]['latitude']))
					{		
						$names[$title]['longitude'] = $ipdata['longitude'];
						$names[$title]['latitude'] = $ipdata['latitude'];
						$names[$title]['country'] = $GLOBALS['FontsDB']->escape($ipdata['country']);
						$names[$title]['region'] = $GLOBALS['FontsDB']->escape($ipdata['region']);
						$names[$title]['city'] = $GLOBALS['FontsDB']->escape($ipdata['city']);
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

	$names[$datastore["FontName"]]['font_id'] = $upload['font_id'];
	$names[$datastore["FontName"]]['upload_id'] = $upload['id'];
	$names[$datastore["FontName"]]['name'] = $datastore["FontName"];
	$names[$datastore["FontName"]]['longitude'] = !empty($datastore['ipsec']['location']['coordinates']['longitude'])?$datastore['ipsec']['location']['coordinates']['longitude']:"0.00000001";
	$names[$datastore["FontName"]]['latitude'] = !empty($datastore['ipsec']['location']['coordinates']['latitude'])?$datastore['ipsec']['location']['coordinates']['latitude']:"0.00000001";
	$names[$datastore["FontName"]]['country'] = $GLOBALS['FontsDB']->escape($datastore['ipsec']['country']['name']);
	$names[$datastore["FontName"]]['region'] = $GLOBALS['FontsDB']->escape($datastore['ipsec']['location']['region']);
	$names[$datastore["FontName"]]['city'] = $GLOBALS['FontsDB']->escape($datastore['ipsec']['location']['city']);
		
	if (count($names)>0)
	{
		foreach($names as $key => $values)
		{
			
			if ($GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `fonts_names` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')"))
				echo "Font Name: " . $values['name'] . ' recorded for font identity: ' . $values['font_id'] . "\n";
			else 
				die("SQL Error: " . $sql . ";");
		}
	} 
	
	if (count($nodes)>0)
	{
		foreach($nodes as $node => $values)
		{
			if ($row = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `nodes` WHERE `node` = '".$values['node']."' AND `type` = '".$values['type']."'"))) {
				$nodes[$node]['node_id'] = $row['id'];
				$GLOBALS['FontsDB']->queryF("UPDATE `nodes` SET `usage` = `usage` + '" . $values['usage'] . "' WHERE `id` = '".$row['id']."'");
			} else {
				$GLOBALS['FontsDB']->queryF("INSERT INTO `nodes` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')");
				$nodes[$node]['node_id'] = $GLOBALS['FontsDB']->getInsertId();
			}
		}
	}
	
	// gets networking
	$networking = array();
	$resultc = $GLOBALS['FontsDB']->queryF("SELECT * from `networking` WHERE `ip_id` IN ('".implode("', '", $ipnet) . "')");
	while($net = $GLOBALS['FontsDB']->fetchArray($resultc))
	{
		$networking[$net['ip_id']] = $net;
	}
	
	// Gets File List in Archive
	if (empty($upload['font_id']))
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
	} else
		$fingerprint = $upload['font_id'];
		
	$GLOBALS['FontsDB']->queryF("INSERT INTO `fonts_archiving` (`font_id`, `filename`, `path`, `repository`, `files`, `bytes`, `fingerprint`, `hits`, `packing`) VALUES ('$fingerprint', '$filename', '" . str_replace(FONT_RESOURCES_RESOURCE, '', dirname($packfile)) . "', '" . FONT_RESOURCES_STORE . "', '0', '" . 0 . "', '" . md5_file(NULL) . "', 0, 'zip')");
	$archive_id = $GLOBALS['FontsDB']->getInsertId();
	
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
			$datastore['json'] = $data = array(	"CSS"=>$cssfiles,'FontType' => $fontage->getFontType(), 'FontCopyright' => $fontage->getFontCopyright(), "FontName" => $fontage->getFontName(),
					'FontSubfamily' => $fontage->getFontSubfamily(), "FontSubfamilyID" => $fontage->getFontSubfamilyID(),
					'FontFullName' => $naming = spacerName($fontage->getFontFullName()), "FontVersion" => $fontage->getFontVersion(),
					'FontWeight' => $fontage->getFontWeight(), "FontPostscriptName" => $fontage->getFontPostscriptName(),
					"Files" => getCompleteFilesListAsArray($currently),
					"Font" => array_merge($datastore['Font']), "Licences" => array($datastore['Font']['licence'] => cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $datastore['Font']['licence'] . DIRECTORY_SEPARATOR . 'LICENCE')))
					,'Table' => $fontage->getTable(), "UnicodeCharMap" => $fontage->getUnicodeCharMap(),
					"Reserves" => $reserves, 'Names' => removeIdentities($names), 'Nodes' => removeIdentities($nodes),
					"Contributors" => removeIdentities($contributors), 'FontIdentity' => $fingerprint, 'Bytes' => $expanded,
					"Networking" => $networking);
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
			writeRawFile($currently . DIRECTORY_SEPARATOR . $upload['font_id'] . ".css", implode("\n", $css));
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
				$canvas->writeText(19, $i, "All Work and No Pay Makes Wishcraft a Dull Bored!");
				$i=$i+$point + $step;
			}
			$canvas->useFont($preview, 14, $img->allocateColor(0, 0, 0));
			$canvas->writeText('right', 'bottom', API_URL);
			$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'Font Preview for '.$naming.'.jpg');
			$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'Font Preview for '.$naming.'.gif');
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
			$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'font-name-banner.jpg');
			$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'font-name-banner.gif');
			$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'font-name-banner.png');
			unset($img);
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
			list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql = "SELECT count(*) from `fonts_files` WHERE `font_id` = '" . $upload['font_id']. "' AND  `archive_id` = '" . $archive_id. "' AND `path` = '/' AND `filename` = '$files'"));
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
				if ($GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `fonts_files` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $upload['font_id']. "', '" . $archive_id. "','$type','$ext','$files','/','" .filesize($currently . DIRECTORY_SEPARATOR . $files) . "',0,unix_timestamp())"))
					echo "Index for File Missing added: $path/$files for font\n";
				else
					die("SQL Failed: $sql;");
			}
		} elseif (is_array($files))
		{
			foreach($files as $ky => $filz)
			{
				$ffls++;
				echo "Checking file $ffls missing in files index: $path/$filz\n";
				list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql = "SELECT count(*) from `fonts_files` WHERE `font_id` = '" . $upload['font_id']. "' AND  `archive_id` = '" . $archive_id. "' AND `path` = '$path' AND `filename` = '$filz'"));
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
					if ($GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `fonts_files` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $upload['font_id']. "', '" . $archive_id. "','$type','$ext','$filz','$path','" .filesize($currently . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $filz) . "',0,unix_timestamp())"))
						echo "Index for File Missing added: $path/$filz for font\n";
					else
						die("SQL Failed: $sql;");
				}
			}
		}
	}
	
	// Writes file.diz
	writeRawFile($comment = $currently . DIRECTORY_SEPARATOR . "file.diz", getFileDIZ(0, $upload['id'], $fingerprint, $filename = $packname.'.zip', $expanded, $filez)."\n.");
	deleteFilesNotListedByArray($currently, array(API_BASE, 'file.diz', 'resource.json', 'LICENCE'));
	foreach(getCompleteFilesListAsArray($currently) as $file)
		if (substr($file, strlen($file)-3) == API_BASE)
			writeFontRepositoryHeader($currently . DIRECTORY_SEPARATOR . $file, $data['Font']['licence'], $data['Font']);
	if (!file_exists($currently . DIRECTORY_SEPARATOR . 'LICENCE'))
		copy(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $datastore['Font']['licence'] . DIRECTORY_SEPARATOR . 'LICENCE', $currently . DIRECTORY_SEPARATOR . 'LICENCE');
	$packing = getArchivingShellExec();
	$stamping = getArchivingStampingExec();
	if (!is_dir($sortpath))
		mkdir($sortpath, 0777, true);
	chdir($currently);
	$cmda = str_replace("%folder", "./", str_replace("%pack", $packfile, str_replace("%comment", $comment, (substr($packing['zip'],0,1)!="#"?$packing['zip']:substr($packing['zip'],1)))));
	echo "Executing: $cmda\n";
	exec($cmda, $output, $resolv);
	if (isset($stamping['zip']))
	{
		$cmdb = str_replace("%pack", $packfile, str_replace("%comment", $comment, $stamping['zip']));
		echo "Executing: $cmdb\n";
		exec($cmdb, $output, $resolve);
		echo implode("\n", $output);
	}
	if (!file_exists($packfile))
		die("File not found: $packfile ~~ Failed: $cmda");
	if (file_exists($packfile))
	{
		$output = array(); 
		exec($cmd = "rm -Rfv $currently", $output);
		echo "Executing: $cmd\n".implode("\n", $output);
		$GLOBALS['FontsDB']->queryF("UPDATE `uploads` SET `cleaned` = '".time()."', `datastore` = \"".$GLOBALS['FontsDB']->escape(json_encode($datastore))."\" WHERE `id` = " . $upload['id']);
		chdir($cmd = FONT_RESOURCES_RESOURCE);
		echo "Path Set: $cmd\n";
		$GLOBALS['FontsDB']->queryF("UPDATE `fonts_archiving` SET `files` = '$ffls', `bytes` = '" . filesize($packfile) . "', `added` = UNIX_TIMESTAMP(), `packed` = UNIX_TIMESTAMP() WHERE `archive_id` = $archive_id");
		if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_fingering` SET `font_id` = '$fingerprint', `archive_id` = '$archive_id' WHERE `upload_id` = " . $upload['id']))
			die("Failed SQL: $sql;\n");
		if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_contributors` SET `font_id` = '$fingerprint', `archive_id` = '$archive_id' WHERE `upload_id` = " . $upload['id']))
			die("Failed SQL: $sql;\n");
		if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `flows_history` SET `font_id` = '$fingerprint' WHERE `upload_id` = " . $upload['id']))
			die("Failed SQL: $sql;\n");
		if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `uploads` SET `font_id` = '$fingerprint', `storaged` = UNIX_TIMESTAMP() WHERE `id` = " . $upload['id']))
			die("Failed SQL: $sql;\n");
		list($fingercount) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) FROM  `fonts_fingering` WHERE `upload_id` = " . $upload['id']));
		foreach($nodes as $type => $values)
		{
			if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `nodes_linking` (`font_id`, `node_id`) VALUES ('$fingerprint', '".$values['node_id']."')"))
				die("Failed SQL: $sql;\n");
		}
		list($nodecount) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) FROM  `nodes_linking` WHERE `font_id` = '" . $fingerprint . "'"));
		if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `fonts` (`id`, `peer_id`, `archive_id`, `type`, `state`, `names`, `fingers`, `nodes`, `created`, `normal`, `italic`, `bold`, `wide`, `condensed`, `light`, `semi`, `book`, `body`, `header`, `heading`, `footer`, `graphic`, `system`, `quote`, `block`, `admin`, `logo`, `slogon`, `legal`, `script`, `medium`, `data`, `longitude`, `latitude`, `version`, `date`, `uploaded`, `licence`, `company`, `matrix`, `bbox`, `painttype`, `info`, `family`, `weight`, `fstype`, `italicangle`, `fixedpitch`, `underlineposition`, `underlinethickness`) VALUES ('$fingerprint', '".$GLOBALS['peer-id']."','$archive_id', 'local', 'online', '".count($names)."', '$fingercount', '$nodecount', '".time()."', '".(in_array('normal', $types)?'yes':'no')."', '".(in_array('italic', $types)?'yes':'no')."', '".(in_array('bold', $types)?'yes':'no')."', '".(in_array('wide', $types)?'yes':'no')."', '".(in_array('condensed', $types)?'yes':'no')."', '".(in_array('light', $types)?'yes':'no')."', '".(in_array('semi', $types)?'yes':'no')."', '".(in_array('book', $types)?'yes':'no')."', '".(in_array('body', $types)?'yes':'no')."', '".(in_array('header', $types)?'yes':'no')."', '".(in_array('heading', $types)?'yes':'no')."', '".(in_array('footer', $types)?'yes':'no')."', '".(in_array('graphic', $types)?'yes':'no')."', '".(in_array('system', $types)?'yes':'no')."', '".(in_array('quote', $types)?'yes':'no')."', '".(in_array('block', $types)?'yes':'no')."', '".(in_array('admin', $types)?'yes':'no')."', '".(in_array('logo', $types)?'yes':'no')."', '".(in_array('slogon', $types)?'yes':'no')."', '".(in_array('legal', $types)?'yes':'no')."', '".(in_array('script', $types)?'yes':'no')."', '".'FONT_RESOURCES_RESOURCE'."', '" . $GLOBALS['FontsDB']->escape(json_encode($data)) . "', '" . $upload['longitude'] . "', '" . $upload['latitude'] . "', '" . $data['Font']['version'] . "', '" . $data['Font']['date'] . "', '" . $data['Font']['uploaded'] . "', '" . $data['Font']['licence'] . "', '" . $data['Font']['company'] . "', '" . $data['Font']['matrix'] . "', '" . $data['Font']['bbox'] . "', '" . $data['Font']['painttype'] . "', '" . $data['Font']['info'] . "', '" . $data['Font']['family'] . "', '" . $data['Font']['weight'] . "', '" . $data['Font']['fstype'] . "', '" . $data['Font']['italicangle'] . "', '" . $data['Font']['fixedpitch'] . "', '" . $data['Font']['underlineposition'] . "', '" . $data['Font']['underlinethickness'] . "')"))
			die("Failed SQL: $sql;\n");
		if (!empty($upload['callback']))
		{
			$cbid = md5($fingerprint.$archive_id.$upload['callback']);
			$GLOBALS['FontsDB']->queryF("INSERT INTO `fonts_callbacks` (`id`, `type`, `font_id`, `archive_id`, `upload_id`, `uri`, `email`) VALUES ('$cbid', 'upload', '$fingerprint', '$archive_id', '".$upload['id']."', '".$upload['callback']."','".$upload['email']."')");
		}
		
		
		if (in_array('svn', explode(",", API_REPOSITORY)))
		{

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
				writeRawFile($filea = FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json", json_encode($ids));
			}
			unset($data);
			$alpha = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP, $subs[1], $subs[1], 120, 120, array())), true)));
			if (empty($alpha[$subs[1][md5_file($file)]]))
			{
			
				$alpha[$subs[1]]['Identity'][$alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity']] = $alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity'];
				$alpha[$subs[1]][md5_file($packfile)]['resource'] = $data;
				writeRawFile(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json", json_encode($alpha));
				$ids = json_decode(getURIData(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP), 120, 120, array()), true);;
				$ids[$subs[1]][$subs[2]][$subs[3]][$alpha[$subs[1]][md5_file($packfile)]['FontIdentity']] = $alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity'];
				writeRawFile($fileb = FONT_RESOURCES_RESOURCE. "/".basename(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP)), json_encode($ids));
			}
			unset($alpha);
			
			if (!file_exists(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-add.sh'))
			{
				$bash=array();
				$bash[] = "#! bash";
				$bash[] = "cd ".FONT_RESOURCES_RESOURCE;
				$bash[] = "svn cleanup";
				$bash[] = "svn update";
			} else {
				echo "Setting Memory Limit To: " .(floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-add.sh')) / (1024) + 50 . "M") . "/n";
				ini_set('memory_limit', floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-add.sh') / (1024) + 50) . "M");
				$bash = cleanWhitespaces(file(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-add.sh'));
				unset($bash[count($bash)-1]);
			}
			$bash[] = "cd " . dirname($packfile);
			$bash[] = "svn cleanup";
			$bash[] = "svn add . --force";
			$bash[] = "svn commit -m \"Importing into Repository for 1st time; the font: $naming\"";
			$bash[] = "cd " . dirname(dirname($packfile));
			$bash[] = "svn cleanup";
			$bash[] = "svn add . --force";
			$bash[] = "svn commit -m \"Importing into Repository for 1st time; the font: ".basename(dirname(dirname($packfile)))."\"";
			$bash[] = "cd " . dirname(dirname(dirname($packfile)));
			$bash[] = "svn cleanup";
			$bash[] = "svn add . --force";
			$bash[] = "svn commit -m \"Importing into Repository for 1st time; the font: ".basename(dirname(dirname(dirname($packfile))))."\"";
			$bash[] = "cd " . dirname(dirname(dirname(dirname($packfile))));
			$bash[] = "svn cleanup";
			$bash[] = "svn add . --force";
			$bash[] = "svn commit -m \"Importing into Repository for 1st time; the font: ".basename(dirname(dirname(dirname(dirname($packfile)))))."\"";
			$bash[] = "unlink " . dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-add.sh';
			writeRawFile(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-add.sh', implode("\n", $bash));
			unset($bash);
		}
		if (in_array('git', explode(",", API_REPOSITORY)))
		{

			$path = str_replace(FONT_RESOURCES_RESOURCE, '', dirname($packfile));
			$subs = explode('/', $path);
			echo "\nIndexing: " . $subs[4] . ' ~~ ' . basename($file);
			$data = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP_GIT, $subs[1] . DIRECTORY_SEPARATOR . $subs[2], $subs[2]), 120, 120, array()), true)));
			if (empty($data[$subs[1]][$subs[2]][md5_file($file)]))
			{
				$data[$subs[1]][$subs[2]][md5_file($packfile)]['repo'] = FONT_RESOURCES_STORE;
				$data[$subs[1]][$subs[2]][md5_file($packfile)]['file'] = basename($packfile);
				$data[$subs[1]][$subs[2]][md5_file($packfile)]['path'] = str_replace(FONT_RESOURCES_RESOURCE, "", dirname($file));
				$data[$subs[1]][$subs[2]][md5_file($packfile)]['files'] = getArchivedZIPContentsArray($file);
				$data[$subs[1]][$subs[2]][md5_file($packfile)]['resource'][$subs[2]] = $data;
				$data[$subs[1]][$subs[2]][md5_file($packfile)]['resource'][$subs[1]] = $data;
				writeRawFile($filea = FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json", json_encode($ids));
			}
			unset($data);
			$alpha = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP_GIT, $subs[1], $subs[1], 120, 120, array())), true)));
			if (empty($alpha[$subs[1][md5_file($file)]]))
			{
			
				$alpha[$subs[1]]['Identity'][$alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity']] = $alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity'];
				$alpha[$subs[1]][md5_file($packfile)]['resource'] = $data;
				writeRawFile(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json", json_encode($alpha));
				$ids = json_decode(getURIData(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP), 120, 120, array()), true);;
				$ids[$subs[1]][$subs[2]][$subs[3]][$alpha[$subs[1]][md5_file($packfile)]['FontIdentity']] = $alpha[$subs[1]][md5_file($packfile)]['resource']['FontIdentity'];
				writeRawFile($fileb = FONT_RESOURCES_RESOURCE. "/".basename(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP)), json_encode($ids));
			}
			unset($alpha);
			if (!file_exists(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-add.sh'))
			{
				$bash=array();
				$bash[] = "#! bash";
				$bash[] = "cd ".FONT_RESOURCES_RESOURCE;
			} else {
				echo "Setting Memory Limit To: " .(floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-add.sh')) / (1024) + 50 . "M") . "/n";
				ini_set('memory_limit', floor(filesize(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-add.sh') / (1024) + 50) . "M");
				$bash = cleanWhitespaces(file(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-add.sh'));
				unset($bash[count($bash)-1]);
			}
			$bash[] = "cd " . dirname($packfile);
			$bash[] = "git add ".basename($packfile)."";
			$bash[] = "cd " . dirname($filea);
			$bash[] = "git add ".basename($filea)."";
			$bash[] = "cd " . dirname($fileb);
			$bash[] = "git add ".basename($fileb)."";
			$bash[] = "git commit -m \"Importing into Repository for 1st time; the font: $naming\"";
			$bash[] = "git push origin master";
			$bash[] = "unlink " . dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-add.sh';
			writeRawFile(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'git-add.sh', implode("\n", $bash));
			unset($bash);
		}
		
	} else 
		echo("Error: Failed generated archived pack font file!!\n");
	
}

exit(0);


?>