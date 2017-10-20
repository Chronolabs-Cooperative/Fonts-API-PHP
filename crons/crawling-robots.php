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

error_reporting(E_ERROR);
set_time_limit(1999);
require_once dirname(__DIR__).'/constants.php';
$filters = array();
$metas = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'crawlers-metas-files.diz'));
$filters['%packs'] = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'packs-converted.diz'));
$filters['%fonts'] = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'font-converted.diz'));
$scripting = $scripts = array();
$urls = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'font-crawls-urls.diz'));
$keywords = cleanWhitespaces(file(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'font-crawls-keywords.diz'));
shuffle($keywords);
shuffle($keywords);
foreach($keywords as $keyword)
{
	shuffle($urls); shuffle($urls); shuffle($urls);
	shuffle($metas); shuffle($metas); shuffle($metas);
	$filtered = false;
	foreach(array_keys($filters) as $fit)
	{
		if (strpos(" $keyword", $fit)>0)
		{
			$filtered = true;
		}
	}
	if ($filtered==false)
	{
		foreach($urls as $url)
		{
			$crawl = sprintf($url, urlencode($keyword));
			$scripts[] = "/usr/bin/wget --span-host --bind-address=".parse_url(API_URL, PHP_URL_HOST)." --referer=".API_URL." --level=" . (!isset($_GET['level'])&&!is_numeric($_GET['level'])?API_CRAWLERS_LEVELS:(integer)$_GET['level']) . " --recursive -x -k --continue --accept=" . implode(",", $metas) . " \"$crawl\" \"%s\"";
		}
	} else {
		$filtered = array();
		foreach(array_keys($filters) as $fit)
		{
			if (strpos(" $keyword", $fit)>0)
			{
				foreach($filters[$fit] as $salt)
					$filtered[] = str_replace($fit, $salt, $keyword);
			}
		}
		$clean = count($filtered);
		while($clean > 1)
		{
			foreach($filtered as $key => $keyword)
			{
				$filtered = false;
				foreach(array_keys($filters) as $fit)
				{
					if (strpos(" $keyword", $fit)>0)
					{
						$step = 1;
						foreach($filters[$fit] as $salt)
						{
							if ($step > count($filters[$fit])-1)
							{
								$filtered[$key] = str_replace($fit, $salt, $keyword);
								++$step;
							} else { 
								$filtered[] = str_replace($fit, $salt, $keyword);
								++$step;
							}
						}
						$filtered = true;
					}
				}
				if ($filtered != true)
					--$clean;
			}
			
		}
		shuffle($filtered); shuffle($filtered); shuffle($filtered);
		foreach($filtered as $keyword)
		{
			foreach($urls as $url)
			{
				$crawl = sprintf($url, urlencode($keyword));
				$scripts[] = "/usr/bin/wget --span-host --level=" . API_CRAWLERS_LEVELS . " --recursive -x -k --continue --accept=" . implode(",", $metas) . " \"$crawl\"";
			}
		}
	}
}
shuffle($scripts); shuffle($scripts);shuffle($scripts); shuffle($scripts); shuffle($scripts); shuffle($scripts); 
$hops = ceil(count($scripts) / API_CRAWLERS_ROBOTS);
$path = array();
$robots=0;
$step=0;
$crawldat = json_decode(file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json"), true);
foreach($scripts as $exec)
{
	if ($step>=$hops)
	{
		$scripting[$robots][] = "/usr/bin/tee \"".$path[$robots]."\" > \"".$path[$robots]."" . DIRECTORY_SEPARATOR . 'finished.dat"';
		$scripting[$robots][] = "/usr/bin/php -q \"".__DIR__."/register-crawling.php?path=".urlencode($path[$robots])."\"";
		$crawldat[$path[$robots]]['steps'] = $step;
		$robots++;
		$step = 0;
	} else 
		$step++;
	if (empty($scripting[$robots]))
	{
		$scripting[$robots] = array('!#/sh/bash');
		$path[$robots] = FONT_UPLOAD_PATH . DIRECTORY_SEPARATOR . API_EMAIL_ADDY . DIRECTORY_SEPARATOR . abs(mt_rand(-microtime(true), microtime(true)));
		mkdir(FONT_UPLOAD_PATH, 0777, true);
		$scripting[$robots][] = 'mkdir "' . FONT_UPLOAD_PATH . '"';		
		$scripting[$robots][] = 'mkdir "' . FONT_UPLOAD_PATH . DIRECTORY_SEPARATOR . API_EMAIL_ADDY . '"';
		$scripting[$robots][] = "mkdir \"".$path[$robots]."\"";
		$scripting[$robots][] = "cd \"".$path[$robots]."\"";
		$crawldat[$path[$robots]]['set'] = microtime(true);
		$crawldat[$path[$robots]]['robot'] = $robots;
	}
	$scripting[$robots][] = sprintf($exec, $path[$robots]);
}
$scripting[$robots][] = "/usr/bin/tee \"".$path[$robots]."\" > \"".$path[$robots]."" . DIRECTORY_SEPARATOR . 'finished.dat"';
$scripting[$robots][] = "/usr/bin/php -q \"".__DIR__."/register-crawling.php?path=".urlencode($path[$robots])."\"";
foreach($scripting as $robot => $script)
{
	putRawFile(__DIR__ . DIRECTORY_SEPARATOR . "crawling-bot--" . str_repeat("0", (strlen((string)API_CRAWLERS_ROBOTS)+1)-strlen((string)$robot)) ."$robot.sh", implode("\n", $script));
}
putRawFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "crawling.json", json_encode($crawldat));



?>
