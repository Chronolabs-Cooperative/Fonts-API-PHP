!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=font+aflaai" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=Schrift+herunterladen" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=font+shkarko" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=pobieranie+czcionki" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1105194828/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1105194828"