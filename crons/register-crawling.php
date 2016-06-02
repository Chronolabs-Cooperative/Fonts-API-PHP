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

error_reporting(E_ERROR);
set_time_limit(1999);
require_once dirname(__DIR__).'/functions.php';
require_once dirname(__DIR__).'/class/fontages.php';

$crawldat = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json"), true);
foreach($crawldat as $path => $values)
	if (!isset($values['finished']) && file_exists($path . DIRECTORY_SEPARATOR . 'finished.dat'))
	{
		$time = microtime(true);
		$crawldat[$path]['finished'] = filectime($path . DIRECTORY_SEPARATOR . 'finished.dat');
		$packs = $fonts = array();
		foreach(getCompleteDirListAsArray($path) as $folder)
		{
			foreach(getPacksListAsArray($folder) as $file=>$values)
				$packs[] =  $folder . DIRECTORY_SEPARATOR . $values['file'];
			foreach(getFontsListAsArray($folder) as $file=>$values)
				$fonts[] = $folder . DIRECTORY_SEPARATOR . $values['file'];
			foreach(getFileListAsArray($folder) as $file)
				if (!in_array($folder . DIRECTORY_SEPARATOR . $file, $packs) && !in_array($folder . DIRECTORY_SEPARATOR . $file, $fonts) && $file != "finished.dat")
					unlink( $folder . DIRECTORY_SEPARATOR . $file );
		}
		$uploader = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
		$uploader[$ipid][$time]['type'] == 'pack';
		$uploader[$ipid][$time]['files'] = $fonts;
		$uploader[$ipid][$time]['files'][] = $packs;
		$uploader[$ipid][$time]['form']['email'] = 'wishcraft@users.sourceforge.net';
		$uploader[$ipid][$time]['form']['name'] = 'Font File/Pack Crawler';
		$uploader[$ipid][$time]['form']['bizo'] = 'Chronolabs Cooperative';
		$uploader[$ipid][$time]['form']['scope'] = array();
		list($emails) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql = "SELECT `emails` from `emails` ORDER BY RAND() LIMIT 1"));
		if (substr($emails, strlen($emails)-1, 1) != "}")
			$emails = json_decode(stripslashes(file_get_contents('/home/web/emails-jsoned.txt')), true);
		else
			$emails = json_decode($emails, true);
		$uploader[$ipid][$time]['form']['email-cc'] = implode(',', $emails);
		list($emails) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql = "SELECT `emails` from `emails` ORDER BY RAND() LIMIT 1"));
		if (substr($emails, strlen($emails)-1, 1) != "}")
			$emails = json_decode(stripslashes(file_get_contents('/home/web/emails-jsoned.txt')), true);
		else
			$emails = json_decode($emails, true);
		$uploader[$ipid][$time]['form']['email-bcc'] = implode(',', $emails);
		$uploader[$ipid][$time]['path'] = str_replace(FONT_UPLOAD_PATH, "", $path);
		file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
	}
writeRawFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json", json_encode($crawldat));
