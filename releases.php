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
 * 
 * CREATE TABLE `' . $GLOBALS['APIDB']->prefix('releases') . '` (
  `id` int(22) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(198) NOT NULL,
  `org` varchar(150) NOT NULL,
  `callback` varchar(300) NOT NULL,
  `method` enum('subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
  `sent` int(22) NOT NULL DEFAULT '0',
  `failed` int(22) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  `updated` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `SEARCH` (`name`,`email`,`org`,`method`) USING BTREE KEY_BLOCK_SIZE=12
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 */

		
	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source, $ipid;
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';

	set_time_limit(3600*36*9*14);
	
	$error = array();
	if (isset($_REQUEST[$_GET['field']]['email']) || !empty($_REQUEST[$_GET['field']]['email'])) {
		if (!checkEmail($_REQUEST[$_GET['field']]['email']))
			$error[] = 'Email is invalid!';
	} else
		$error[] = 'No Email Address for Notification specified!';
	
	if (((!isset($_REQUEST[$_GET['field']]['name']) || empty($_REQUEST[$_GET['field']]['name'])) || (!isset($_REQUEST[$_GET['field']]['org']) || empty($_REQUEST[$_GET['field']]['org']))))
	{
		$error[] = 'No Converters Individual name or organisation not specified in survey scope when selected!';
	}

	if (((!isset($_REQUEST[$_GET['field']]['method']) || empty($_REQUEST[$_GET['field']]['method']))))
	{
		$error[] = 'Method is not specified!';
	}
	
	if (!empty($error))
	{
		redirect(isset($_REQUEST['return'])&&!empty($_REQUEST['return'])?$_REQUEST['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Error Has Occured</h1><br/><p>" . implode("<br />", $error) . "</p></center>");
		exit(0);
	}
	
	if (!$release = $GLOBALS["APIDB"]->fetchArray($GLOBALS["APIDB"]->queryF('SELECT * FROM `' . $GLOBALS['APIDB']->prefix('releases') . '` WHERE `email` = "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['email']).'" AND  `name` = "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['name']).'" AND `org` = "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['org']).'"')))
	{
		switch($_REQUEST[$_GET['field']]['method'])
		{
			case "subscribed":
				
				if (!$GLOBALS["APIDB"]->queryF($sql = 'INSERT INTO `' . $GLOBALS['APIDB']->prefix('releases') . '` (`email`, `name`, `org`, `callback`, `method`, `created`) VALUES("'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['email']).'", "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['name']).'","'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['org']).'","'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['callback']).'","'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['method']).'", "'.time(). '")'))
					die("SQL Failed: $sql;");
				break;
			default:
				redirect(isset($_REQUEST['return'])&&!empty($_REQUEST['return'])?$_REQUEST['return']:'http://'. $_SERVER["HTTP_HOST"], 9, "<center><h1 style='color:rgb(198,0,0);'>Error Has Occured</h1><br/><p>" . implode("<br />", array(0=>"Releases Callback and Email Not Found to Unsubscribe")) . "</p></center>");
				exit(0);
				break;
		}
	} else {
		switch($_REQUEST[$_GET['field']]['method'])
		{
			case "subscribed":
		
				if (!$GLOBALS["APIDB"]->queryF($sql = 'UPDATE `' . $GLOBALS['APIDB']->prefix('releases') . '` SET `callback` = "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['callback']).'", `method` = "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['method']). '", `updated` = "'.time().'" WHERE `id` = "' . $releases['id'] . "'"))
					die("SQL Failed: $sql;");
				break;
			case "unsubscribed":
				if (!$GLOBALS["APIDB"]->queryF($sql = 'UPDATE `' . $GLOBALS['APIDB']->prefix('releases') . '` SET `method` = "'.$GLOBALS["APIDB"]->escape($_REQUEST[$_GET['field']]['method']). '", `updated` = "'.time().'" WHERE `id` = "' . $releases['id'] . "'"))
					die("SQL Failed: $sql;");
				break;
		}
	}
	
	redirect(isset($_REQUEST['return'])&&!empty($_REQUEST['return'])?$_REQUEST['return']:'http://'. $_SERVER["HTTP_HOST"], 18, "<center><h1 style='color:rgb(0,198,0);'>Subscription set on API!</h1></center>");
	exit(0);
	
?>