!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=font+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=download+bunyaganan" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=fonte+jarolla" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=font+preuzimanje" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325"



/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/535048325/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F535048325"