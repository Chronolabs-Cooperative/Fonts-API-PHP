!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=la+descarga+de+fuentes" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=pobieranie+czcionki" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=la+descarga+de+fuentes" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901"


/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/472552901/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F472552901"