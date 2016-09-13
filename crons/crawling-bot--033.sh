!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=Ukukhuphela+befonti" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=fondi+lae+alla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=descargar+fonte" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1080800747/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1080800747"