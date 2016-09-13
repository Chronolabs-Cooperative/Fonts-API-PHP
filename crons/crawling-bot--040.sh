!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=prenos+pisave" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=endri-tsoratra+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=harflarning+yuklab" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=font+nedladdning" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=font+nedladdning" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1025517216/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1025517216"