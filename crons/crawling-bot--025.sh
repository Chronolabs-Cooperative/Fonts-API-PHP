!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=Font+manual" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=muat+turun+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100"



/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/45813100/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F45813100"