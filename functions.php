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



if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
require_once __DIR__.'/constants.php';
if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
use FontLib\Font;
require_once __DIR__.'/class/FontLib/Autoloader.php';
if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");

/**
 *
 * @param string $fontfile path to font file (TTF, OTF or PFB).
 * @param string $fmfile font metrics file (UFM or AFM).
 * @param boolean $embedded Set to false to not embed the font, true otherwise (default).
 * @param string $enc Name of the encoding table to use. Omit this parameter for TrueType Unicode, OpenType Unicode and symbolic fonts like Symbol or ZapfDingBats.
 * @param array $patch Optional modification of the encoding
 */
function MakePHPFont($fontfile, $fmfile, $path = "/tmp/", $embedded=true, $enc='cp1252', $patch=array()) {
	//Generate a font definition file
	ini_set('auto_detect_line_endings', '1');
	if (!file_exists($fontfile)) {
		die('Error: file not found: '.$fontfile);
	}
	if (!file_exists($fmfile)) {
		die('Error: file not found: '.$fmfile);
	}
	$cidtogidmap = '';
	$map = array();
	$diff = '';
	$dw = 0; // default width
	$ffext = strtolower(substr($fontfile, -3));
	$fmext = strtolower(substr($fmfile, -3));
	if ($fmext == 'afm') {
		if (($ffext == 'ttf') OR ($ffext == 'otf')) {
			$type = 'TrueType';
		} elseif ($ffext == 'pfb') {
			$type = 'Type1';
		} else {
			die('Error: unrecognized font file extension: '.$ffext);
		}
		if ($enc) {
			$map = ReadMap($enc);
			foreach ($patch as $cc => $gn) {
				$map[$cc] = $gn;
			}
		}
		$fm = ReadAFM($fmfile, $map);
		if (isset($widths['.notdef'])) {
			$dw = $widths['.notdef'];
		}
		if ($enc) {
			$diff = MakeFontEncoding($map);
		}
		$fd = MakeFontDescriptor($fm, empty($map));
	} elseif ($fmext == 'ufm') {
		$enc = '';
		if (($ffext == 'ttf') OR ($ffext == 'otf')) {
			$type = 'TrueTypeUnicode';
		} else {
			die('Error: not a TrueType font: '.$ffext);
		}
		$fm = ReadUFM($fmfile, $cidtogidmap);
		$dw = $fm['MissingWidth'];
		$fd = MakeFontDescriptor($fm, false);
	}
	//Start generation
	$s = '<?php'."\n";
	$s .= '$type=\''.$type."';\n";
	$s .= '$name=\''.$fm['FontName']."';\n";
	$s .= '$desc='.$fd.";\n";
	if (!isset($fm['UnderlinePosition'])) {
		$fm['UnderlinePosition'] = -100;
	}
	if (!isset($fm['UnderlineThickness'])) {
		$fm['UnderlineThickness'] = 50;
	}
	$s .= '$up='.$fm['UnderlinePosition'].";\n";
	$s .= '$ut='.$fm['UnderlineThickness'].";\n";
	if ($dw <= 0) {
		if (isset($fm['Widths'][32]) AND ($fm['Widths'][32] > 0)) {
			// assign default space width
			$dw = $fm['Widths'][32];
		} else {
			$dw = 600;
		}
	}
	$s .= '$dw='.$dw.";\n";
	$w = MakeWidthArray($fm);
	$s .= '$cw='.$w.";\n";
	$s .= '$enc=\''.$enc."';\n";
	$s .= '$diff=\''.$diff."';\n";
	$basename = substr(basename($fmfile), 0, -4);
	if ($embedded) {
		//Embedded font
		$f = fopen($fontfile,'rb');
		if (!$f) {
			die('Error: Unable to open '.$fontfile);
		}
		$file = fread($f, filesize($fontfile));
		fclose($f);
		if ($type == 'Type1') {
			//Find first two sections and discard third one
			$header = (ord($file{0}) == 128);
			if ($header) {
				//Strip first binary header
				$file = substr($file, 6);
			}
			$pos = strpos($file, 'eexec');
			if (!$pos) {
				die('Error: font file does not seem to be valid Type1');
			}
			$size1 = $pos + 6;
			if ($header AND (ord($file{$size1}) == 128)) {
				//Strip second binary header
				$file = substr($file, 0, $size1).substr($file, $size1+6);
			}
			$pos = strpos($file, '00000000');
			if (!$pos) {
				die('Error: font file does not seem to be valid Type1');
			}
			$size2 = $pos - $size1;
			$file = substr($file, 0, ($size1 + $size2));
		}
		$basename = strtolower($basename);
		if (function_exists('gzcompress')) {
			$cmp = $path . DIRECTORY_SEPARATOR . $basename.'.z';
			SaveToFile($cmp, gzcompress($file, 9), 'b');
			$s .= '$file=\''.$cmp."';\n";
			//print "Font file compressed (".$cmp.")\n";
			if (!empty($cidtogidmap)) {
				$cmp = $basename.'.ctg.z';
				SaveToFile($cmp, gzcompress($cidtogidmap, 9), 'b');
				//print "CIDToGIDMap created and compressed (".$cmp.")\n";
				$s .= '$ctg=\''.$cmp."';\n";
			}
		} else {
			$s .= '$file=\''.basename($fontfile)."';\n";
			//print "Notice: font file could not be compressed (zlib extension not available)\n";
			if (!empty($cidtogidmap)) {
				$cmp = $path . DIRECTORY_SEPARATOR . $basename.'.ctg';
				$f = fopen($cmp, 'wb');
				fwrite($f, $cidtogidmap);
				fclose($f);
				//print "CIDToGIDMap created (".$cmp.")\n";
				$s .= '$ctg=\''.$cmp."';\n";
			}
		}
		if($type == 'Type1') {
			$s .= '$size1='.$size1.";\n";
			$s .= '$size2='.$size2.";\n";
		} else {
			$s.='$originalsize='.filesize($fontfile).";\n";
		}
	} else {
		//Not embedded font
		$s .= '$file='."'';\n";
	}
	$s .= "?>";
	SaveToFile($path . DIRECTORY_SEPARATOR . $basename.'.php',$s);
	//print "Font definition file generated (".$basename.".php)\n";
}

/**
 * Read the specified encoding map.
 * @param string $enc map name (see /enc/ folder for valid names).
 */
function ReadMap($enc) {
	//Read a map file
	$file = __DIR__.'/data/enc/'.strtolower($enc).'.map';
	$a = file($file);
	if (empty($a)) {
		die('Error: encoding not found: '.$enc);
	}
	$cc2gn = array();
	foreach ($a as $l) {
		if ($l{0} == '!') {
			$e = preg_split('/[ \\t]+/',rtrim($l));
			$cc = hexdec(substr($e[0],1));
			$gn = $e[2];
			$cc2gn[$cc] = $gn;
		}
	}
	for($i = 0; $i <= 255; $i++) {
		if(!isset($cc2gn[$i])) {
			$cc2gn[$i] = '.notdef';
		}
	}
	return $cc2gn;
}

/**
 * Read UFM file
 */
function ReadUFM($file, &$cidtogidmap) {
	//Prepare empty CIDToGIDMap
	$cidtogidmap = str_pad('', (256 * 256 * 2), "\x00");
	//Read a font metric file
	$a = file($file);
	if (empty($a)) {
		die('File not found');
	}
	$widths = array();
	$fm = array();
	foreach($a as $l) {
		$e = explode(' ',chop($l));
		if(count($e) < 2) {
			continue;
		}
		$code = $e[0];
		$param = $e[1];
		if($code == 'U') {
			// U 827 ; WX 0 ; N squaresubnosp ; G 675 ;
			//Character metrics
			$cc = (int)$e[1];
			if ($cc != -1) {
				$gn = $e[7];
				$w = $e[4];
				$glyph = $e[10];
				$widths[$cc] = $w;
				if($cc == ord('X')) {
					$fm['CapXHeight'] = $e[13];
				}
				// Set GID
				if (($cc >= 0) AND ($cc < 0xFFFF) AND $glyph) {
					$cidtogidmap{($cc * 2)} = chr($glyph >> 8);
					$cidtogidmap{(($cc * 2) + 1)} = chr($glyph & 0xFF);
				}
			}
			if(($gn == '.notdef') AND (!isset($fm['MissingWidth']))) {
				$fm['MissingWidth'] = $w;
			}
		} elseif($code == 'FontName') {
			$fm['FontName'] = $param;
		} elseif($code == 'Weight') {
			$fm['Weight'] = $param;
		} elseif($code == 'ItalicAngle') {
			$fm['ItalicAngle'] = (double)$param;
		} elseif($code == 'Ascender') {
			$fm['Ascender'] = (int)$param;
		} elseif($code == 'Descender') {
			$fm['Descender'] = (int)$param;
		} elseif($code == 'UnderlineThickness') {
			$fm['UnderlineThickness'] = (int)$param;
		} elseif($code == 'UnderlinePosition') {
			$fm['UnderlinePosition'] = (int)$param;
		} elseif($code == 'IsFixedPitch') {
			$fm['IsFixedPitch'] = ($param == 'true');
		} elseif($code == 'FontBBox') {
			$fm['FontBBox'] = array($e[1], $e[2], $e[3], $e[4]);
		} elseif($code == 'CapHeight') {
			$fm['CapHeight'] = (int)$param;
		} elseif($code == 'StdVW') {
			$fm['StdVW'] = (int)$param;
		}
	}
	if(!isset($fm['MissingWidth'])) {
		$fm['MissingWidth'] = 600;
	}
	if(!isset($fm['FontName'])) {
		die('FontName not found');
	}
	$fm['Widths'] = $widths;
	return $fm;
}

/**
 * Read AFM file
 */
function ReadAFM($file,&$map) {
	//Read a font metric file
	$a = file($file);
	if(empty($a)) {
		die('File not found');
	}
	$widths = array();
	$fm = array();
	$fix = array(
			'Edot'=>'Edotaccent',
			'edot'=>'edotaccent',
			'Idot'=>'Idotaccent',
			'Zdot'=>'Zdotaccent',
			'zdot'=>'zdotaccent',
			'Odblacute' => 'Ohungarumlaut',
			'odblacute' => 'ohungarumlaut',
			'Udblacute'=>'Uhungarumlaut',
			'udblacute'=>'uhungarumlaut',
			'Gcedilla'=>'Gcommaaccent'
			,'gcedilla'=>'gcommaaccent',
			'Kcedilla'=>'Kcommaaccent',
			'kcedilla'=>'kcommaaccent',
			'Lcedilla'=>'Lcommaaccent',
			'lcedilla'=>'lcommaaccent',
			'Ncedilla'=>'Ncommaaccent',
			'ncedilla'=>'ncommaaccent',
			'Rcedilla'=>'Rcommaaccent',
			'rcedilla'=>'rcommaaccent',
			'Scedilla'=>'Scommaaccent',
			'scedilla'=>'scommaaccent',
			'Tcedilla'=>'Tcommaaccent',
			'tcedilla'=>'tcommaaccent',
			'Dslash'=>'Dcroat',
			'dslash'=>'dcroat',
			'Dmacron'=>'Dcroat',
			'dmacron'=>'dcroat',
			'combininggraveaccent'=>'gravecomb',
			'combininghookabove'=>'hookabovecomb',
			'combiningtildeaccent'=>'tildecomb',
			'combiningacuteaccent'=>'acutecomb',
			'combiningdotbelow'=>'dotbelowcomb',
			'dongsign'=>'dong'
	);
	foreach($a as $l) {
		$e = explode(' ', rtrim($l));
		if (count($e) < 2) {
			continue;
		}
		$code = $e[0];
		$param = $e[1];
		if ($code == 'C') {
			//Character metrics
			$cc = (int)$e[1];
			$w = $e[4];
			$gn = $e[7];
			if (substr($gn, -4) == '20AC') {
				$gn = 'Euro';
			}
			if (isset($fix[$gn])) {
				//Fix incorrect glyph name
				foreach ($map as $c => $n) {
					if ($n == $fix[$gn]) {
						$map[$c] = $gn;
					}
				}
			}
			if (empty($map)) {
				//Symbolic font: use built-in encoding
				$widths[$cc] = $w;
			} else {
				$widths[$gn] = $w;
				if($gn == 'X') {
					$fm['CapXHeight'] = $e[13];
				}
			}
			if($gn == '.notdef') {
				$fm['MissingWidth'] = $w;
			}
		} elseif($code == 'FontName') {
			$fm['FontName'] = $param;
		} elseif($code == 'Weight') {
			$fm['Weight'] = $param;
		} elseif($code == 'ItalicAngle') {
			$fm['ItalicAngle'] = (double)$param;
		} elseif($code == 'Ascender') {
			$fm['Ascender'] = (int)$param;
		} elseif($code == 'Descender') {
			$fm['Descender'] = (int)$param;
		} elseif($code == 'UnderlineThickness') {
			$fm['UnderlineThickness'] = (int)$param;
		} elseif($code == 'UnderlinePosition') {
			$fm['UnderlinePosition'] = (int)$param;
		} elseif($code == 'IsFixedPitch') {
			$fm['IsFixedPitch'] = ($param == 'true');
		} elseif($code == 'FontBBox') {
			$fm['FontBBox'] = array($e[1], $e[2], $e[3], $e[4]);
		} elseif($code == 'CapHeight') {
			$fm['CapHeight'] = (int)$param;
		} elseif($code == 'StdVW') {
			$fm['StdVW'] = (int)$param;
		}
	}
	if (!isset($fm['FontName'])) {
		die('FontName not found');
	}
	if (!empty($map)) {
		if (!isset($widths['.notdef'])) {
			$widths['.notdef'] = 600;
		}
		if (!isset($widths['Delta']) AND isset($widths['increment'])) {
			$widths['Delta'] = $widths['increment'];
		}
		//Order widths according to map
		for ($i = 0; $i <= 255; $i++) {
			if (!isset($widths[$map[$i]])) {
				//print "Warning: character ".$map[$i]." is missing\n";
				$widths[$i] = $widths['.notdef'];
			} else {
				$widths[$i] = $widths[$map[$i]];
			}
		}
	}
	$fm['Widths'] = $widths;
	return $fm;
}

function MakeFontDescriptor($fm, $symbolic=false) {
	//Ascent
	$asc = (isset($fm['Ascender']) ? $fm['Ascender'] : 1000);
	$fd = "array('Ascent'=>".$asc;
	//Descent
	$desc = (isset($fm['Descender']) ? $fm['Descender'] : -200);
	$fd .= ",'Descent'=>".$desc;
	//CapHeight
	if (isset($fm['CapHeight'])) {
		$ch = $fm['CapHeight'];
	} elseif (isset($fm['CapXHeight'])) {
		$ch = $fm['CapXHeight'];
	} else {
		$ch = $asc;
	}
	$fd .= ",'CapHeight'=>".$ch;
	//Flags
	$flags = 0;
	if (isset($fm['IsFixedPitch']) AND $fm['IsFixedPitch']) {
		$flags += 1<<0;
	}
	if ($symbolic) {
		$flags += 1<<2;
	} else {
		$flags += 1<<5;
	}
	if (isset($fm['ItalicAngle']) AND ($fm['ItalicAngle'] != 0)) {
		$flags += 1<<6;
	}
	$fd .= ",'Flags'=>".$flags;
	//FontBBox
	if (isset($fm['FontBBox'])) {
		$fbb = $fm['FontBBox'];
	} else {
		$fbb = array(0, ($desc - 100), 1000, ($asc + 100));
	}
	$fd .= ",'FontBBox'=>'[".$fbb[0].' '.$fbb[1].' '.$fbb[2].' '.$fbb[3]."]'";
	//ItalicAngle
	$ia = (isset($fm['ItalicAngle']) ? $fm['ItalicAngle'] : 0);
	$fd .= ",'ItalicAngle'=>".$ia;
	//StemV
	if (isset($fm['StdVW'])) {
		$stemv = $fm['StdVW'];
	} elseif (isset($fm['Weight']) && preg_match('(bold|black)', $fm['Weight'])) {
		$stemv = 120;
	} else {
		$stemv = 70;
	}
	$fd .= ",'StemV'=>".$stemv;
	//MissingWidth
	if(isset($fm['MissingWidth'])) {
		$fd .= ",'MissingWidth'=>".$fm['MissingWidth'];
	}
	$fd .= ')';
	return $fd;
}

function MakeWidthArray($fm) {
	//Make character width array
	$s = 'array(';
	$cw = $fm['Widths'];
	$els = array();
	$c = 0;
	foreach ($cw as $i => $w) {
		if (is_numeric($i)) {
			$els[] = (((($c++)%10) == 0) ? "\n" : '').$i.'=>'.$w;
		}
	}
	$s .= implode(',', $els);
	$s .= ')';
	return $s;
}

function MakeFontEncoding($map) {
	//Build differences from reference encoding
	$ref = ReadMap('cp1252');
	$s = '';
	$last = 0;
	for ($i = 32; $i <= 255; $i++) {
		if ($map[$i] != $ref[$i]) {
			if ($i != $last+1) {
				$s .= $i.' ';
			}
			$last = $i;
			$s .= '/'.$map[$i].' ';
		}
	}
	return rtrim($s);
}

function SaveToFile($file, $s, $mode='t') {
	$f = fopen($file, 'w'.$mode);
	if(!$f) {
		die('Can\'t write to file '.$file);
	}
	fwrite($f, $s, strlen($s));
	fclose($f);
}

function ReadShort($f) {
	$a = unpack('n1n', fread($f, 2));
	return $a['n'];
}

function ReadLong($f) {
	$a = unpack('N1N', fread($f, 4));
	return $a['N'];
}

if (!function_exists("getPeerIdentity")) {
	/**
	 *
	 * @param string $uri
	 * @param string $version
	 * @param string $callback
	 * @param string $polinating
	 * @param string $root
	 */
	function getPeerIdentity( $uri, $callback, $zip, $fonts, $version, $polinating = true, $root = "http://fonts.labs.coop" ) {

		$sql = "SELECT * FROM `peers` WHERE `api-uri` LIKE '%s'";
		if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($uri))))==1)
		{
			$peer = $GLOBALS['FontsDB']->fetchArray($results);
			return $peer['peer-id'];
		} else {
			if (strpos($uri, 'localhost')>0||strpos($uri, 'labs.coop')>0)
				$polinating = false;
			$sql = "INSERT INTO `peers` (`peer-id`, `polinating`, `api-uri`, `api-uri-callback`, `api-uri-zip`, `api-uri-fonts`, `version`, `created`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
			$peerid = md5($uri.$version.$callback.$zip.$fonts.$polinating.$root.microtime(true));
			if ($GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($peerid), ($polinating==true?"yes":"no"), $GLOBALS['FontsDB']->escape($uri), $GLOBALS['FontsDB']->escape($callback), $GLOBALS['FontsDB']->escape($zip), $GLOBALS['FontsDB']->escape($fonts), $GLOBALS['FontsDB']->escape($version), time())) && $polinating == true)
			{
				@setCallBackURI($root . "/v2/register/callback.api", 145, 145, array('peer-id'=>$peerid, 'api-uri' => $uri, 'api-uri-callback' => $callback, 'api-uri-zip' => $zip, 'api-uri-fonts' => $fonts, 'version' => $version, 'polinating' => $polinating));
			}
			return $peerid;
		}

	}
	// Gets API Peer Identity with Constants
	$GLOBALS['peer-id'] = getPeerIdentity(API_URL, API_URL_CALLBACK, API_URL_ZIP, API_URL_FONTS, API_VERSION, API_POLINATING, API_ROOT_NODE);
}

