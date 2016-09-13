!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=fondi+lae+alla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=fonte+jarolla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=font+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1127649266/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1127649266"