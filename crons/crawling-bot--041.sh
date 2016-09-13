!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"


/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=font+download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=pobieranie+czcionki" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=download+momotuhi" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=prenos+pisave" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=harflarning+yuklab" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/571505738/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F571505738"