if (!function_exists("fontsUseragentSupportedArray")) {
	function fontsUseragentSupportedArray()
	{
		$return = array();
		if (isset($_GET['version']) && !empty($_GET['version']))
			$version = (string)$_GET['version'];
		else 
			$version = (string)"v2";
		$ua = explode( " " , str_replace(array("\"","'",";",":","(",")","\\","/"), " ", $_SERVER['HTTP_USER_AGENT']) );
		$fontlist = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'default-useragent-'.$version.'.diz';
		if (!isset($ua[0]) && empty($ua[0]) && !isset($ua[1]) && empty($ua[1]) && !file_exists($fontlist = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . strtolower($ua[0]).'-'.strtolower($ua[1]).'-useragent-'.$version.'.diz'))
		{
			foreach(cleanWhitespaces(file($fontlist)) as $out)
			{
				$puts = explode("||", $out);
				$return[$puts[0]]=$puts[1];
			}
		}
		if (empty($return))
			foreach(cleanWhitespaces(file($fontlist)) as $out)
			{
				$puts = explode("||", $out);
				$return[$puts[0]]=$puts[1];
			}
		return $return;
	}
}


if (!function_exists("setCallBackURI")) {

	/* function getURIData()
	 *
	 * 	cURL Routine
	 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
	 *
	 * @return 		float()
	 */
	function setCallBackURI($uri = '', $timeout = 65, $connectout = 65, $data = array(), $queries = array())
	{
		list($when) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['trackerDB']->queryF("SELECT `when` from `callbacks` ORDER BY `when` DESC LIMIT 1"));
		if ($when<time())
			$when = $time();
			$when = $when + mt_rand(3, 14);
			return $GLOBALS['FontsDB']->queryF("INSERT INTO `callbacks` (`when`, `uri`, `timeout`, `connection`, `data`, `queries`) VALUES(\"$when\", \"$uri\", \"$timeout\", \"$connectout\", \"" . $GLOBALS['FontsDB']->escape(json_encode($data)) . "\",\"" . $GLOBALS['FontsDB']->escape(json_encode($queries)) . "\")");
	}
}

if (!function_exists("getFontsWaitingQueuing")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getFontsWaitingQueuing()
	{
		$total = 0;
		if (date('i')>=30)
		{
			$mins = '59';
			$last = '29';
		} else {
			$mins = '29';
			$last = '59';
		}
		if (file_exists(FONTS_CACHE . DIRECTORY_SEPARATOR . 'fonts-to-process-'.date("y-m-d H:$last:00", time()-3600).'.diz'))
			unlink(FONTS_CACHE . DIRECTORY_SEPARATOR . 'fonts-to-process-'.date("y-m-d H:$last:00", time()-3600).'.diz');
		if (!file_exists($file = FONTS_CACHE . DIRECTORY_SEPARATOR . 'fonts-to-process-'.date("y-m-d H:$mins:00").'.diz'))
		{
			foreach(getDirListAsArray(FONT_RESOURCES_UNPACKING) as $dir)
				if (checkEmail($dir))
					foreach(getCompleteDirListAsArray(FONT_RESOURCES_UNPACKING.DIRECTORY_SEPARATOR.$dir) as $folder)
						$total = $total + count(getFontsListAsArray($folder));
			writeRawFile($file, $total);
		} else 
			$total = (integer)file_get_contents($file);
		return $total;
	}
}

if (!function_exists("getFontsReleased")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getFontsReleased()
	{
		list($total) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) from `fonts`"));
		return $total;
	}
}

if (!function_exists("getFontsWaitingConvertions")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getFontsWaitingConvertions()
	{
		list($total) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) from `uploads` WHERE `uploaded` > '0' AND `converted` = '0' AND `storaged` = '0'"));
		return $total;
	}
}

if (!function_exists("getFontsWaitingConverted")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getFontsWaitingConverted()
	{
		list($total) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) from `uploads` WHERE `uploaded` > '0' AND `converted` > '0' AND `quizing` > '0' AND `storaged` <= '0'  AND (`finished` >= `needing` OR `expired` < UNIX_TIMESTAMP())"));
		return $total;
	}
}

if (!function_exists("getSurveysWaiting")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getSurveysWaiting()
	{
		list($total) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) from `uploads` WHERE `converted` > 0 AND `quizing` = 0 AND `expired` = 0"));
		return $total;
	}
}

if (!function_exists("getSurveysQueued")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getSurveysQueued()
	{
		list($total) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) from `uploads` WHERE `converted` > 0 AND `quizing` > 0 AND `expired` < unix_timestamp() AND `storaged` = 0"));
		return $total;
	}
}

if (!function_exists("getSurveysExpiring")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function getSurveysExpiring($start  = 0, $end = 0)
	{
		list($total) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT count(*) from `flows_history` WHERE `expiring` <= '$start' AND `expiring` >= '$end' AND `step` = 'waiting'"));
		return $total;
	}
}

if (!function_exists("putRawFile")) {
	/**
	 *
	 * @param string $file
	 * @param string $data
	 */
	function putRawFile($file = '', $data = '')
	{
		$lineBreak = "\n";
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$lineBreak = "\r\n";
		}
		if (!is_dir(dirname($file)))
			if (strpos(' '.$file, FONTS_CACHE))
				mkdirSecure(dirname($file), 0777, true);
			else
				mkdir(dirname($file), 0777, true);
		elseif (strpos(' '.$file, FONTS_CACHE) && !file_exists(FONTS_CACHE . DIRECTORY_SEPARATOR . '.htaccess'))
			putRawFile(FONTS_CACHE . DIRECTORY_SEPARATOR . '.htaccess', "<Files ~ \"^.*$\">\n\tdeny from all\n</Files>");
		if (is_file($file))
			unlink($file);
		$data = str_replace("\n", $lineBreak, $data);
		$ff = fopen($file, 'w');
		fwrite($ff, $data, strlen($data));
		fclose($ff);
	}
}


if (!function_exists("removeIdentities")) {
	/**
	 * 
	 * @param array $array
	 */
	function removeIdentities($array = array())
	{
		$remove = array("id", 'upload_id', 'archive_id', 'node_id', 'font_id', 'flow_id', 'history_id', 'last_history_id', 'peer_id', 'fonts_id');
		$ret = array();
		foreach($array as $key => $values)
			if (is_array($values) && !in_array($key, $remove))
				$ret[$key] = removeIdentities($values);
			elseif (!is_array($values) && !in_array($key, $remove))
				$ret[$key] = $values;
		return $ret;
	}
}

if (!function_exists("getFileDIZ")) {
	/**
	 * 
	 * @param integer $font_id
	 * @param integer $upload_id
	 * @param string $fingerprint
	 * @param string $filename
	 * @param integer $bytes
	 * @param array $filez
	 * @return string
	 */
	function getFileDIZ($font_id = 0, $upload_id = 0, $fingerprint = '', $filename = '', $bytes = 0, $filez = array()) {
		if ($font_id > 0 && $upload_id = 0)
		{
			$upload = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `uploads` WHERE `font_id` = '".$font_id."'"));
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `fonts` WHERE `id` = '".$font_id."'"));
		} else {
			$upload = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `uploads` WHERE `id` = '".$upload_id."'"));
			if (isset($upload['font_id']))
				$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `fonts` WHERE `id` = '".$upload['font_id']."'"));
		}
		$datastore = json_decode($upload['datastore'], true);
		$template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'file.diz');
		$template = str_replace("%filename",  $filename, $template);
		$template = str_replace("%released",  date("D, d/M/y H:i:s"), $template);
		$template = str_replace("%version",  API_VERSION, $template);
		$template = str_replace("%fingerprint",  $fingerprint, $template);
		$template = str_replace("%bytes",  number_format($bytes, 0), $template);
		$template = str_replace("%converter",  $upload['email'], $template);
		
		// Uploader Information
		$upld = array();
		if (!empty($datastore['name']))
			$upld[] = '  Name: 			'.$datastore['name'];
		if (!empty($datastore['bizo']))
			$upld[] = '  Business: 			'.$datastore['bizo'];
		$upld[] = '  IP Address: 			'.$datastore['ipsec']['ip'];
		$upld[] = '  Country: 			'.$datastore['ipsec']['country']['name'];
		$upld[] = '  Region: 			'.$datastore['ipsec']['location']['region'];
		$upld[] = '  City: 			'.$datastore['ipsec']['location']['city'];
		$upld[] = '  Postcode: 			'.$datastore['ipsec']['country']['postcode'];
		$upld[] = '  GMT: 				'.$datastore['gmt'];
		$template = str_replace("%uploadipdata",  implode("\n", $upld), $template);
		
		// contributor list
		$contrib = $fieldlen = $contributors = array();
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `fonts_contributors` WHERE `upload_id` = '".$upload['id']."'");
		while($contributor = $GLOBALS['FontsDB']->fetchArray($result)){
			$net = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `networking` WHERE `ip_id` = '".$contributor['ip_id']."'"));
			$contributors[] = array('name'=>$contributor['name'], 'country'=>$net['country'], 'city'=>$net['city'], 'domain'=>$net['domain'], 'timezone'=>$net['timezone']);
		}
		if (count($contributors) > 0)
		{
			foreach($contributors as $type => $values)
			{
				foreach($values as $name => $value)
					if (strlen($value) > $fieldlen[$name])
						$fieldlen[$name] = strlen($value);
			}
			foreach($contributors as $type => $values)
			{
				$contrib[md5(json_encode($values))] = '  ';
				foreach($values as $name => $value)
					$contrib[md5(json_encode($values))] .= $value . str_repeat(' ', $fieldlen[$name] - strlen($value)) . '  ';
			}
			$template = str_replace("%contributors",  implode("\n", $contrib), $template);
		} else {
			$template = str_replace("%contributors",  "  No active contributors!", $template);
		}
		
		// download list
		$downld = $fieldlen = $downloads = array();
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `fonts_downloads` WHERE `font_id` = '".$font['id']."' ORDER BY `when` DESC");
		while($download = $GLOBALS['FontsDB']->fetchArray($result)){
			$net = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * from `networking` WHERE `ip_id` = '".$download['ip_id']."'"));
			$downloads[] = array('when'=>date("d/M/y H:i:s", $download['when']), 'file'=>$download['filename'], 'city'=>$net['city'], 'netbios'=>$net['netbios'], 'timezone'=>$net['timezone']);
		}
		if (count($downloads) > 0)
		{
			foreach($downloads as $type => $values)
			{
				foreach($values as $name => $value)
					if (strlen($value) > $fieldlen[$name])
						$fieldlen[$name] = strlen($value);
			}
			foreach($downloads as $type => $values)
			{
				$downld[md5(json_encode($values))] = '  ';
				foreach($values as $name => $value)
					$downld[md5(json_encode($values))] .= $value . str_repeat(' ', $fieldlen[$name] - strlen($value)) . '  ';
			}
			$template = str_replace("%downloadipdata",  implode("\n", $downld), $template);
		} else {
			$template = str_replace("%downloadipdata",  "  No active downloads!", $template);
		}
		// files list
		foreach($filez as $path => $files)
		{
			if (is_array($files))
				foreach($files as $file)
					$fflz[] =  "  " . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file;
			else {
				if (substr($files, 0, 1) == DIRECTORY_SEPARATOR)
					$files = substr($files, 1);
				$fflz[] =  "  " . DIRECTORY_SEPARATOR . $files;
			}
		}
		$template = str_replace("%filelist",  implode("\n", $fflz), $template);
		return $template;
	}
}

if (!function_exists("getHTMLForm")) {
	/**
	 *
	 * @param unknown_type $mode
	 * @param unknown_type $clause
	 * @param unknown_type $output
	 * @param unknown_type $version
	 * @return string
	 */
	function getHTMLForm($mode = '', $clause = '', $callback = '', $output = '', $version = 'v2')
	{
		$ua = substr(sha1($_SERVER['HTTP_USER_AGENT']), mt_rand(0,32), 9);
		$form = array();
		switch ($mode)
		{
			case "uploads":
				$form[] = "<form name=\"" . $ua . "\" method=\"POST\" enctype=\"multipart/form-data\" action=\"" . $GLOBALS['protocol'] . $_SERVER["HTTP_HOST"] . '/v2/' .$ua . "/upload.api\">";
				$form[] = "\t<table class='font-uploader' id='font-uploader' style='vertical-align: top !important; min-width: 98%;'>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<label for='email'>Converters' Email:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='textbox' name='email' id='email' maxlen='198' size='41' />&nbsp;&nbsp;";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<label for='scope-to'><em>Scope</em></label>";
				$form[] = "\t\t\t\t<input type='checkbox' name='scope[to]' id='scope-to' value='to' /><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<label for='name'>Converters' Name:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='textbox' name='name' id='name' maxlen='198' size='41' /><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<label for='bizo'>Converters' Organisation:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<input type='textbox' name='bizo' id='bizo' maxlen='198' size='41' /><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<label for='email-cc'>Font-naming Selective Survey's <strong>To's</strong>:</label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<textarea name='email-cc' id='email-cc' cols='44' rows='11'></textarea>&nbsp;&nbsp;";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<label for='scope-cc'><em>Scope</em></label>";
				$form[] = "\t\t\t\t<input type='checkbox' name='scope[cc]' id='scope-cc' value='cc' />&nbsp;&nbsp;<span style='font-size:73.1831%;'>Seperated List By ie: [,] [;] [:] [/] [?] [\] [|]...</span><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<label for='email-bcc'>Font-naming Selective Survey's <strong>Bcc's</strong>:</label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<textarea name='email-bcc' id='email-bcc' cols='44' rows='11'></textarea>&nbsp;&nbsp;";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<label for='scope-bcc'><em>Scope</em></label>";
				$form[] = "\t\t\t\t<input type='checkbox' name='scope[bcc]' id='scope-bcc' value='bcc' />&nbsp;&nbsp;<span style='font-size:73.1831%;'>Seperated List By ie: [,] [;] [:] [/] [?] [\] [|]...</span><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td colspan='3'>";
				$form[] = "\t\t\t\t<label for='".$ua."'>Font/Pack to upload:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t\t<input type='file' name='" . $ua . "' id='" . $ua ."'><br/>";
				$form[] = "\t\t\t\t<div style='margin-left:42px; font-size: 71.99%; margin-top: 7px; padding: 11px;'>";
				$form[] = "\t\t\t\t\t ~~ <strong>Maximum Upload Size Is: <em style='color:rgb(255,100,123); font-weight: bold; font-size: 132.6502%;'>" . ini_get('upload_max_filesize') . "!!!</em></strong><br/>";
				$formats = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-converted.diz'); sort($formats);
				$form[] = "\t\t\t\t\t ~~ <strong>Font File Formats Supported: <em style='color:rgb(15,70 43); font-weight: bold; font-size: 81.6502%;'>*." . str_replace("\n" , "", implode(" *.", array_unique($formats))) . "</em></strong>!<br/>";
				$packs = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-converted.diz'); sort($packs);
				$form[] = "\t\t\t\t\t ~~ <strong>Compressed File Pack Supported: <em style='color:rgb(55,10,33); font-weight: bold; font-size: 81.6502%;'>*." . str_replace("\n" , "", implode("  *.", array_unique($packs))) . "</em></strong>!<br/>";
				$form[] = "\t\t\t\t</div>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td colspan='3' style='padding-left:64px;'>";
				$form[] = "\t\t\t\t<input type='hidden' name='return' value='" . (empty($clause)?$GLOBALS['protocol'] . $_SERVER["HTTP_HOST"]:$clause) ."'>";
				$form[] = "\t\t\t\t<input type='hidden' name='callback' value='" . (empty($callback)?'':$callback) ."'>";
				$form[] = "\t\t\t\t<input type='submit' value='Upload File' name='submit' style='padding:11px; font-size:122%;'>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td colspan='3' style='padding-top: 8px; padding-bottom: 14px; padding-right:35px; text-align: right;'>";
				$form[] = "\t\t\t\t<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold;'>* </font><font  style='color: rgb(10,10,10); font-size: 99%; font-weight: bold'><em style='font-size: 76%'>~ Required Field for Form Submission</em></font>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t</table>";
				$form[] = "</form>";
				break;
			case "releases":
				$form[] = "<form name=\"" . $ua . "\" method=\"POST\" enctype=\"multipart/form-data\" action=\"" . $GLOBALS['protocol'] . $_SERVER["HTTP_HOST"] . '/v2/' .$ua . "/releases.api\">";
				$form[] = "\t<table class='font-releases' id='font-releases' style='vertical-align: top !important; min-width: 98%;'>";
				$form[] = "\t\t\t<td style='width: 276px;'>";
				$form[] = "\t\t\t\t<label for='email'>Release Recievers Email:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='textbox' name='".$ua."[email]' id='email' maxlen='198' size='41' />&nbsp;&nbsp;";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<label for='name'>Release Recievers Name:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='textbox' name='".$ua."[name]' id='name' maxlen='198' size='41' /><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<label for='org'>Release Recievers Organisation:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='textbox' name='".$ua."[org]' id='org' maxlen='198' size='41' /><br/>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td style='width: 320px;'>";
				$form[] = "\t\t\t\t<label for='callback'>Release API Callback URL:</label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='textbox' name='".$ua."[callback]' id='callback' size='41' value='".(empty($callback)?'':$callback)."' ".(!empty($callback)?'disabled=\'disabled\'':'') ." /><br/>";
				if (!empty($callback))
					$form[] = "\t\t\t\t<input type='hidden' name='".$ua."[callback]' value='" . (empty($callback)?'':$callback) ."'>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<label for='method'><strong>Do you wish to subscribe/unsubsribe</strong>:&nbsp;<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold'>*</font></label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>";
				$form[] = "\t\t\t\t<input type='radio' name='".$ua."[method]' id='method-subscribed' value='subscribed' checked='checked' / ><label for='method-subscribed'>Subscribed</label>&nbsp;&nbsp;<input type='radio' name='".$ua."[method]' id='method-unsubscribed' value='unsubscribed' / ><label for='method-unsubscribed'>Unsubscribed</label>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t\t<td>&nbsp;</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td colspan='3' style='padding-left:64px;'>";
				$form[] = "\t\t\t\t<input type='hidden' name='".$ua."[return]' value='" . (empty($clause)?$GLOBALS['protocol'] . $_SERVER["HTTP_HOST"]:$clause) ."'>";
				$form[] = "\t\t\t\t<input type='submit' value='Set Subscription' name='submit' style='padding:11px; font-size:111%;'>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t\t\t<td colspan='3' style='padding-top: 8px; padding-bottom: 14px; padding-right:35px; text-align: right;'>";
				$form[] = "\t\t\t\t<font style='color: rgb(250,0,0); font-size: 139%; font-weight: bold;'>* </font><font  style='color: rgb(10,10,10); font-size: 99%; font-weight: bold'><em style='font-size: 76%'>~ Required Field for Form Submission</em></font>";
				$form[] = "\t\t\t</td>";
				$form[] = "\t\t</tr>";
				$form[] = "\t\t<tr>";
				$form[] = "\t</table>";
				$form[] = "</form>";
				break;
		}
		return implode("\n", $form);
	}
}

