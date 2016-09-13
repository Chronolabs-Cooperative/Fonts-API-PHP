!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/326843949"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/326843949"









/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=fontti+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/326843949"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=tiparo+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/326843949"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/326843949" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/326843949/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F326843949"