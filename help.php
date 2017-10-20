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

	$nodes = getExampleNodes();
	$random = getExampleNodes();
	$tries = -1;
	while(empty($font)&&$tries<11)
	{
		$tries++;
		$font = getExampleFingerprint();
	}
	if (!empty($font))
	{
		$fontmd5 = $font['id'];
		$fontfiles = getExampleFontFiles($fontmd5);
	}
	$glyph = '0';
	if (!empty($fontmd5))
	{
		$data = getFontsDataArray($fontmd5, 'data','json', 'v2');
		$keys = array_keys($data['UnicodeCharMap']);
		$glyph = $keys[mt_rand(0, count($keys)-1)];
	}

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
<!-- AddThis Smart Layers BEGIN -->
<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50f9a1c208996c1d"></script>
<script type="text/javascript">
  addthis.layers({
	'theme' : 'transparent',
	'share' : {
	  'position' : 'right',
	  'numPreferredServices' : 6
	}, 
	'follow' : {
	  'services' : [
		{'service': 'facebook', 'id': 'Chronolabs'},
		{'service': 'twitter', 'id': 'JohnRingwould'},
		{'service': 'twitter', 'id': 'ChronolabsCoop'},
		{'service': 'twitter', 'id': 'Cipherhouse'},
		{'service': 'twitter', 'id': 'OpenRend'},
	  ]
	},  
	'whatsnext' : {},  
	'recommended' : {
	  'title': 'Recommended for you:'
	} 
  });
</script>
<!-- AddThis Smart Layers END -->
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