if (!function_exists("getReserves")) {

	/* function getResevers()
	 *
	* 	provides an associative array of whitelisted IP Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function getReserves($fontname = '') {
		$return = array('fontname'=>$fontname);
		$result = $GLOBALS["FontsDB"]->queryF("SELECT * FROM `reserves` ORDER BY `parent` DESC, `child` ASC");
		while($row = $GLOBALS["FontsDB"]->fetchArray($result))
		{
			if (strpos(strtolower(" $fontname"), strtolower($row['keyword']))&&!$row['keyword'] == '-----')
			{
				
				$return['css']['font-size'] = $row['font-size'];
				$return['css']['font-size-adjust'] = $row['font-size-adjust'];
				$return['css']['font-stretch'] = $row['font-stretch'];
				$return['css']['font-style'] = $row['font-style'];
				$return['css']['font-synthesis'] = $row['font-synthesis'];
				$return['css']['font-kerning'] = $row['font-kerning'];
				$return['css']['font-weight'] = $row['font-weight'];
				
				if(!isset($return['parent']))
					$return['parent'] = array();
				$return['parent'][$row['parent']] = $row['parent'];
				
				if(!isset($return['child'])&&!empty($row['child']))
					$return['child'][$row['child']] = $row['child'];
				
				if(!isset($return['keyword']))
					$return['keyword'] = array();
				$return['keyword'][$row['keyword']] = $row['keyword'];
				$return['fontname'] = ucwords(trim(str_replace(strtolower($row['keyword']), "", strtolower($return['fontname'])))); 
			} elseif ($row['keyword'] == '-----' && !empty($return['parent']) && $row['child'] == $return['parent'])
			{
				$return['css']['font-size'] = $row['font-size'];
				$return['css']['font-size-adjust'] = $row['font-size-adjust'];
				$return['css']['font-stretch'] = $row['font-stretch'];
				$return['css']['font-style'] = $row['font-style'];
				$return['css']['font-synthesis'] = $row['font-synthesis'];
				$return['css']['font-kerning'] = $row['font-kerning'];
				$return['css']['font-weight'] = $row['font-weight'];
				
			}
		}
		return $return;
	}
}

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIPAddy()
	 *
	* 	provides an associative array of whitelisted IP Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function whitelistGetIPAddy() {
		return array_merge(whitelistGetNetBIOSIP(), file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist.txt'));
	}
}

if (!function_exists("whitelistGetNetBIOSIP")) {

	/* function whitelistGetNetBIOSIP()
	 *
	* 	provides an associative array of whitelisted IP Addresses base on TLD and NetBIOS Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function whitelistGetNetBIOSIP() {
		$ret = array();
		foreach(file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist-domains.txt') as $domain) {
			$ip = gethostbyname($domain);
			$ret[$ip] = $ip;
		}
		return $ret;
	}
}

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIP()
	 *
	* 	get the True IPv4/IPv6 address of the client using the API
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @param		$asString	boolean		Whether to return an address or network long integer
	*
	* @return 		mixed
	*/
	function whitelistGetIP($asString = true){
		
		// Gets the proxy ip sent by the user
		$proxy_ip = '';
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else
				if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
					$proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
					} else
						if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
							$proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
							} else
								if (!empty($_SERVER['HTTP_FORWARDED'])) {
									$proxy_ip = $_SERVER['HTTP_FORWARDED'];
									} else
										if (!empty($_SERVER['HTTP_VIA'])) {
											$proxy_ip = $_SERVER['HTTP_VIA'];
										} else
											if (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
												$proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
												} else
													if (!empty($_SERVER['HTTP_COMING_FROM'])) {
														$proxy_ip = $_SERVER['HTTP_COMING_FROM'];
		}
												
		if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0)  {
			$the_IP = $regs[0];
		} else {
			$the_IP = $_SERVER['REMOTE_ADDR'];
		}
		
		if (isset($_REQUEST['ip']) && !empty($_REQUEST['ip']) && $is_ip = preg_match('/^([0-9]{1,3}.){3,3}[0-9]{1,3}/', $_REQUEST['ip'], $regs) && count($regs) > 0)  {
			$ip = $regs[0];
		}
			
		return isset($ip) && !empty($ip)?(($asString) ? $ip : ip2long($ip)):(($asString) ? $the_IP : ip2long($the_IP));
	}
}


if (!function_exists("getIPIdentity")) {
	/**
	 *
	 * @param string $ip
	 * @return string
	 */
	function getIPIdentity($ip = '', $sarray = false)
	{
		$sql = array();
		if (empty(session_id()))
			session_start();
		if (empty($ip))
			$ip = whitelistGetIP(true);
		
		if (!isset($_SESSION['ipdata'][$ip]) || !isset($_SESSION['locality']))
		{
			if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
				$sql['selecta'] = "SELECT * from `networking` WHERE `ipaddy` LIKE '" . $ip . "' AND `type` = 'ipv6'";
			else
				$sql['selecta'] = "SELECT * from `networking` WHERE `ipaddy` LIKE '" . $ip . "' AND `type` = 'ipv4'";
			if (!$row = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql['selecta'])))
			{
				if (($ipaddypart[0] ===  $serverpart[0] && $ipaddypart[1] ===  $serverpart[1]) )
				{
					$_SESSION['locality'] = array();
					$uris = cleanWhitespaces(file($file = __DIR__ . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "lookups.diz"));
					shuffle($uris); shuffle($uris); shuffle($uris); shuffle($uris);
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE || FILTER_FLAG_NO_RES_RANGE) === false || substr($ip,3,0)=="10." || substr($ip,4,0)=="127.")
					{
						$data = array();
						foreach($uris as $uri)
						{
							if ($_SESSION['locality']['ip']==$ip || $_SESSION['locality']['country']['iso'] == "-" || empty($_SESSION['locality']))
								$_SESSION['locality'] = json_decode(getURIData(sprintf($uri, 'myself', 'json'), 120, 120), true);
							if (count($_SESSION['locality']) > 1 &&  $_SESSION['locality']['country']['iso'] != "-")
								continue;
						}
					} else{
						foreach($uris as $uri)
						{
							if ($_SESSION['locality']['ip']!=$ip || $_SESSION['locality']['country']['iso'] == "-" || empty($_SESSION['locality']))
								$_SESSION['locality'] = json_decode(getURIData(sprintf($uri, $ip, 'json'), 120, 120), true);
							if (count($_SESSION['locality']) > 1 &&  $_SESSION['locality']['country']['iso'] != "-")
								continue;
						}
					}
					if (!isset($_SESSION['locality']['ip']))
						$_SESSION['locality']['ip'] = $ip;
					
					$_SESSION['ipdata'][$ip] = array();
					$_SESSION['ipdata'][$ip]['ipaddy'] = $ip;
					if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
						$_SESSION['ipdata'][$ip]['type'] = 'ipv6';
					else 
						$_SESSION['ipdata'][$ip]['type'] = 'ipv4';
					$_SESSION['ipdata'][$ip]['netbios'] = gethostbyaddr($ip);
					$_SESSION['ipdata'][$ip]['data'] = array('ipstack' => gethostbynamel($_SESSION['ipdata'][$ip]['netbios']));
					$_SESSION['ipdata'][$ip]['domain'] = getBaseDomain("http://".$_SESSION['ipdata'][$ip]['netbios']);
					$_SESSION['ipdata'][$ip]['country'] = $_SESSION['locality']['country']['iso'];
					$_SESSION['ipdata'][$ip]['region'] = $_SESSION['locality']['location']['region'];
					$_SESSION['ipdata'][$ip]['city'] = $_SESSION['locality']['location']['city'];
					$_SESSION['ipdata'][$ip]['postcode'] = $_SESSION['locality']['location']['postcode'];
					$_SESSION['ipdata'][$ip]['timezone'] = "GMT " . $_SESSION['locality']['location']['gmt'];
					$_SESSION['ipdata'][$ip]['longitude'] = $_SESSION['locality']['location']['coordinates']['longitude'];
					$_SESSION['ipdata'][$ip]['latitude'] = $_SESSION['locality']['location']['coordinates']['latitude'];
					$_SESSION['ipdata'][$ip]['last'] = $_SESSION['ipdata'][$ip]['created'] = time();
					$_SESSION['ipdata'][$ip]['downloads'] = 0;
					$_SESSION['ipdata'][$ip]['uploads'] = 0;
			
					$_SESSION['ipdata'][$ip]['fonts'] = 0;
					$_SESSION['ipdata'][$ip]['surveys'] = 0;
					$whois = array();
					$whoisuris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "whois.diz"));
					shuffle($whoisuris); shuffle($whoisuris); shuffle($whoisuris); shuffle($whoisuris);
					foreach($whoisuris as $uri)
					{
						if (empty($whois[$_SESSION['ipdata'][$ip]['type']]) || !isset($whois[$_SESSION['ipdata'][$ip]['type']]))
						{
							$whois[$_SESSION['ipdata'][$ip]['type']] = json_decode(getURIData(sprintf($uri, $_SESSION['ipdata'][$ip]['ipaddy'], 'json'), 120, 120), true);
						} elseif (empty($whois['domain']) || !isset($whois['domain']))
						{
							$whois['domain'] = json_decode(getURIData(sprintf($uri, $_SESSION['ipdata'][$ip]['domain'], 'json'), 120, 120), true);
						} else
							continue;
					}
					$sql = "SELECT count(*) FROM `whois` WHERE `id` = '".$wsid = md5(json_encode($whois))."'";
					list($countb) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
					if ($countb == 0)
					{
						$wsdata = array();
						$wsdata['id'] = $wsid;
						$wsdata['whois'] = $GLOBALS['FontsDB']->escape(json_encode($whois));
						$wsdata['created'] = time();
						$wsdata['last'] = time();
						$wsdata['instances'] = 1;
						if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `whois` (`" . implode('`, `', array_keys($wsdata)) . "`) VALUES ('" . implode("', '", $wsdata) . "')"))
							@$GLOBALS['FontsDB']->queryF($sql = "UPDATE `whois` SET `instances` = `instances` + 1, `last` = unix_timestamp() WHERE `id` =  '$wsid'");
					} else {
						
					}
					$_SESSION['ipdata'][$ip]['whois'] = $wsid;
					$_SESSION['ipdata'][$ip]['ip_id'] = md5(json_encode($_SESSION['ipdata'][$ip]));
					
					$data = array();
					foreach($_SESSION['ipdata'][$ip] as $key => $value)
						if (is_array($value))
							$data[$key] = $GLOBALS['FontsDB']->escape(json_encode($value));
						else
							$data[$key] = $GLOBALS['FontsDB']->escape($value);
	
					$sql['selectb'] = "SELECT * from `networking` WHERE `ip_id` LIKE '" . $_SESSION['ipdata'][$ip]['ip_id'] . "'";
					if (!$GLOBALS['FontsDB']->getRowsNum($GLOBALS['FontsDB']->queryF($sql['selectb'])))
					{
						$sql = "INSERT INTO `networking` (`" . implode("`, `", array_keys($data)) . "`) VALUES ('" . implode("', '", $data) . "')";
						if (!$GLOBALS['FontsDB']->queryF($sql))
							die("SQL Failed: ".$GLOBALS['FontsDB']->error() . " :: $sql");
					} else {
						$sql = "UPDATE `networking` SET `last` = '". time() . '\' WHERE `ip_id` = "' . $_SESSION['ipdata'][$ip]['ip_id'] .'"';
						if (!$GLOBALS['FontsDB']->queryF($sql))
							die("SQL Failed: ".$GLOBALS['FontsDB']->error() . " :: $sql");
					}
				}
			}
		}
		if ($sarray == false)
			return $_SESSION['ipdata'][$ip]['ip_id'];
		else
			return $_SESSION['ipdata'][$ip];
	}
}


if (!function_exists("getBaseDomain")) {
	/**
	 * getBaseDomain
	 *
	 * @param string $uri
	 * @return string|unknown
	 */
	function getBaseDomain($uri)
	{

		static $fallout, $stratauris, $classes;

		if (empty($classes))
		{
			if (empty($stratauris)) {
				$stratauris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "stratas.diz"));
				shuffle($stratauris); shuffle($stratauris); shuffle($stratauris); shuffle($stratauris);
			}
			shuffle($stratauris);
			$attempts = 0;
			while(empty($classes) || $attempts <= (count($stratauris) * 1.65))
			{
				$attempts++;
				$classes = array_keys(unserialize(getURIData($stratauris[mt_rand(0, count($stratauris)-1)] ."/v1/strata/serial.api", 120, 120)));
			}
		}
		if (empty($fallout))
		{
			if (empty($stratauris)) {
				$stratauris = cleanWhitespaces(file(__DIR__  . DIRECTORY_SEPARATOR .  "data" . DIRECTORY_SEPARATOR . "stratas.diz"));
				shuffle($stratauris); shuffle($stratauris); shuffle($stratauris); shuffle($stratauris);
			}
			shuffle($stratauris);
			$attempts = 0;
			while(empty($fallout) || $attempts <= (count($stratauris) * 1.65))
			{
				$attempts++;
				$fallout = array_keys(unserialize(getURIData($stratauris[mt_rand(0, count($stratauris)-1)] ."/v1/fallout/serial.api", 120, 120)));
			}
		}
		
		// Get Full Hostname
		$uri = strtolower($uri);
		$hostname = parse_url($uri, PHP_URL_HOST);
		if (!filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 || FILTER_FLAG_IPV4) === false)
			return $hostname;

		// break up domain, reverse
		$elements = explode('.', $hostname);
		$elements = array_reverse($elements);

		// Returns Base Domain
		if (in_array($elements[0], $classes))
			return $elements[1] . '.' . $elements[0];
		elseif (in_array($elements[0], $fallout) && in_array($elements[1], $classes))
			return $elements[2] . '.' . $elements[1] . '.' . $elements[0];
		elseif (in_array($elements[0], $fallout))
			return  $elements[1] . '.' . $elements[0];
		else
			return  $elements[1] . '.' . $elements[0];
	}
}

if (!function_exists("getUploadHTML")) {

	/* function getPeersSupporting()
	 *
	* 	Get a supporting domain system for the API
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array()
	*/
	function getUploadHTML($return = '')
	{
		$forms = array();
		
			$forms[md5($return)]['html'] = getURIData(API_URL."v2/uploads/forms.api", 83, 83, array('return'=>$return));
			$forms[md5($return)]['timeout'] = time() + mt_rand(3600*3.5,3600*9.5) * mt_rand(4.5,11.511);
		
		return $forms[md5($return)]['html'];
	}
}

if (!function_exists("generateCSS")) {
	function generateCSS($fonts = array(), $name = '', $normal = 'no', $bold = 'no', $italic = 'no', $version = "v2")
	{
		if ($bold == 'yes')
			$name .= ' Bold';
		if ($italic == 'yes')
			$name .= ' Italic';
		$name = trim($name);
		$typals = fontsUseragentSupportedArray();
		$buff = array();
		$keys = array_keys($fonts);
		sort($keys);
		$buff[] = "local('||')";
		foreach($keys as $type)
			$buff[] = "url('".$fonts[$type]."') format('".$typals[$type]."')";
		$css = array();
		$css[] = "";
		$css[] = "/** Font: $name **/";
		$css[] = "@font-face {";
		$css[] = "\tfont-family: '$name';";
		$css[] = "\tsrc: url('".$fonts['eot']."');";
		$css[] = "\tsrc: ".implode(", ", $buff) .";";
		$css[] = "\tfont-weight: ".($bold=='yes'?'900':'normal') . ";";
		$css[] = "\tfont-style: ".($italic=='yes'?'italic':'normal') . ";";
		$css[] = "}";
		return implode("\n", $css);
	}
}


if (!function_exists("getPreviewHTML")) {
	function getPreviewHTML($mode = '', $clause = '', $state = '', $name = '', $output = '', $version = "v2")
	{
		$styles = array();
		$GLOBALS['fontnames'] = array();
		switch($mode)
		{
			case "font":
				switch($state)
				{
					case "default":
					default:
						$styles[$clause][getRegionalFontName($clause)] = getCSSListArray($mode, $clause, 'preview', getRegionalFontName($clause), $output, $version);
						foreach(getArchivingShellExec() as $type => $exec)
								$GLOBALS['downloaduris'][getRegionalFontName($clause)][$type] = API_URL . '/v2/data/' .  $clause . '/' . $type . '/download.api';
						break;
					case "jpg":
					case "gif":
					case "png":
						$json = json_decode(getFontRawData($mode, $clause, "font-resource.json", ''), true);
						if (!file_exists($font = FONTS_CACHE . DIRECTORY_SEPARATOR . md5($clause.sha1(date('Y-m-d'))) . ".ttf"))
						{
							$ttf = getFontRawData($mode, $clause, 'ttf', '');
							if (!is_dir(FONTS_CACHE))
								mkdir(FONTS_CACHE, 0777, true);
							writeRawFile($font, $ttf);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `type` = 'ttf AND `font_id` = '$clause'");
						}
						if (isset($font) && file_exists($font))
						{
							require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
							$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-preview.png');
							if ($state == 'jpg')
							{
								$bg = $img->allocateColor(255, 255, 255);
								$img->fill(0, 0, $bg);
							}
							$height = $img->getHeight();
							$lsize = 66;
							$ssize = 14;
							$step = mt_rand(8,11);
							$canvas = $img->getCanvas();
							$i=0; 
							while($i<$height)
							{
								$canvas->useFont($font, $point = $ssize + ($lsize - (($lsize  * ($i/$height)))), $img->allocateColor(0, 0, 0));
								$canvas->writeText(19, $i, getFontPreviewText());
								$i=$i+$point + $step;
							}
							if (!isset($_SESSION['shorturls']['downloads-zip'][$clause]) || empty($_SESSION['shorturls']['downloads-zip'][$clause]))
							{
								$jump = json_decode(getURIData(API_SHORTENING_URL.'/v2/url.api', 45, 45, array('response'=>'json', 'url'=>API_URL . '/v2/data/'.$clause.'/zip/download.api')), true);
								if (mt_rand(0,6)<4)
									$url = $_SESSION['shorturls']['downloads-zip'][$clause] = $jump['short'];
								else 
									$url = $_SESSION['shorturls']['downloads-zip'][$clause] = $jump['domain'];
							} else
								$url = $_SESSION['shorturls']['downloads-zip'][$clause];
							$canvas->useFont(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'titles.ttf', 19, $img->allocateColor(50, 85, 105));
							$canvas->writeText('right - 27', 'bottom - ' . (38+26+26+26), getRegionalFontName($clause) . " -- Font Name");
							if (!empty($url))
							{
								$canvas->writeText('right - 27', 'bottom - ' . (38+26+26), $url . " -- Download Font");
								$canvas->writeText('right - 27', 'bottom - ' . (38+26), $clause . " -- Font Identity");
							} else 
								$canvas->writeText('right - 27', 'bottom - ' . (38+26+26), $clause . " -- Font Fingerprint");
							$canvas->useFont(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'titles.ttf', 26, $img->allocateColor(30, 40, 50));
							$canvas->writeText('right - 27', 'bottom - 4', API_URL . " -- Generated ".date("Y-m-d, D H:i:s"));
							header("Content-type: ".getMimetype($state));
							die($img->output($state));
							exit(0);
						}
						die("Error Generating Preview!!");
						break;
				}
				break;
			case "fonts":
				$names = array();
				foreach(getFontsListArray($clause, $output) as $key => $font)
				{
					$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $key . "'");
					$fonts = array();
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fonts[spacerName($font['name'])][$fonttype] = $GLOBALS["source"] . "/".$version."/font/$key/$fonttype.api";
					}
					foreach(getArchivingShellExec() as $type => $exec)
						$GLOBALS['downloaduris'][spacerName($font['name'])][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
					$styles[$key][spacerName($font['name'])] = getCSSListArray($mode, $clause, $state, spacerName($font['name']), $output, $version);
				}
				break;
			case "random":
				$fonts = array();
				$fonts['normal'] = getRandomFontsFromStringList($clause, 'yes', '', '', '');
				$fonts['bold'] = getRandomFontsFromStringList($clause, '', 'yes', '', '');
				$fonts['italic'] = getRandomFontsFromStringList($clause, '', '', 'yes', '');
				$fonts['condensed'] = getRandomFontsFromStringList($clause, '', '', '', 'yes');
				$fontooo = array();
				foreach($fonts as $key => $font)
				{
					if (!empty($font))
					{
						$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $key . "'");
						$font['name'] = spacerName(trim(ucwords(str_replace('-', ' ', $state))));
						foreach(getArchivingShellExec() as $type => $exec)
							$GLOBALS['downloaduris'][$font['name']][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
						if (count($fontooo)>=2)
							$fontooo[$font['id']]['name'] . " " . ucfirst($key);
					}
					
					$fontscss = array();
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fontscss[$font['name']][$fonttype] = $GLOBALS["source"] . "/".$version."/font/$key/$fonttype.api";
					}
					foreach(getArchivingShellExec() as $type => $exec)
						$GLOBALS['downloaduris'][$font['name']][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
					$styles[$key][$font['name']] = getCSSListArray($mode, $clause, $state, $font['name'], $output, $version);
				}
				break;
		}
		$GLOBALS['fontcss'] = $styles;
		include __DIR__ . DIRECTORY_SEPARATOR . 'preview.php';
		exit(0);
	}
}

