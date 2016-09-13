!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=prenos+pisave" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=harflarning+yuklab" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141"





/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=font+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/833527141/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F833527141"