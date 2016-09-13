!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567"






/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=endri-tsoratra+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=prenos+pisave" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=fonte+jarolla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/376334567/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F376334567"