</head>
<body>
<div class="main">
	<img style="float: right; margin: 11px; width: auto; height: auto; clear: none;" src="<?php echo API_URL; ?>/assets/images/logo_350x350.png" />
    <h1><?php echo API_VERSION; ?> -- <?php echo API_LICENSE_COMPANY; ?></h1>
    <p>This is an API Service for providing fonts to your application or website. It provides the the fonts through either fingerprinting checksums for the font or keywords from the nodes list when access the API inclusing JSON, XML, Serialisation, HTML, RAW, CSS and raw file outputs.</p>
    <p>Here is a sample of what a converted font download from an eot produces, it is the same for most of them, the other thing that will be in this is a file.diz ~ distribution file.<br/><br>Download Sample: <a href="<?php echo API_URL; ?>/font-download-example.zip" target="_blank"><?php echo API_URL; ?>/font-download-example.zip</a></p>
    <h2>API Runtime Statistics</h2>
    <ul>
    	<li>There is currently <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(getFontsWaitingQueuing(),0); ?></strong> fonts waiting to be queued for conversion to all formats!</li>
    	<li>In the conversion queue there is <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(getFontsWaitingConvertions(),0); ?></strong> fonts waiting to have format conversion!</li>
    	<li>Currently at the moment there is <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(getFontsWaitingConverted(),0); ?></strong> fonts waiting to be packed after conversion!</li>
    	<li>There is waiting to commence surveying <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(getSurveysWaiting(),0); ?></strong> fonts in queue!</li>
    	<li>At the moment there is <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(abs(getSurveysQueued()-getFontsWaitingConverted()),0); ?></strong> survey's out in the field for categorisation!</li>
    	<li>Between <em><?php echo date('Y/m/d 00:00:00', time());?></em> and <em><?php echo date('Y/m/d 23:59:59', time() + 3600 *24); ?></em> there is <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(getSurveysExpiring(strtotime(date('Y/m/d 00:00:00', time())), strtotime(date('Y/m/d 23:59:59', time() + 3600 *24))),0); ?></strong> survey's expiring!</li>
    	<li>Total amount of font's released <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format(getFontsReleased(),0); ?></strong> and available on the API!</li>
    	<li>Lost font uploads to upload again <strong style="font-size:135.98765%; color: rgba(0, 0, 255, 0.79876);"><?php echo number_format($lost = getLostUploads(),0); ?></strong> <?php if($lost>0) { ?> + Explore: <a href="<?php echo API_URL ?>/lost" target="_blank"><?php echo API_URL ?>/lost</a> <?php } ?></li>
    </ul>
    <h2>Code API Documentation</h2>
    <p>You can find the phpDocumentor code API documentation at the following path :: <a href="<?php echo API_URL; ?>/docs/" target="_blank"><?php echo API_URL; ?>/docs/</a>. These should outline the source code core functions and classes for the API to function!</p>
    <h2>PREVIEW Document Output</h2>
    <p>This is done with the <em>preview.api</em> or <em>glyphs.api</em>  extension at the end of the url, this is for the functions for fonts on the API!</p>
    <blockquote>
        <font class="help-title-text">This is for a html output for a preview of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/jpg output for a preview of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview/jpg.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview/jpg.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/png output for a preview of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview/png.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview/png.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/gif output for a preview of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview/gif.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/preview/gif.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/jpg output for a locality name/title of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/naming/jpg.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/naming/jpg.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/png output for a locality name/title of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/naming/png.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/naming/png.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/gif output for a locality name/title of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/naming/gif.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/naming/gif.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/jpg output for a preview of the font glyph html unicode &amp#<?php echo $glyph; ?>; with the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/glyph/<?php echo $glyph; ?>/jpg.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/glyph/<?php echo $glyph; ?>/jpg.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/png output for a preview of the font glyph html unicode &amp#<?php echo $glyph; ?>; with the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/glyph/<?php echo $glyph; ?>/png.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/glyph/<?php echo $glyph; ?>/png.api</a></font><br /><br />
        <font class="help-title-text">This is for a image/gif output for a preview of the font glyph html unicode &amp#<?php echo $glyph; ?>; with the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/glyph/<?php echo $glyph; ?>/gif.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/glyph/<?php echo $glyph; ?>/gif.api</a></font><br /><br />
        <font class="help-title-text">This is for a html output for a preview of the fonts in the node list</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/preview.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/preview.api</a></font><br /><br />
        <font class="help-title-text">This is for a html output for one of any as a random font based on the resource!! The font will have the name: 'Font Named As';</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/random/any/font-named-as/preview.api" target="_blank"><?php echo API_URL; ?>/v2/random/any/font-named-as/preview.api</a></font><br /><br />
        <font class="help-title-text">This is for a html output for one of node based random which is <strong>bold</strong> &amp;  <strong><em>italic</font> font. The font will have the name: 'Font Named As';<br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/random/any-bold-italic/font-named-as/preview.api" target="_blank"><?php echo API_URL; ?>/v2/random/any-bold-italic/font-named-as/preview.api</a></font><br /><br />
        <font class="help-title-text">This is for a html output for a selection of a random font based on the node list!! The font will have the name: 'Font Named As';</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/random/<?php echo $random; ?>/font-named-as/preview.api" target="_blank"><?php echo API_URL; ?>/v2/random/<?php echo $random; ?>/font-named-as/preview.api</a></font><br /><br />
    </blockquote>
    <h2>CSS Document Output</h2>
    <p>This is done with the <em>css.api</em> extension at the end of the url, this is for the functions for fonts on the API!</p>
    <blockquote>
        <font class="help-title-text">This is for a css output for a stylesheet of the font in the fingerprint listed in the URI!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/css.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/css.api</a></font><br /><br />
        <font class="help-title-text">This is for a css output for a stylesheet of the fonts in the node list</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/css.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/css.api</a></font><br /><br />
        <font class="help-title-text">This is for a css output for a stylesheet for a selection of one of any of the font libraries a random font! The font will have the name: 'Font Named As';</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/random/any/font-named-as/css.api" target="_blank"><?php echo API_URL; ?>/v2/random/any/font-named-as/css.api</a></font><br /><br />
        <font class="help-title-text">This is for a css output for one of node based random any which is <strong>bold</strong> &amp;  <strong><em>italic</font> font. The font will have the name: 'Font Named As';<br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/random/any-bold-italic/font-named-as/css.api" target="_blank"><?php echo API_URL; ?>/v2/random/any-bold-italic/font-named-as/css.api</a></font><br /><br />
        <font class="help-title-text">This is for a css output for a stylesheet for a selection of a random font based on the node list! The font will have the name: 'Font Named As';</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/random/<?php echo $random; ?>/font-named-as/css.api" target="_blank"><?php echo API_URL; ?>/v2/random/<?php echo $random; ?>/font-named-as/css.api</a></font><br /><br />
    </blockquote>
    <h2>Font Information & Meta-information Output</h2>
    <p>This is done with the <em>rss.api</em>, <em>download.api</em>, <em>diz.api</em>, <em>json.api</em> or <em>serial.api</em> extension at the end of the url, this is for the meta-information & data for fonts on the API!</p>
    <blockquote>
        <font class="help-title-text">This will produce 20 real-time RSS Feed of font releases; when a new one is on the system it will be zero day listed via this RSS Feed!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/data/zeroday/rss.api?20" target="_blank"><?php echo API_URL; ?>/v2/data/zeroday/rss.api?20</a></font><br /><br />
        <font class="help-title-text">Top 20 popular fonts on the resource, if something is getting traffic we will tell you when it is comodities!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/data/popular/rss.api?20" target="_blank"><?php echo API_URL; ?>/v2/data/popular/rss.api?20</a></font><br /><br />
        <?php foreach(getArchivingShellExec() as $type => $exec) { ?>
        <font class="help-title-text">This will session a file download of the complete font archive in <font class="help-url-example">*.<?php echo $type; ?></font> format relating to the font fingerprint you enter!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/data/<?php echo $fontmd5; ?>/<?php echo $type; ?>/download.api" target="_blank"><?php echo API_URL; ?>/v2/data/<?php echo $fontmd5; ?>/<?php echo $type; ?>/download.api</a></font><br /><br />
         <?php } ?>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/data/<?php echo $fontmd5; ?>/diz.api" target="_blank"><?php echo API_URL; ?>/v2/data/<?php echo $fontmd5; ?>/diz.api</a></font><br /><br />
        <font class="help-title-text">This will list all the glyph, and associated meta data as well as contributors and other useful information about the font resource!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/data/<?php echo $fontmd5; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/data/<?php echo $fontmd5; ?>/json.api</a></font><br /><br />
        <font class="help-title-text">This will list all the peers to the API!</font><br/>
  		<font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/peers/all/json.api" target="_blank"><?php echo API_URL; ?>/v2/peers/all/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all callbacks and pinings traces of all the API peer's URL's on the API for the font fingerprint</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/callbacks/<?php echo $fontmd5; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/callbacks/<?php echo $fontmd5; ?>/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all repository store of all the API peer's URL's on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/download/repository/json.api" target="_blank"><?php echo API_URL; ?>/v2/peers/repository/json.api</a></font><br /><br />
    </blockquote>
    <h2>Font API Callbacks & Resource Management</h2>
    <p>This is done with the <em>callback.api</em> extension at the end of the url, this is for the meta-information & data for fonts on the API!</p>
    <blockquote>
    	<font class="help-title-text">This is for a list of all font download callback store of all the API peer's URL's on the API, you have to specify the URI/URL for your callback and your email address as well as the hash for the font you want the call back on!</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/archive/<?php echo $fontmd5; ?>/callback.api?uri=http://your.callback.com/uri.php&email=your@emailaddress.com" target="_blank"><?php echo API_URL; ?>/v2/archive/<?php echo $fontmd5; ?>/callback.api?uri=http://your.callback.com/uri.php&amp;email=your@emailaddress.com</a></font><br /><br />
        <h3> Variable passed by function 'archive' callback via $_POST</h3>
        <code style="margin-left: 54px; max-height: 46px; overflow: v-scroll; height: 36px !important; margin: 32px; ">
			<?php 	if (!isset($_SESSION['network']))
						$_SESSION['network'] = getIPIdentity(whitelistGetIP(true), true);
					unset($_SESSION['network']['whois']);
					$_SESSION['network']['whois'] = '...';
					echo "<br/>/** " . API_URL . "/v2/archive/" .  $fontmd5 . "/callback.api ~ variables on call!";
					echo "<br/> * <br/>";
					echo " * \t\t\t\$_POST['format'] = 'zip';<br/>";
					foreach($_SESSION['network'] as $kui => $id)
						echo " * \t\t\t\$_POST['ipid']['$kui'] = '" . $id. "';<br/>"; 
					echo "*/<br/><br/>";
