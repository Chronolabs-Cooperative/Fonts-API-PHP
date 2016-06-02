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


ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '315M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/fontages.php';
require_once dirname(__DIR__).'/class/fontsmailer.php';
set_time_limit(7200*99*25);
$uploader = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
$folders = array();
foreach(getCompleteDirListAsArray(constant("FONT_RESOURCES_UNPACKING")) as $path => $dir)
{
	$folders[$dir] = getFileListAsArray($dir);
}
foreach($folders as $path => $files) {
	foreach($files as $ifd => $file)
	{
		if (strpos("--$file", 'upload.json'))
		{
			$data = json_decode(file_get_contents($path   . DIRECTORY_SEPARATOR . "upload.json"), true);
			if (!isset($data['finished']) && $data['process'] <= microtime(true) - mt_rand(900, 1800))
			{
				$uploader[$data['ipid']][$data['time']] = $data;
				echo "Imported: $file\n";
				unlink($path   . DIRECTORY_SEPARATOR . "upload.json");
			} elseif (isset($data['finished']) && $data['finished'] > 0)
			{
				chdir($path);
				exec("rm -Rfv ./", $output);
			}
		}
	}
}
foreach($uploader as $ipid => $values)
{
	if (empty($values))
		unset($uploader[$ipid]);
}
file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));

exit(0);


?>