if (!function_exists("getNamingImage")) {
	function getNamingImage($mode = '', $clause = '', $state = '', $output = '', $version = "v2")
	{

		if (!file_exists($font = FONTS_CACHE . DIRECTORY_SEPARATOR . md5($clause.sha1(date('Y-m-d'))) . ".ttf"))
		{
			$ttf = getFontRawData($mode, $clause, 'ttf', '');
			if (!is_dir(FONTS_CACHE))
				mkdir(FONTS_CACHE, 0777, true);
			writeRawFile($font, $ttf);
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `type` = 'ttf AND `font_id` = '$clause'");
		}
		
		if (isset($font) && file_exists($font))
		{
			$naming = getRegionalFontName($clause);
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
			if (strlen($naming)<=9)
			{
				$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-small.png');
			} elseif (strlen($naming)<=12)
			{
				$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-medium.png');
			}elseif (strlen($naming)<=21)
			{
				$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-large.png');
			} else
			{
				$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-extra.png');
			}
			$height = $img->getHeight();
			$point = $height * (32/99);
			$canvas = $img->getCanvas();
			$canvas->useFont($font, $point, $img->allocateColor(0, 0, 0));
			$canvas->writeText('center', 'center', $naming);
			header("Content-type: ".getMimetype($state));
			die($img->output($state));
			exit(0);
		}
		die("Error Generating Naming Title Preview!!");
	}
}

if (!function_exists("getFontIdentitiesArray")) {
	function getFontIdentitiesArray($mode = '', $clause = '', $state = '', $name = '', $output = '', $version = "v2")
	{
		$ret=array();
		$sql = "SELECT * from `fonts`";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($font = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$ret[] = $font['id'];
		}
		return $ret;
	}
}

if (!function_exists("getGlyphPreview")) {
	function getGlyphPreview($mode = '', $clause = '', $state = '', $name = '', $output = '', $char = '0', $version = "v2")
	{

		if (!file_exists($font = FONTS_CACHE . DIRECTORY_SEPARATOR . md5($clause.sha1(date('Y-m-d'))) . ".ttf"))
		{
			$ttf = getFontRawData($mode, $clause, 'ttf', '');
			if (!is_dir(FONTS_CACHE))
				mkdir(FONTS_CACHE, 0777, true);
			writeRawFile($font, $ttf);
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `type` = 'ttf AND `font_id` = '$clause'");
		}
		
		if (isset($font) && file_exists($font))
		{
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
			$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-glyph.png');
			if ($state == 'jpg')
			{
				$bg = $img->allocateColor(255, 255, 255);
				$img->fill(0, 0, $bg);
			}
			$height = $img->getHeight();
			$canvas = $img->getCanvas();
			$canvas->useFont($font, $height-37, $img->allocateColor(0, 0, 0));
			$canvas->writeText("center", "center", "&#$char;");
			$canvas->useFont(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'titles.ttf', 10, $img->allocateColor(0, 0, 0));
			$canvas->writeText('center', 'top + 3', '&amp;#'.$char.';');
			header("Content-type: ".getMimetype($state));
			die($img->output($state));
			exit(0);
		}
		die("Error Generating Glyph Preview!!");
	}
}

if (!function_exists("getRegionalFontName")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function getRegionalFontName($fontid = '', $latitude = 0, $longitude = 0, $getGistance = false)
	{
		static $variables = array();
		if (!isset($variables[$fontid]))
		{
			if ($latitude==0 && $longitude == 0)
			{
				if (empty($iparray)) 
					$iparray = getIPIdentity(whitelistGetIP(true), true);
				$latitude = $iparray['latitude'];
				$longitude = $iparray['longitude'];
			}
			list($name, $distance) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF("SELECT `name`, 3956 * 2 * ASIN(SQRT(POWER(SIN((" . abs($latitude) . " - abs(`latitude`)) * pi() / 180 / 2), 2) + COS(" . abs($latitude) . " * pi() / 180 ) * COS(abs(`latitude`) *  pi() / 180) * POWER(SIN((" . $longitude . " - `longitude`) *  pi() / 180 / 2), 2) )) as distance FROM `fonts_names` WHERE `font_id` = '$fontid' ORDER BY `distance` LIMIT 1"));
			$variables[$fontid]['name'] = empty($name)?$fontid:$name;
			$variables[$fontid]['distance'] = $distance;
		}
		return spacerName(!isset($variables[$fontid]['name'])||empty($variables[$fontid]['name'])?$fontid:($getGistance == false?$variables[$fontid]['name']:$variables[$fontid]['distance']));
	}
}

if (!function_exists("getMimetype")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function getMimetype($extension = '-=-')
	{
		$mimetypes = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mimetypes.diz'));
		foreach($mimetypes as $mimetype)
		{
			$parts = explode("||", $mimetype);
			if (strtolower($extension) == strtolower($parts[0]))
				return $parts[1];
			if (strtolower("-=-") == strtolower($parts[0]))
				$final = $parts[1];
		}
		return $final;
	}
}

if (!function_exists("mkdirSecure")) {
	/**
	 *
	 * @param unknown_type $path
	 * @param unknown_type $perm
	 * @param unknown_type $secure
	 */
	function mkdirSecure($path = '', $perm = 0777, $secure = true)
	{
		if (!is_dir($path))
		{
			mkdir($path, $perm, true);
			if ($secure == true)
			{
				writeRawFile($path . DIRECTORY_SEPARATOR . '.htaccess', "<Files ~ \"^.*$\">\n\tdeny from all\n</Files>");
			}
			return true;
		}
		return false;
	}
}

if (!function_exists("cleanWhitespaces")) {
	/**
	 *
	 * @param array $array
	 */
	function cleanWhitespaces($array = array())
	{
		foreach($array as $key => $value)
		{
			if (is_array($value))
				$array[$key] = cleanWhitespaces($value);
			else {
				$array[$key] = trim(str_replace(array("\n", "\r", "\t"), "", $value));
			}
		}
		return $array;
	}
}

if (!function_exists("getDomainSupportism")) {

	/* function getDomainSupportism()
	 *
	* 	Get a supporting domain system for the API
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		string
	*/
	function getDomainSupportism($variable = 'array', $realm = '')
	{
		static $ret = array();
		if (empty($ret))
		{
			$supporters = (file(API_FILE_IO_DOMAINS));
			foreach($supporters as $supporter)
			{
				$parts = explode("||", str_replace("\n", "", $supporter));
				if (strpos(' '.strtolower($realm), strtolower($parts[0]))>0)
				{
					$ret['domain'] = $parts[0];
					$ret['protocol'] = $parts[1];
					$ret['business'] = $parts[2];
					$ret['entity'] = $parts[3];
					$ret['contact'] = $parts[4];
					$ret['referee'] = $parts[5];
					continue;
				}
			}
		}
		if (isset($ret[$variable]))
			return $ret[$variable];
		return $ret;
	}
}


if (!function_exists("getPingTiming")) {

	/* function getPingTiming()
	 *
	* 	Get a ping timing for a URL
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		float()
	*/
	function getPingTiming($uri = '', $timeout = 14, $connectout = 13)
	{
		$pings = array();
		if (file_exists(FONTS_CACHE . DIRECTORY_SEPARATOR . 'pings-list.serial'))
			$pings = unserialize(file_get_contents(FONTS_CACHE . DIRECTORY_SEPARATOR . 'pings-list.serial'));
		foreach($pings as $key => $values)
			if ($values['timeout'] <= microtime(true))
			unset($pings[$key]);
		if (!isset($pings[md5($uri)]))
		{
			$start = microtime(true);
			ob_start();
			if (!$btt = curl_init($uri)) {
				$pings[md5($uri)]['ping'] = 0;
				$pings[md5($uri)]['timeout'] = time() + mt_rand(23,97);
				@writeRawFile(FONTS_CACHE . DIRECTORY_SEPARATOR . 'pings-list.serial', serialize($pings));
			} else {
				curl_setopt($btt, CURLOPT_HEADER, 0);
				curl_setopt($btt, CURLOPT_POST, 0);
				curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
				curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($btt, CURLOPT_VERBOSE, false);
				curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
				@curl_exec($btt);
				curl_close($btt);
				ob_end_clean();
				$pings[md5($uri)]['ping'] = microtime(true) - $start * 1000;
				$pings[md5($uri)]['timeout'] = time() + mt_rand(23,97);
				@writeRawFile(FONTS_CACHE . DIRECTORY_SEPARATOR . 'pings-list.serial', serialize($pings));
			}
		}
		return $pings[md5($uri)]['ping'];
	}
}


if (!function_exists("getURIData")) {

	/* function getURIData()
	 *
	* 	cURL Routine
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
   	*
	* @return 		float()
	*/
	function getURIData($uri = '', $timeout = 65, $connectout = 65, $post_data = array())
	{
		if (!function_exists("curl_init"))
		{
			die("Need to install php-curl: $ sudo apt-get install php-curl");
		}
		if (!$btt = curl_init($uri)) {
			return false;
		}
		curl_setopt($btt, CURLOPT_HEADER, 0);
		curl_setopt($btt, CURLOPT_POST, (count($post_data)==0?false:true));
		if (count($post_data)!=0)
			curl_setopt($btt, CURLOPT_POSTFIELDS, http_build_query($post_data));
		curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
		curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($btt, CURLOPT_VERBOSE, false);
		curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($btt);
		curl_close($btt);
		return $data;
	}
}

if (!function_exists("getPeersSupporting")) {

	/* function getPeersSupporting()
	 *
	* 	Get a supporting domain system for the API
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array()
	*/
	function getPeersSupporting()
	{
		if (filectime(API_FILE_IO_PEERS) + 3600 * 24 * 3.75 <= time())
		{
			if (getPingTiming("http://peers.labs.coop/v2/" . basename(__DIR__) . "/json.api")>1
					&& $peerise = json_decode(getURIData("http://peers.labs.coop/v2/" . basename(__DIR__) . "/json.api"), true))
			{
				$ioout = array();
				foreach($peerise as $ll => $values)
					$ioout[] = implode("||", $values);
				if (count($ioout)>1)
					writeRawFile(API_FILE_IO_PEERS, implode("\n", $ioout));
			}
		}
		static $ret = array();
		if (empty($ret))
		{
			$peerings = file(API_FILE_IO_PEERS);
			foreach($peerings as $peer)
			{
				$parts = explode("||", $peer);
				$realm = $parts[0];
				$ret[$realm]['domain'] = $parts[0];
				$ret[$realm]['protocol'] = $parts[1];
				$ret[$realm]['business'] = $parts[2];
				$ret[$realm]['search'] = $parts[3];
				$ret[$realm]['mirror'] = $parts[4];
				$ret[$realm]['contact'] = $parts[5];
				$ret[$realm]['ping'] = getPingTiming($parts[1].$parts[0]);
			}
		}
		return $ret;
	}
}


if (!function_exists("getCSSListArray")) {
	function getCSSListArray($mode = '', $clause = '', $state = '', $name = '', $output = '', $version = "v2")
	{
	$styles = array();
	switch($mode)
	{
		case "font":
			$sql = "SELECT * from `fonts` WHERE `id` = '$clause'";
			$result = $GLOBALS['FontsDB']->queryF($sql);
			while($font = $GLOBALS['FontsDB']->fetchArray($result))
			{
				foreach(getArchivingShellExec() as $type => $exec)
					$GLOBALS['downloaduris'][$font['name']][$type] = API_URL . '/v2/data/' .  $font['id'] . '/' . $type . '/download.api';
				$fonts = array();
				$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $clause . "'");
				foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
				{
					$fonts[$fonttype] = API_URL . "/".$version."/font/$clause/$fonttype.api";
				}
				//die(getRegionalFontName($clause));
				$GLOBALS['fontnames'][spacerName(getRegionalFontName($clause))] = spacerName(getRegionalFontName($clause));
				$styles[spacerName(getRegionalFontName($clause))] = generateCSS($fonts, spacerName(getRegionalFontName($clause)), $font['normal'], $font['bold'], $font['italics']);
				if ($state!='preview')
				{
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fonts[$clause][$fonttype] = API_URL . "/".$version."/font/$clause/$fonttype.api";
					}
					$GLOBALS['fontnames'][$clause] = $clause;
					foreach(getArchivingShellExec() as $type => $exec)
						$GLOBALS['downloaduris'][$clause][$type] = API_URL . '/v2/data/' .  $clause . '/' . $type . '/download.api';
					$styles[$clause] = generateCSS($fonts[$clause], $clause, $font['normal'], $font['bold'], $font['italics']);
				}
			}
			break;
		case "fonts":
			$names = array();
			foreach(getFontsListArray($clause, $output) as $key => $font)
			{
				$fonts = array();
				$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $key . "'");
				foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
				{
					$fonts[getRegionalFontName($key)][$fonttype] = API_URL . "/".$version."/font/$key/$fonttype.api";
				}
				$GLOBALS['fontnames'][getRegionalFontName($key)] = getRegionalFontName($key);
				foreach(getArchivingShellExec() as $type => $exec)
					$GLOBALS['downloaduris'][getRegionalFontName($key)][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
				$sql = "SELECT * from `fonts_names` WHERE `font_id` = '$key'";
				$result = $GLOBALS['FontsDB']->queryF($sql);
				while($fontname = $GLOBALS['FontsDB']->fetchArray($result))
				{
					$styles[md5($key.$fontname['name'])] = generateCSS($fonts[getRegionalFontName($key)], spacerName($fontname['name']), $font['normal'], $font['bold'], $font['italics']);
				}
				$GLOBALS['fontnames'][$key] = $key;
				foreach(getArchivingShellExec() as $type => $exec)
					$GLOBALS['downloaduris'][$key][$type] = API_URL . '/v2/data/' .  $key . '/' . $type . '/download.api';
				$styles[$key] = generateCSS($fonts[getRegionalFontName($key)], getRegionalFontName($key), $font['normal'], $font['bold'], $font['italics']);
				
			}
			break;
		case "sites":
			break;
		case "random":
			$fonts = array();
			$fonts['normal'] = getRandomFontsFromStringList($clause, 'yes', '', '', '');
			$fonts['bold'] = getRandomFontsFromStringList($clause, '', 'yes', '', '');
			$fonts['italic'] = getRandomFontsFromStringList($clause, '', '', 'yes', '');
			$fonts['condensed'] = getRandomFontsFromStringList($clause, '', '', '', 'yes');
			$fontooo = array();
			foreach($fonts as $key => $font)
			{
				if (!empty($font))
				{
					$font['name'] = trim(ucwords(str_replace('-', ' ', spacerName($state))));
					$fontooo[$font['id']] = $font;
					if (count($fontooo)>=2)
						$fontooo[$font['id']]['name'] . " " . ucfirst(spacerName($key));
				}
			}
			$GLOBALS['fontnames'][] = $font['name'];
			foreach($fontooo as $key => $font)
			{
				if (!empty($font))
				{
					$fonter = array();
					$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $key . "'");
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fonter[$fonttype] = API_URL . "/".$version."/font/$key/$fonttype.api";
					}
					$styles[$key] = generateCSS($fonter, trim(spacerName($font['name'])) . ($names[$font['name']]!="AA"?" ".$names[$font['name']]:""), $font['normal'], $font['bold'], $font['italics']);
				}
			}
			break;
		}
		foreach($GLOBALS['fontnames'] as $key => $value)
			if (empty($value)||empty($key))
				unset($GLOBALS['fontnames'][$key]);
			
		return $styles;
	}
}


if (!function_exists("getFontsRssData")) {
	function getFontsRssData($mode = '', $clause = '', $state = '', $output = '', $version = "v2")
	{
		$xml = "<?xml version='1.0' encoding='utf-8' ?>";
		$xml .= "\n<rss version='2.0'>";
		$xml .= "\n<channel>";
		$xml .= "\n\t<title>".($clause=='zeroday'?"Zero-day Font Releases":"Popular Fonts")."</title>";
		$xml .= "\n\t<link>http://fonts.labs.coop</link>";
		$xml .= "\n\t<description>".($clause=='zeroday'?"Zero-day Font Releases":"Popular Fonts")." on our font api ~ http://fonts.labs.coop</description>";
		$xml .= "\n\t<language>en</language>";
		$xml .= "\n\t<webMaster>wishcraft@users.sourceforge.net</webMaster>";
		$xml .= "\n\t<image>";
		$xml .= "\n\t\t<title>Chronolabs Cooperative</title>";
		$xml .= "\n\t\t<url>http://fonts.labs.coop/images/200x200.png</url>";
		$xml .= "\n\t\t<link>http://fonts.labs.coop</link>";
		$xml .= "\n\t</image>";
		
		$items = 30;
		foreach($_GET as $key => $value)
			if (empty($value) && is_numeric($key))
				$items = $key;
		if ($items < 5)
			$items = 5;
		
		switch($clause)
		{
			case "zeroday":
				$sql = "SELECT * from `uploads` WHERE (`released` > 0) ORDER BY `released` DESC LIMIT $items";
				$result = $GLOBALS['FontsDB']->queryF($sql);
				while($row = $GLOBALS['FontsDB']->fetchArray($result))
				{
					$xml .= "\n\n\t<item>";
					$xml .= "\n\t\t<title>Font Released: ".getRegionalFontName($row['font_id'])."</title>";
					$xml .= "\n\t\t<pubDate>".date('D, y-m-d H:i:s', $row['released'])." +1000</pubDate>";
					$xml .= "\n\t\t<link>".API_URL."/v2/font/".$row['font_id']."/preview.api</link>";
					$xml .= "\n\t\t<guid>".sha1($row['font_id'].$row['released'])."</guid>";
					$xml .= "\n\t\t<description>&lt;img src='".API_URL."/v2/font/".$row['font_id']."/preview/image.png' width='100%'/&gt;</description>";
					$xml .= "\n\t\t<identity>".$row['font_id']."</identity>";
					$xml .= "\n\t\t<enclosure url=\"".API_URL."/v2/data/".$row['font_id']."/zip/download.api\" />";
					$xml .= "\n\t</item>";
				}
				break;
			default:
			case "popular":
				$sql = "SELECT * from `fonts` WHERE (`hits` > 0) ORDER BY `hits` DESC LIMIT $items";
				$result = $GLOBALS['FontsDB']->queryF($sql);
				while($row = $GLOBALS['FontsDB']->fetchArray($result))
				{
					$xml .= "\n\n\t<item>";
					$xml .= "\n\t\t<title>Popular Font: ".getRegionalFontName($row['id'])." ~ Hits: ".$row['hits']."</title>";
					$xml .= "\n\t\t<pubDate>".date('D, y-m-d H:i:s', time())." +1000</pubDate>";
					$xml .= "\n\t\t<link>".API_URL."/v2/font/".$row['id']."/preview.api</link>";
					$xml .= "\n\t\t<guid>".sha1($row['id'].$row['hits'])."</guid>";
					$xml .= "\n\t\t<description>&lt;img src='".API_URL."/v2/font/".$row['id']."/preview/image.png' width='100%'/&gt;</description>";
					$xml .= "\n\t\t<identity>".$row['id']."</identity>";
					$xml .= "\n\t\t<enclosure url=\"".API_URL."/v2/data/".$row['id']."/zip/download.api\" />";
					$xml .= "\n\t</item>";
				}
				break;
		}

		$xml .= "\n</channel>";
		$xml .= "\n</rss>";
		return $xml;
	}
}

