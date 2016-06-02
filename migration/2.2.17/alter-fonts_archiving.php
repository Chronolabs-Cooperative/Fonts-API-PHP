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

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_archiving` 
ADD COLUMN `added` INT(13) NOT NULL DEFAULT 0 COMMENT 'Date Archive was added to DB' AFTER `packing`,
ADD COLUMN `packed` INT(13) NOT NULL DEFAULT 0 COMMENT 'Date Archive was first packed' AFTER `added`,
ADD COLUMN `repacked` INT(13) NOT NULL DEFAULT 0 COMMENT 'Date Archive was repaired or repacked' AFTER `packed`,
ADD COLUMN `unlocalise` INT(13) NOT NULL DEFAULT 0 COMMENT 'Date Archive was delocalised from server too cold SVN' AFTER `repacked`,
ADD COLUMN `accessed` INT(13) NOT NULL DEFAULT 0 COMMENT 'Date Archive was last accessed on a hit' AFTER `unlocalise`,
ADD COLUMN `checked` INT(13) NOT NULL DEFAULT 0 COMMENT 'Date Archive was last spot check for flaws or errors' AFTER `accessed`,
ADD INDEX `CHRONOLOGISTIC` (`checked` ASC, `accessed` ASC, `unlocalise` ASC, `repacked` ASC, `packed` ASC, `added` ASC, `packing` ASC, `hits` ASC, `bytes` ASC, `files` ASC, `path` ASC, `filename` ASC, `font_id` ASC, `id` ASC)  COMMENT '';
		"))
	echo "SQL Failed: $sql;\n<br/>";

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_archiving`
	ADD COLUMN `repacks` INT(24) NOT NULL DEFAULT 0 COMMENT 'Number of times the archive has been repacked' AFTER `fingerprint`,
	ADD COLUMN `unlocalisations` INT(24) NOT NULL DEFAULT 0 COMMENT 'Number of times the the archive has be unlocalised to cold SVN' AFTER `repacks`,
	ADD COLUMN `cachings` INT(24) NOT NULL DEFAULT 0 COMMENT 'Number of times the cache has regenerated' AFTER `unlocalisations`,
	ADD COLUMN `cached` INT(13) NOT NULL DEFAULT 0 COMMENT 'Last time the cache was regenerated' AFTER `checked`,
	DROP INDEX `CHRONOLOGISTIC` ,
	ADD INDEX `CHRONOLOGISTIC` (`accessed` ASC, `unlocalise` ASC, `repacked` ASC, `packed` ASC, `added` ASC, `packing` ASC, `path` ASC, `filename` ASC, `font_id` ASC, `id` ASC, `checked` ASC, `cached` ASC, `cachings` ASC, `fingerprint` ASC)  COMMENT '';
	"))

if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_archiving`
		ADD COLUMN `sourcings` INT(24) NOT NULL DEFAULT 0 COMMENT 'Number of times archive was source for remote downloading' AFTER `cachings`,
		ADD COLUMN `sourced` INT(13) NOT NULL DEFAULT 0 COMMENT 'When last archive was sourced for remote downloading' AFTER `cached`,
		DROP INDEX `CHRONOLOGISTIC` ,
		ADD INDEX `CHRONOLOGISTIC` (`accessed` ASC, `unlocalise` ASC, `repacked` ASC, `packed` ASC, `added` ASC, `packing` ASC, `path` ASC, `filename` ASC, `font_id` ASC, `id` ASC, `checked` ASC, `cached` ASC, `fingerprint` ASC, `sourced` ASC)  COMMENT '';
		"))
		echo "SQL Failed: $sql;\n<br/>";
		
if (!$GLOBALS['FontsDB']->queryF($sql = "ALTER TABLE `fonts_archiving`
		DROP COLUMN `hits`;
		"))
		echo "SQL Failed: $sql;\n<br/>";		
	
		