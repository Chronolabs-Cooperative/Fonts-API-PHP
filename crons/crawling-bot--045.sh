!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1374495462"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1374495462"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=fondi+lae+alla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1374495462"










/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1374495462" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1374495462/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1374495462"