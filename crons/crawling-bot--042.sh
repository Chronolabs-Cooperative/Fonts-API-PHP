!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=download+vai" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=scaricare+LINUX" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=tiparo+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=Ukukhuphela+befonti" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644"





/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/207321644/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F207321644"