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

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `uploads` 
CHANGE COLUMN `scope` `scope` ENUM('to', 'cc', 'bcc', 'all', 'none') NULL DEFAULT 'none' COMMENT '' ;
		"))
	echo "SQL Failed: $sql;\n<br/>";
		
	if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `uploads` SET `quizing` = UNIX_TIMESTAMP(), `expired` = UNIX_TIMESTAMP()+1831, `slotting` = 0, `needing` = 1, `finished` = 2, `surveys` = 2, `available` = 0 WHERE `quizing` = 0"))
		echo "SQL Failed: $sql;\n<br/>";

	
	
