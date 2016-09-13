!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/988411590"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/988411590"




/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=wosasintha+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/988411590"




/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=Schrift+herunterladen" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/988411590"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/988411590" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/988411590/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F988411590"