if (!function_exists("getSurveyCSSListArray")) {
	function getSurveyCSSListArray($mode = '', $clause = '', $state = '', $name = '', $output = '', $version = "v2")
	{
		$row = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * FROM `uploads` WHERE `key` = '$clause'"));
		if (count($row)>0)
		{
			if (file_exists($row['currently_path'] . DIRECTORY_SEPARATOR . "font-resource.json"))
				$data = json_decode(file_get_contents($row['currently_path'] . DIRECTORY_SEPARATOR  . "font-resource.json"), true);
		}
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . $GLOBALS['hourprev'] . '--' . date('d-M-Y') . '---css-list--' . sha1($mode.$clause.$state.$name).'.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . $GLOBALS['hourindx'] . '--' . date('d-M-Y') . '---css-list--' . sha1($mode.$clause.$state.$name).'.serial'))
		{
			$styles = array();
			switch($mode)
			{
				case "font":
					foreach(array_keys(fontsUseragentSupportedArray()) as $fonttype)
					{
						$fonts[$fonttype] = API_URL . "/".$version."/survey/font/$clause/".$fonttype.".api";
					}
					$styles = generateCSS($fonts, $data['FontName'], true, false, false);
					break;
			}
			@writeRawFile($cache, $styles);
			return $styles;
		}
		return file_get_contents($cache);
	}
}

if (!function_exists("getFontRawData")) {
	function getFontRawData($mode = '', $clause = '', $output = '', $ufofile = '')
	{
		global $ipid;
		if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `networking` SET `fonts` = `fonts` + 1 WHERE `ip_id` LIKE '$ipid'"))
			die("SQL Failed: $sql;");
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		if (!$result = $GLOBALS['FontsDB']->queryF($sql))
			die("SQL Failed: $sql;");
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $row['font_id'] . "'");
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `accessings` = `accessings` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			switch($font['medium'])
			{
				case 'FONT_RESOURCES_CACHE':
				case 'FONT_RESOURCES_RESOURCE':
					if ($font['medium'] == 'FONT_RESOURCES_CACHE')
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					} elseif ($font['medium'] == 'FONT_RESOURCES_RESOURCE')
					{
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant($font['medium']) . $row['path'], 0777, true);
							writeRawFile(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						}
					}
					$json = json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
				case 'FONT_RESOURCES_PEER':
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					$json = json_decode(getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
			}
			if ($output!="font-resource.json")
			{
				$found = false;
				$cachefile = FONTS_CACHE . DIRECTORY_SEPARATOR . '--raw--' . DIRECTORY_SEPARATOR . md5(getRegionalFontName($row['font_id']).$row['font_id']) . '.zip';
				if (!is_dir(FONTS_CACHE . DIRECTORY_SEPARATOR . '--raw--' . DIRECTORY_SEPARATOR))
					mkdir(FONTS_CACHE . DIRECTORY_SEPARATOR . '--raw--' . DIRECTORY_SEPARATOR, 0777, true);
				if (!file_exists($cachefile))
					$found = false;
				else {
					foreach(getArchivedZIPContentsArray($cachefile) as $crc => $file)
						if (substr($file['filename'], strlen($file['filename']) - strlen($output)) == $output || strpos($file['path'], $output) > 0)
							$found = true;
				}
				if ($found != true)
				{
					mkdir($currently = FONT_RESOURCES_CONVERTING . DIRECTORY_SEPARATOR . sha1(md5_file($zip).$row['font_id']), 0777, true);
					chdir($currently);
					foreach(getArchivedZIPContentsArray($zip) as $crc => $file)
						if (substr($file['filename'], strlen($file['filename']) - strlen(API_BASE)) == API_BASE)
						{
							$basefile = $file['filename'];
							continue;
						}
					writeRawFile($font = $currently . DIRECTORY_SEPARATOR . $basefile, getArchivedZIPFile($zip, $basefile, $row['font_id']));					
					if (isset($json['Font']))
						writeFontResourceHeader($font, $json["Font"]['licence'], $json['Font']);
					$totalmaking = count(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"))-1;
					$outt = array();exec("cd $currently", $outt, $return);
					$covertscript = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts.pe"));
					foreach($covertscript as $line => $value)
						if (!strpos($value, $output) && substr($value,0,4)!='Open' && !in_array($output, array('z', 'php')))
							unset($covertscript[$line]);
						elseif(in_array($output, array('z', 'php') && substr($value,0,4)!='Open' && (!strpos($value, 'ttf')) && !strpos($value, 'afm')))
							unset($covertscript[$line]);
					writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).$zip.$row['font_id']).".pe", implode("\n", $covertscript));
					$outt = shell_exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $font));
					unlink($script);
					if (in_array($output, array('z', 'php')))
					{
						$parts = explode('.', basename($font));
						unset($parts[count($parts)-1]);
						$fbase = implode(".", $parts);
						if (file_exists($currently . DIRECTORY_SEPARATOR . $fbase . '.ttf') && file_exists($currently . DIRECTORY_SEPARATOR . $fbase . '.afm'))
							MakePHPFont($currently . DIRECTORY_SEPARATOR . $fbase . '.ttf', $currently . DIRECTORY_SEPARATOR . $fbase . '.afm', $currently, true);
					}
					$packing = getArchivingShellExec();
					chdir($currently);
					$cmda = str_replace("%folder", "./", str_replace("%pack", $cachefile, str_replace("%comment", $comment, (substr($packing['zip'],0,1)!="#"?$packing['zip']:substr($packing['zip'],1)))));
					$outt = shell_exec($cmda);
					if (!file_exists($cachefile))
						die("File not found: $cachefile ~~ Failed: $cmda\n\n$outt");
					$output = array();
					exec($cmd = "rm -Rfv $currently", $output);
				}
				$zip = $cachefile;
			}
			$fontfiles = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts_files` WHERE `font_id` = '" . $row['font_id'] . "' AND `type` = '$output'"));
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['font_id'] . "'");
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `hits` = `hits` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			$resultb = $GLOBALS['FontsDB']->queryF($sql = "SELECT * FROM `fonts_callbacks` WHERE `failed` <= unix_timestamp() - (3600 * 6) AND LENGTH(`uri`) > 0 AND `type` IN ('fonthit') AND `font_id` = '" . $row['font_id'] . "'");
			while($callback = $GLOBALS['FontsDB']->fetchArray($resultb))
			{
				@setCallBackURI($callback['uri'], 145, 145, array_merge(array('type' => $output, 'hits' => $fontfiles['hits']+1, 'font-key' => $row['font_id'], 'ipid' => getIPIdentity('', true))), array("success"=>"UPDATE `fonts_callbacks` SET `calls` = `calls` + 1, `last` = UNIX_TIMESTAMP() WHERE `id` = '" . $callback['id'] . "'", "failed" => "UPDATE `fonts_callbacks` SET `calls` = `calls` + 1, `last` = UNIX_TIMESTAMP(), `failed` = UNIX_TIMESTAMP() WHERE `id` = '" . $callback['id'] . "'"));
			}
			$resdata = array();
			foreach(getArchivedZIPContentsArray($zip) as $md5 => $values)
				if ($output == 'ufo' || $values['type'] === $output || strtolower(substr($values['filename'], strlen($values['filename']) - strlen($output), strlen($output))) == strtolower($output))
					switch($output)
					{
						default:
							$GLOBALS['filename'] = $values['filename'];
							if (!file_exists($font = FONTS_CACHE . DIRECTORY_SEPARATOR . '--data--' . DIRECTORY_SEPARATOR . md5($zip.sha1(date('Y-m-d'))) . ".$output"))
							{
								$data = getArchivedZIPFile($zip, $values['filename'], $row['font_id']);
								if (!is_dir(FONTS_CACHE . DIRECTORY_SEPARATOR . '--data--'))
									mkdir(FONTS_CACHE . DIRECTORY_SEPARATOR . '--data--', 0777, true);
								writeRawFile($font, $data);
								return $data;
							}
							return file_get_contents($font);
							break;
						case "ufo":
							$GLOBALS['filename'] = basename($ufofile);
							return getArchivedZIPFile($zip, $ufofile, $row['font_id']);
							break;
					}
			if (!empty($data))
				return $data;
			die("Font Type: $output - Not found in Font Resource: ".basename($zip));
		}
		return false;
	}
}


if (!function_exists("getFontFileDiz")) {
	function getFontFileDiz($mode = '', $clause = '', $state = '', $output = '', $version = '')
	{
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `accessings` = `accessings` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			switch($font['medium'])
			{
				case 'FONT_RESOURCES_CACHE':
				case 'FONT_RESOURCES_RESOURCE':
					if ($font['medium'] == 'FONT_RESOURCES_CACHE')
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					} elseif ($font['medium'] == 'FONT_RESOURCES_RESOURCE')
					{
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant($font['medium']) . $row['path'], 0777, true);
							writeRawFile(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						}
					}
					return getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'file.diz');
					break;
				case 'FONT_RESOURCES_PEER':
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					return getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'file_diz');
					break;
			}
		}
	}
}

if (!function_exists("setFontCallback")) {
	function setFontCallback($mode = '', $clause = '', $state = '', $output = '', $version = '')
	{
		$string = parse_url(API_URL.$_SERVER["REQUEST_URI"], PHP_URL_QUERY);
		parse_str($string, $values);
		foreach($_REQUEST as $key => $value)
			$values[$key] = $value;
		$error = array();
		if (isset($values['email']) || !empty($values['email'])) {
			if (!checkEmail($values['email']))
				$error[] = 'Email is invalid!';
		} else
			$error[] = 'No Email Address for Notification specified!';
		
		if (!isset($values['uri']) || empty($values['uri']))
		{
			$error[] = 'No callback URI Specified!';
		}
		$sql = "Select count(*) from `fonts_callbacks` WHERE `type` = '$mode' and `font_id` = '$clause' and `email` = '".$GLOBALS['FontsDB']->escape($values['email']) . "' AND `uri` = '".$GLOBALS['FontsDB']->escape($values['uri']) . "'";
		list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
		if ($count>0)
		{
			$error[] = 'Callback Already Exists!';
		}
		
		if (!empty($error))
		{
			redirect(isset($values['return'])&&!empty($values['return'])?$values['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Error Has Occured</h1><br/><p>" . implode("<br />", $error) . "</p></center>");
			exit(0);
		}
		$sql = "Select `id` from `fonts_archiving` WHERE `font_id` = '$clause'";
		list($archiveid) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
		$sql = "Select `id` from `uploads` WHERE `font_id` = '$clause'";
		list($uploadid) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF($sql));
		$sql = "INSERT INTO `fonts_callbacks` (`type`, `email`, `uri`, `font_id`, `archive_id`, `upload_id`) VALUES('$mode', '".$GLOBALS['FontsDB']->escape($values['email']) . "','".$GLOBALS['FontsDB']->escape($values['uri']) . "', '$clause', '$archiveid', '$uploadid')";
		if (!$GLOBALS['FontsDB']->queryF($sql))
			die("SQL Failed: $sql;");
		redirect(isset($values['return'])&&!empty($values['return'])?$values['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center>Font Callback Created!</center>");
		exit(0);
	}
}

if (!function_exists("getFontsCallbacksArray")) {
	function getFontsCallbacksArray($clause = '', $state = '', $output = '', $version = '')
	{
		$return = array();
		$sql = "SELECT * from `fonts_callbacks` WHERE (`font_id` = '$clause')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			unset($row['id']);
			unset($row['archive_id']);
			$return[] = $row;
		}
		return $return;
	}
}

if (!function_exists("getFontsDataArray")) {
	function getFontsDataArray($clause = '', $state = '', $output = '', $version = '')
	{
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $row['font_id'] . "'");
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `accessings` = `accessings` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			switch($font['medium'])
			{
				case 'FONT_RESOURCES_CACHE':
				case 'FONT_RESOURCES_RESOURCE':
					if ($font['medium'] == 'FONT_RESOURCES_CACHE')
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					} elseif ($font['medium'] == 'FONT_RESOURCES_RESOURCE')
					{
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant($font['medium']) . $row['path'], 0777, true);
							writeRawFile(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
						}
					}
					return json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
				case 'FONT_RESOURCES_PEER':
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					return json_decode(getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
			}
		}
	}
}


if (!function_exists("getFontDownload")) {
	function getFontDownload($mode = '', $clause = '', $state = '', $output = '', $version = '')
	{

		
		global $ipid;
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `accessings` = `accessings` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			switch($font['medium'])
			{
				case 'FONT_RESOURCES_CACHE':
				case 'FONT_RESOURCES_RESOURCE':
					if ($font['medium'] == 'FONT_RESOURCES_CACHE')
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					} elseif ($font['medium'] == 'FONT_RESOURCES_RESOURCE')
					{
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant($font['medium']) . $row['path'], 0777, true);
							writeRawFile(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
						}
					}
					$resource = json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
				case 'FONT_RESOURCES_PEER':
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					$zip = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]["resource"];
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					$resource = json_decode(getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
			}
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `downloaded` = `downloaded` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['font_id'] . "'");
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `sourcings` = `sourcings` + 1, `sourced` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			$resource['downloads'][$ipid][microtime(true)] = getIPIdentity(whitelistGetIP(true), true);
			if (!mkdir($currently = FONT_RESOURCES_SORTING . DIRECTORY_SEPARATOR .$state . DIRECTORY_SEPARATOR . $ipid. DIRECTORY_SEPARATOR . $row['font_id'], 0777, true))
				if (!is_dir($currently))
					die("Failed to make path: $currently");
			$filename = getRegionalFontName($row['font_id']) . '.'.$state;
			if (!$GLOBALS['FontsDB']->queryF($sql = "INSERT INTO `fonts_downloads` (`font_id`, `archive_id`, `filename`, `ip_id`, `when`) VALUES ('" . $row['font_id'] . "', '" . $row['id'] . "', '$filename', '$ipid', unix_timestamp())"))
				die("SQL Failed: $sql;");
			if (!$GLOBALS['FontsDB']->queryF($sql = "UPDATE `networking` SET `downloads` = `downloads` +1 WHERE `ip_id` LIKE '$ipid'"))
				die("SQL Failed: $sql;");
			
			$found = false;
			$cachefile = FONTS_CACHE . DIRECTORY_SEPARATOR . '--download--' . DIRECTORY_SEPARATOR . md5(getRegionalFontName($row['font_id']).$row['font_id']) . '.' . $state;
			if (!is_dir(FONTS_CACHE . DIRECTORY_SEPARATOR . '--download--' . DIRECTORY_SEPARATOR))
				mkdir(FONTS_CACHE . DIRECTORY_SEPARATOR . '--download--' . DIRECTORY_SEPARATOR, 0777, true);
			if (!file_exists($cachefile))
			{
				mkdir($currently = FONT_RESOURCES_CONVERTING . DIRECTORY_SEPARATOR . md5(md5_file($zip).microtime(true).getRegionalFontName($row['font_id'])), 0777, true);
				chdir($currently);
				foreach(getArchivedZIPContentsArray($zip) as $crc => $file)
					if (substr($file['filename'], strlen($file['filename']) - strlen(API_BASE)) == API_BASE)
					{
						$basefile = $file['filename'];
						continue;
					}
				writeRawFile($font = $currently . DIRECTORY_SEPARATOR . $basefile, getArchivedZIPFile($zip, $basefile, $row['font_id']));
				if (isset($resource['Font']))
					writeFontResourceHeader($font, $resource["Font"]['licence'], $resource['Font']);
				$outt = array(); exec("cd $currently", $outt, $return);
				$covertscript = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts-distribution.pe"));
				foreach($covertscript as $line => $value)
					if (strpos($value, API_BASE))
						unset($covertscript[$line]);
				writeRawFile($script = FONT_RESOURCES_CACHE.DIRECTORY_SEPARATOR.md5(microtime(true).API_URL).".pe", implode("\n", $covertscript));
				$outt = array(); exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", $script, $font), $outt, $return);;
				unlink($script);
				$parts = explode('.', basename($font));
				unset($parts[count($parts)-1]);
				$fbase = implode(".", $parts);
				if (file_exists($currently . DIRECTORY_SEPARATOR . $fbase . '.ttf') && file_exists($currently . DIRECTORY_SEPARATOR . $fbase . '.afm'))
					MakePHPFont($currently . DIRECTORY_SEPARATOR . $fbase . '.ttf', $currently . DIRECTORY_SEPARATOR . $fbase . '.afm', $currently, true);

				$files = getCompleteFontsListAsArray($currently);
				foreach($files['ttf'] as $md5 => $preview)
				{
					if (isset($preview) && file_exists($preview))
					{
						require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
						$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-preview.png');
						$height = $img->getHeight();
						$lsize = 66;
						$ssize = 14;
						$step = mt_rand(8,11);
						$canvas = $img->getCanvas();
						$i=0;
						while($i<$height)
						{
							$canvas->useFont($preview, $point = $ssize + ($lsize - (($lsize  * ($i/$height)))), $img->allocateColor(0, 0, 0));
							$canvas->writeText(19, $i, getFontPreviewText());
							$i=$i+$point + $step;
						}
						if (!isset($_SESSION['shorturls']['downloads-zip'][$clause]) || empty($_SESSION['shorturls']['downloads-zip'][$clause]))
						{
							$jump = json_decode(getURIData(API_SHORTENING_URL.'/v2/url.api', 45, 45, array('response'=>'json', 'url'=>API_URL . '/v2/data/'.$clause.'/zip/download.api')), true);
							if (mt_rand(0,6)<4)
								$url = $_SESSION['shorturls']['downloads-zip'][$clause] = $jump['short'];
							else 
								$url = $_SESSION['shorturls']['downloads-zip'][$clause] = $jump['domain'];
						} else
							$url = $_SESSION['shorturls']['downloads-zip'][$clause];
						$canvas->useFont(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'titles.ttf', 19, $img->allocateColor(50, 85, 105));
						$canvas->writeText('right - 27', 'bottom - ' . (38+26+26+26), getRegionalFontName($clause) . " -- Font Name");
						if (!empty($url))
						{
							$canvas->writeText('right - 27', 'bottom - ' . (38+26+26), $url . " -- Download Font");
							$canvas->writeText('right - 27', 'bottom - ' . (38+26), $clause . " -- Font Identity");
						} else 
							$canvas->writeText('right - 27', 'bottom - ' . (38+26+26), $clause . " -- Font Fingerprint");
						$canvas->useFont(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'titles.ttf', 26, $img->allocateColor(30, 40, 50));
						$canvas->writeText('right - 27', 'bottom - 4', API_URL . " -- Generated ".date("Y-m-d, D H:i:s"));
						$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'Font Preview for '.getRegionalFontName($row['font_id']).'.png');
						unset($img);
						$title = spacerName(getRegionalFontName($row['font_id']));
						if (strlen($title)<=9)
							$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-small.png');
							elseif (strlen($title)<=18)
							$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-medium.png');
							elseif (strlen($title)<=35)
							$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-large.png');
							elseif (strlen($title)>=36)
							$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'font-title-extra.png');
							$canvas->useFont($preview, 78, $img->allocateColor(0, 0, 0));
							$canvas->writeText('center', 'center', $title);
							$img->saveToFile($currently . DIRECTORY_SEPARATOR . 'font-name-banner.png');
							unset($img);
					}
				}
				$grader = array();
				$files = getFontsListAsArray($currently);
				foreach($files as $id => $values)
				{
					if (filesize($currently . DIRECTORY_SEPARATOR . $values['file']) > 0)
					{
						$grader[$values['type']] = $currently . DIRECTORY_SEPARATOR . $values['file'];
					}
				}
				$keies = array_keys($grader);
				foreach(array("ttf", "otf", "woff") as $type)
				{
					if (file_exists($grader[$type]))
					{
				
						$reserves = getReserves(getRegionalFontName($row['font_id']));
						$css[] = "/** " .getRegionalFontName($row['font_id']) ." */";
						$css[] = "@font-face {";
						foreach($reserves['css'] as $tag => $value)
							$css[] = "\t$tag:\t\t'" .$value. "';";
							$css[] = "\tfont-family:\t\t'" .getRegionalFontName($row['font_id']). "';";
						foreach($files as $type => $values)
							$css[] = ($keies[0]==$values['type']?"\tsrc:\t\t":"\t\t\t")."url('./".$values['file']."') format('".$values['type']."')" .($keies[count($keies)-1]==$values['type']?";":",") ."\t\t/* Filesize: ". filesize($currently . DIRECTORY_SEPARATOR . $values['file']) . " bytes, md5: " . md5_file($currently . DIRECTORY_SEPARATOR . $values['file']) . " */";
							$css[] = "}";
						$css[] = "";
						$css[] = "/** " .$row['font_id'] ." */";
						$css[] = "@font-face {";
						foreach($reserves['css'] as $tag => $value)
							$css[] = "\t$tag:\t\t'" .$value. "';";
							$css[] = "\tfont-family:\t\t'" .$row['font_id']. "';";
						foreach($files as $type => $values)
							$css[] = ($keies[0]==$values['type']?"\tsrc:\t\t":"\t\t\t")."url('./".$values['file']."') format('".$values['type']."')" .($keies[count($keies)-1]==$values['type']?";":",") ."\t\t/* Filesize: ". filesize($currently . DIRECTORY_SEPARATOR . $values['file']) . " bytes, md5: " . md5_file($currently . DIRECTORY_SEPARATOR . $values['file']) . " */";
							$css[] = "}";							
						writeRawFile($currently . DIRECTORY_SEPARATOR . getRegionalFontName($row['font_id']) . ".css", implode("\n", $css));
						continue;
					}
				}
				chdir($currently);
				writeRawFile($currently . DIRECTORY_SEPARATOR . "font-resource.json", getArchivedZIPFile($zip, "font-resource.json", $row['font_id']));
				writeRawFile($currently . DIRECTORY_SEPARATOR . "LICENCE", getArchivedZIPFile($zip, "LICENCE", $row['font_id']));
				writeRawFile($currently . DIRECTORY_SEPARATOR . "file.diz", getArchivedZIPFile($zip, "file.diz", $row['font_id']));
				$packing = getArchivingShellExec();
				$stamping = getStampingShellExec();
				$cmd = (substr($packing[$state],0,1)!="#"?DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR:'') . str_replace("%filelist", "\"".implode("\" \"", $filelist)."\"", str_replace("%folder", "./", str_replace("%pack", $cachefile, str_replace("%commentfile", "./file.diz", (substr($packing[$state],0,1)!="#"?$packing[$state]:substr($packing[$state],1))))));
				$outt = shell_exec($cmd);
				if (isset($stamping[$state]) && file_exists($currently . DIRECTORY_SEPARATOR . "file.diz"))
				{
					$cmdb = str_replace("%pack", $cachefile, str_replace("%comment", $currently . DIRECTORY_SEPARATOR . "file.diz", $stamping[$state]));
					$outt = array(); exec($cmdb, $outt, $resolve);
				}
				$cmd = "rm -Rfv \"$currently\"";
				$outt = array(); exec($cmd, $outt);
			}
			if (file_exists($cachefile)) {	
				if(ini_get('zlib.output_compression')) {
					ini_set('zlib.output_compression', 'Off');
				}
				// Send Headers
				header('Content-Type: ' . getMimetype($state));
				header('Content-Disposition: attachment; filename="' . getRegionalFontName($row['font_id']) . '.'.$state.'"');
				header('Content-Transfer-Encoding: binary');
				header('Accept-Ranges: bytes');
				header('Cache-Control: private');
				header('Pragma: private');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				$data = file_get_contents($cachefile);
				die($data);
			} else {
				die("Failed Cache File for Download: $cachefile");
			}
			
		}
	}
}

if (!function_exists("getFontUFORawData")) {
	function getFontUFORawData($mode = '', $clause = '', $state = '', $output = '', $ufofile = '')
	{
		$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '$clause' OR `fingerprint` = '$clause')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `accessings` = `accessings` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts` SET `hits` = `hits` + 1 WHERE `id` = '" . $row['font_id'] . "'");
			$sql = "SELECT * from `fonts` WHERE `id` = '" . $row['font_id'] . "'";
			$font = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF($sql));
			switch($font['medium'])
			{
				case 'FONT_RESOURCES_CACHE':
				case 'FONT_RESOURCES_RESOURCE':
					
					if ($font['medium'] == 'FONT_RESOURCES_CACHE')
					{
						$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
						if (!file_exists(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						} else {
							if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
								$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
						}
						$zip = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['resource'];
						writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					} elseif ($font['medium'] == 'FONT_RESOURCES_RESOURCE')
					{
						if (!file_exists($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
						{
							mkdir(constant($font['medium']) . $row['path'], 0777, true);
							writeRawFile(constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf(FONT_RESOURCES_STORE, $row['path'] . DIRECTORY_SEPARATOR . $row['filename'])));
						}
					}
					$json = json_decode(getArchivedZIPFile($zip = constant($font['medium']) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
				case 'FONT_RESOURCES_PEER':
					$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
					if (!file_exists(constant(FONT_RESOURCES_CACHE) . $row['path'] . DIRECTORY_SEPARATOR . $row['filename']) && !isset($sessions[md5($font['path'] . DIRECTORY_SEPARATOR . $font['filename'])]))
					{
						$sql = "SELECT * FROM `peers` WHERE `peer-id` LIKE '%s'";
						if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($font['peer_id']))))==1)
						{
							$peer = $GLOBALS['FontsDB']->fetchArray($results);
							mkdir(constant("FONT_RESOURCES_CACHE") . $row['path'], 0777, true);
							writeRawFile(constant("FONT_RESOURCES_CACHE") . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], getURIData(sprintf($peer['api-uri'].$peer['api-uri-zip'], $row['font_id'])));
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])] = array("opened" => microtime(true), "dropped" => microtime(true) + mt_rand(3600 * 0.785, 3600 * 1.896), "resource" => $font['path'] . DIRECTORY_SEPARATOR . $font['filename']);
							$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_archiving` SET `cachings` = `cachings` + 1, `cached` = UNIX_TIMESTAMP() WHERE `id` = '" . $row['id'] . "'");
						}
					} else {
						if ($sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] < microtime(true) + ($next = mt_rand(1800*.3236, 2560*.5436)))
							$sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['dropped'] + $next;
					}
					$zip = $sessions[md5($row['path'] . DIRECTORY_SEPARATOR . $row['filename'])]['resource'];
					writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
					$json = json_decode(getArchivedZIPFile($zip = FONT_RESOURCES_CACHE . $row['path'] . DIRECTORY_SEPARATOR . $row['filename'], 'font-resource.json'), true);
					break;
			}
			$filez = $files = $folder = array();
			foreach($json['Files'] as $key => $file)
			{
				if (strpos(dirname($key), '.ufo'))
				{
					$parts = explode('.ufo/', dirname($key));
					if (strlen($state)==1 || empty($state) && !isset($parts[1]) && isset($parts[0]))
					{
						$files['.'][md5($key)] = basename($file); 
					} elseif ($parts[1] == substr($state, 0, strlen($state) - 1))
					{
						$folder[md5($parts[1])] = $parts[1];
						$files[$parts[1]][md5($key)] = basename($file);
					} elseif (isset($parts[1]))
					{
						$folder[md5($parts[1])] = $parts[1];
					}
				}
			}			
			if (strlen($state)==1 || empty($state)) 
			{
				$filez['parent'] = API_URL;
				if (is_array($files['.']))
				{
					$filez['root'] = API_URL."/v2/font/$clause/ufo.api";
					$filez['title'] = "$clause/ufo.api";
					foreach($files['.'] as $file)
					{
						$filez['files'][md5($file)]['name'] = $file;
						$filez['files'][md5($file)]['bytes'] = number_format(strlen(getArchivedZIPFile($zip, basename($file), true)),0);
					}
				}		
				$filez['folder'] = $folder;
			} elseif (substr($state, strlen($state)-1, 1) == "/")
			{
				$state = substr($state,0, strlen($state)-1);
				$filez['parent'] = API_URL."/v2/font/$clause/ufo.api";
				$filez['root'] = API_URL."/v2/font/$clause/ufo.api/$state";
				$filez['title'] = "ufo.api/$state";
				foreach($files[$state] as $key => $file)
				{
					$filez['files'][md5($file)]['name'] = $file;
					$filez['files'][md5($file)]['bytes'] = number_format(strlen(getArchivedZIPFile($zip, basename($file), true)),0);
				}
				$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `accessings` = `accessings` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `path` LIKE '" . dirname($state) . "' AND `font_id` = '".$row['font_id']."'");
			} elseif (substr($state, strlen($state)-1, 1) != "/" && strlen($state)) {
				$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `sourcings` = `sourcings` + 1, `sourced` = UNIX_TIMESTAMP() WHERE `filename` = '" . basename($state) . "' AND `path` LIKE '" . dirname($state) . "' AND `font_id` = '".$row['font_id']."'");
				return getArchivedZIPFile($zip, basename($state), $row['font_id']);
			}
		}
		$html = "<h1>Index of ".$filez['title']."</h1>\n";
		$html .= "<table>\n";
		$html .= "<tbody>";
		$html .= "<tr><th colspan=\"5\"><hr></th></tr>";
		$html .= "<tr><td valign=\"top\"><img src=\"".API_URL."/images/back.gif\" alt=\"[PARENTDIR]\"></td><td><a href=\"".$filez['parent']."\">Parent Directory</a></td><td>&nbsp;</td><td align=\"right\">  - </td><td>&nbsp;</td></tr>\n";
		if (isset($filez['folder']))
		{
			foreach($filez['folder'] as $md5 => $folder)
				$html .= "<tr><td valign=\"top\"><img src=\"".API_URL."/images/folder.gif\" alt=\"[DIR]\"></td><td><a href=\"".$filez['root']."/$folder/\">$folder/</a></td><td align=\"right\">".date("Y-m-d H:i:s")."</td><td align=\"right\">  - </td><td>&nbsp;</td></tr>\n";
		}
		if (isset($filez['files']))
		{
			foreach($filez['files'] as $md5 => $file)
				$html .= "<tr><td valign=\"top\"><img src=\"".API_URL."/images/text.gif\" alt=\"[FILE]\"></td><td><a href=\"".$filez['root']."/".$file['name']."\">".$file['name']."</a></td><td align=\"right\">".date("Y-m-d H:i:s")."</td><td align=\"right\">".$file['bytes']." bytes</td><td>&nbsp;</td></tr>\n";
		}
		$html .= "<tr><th colspan=\"5\"><hr></th></tr></tbody></table>\n";
		$html .= "<address>Fonts API/".API_VERSION." (".PHP_VERSION.") Server at ".parse_url("http://".$_SERVER["HTTP_HOST"], PHP_URL_HOST). " Port ".$_SERVER["SERVER_PORT"]."</address>\n";
		return $html;
	}
}


if (!function_exists("getSurveyFontRawData")) {
	function getSurveyFontRawData($mode = '', $clause = '', $output = '', $ufofile = '')
	{
		$row = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF("SELECT * FROM `uploads` WHERE `key` = '$clause'"));
		if (count($row)>0)
		{
			switch($output)
			{
				default:
					if (file_exists($row['currently_path'] . DIRECTORY_SEPARATOR . $clause . "." . $output))
						return file_get_contents($row['currently_path'] . DIRECTORY_SEPARATOR . $clause . "." . $output);
					break;
				case "ufo":
					if (file_exists($row['currently_path'] . DIRECTORY_SEPARATOR . $clause . "." . $output . DIRECTORY_SEPARATOR . $ufofile))
						return file_get_contents($row['currently_path'] . DIRECTORY_SEPARATOR . $clause . "." . $output . DIRECTORY_SEPARATOR . $ufofile);
					break;
			}
			
		}
	}
}

if (!function_exists("cleanResourcesCache")) {
	function cleanResourcesCache()
	{
		$sessions = unserialize(file_get_contents(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial"));
		foreach($sessions as $key => $values)
		{
			if ($values['dropped']<microtime(true))
			{
				unlink(FONT_RESOURCES_CACHE.$values['resource']);
				$path = constant("FONT_RESOURCES_CACHE") . ($subpath = dirname($values['resource']));
				foreach(explode(DIRECTORY_SEPARATOR, $subpath) as $folder)
				{
					rmdir($path);
					$path = dirname($path);
				}
				unset($sessions[$key]);
			}
		}
		writeRawFile(FONT_RESOURCES_CACHE . DIRECTORY_SEPARATOR . "file-store-sessions.serial", serialize($sessions));
		return true;
	}
}

if (!function_exists("getFontsByNodeListArray")) {
	function getFontsByNodeListArray($node_id = 0)
	{
		$fonts = array();
		$sql = "SELECT * from `nodes_linking` WHERE `node_id` = '$node_id'";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$sql = "SELECT * from `fonts` WHERE `font_id` = '".$row['font_id']."'";
			$fontages = $GLOBALS['FontsDB']->queryF($sql);
			while($font = $GLOBALS['FontsDB']->fetchArray($fontages))
			{
				$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '%s')";
				$archive = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF(sprintf($sql, $row['font_id'])));
				$files = array();
				$sql = "SELECT * from `fonts_files` WHERE (`font_id` = '%s')";
				$fresult = $GLOBALS['FontsDB']->queryF(sprintf($sql, $row['font_id']));
				while($row = $GLOBALS['FontsDB']->fetchArray($fresult))
					$files[$row['id']] = $row;
				$names = array();
				$sql = "SELECT * from `fonts_names` WHERE (`font_id` = '%s')";
				$nresult = $GLOBALS['FontsDB']->queryF(sprintf($sql, $row['font_id']));
				while($row = $GLOBALS['FontsDB']->fetchArray($nresult))
				{
					unset($row['upload_id']);
					$names[] = $row;
				}
				$fonts[$font['id']] = array('key' => $font['id'], 'peer-id' => $font['peer_id'], 'names' => $names, 'normal' => $font['normal'], 'italic' => $font['italic'], 'bold' => $font['bold'], 'condensed' => $font['condensed'], 'light' => $font['light'], 'semi' => $font['semi'], 'book' => $font['book'], 'body' => $font['body'], 'header' => $font['header'], 'heading' => $font['heading'], 'footer' => $font['footer'], 'graphic' => $font['graphic'], 'system' => $font['system'], 'block' => $font['block'], 'quote' => $font['quote'], 'message' => $font['message'], 'admin' => $font['admin'], 'logo' => $font['logo'], 'slogon' => $font['slogon'], 'legal' => $font['legal'], 'script' => $font['script'], 'medium' => $font['medium'], 'archive' => $archive, 'files'=> $files);
			}			
		}
		return $fonts;
	}
}

if (!function_exists("getNodesByFontListArray")) {
	function getNodesByFontListArray($font_id = '')
	{
		$nodes = array();
		$sql = "SELECT * from `nodes_linking` WHERE `font_id` = '$font_id'";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$ncity = $GLOBALS['FontsDB']->queryF("SELECT * from `nodes` WHERE `id` = '".$row['node_id']."'");
			while($node = $GLOBALS['FontsDB']->fetchArray($ncity))
			{
				$nodes[$node['type']][$node['node']] = array('node' => $node['node'], 'type' => $node['type'], 'usage' => $node['usage'], 'weight' => $node['weight'], 'fonts' => getFontsByNodeListArray($row['node_id']));
			}
		}
		return $nodes;
	}
}

if (!function_exists("getFontsIDsNodeStringArray")) {
	function getFontsIDsNodeStringArray()
	{
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . $GLOBALS['hourprev'] . '--' . date('d-M-Y') . '---font-identities.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . $GLOBALS['hourindx'] . '--' . date('d-M-Y') . '---font-identities.serial'))
		{
			$ncity = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts`");
			while($font = $GLOBALS['FontsDB']->fetchArray($ncity))
			{
				$fonts[getNodesByFontString($font['id'])] = $font['id'];
			}
			@writeRawFile($cache, serialize($fonts));
			return $fonts;
		}
		return unserialize(file_get_contents($cache));
	}
}

