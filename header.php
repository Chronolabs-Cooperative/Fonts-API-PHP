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
	if (!defined('API_DEBUG'))
		define('API_DEBUG', false);
	
	error_reporting(E_ERROR);
	ini_set('display_errors', true);
	
	/**
	 * Opens Access Origin Via networking Route NPN
	*/
	header('Access-Control-Allow-Origin: *');
	header('Origin: *');
	
	/**
	 * Turns of GZ Lib Compression for Document Incompatibility
	 */
	ini_set("zlib.output_compression", 'Off');
	ini_set("zlib.output_compression_level", -1);

	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	
	/**
	 *
	 * @var constants
	 */
	define("API_FILE_IO_PEERS", __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'peers.diz');
	define("API_FILE_IO_DOMAINS", __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'domains.diz');
	define("API_FILE_IO_FOOTER", __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'api-%s.html');
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	require_once __DIR__.'/class/fontages.php';
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	require_once __DIR__.'/functions.php';
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	require_once __DIR__.'/apiconfig.php';
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	$parts = explode(".", microtime(true));
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	mt_srand(mt_rand(-microtime(true), microtime(true))/$parts[1]);
	$salter = ((float)(mt_rand(0,1)==1?'':'-').$parts[1].'.'.$parts[0]) / sqrt((float)$parts[1].'.'.intval(cosh($parts[0])))*tanh($parts[1]) * mt_rand(1, intval($parts[0] / $parts[1]));
	header('Blowfish-salt: '. $salter);
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source, $ipid, $fontnames;
	$fontnames = array();
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	define('FONTS_CACHE', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'cache');
	if (!is_dir(FONTS_CACHE))
		mkdirSecure(FONTS_CACHE, 0777, true);
	
		if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	/**
	 * Global API Configurations and Setting from file Constants!
	 */
	$domain = getDomainSupportism('domain', $_SERVER["HTTP_HOST"]);
	$protocol = getDomainSupportism('protocol', $_SERVER["HTTP_HOST"]);
	$business = getDomainSupportism('business', $_SERVER["HTTP_HOST"]);
	$entity = getDomainSupportism('entity', $_SERVER["HTTP_HOST"]);
	$contact = getDomainSupportism('contact', $_SERVER["HTTP_HOST"]);
	$referee = getDomainSupportism('referee', $_SERVER["HTTP_HOST"]);
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	/**
	 * URI Path Finding of API URL Source Locality
	 * @var unknown_type
	 */
	$pu = parse_url($_SERVER['REQUEST_URI']);
	$source = (isset($_SERVER['HTTPS'])?'https://':'http://').strtolower($_SERVER['HTTP_HOST']);
	
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	if (defined("MAXIMUM_QUERIES")) {
		session_start();
		if (!in_array(whitelistGetIP(true), whitelistGetIPAddy())) {
			if (isset($_SESSION['reset']) && $_SESSION['reset']<microtime(true))
				$_SESSION['hits'] = 0;
				if ($_SESSION['hits']<=MAXIMUM_QUERIES) {
					if (!isset($_SESSION['hits']) || $_SESSION['hits'] = 0)
						$_SESSION['reset'] = microtime(true) + 3600;
						$_SESSION['hits']++;
				} else {
					header("HTTP/1.0 404 Not Found");
					exit;
				}
		}
	}
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	/**
	 * URI Path Finding of API URL Source Locality
	 * @var unknown_type
	 */
	$ipid = getIPIdentity(whitelistGetIP(true));
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
	
	