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

/** THIS CRON ONLY EXECUTES ONCE A MONTH MINIMAL IF NOT EVERY 2 - 4 MONTHS 
 * 
 * Only executed via bash script in crontab and will execute for many days at a time ie: 
 * 
 *    0 12 28 1,3,5,7,9,11 * sh /path/to/api/fonts/crons/crawling-fonts.sh
 *    
 * You may wish to on installation execute in the shell terminal the following to set up the bash script for the first time just once
 * 
 *    /usr/bin/php -q /path/to/api/fonts/crons/crawling-fonts.php
 * 
 **/

error_reporting(E_ERROR);
set_time_limit(1999);
require_once dirname(__DIR__).'/functions.php';
require_once dirname(__DIR__).'/class/fontages.php';

$packs = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-converted.diz'));
$formats = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-converted.diz'));

$script = array('!#/sh/bash');
$path = FONT_UPLOAD_PATH . DIRECTORY_SEPARATOR . 'wishcraft@users.sourceforge.net' . DIRECTORY_SEPARATOR . microtime();
mkdir(FONT_UPLOAD_PATH, 0777, true);
$script[] = 'mkdir "' . FONT_UPLOAD_PATH . DIRECTORY_SEPARATOR . 'wishcraft@users.sourceforge.net"';
$script[] = "mkdir \"$path\"";
$crawls = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-crawls.diz'));
shuffle($crawls);
shuffle($crawls);
shuffle($crawls);
shuffle($crawls);
foreach($crawls as $crawl)
	$script[] = "/usr/bin/wget --span-host --level=11 --recursive -x -k --continue --accept=txt,jsp,java,css,htm,html,php,asp," . implode(",", $packs) . "," . implode(",", $formats) . " \"$crawl\" \"$path\"";
$script[] = "/usr/bin/tee \"$path\" > \"$path" . DIRECTORY_SEPARATOR . 'finished.dat"';
$script[] = "/usr/bin/php -q \"".__DIR__."/register-crawling.php\"";
$script[] = "/usr/bin/php -q \"".__DIR__."/crawling-fonts.php\"";		
writeRawFile(__DIR__ . DIRECTORY_SEPARATOR . "crawling-fonts.sh", implode("\n", $script));
$crawldat = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json"), true);
$crawldat[$path]['set'] = microtime(true);
writeRawFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json", json_encode($crawldat));



?>