<?php
error_reporting(E_ERROR);
set_time_limit(1999);
require_once __DIR__.'/functions.php';
print_r(getGlyphArrayFromXML(xml2array(file_get_contents(__DIR__."/examples/glyph.glif"))));

?>