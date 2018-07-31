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

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '315M');
include_once dirname(__DIR__).'/constants.php';
include_once dirname(__DIR__).'/include/functions.php';
require_once dirname(__DIR__).'/class/fontsmailer.php';

if (!defined('API_FRONT_EXECSECS'))
    define('API_FRONT_EXECSECS', (mt_rand(27, 60) * mt_rand(5, 8)));
if (!defined('API_PATH_CONTROLS'))
    define('API_PATH_CONTROLS', API_PATH . DS . 'pickings-json');
if (!is_dir(API_PATH_CONTROLS))
    mkdir(API_PATH_CONTROLS, 0777, true);
if (!defined('API_PATH_PICKINGS'))
    define('API_PATH_PICKINGS', API_PATH . DS . 'Fonts' . DS . 'Pickings');
if (!defined('API_PATH_MEXICAN'))
    define('API_PATH_MEXICAN', date('W') . DS . date('Y') . DS . date('m') . DS . date('D') . DS . date('d'));
    
if (!defined('API_JSON_STRUCTURES'))
    define('API_JSON_STRUCTURES', 'https://sourceforge.net/p/chronolabs-cooperative/fonts/HEAD/tree/json/structures.json?format=raw');
if (!defined('API_GET_SVNFILE'))
    define('API_GET_SVNFILE', 'https://sourceforge.net/p/chronolabs-cooperative/fonts/HEAD/tree/%s?format=raw');
if (!defined('API_EXPORT_SVNFILE'))
    define('API_EXPORT_SVNFILE', 'svn export --force \"svn://svn.code.sf.net/p/chronolabs-cooperative/fonts/%s\" \"%s\"');
if (!defined('API_FONTS_UPLOADED'))
    define('API_FONTS_UPLOADED', 'eot|otf|ttf|woff|sfd|pf3|pfa|pfb|pt3|t42|gsf|gai');
    
set_time_limit(7200*99*25);
//shell_exec('rm -rf "' . API_PATH_PICKINGS . DS . '*"');
$start = time();
$structures = json_decode(getURIData(API_JSON_STRUCTURES, 480, 480, array()), true);
foreach($structures as $structmd5 => $structure) {
    if ($structure['meter'] != 'all' && strlen($structure['meter']) == 3 && $structure['type'] == 'fonts') {
        if (file_exists($jfile = API_PATH_CONTROLS . DS . 'fonts.md5s.' . substr($structure['meter'], 0, 2) . '.json')) {
            $filemd5s = json_decode(file_get_contents($jfile), true);
        } else 
            $filemd5s = array();
        if (!in_array($structmd5, $filemd5s)) {
            if (file_exists($jfont = API_PATH_CONTROLS . DS . 'fonts.keys.' . substr($structure['meter'], 0, 2) . '.json')) {
                $fontkeys = json_decode(file_get_contents($jfont), true);
            } else
                $fontkeys = array();
            $fonts = json_decode(getURIData(sprintf(API_GET_SVNFILE, $structure['path'] . DS . $structure['filename']), 480, 480, array()), true);
            $files = json_decode(getURIData(sprintf(API_GET_SVNFILE, str_replace('fonts', 'files', $structure['path'] . DS . $structure['filename'])), 480, 480, array()), true);
            foreach ($fonts as $fkey => $ffont) {
                if (!in_array($ffont['key'], $fontkeys)) {
                    if (!is_dir($outpath = API_PATH_PICKINGS . DS . API_PATH_MEXICAN . DS . $ffont['key']))
                        mkdir(API_PATH_PICKINGS . DS . API_PATH_MEXICAN . DS . $ffont['key'], 0777, true);
                    foreach(explode('|', API_FONTS_UPLOADED) as $fontext) {
                        foreach($files as $filename => $svnfile) {
                            if ($svnfile['extension'] == $fontext && $svnfile['key'] == $ffont['key']) {
                                echo "\nPicking Font Exporting: " . $svnfile['filename'];
                                $output = array();
                                exec(sprintf(API_EXPORT_SVNFILE, $svnfile['path'] . DS . $svnfile['filename'], $ffile = $outpath . DS . $svnfile['filename']), $output);
                                echo implode("\n ~ ", $output);
                                if (md5_file($ffile) == $svnfile['md5']) {
                                    $fontkeys[$ffont['key']] = $ffont['key'];
                                    continue;
                                    continue;
                                    echo " ~ success exporting";
                                } else 
                                    echo " ~ failed exporting";
                            }
                        }
                    }
                }
            }
            file_put_contents($jfont, json_encode($fontkeys));
            if ($start + API_FRONT_EXECSECS < time())
            {
                continue;
            }
        }
        if ($start + API_FRONT_EXECSECS < time())
        {
            continue;
            continue;
        } else 
            $filemd5s[$structmd5] = $structmd5;
        file_put_contents($jfile, json_encode($filemd5s));
    }
}

