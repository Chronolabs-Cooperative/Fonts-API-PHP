!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=descargar+fonte" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=font+aflaai" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=Schrift+herunterladen" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1057243203/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1057243203"