?>
		</code><br/>
    	<font class="help-title-text">Read Hit Callback is for a <strong>signular font files trackback ping counter</strong> read hit! Statical ping counter for the font fingerprint! You have to specify the URI/URL for your callback and your email address as well as the hash for the font you want the call back on!</strong></font><br/>
		<font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonthit/<?php echo $fontmd5; ?>/callback.api?uri=http://your.callback.com/uri.php&email=your@emailaddress.com" target="_blank"><?php echo API_URL; ?>/v2/fonthit/<?php echo $fontmd5; ?>/callback.api?uri=http://your.callback.com/uri.php&amp;email=your@emailaddress.com</a></font><br /><br />
		<h3> Variable passed by function 'fonthit' callback via $_POST</h3>
        <code style="margin-left: 54px; max-height: 46px; overflow: v-scroll; height: 36px !important; margin: 32px; ">
			<?php 	echo "<br/>\t/** " . API_URL . "/v2/fonthit/" .  $fontmd5 . "/callback.api ~ variables on call!";
					echo "<br/>\t * <br/>";
					echo "\t * \t\t\$_POST['type'] = 'ttf';<br/>";
					foreach($_SESSION['network'] as $kui => $id)
						echo "\t * \t\t\$_POST['ipid']['$kui'] = '" . $id. "';<br/>"; 
					echo "*/<br/><br/>";
