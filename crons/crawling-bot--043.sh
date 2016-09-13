!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=descargar+fonte" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=font+shkarko" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=wosasintha+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869"




/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=font+shkarko" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/920266869/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F920266869"