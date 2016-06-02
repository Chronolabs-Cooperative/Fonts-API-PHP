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

$sql = "SELECT * FROM `uploads` WHERE `key` = '" . $fontmd5 = $_REQUEST['key'] . "'";
if ($result = $GLOBALS['FontsDB']->queryF($sql))
{
	if ($row = $GLOBALS['FontsDB']->fetchArray($result))
	{
		$data = json_decode($row['datastore'], true);
		$fontname = str_replace(" ", "", $fontspaces = $data["FontName"]);
		$currently = $row['currently'];
	} else
		die("Font fingerprint not found!");
}
if (in_array($_REQUEST['mode'], array('jpg', 'png', 'gif')))
{
	$fonts = getCompleteFontsListAsArray($currently);
	foreach($fonts['ttf'] as $md5 => $ffont)
		$font = $ffont;
	if (isset($font) && file_exists($font))
	{
		$json = json_decode(file_get_contents($currently . DIRECTORY_SEPARATOR . 'font-resource.json'), true);
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'WideImage' . DIRECTORY_SEPARATOR . 'WideImage.php';
		$img = WideImage::load(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-preview.png');
		if ($_REQUEST['mode'] == 'jpg')
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
			$canvas->writeText(19, $i, "All Work and No Pay Makes Wishcraft a Dull Bored!");
			$i=$i+$point + $step;
		}
		$canvas->useFont($font, 14, $img->allocateColor(0, 0, 0));
		$canvas->writeText('right', 'bottom', API_URL);
		header("Content-type: ".getMimetype($state));
		die($img->output($state));
		exit(0);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $fontspaces; ?> Preview || Chronolabs Cooperative</title>
<link rel="stylesheet" href="https://css.ringwould.com.au/3/gradientee/stylesheet.css" type="text/css" />
<link rel="stylesheet" href="https://css.ringwould.com.au/3/shadowing/styleheet.css" type="text/css" />
<style>
 * {
 font-family: <?php echo $fontname; ?>;
 }<?php echo getURIData($source ."/v2/survey/font/" . $_REQUEST['key'] . "/css.api"); ?>
 
</style>
</head>

<body>
<div class="main">
    <div style="margin-top: 19px; clear: both; padding: 8px;">
	    <h1><?php echo $fontspaces; ?> Preview</h1>
	    <blockquote style="font-family: <?php echo $fontname; ?> !important;">
	    	<?php for($size = mt_rand(225,295); $size >= mt_rand(65,110); $size = $size - mt_rand(7,12)) { ?>
	        <font color="#012101" style="font-family: <?php echo $fontname; ?>; font-size: <?php echo $size; ?>% !important; margin-bottom: 9px;">All Work and No Pay Makes <?php echo $entity; ?> a Dull Bored!</font><br/>
	        <?php } ?>
	    </blockquote>
	</div>
</div>
</html>
<?php 
