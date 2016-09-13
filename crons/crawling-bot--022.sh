!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=Font+manual" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=endri-tsoratra+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=Font+manual" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=font+indir" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287"



/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/851878287/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F851878287"