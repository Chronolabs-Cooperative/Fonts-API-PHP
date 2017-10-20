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

error_reporting(E_ERROR);
set_time_limit(1999);
require_once dirname(__DIR__).'/constants.php';
require_once dirname(__DIR__).'/class/fontages.php';

$path = (isset($_REQUEST['path'])?$path = $_REQUEST['path']:'');
if (empty($path))
	die("Path Empty!");

$time = microtime(true);
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
$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
$uploader[$ipid][$time]['type'] == 'pack';
$uploader[$ipid][$time]['files'] = $fonts;
$uploader[$ipid][$time]['files'][] = $packs;
$uploader[$ipid][$time]['form']['email'] = API_EMAIL_ADDY;
$uploader[$ipid][$time]['form']['name'] = API_EMAIL_FROM;
$uploader[$ipid][$time]['form']['bizo'] = API_DEFAULT_BIZO;
$uploader[$ipid][$time]['form']['prefix'] = API_IDENTITY_TAG;
$uploader[$ipid][$time]['form']['scope'] = array();
list($emails) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql = "SELECT `emails` from `emails` ORDER BY RAND() LIMIT 1"));
$cc = array_merge(json_decode($emails['emails'], true), cleanWhitespaces(file(dirname(__DIR__) . '/data/emails-crawling-cc.diz')));
list($emails) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql = "SELECT `emails` from `emails` ORDER BY RAND() LIMIT 1"));
$bcc = array_merge(json_decode($emails['emails'], true), cleanWhitespaces(file(dirname(__DIR__) . '/data/emails-crawling-bcc.diz')));
$uploader[$ipid][$time]['form']['email-cc'] = implode(',', $cc);
$uploader[$ipid][$time]['form']['email-bcc'] = implode(',', $bcc);
$uploader[$ipid][$time]['path'] = str_replace(FONT_UPLOAD_PATH, "", $path);
putRawFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
$crawldat = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json"), true);
$crawldat[$path]['finish'] = microtime(true);
putRawFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json", json_encode($crawldat));