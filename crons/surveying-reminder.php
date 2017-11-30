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

$seconds = floor(mt_rand(1, floor(60 * 4.75)));
set_time_limit($seconds ^ 4);
sleep($seconds);

$sql = array();
ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '300M');
require_once dirname(__DIR__).'/constants.php';
include_once dirname(__DIR__).'/include/functions.php';
require_once dirname(__DIR__).'/class/fontsmailer.php';
error_reporting(E_ERROR);
set_time_limit(7200);
$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
$result = $GLOBALS['APIDB']->queryF($sql[] = "SELECT * from `" . $GLOBALS['APIDB']->prefix('flows_history') . "` WHERE `reminders` > 0 AND `reminding` > '0' AND `reminding` <= '".time()."'  AND `step` = 'waiting' ORDER BY RAND() LIMIT 99");
while($row = $GLOBALS['APIDB']->fetchArray($result))
{
	$sendmail = false;
	if ($rrow = $GLOBALS['APIDB']->fetchArray($GLOBALS['APIDB']->queryF($sql[] = "SELECT * from `" . $GLOBALS['APIDB']->prefix('flows') . "` WHERE `flow_id` = '".$row['flow_id']."' AND `participate` = 'yes'")))
	{
		$mailer = new FontsMailer(API_EMAIL_ADDY, API_EMAIL_FROM);
		if (file_exists($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "SMTPAuth.diz"))
			$smtpauths = explode("\n", str_replace(array("\r\n", "\n\n", "\n\r"), "\n", file_get_contents($file)));
		if (count($smtpauths)>=1)
			$auth = explode("||", $smtpauths[mt_rand(0, count($smtpauths)-1)]);
		if (!empty($auth[0]) && !empty($auth[1]) && !empty($auth[2]))
			$mailer->multimailer->setSMTPAuth($auth[0], $auth[1], $auth[2]);
		$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'survey-reminder.html');
		$html = str_replace("{X_FONTSURVEYLINK}", API_URL . '/v2/survey/start/' . $row['key'] . '/html.api', $html);
		$previewimg = API_URL . '/v2/survey/preview/' . $row['key'] . '/png.api';
		$html = str_replace("{X_FONTUPLOADPREVIEW}", $previewimg, $html);
		if ($mailer->sendMail($rrow['email'], array(),  array(), "Font cateloguing survery reminder", $html, array(), NULL, true))
		{
			echo "Sent mail to: " . $rrow['email']."\n\n<br/>\n";
			$reminding = time()+(($row['expiring']-time())/$row['reminders']);
			$GLOBALS['APIDB']->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('flows_history') . "` SET `reminders` = `reminders` - 1, `reminding` = '$reminding' where `id` = '" . $row['history_id'] . "'");
		}
	}
}
$GLOBALS['APIDB']->queryF($sql = "COMMIT");
?>