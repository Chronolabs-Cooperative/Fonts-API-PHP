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
$GLOBALS['FontsDB']->queryF($sql = "START TRANSACTION");
$result = $GLOBALS['FontsDB']->queryF("SELECT * from `fonts_archiving` ORDER BY RAND() LIMIT 300");
while($row = $GLOBALS['FontsDB']->fetchArray($result))
{
	$sql = "SELECT count(*) FROM `uploads` WHERE `font_id` = '".$row['font_id']."'";
	list($countb) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
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
		if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `uploads` (`" . implode('`, `', array_keys($font)) . "`) VALUES ('" . implode("', '", $font) . "')"))
			die("SQL Failed: $sql;");
		else 
			echo ".";
	}
	
}
$GLOBALS['FontsDB']->queryF($sql = "COMMIT");