$files = getCompleteFontsListAsArray(constant("API_PATH_PICKINGS"));
$data['files'] = array();
foreach($files as $type => $fontfiles)
{
	$keys = array_keys($fontfiles);
	shuffle($keys); shuffle($keys); shuffle($keys);
	foreach($keys as $key)
		$data['files'][$type][$key] = $fontfiles[$key];
}
$files = $data['files'];
$size = 0;
foreach($files as $type => $fontfiles)
{
    foreach($fontfiles as $finger => $fontfile)
    {
        $size += filesize($fontfile);
    }
}

foreach($files as $type => $fontfiles)
{
	$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
	foreach($fontfiles as $finger => $fontfile)
	{
	    $copypath = FONT_RESOURCES_SORTING . DIRECTORY_SEPARATOR . API_LICENSE_EMAIL . DIRECTORY_SEPARATOR . microtime(true);
	    if (!is_dir($copypath))
	        mkdir($copypath, 0777, true);
	        
		if (!file_exists($copypath . DIRECTORY_SEPARATOR . basename($fontfile))&&filesize($fontfile)>199)
		{
			if (copy($fontfile, $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile))))
			{
				if (file_exists($uploadfile = $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($fontfile))))
				{
					@exec("cd $copypath", $out, $return);
					@exec($exe = sprintf(DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "fontforge -script \"%s\" \"%s\"", dirname(__DIR__ ) . DIRECTORY_SEPARATOR . "include"  . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "convert-fonts-upload.pe", $uploadfile), $out, $return);
					deleteFilesNotListedByArray($copypath, array(API_BASE=>API_BASE, 'ufo'=>'ufo'));
					unlink($fontfile);
					$glyphsfingerprint = '';
					foreach(getFontsListAsArray($copypath) as $file)
						if ($file['type']==API_BASE)
							$uploadfile = $copypath . DIRECTORY_SEPARATOR . $file['file'];
						elseif($file['type']=='ufo') {
						    $glyphs = array();
						    $fileglyphs = getFileListAsArray($copypath . DIRECTORY_SEPARATOR . $file['file'] . DIRECTORY_SEPARATOR . 'glyphs');
						    sort($fileglyphs);
						    foreach($fileglyphs as $glyph)
						    {
						        $glyphs[] = md5_file($copypath . DIRECTORY_SEPARATOR . $file['file'] . DIRECTORY_SEPARATOR . 'glyphs' . DIRECTORY_SEPARATOR . $glyph);
						    }
						    $glyphsfingerprint = md5(implode('', $glyphs));
						}
					$fontdata = getBaseFontValueStore($uploadfile);
					if (isset($fontdata['version']))
						$fontdata['version'] = $fontdata['version'] + 1.001;
					$fontdata['person'] = $data['form']['name'];
					$fontdata['company'] = $data['form']['bizo'];
					$fontdata['uploaded'] = microtime(true);
					$fontdata['licence'] = API_LICENCE;
					writeFontRepositoryHeader($uploadfile, API_LICENCE, $fontdata);
					$data = file($uploadfile);
					$found = false;
					foreach($data as $line => $value)
						if (!strpos(" $value", 'currentfile eexec') && $found == false)
							unset($data[$line]);									
						elseif (strpos(" $value", 'currentfile eexec') && $found == false) {
							unset($data[$line]);
							$found = true;
						}
					$fingerprint = md5(implode("", $data));
					
					if (!empty($glyphsfingerprint))
					{
						$sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` WHERE `fingerprint` LIKE '" . $glyphsfingerprint . "'";
						list($gfingers) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql));
					}
					$sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` WHERE `fingerprint` LIKE '" . $fingerprint . "'";
					list($fingers) = $GLOBALS['APIDB']->fetchRow($GLOBALS['APIDB']->queryF($sql));
					if ($fingers==0 && $gfingers == 0)
					{
						$ffile++;
						$data['process'] = microtime(true);
						$data['mode'] = 'queuing';
						$data['current'] = $copypath . DIRECTORY_SEPARATOR .  strtolower(basename($uploadfile));
						$queued[] = $fontfile;
						$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('uploads') . "` (`ip_id`, `available`, `key`, `scope`, `prefix`, `email`, `uploaded_file`, `uploaded_path`, `uploaded`, `referee_uri`, `callback`, `bytes`, `batch-size`, `datastore`, `cc`, `bcc`, `frequency`, `elapses`, `longitude`, `latitude`) VALUES ('$ipid','" . $available = mt_rand(7,13) . "','" . basename(dirname(dirname($fontfile))) . "','none','" . 'webdav'. "','" . $GLOBALS['APIDB']->escape($email = API_LICENSE_EMAIL) . "','" . $GLOBALS['APIDB']->escape($filename = strtolower(basename($uploadfile))) . "','" . $GLOBALS['APIDB']->escape($copypath) . "','" . time(). "','" . $GLOBALS['APIDB']->escape($_SERVER['HTTP_REFERER']) . "','" . $GLOBALS['APIDB']->escape($callback = '') . "'," . (filesize($uploadfile)==''?0:filesize($uploadfile)) . "," . $size . ",'" . $GLOBALS['APIDB']->escape(json_encode(array('scope' => '', 'ipsec' => $locality = json_decode(array()), 'name' => API_LICENSE_COMPANY, 'bizo' => API_LICENSE_COMPANY, 'batch-size' => $size, 'font' => $fontdata))) . "','$ccid','$bccid','" . $GLOBALS['APIDB']->escape($freq = mt_rand(2.76,6.75)*3600*24) . "','" . $GLOBALS['APIDB']->escape($elapse = mt_rand(9,27)*3600*24) . "','". (!isset($_SESSION['locality']['location']["coordinates"]["longitude"])?"0.0001":$_SESSION['locality']['location']["coordinates"]["longitude"])."','". (!isset($_SESSION['locality']['location']["coordinates"]["latitude"])?"0.0001":$_SESSION['locality']['location']["coordinates"]["latitude"])."')";
						if ($GLOBALS['APIDB']->queryF($sql))
						{
							$uploadid = $GLOBALS['APIDB']->getInsertId();
							if ($scope == 'none')
							{
								$sql = "UPDATE `" . $GLOBALS['APIDB']->prefix('uploads') . "` SET `quizing` = UNIX_TIMESTAMP(), `expired` = UNIX_TIMESTAMP()+1831, `slotting` = 0, `needing` = 1, `finished` = 2, `surveys` = 2, `available` = 0 WHERE `id` = $uploadid";
								$GLOBALS['APIDB']->queryF($sql);
							}
							echo "\nCreated Upload Identity: ".$uploadid;
							$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` (`type`, `upload_id`, `fingerprint`) VALUES ('" . $GLOBALS['APIDB']->escape(API_BASE) . "','" . $GLOBALS['APIDB']->escape($uploadid) . "','" . $GLOBALS['APIDB']->escape($glyphsfingerprint) . "')";
							if (!$GLOBALS['APIDB']->queryF($sql))
							    echo "SQL Failed: $sql;\n";
							$sql = "INSERT INTO `" . $GLOBALS['APIDB']->prefix('fonts_fingering') . "` (`type`, `upload_id`, `fingerprint`) VALUES ('" . $GLOBALS['APIDB']->escape(API_BASE) . "','" . $GLOBALS['APIDB']->escape($uploadid) . "','" . $GLOBALS['APIDB']->escape($fingerprint) . "')";
							if (!$GLOBALS['APIDB']->queryF($sql))
								echo "SQL Failed: $sql;\n";
							$success[] = basename($fontfile);
							$data['success'][] = basename($fontfile);
							if (isset($data['form']['callback']) && !empty($data['form']['callback']))
								@setCallBackURI($data['form']['callback'], 145, 145, array('action'=>'uploaded', 'file-md5' => $finger, 'allocated' => $available, 'key' => $key, 'email' => $data['form']['email'], 'name' => $data['form']['name'], 'bizo' => $data['form']['bizo'], 'frequency' => $freq, 'elapsing' => $elapses, 'filename' => $filename, 'culled' => false));
								$GLOBALS["APIDB"]->queryF('UPDATE `' . $GLOBALS['APIDB']->prefix('networking') . '` SET `fonts` = `fonts` + 1 WHERE `ip_id` = "'.$ipid.'"');
							echo "\nUploaded file Queued: ".basename($fontfile);
							unlink($uploadfile);
							rmdir(dirname($uploadfile));
						} else {
							echo ("SQL Failed: $sql;\n");
						}
						$GLOBALS['APIDB']->queryF($sql = "COMMIT");
						$GLOBALS['APIDB']->queryF($sql = "START TRANSACTION");
					} 
				}
			}
		}
	}
	$GLOBALS['APIDB']->queryF($sql = "COMMIT");
	sleep(mt_rand(2,7));
}

deleteFilesNotListedByArray(API_PATH_PICKINGS, explode('|', API_FONTS_UPLOADED));
removeEmptyPathFolderList(API_PATH_PICKINGS);

?>
