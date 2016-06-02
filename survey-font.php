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

	$version = isset($_GET['version'])?(string)$_GET['version']:'v2';
	$output = isset($_GET['output'])?(string)$_GET['output']:'';
	$key = isset($_GET['key'])?(string)$_GET['key']:'';
	
	$sql = "SELECT * FROM `uploads` WHERE `key` = '" . $key . "'";
	if ($result = $GLOBALS['FontsDB']->queryF($sql))
	{
		if ($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$data = json_decode($row['datastore'], true);
			$fontname = str_replace(" ", "", $fontspaces = $data["FontName"]);
			$path = $row['currently_path'];
		} else
			die("Font fingerprint not found!");
	}
	
	if (isset($_GET['output']) || !empty($key)) {
		switch($output)
		{
			default:
				if (file_exists($file = $path . DIRECTORY_SEPARATOR . $key . "." . $output))
				{
					header("Context-Type: " . getMimetype($output));
					die(file_get_contents($file));
				} else
					die("Font Unknown format ~ not found!");
				break;
			case "css":
				$formats = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-css-listed.diz'));
				$fonts = array();
				foreach(getFileListAsArray($path) as $kkey => $file)
					foreach($formats as $format)
						if (strpos(strtolower($file), strtolower(".$format")))
							$fonts[$format] = $file;
				$buff = array();
				foreach($fonts as $type => $file)
					$buff[] = "url($source/v2/survey/font/$key/$type.api) format('".($type=='ttf'?'truetype':($type == 'otf'?'opentype':$type))."')";
				$css = array();
				$css[] = "\n";
				$css[] = "/** Font: $fontspaces **/";
				$css[] = "@font-face {";
				$css[] = "\tfont-family: $fontname;";
				$css[] = "\tsrc: " . implode(", ", $buff) . ";";
				$css[] = "}";
				header("Context-Type: text/css");
				die(implode("\n", $css));
				break;
		}
	}
