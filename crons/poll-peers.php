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

ini_set('display_errors', true);
ini_set('log_errors', true);
error_reporting(E_ERROR);
define('MAXIMUM_QUERIES', 25);
ini_set('memory_limit', '128M');
include_once dirname(dirname(__FILE__)).'/functions.php';
include_once dirname(dirname(__FILE__)).'/class/fontages.php';
error_reporting(E_ERROR);
set_time_limit(7200);

$sql = "SELECT * FROM `peers` WHERE `peer-id` NOT LIKE '%s' AND `polinating` = 'Yes' ORDER BY RAND() LIMIT 5";
if ($GLOBALS['FontsDB']->getRowsNum($results = $GLOBALS['FontsDB']->queryF(sprintf($sql, mysql_real_escape_string($GLOBALS['peer-id']))))>=1)
{
				
	while(isset($ret[$fingerprint]) && $peer = $GLOBALS['FontsDB']->fetchArray($results))
	{
		$fonts = json_decode(getURIData($other['api-uri'].$other['api-uri-fonts'], 900, 900, array()), true);
		foreach($fonts as $fontid => $values)
		{
			switch ($values['medium'])
			{
				case 'FONT_RESOURCES_CACHE':
				case 'FONT_RESOURCES_RESOURCE':
					$numnodes = 0 ;
					foreach($values['nodes'] as $type => $nodes)
						$numnodes = $numnodes + count($nodes);
					$sql = "SELECT COUNT(*) as `count` FROM `fonts` WHERE `id` = '%s'";
					list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid)));
					if ($count == 0)
					{
						$sql = "INSERT INTO `fonts` (`id`, `peer_id`, `type`, `state`, `normal`, `italic`, `bold`, `wide`, `condensed`, `light`, `semi`, `book`, `body`, `header`, `heading`, `footer`, `graphic`, `system`, `quote`, 																																									`block`, `message`, `admin`, `logo`, `slogon`, `legal`, `script`, `medium`, `nodes`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
						$GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $peer['peer-id'], 'peer', 'online', $values['normal'], $values['italic'], $values['bold'], $values['wide'], $values['condensed'], $values['light'], $values['semi'], $values['book'], $values['body'], $values['header'], $values['heading'], $values['footer'], $values['graphic'], $values['system'], $values['quote'], $values['block'], $values['message'], $values['admin'], $values['logo'], $values['slogon'], $values['legal'], $values['script'], 'FONT_RESOURCES_PEERS', $numnodes));
						unset($values['archive']['id']);
						foreach($values['archive'] as $key => $value)
							$values['archive'][$key] = mysql_real_escape_string($value);
						$sql = "INSERT INTO `fonts_archiving` (`".implode("`, `", array_keys($values['archive'])) . "`) VALUES (`".implode("', '", $values['archive']) . "')";
						$GLOBALS['FontsDB']->queryF($sql);
						$archive_id = $GLOBALS['FontsDB']->getInsertId();
						unset($values['archive']['id']);
						foreach($values['files'] as $key => $fvalue)
						{
							unset($fvalue['id']);
							$fvalue['archive_id'] = $archive_id;
							foreach($fvalue as $key => $value)
								$fvalue[$key] = mysql_real_escape_string($value);
							$sql = "INSERT INTO `fonts_files` (`".implode("`, `", array_keys($fvalue)) . "`) VALUES (`".implode("', '", $fvalue) . "')";
							$GLOBALS['FontsDB']->queryF($sql);
						}
						foreach($values['names'] as $key => $nvalue)
						{
							
							$nvalue['upload_id'] = -1;
							foreach($nvalue as $key => $value)
								$fvalue[$key] = mysql_real_escape_string($value);
							$sql = "SELECT COUNT(*) as `count` FROM `fonts_names` WHERE `font_id` = '%s' AND `name` = '%s'";
							list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $nvalue['name'])));
							if ($count == 0)
							{
								$sql = "INSERT INTO `fonts_names` (`".implode("`, `", array_keys($fvalue)) . "`) VALUES (`".implode("', '", $fvalue) . "')";
								$GLOBALS['FontsDB']->queryF($sql);
							}
						}
						foreach($values['nodes'] as $type => $nodes)
						{
							foreach($nodes as $node => $values)
							{
								$sql = "SELECT `id` FROM `nodes` WHERE `type` = '%s' AND  `node` = '%s'";
								list($nodeid) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $type, $node)));
								if (empty($nodeid))
								{
									$sql = "INSERT INTO `nodes` VALUE(`type`, `node`, `usage`, `weight`) VALUES ('%s', '%s', '%s', '%s')";
									$GLOBALS['FontsDB']->queryF(sprintf($sql, $type, $node, 0, 1));
									$nodeid = $GLOBALS['FontsDB']->getInsertId();
								}
								$sql = "SELECT COUNT(*) as `count` FROM `nodes_linking` WHERE `font_id` = '%s' AND `node_id` = '%s'";
								list($count) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $nodeid)));
								if ($count == 0)
								{
									$sql = "INSERT INTO `nodes_linking` VALUE(`font_id`, `node_id`) VALUES ('%s', '%s', '%s', '%s')";
									$GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $nodeid));
									$sql = "UPDATE `nodes` SET `usage` = `usage` + 1 WHERE `id` = '$nodeid'";
									$GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $nodeid));
								}
							}
						}
					} else {
						foreach($values['names'] as $key => $nvalue)
						{
								
							$nvalue['upload_id'] = -1;
							foreach($nvalue as $key => $value)
								$fvalue[$key] = mysql_real_escape_string($value);
								$sql = "INSERT INTO `fonts_names` (`".implode("`, `", array_keys($fvalue)) . "`) VALUES (`".implode("', '", $fvalue) . "')";
								$GLOBALS['FontsDB']->queryF($sql);
						}
						foreach($values['nodes'] as $type => $nodes)
						{
							foreach($nodes as $node => $values)
							{
								$sql = "SELECT `id` FROM `nodes` WHERE `type` = '%s' AND  `node` = '%s'";
								list($nodeid) = $GLOBALS['FontsDB']->fetchRow($GLOBALS['FontsDB']->queryF(sprintf($sql, $type, $node)));
								if (empty($nodeid))
								{
									$sql = "INSERT INTO `nodes` VALUE(`type`, `node`, `usage`, `weight`) VALUES ('%s', '%s', '%s', '%s')";
									$GLOBALS['FontsDB']->queryF(sprintf($sql, $type, $node, 0, 1));
									$nodeid = $GLOBALS['FontsDB']->getInsertId();
								}
								$sql = "INSERT INTO `nodes_linking` VALUE(`font_id`, `node_id`) VALUES ('%s', '%s', '%s', '%s')";
								$GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $nodeid));
								$sql = "UPDATE `nodes` SET `usage` = `usage` + 1 WHERE `id` = '$nodeid'";
								$GLOBALS['FontsDB']->queryF(sprintf($sql, $fontid, $nodeid));
							}
						}
					}
					
					break;
			}
		}
	}
}


?>