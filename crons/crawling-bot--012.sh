!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=lettertype+te+downloaden" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=font+aflaai" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=scaricare+LINUX" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=download+bunyaganan" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://duckduckgo.com/?q=font+nedladdning" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195"


/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/710424195/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F710424195"