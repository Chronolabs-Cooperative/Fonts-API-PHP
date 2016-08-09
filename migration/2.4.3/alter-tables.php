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

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts` ADD `barcode_id` VARCHAR(13) NOT NULL DEFAULT '-------------' AFTER `id`, ADD `referee_id` VARCHAR(8) NOT NULL DEFAULT '--------' AFTER `barcode_id`;"))
	echo "SQL Failed: $sql;\n<br/>";
	
if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_glyphs` CHANGE `glyph-id` `glyph_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '--------------------------------', CHANGE `font-id` `font_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '--------------------------------', CHANGE `addon-glyph-id` `addon_glyph_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `addon-font-id` `addon_font_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';"))
	echo "SQL Failed: $sql;\n<br/>";
	
if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_glyphs_contours` CHANGE `font-id` `font_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '--------------------------------', CHANGE `glyph-id` `glyph_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '--------------------------------';"))
	echo "SQL Failed: $sql;\n<br/>";
	
if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts` ADD `added` INT(12) NOT NULL DEFAULT '0' AFTER `nodes`;"))
	echo "SQL Failed: $sql;\n<br/>";
	
	
