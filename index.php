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
	define('API_DEBUG', false);

	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source;
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
	setExecutionTimer('header');
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';
	
	
	/**
	 * URI Path Finding of API URL Source Locality
	 * @var unknown_type
	 */
	$odds = $inner = array();
	foreach($_GET as $key => $values) {
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
	$GLOBAL['apifuncs'] = array();	
	$help=true;
	if (isset($inner['output']) || !empty($inner['output'])) {
		$version = isset($inner['version'])?(string)$inner['version']:'v2';
		$output = isset($inner['output'])?(string)$inner['output']:'';
		$name = isset($inner['name'])?(string)$inner['name']:'';
		$clause = isset($inner['clause'])?(string)$inner['clause']:'';
		$callback = isset($_REQUEST['callback'])?(string)$_REQUEST['callback']:'';
		$mode = isset($inner['mode'])?(string)$inner['mode']:'';
		$state = isset($inner['state'])?(string)$inner['state']:'';
		switch($output)
		{
			default:
				if (!in_array($output, cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-supported-'.$version.'.diz'))))
					$help=true;
				elseif (in_array($mode, array('font')) && strlen($clause) == 32)
					$help=false;
				break;
			case "callback":
				if (in_array($mode, array('fonthit', 'archive')) && strlen($clause) == 32)
					$help=false;
					break;
			case "ufo":
				if (in_array($mode, array('font')) && strlen($clause) == 32)
					$help=false;
					break;
			case "rss":
				if (in_array($mode, array('data')))
					$help=false;
					
				break;
			case "download":
				if (in_array($mode, array('data')) && strlen($clause) == 32)
					$help=false;
				else
					$help=true;
				if (in_array($state, array_keys(getArchivingShellExec())))
					$help=false;
				else
					$help=true;
			case "diz":
				if (in_array($mode, array('data')) && strlen($clause) == 32)
					$help=false;
				
				break;
			case "raw":
			case "html":
			case "serial":
			case "json":
			case "xml":
				if (in_array($mode, array('nodes', 'fonts', 'data', 'callbacks','identities')))
					$help=false;
				break;
			case "forms":
				if (in_array($mode, array('uploads','releases')))
				{
					$help=false;
					if (empty($clause) && isset($_POST['return']))
						$clause = $_POST['return'];
				}
				break;
			case "profile":
				if (in_array($mode, array('sites')) && in_array($clause, array('create', 'forgotten', 'edit')))
					$help=false;
				break;
			case "css":
				if (in_array($mode, array('fonts', 'font', 'random')) && !empty($clause))
				{
					$help=false;
					if ($mode == 'random' && empty($state))
						$help=true;
				}
				break;	
			case "naming":
				if (in_array($mode, array('font')) && !empty($clause))
				{
					$help=false;
				}
				break;
			case "preview":
				if (in_array($mode, array('fonts', 'font', 'random')) && !empty($clause))
				{
					$help=false;
					if ($mode == 'random' && empty($state))
						$help=true;
				}
				break;	
			case "glyph":
				if (in_array($mode, array('font')) && !empty($clause) && !empty($inner['char']))
				{
					$help=false;
				}
				break;
		}
	} else {
		$help=true;
	}
	
	if ($help==true) {
		setExecutionTimer('help');
		$GLOBAL['apifuncs']['help']['start'] = microtime(true);
		if (function_exists('http_response_code'))
			http_response_code(400);
		include dirname(__FILE__).'/help.php';
		$GLOBAL['apifuncs']['help']['end'] = microtime(true);
		saveExecutionTimer();
		exit;
	}
	
	setExecutionTimer($output);
	$GLOBAL['apifuncs'][$output]['start'] = microtime(true);

	switch($output)
	{
		default:
			$data = getFontRawData($mode, $clause, $output, $version);
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `sourcings` = `sourcings` + 1, `sourced` = UNIX_TIMESTAMP() WHERE `filename` LIKE '" . $GLOBALS['filename'] . "' AND `font_id` = '" . $clause . "'");
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `downloaded` = `downloaded` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $clause . "'");
			break;
		case "callback":
			$data = setFontCallback($mode, $clause, $state, $output, $version);
			break;
		case "ufo":
			$data = getFontUFORawData($mode, $clause, $state, $output, $version);
			break;
		case "rss":
			if (!file_exists($file = FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . $mode .'-' . $clause.'-'.date("Y-m-d-H-") . floor((time()-strtotime(date("Y-m-d H:00:00")))/600). '.rss'))
			{
				$data = getFontsRssData($mode, $clause, $state, $output, $version);
				writeRawFile($file, $data);
			} else 
				$data = file_get_contents($file);
			break;
		case "download":
			$data = getFontDownload($mode, $clause, $state, $output, $version);
			break;
		case "diz":
			$data = getFontFileDiz($mode, $clause, $state, $output, $version);	
			break;
		case "raw":
		case "html":
		case "serial":
		case "json":
		case "xml":
			switch ($mode)
			{
				case "identities":
					$data = getFontIdentitiesArray($clause, $output, $state, $version);
					break;
				case "nodes":
					$data = getNodesListArray($clause, $output, $state, $version);
					break;
				case "fonts":
					$data = getFontsListArray($clause, $output, $state, $version);
					break;
				case "data":
					$data = getFontsDataArray($clause, $state, $output, $version);
					break;
				case "callbacks":
					$data = getFontsCallbacksArray($clause, $state, $output, $version);
					break;
			}
			break;
		case "profile":
			$data = '';
			break;
		case "css":
			$data = getCSSListArray($mode, $clause, $state, $name, $output, $version);
			break;	
		case "naming":
			if (function_exists('http_response_code'))
				if (substr($_SERVER["REQUEST_URI"], strlen($_SERVER["REQUEST_URI"])-strlen($file='image.'.$output))==$file)
					http_response_code(200);
				else 
					http_response_code(501);
			$data = getNamingImage($mode, $clause, $state, $output, $version);
			break;
		case "preview":
			if (function_exists('http_response_code'))
				if (substr($_SERVER["REQUEST_URI"], strlen($_SERVER["REQUEST_URI"])-strlen($file='image.'.$output))==$file)
					http_response_code(200);
				else 
					http_response_code(501);
			$data = getPreviewHTML($mode, $clause, $state, $name, $output, $version);
			break;	
		case "glyph":
			if (function_exists('http_response_code'))
				if (substr($_SERVER["REQUEST_URI"], strlen($_SERVER["REQUEST_URI"])-strlen($file='image.'.$output), strlen($file))==$file)
					http_response_code(200);
				else 
					http_response_code(501);
			$data = getGlyphPreview($mode, $clause, $state, $name, $output, $inner['char'], $version);
			break;
		case "forms":
			if (function_exists('http_response_code'))
				http_response_code(201);
			die(getHTMLForm($mode, $clause, $callback, $output, $version));
			break;
	}
	
	if (function_exists('http_response_code'))
		http_response_code(200);
	
	switch ($output) {
		default:
			if(ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}
			if (isset($GLOBALS['filename'])) {
				// Send Download Headers
				header('Content-Type: ' . getMimetype($output));
				header('Content-Disposition: attachment; filename="' . $GLOBALS['filename'] . '"');
				header('Content-Transfer-Encoding: binary');
				header('Accept-Ranges: bytes');
				header('Cache-Control: private');
				header('Pragma: private');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			}	
			die($data);
			break;
		case "ufo":
			if (!strpos($data, "xml"))
				header('Content-type: text/html');
			else
				header('Content-type: application/xml');
			die($data);
			break;
		case 'html':
			echo '<h1>' . $country . ' - ' . $place . ' (Places data)</h1>';
			echo '<pre style="font-family: \'Courier New\', Courier, Terminal; font-size: 0.77em;">';
			echo implode("\n", $data);
			echo '</pre>';
			break;
		case 'raw':
			echo implode("} | {", $data);
			break;
		case 'json':
			header('Content-type: application/json');
			echo json_encode($data);
			break;
		case 'serial':
			header('Content-type: text/plain');
			echo serialize($data);
			break;
		case 'xml':
			header('Content-type: application/xml');
			$dom = new XmlDomConstruct('1.0', 'utf-8');
			$dom->fromMixed(array('root'=>$data));
 			echo $dom->saveXML();
			break;
		case "css":
			header('Content-type: text/css');
			echo implode("\n\n", $data);
			break;
		case "preview":
			header('Content-type: text/html');
			echo $data;
			break;
		case "rss":
			header('Content-type: application/rss+xml');
			echo $data;
			break;
		case "diz":
			header('Content-type: text/plain');
			echo $data;
			break;
	}
	$GLOBAL['apifuncs'][$output]['end'] = microtime(true);
	// Checks Cache for Cleaning
	@cleanResourcesCache();
	saveExecutionTimer();
?>		
