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


	global $domain, $protocol, $business, $entity, $contact, $referee, $peerings, $source, $fontnames;
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta property="og:title" content="<?php echo API_VERSION; ?>"/>
<meta property="og:type" content="api<?php echo API_TYPE; ?>"/>
<meta property="og:image" content="<?php echo API_URL; ?>/assets/images/logo_500x500.png"/>
<meta property="og:url" content="<?php echo (isset($_SERVER["HTTPS"])?"https://":"http://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>" />
<meta property="og:site_name" content="<?php echo API_VERSION; ?> - <?php echo API_LICENSE_COMPANY; ?>"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="rating" content="general" />
<meta http-equiv="author" content="wishcraft@users.sourceforge.net" />
<meta http-equiv="copyright" content="<?php echo API_LICENSE_COMPANY; ?> &copy; <?php echo date("Y"); ?>" />
<meta http-equiv="generator" content="Chronolabs Cooperative (<?php echo $place['iso3']; ?>)" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo API_VERSION; ?> || <?php echo API_LICENSE_COMPANY; ?></title>

<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/style.css" type="text/css" />
<!-- Custom Fonts -->
<link href="<?php echo API_URL; ?>/assets/media/Labtop/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Bold Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Superwide Boldish/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Thin/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Labtop Unicase/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/LHF Matthews Thin/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Life BT Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Life BT Bold Italic/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Bold/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo API_URL; ?>/assets/media/Prestige Elite Normal/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/gradients.php" type="text/css" />
<link rel="stylesheet" href="<?php echo API_URL; ?>/assets/css/shadowing.php" type="text/css" />

<style>

<?php 

foreach( $GLOBALS['fontcss'] as $key => $values)
{
	foreach($values as $name => $css)
	{
		echo "<!-- Font Key name: $name -->\n".implode("\n\n", $css)."\n\n"; 
	}

}?>
	
</style>
</head>

<body>
<div class="main">
    <div style="margin-bottom: 19px; padding: 15px; clear: both;">
    	<img style="float: right; margin: 11px; width: auto; height: auto; clear: none;" src="<?php echo API_URL; ?>/assets/images/logo_350x350.png" />
    	<h1><?php echo API_VERSION; ?> (<?php echo API_LICENSE_COMPANY;?>) ~ Font's Previewer</h1>
    	<p>The following <?php echo count($fontnames); ?> font(s) are shown in this preview:-</p><p style="height: auto; clear: both;"><ol style="height: auto; clear: both;"><li style="width: 25%; float: left;"><?php echo implode('</li><li style="width: 25%; float: left;">', $names = $fontnames); ?></li></ol></p>
    </div>
<?php foreach( $GLOBALS['fontcss'] as $key => $values)
	{
		foreach($values as $fontname => $css)
		{ ?>
    <div style="margin-top: 19px; clear: both; padding: 8px;">
    	<h2><?php echo $fontname; ?> Image Preview</h2>
    	<blockquote >
    		<img src="<?php echo API_URL; ?>/v2/font/<?php echo $key; ?>/preview/jpg.api" width="100%" />
    	<?php if (isset($GLOBALS['downloaduris'][$fontname])) { ?>
			<div style="padding-top: 13px; padding-left: 13px; color: #ffa !important; font-size: 66.5345%; text-align: right;"><strong>Download Now:&nbsp;</strong><em><?php foreach($GLOBALS['downloaduris'][$fontname] as $type => $uri) { echo "<a href='$uri' target='_blank'>*.$type</a>;&nbsp;&nbsp;"; } ?></em></div>
	<?php } ?>
    	</blockquote>
	    <h2><?php echo $fontname; ?> CSS/HTML Preview</h2>
	    <blockquote style="font-family: '<?php echo $fontname; ?>' !important;">
<?php for($size = mt_rand(225,295); $size >= mt_rand(65,110); $size = $size - mt_rand(7,12)) { ?>
	        <font color="#012101" style="font-family: '<?php echo $fontname; ?>' !important; font-size: <?php echo $size; ?>%; margin-bottom: 9px;">All Work and No Pay Makes <?php echo $entity; ?> a Dull Bored!</font><br/>
<?php } 
		if (isset($GLOBALS['downloaduris'][$fontname])) { ?>
			<div style="padding-top: 13px; padding-left: 13px; color: #ffa !important; font-size: 86.5345%; text-align: right;"><strong>Download Now:&nbsp;</strong><em><?php foreach($GLOBALS['downloaduris'][$fontname] as $type => $uri) { echo "<a href='$uri' target='_blank'>*.$type</a>;&nbsp;&nbsp;"; } ?></em></div>
	<?php } else die(print_r($GLOBALS['downloaduris'], true));?>
	    </blockquote>
	</div>
    <?php }
	} ?>
	   <?php if (file_exists(API_FILE_IO_FOOTER)) {
    	readfile(API_FILE_IO_FOOTER);
    }?>	
    <?php if (!in_array(whitelistGetIP(true), whitelistGetIPAddy())) { ?>
    <h2>Limits</h2>
    <p>There is a limit of <?php echo MAXIMUM_QUERIES; ?> queries per hour. You can add yourself to the whitelist by using the following form API <a href="http://whitelist.<?php echo domain; ?>/">Whitelisting form (whitelist.<?php echo domain; ?>)</a>. This is only so this service isn't abused!!</p>
    <?php } ?>
    <h2>The Author</h2>
    <p>This was developed by Simon Roberts in 2013 and is part of the Chronolabs System and api's.<br/><br/>This is open source which you can download from <a href="https://sourceforge.net/projects/chronolabsapis/">https://sourceforge.net/projects/chronolabsapis/</a> contact the scribe  <a href="mailto:wishcraft@users.sourceforge.net">wishcraft@users.sourceforge.net</a></p></body>
</div>
</html>
<?php 
