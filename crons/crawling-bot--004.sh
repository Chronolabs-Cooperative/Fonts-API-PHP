!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=Font+manual" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=preuzimanje+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=lettertype+te+downloaden" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=harflarning+yuklab" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://s.webwombat.com.au/aus?ix=pobieranie+czcionki" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999"

/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/602833999/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F602833999"