!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.dogpile.com/search/web?q=download+bunyaganan" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=wosasintha+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.baidu.com/s?wd=font+aflaai" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"

/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.ask.com/web?q=wosasintha+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"



/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://www.google.com.au/search?q=muat+turun+font" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"
/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "http://www.bing.com/search?q=thwebula+ifonti" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705"
/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/1045351705/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F1045351705"