?>
		</code><br/>
    </blockquote>
    <h2>UPLOAD Document Output</h2>
    <p>This is done with the <em>upload.api</em> extension at the end of the url, you can upload and stage fonts on the API permanently and upload them in the file formats of either each one by one or in an archive ZIP file the font formats we will convert and use <strong style="text-shadow: 0px 0px 0px !important;">( *.<?php $formats = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-converted.diz'); sort($formats); echo implode("  *.", array_unique($formats)); ?> )</strong> ~~ simply put them in a compressed archive if you want in any of these formats <strong style="text-shadow: 0px 0px 0px !important;">( *.<?php $packs = file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'packs-converted.diz'); sort($packs); echo implode("  *.", array_unique($packs)); ?> )</strong> containing any of these file formats any other will be ignored, you will be notified and sent a copy of the web font when they are converted with example CSS via the email address.<br/><br/>The cataloging of your font whether it has been encountered or not is based on forensic fingerprinting of the font as well as running a contributor's survey to you or the scope of contact emails you have provided, this process can take up to several days to complete, or as when the batch of surveys is complete on that uploaded font; you will have no more than 8 surveys at any one time to complete, and we do not spam your email lists you place in addition to the upload! We only select based on random a lot a selection of the emails; maybe no more than 7 - 14 at any one time and people have an option to opted out and not contribute at all on the service, this all executes on some proficient scheduled tasks on the service as it is scheduled on the system!</p>
    <blockquote>
        <?php 	if (!isset($_SESSION['upldform']))
        			$_SESSION['upldform'] = getURIData(API_URL."/v2/uploads/forms.api", 560, 560,
				array('return' => '".API_URL."', 
				'callback' => '')); 
        		echo $_SESSION['upldform']; ?>
		<h3>Code Example:</h3>
		<div style="max-height: 375px; overflow: scroll;">
			<pre style="margin: 14px; padding: 12px; border: 2px solid #ee43a4;">
<?php echo htmlspecialchars($_SESSION['upldform']); ?>
			</pre>
		</div>
    </blockquote>
    <h2>RELEASES Document Output</h2>
    <p>This is done with the <em>releases.api</em> extension at the end of the url, this form will subscribe/unsubscribe you from recieving releases of fonts after they have been catelogued by the survey!</p>
    <blockquote>
        <?php if (!isset($_SESSION['rlesform']))
        		$_SESSION['rlesform'] = getURIData(API_URL."/v2/releases/forms.api", 560, 560, 
				array('return' => '".API_URL."', 
				'callback' => '')); 
        		echo $_SESSION['rlesform']; ?>
		<h3>Code Example:</h3>
		<div style="max-height: 375px; overflow: scroll;">
			<pre style="margin: 14px; padding: 12px; border: 2px solid #ee43a4;">
<?php echo htmlspecialchars($_SESSION['rlesform']); ?>
			</pre>
		</div>
    </blockquote>
    <h2>FORMS Document Output</h2>
    <p>This is done with the <em>forms.api</em> extension at the end of the urland will provide a HTML Submission form for the API in options the only modal for this at the moment is an Upload form!</p>
    <blockquote>
    <font class="help-title-text">The following examples for <em>forms.api</em> uses the cURL function <strong>getURIData()</strong> in PHP to use the example below in PHP!</font><br/><br/>
    <pre style="margin: 14px; padding: 12px; border: 2px solid #ee43a4;">
&lt;?php
	if (!function_exists("getURIData")) {
	
		/* function getURIData() cURL Routine
		 * 
		 * @author 		Simon Roberts (labs.coop) wishcraft@users.sourceforge.net
		 * @return 		string
		 */
		function getURIData($uri = '', $timeout = 25, $connectout = 25, $post_data = array())
		{
			if (!function_exists("curl_init"))
			{
				return file_get_contents($uri);
			}
			if (!$btt = curl_init($uri)) {
				return false;
			}
			curl_setopt($btt, CURLOPT_HEADER, 0);
			curl_setopt($btt, CURLOPT_POST, (count($posts)==0?false:true));
			if (count($posts)!=0)
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
?&gt;

		</pre><br/><br/>
        <font class="help-title-text">You basically import and output to the buffer the HTML Submission form for the form to be emailed new releases of a font at the following URI: <strong><?php echo API_URL; ?>/v2/releases/forms.api</strong> -- this will generate a HTML form with the return path specified for you to buffer -- see example below in PHP!</font><br/>
		<pre style="margin: 14px; padding: 12px; border: 2px solid #ee43a4;">
&lt;?php
	// output the table & form
	echo getURIData("<?php echo API_URL; ?>/v2/releases/forms.api", 560, 560, 
				 
				 /* URL Upload return after submission (required) */
				array('return' => '<?php echo $source; ?>', 
				
				/* URL for API Callback for progress and archive with data  (optional) */
				'callback' => '<?php echo API_URL; ?>/v2/releases/callback.api'));
?&gt;
		</pre><br/><br/>
		<font class="help-title-text">You basically import and output to the buffer the HTML Submission form for uploading a font at the following URI: <strong><?php echo API_URL; ?>/v2/uploads/forms.api</strong> -- this will generate a HTML form with the return path specified for you to buffer -- see example below in PHP!</font><br/>
		<pre style="margin: 14px; padding: 12px; border: 2px solid #ee43a4;">
&lt;?php
	// output the table & form
	echo getURIData("<?php echo API_URL; ?>/v2/uploads/forms.api", 560, 560, 
				 
				 /* URL Upload return after submission (required) */
				array('return' => '<?php echo $source; ?>', 
				
				/* URL for API Callback for progress and archive with data  (optional) */
				'callback' => '<?php echo API_URL; ?>/v2/uploads/callback.api'));
?&gt;
		</pre>
		 <font color="#2e31c1; font-size: 134%; font-weight: 900;">An example of the callback routines the variables are outlined in this file you click and download the PHP Routines examples: <a href="/callback-example.php" target="_blank">callback-example.php</a></font>
    </blockquote>    
    <h2>Font File Data Output</h2>
    <p>This is done with the extension of the fontfile type with the fingerprint for the fontfile, this can either be the grouping md5 or individual file!</p>
    <blockquote>
    	<?php $fontfiles = cleanWhitespaces(file(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'font-supported-v2.diz'));
    		sort($fontfiles);
    	foreach( $fontfiles as $type ) { ?>
        <font class="help-title-text">This is for a <strong><?php echo $type; ?> output</strong> for a data of the font under the any fingerprint for it! Mime-type will be: <strong><em><?php echo getMimetype($type); ?></font></font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/<?php echo $type; ?>.api" target="_blank"><?php echo API_URL; ?>/v2/font/<?php echo $fontmd5; ?>/<?php echo $type; ?>.api</a></font><br /><br />
        <?php } ?>
    </blockquote>
    <h2>Serialisation Document Output</h2>
    <p>This is done with the <em>serial.api</em> extension at the end of the url, this is for the functions for fonts on the API!</p>
    <blockquote>
        <font class="help-title-text">This is for a list of all nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/all/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/all/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the keys for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/keys/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/keys/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the fixes for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/fixes/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/fixes/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the typal for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/typal/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/typal/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/all/serial.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/all/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all the fonts identity checksum against names on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/identities/serial.api" target="_blank"><?php echo API_URL; ?>/v2/identities/serial.api</a></font><br /><br />       
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/all/1-20/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/all/1-20/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the keys for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/keys/1-20/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/keys/1-20/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the fixes for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/fixes/1-20/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/fixes/1-20/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the typal for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/typal/1-20/serial.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/typal/1-20/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  all the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/all/1-20/serial.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/all/1-20/serial.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the typal for fonts for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/serial.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/serial.api</a></font><br /><br />
    </blockquote>
    <h2>XML Document Output</h2>
    <p>This is done with the <em>xml.api</em> extension at the end of the url, this is for the functions for fonts on the API!</p>
    <blockquote>
        <font class="help-title-text">This is for a list of all nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/all/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/all/xml.api</a></font><br /><br />
         <font class="help-title-text">This is for a list of just the keys for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/keys/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/keys/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the fixes for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/fixes/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/fixes/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the typal for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/typal/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/typal/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/all/xml.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/all/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all the fonts identity checksum against names on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/identities/xml.api" target="_blank"><?php echo API_URL; ?>/v2/identities/xml.api</a></font><br /><br />       
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/all/1-20/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/all/1-20/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the keys for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/keys/1-20/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/keys/1-20/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the fixes for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/fixes/1-20/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/fixes/1-20/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the typal for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/typal/1-20/xml.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/typal/1-20/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  all the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/all/1-20/xml.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/all/1-20/xml.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the fonts for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/xml.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/xml.api</a></font><br /><br />
    </blockquote>
    <h2>JSON Document Output</h2>
    <p>This is done with the <em>json.api</em> extension at the end of the url, this is for the functions for fonts on the API!</p>
    <blockquote>
        <font class="help-title-text">This is for a list of all nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/all/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/all/json.api</a></font><br /><br />
         <font class="help-title-text">This is for a list of just the keys for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/keys/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/keys/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the fixes for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/fixes/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/fixes/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the typal for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/typal/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/typal/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of all the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/all/json.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/all/json.api</a></font><br /><br />       
        <font class="help-title-text">This is for a list of all the fonts identity checksum against names on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/identities/json.api" target="_blank"><?php echo API_URL; ?>/v2/identities/json.api</a></font><br /><br />       
        <font class="help-title-text">This is for a list at start of record 1 and the next 20 nodes for the fonts on the API</font><br/>    
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/all/1-20/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/all/1-20/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the keys for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/keys/1-20/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/keys/1-20/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the fixes for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/fixes/1-20/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/fixes/1-20/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  just the typal for nodes for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/nodes/typal/1-20/json.api" target="_blank"><?php echo API_URL; ?>/v2/nodes/typal/1-20/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list at start of record 1 and the next 20  all the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/all/1-20/json.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/all/1-20/json.api</a></font><br /><br />
        <font class="help-title-text">This is for a list of just the typal for fonts for the fonts on the API</font><br/>
        <font class="help-url-example"><a href="<?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/json.api" target="_blank"><?php echo API_URL; ?>/v2/fonts/<?php echo $nodes; ?>/json.api</a></font><br /><br />
   </blockquote>
  <?php if (file_exists($fionf = __DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'apis-labs.coop.html')) {
    	readfile($fionf);
    }?>	
    <?php if (!in_array(whitelistGetIP(true), whitelistGetIPAddy())) { ?>
    <?php if (defined("MAXIMUM_QUERIES")) { ?>
    <h2>Limits</h2>
    <p>There is a limit of <?php echo MAXIMUM_QUERIES; ?> queries per hour. You can add yourself to the whitelist by using the following form API <a href="http://whitelist.<?php echo domain; ?>/">Whitelisting form (whitelist.<?php echo domain; ?>)</a>. This is only so this service isn't abused!!</p>
    <?php }
    } ?>
    <h2>The Author</h2>
    <p>This was developed by Simon Roberts in 2013 and is part of the Chronolabs System and api's.<br/><br/>This is open source which you can download from <a href="https://sourceforge.net/projects/chronolabsapis/">https://sourceforge.net/projects/chronolabsapis/</a> contact the scribe  <a href="mailto:wishcraft@users.sourceforge.net">wishcraft@users.sourceforge.net</a></p></body>
</div>
</html>
<?php 
