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

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts` 
	ADD COLUMN `version` FLOAT(6,3) NULL DEFAULT 1.0 COMMENT '' AFTER `latitude`,
	ADD COLUMN `date` VARCHAR(32) NULL DEFAULT '' COMMENT '' AFTER `version`,
	ADD COLUMN `uploaded` INT(13) NULL DEFAULT 0 COMMENT '' AFTER `date`,
	ADD COLUMN `licence` VARCHAR(15) NULL DEFAULT 'gpl3' COMMENT '' AFTER `uploaded`,
	ADD COLUMN `company` VARCHAR(64) NULL DEFAULT '' COMMENT '' AFTER `licence`,
	ADD COLUMN `matrix` VARCHAR(128) NULL DEFAULT '' COMMENT '' AFTER `company`,
	ADD COLUMN `bbox` VARCHAR(20) NULL DEFAULT '' COMMENT '' AFTER `matrix`,
	ADD COLUMN `painttype` VARCHAR(20) NULL DEFAULT '' COMMENT '' AFTER `bbox`,
	ADD COLUMN `info` VARCHAR(128) NULL DEFAULT '' COMMENT '' AFTER `painttype`,
	ADD COLUMN `family` VARCHAR(128) NULL DEFAULT '' COMMENT '' AFTER `info`,
	ADD COLUMN `weight` VARCHAR(32) NULL DEFAULT '' COMMENT '' AFTER `family`,
	ADD COLUMN `fstype` VARCHAR(32) NULL DEFAULT '' COMMENT '' AFTER `weight`,
	ADD COLUMN `italicangle` VARCHAR(16) NULL DEFAULT '' COMMENT '' AFTER `fstype`,
	ADD COLUMN `fixedpitch` VARCHAR(16) NULL DEFAULT '' COMMENT '' AFTER `italicangle`,
	ADD COLUMN `underlineposition` VARCHAR(16) NULL DEFAULT '' COMMENT '' AFTER `fixedpitch`,
	ADD COLUMN `underlinethickness` VARCHAR(16) NULL DEFAULT '' COMMENT '' AFTER `underlineposition`;"))
	echo "SQL Failed: $sql;\n<br/>";
	
if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `checked` = -1"))
		echo "SQL Failed: $sql;\n<br/>";
		

	
	
