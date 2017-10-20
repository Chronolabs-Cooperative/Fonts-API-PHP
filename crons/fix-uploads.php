<?php
/**
 * Chronolabs Fontages API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers FROM this source code or any supporting source code
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
$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
$result = $GLOBALS['APIDB']->queryF("SELECT * FROM `" . $GLOBALS['APIDB']->prefix('fonts_archiving') . "` ORDER BY RAND() LIMIT 300");
while($row = $GLOBALS['APIDB']->fetchArray($result))
{
	sleep(mt_rand(10,30));
	$sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('uploads') . "` WHERE `font_id` = '".$row['font_id']."'";
	list($countb) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql));
	if ($countb == 0)
	{
		$font['font_id'] = $row['font_id'];
		$font['uploaded'] = time();
		$font['converted'] = time();
		$font['quizing'] = time();
		$font['storaged'] = time();
		$font['sorting'] = time();
		$font['cleaning'] = time();
		$font['released'] = time();
		$font['finished'] = time();
		if (!$GLOBALS['APIDB']->queryF($sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('uploads') . "` (`" . implode('`, `', array_keys($font)) . "`) VALUES ('" . implode("', '", $font) . "')"))
			die("SQL Failed: $sql;");
		else 
			echo ".";
	}
	
}
$GLOBALS['APIDB']->queryF($sql = "COMMIT");