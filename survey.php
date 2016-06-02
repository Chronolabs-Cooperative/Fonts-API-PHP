<?php
/**
 * Chronolabs Fonting Repository Services REST API API
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
 * @since           2.1.9
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Fonting Repository Services REST API
 * @link			http://sourceforge.net/projects/chronolabsapis
 * @link			http://cipher.labs.coop
 */

global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source;
require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';

	
	$help=true;
	if (isset($_GET['output']) || !empty($_GET['output'])) {
		$version = isset($_GET['version'])?(string)$_GET['version']:'v2';
		$output = isset($_GET['output'])?(string)$_GET['output']:'';
		$name = isset($_GET['name'])?(string)$_GET['name']:'';
		$clause = isset($_GET['clause'])?(string)$_GET['clause']:'';
		$callback = isset($_REQUEST['callback'])?(string)$_REQUEST['callback']:'';
		$mode = isset($_GET['mode'])?(string)$_GET['mode']:'';
		$state = isset($_GET['state'])?(string)$_GET['state']:'';
		switch($output)
		{
			default:
				if (!in_array($output, file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-supported.diz')))
					$help=true;
				elseif (in_array($mode, array('font')) && strlen($clause) == 32)
					$help=false;
				break;
			case "css":
				if (in_array($mode, array('font')) && !empty($clause))
				{
					$help=false;
					if ($mode == 'random' && empty($state))
						$help=true;
				}
				break;	
			case "preview":
				if (in_array($mode, array('font')) && !empty($clause))
				{
					$help=false;
					if ($mode == 'random' && empty($state))
						$help=true;
				}
				break;		
		}
	} else {
		$help=true;
	}
	
	switch($output)
	{
		default:
			$data = getSurveyFontRawData($mode, $clause, $output, $version);
			break;
		case "html":
			break;
		case "css":
			$data = getSurveyCSSListArray($mode, $clause, $state, $name, $output, $version);
			break;	
		case "preview":
			if (function_exists('http_response_code'))
				http_response_code(400);
			$data = getSurveyPreviewHTML($mode, $clause, $state, $name, $output, $version);
			break;	
	}
	
	if (function_exists('http_response_code'))
		http_response_code(200);
	
	switch ($output) {
		default:
			echo $data;
			break;
		case 'html':
			break;
			break;
		case "css":
			header('Content-type: text/css');
			echo implode("\n\n", $data);
			break;
		case "preview":
			header('Content-type: text/html');
			echo $data;
			break;
			break;
	}
?>		