if (!function_exists("getFontsByNodesListArray")) {
	function getFontsByNodesListArray($node_id = '')
	{
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . $GLOBALS['hourprev'] . '--' . date('d-M-Y') . '---font-noding-'.$node_id.'.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . $GLOBALS['hourindx'] . '--' . date('d-M-Y') . '---font-noding-'.$node_id.'.serial'))
		{
			try {
			
				$font_ids = $fonts = array();
				$sql = "SELECT * from `nodes_linking` WHERE `node_id` = '$node_id'";
				$result = $GLOBALS['FontsDB']->queryF($sql);
				while($row = $GLOBALS['FontsDB']->fetchArray($result))
				{
					$font_ids[$row['font_id']] = $row['font_id'];
				}
				$ncity = $GLOBALS['FontsDB']->queryF($sql = "SELECT * from `fonts` WHERE `id` IN ('".implode("','", $font_ids)."')");
				while($font = $GLOBALS['FontsDB']->fetchArray($ncity))
				{
					try {
						$downloads = array();
						foreach(getArchivingShellExec() as $type => $exec)
							$downloads[$type] = API_URL ."/v2/data/".$font['id']."/".$type."/download.api";
						$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '%s')";
						$archive = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF(sprintf($sql, $font['id'])));
						$names = $files = array();
						$sql = "SELECT * from `fonts_files` WHERE (`font_id` = '%s')";
						$fresult = $GLOBALS['FontsDB']->queryF(sprintf($sql, $font['id']));
						while($row = $GLOBALS['FontsDB']->fetchArray($fresult))
							$files[$row['id']] = $row;
						$sql = "SELECT * from `fonts_names` WHERE (`font_id` = '%s')";
						$nresult = $GLOBALS['FontsDB']->queryF(sprintf($sql, $font['id']));
						while($row = $GLOBALS['FontsDB']->fetchArray($nresult))
						{
							unset($row['upload_id']);
							$names[] = $row;
						}
						$fonts[$font['id']] = array('key' => $font['id'], 'peer-id' => $font['peer_id'], 'names' => $names, 'normal' => $font['normal'], 'italic' => $font['italic'], 'bold' => $font['bold'], 'condensed' => $font['condensed'], 'light' => $font['light'], 'semi' => $font['semi'], 'book' => $font['book'], 'body' => $font['body'], 'header' => $font['header'], 'heading' => $font['heading'], 'footer' => $font['footer'], 'graphic' => $font['graphic'], 'system' => $font['system'], 'quote' => $font['quote'], 'block' => $font['block'], 'message' => $font['message'], 'admin' => $font['admin'], 'logo' => $font['logo'], 'slogon' => $font['slogon'], 'legal' => $font['legal'], 'script' => $font['script'], 'medium' => $font['medium'], 'node-string' => getNodesByFontString($font['id']), 'download-urls' => $downloads, 'archive' => $archive, 'files' => $files);
					}
					catch (Exception $error)
					{
						trigger_error($error, E_RECOVERABLE_ERROR);
					}
				}
			}
			catch (Exception $error)
			{
				die($error);
			}
			@writeRawFile($cache, serialize($fonts));
			return $fonts;
		}
		return unserialize(file_get_contents($cache));
	
	}
}

if (!function_exists("getRandomFontsFromStringList")) {
	function getRandomFontsFromStringList($nodestring = '', $normal = '', $bold = '', $italic = '', $condensed = '')
	{
		$fonts_id = $nodes_id = array();
		foreach(getFontsIDsNodeStringArray() as $nodestr => $key)
		{
			if (strpos(' '.$nodestring, $nodestr))
			{
				$fonts_id[$key] = $key;
				$nodestring = sef(str_replace($nodestr, '', $nodestring));
			}
		}
		$sql = "SELECT * from `nodes` WHERE `node` IN ('".str_replace("-", "','", $nodestring). "')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$nodes_id[$row['id']] = $row['id'];
		}
		$sql = "SELECT * from `nodes_linking` WHERE `node_id` IN ('".implode("','", $nodes_id)."') ORDER BY RAND()";
		$nodocity = $GLOBALS['FontsDB']->queryF($sql);
		while($font = $GLOBALS['FontsDB']->fetchArray($nodocity))
		{
			$fonts_id[$font['font_id']] = $font['font_id'];
		}
		$sql = "SELECT * from `fonts` WHERE `id` IN ('".implode("','", $fonts_id)."') " . (!empty($normal)?" AND `normal` = $normal":"") . (!empty($normal)?" AND `bold` = $bold":"") . (!empty($italic)?" AND `italic` = $italic":"") . (!empty($condensed)?" AND `condensed` = $condensed":"") . " ORDER BY RAND() LIMIT 1";
		$fonteo = $GLOBALS['FontsDB']->queryF($sql);
		while($fontee = $GLOBALS['FontsDB']->fetchArray($fonteo))
		{
			return $fontee;
		}
		return false;
	}
}

