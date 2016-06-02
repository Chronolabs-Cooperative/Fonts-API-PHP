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

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_files` 
ADD COLUMN `updates` INT(20) NULL COMMENT 'Number of time file has updated' AFTER `hits`,
ADD COLUMN `caching` INT(20) NULL COMMENT 'Number of time the file has been cached' AFTER `updates`,
ADD COLUMN `updated` INT(13) NULL COMMENT 'When it was last updated' AFTER `accessed`,
ADD COLUMN `cached` INT(13) NULL COMMENT 'When it was last cached' AFTER `updated`,
ADD INDEX `CHRONOLOGISTIC` (`updated` ASC, `accessed` ASC, `created` ASC, `updates` ASC, `hits` ASC, `bytes` ASC, `path` ASC, `filename` ASC, `extension` ASC, `font_id` ASC, `id` ASC, `archive_id` ASC, `caching` ASC, `cached` ASC)  COMMENT '';
		"))
	echo "SQL Failed: $sql;\n<br/>";
		
	if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_files`
ADD COLUMN `accessing` INT(20) NULL COMMENT 'Number of time file has accessed' AFTER `hits`,
ADD COLUMN `sourcing` INT(20) NULL COMMENT 'Number of time the file has been physically remotely downloaded' AFTER `caching`,
ADD COLUMN `sourced` INT(13) NULL COMMENT 'When it was last the file was remotely downloaded' AFTER `cached`,
DROP INDEX `CHRONOLOGISTIC` ,
ADD INDEX `CHRONOLOGISTIC` (`updated` ASC, `cached` ASC, `sourced` ASC, `accessed` ASC, `created` ASC, `bytes` ASC, `path` ASC, `filename` ASC, `extension` ASC, `font_id` ASC, `id` ASC, `archive_id` ASC)  COMMENT '';
		"))
		echo "SQL Failed: $sql;\n<br/>";
		
if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_files`
		CHANGE COLUMN `type` `type` ENUM('json', 'diz', 'pfa', 'pfb', 'pt3', 't42', 'sfd', 'ttf', 'bdf', 'otf', 'otb', 'cff', 'cef', 'gai', 'woff', 'svg', 'ufo', 'pf3', 'ttc', 'gsf', 'cid', 'bin', 'hqx', 'dfont', 'mf', 'ik', 'fon', 'fnt', 'pcf', 'pmf', 'pdb', 'eot', 'afm', 'php', 'z', 'png', 'gif', 'jpg', 'data', 'css', 'other') NOT NULL DEFAULT 'other' COMMENT '' ;
	"))
		echo "SQL Failed: $sql;\n<br/>";	
	
	
		