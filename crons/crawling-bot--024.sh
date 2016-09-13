!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=Font+manual" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=fondi+lae+alla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=Schrift+herunterladen" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=tiparo+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=lettertype+te+downloaden" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/531267965/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F531267965"