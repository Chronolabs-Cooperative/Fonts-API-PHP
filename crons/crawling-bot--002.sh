!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/553566605"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/553566605"







/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=font+shkarko" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/553566605"



/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/553566605" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/553566605/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F553566605"