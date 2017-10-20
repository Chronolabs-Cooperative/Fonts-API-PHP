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
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '315M');
include_once dirname(__DIR__).'/constants.php';
set_time_limit(7200);

// Searches For Unrecorded Fonts
foreach(getCompleteZipListAsArray(FONT_RESOURCES_RESOURCE) as $md5 => $file)
{
	$json = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
	$archive = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` WHERE `font_id` = '" . $json["FontIdentity"]. "'"));
	if ($archive['filename']!=basename($file) || $archive['path']!=str_replace(FONT_RESOURCES_RESOURCE, '', dirname($file)))
	{
		if ($GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` SET `filename` = '".mysql_real_escape_string($archive['filename'])."', `path` = '".mysql_real_escape_string(str_replace(FONT_RESOURCES_RESOURCE, '', dirname($file)))."' WHERE `font_id` = '" . $json["FontIdentity"]. "'"))
			echo "\nPath and Filename adjusted for: " . $json["FontIdentity"];
		
	} else 
		echo "\nPath and Filename fine: " . $json["FontIdentity"];
	if (!empty($archive["id"]))
		foreach($json['Files'] as $path => $files)
		{
			if (!is_array($files))
			{
				list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $json["FontIdentity"]. "' AND  `archive_id` = '" . $archive["id"]. "' AND `path` = '/' AND `filename` = '$files'"));
				if ($count==0)
				{
					$exts = explode('.', $files);
					$ext = $exts[count($exts)-1];
					$type = 'other';
					foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $fileext)
						if (strpos($files, ".$filetype")>0)
							$type = $filetype;
					if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $json["FontIdentity"]. "', '" . $archive["id"]. "','$type','$ext','$files','/','" .strlen(getArchivedZIPFile($file, $files)) . "',0,unix_timestamp())"))
							echo "\nFile Missing from Database added: /$files for $file";
				}
			} elseif (is_array($files))
			{
				foreach($files as $ky => $filz)
				{
					list($count) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` = '" . $json["FontIdentity"]. "' AND  `archive_id` = '" . $archive["id"]. "' AND `path` = '$path' AND `filename` = '$filz'"));
					if ($count==0)
					{
						$exts = explode('.', $filz);
						$ext = $exts[count($exts)-1];
						$type = 'other';
						foreach(array('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') as $fileext)
							if (strpos($filz, ".$filetype")>0)
								$type = $filetype;
						if ($GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` (`font_id`, `archive_id`, `type`, `extension`, `filename`, `path`, `bytes`, `hits`, `created`) VALUES('" . $json["FontIdentity"]. "', '" . $archive["id"]. "','$type','$ext','$filz','$path','" .strlen(getArchivedZIPFile($file, $filz)) . "',0,unix_timestamp())"))
							echo "\nFile Missing from Database added: $path/$filz for $file";
					}	
				}
			}
		}
}
// Does 99 Fonts at random from the fonts list
$result = $GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('fonts') . "` ORDER BY RAND() LIMIT 99");
while($font = $GLOBALS['APIDB']->fetchArray($result))
{
	$upload = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `font_id` = '" . $font['id'] . "'"));
	$data = json_decode($upload['datastore'], true);
	echo "\n\n\nProcessing Font: " . $data['FontName'] . "\n\n";
	$reserves = getReserves($reserves["fontname"]);
	$repopath = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . substr(strtolower($reserves["fontname"]),0, 1) . DIRECTORY_SEPARATOR . substr(strtolower($reserves["fontname"]),0, 2) . DIRECTORY_SEPARATOR . substr(strtolower($reserves["fontname"]),0, 3) . DIRECTORY_SEPARATOR . $reserves["fontname"] . (count($reserves['parent'])>0?DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $reserves['parent']):"")));
	$ipnet = array();
	
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
		foreach(getNodesArray(array($data['FontName']), array($data['FontFamily'])) as $type => $dnodes)
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
				$names[$title]['font_id'] = $font['id'];
				$names[$title]['upload_id'] = $upload['id'];
				$names[$title]['name'] = $title;
				foreach($values['ipid'] as $ip_id => $ipdata)
				{
					$ipnet[$ip_id] = $ip_id;
					if (!isset($names[$title]['longitude']) && !isset($names[$title]['latitude']))
					{		
						$names[$title]['longitude'] = $ipdata['longitude'];
						$names[$title]['latitude'] = $ipdata['latitude'];
						$names[$title]['country'] = mysql_real_escape_string($ipdata['country']);
						$names[$title]['region'] = mysql_real_escape_string($ipdata['region']);
						$names[$title]['city'] = mysql_real_escape_string($ipdata['city']);
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
	$names[$reserves["fontname"]]['font_id'] = $font['id'];
	$names[$reserves["fontname"]]['upload_id'] = $upload['id'];
	$names[$reserves["fontname"]]['name'] = $reserves["fontname"];
	$names[$reserves["fontname"]]['longitude'] = $data['ipsec']['location']['coordinates']['longitude'];
	$names[$reserves["fontname"]]['latitude'] = $data['ipsec']['location']['coordinates']['latitude'];
	$names[$reserves["fontname"]]['country'] = mysql_real_escape_string($data['ipsec']['country']['name']);
	$names[$reserves["fontname"]]['region'] = mysql_real_escape_string($data['ipsec']['location']['region']);
	$names[$reserves["fontname"]]['city'] = mysql_real_escape_string($data['ipsec']['location']['city']);
		
	list($naming) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts_names') . "` WHERE `font_id` = '" . $font['id'] . "'"));
	if ($naming == 0)
	{
		foreach($names as $key => $values)
		{
			if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_names') . "` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')"))
				die("SQL Failed: $sql;");
		}
	}  
	
	list($noding) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('nodes_linking') . "` WHERE `font_id` = '" . $font['id'] . "'"));
	if ($noding == 0)
	{
		foreach($nodes as $node => $values)
		{
			if ($row = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF("SELECT * from `" . $GLOBALS['APIDB']->prefix('nodes') . "` WHERE `node` = '".$values['node']."' AND `type` = '".$values['type']."'"))) {
				$nodes[$node]['node_id'] = $row['id'];
				$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('nodes') . "` SET `usage` = `usage` + '" . $values['usage'] . "' WHERE `id` = '".$row['id']."'");
			} else {
				$GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('nodes') . "` (`" . implode('`, `', array_keys($values)) . "`) VALUES('" . implode("', '", $values) . "')");
				$nodes[$node]['node_id'] = $GLOBALS['APIDB']->getInsertId();
			}
		}
		foreach($nodes as $type => $values)
		{
			$GLOBALS['APIDB']->queryF("INSERT INTO `" . $GLOBALS['APIDB']->prefix('nodes_linking') . "` (`font_id`, `node_id`) VALUES ('".$font['id']."', '".$values['node_id']."')");
		}
		$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('fonts') . "` SET `nodes` = '" . count($nodes) ."' WHERE `id` = '" . $font['id'] . "'");
	}
	
	list($archiving) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` WHERE `font_id` = '" . $font['id'] . "'"));
	if ($archiving == 0)
	{
		foreach(getCompleteZipListAsArray(FONT_RESOURCES_RESOURCE) as $md5 => $file)
		{
			$files = getArchivedZIPContentsArray($file);
			$json = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
			if ($json['FontIdentity'] == $font['id'])
			{
				if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` (`font_id`, `filename`, `path`, `repository`, `files`, `bytes`, `fingerprint`, `packing`) VALUES(\"" . mysql_real_escape_string($font['id']) . "\",\"" . mysql_real_escape_string(basename($file)) . "\",\"" . mysql_real_escape_string(str_replace(FONT_RESOURCES_RESOURCE, "", dirname($file)))  . "\",\"" . mysql_real_escape_string(FONT_RESOURCES_STORE) . "\",\"" . mysql_real_escape_string(count($files)) . "\",\"" . mysql_real_escape_string(filesize($file)) . "\",\"" . mysql_real_escape_string(md5_file($file)). "\",\"zip\")"))
					die("SQL Failed: $sql");
				continue;
			}
		}
	} else {
		$bytes = 0;
		$archive = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql = "SELECT * from `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` WHERE `font_id` = '" . $font['id'] . "'"));
		if (file_exists(FONT_RESOURCES_RESOURCE . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename']))
		{
			foreach(getArchivedZIPContentsArray(FONT_RESOURCES_RESOURCE . $archive['path'] . DIRECTORY_SEPARATOR . $archive['filename']) as $md5 => $values)
				$bytes = $bytes + $values['bytes'];
			if ($font['bytes']!=$bytes)
			{
				if (!$GLOBALS['APIDB']->queryF($sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('fonts') . "` SET `bytes` = \"" . mysql_real_escape_string($bytes) . "\" WHERE `id` = '" . $font['id'] . "'"))
					die("SQL Failed: $sql");
			}
		}
	}
}

exit(0);


?>