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
set_time_limit(7200 * 89);
$filez = array();
$filez = getCompleteZipListAsArray(FONT_RESOURCES_RESOURCE."-old");
foreach($filez as $md5 => $file)
{	
	$filename = str_replace(array("-", " ", ".", "_"), "", urlencode(strtolower(basename(urldecode($file)))));
	if (!is_dir(FONT_RESOURCES_RESOURCE.($path=DIRECTORY_SEPARATOR.urlencode(substr($filename,0,1)).DIRECTORY_SEPARATOR.urlencode(substr($filename,0,2)).DIRECTORY_SEPARATOR.urlencode(substr($filename,0,3)).DIRECTORY_SEPARATOR.urlencode(str_replace(".zip", "", urldecode(basename($file)))))))
		mkdir(FONT_RESOURCES_RESOURCE.$path, 0777, true);
	if ($file != ($newfile = FONT_RESOURCES_RESOURCE.$path.DIRECTORY_SEPARATOR.($filename = urlencode(urldecode(basename($file))))))
		if (copy($file, $newfile))
		{
			if (basename(dirname(dirname(dirname($file)))) == basename(FONT_RESOURCES_RESOURCE))
			{
				chdir(FONT_RESOURCES_RESOURCE);
				$output = array();
				//exec("/usr/bin/svn delete \"" . dirname($file) . "\" --force", $output);
				//exec("rm -Rfv \"" . dirname($file) . "\"", $output);
				//echo "Execution: " . implode("\n", $output) . "\n\n\n";
			} else {			
				//unlink($file);
				//rmdir(dirname($file));
			}
			echo "\nMoved: " . basename($file) . ' too ' . $path;
			if ($archive = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts_archiving` WHERE `filename` = '" . basename($file) . "' OR `filename` = '" . urldecode(basename($file)) . "'")))
			{
				if ($path != $archive['path'])
					if( !$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `filename` = '" . mysql_escape_string($filename) . "', `path` = '" . mysql_escape_string($path) . "' WHERE `id` = '" . $archive['id'] . "'"))
						die("SQL Failed: $sql;");
					else
						echo "\nPath Adjusted for: " . $filename . ' too ' . $path;
				else
					echo "\nPath Fine for: " . basename($file);
			}
		}
	else
		echo "\nPath Fine for: " . basename($file);
			
}

exit(0);


?>