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
include_once dirname(__DIR__).'/include/functions.php';

$ids = getFontIdentitiesArray();
$GLOBALS['APIDB']->queryF("START TRANSACTION");
$sql = "DELETE FROM `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` WHERE `font_id` NOT IN('" . implode("', '", $ids) . "')";
if ($GLOBALS['APIDB']->queryF($sql))
	echo "Droped some orphan Fingerprints: ". $GLOBALS['APIDB']->getAffectedRows() . "\n";
$sql = "DELETE FROM `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` WHERE `font_id` NOT IN('" . implode("', '", $ids) . "')";
	if ($GLOBALS['APIDB']->queryF($sql))
		echo "Droped some orphan Archives: ". $GLOBALS['APIDB']->getAffectedRows() . "\n";
$sql = "DELETE FROM `" . $GLOBALS['APIDB']->prefix('fonts_files') . "` WHERE `font_id` NOT IN('" . implode("', '", $ids) . "')";
	if ($GLOBALS['APIDB']->queryF($sql))
		echo "Droped some orphan Files: ". $GLOBALS['APIDB']->getAffectedRows() . "\n";
$GLOBALS['APIDB']->queryF("COMMIT");
sleep(mt_rand(7,14));

echo "Searching for files to unlink in: " . FONT_RESOURCES_SORTING . ":~ ";
foreach(getDirListAsArray(FONT_RESOURCES_SORTING) as $dir)
	if (!checkEmail(basename($dir)))
		foreach(getCompleteDirListAsArray(FONT_RESOURCES_SORTING."/$dir") as $folder)
			foreach(getFileListAsArray($folder) as $key => $file)
			{
				if (filectime($folder . DIRECTORY_SEPARATOR . $key) <= time() - (7 * 3600))
				{
					unlink($folder . DIRECTORY_SEPARATOR . $key);
					rmdir($folder);
					echo ".";
				} else {
					echo "x";
			}
		}
sleep(mt_rand(7,14));

echo "\n\nSearching for files to unlink in: " . FONT_RESOURCES_UNPACKING . ":~ "; 
foreach(getDirListAsArray(FONT_RESOURCES_UNPACKING) as $dir)
	if (!checkEmail(basename($dir)))
		foreach(getCompleteDirListAsArray(FONT_RESOURCES_UNPACKING."/$dir") as $folder)
			foreach(getFileListAsArray($folder) as $key => $file)
			{
				if (filectime($folder . DIRECTORY_SEPARATOR . $key) <= time() - (7 * 3600))
				{
					unlink($folder . DIRECTORY_SEPARATOR . $key);
					rmdir($folder);
					echo ".";
				} else {
					echo "x";
				}
			}
sleep(mt_rand(7,14));

echo "\n\nSearching for files to unlink in: " . FONTS_CACHE . ":~ ";
foreach(getDirListAsArray(FONTS_CACHE) as $dir)
	foreach(getCompleteDirListAsArray(FONTS_CACHE."/$dir") as $folder)
		foreach(getFileListAsArray($folder) as $key => $file)
		{
			if (filectime($folder . DIRECTORY_SEPARATOR . $key) <= time() - (7 * 3600))
			{
				unlink($folder . DIRECTORY_SEPARATOR . $key);
				rmdir($folder);
				echo ".";
			} else {
				echo "x";				
			}
		}
sleep(mt_rand(7,14));
	
foreach(getFileListAsArray(FONTS_CACHE) as $key => $file)
{
	if (filectime(FONTS_CACHE . DIRECTORY_SEPARATOR . $key) <= time() - (7 * 3600))
	{
		unlink(FONTS_CACHE . DIRECTORY_SEPARATOR . $key);
		echo ".";
	} else {
		echo "x";
	}
}
sleep(mt_rand(7,14));

echo "\n\nSearching for files to unlink in: " . FONT_RESOURCES_CACHE . ":~ ";
foreach(getDirListAsArray(FONT_RESOURCES_CACHE) as $dir)
	foreach(getCompleteDirListAsArray(FONT_RESOURCES_CACHE."/$dir") as $folder)
		foreach(getFileListAsArray($folder) as $key => $file)
		{
			if (filectime($folder . DIRECTORY_SEPARATOR . $key) <= time() - (7 * 3600))
			{
				unlink($folder . DIRECTORY_SEPARATOR . $key);
				rmdir($folder);
				echo ".";
			} else {
				echo "x";
			}
		}
sleep(mt_rand(7,14));
	
foreach(getFileListAsArray(FONT_RESOURCES_CACHE) as $key => $file)
{
	if (filectime(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . $key) <= time() - (7 * 3600))
	{
		unlink(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . $key);
		echo ".";
	} else {
		echo "x";
	}
}
sleep(mt_rand(7,14));

echo "\n\nSearching for files to unlink in: /tmp:~ ";
foreach(getDirListAsArray("/tmp") as $dir)
	foreach(getCompleteDirListAsArray("/tmp/$dir") as $folder)
		foreach(getFileListAsArray($folder) as $key => $file)
		{
			if (filectime($folder . DIRECTORY_SEPARATOR . $key) <= time() - (21 * 3600))
			{
				unlink($folder . DIRECTORY_SEPARATOR . $key);
				rmdir($folder);
				echo ".";
			} else {
				echo "x";
			}
		}
sleep(mt_rand(7,14));

foreach(getFileListAsArray("/tmp") as $key => $file)
{
	if (filectime(FONTS_CACHE . DIRECTORY_SEPARATOR . $key) <= time() - (21 * 3600))
	{
		unlink(FONTS_CACHE . DIRECTORY_SEPARATOR . $key);
		echo ".";
	} else {
		echo "x";
	}
}
