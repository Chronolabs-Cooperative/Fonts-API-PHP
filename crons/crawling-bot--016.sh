!#/sh/bash
mkdir "/tmp/Fonts-Uploads"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net"
mkdir "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/979885337"
cd "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/979885337"






/usr/bin/wget --span-host --bind-address=fonts.labs.coop --referer=http://fonts.labs.coop --level=17 --recursive -x -k --continue --accept= "https://au.search.yahoo.com/search?p=wosasintha+Download" "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/979885337"




/usr/bin/tee "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/979885337" > "/tmp/Fonts-Uploads/wishcraft@users.sourceforge.net/979885337/finished.dat"
/usr/bin/php -q "/var/www/fonts.labs.coop/crons/register-crawling.php?path=%2Ftmp%2FFonts-Uploads%2Fwishcraft%40users.sourceforge.net%2F979885337"