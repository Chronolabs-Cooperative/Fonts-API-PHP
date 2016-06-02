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
require_once dirname(dirname(__DIR__)).'/functions.php';
require_once dirname(dirname(__DIR__)).'/class/fontages.php';

if (!$GLOBALS['FontsDB']->queryF($sql = "CREATE TABLE `whois` (
  `id` varchar(32) NOT NULL,
  `whois` mediumtext NOT NULL,
  `created` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  `instances` mediumint(18) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
	echo "SQL Failed: $sql;\n<br/>";


$result = $GLOBALS['FontsDB']->queryF("SELECT DISTINCT md5(`whois`) as `id`, `whois` FROM `networking`");
while($row = $GLOBALS['FontsDB']->fetchArray($result))
{
	$sql = "SELECT count(*) FROM `whois` WHERE `id` = '".$row['id']."'";
	list($countb) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
	if ($countb == 0)
	{
		$whois = array();
		$whois['id'] = $row['id'];
		$whois['whois'] = mysql_real_escape_string($row['whois']);
		$whois['created'] = time();
		$whois['last'] = time();
		$whois['instances'] = 1;
		if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `whois` (`" . implode('`, `', array_keys($whois)) . "`) VALUES ('" . implode("', '", $whois) . "')"))
			die("SQL Failed: $sql;");
		else 
			echo ".";
	}
}

if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `networking` SET `whois` = md5(`whois`)"))
	die("SQL Failed: $sql;");
else
	echo ".";


if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `networking` CHANGE COLUMN `whois` `whois` VARCHAR(32) NULL DEFAULT NULL COMMENT '' ;"))
	die("SQL Failed: $sql;");
else
	echo ".";
	
	
