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

if (!$GLOBALS['FontsDB']->queryF($sql = "CREATE TABLE `peers` (
  `peer-id` varchar(32) NOT NULL,
  `api-uri` varchar(200) NOT NULL,
  `api-uri-callback` varchar(200) NOT NULL,
  `api-uri-zip` varchar(200) NOT NULL,
  `api-uri-fonts` varchar(200) NOT NULL,
  `polinating` enum('Yes','No') NOT NULL,
  `version` varchar(20) NOT NULL,
  `heard` int(12) NOT NULL DEFAULT '0',
  `called` int(12) NOT NULL DEFAULT '0',
  `down` int(12) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`peer-id`,`api-uri`,`api-uri-callback`,`api-uri-zip`,`api-uri-fonts`,`polinating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
	die("SQL Failed: $sql;\n<br/>");


if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `peer_id` = '". $GLOBALS['peer-id']."'"))
		die("SQL Failed: $sql;\n<br/>");

	
	
