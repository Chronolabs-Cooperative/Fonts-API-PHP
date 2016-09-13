!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1087153384"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1087153384"










/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1087153384"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1087153384" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1087153384/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1087153384"