!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=fontti+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=fontti+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=font+indir" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739"


/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/859087739/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F859087739"