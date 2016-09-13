!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"





/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=fontti+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=download+bunyaganan" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=pobieranie+czcionki" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=Font+manual" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=tiparo+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/361633027/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F361633027"