if (!function_exists("getRandomFontsIDFromNodesList")) {
	function getRandomFontsIDFromNodesList($nodestring = '', $toponly = false)
	{
		$fonts_id = $node_id = array();
		foreach(getFontsIDsNodeStringArray() as $nodestr => $key)
		{
			if (strpos(' '.$nodestring, $nodestr))
			{
				foreach(getNodesByFontString($key) as $nod_id => $string)
					$node_id[$nod_id] = $row[$nod_id];
				$nodestring = sef(str_replace($nodestr, '', $nodestring));
			}
		}
		$sql = "SELECT * from `nodes` WHERE `node` IN ('".str_replace("-", "','", $nodestring). "')";
		$result = $GLOBALS['FontsDB']->queryF($sql);
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			$node_id[$row['node_id']] = $row['node_id'];
		}
		$sql = "SELECT * from `nodes_linking` WHERE `node_id` IN ('".implode("','", $node_id)."') ORDER BY RAND() LIMIT 1";
		$nodocity = $GLOBALS['FontsDB']->queryF($sql);
		while($node = $GLOBALS['FontsDB']->fetchArray($nodocity))
		{
			return $node['font_id'];
		}
		return '';
	}
}

if (!function_exists("getFontsIDsFromNodesList")) {
	function getFontsIDsFromNodesList($nodestring = '', $toponly = false)
	{
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H", time() - 3600 *24 * 7) . 'fonts-identities-by-string--' . sha1($nodestring).'.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H") . 'fonts-identities-by-string--' . sha1($nodestring).'.serial'))
		{
			$node_ids = $ids = array();
			foreach(getFontsIDsNodeStringArray() as $nodestr => $key)
			{
				if (strpos(' '.$nodestring, $nodestr))
				{
					if (!isset($ids[$key]))
						$ids[$key] = array('key' => $key, 'count' => 1);
					else
						$ids[$key]['count']++;
					$nodestring = sef(str_replace($nodestr, '', $nodestring));
				}
			}
			$sql = "SELECT * from `nodes` WHERE `node` IN ('".str_replace("-", "','", $nodestring). "')";
			$result = $GLOBALS['FontsDB']->queryF($sql);
			while($row = $GLOBALS['FontsDB']->fetchArray($result))
			{
				$node_ids[$row['id']] = $row['id'];
			}
			$sql = "SELECT * from `nodes_linking` WHERE `node_id` IN ('".implode( "','", $node_ids). "')";
			$nodocity = $GLOBALS['FontsDB']->queryF($sql);
			while($node = $GLOBALS['FontsDB']->fetchArray($nodocity))
			{
				if (!isset($ids[$node['font_id']]))
					$ids[$node['font_id']] = array('key' => $node['font_id'], 'count' => 1);
				else
					$ids[$node['font_id']]['count']++;
			}
			@writeRawFile($cache, serialize($ids));
		}
		else
			$ids = unserialize(file_get_contents($cache));
		$count = 0;
		if ($toponly==false)
			return array_keys($ids);
		else
			foreach($ids as $id => $data)
			{
				if ($count<$data['count'])
				{
					$count=$data['count'];
					$idkey = $id;
				}
			}
		return $idkey;
	}
}

if (!function_exists("getNodesByFontString")) {
	function getNodesByFontString($font_id = '')
	{
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H", time() - 3600 *24 * 7) . 'nodes-by-font--' . sha1($font_id).'.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H") . 'nodes-by-font--' . sha1($font_id).'.serial'))
		{
			$nodes = array();
			try 
			{
				$sql = "SELECT * from `nodes_linking` WHERE `font_id` = '$font_id'";
				$result = $GLOBALS['FontsDB']->queryF($sql);
				while($row = $GLOBALS['FontsDB']->fetchArray($result))
				{
					$sql = "SELECT * from `nodes` WHERE `id` = '".$row['node_id']."' AND `type` = 'keys'";
					$nodocity = $GLOBALS['FontsDB']->queryF($sql);
					while($node = $GLOBALS['FontsDB']->fetchArray($nodocity))
					{
						$nodes[$node['id']] = $node['node'];
					}
				}
				sort($nodes);
				@writeRawFile($cache, serialize($nodes));
			}
			catch (Exception $error)
			{
				die($error);
			}
			return implode("-", $nodes);
		}
		return implode("-", unserialize(file_get_contents($cache)));
	}
}

if (!function_exists("getFontsListArray")) {
	function getFontsListArray($clause = 'all', $output = '', $state = '')
	{

		if ($clause == 'all' && $state = '' && file_exists($file = FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . 'fonts-all.json'))
		{
			return json_decode(file_get_contents($file), true);
		}
		if ($state=='cron')
		{
			$state = '';
		}
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H", time() - 3600 *24 * 7) . 'fonts-listing--' . sha1($clause.$state).'.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H") . 'fonts-listing--' . sha1($clause.$state).'.serial'))
		{
			$limits = "";
			if (strpos($state, '-'))
			{
				$parts = explode("-", $state);
				$limits = " LIMIT " . $parts[0] . ", " . $parts[1];
			}
			$local = '';
			$fonts = array();
			switch ($clause)
			{
				case "all":
					if (!empty($local))
						$local = " WHERE $local";
					$sql = "SELECT * from `fonts`$local ORDER BY `nodes` DESC$limits";
					break;
				default:
					if (!empty($local))
						$local = " AND $local";
					$sql = "SELECT * from `fonts` WHERE `id` IN ('".implode("','", @getFontsIDsFromNodesList($clause, false))."')$local ORDER BY `nodes` DESC$limits";
					break;
			}
			$timelimit = 120;
			$result = $GLOBALS['FontsDB']->queryF($sql);
			while($font = $GLOBALS['FontsDB']->fetchArray($result))
			{
				
				set_time_limit($timelimit=$timelimit+6);
				try {
					$downloads = array();
					foreach(getArchivingShellExec() as $type => $exec)
						$downloads[$type] = API_URL ."/v2/data/".$font['id']."/".$type."/download.api";
					$sql = "SELECT * from `fonts_archiving` WHERE (`font_id` = '%s')";
					$archive = $GLOBALS['FontsDB']->fetchArray($GLOBALS['FontsDB']->queryF(sprintf($sql, $font['id'])));
					$names = $files = array();
					$sql = "SELECT * from `fonts_files` WHERE (`font_id` = '%s')";
					$fresult = $GLOBALS['FontsDB']->queryF(sprintf($sql, $font['id']));
					while($row = $GLOBALS['FontsDB']->fetchArray($fresult))
						$files[$row['id']] = $row;
					$sql = "SELECT * from `fonts_names` WHERE (`font_id` = '%s')";
					$nresult = $GLOBALS['FontsDB']->queryF(sprintf($sql, $font['id']));
					while($row = $GLOBALS['FontsDB']->fetchArray($nresult))
					{
						unset($row['upload_id']);
						$names[] = $row;
					}
					$fonts[$font['id']] = array('key'=> $font['id'], 'peer-id' => $font['peer_id'], 'names' => $names, 'normal' => $font['normal'], 'italic' => $font['italic'], 'bold' => $font['bold'], 'condensed' => $font['condensed'], 'light' => $font['light'], 'semi' => $font['semi'], 'book' => $font['book'], 'body' => $font['body'], 'header' => $font['header'], 'heading' => $font['heading'], 'footer' => $font['footer'], 'graphic' => $font['graphic'], 'system' => $font['system'], 'quote' => $font['quote'], 'block' => $font['block'], 'message' => $font['message'], 'admin' => $font['admin'], 'logo' => $font['logo'], 'slogon' => $font['slogon'], 'legal' => $font['legal'], 'script' => $font['script'], 'medium' => $font['medium'], 'nodes' => getNodesByFontListArray($font['id']), 'node-string' => getNodesByFontString($font['id']), 'download-urls' => $downloads, 'archive' => $archive, 'files' => $files);
				}
				catch (Exception $error)
				{
					trigger_error($error, E_RECOVERABLE_ERROR);
				}
			}
			@writeRawFile($cache, serialize($fonts));
			return $fonts;
		}
		return unserialize(file_get_contents($cache));
	}
}

if (!function_exists("getNodesListArray")) {
	function getNodesListArray($clause = 'all', $output = '', $state = '')
	{
	
		if ($clause == 'all' && $state = '' && file_exists($file = FONT_RESOURCES_RESOURCE . DIRECTORY_SEPARATOR . 'nodes-all.json'))
		{
			return json_decode(file_get_contents($file), true);
		}
		if ($state=='cron')
			$state = '';
		if (file_exists($unlink = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H", time() - 3600 *24 * 7) . 'nodes-listing--' . sha1($clause.$state).'.serial'))
			unlink($unlink);
		if (!file_exists($cache = FONTS_CACHE . DIRECTORY_SEPARATOR . date("Y-m-W-H") . 'nodes-listing--' . sha1($clause.$state).'.serial'))
		{
			$limits = "";
			if (strpos($state, '-'))
			{
				$parts = explode("-", $state);
				$limits = " LIMIT " . $parts[0] . ", " . $parts[1];
			}
			$nodes = array();
			switch ($clause)
			{
				default:
					$sql = "SELECT * from `nodes` ORDER BY `weight`, `usage` DESC".$limits;
					break;
				case "keys":
				case "fixes":
				case "typal":
					$sql = "SELECT * from `nodes` WHERE `type` = '$clause' ORDER BY `weight`, `usage` DESC".$limits;
					break;
			}
			$timelimit = 120;
			$result = $GLOBALS['FontsDB']->queryF($sql);
			while($row = $GLOBALS['FontsDB']->fetchArray($result))
			{
				set_time_limit($timelimit=$timelimit+6);
				try {
					$nodes[$row['node']] = array('node'=> $row['node'], 'usage' => $row['usage'], 'fonts' => getFontsByNodesListArray($row['id']));
				}
				catch (Exception $error)
				{
					trigger_error($error, E_RECOVERABLE_ERROR);
				}
				
			}
			@writeRawFile($cache, serialize($nodes));
			return $nodes;
		}
		return unserialize(file_get_contents($cache));
	}
}

if (!function_exists("getMimetype")) {
	function getMimetype($type = '')
	{
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `mimetypes` WHERE `type` LIKE '$type'");
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
			return $row['mimetype'];
		return 'text/html';
	}
}

if (!function_exists("getExampleNodes")) {
	function getExampleNodes()
	{
		$nodes = array();
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `nodes` WHERE LENGTH(`nodes`.`node`) > 3 ORDER BY RAND() LIMIT " . mt_rand(3,6));
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
			$nodes[] = $row['node'];
		sort($nodes);
		return implode('-', $nodes);
	}
}

if (!function_exists("getExampleFingerprint")) {
	function getExampleFingerprint()
	{
		$nodes = array();
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `fonts` ORDER BY RAND() LIMIT 1");
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
		{
			return $row;
		}
	}
}

if (!function_exists("getExampleFontFiles")) {
	function getExampleFontFiles($md5 = '')
	{
		$fonts = array();
		$result = $GLOBALS['FontsDB']->queryF("SELECT * from `fonts_archiving` WHERE `font_id` = '$md5'");
		while($row = $GLOBALS['FontsDB']->fetchArray($result))
			if (!empty($row['type']))
				$fonts[$row['type']] = $row;
		return $fonts;
	}
}

if (!function_exists("getArchivedZIPFile")) {
	function getArchivedZIPFile($zip_resource = '', $zip_file = '', $fontid = '')
	{
		if (!empty($fontid))
			$GLOBALS['FontsDB']->queryF($sql = "UPDATE `fonts_files` SET `hits` = `hits` + 1, `accessed` = UNIX_TIMESTAMP() WHERE `font_id` = '" . $fontid . "' AND `filename` = '$zip_file'");
		$data = '';
 		$zip = zip_open($zip_resource);
        if ($zip) {
        	while ($zip_entry = zip_read($zip)) {
            	if (strpos('  '.strtolower(zip_entry_name($zip_entry)), strtolower($zip_file)))
                	if (zip_entry_open($zip, $zip_entry, "r")) {
                    	$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                        zip_entry_close($zip_entry);
                        continue;
                        continue;
                    }
            }
            zip_close($zip);
         }
         return $data;
		
	}
}

if (!function_exists('sef'))
{

	/**
	 * Safe encoded paths elements
	 *
	 * @param unknown $datab
	 * @param string $char
	 * @return string
	 */
	function sef($value = '', $stripe ='-')
	{
		$value = str_replace('&', 'and', $value);
		$value = str_replace(array("'", '"', "`"), 'tick', $value);
		$replacement_chars = array();
		$accepted = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","m","o","p","q",
				"r","s","t","u","v","w","x","y","z","0","9","8","7","6","5","4","3","2","1");
		for($i=0;$i<256;$i++){
			if (!in_array(strtolower(chr($i)),$accepted))
				$replacement_chars[] = chr($i);
		}
		$result = (str_replace($replacement_chars, $stripe, ($value)));
		while(substr($result, 0, strlen($stripe)) == $stripe)
			$result = substr($result, strlen($stripe), strlen($result) - strlen($stripe));
		while(substr($result, strlen($result) - strlen($stripe), strlen($stripe)) == $stripe)
			$result = substr($result, 0, strlen($result) - strlen($stripe));
		while(strpos($result, $stripe . $stripe))
			$result = str_replace($stripe . $stripe, $stripe, $result);
		return(strtolower($result));
	}
}

if (!function_exists("getSubPaths")) {
	function cleanPath($path = '')
	{
		$folders = array();
		foreach(explode(DIRECTORY_SEPARATOR, $path) as $path)
			$folders[] = sef($path);
		return implode(DIRECTORY_SEPARATOR, $folders);
	}
}

if (!function_exists("getSubPaths")) {
	function getSubPaths($path = '', $paths = array())
	{
		foreach(getDirListAsArray($path) as $dir)
		{
			$paths[$dir] = $dir;
			foreach(getSubPaths($dir, $paths) as $dirb)
			{
				$paths[$dirb] = $dirb;
			}
		}
		return $paths;
	}
}

if (!function_exists("spacerName")) {
	/**
	 * checkEmail()
	 *
	 * @param mixed $email
	 * @return bool|mixed
	 */
	function spacerName($name = '')
	{
		$name = str_replace(array('-', ':', ',', '<', '>', ';', '+', '_', '(', ')', '[', ']', '{', '}', '='), ' ', $name);
		$nname = '';
		$previous = $last = '';
		for($i=0; $i<strlen($name); $i++)
		{
			if (substr($name, $i, 1)==strtoupper(substr($name, $i, 1)) && $last==strtolower($last))
			{
				$nname .= ' ' . substr($name, $i, 1); 
			} else 
				$nname .= substr($name, $i, 1);
			$last=substr($name, $i, 1);
		}
		while(strpos($nname, '  ')>0)
			$nname = str_replace('  ', ' ', $nname);
		return trim(implode(' ', array_unique(explode(' ', $nname))));
	}
}

if (!function_exists("redirect")) {
	/**
	 * checkEmail()
	 *
	 * @param mixed $email
	 * @return bool|mixed
	 */
	function redirect($uri = '', $seconds = 9, $message = '')
	{
		$GLOBALS['url'] = $uri;
		$GLOBALS['time'] = $seconds;
		$GLOBALS['message'] = $message;
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'redirect.php';
		exit(-1000);
	}
}

if (!function_exists("checkEmail")) {
	/**
	 * checkEmail()
	 *
	 * @param mixed $email
	 * @return bool|mixed
	 */
	function checkEmail($email)
	{
		if (!$email || !preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
			return false;
		}
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
				return false;
			}
		}
		if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
					return false;
				}
			}
		}
		return $email;
	}
}

