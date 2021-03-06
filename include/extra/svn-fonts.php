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
ini_set('memory_limit', '315M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/fontages.php';
set_time_limit(7200);

// Searches For Unrecorded Fonts
foreach(getCompleteZipListAsArray(FONT_RESOURCES_RESOURCE) as $md5 => $file)
{
	chdir(dirname($file));
	$output = array(); 
	exec($cmd = "/usr/bin/svn add ./ --force", $output);
	echo "Executing: $cmd\n".implode("\n", $output)."\n";
	$output = array(); 
	exec($cmd = "/usr/bin/svn commit -m \"Committed of Font Resource: " . basename($file) ." ~~ API Version: " . API_VERSION." ~~ API URL: " . API_URL . "\"", $output);
	echo "Executing: $cmd\n".implode("\n", $output)."\n";
		
}
exit(0);


?>