!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=endri-tsoratra+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=lettertype+te+downloaden" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=la+descarga+de+fuentes" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=muat+turun+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1338949673/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1338949673"