if (!function_exists("writeRawFile")) {
	function writeRawFile($file = '', $data = '')
	{
		if (!is_dir(dirname($file)))
			mkdir(dirname($file), 0777, true);
		if (is_file($file))
			unlink($file);
		file_put_contents($file, $data);
		if (!strpos($file, 'caches-files-sessioning.serial') && strpos($file, '.serial'))
		{
			
			if (file_exists(FONTS_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial'))
				$sessions = unserialize(file_get_contents(FONTS_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial'));
			else
				$sessions = array();
			if (!isset($sessions[basename($file)]))
				$sessions[basename($file)] = array('file' => $file, 'till' =>microtime(true) + mt_rand(3600*24*7.35,3600*24*14*8.75));
			foreach($sessions as $file => $values)
				if ($values['till']<time() && isset($values['till']))
				{
					if (file_exists($values['file']))
						unlink($values['file'])	;
					unset($sessions[$file]);
				}
			file_put_contents(FONTS_CACHE . DIRECTORY_SEPARATOR . 'caches-files-sessioning.serial', serialize($sessions));
		}
	}
}

if (!function_exists("getArchivedZIPContentsArray")) {
	function getArchivedZIPContentsArray($zip_file = '')
	{
		$zip = zip_open($zip_file);
		$files = array();
		if ($zip) {
			while ($zip_entry = zip_read($zip)) {
				if (zip_entry_open($zip, $zip_entry, "r")) {
					$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					$type = '';
					$parts = explode(".", basename(zip_entry_name($zip_entry)));
					$type = $parts[count($parts)-1];
					$files[md5($data)] = array('filename' => basename(zip_entry_name($zip_entry)), 'path' => dirname(zip_entry_name($zip_entry)), 'bytes' => strlen($data), 'type' => $type);
					zip_entry_close($zip_entry);
				}
			}
			zip_close($zip);
		}
		return $files;
	}
}

if (!function_exists("compileNodesArray")) {
	function compileNodesArray($dirname, $weight = 2)
	{
		static $nodes = array();
		if (!isset($nodes[$dirname]))
		{
			foreach(getCompleteDirListAsArray($dirname) as $path)
			{
				$parts = array_unique(explode(DIRECTORY_SEPARATOR, str_replace(array("_", "-", ".", ","), DIRECTORY_SEPARATOR, str_replace($dirname, '', $path))));
				if (count($parts)>=$weight)
				{
					foreach($parts as $parter)
					{
						foreach(getNodeNumeracy($parter) as $part)
						{
							foreach(getFontTypes() as $type => $types)
								if (in_array($part, $types))
									$part = $type;
							if (strlen($part) < 3)
								$typal = 'typal';
							else
								$typal = 'keys';
							if (isset($nodes[$typal][$part]))
								$nodes[$dirname][$typal][$part]++;
							else
								$nodes[$dirname][$typal][$part] = 1;
							if ($lastlen<=2 && strlen($part))
							{
								if (isset($nodes['fixes'][substr($part, strlen($part)-4)]))
									$nodes[$dirname]['fixes'][substr($part, strlen($part)-4)]++;
								else
									$nodes[$dirname]['fixes'][substr($part, strlen($part)-4)] = 1;
								if (isset($nodes['fixes'][substr($part, strlen($part)-3)]))
									$nodes[$dirname]['fixes'][substr($part, strlen($part)-3)]++;
								else
									$nodes[$dirname]['fixes'][substr($part, strlen($part)-3)] = 1;
							}
							$lastlen = strlen($part);
						}
					}
				}
			}
		}
		return $nodes[$dirname];
	}

}

if (!function_exists("getNodesArray")) {
	function getNodesArray($parters = array(), $fixes = array())
	{
		$ret = array();
		foreach($parters as $part)
		{
			$part = str_replace(array("_", "-", "'", "\"", "\\", "/", "~", "`"), " ", $part);
			foreach(array('typal', 'keys', 'fixes')  as $typal)
			{
				switch ($typal) {
					case 'typal':
						$node = array();
						foreach(explode(" ", $part) as $component)
						{
							$component = strtolower($component);
							if (strlen($component)>2)
							{
								$node[substr($component, 0, 2)][] = 1;
								$node[substr($component, strlen($component)-2, 2)][] = 1;
							}
						}
						foreach(explode(" ", $part) as $component)
						{
							$component = strtolower($component);
							if (strlen($component)>3)
							{
								$node[substr($component, 0, 3)][] = 1;
								$node[substr($component, strlen($component)-3, 3)][] = 1;
							}
						}						
						break;
					case 'keys':
						$node = array();
						foreach(explode(" ", $part) as $component)
						{
							$component = strtolower($component);
							if (strlen($component)>4)
							{
								$node[substr($component, 0, 4)][] = 1;
								$node[substr($component, strlen($component)-4, 4)][] = 1;
							}
						}
						foreach(explode(" ", $part) as $component)
						{
							$component = strtolower($component);
							if (strlen($component)>5)
							{
								$node[substr($component, 0, 5)][] = 1;
								$node[substr($component, strlen($component)-5, 5)][] = 1;
							}
						}
						break;
					case 'fixes':
						$node = array();
						foreach(explode(" ", $part) as $component)
						{
							$component = strtolower($component);
							$node[$component][] = 1;
						}
						foreach($fixes as $component => $values)
						{
							foreach($values as $value)
								$node[strtolower($value)][] = 1;
						}
						break;
				}
				foreach( $node as $component => $values)
				{
					$ret[$typal][$component] = count($values);
				}
			}
		}
		return $ret;
	}
}


if (!function_exists("writeFontResourceHeader")) {
	function writeFontResourceHeader($font, $licence = 'gpl3', $values = array())
	{
		$baseheader = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $licence . DIRECTORY_SEPARATOR . strtoupper(API_BASE) . '-HEADER'));
		if (count($baseheader)>0)
		{
			$stoptxt = '';
			foreach(array_reverse(array_keys($baseheader)) as $key)
				if (strlen(trim($baseheader[$key]))>0 && empty($stoptxt))
					$stoptxt = $baseheader[$key];

			$output = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $licence . DIRECTORY_SEPARATOR . strtoupper(API_BASE) . '-HEADER');
			$buffer = false;
			foreach($file($font) as $line)
			{
				if ($buffer == true)
					$output[] = $line;
				elseif (substr($line, 0, strlen($stoptxt)) == $stoptxt)
					$buffer = true;
			}
			$data = implode("", $output);
			if (file_exists($licfile = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $licence . DIRECTORY_SEPARATOR . 'LICENCE'))
			{
				$licence = cleanWhitespaces(file($licfile));
				$ccp = '';
				foreach($licence as $line)
					if (!empty($ccp))
						$ccp .= "% $line\n";
					else 
						$ccp = "$line\n";
				$ccp .= "% ----------------------------------------------------------------------------\n";
				$data = str_replace('%%fontcopyright%%', $ccp, $data);
				$data = str_replace('%%%fontcopyright%%%', implode("\010", $licence), $data);
				$data = str_replace('%fontcompany%', $values['company'], $data);
				$data = str_replace('%fontuploaddate%', date("YYYYmmdd", $values['uploaded']), $data);
				$data = str_replace('%apiurl%', API_URL, $data);
			} elseif (file_exists($licfile = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . 'cc' . DIRECTORY_SEPARATOR . 'LICENCE')) {	
				$licence = cleanWhitespaces(file($licfile));
				$ccp = '';
				foreach($licence as $line)
					if (!empty($ccp))
						$ccp .= "% $line\n";
					else
						$ccp = "$line\n";
				$ccp .= "% ----------------------------------------------------------------------------\n";
				$data = str_replace('%%fontcopyright%%', $ccp, $data);
				$data = str_replace('%%%fontcopyright%%%', implode("\010", $licence), $data);
				$data = str_replace('%fontcompany%', $values['company'], $data);
				$data = str_replace('%fontuploaddate%', date("YYYYmmdd", $values['uploaded']), $data);
				$data = str_replace('%apiurl%', API_URL, $data);
			}
			foreach($values as $key => $value)
			{
				switch($key)
				{
					case 'title':
						$data = str_replace('%fontnamespaced%', sef(str_replace(" ", "", $value)), $data);
						$data = str_replace('%fontname%', $value, $data);
						break;
					case 'version':
						$data = str_replace('666.666', $value, $data);
						break;
					case 'date':
						$data = str_replace('%fontdate%', $value, $data);
						break;
					case 'creator':
						$data = str_replace('%fontcreator%', $value, $data);
						break;
					case 'type':
						$data = str_replace('%fonttype%', $value, $data);
						break;
					case 'matrix':
						$data = str_replace('%fontmatrix%', $value, $data);
						break;
					case 'bbox':
						$data = str_replace('%fontbbox%', $value, $data);
						break;
					case 'painttype':
						$data = str_replace('%fontpainttype%', $value, $data);
						break;
					case 'info':
						$data = str_replace('%fontinfo%', $value, $data);
						break;
					case 'family':
						$data = str_replace('%fontfamilyname%', $value, $data);
						break;
					case 'weight':
						$data = str_replace('%fontweight%', $value, $data);
						break;
					case 'fstype':
						$data = str_replace('%fontfstype%', $value, $data);
						break;
					case 'italicangle':
						$data = str_replace('%fontitalicangle%', $value, $data);
						break;
					case 'fixedpitch':
						$data = str_replace('%fontfixedpitch%', $value, $data);
						break;
					case 'underlineposition':
						$data = str_replace('%fontunderline%', $value, $data);
						break;
					case 'underlinethickness':
						$data = str_replace('%fontunderthickness%', $value, $data);
						break;
				}
			}				
			writeRawFile($font, $data);
		}
	}
}



if (!function_exists("writeFontRepositoryHeader")) {
	function writeFontRepositoryHeader($font, $licence = 'gpl3', $values = array())
	{
		$baseheader = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $licence . DIRECTORY_SEPARATOR . strtoupper(API_BASE) . '-HEADER'));
		if (count($baseheader)>0)
		{
			$stoptxt = '';
			foreach(array_reverse(array_keys($baseheader)) as $key)
				if (strlen(trim($baseheader[$key]))>0 && empty($stoptxt))
					$stoptxt = $baseheader[$key];
				
			$output = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'licences' . DIRECTORY_SEPARATOR . $licence . DIRECTORY_SEPARATOR . strtoupper(API_BASE) . '-HEADER');
			$buffer = false;
			foreach(file($font) as $line)
			{
				if ($buffer == true)
					$output[] = $line;
				elseif (substr($line, 0, strlen($stoptxt)) == $stoptxt)
					$buffer = true;
			}
			$data = implode("", $output);
			writeRawFile($font, $data);
		}
	}
}

if (!function_exists("getFontPreviewText")) {
	function getFontPreviewText()
	{
		static $text = '';
		if (empty($text))
		{
			$texts = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'preview-texts.diz'));
			shuffle($texts); shuffle($texts); shuffle($texts); shuffle($texts);
			while(empty($text))
				$text = $texts[mt_rand(0, count($texts)-1)];
		}
		return $text;
	}
}

if (!function_exists("getBaseFontValueStore")) {
	function getBaseFontValueStore($font)
	{
		$result = array('uploaded' => microtime(true), 'licence' => API_LICENCE);
		if (file_exists($font))
		foreach(file($font) as $line)
		{
			$line = str_replace(array("/n", "/r", "\n", "\r", "\t", "/t"), '', $line);
			if (substr($line,0, $from = strlen('%%Title: ')) == '%%Title: ')
			{
				$result['title'] = trim(substr($line, $from, strlen($line) - $from));
			} elseif (substr($line,0, $from = strlen('%Version: ')) == '%Version: ')
			{
				$result['version'] = floatval(trim(substr($line, $from, strlen($line) - $from)));
			} elseif (substr($line,0, $from = strlen('%%CreationDate: ')) == '%%CreationDate: ')
			{
				$result['date'] = trim(substr($line, $from, strlen($line) - $from));
			} elseif (substr($line,0, $from = strlen('%%Creator: ')) == '%%Creator: ')
			{
				$result['creator'] = trim(substr($line, $from, strlen($line) - $from));
			} elseif (substr($line,0, $from = strlen('/FontType ')) == '/FontType ')
			{
				$result['type'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen('/FontMatrix [')) == '/FontMatrix [')
			{
				$result['matrix'] = trim(substr($line, $from, strlen($line) - $from - strlen(' ]readonly def')));
			} elseif (substr($line,0, $from = strlen('/FontName /')) == '/FontName /')
			{
				$result['named'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen('/FontBBox {')) == '/FontBBox { ')
			{
				$result['bbox'] = trim(substr($line, $from, strlen($line) - $from - strlen(' }readonly def')));
			} elseif (substr($line,0, $from = strlen('/PaintType ')) == '/PaintType ')
			{
				$result['painttype'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen('/FontInfo ')) == '/FontInfo ')
			{
				$result['info'] = trim(substr($line, $from, strlen($line) - $from - strlen(' begin')));
			} elseif (substr($line,0, $from = strlen(' /FullName (')) == ' /FullName (')
			{
				$result['name'] = trim(substr($line, $from, strlen($line) - $from - strlen(') readonly def')));
			} elseif (substr($line,0, $from = strlen(' /FamilyName (')) == ' /FamilyName (')
			{
				$result['family'] = trim(substr($line, $from, strlen($line) - $from - strlen(') readonly def')));
			} elseif (substr($line,0, $from = strlen(' /Weight (')) == ' /Weight (')
			{
				$result['weight'] = trim(substr($line, $from, strlen($line) - $from - strlen(') readonly def')));
			} elseif (substr($line,0, $from = strlen(' /FSType ')) == ' /FSType ')
			{
				$result['fstype'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen(' /ItalicAngle ')) == ' /ItalicAngle ')
			{
				$result['italicangle'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen(' /isFixedPitch  ')) == ' /isFixedPitch ')
			{
				$result['fixedpitch'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen(' /UnderlinePosition ')) == ' /UnderlinePosition ')
			{
				$result['underlineposition'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen(' /UnderlineThickness ')) == ' /UnderlineThickness ')
			{
				$result['underlinethickness'] = trim(substr($line, $from, strlen($line) - $from - strlen(' def')));
			} elseif (substr($line,0, $from = strlen('end readonly def')) == 'end readonly def')
			{
				return $result;
			}
		}
		return $result;
	}
}


if (!function_exists("deleteFilesNotListedByArray")) {
	function deleteFilesNotListedByArray($dirname, $skipped = array())
	{
		foreach(array_reverse(getCompleteFilesListAsArray($dirname)) as $file)
		{
			$found = false;
			foreach($skipped as $skip)
				if (strtolower(substr($file, strlen($file)-strlen($skip)))==strtolower($skip))
					$found = true;
			if ($found == false)
			{
				unlink($file);
				rmdir(dirname($file));
			}
		}
	}

}

if (!function_exists("getCompleteFilesListAsArray")) {
	function getCompleteFilesListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
			foreach(getFileListAsArray($path) as $file)
				$result[$path.DIRECTORY_SEPARATOR.$file] = $path.DIRECTORY_SEPARATOR.$file;
		return $result;
	}

}


if (!function_exists("getCompleteDirListAsArray")) {
	function getCompleteDirListAsArray($dirname, $result = array())
	{
		$result[$dirname] = $dirname;
		foreach(getDirListAsArray($dirname) as $path)
		{
			$result[$dirname . DIRECTORY_SEPARATOR . $path] = $dirname . DIRECTORY_SEPARATOR . $path;
			$result = getCompleteDirListAsArray($dirname . DIRECTORY_SEPARATOR . $path, $result);
		}
		return $result;
	}
	
}

if (!function_exists("getCompleteZipListAsArray")) {
	function getCompleteZipListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
		{
			foreach(getZipListAsArray($path) as $file)
				$result[md5_file($path . DIRECTORY_SEPARATOR . $file)] =  $path . DIRECTORY_SEPARATOR . $file;
		}
		return $result;
	}
}


if (!function_exists("getCompletePacksListAsArray")) {
	function getCompletePacksListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
		{
			foreach(getPacksListAsArray($path) as $file=>$values)
				$result[$values['type']][md5_file( $path . DIRECTORY_SEPARATOR . $values['file'])] =  $path . DIRECTORY_SEPARATOR . $values['file'];
		}
		return $result;
	}
}

if (!function_exists("getCompleteFontsListAsArray")) {
	function getCompleteFontsListAsArray($dirname, $result = array())
	{
		foreach(getCompleteDirListAsArray($dirname) as $path)
		{
			foreach(getFontsListAsArray($path) as $file=>$values)
				$result[$values['type']][md5_file($path . DIRECTORY_SEPARATOR . $values['file'])] = $path . DIRECTORY_SEPARATOR . $values['file'];
		}
		return $result;
	}
}

if (!function_exists("getDirListAsArray")) {
        function getDirListAsArray($dirname)
        {
            $ignored = array(
                'cvs' ,
                '_darcs');
            $list = array();
            if (substr($dirname, - 1) != '/') {
                $dirname .= '/';
            }
            if ($handle = opendir($dirname)) {
                while ($file = readdir($handle)) {
                    if (substr($file, 0, 1) == '.' || in_array(strtolower($file), $ignored))
                        continue;
                    if (is_dir($dirname . $file)) {
                        $list[$file] = $file;
                    }
                }
                closedir($handle);
                asort($list);
                reset($list);
            }

            return $list;
        }
}

if (!function_exists("getFileListAsArray")) {
        function getFileListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if (substr($dirname, - 1) == '/') {
                $dirname = substr($dirname, 0, - 1);
            }
            if (is_dir($dirname) && $handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (! preg_match('/^[\.]{1,2}$/', $file) && is_file($dirname . '/' . $file)) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }
}

if (!function_exists("getZipListAsArray")) {
        function getZipListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if ($handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (preg_match('/(\.zip)$/i', $file)) {
                        $file = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }
}


if (!function_exists("getPacksListAsArray")) {
	function getPacksListAsArray($dirname, $prefix = '')
	{
		$packs = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-converted.diz'));
		$filelist = array();
		if ($handle = opendir($dirname)) {
			while (false !== ($file = readdir($handle))) {
				foreach($packs as $pack)
					if (substr(strtolower($file), strlen($file)-strlen(".".$pack)) == strtolower(".".$pack)) {
						$file = $prefix . $file;
						$filelist[$file] = array('file'=>$file, 'type'=>$pack);
					}
			}
			closedir($handle);
		}
		return $filelist;
	}
}


if (!function_exists("getFontsListAsArray")) {
	function getFontsListAsArray($dirname, $prefix = '')
	{
		$formats = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-converted.diz'));
		$filelist = array();
		
		if ($handle = opendir($dirname)) {
			while (false !== ($file = readdir($handle))) {
				foreach($formats as $format)
					if (substr(strtolower($file), strlen($file)-strlen(".".$format)) == strtolower(".".$format)) {
						$file = $prefix . $file;
						$filelist[$file] = array('file'=>$file, 'type'=>$format);
					}
			}
			closedir($handle);
		}
		return $filelist;
	}
}

if (!function_exists("getStampingShellExec")) {
	function getStampingShellExec()
	{
		$ret = array();
		foreach(cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-stamping.diz')) as $values)
		{
			$parts = explode("||", $values);
			$ret[$parts[0]] = $parts[1];
		}
		return $ret;
	}
}

if (!function_exists("getArchivingShellExec")) {
	function getArchivingShellExec()
	{
		$ret = array();
		foreach(cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-archiving.diz')) as $values)
		{
			$parts = explode("||", $values);
			$ret[$parts[0]] = $parts[1];
		}
		return $ret;
	}
}

if (!function_exists("getExtractionShellExec")) {
	function getExtractionShellExec()
	{
		$ret = array();
		foreach(cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-extracting.diz')) as $values)
		{
			$parts = explode("||", $values);
			$ret[$parts[0]] = $parts[1];
		}
		return $ret;
	}
}


if (!function_exists("getFontsCullList")) {
	function getFontsCullList($files = array())
	{
		$handlers = $ret = array();
		foreach($files as $type => $fonts)
		{
			foreach($fonts as $hashinfo => $font)
			{
				$id = str_replace(array("_", '.', "-", " ", ",", "="), '', substr(strtolower(basename($font)), 0, strlen($font) - strlen($type)));
				$handlers[$type][$id] = $hashinfo;
			}
		}
		$keys = array_keys($handlers);
		foreach(cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-preferences.diz')) as $prefered)
		{
			if (in_array($prefered, $keys))
			{
				unset($keys[$prefered]);
				foreach($keys as $key)
				{
					foreach($handlers[$prefered] as $idc => $finger)
					{
						foreach($handlers[$key] as $idd => $fingerb)
						{
							if ($idc == $idd)
								$ret[$finger][$key][$fingerb] = $files[$key][$finger];
						}
					}
				}
			}
		}
		$sql = "SELECT COUNT(*) as RC from `fonts_fingering` where `fingerprint` LIKE '%s'";
		foreach($ret as $fingerprint => $filevars)
		{
			$found = false;
			list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $fingerprint)));
			if ($count>0)
				$found = true;
			if (strpos(API_URL, 'fonts.labs.coop'))
				$found = false;
			else {
				$sql = "SELECT * FROM `peers` WHERE `peer-id` NOT LIKE '%s' AND `polinating` = 'Yes'";
				if ($found == false && $GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, $GLOBALS['FontsDB']->escape($GLOBALS['peer-id']))))>=1)
				{
								
					while(isset($ret[$fingerprint]) && $peer = $GLOBALS['FontsDB']->fetchArray($results))
					{
						$result = json_decode(getURIData(sprintf($other['api-uri'].$other['api-uri-callback'], 'fingering'), 100, 100, array('fingerprint' => $fingerprint, 'peer-id' => $GLOBALS['peer-id'])), true);
						if (isset($result['count']) && $result['count']>0)
						{
							$found=true;
							continue;
						}
					}
				}
			}
			if ($found==false)
				unset($ret[$fingerprint]);
		}
		return $ret;
	}
}

if (!class_exists("XmlDomConstruct")) {
	/**
	 * class XmlDomConstruct
	 *
	 * 	Extends the DOMDocument to implement personal (utility) methods.
	 *
	 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
	 */
	class XmlDomConstruct extends DOMDocument {

		/**
		 * Constructs elements and texts from an array or string.
		 * The array can contain an element's name in the index part
		 * and an element's text in the value part.
		 *
		 * It can also creates an xml with the same element tagName on the same
		 * level.
		 *
		 * ex:
		 * <nodes>
		 *   <node>text</node>
		 *   <node>
		 *     <field>hello</field>
		 *     <field>world</field>
		 *   </node>
		 * </nodes>
		 *
		 * Array should then look like:
		 *
		 * Array (
		 *   "nodes" => Array (
		 *     "node" => Array (
		 *       0 => "text"
		 *       1 => Array (
		 *         "field" => Array (
		 *           0 => "hello"
		 *           1 => "world"
		 *         )
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param mixed $mixed An array or string.
		 *
		 * @param DOMElement[optional] $domElement Then element
		 * from where the array will be construct to.
		 *
		 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
		 *
		 */
		public function fromMixed($mixed, DOMElement $domElement = null) {

			$domElement = is_null($domElement) ? $this : $domElement;

			if (is_array($mixed)) {
				foreach( $mixed as $index => $mixedElement ) {

					if ( is_int($index) ) {
						if ( $index == 0 ) {
							$node = $domElement;
						} else {
							$node = $this->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					}

					else {
						$node = $this->createElement($index);
						$domElement->appendChild($node);
					}

					$this->fromMixed($mixedElement, $node);

				}
			} else {
				$domElement->appendChild($this->createTextNode($mixed));
			}

		}
			
	}
}
if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
?>
