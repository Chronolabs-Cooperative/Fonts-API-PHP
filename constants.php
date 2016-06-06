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
	/**
	 *
	 * @var string
	 */
	if (!defined('API_DEBUG'))
		define('API_DEBUG', true);
	define('API_VERSION', '2.3.0');
	define('MAXIMUM_QUERIES', 2600);
	
	/**
	 * Twitter Setting for Releases Announces etc
	 * @var string
	 */
	// Twitter API oAuth Details
	$GLOBALS['twitter'] = array(
			'consumer_key' => "NLFAz4ZfgzrVal08ZovIkPyVx",
			'consumer_secret' => "oSsfeitrUv7Jo1eK0ULV3geL5I1F0u66HtvEjADLH1pzPmHJU5",
			'oauth_access_token' => "2916941286-w2hKqu2obzyVM9VkF2FmUedEgmKdEWtosbfb6IM",
			'oauth_access_token_secret' => "6CRttMfnudkWhparEtWI5CEF9TOR13KKPICLDKix0I3Fl"
	);
	
	// Twitter Responses
	if (!defined("API_TWITTER_RELEASES"))
		define("API_TWITTER_RELEASES", "Zero-day Font Release: %s\nFiles Packed: %s\nInflated: %sMb's\nDownload: %s\nPreview: %s");
	
	// Added version 2.2.1 - Pertanence too the local API Session
	define('API_URL', (!isset($_SERVER["HTTP_HOST"])?"http://fonts.labs.coop":(isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"]));
	define('API_URL_CALLBACK', '/v2/%s/callback.api');
	define('API_URL_ZIP', '/v2/data/%s/zip/download.api');
	define('API_URL_FONTS', '/v2/fonts/all/json.api?local=only');
	define('API_POLINATING', (strpos(API_URL, 'localhost')&&strpos(API_URL, 'labs.coop')&&strpos(API_URL, 'syd.labs.coop')?false:true));
	define('API_REPOSITORY', 'git'); // = git or svn or git,svn
	define('API_BASE', 'eot');
	define('API_LICENCE', 'gpl3');
	
	/**
	 * 
	 * @var string
	 */
	define('FONT_RESOURCES_UNPACKING', '/fonts/Unpacking');
	define('FONT_RESOURCES_SORTING', '/fonts/Sorting');
	define('FONT_RESOURCES_CONVERTING', '/fonts/Converting');
	define('FONT_RESOURCES_RESOURCE', '/fonts/Fonting');
	define('FONT_RESOURCES_CACHE', '/fonts/Cache');
	define('FONT_RESOURCES_STORE', 'https://sourceforge.net/p/chronolabsapis/Fonting/HEAD/tree/%s?format=raw');
	define('FONT_RESOURCES_PEERS', 'https://sourceforge.net/p/chronolabsapis/Fonting/HEAD/tree/peers.json?format=raw');
	define('FONT_RESOURCES_REPOMAP', 'https://sourceforge.net/p/chronolabsapis/Fonting/HEAD/tree/%s/%s--repository-mapping.json?format=raw');
	define('FONT_RESOURCES_STORE_GIT', 'https://github.com/Chronolabs-Cooperative/Fonting-Repository/raw/master/%s');
	define('FONT_RESOURCES_PEERS_GIT', 'https://github.com/Chronolabs-Cooperative/Fonting-Repository/raw/master/peers.json');
	define('FONT_RESOURCES_REPOMAP_GIT', 'https://github.com/Chronolabs-Cooperative/Fonting-Repository/raw/master/%s/%s--repository-mapping.json');
	define('FONT_UPLOAD_PATH', '/tmp/Fonts-Uploads');
	define('FONTS_CACHE', '/tmp/Fonts-Cache');
	

	/******* DO NOT CHANGE THIS VARIABLE ****
	 * @var string
	 */
	define('API_ROOT_NODE', 'http://fonts.labs.coop');
	
	
	/**
	 * Connects Global Database Objectivity
	 */
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'fontages.php';
	
	/**
	 * Cache Indexing Meter
	 */
	define('CACHE_METER_USAGE', 4);
	global $hourindx, $hourprev;
	$hourindx = floor(date("H") / CACHE_METER_USAGE);
	$hourprev = floor(date("H", time() - (3600 * CACHE_METER_USAGE)) / CACHE_METER_USAGE);
	if (API_DEBUG==true) echo (basename(__FILE__) . "::"  . __LINE__ . "<br/>\n");
?>
