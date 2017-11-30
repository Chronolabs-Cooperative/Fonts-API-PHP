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
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         fonts
 * @since           2.1.9
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Fonting Repository Services REST API
 * @link			http://sourceforge.net/projects/chronolabsapis
 * @link			http://cipher.labs.coop
 */

	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';
    
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	set_time_limit(3600*36*9*14*28);
	
	/**
	 * URI Path Finding of API URL Source Locality
	 * @var unknown_type
	 */
	$odds = $inner = array();
	foreach($inner as $key => $values) {
	    if (!isset($inner[$key])) {
	        $inner[$key] = $values;
	    } elseif (!in_array(!is_array($values) ? $values : md5(json_encode($values, true)), array_keys($odds[$key]))) {
	        if (is_array($values)) {
	            $odds[$key][md5(json_encode($inner[$key] = $values, true))] = $values;
	        } else {
	            $odds[$key][$inner[$key] = $values] = "$values--$key";
	        }
	    }
	}
	
	foreach($_POST as $key => $values) {
	    if (!isset($inner[$key])) {
	        $inner[$key] = $values;
	    } elseif (!in_array(!is_array($values) ? $values : md5(json_encode($values, true)), array_keys($odds[$key]))) {
	        if (is_array($values)) {
	            $odds[$key][md5(json_encode($inner[$key] = $values, true))] = $values;
	        } else {
	            $odds[$key][$inner[$key] = $values] = "$values--$key";
	        }
	    }
	}
	
	foreach(parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'], '?')?'&':'?').$_SERVER['QUERY_STRING'], PHP_URL_QUERY) as $key => $values) {
	    if (!isset($inner[$key])) {
	        $inner[$key] = $values;
	    } elseif (!in_array(!is_array($values) ? $values : md5(json_encode($values, true)), array_keys($odds[$key]))) {
	        if (is_array($values)) {
	            $odds[$key][md5(json_encode($inner[$key] = $values, true))] = $values;
	        } else {
	            $odds[$key][$inner[$key] = $values] = "$values--$key";
	        }
	    }
	}

	//echo "Processed Upload Form Fine<br/>";
	$time = time();
	$error = array();
	if (isset($inner['field']) || !empty($inner['field'])) {
		if (empty($_FILES[$inner['field']]))
			$error[] = 'No file uploaded in the correct field name of: "' . $inner['field'] . '"';
		else {
		    $formats = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'include'  . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-converted.diz')); 
			$packs = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'include'  . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-converted.diz'));
			$extensions = array_unique(array_merge($formats, $packs));
			sort($extensions);
			$pass = false;
			foreach($extensions as $xtension)
			{
				if (strtolower(substr($_FILES[$inner['field']]['name'], strlen($_FILES[$inner['field']]['name'])- strlen($xtension))) == strtolower($xtension))
					if (in_array($xtension, $formats))
						$filetype = 'font';
					else {
						$filetype = 'pack';
						$packtype = $xtension;
					}
					$pass=true;
					continue;
			}
			if ($pass == false)
				$error[] = 'The file extension type of <strong>'.$_FILES[$inner['field']]['name'].'</strong> is not valid you can only upload the following file types: <em>'.implode("</em>&nbsp;<em>*.", $extensions).'</em>!';
		}
	} else 
		$error[] = 'File uploaded field name not specified in the URL!';

	if (!isset($inner['prefix']) || empty($inner['prefix']) || strlen(trim($inner['prefix']))==0) {
		$error[] = 'No Prefix Specified for the Individual Font Identifier Hashinfo!';
	}
		
	if (isset($inner['email']) || !empty($inner['email'])) {
		if (!checkEmail($inner['email']))
			$error[] = 'Email is invalid!';
	} else
		$error[] = 'No Email Address for Notification specified!';
	
	if (((!isset($inner['name']) || empty($inner['name'])) || (!isset($inner['bizo']) || empty($inner['bizo']))) && 
		(isset($inner['scope']['to']) && $inner['scope']['to'] = 'to')) {
		$error[] = 'No Converters Individual name or organisation not specified in survey scope when selected!';
	}
	
	if ((!isset($inner['email-cc']) || empty($inner['email-cc'])) && (isset($inner['scope']['cc']) && $inner['scope']['cc'] = 'cc')) {
		$error[] = 'No Survey addressee To by survey cc participants email\'s specified when survey scope is selected!';
	}
	
	if ((!isset($inner['email-bcc']) || empty($inner['email-bcc'])) && (isset($inner['scope']['bcc']) && $inner['scope']['bcc'] = 'bcc')) {
		$error[] = 'No Survey addressee To by survey bcc participants email\'s specified when survey scope is selected!';
	}
	
	$uploadpath = DIRECTORY_SEPARATOR . $inner['email'] . DIRECTORY_SEPARATOR . microtime(true);
	if (!is_dir(constant("FONT_UPLOAD_PATH") . $uploadpath)) {
		if (!mkdir(constant("FONT_UPLOAD_PATH") . $uploadpath, 0777, true)) {
			$error[] = 'Unable to make path: '.constant("FONT_UPLOAD_PATH") . $uploadpath;
		}
	}
	
	if (!is_dir(constant("FONT_RESOURCES_UNPACKING") . $uploadpath)) {
		if (!mkdir(constant("FONT_RESOURCES_UNPACKING") . $uploadpath, 0777, true)) {
			$error[] = 'Unable to make path: '.constant("FONT_RESOURCES_UNPACKING") . $uploadpath;	
		}
	}
	//echo "Checked for Errors Fine<br/>";
	if (!empty($error))
	{
		redirect(isset($inner['return'])&&!empty($inner['return'])?$inner['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Error Has Occured</h1><br/><p>" . implode("<br />", $error) . "</p></center>");
		exit(0);
	}
	$uploader = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json"), true);
	//echo "Loaded Upload Data Fine<br/>";
	$file = array();
	$uploader[$ipid][$time]['type'] = $filetype;
	switch ($filetype)
	{
		case "font":
			if (!move_uploaded_file($_FILES[$inner['field']]['tmp_name'], $file[] = constant("FONT_UPLOAD_PATH") . $uploadpath . DIRECTORY_SEPARATOR . ($uploader[$ipid][$time]['file'] = $_FILES[$inner['field']]['name']))) {
				redirect(isset($inner['return'])&&!empty($inner['return'])?$inner['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Uploading Error Has Occured</h1><br/><p>Fonts API was unable to recieve and store: <strong>".$_FILES[$inner['field']]['name']."</strong>!</p></center>");
				exit(0);
			} else 
				$success = array($_FILES[$inner['field']]['name'] => $_FILES[$inner['field']]['name']);
			break;
		case "pack":
			if (!move_uploaded_file($_FILES[$inner['field']]['tmp_name'], $file[] = constant("FONT_UPLOAD_PATH") . $uploadpath . DIRECTORY_SEPARATOR . ($uploader[$ipid][$time]['pack'] = $_FILES[$inner['field']]['name']))) {
				redirect(isset($inner['return'])&&!empty($inner['return'])?$inner['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Uploading Error Has Occured</h1><br/><p>Fonts API was unable to recieve and store: <strong>".$_FILES[$inner['field']]['name']."</strong>!</p></center>");
				exit(0);
			} else 
				$success = array($_FILES[$inner['field']]['name'] => $_FILES[$inner['field']]['name']);
			$uploader[$ipid][$time]['packtype'] = $packtype;
			break;
		default:
			$error[] = 'The file extension type of <strong>*.'.$fileext.'</strong> is not valid you can only upload the following: <em>*.otf</em>, <em>*.ttf</em> & <em>*.zip</em>!';
			break;
	}
	//echo "Uploaded Fine<br/>";
	if (!empty($error))
	{
		redirect(isset($inner['return'])&&!empty($inner['return'])?$inner['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Error Has Occured</h1><br/><p>" . implode("<br />", $error) . "</p></center>");
		exit(0);
	}
	mkdir(__DIR__  . DIRECTORY_SEPARATOR . "lost", 0777, true);
	foreach( get7zListAsArray($path = __DIR__ . DIRECTORY_SEPARATOR . 'lost') as $lost)
	{
		if (md5_file($file) == md5_file($path . DIRECTORY_SEPARATOR . $lost))
			unlink($path . DIRECTORY_SEPARATOR . $lost);
	}
	//echo "Lost Fine<br/>";
	$GLOBALS["APIDB"]->queryF("UPDATE `" . $GLOBALS['APIDB']->prefix('networking') . "` SET `uploads` = `uploads` + 1 WHERE `ip_id` = '".$ipid."'");
	$uploader[$ipid][$time]['files'][] = $file;
	$uploader[$ipid][$time]['form'] = $inner;
	$uploader[$ipid][$time]['path'] = $uploadpath;
	mkdirSecure(__DIR__  . DIRECTORY_SEPARATOR . "include"  . DIRECTORY_SEPARATOR . "data", 0777, true);
	file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . "include"  . DIRECTORY_SEPARATOR . "data". DIRECTORY_SEPARATOR . "uploads.json", json_encode($uploader));
	//echo "Made Record of Upload - Ok!<br/>";
	redirect(isset($inner['return'])&&!empty($inner['return'])?$inner['return']:'http://'. $_SERVER["HTTP_HOST"], 18, "<center><h1 style='color:rgb(0,198,0);'>Uploading Partially or Completely Successful</h1><br/><div>The following files where uploaded and queued for conversion on the API Successfully:</div><div style='height: auto; clear: both; width: 100%;'><ul style='height: auto; clear: both; width: 100%;'><li style='width: 24%; float: left;'>".implode("</li><li style='width: 24%; float: left;'>", $success)."</li></ul></div><br/><div style='clear: both; height: 11px; width: 100%'>&nbsp;</div><p>You need to wait for the conversion maintenance to run in the next 30 minutes, you will recieve an email when done per each file!</p></center>");
	exit(0);
	
?>