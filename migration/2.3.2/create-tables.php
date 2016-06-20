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

if (!$GLOBALS['FontsDB']->queryF($sql = "CREATE TABLE `fonts_glyphs_contours` (
		`id` MEDIUMINT(41) NOT NULL AUTO_INCREMENT COMMENT '',
		`font-id` VARCHAR(32) NOT NULL DEFAULT '--------------------------------' COMMENT '',
		`glyph-id` VARCHAR(32) NOT NULL DEFAULT '--------------------------------' COMMENT '',
		`contour` INT(10) NOT NULL DEFAULT 0 COMMENT '',
		`weight` INT(10) NOT NULL DEFAULT 0 COMMENT '',
		`x` INT(8) NOT NULL DEFAULT 0 COMMENT '',
		`y` INT(8) NOT NULL DEFAULT 0 COMMENT '',
		`type` VARCHAR(15) NOT NULL DEFAULT '-----' COMMENT '',
		`smooth` ENUM('yes', 'no', '-----') NOT NULL DEFAULT '-----' COMMENT '',
		`created` INT(13) NOT NULL DEFAULT 0 COMMENT '',
		PRIMARY KEY (`id`, `font-id`, `glyph-id`, `weight`, `contour`)  COMMENT '');"))
	echo "SQL Failed: $sql;\n<br/>";
	
if (!$GLOBALS['FontsDB']->queryF($sql = "CREATE TABLE `fonts_glyphs` (
		`glyph-id` VARCHAR(32) NOT NULL DEFAULT '--------------------------------' COMMENT '',
		`font-id` VARCHAR(32) NOT NULL DEFAULT '--------------------------------' COMMENT '',
		`fingerprint` VARCHAR(44) NOT NULL DEFAULT '--------------------------------------------' COMMENT '',
		`name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '',
		`ufofile` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '',
		`unicode` VARCHAR(8) NOT NULL DEFAULT '--------' COMMENT '',
		`format` INT(10) NOT NULL DEFAULT 1 COMMENT '',
		`width` INT(10) NOT NULL DEFAULT 0 COMMENT '',
		`contours` INT(8) NOT NULL DEFAULT 0 COMMENT '',
		`pointers` INT(8) NOT NULL DEFAULT 0 COMMENT '',
		`smoothers` INT(8) NOT NULL DEFAULT 0 COMMENT '',
		`addon` ENUM('yes', 'no') NOT NULL DEFAULT 'no' COMMENT '',
		`addon-glyph-id` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '',
		`addon-font-id` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '',
		`created` INT(13) NOT NULL DEFAULT 0 COMMENT '',
		`occurences` INT(10) NOT NULL DEFAULT 1 COMMENT '',
		PRIMARY KEY (`glyph-id`, `font-id`, `name`)  COMMENT '');"))
	echo "SQL Failed: $sql;\n<br/>";
		

	
	
