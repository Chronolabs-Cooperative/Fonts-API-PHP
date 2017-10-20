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

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '315M');
include_once dirname(__DIR__).'/constants.php';
set_time_limit(7200);

$ids = json_decode(getURIData(str_replace(array("%s/", "%s--", "?format=raw"), "", FONT_RESOURCES_REPOMAP), 120, 120, array()), true);;
// Searches For Unrecorded Fonts
foreach(getCompleteZipListAsArray(FONT_RESOURCES_RESOURCE) as $md5 => $file)
{
	sleep(mt_rand(6,13));
	list($archiving) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql = "SELECT count(*) from `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` WHERE `filename` = '" . basename($file) . "' AND `path` = '".mysql_real_escape_string(str_replace(FONT_RESOURCES_RESOURCE, "", dirname($file)))."'"));
	if ($archiving != 0)
	{
		$path = str_replace(FONT_RESOURCES_RESOURCE, '', dirname($file));
		$subs = explode('/', $path);
		echo "\nIndexing: " . $subs[4] . ' ~~ ' . basename($file);
		$data = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP, $subs[1] . DIRECTORY_SEPARATOR . $subs[2], $subs[2]), 120, 120, array()), true)));
		if (empty($data[$subs[1]][$subs[2]][md5_file($file)]))
		{
			$data[$subs[1]][$subs[2]][md5_file($file)]['repo'] = FONT_RESOURCES_STORE;
			$data[$subs[1]][$subs[2]][md5_file($file)]['file'] = basename($file);
			$data[$subs[1]][$subs[2]][md5_file($file)]['path'] = str_replace(FONT_RESOURCES_RESOURCE, "", dirname($file));
			$data[$subs[1]][$subs[2]][md5_file($file)]['files'] = getArchivedZIPContentsArray($file);
			$data[$subs[1]][$subs[2]][md5_file($file)]['resource'][$subs[2]] = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
			$data[$subs[1]][$subs[2]][md5_file($file)]['resource'][$subs[1]] = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
			writeRawFile(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[2].DIRECTORY_SEPARATOR.$subs[2]."--repository-mapping.json", json_encode($ids));
		}
		$alpha = array_unique(array_merge(json_decode(file_get_contents(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json"), true), json_decode(getURIData(sprintf(FONT_RESOURCES_REPOMAP, $subs[1], $subs[1], 120, 120, array())), true)));
		if (empty($alpha[$subs[1][md5_file($file)]]))
		{
			
			$alpha[$subs[1]]['Identity'][$alpha[$subs[1]][md5_file($file)]['resource']['FontIdentity']] = $alpha[$subs[1]][md5_file($file)]['resource']['FontIdentity'];
			$alpha[$subs[1]][md5_file($file)]['resource'] = json_decode(getArchivedZIPFile($file, 'font-resource.json'), true);
			writeRawFile(FONT_RESOURCES_RESOURCE. "/".$subs[1].DIRECTORY_SEPARATOR.$subs[1]."--repository-mapping.json", json_encode($alpha));
			$ids = json_decode(getURIData(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP), 120, 120, array()), true);;
			$ids[$subs[1]][$subs[2]][$subs[3]][$alpha[$subs[1]][md5_file($file)]['FontIdentity']] = $alpha[$subs[1]][md5_file($file)]['resource']['FontIdentity'];
			writeRawFile(FONT_RESOURCES_RESOURCE. "/".basename(str_replace(array("%s/", "%s--", '?format=raw'), "", FONT_RESOURCES_REPOMAP)), json_encode($ids));
		}
	}
}


exit(0);


?>
