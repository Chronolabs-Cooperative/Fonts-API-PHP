!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=muat+turun+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216"




/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=font+shkarko" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/264858216/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F264858216"