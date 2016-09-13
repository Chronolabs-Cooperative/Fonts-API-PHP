!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=font+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579"





/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=endri-tsoratra+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579"



/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1008807579/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1008807579"