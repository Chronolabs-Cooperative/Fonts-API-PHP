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
 * @subpackage		extra-files
 * @description		Create's an SH Bash script to export Files to SVN
 */


 //   Scheduled Cron Job Details.,
 //   Execute:- 
 //   
 //   $ sudo crontab -e
 //   
 //   CronTab Entry:
 //   
 //   */1 * * * * /usr/bin/php -q /path/to/cronjobs/callbacks.php


ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '315M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/FontsDB.php';

$paths = array();
foreach(array_unique(array_merge(getDirListAsArray(FONT_RESOURCES_RESOURCE), array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','9','8','7','6','5','4','3','2','1'))) as $path)
	foreach(getDirListAsArray(FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . $path) as $subpath)
		foreach(getDirListAsArray(FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $subpath) as $basepath)
				$paths[] = DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $subpath . DIRECTORY_SEPARATOR . $basepath;
shuffle($paths);

$bash=array();
$bash[] = "#! bash";
$bash[] = "cd " . FONT_RESOURCES_RESOURCE;
$bash[] = "svn cleanup";
$bash[] = "svn update";

shuffle($paths);
shuffle($paths);
foreach($paths as $path)
{
	$bash[] = "cd " . FONT_RESOURCES_RESOURCE . $path;
	$bash[] = "svn add . --force";
	$bash[] = "svn commit -m \"Importing from Final SVN Rebuild: $path\"";
}
$bash[] = "cd " . FONT_RESOURCES_RESOURCE;
$bash[] = "svn add . --force";
$bash[] = "svn commit -m \"Final Commit on Rebuilding SVN\"";
writeRawFile(dirname(FONT_RESOURCES_RESOURCE) . DIRECTORY_SEPARATOR . 'svn-import.sh', implode("\n", $bash));