## Chronolabs Cooperative presents
# Fonting Repository Services API  - http://fonty.snails.email -

# BASIC INSTALLATION MANUAL
## by. Simon Antony Roberts (Sydney)
## simon@snails.email

# Foreword

In this manual we will take you through all the conditions which you will encounter in general Ubuntu or Debian environment setting up this API. It will include cronjobs as well as any basis of general configuration you will encounter with your API in configuring and definition operations parameters in most types of places you find Ubuntu or Debian.

Download the API archive of PHP files from: https://sourceforge.net/p/chronolabsapis/files/fonts.labs.coop

# Setting up the environment
You will first have to set up the environment running the following script at root on Ubuntu or Debian Server it will install all the system environment variables you require to do an installation:-

    
    $ sudo apt-get install traceroute rar* p7zip-full unace unrar* zip unzip sharutils sharutils uudeview mpack arj cabextract file-roller fontforge tasksel nano bzip2 cpulimit
    

Now you will have to execute 'tasksel' in root with the 'sudo' precursor for this to install the LAMP environment; run the following command and install the LAMP environment.

    
    $ sudo tasksel

    
Now you have to make your paths for the system to operate from there is a few the following paths we will discuss. 
/fonts/Unpacking this folder is used to unpack zips to be staged into the conversion area anything that isn't a font gets deleted from this path. /fonts/Conversion this folder is using for crashing the conversion to the placement of staying open for survey or being packed. /fonts/Sorting this folder is used for any sorting that occurs currently use just after Unpacking the font archives. /fonts/Fonting this is the folder for your fonts SVN if your using this feature to move to offline position as most fonts unless they are popular can be offlined to an SVN or if you want to alter the code a Git repository while they are not being used.

/tmp/Fonts-Uploads this is the folder used to take uploads from the server before they get moved to unpacking by the upload cronjob. /tmp/Fonts-Cache this is the general purpose cache folder for storing font files components in cache.

You will need to create these fonts folders now with 'mkdir' these are all listed in the constants.php if you need to alter them, you may find if you are handling a large amount of fonts as on average a storage zip is around 10 – 20Mb's but in complete glyph listing can be upwards of 120Mbs so each font file in storing in all formats will use this sort of file size, so you may want find some instructions on setting an remote local via webdav and mount SVN that is stored on somewhere where it will be cheaper to store the file-base this apart from retrieving and sending fonts doesn't have huge read and write.
You will now need to make these paths or the paths you have decided and set them in the constants. The following commands will do this (We are assuming your user-name is 'web' in this example of calls to do on the ubuntu service.

    
    $ sudo mkdir /fonts
    $ sudo mkdir /fonts/Unpacking
    $ sudo mkdir /fonts/Converting
    $ sudo mkdir /fonts/Sorting
    $ sudo mkdir /fonts/Fonting
    $ sudo mkdir /fonts/Cache
    $ sudo mkdir /tmp/Fonts-Uploads
    $ sudo mkdir /tmp/Fonts-Cache
    $ sudo chown -Rfv web:www-data /fonts
    $ sudo chown -Rfv web:www-data /tmp/Fonts*
    $ sudo chmod -Rfv 0777 /fonts
    $ sudo chmod -Rfv 0777 /tmp/Fonts*
    

We are going to assume for the fonting api runtime PHP files you are going to store them in /var/www/fonts-api and this will be the path you have to unpack the downloaded archive from Chronolabs APIs on sourceforge.net into with the contants.php listed in the root of this folder.
Setting Up Apache 2 (httpd)
We are going to assume your domain your setting it up on is a sub-domain of mysite.com so the following example in installing and setting up Apache 2 examples will place this on the sub-domain of fonts.mysite.com.

You will have to make the file /etc/apache2/sites-available/fonts.mysite.com.conf which you can with the following command:-
$ sudo nano /etc/apache2/sites-available/fonts.mysite.com.conf
You need to put the following configuration in to run a standard site, there is more to add for SSL which is not included in this example but you can find many examples on what to add to this file for port 443 for SSL which is duplicated code for port 443 not 80 with the SSL Certificates included, use the following code as your measure of basis of what to configure for apache 2 (httpd):-

    
    <VirtualHost *:80>
           ServerName fonts.mysite.com
           ServerAdmin webmaster@mysite.com
           DocumentRoot /var/www/fonts-api
           ErrorLog /var/log/apache2/fonts.mysite.com-error.log
           CustomLog /var/log/apache2/fonts.mysite.com-access.log common
           <Directory /var/www/fonts-api>
                   Options Indexes FollowSymLinks MultiViews
                   AllowOverride All
                   Require all granted
           </Directory>
    </VirtualHost>
    

You need to now enable this website in apache the following command will do this from root:-

    
    $ sudo a2ensite fonts.mysite.com
    $ sudo service apache2 reload
    

This is all that is involved in configuring apache 2 httpd on Debian/Ubuntu, the next step is the database.

# Installing API

Copy the contents of this distribution to your visually routable path via http(s) etc. Then poll the path required and run the install;

it requires apache2, nixi, iis etc and best with php5+;

## Configuring CPU throttling (CPULimit)

You now need to cpu load balance with cpulimit sometimes fontforge can really chew MIPS, run the following on the shell to edit the file that will intialise CPU Throttling on boot, as fontforge as mention can really chew and chew and chew your CPU usage:

    $ sudo nano /etc/rc.local
    
and put the following lines in it before the exit() command:

    pkill cpulimit
    /usr/bin/cpulimit -e mysql -b -q -l 67
    /usr/bin/cpulimit -e fontforge -b -q -l 36
    /usr/bin/cpulimit -e apache2 -b -q -l 35
    /usr/bin/cpulimit -e php -b -q -l 35
    /usr/bin/cpulimit -e cron -b -q -l 25
    /usr/bin/cpulimit -e wget -b -q -l 15

You may have to play around with the cpu throttling if your site is down ever to have the levels picture perfect, these are just estimates on the adverage service.

## Configuring Scheduled Tasks (CronJobs)

Once you have configured the above you will have to set up the cronjobs for all the scheduled tasks on the fonts-api these are all found on /var/www/fonts-api/crons where they are all required, some are for recovery if you have to drop your database and start from scratch will rebuild from your current processing.
You need to run the following command from root with sudo at the start unless you are doing user basis of cronjobs to set them below the command is the listing with 'suggested' not 'finited' schedules for tasks to operate; remember adjusting these could leave your system paralised with to much to do in one hit so execute the following and put these lines in: $ sudo crontab -e

## fonty.snails.email ###########################################################################################################
    */5 * * * * mkdir /tmp/Fonts-Uploads >/dev/null 2>&1
    */5 * * * * mkdir /tmp/Fonts-Cache >/dev/null 2>&1
    */7 * * * * chown -Rf www-data:www-data /tmp/Fonts-Uploads >/dev/null 2>&1
    */7 * * * * chown -Rf www-data:root /tmp >/dev/null 2>&1
    */7 * * * * chown -Rf www-data:root /fonts >/dev/null 2>&1
    */7 * * * * chmod -Rf 0777 /tmp/Font* >/dev/null 2>&1
    */7 * * * * chmod -Rf 0777 /fonts >/dev/null 2>&1
    */57 */3 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/lost-fonts-uploads.php >/dev/null 2>&1
    */11 */11 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/poll-peers.php >/dev/null 2>&1
    */13 */9 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/check-cache.php >/dev/null 2>&1
    */13 */2 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/convert-fonts.php >/dev/null 2>&1
    */36 */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/convert-fonts.php >/dev/null 2>&1
    */53 */4 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/convert-fonts.php >/dev/null 2>&1
    */11 */11 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/clean-tmp.php >/dev/null 2>&1
    */56 */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/surveying-fonts.php >/dev/null 2>&1
    */26 */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/surveying-reminder.php >/dev/null 2>&1
    */16 */8 */2 * * /usr/bin/php -q /var/www/fonty.snails.email/crons/surveying-expiring.php >/dev/null 2>&1
    */19 */2 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/unknown-fonts.php >/dev/null 2>&1
    */19 */2 */8 * * /usr/bin/php -q /var/www/fonty.snails.email/crons/verify-fonts.php >/dev/null 2>&1
    */11 * * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/uploading-fonts.php >/dev/null 2>&1
    */45 */4 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/processing-fonts.php >/dev/null 2>&1
    */21 */3 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/fix-uploads.php >/dev/null 2>&1
    */11 */11 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/poll-peers.php >/dev/null 2>&1
    */1 */9 */2 * * /usr/bin/php -q /var/www/fonty.snails.email/crons/check-cache.php >/dev/null 2>&1
    */47 */7 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/convert-fonts.php >/dev/null 2>&1
    */27 */7 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/spot-checker.php >/dev/null 2>&1
    */22 * * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/zip-fonts.php >/dev/null 2>&1
    */17 */6 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/release-fonts.php >/dev/null 2>&1
    */47 */7 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/release-fonts.php >/dev/null 2>&1
    */27 */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/release-fonts.php >/dev/null 2>&1
    */27 */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/release-fonts.php >/dev/null 2>&1
    */7 */7 * * */2 /usr/bin/php -q /var/www/fonty.snails.email/crons/release-fonts.php >/dev/null 2>&1
    */37 */2 * * */2 /usr/bin/php -q /var/www/fonty.snails.email/crons/release-fonts.php >/dev/null 2>&1
    */12 * * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/uploading-fonts.php >/dev/null 2>&1
    */3 * * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/callbacks.php >/dev/null 2>&1
    * */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/mapping-repository.php >/dev/null 2>&1
    */3 * * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/callbacks.php >/dev/null 2>&1
    * */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/mapping-repository.php >/dev/null 2>&1
    */21 */5 * * * /usr/bin/php -q /var/www/fonty.snails.email/crons/fix-uploads.php >/dev/null 2>&1
    */13 */3 * * * sh /fonts/svn-add.sh >/dev/null 2>&1
    */43 */4 * * */3 sh /fonts/svn-update.sh >/dev/null 2>&1
    11 21 * * */6 sh /fonts/svn-all.sh >/dev/null 2>&1
    */33 */4 * * * sh /fonts/svn-json.sh >/dev/null 2>&1
    */13 */2 * * * sh /fonts/git-add.sh >/dev/null 2>&1
    */43 */3 * * */3 sh /fonts/git-update.sh >/dev/null 2>&1
    11 11 * * */5 sh /fonts/git-all.sh >/dev/null 2>&1
    */33 */4 * * * sh /fonts/git-json.sh >/dev/null 2>&1
    */45 */3 * * * unlink /fonts/Fonting/.git/index.lock >/dev/null 2>&1
    1 0 27 */3 * /usr/bin/php -q /var/www/fonty.snails.email/crons/crawling-robots.php >/dev/null 2>&1
    21 0 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--001.sh >/dev/null 2>&1
    41 0 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--002.sh >/dev/null 2>&1
    1 1 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--003.sh >/dev/null 2>&1
    21 1 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--004.sh >/dev/null 2>&1
    41 1 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--005.sh >/dev/null 2>&1
    1 2 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--006.sh >/dev/null 2>&1
    21 2 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--007.sh >/dev/null 2>&1
    41 2 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--008.sh >/dev/null 2>&1
    1 3 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--009.sh >/dev/null 2>&1
    21 3 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--010.sh >/dev/null 2>&1
    41 3 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--011.sh >/dev/null 2>&1
    1 4 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--012.sh >/dev/null 2>&1
    21 4 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--013.sh >/dev/null 2>&1
    41 4 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--014.sh >/dev/null 2>&1
    1 5 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--015.sh >/dev/null 2>&1
    21 5 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--016.sh >/dev/null 2>&1
    41 5 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--017.sh >/dev/null 2>&1
    1 6 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--018.sh >/dev/null 2>&1
    21 6 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--019.sh >/dev/null 2>&1
    41 6 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--020.sh >/dev/null 2>&1
    1 7 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--021.sh >/dev/null 2>&1
    21 7 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--022.sh >/dev/null 2>&1
    41 7 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--023.sh >/dev/null 2>&1
    1 8 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--024.sh >/dev/null 2>&1
    21 8 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--025.sh >/dev/null 2>&1
    41 8 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--026.sh >/dev/null 2>&1
    1 9 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--027.sh >/dev/null 2>&1
    21 9 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--028.sh >/dev/null 2>&1
    41 9 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--029.sh >/dev/null 2>&1
    1 10 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--030.sh >/dev/null 2>&1
    21 10 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--031.sh >/dev/null 2>&1
    41 10 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--032.sh >/dev/null 2>&1
    1 11 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--033.sh >/dev/null 2>&1
    21 11 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--034.sh >/dev/null 2>&1
    41 11 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--035.sh >/dev/null 2>&1
    1 12 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--036.sh >/dev/null 2>&1
    21 12 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--037.sh >/dev/null 2>&1
    41 12 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--038.sh >/dev/null 2>&1
    1 13 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--039.sh >/dev/null 2>&1
    21 13 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--040.sh >/dev/null 2>&1
    41 13 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--041.sh >/dev/null 2>&1
    1 14 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--042.sh >/dev/null 2>&1
    21 14 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--043.sh >/dev/null 2>&1
    41 14 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--044.sh >/dev/null 2>&1
    1 15 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--045.sh >/dev/null 2>&1
    21 15 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--046.sh >/dev/null 2>&1
    41 15 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--047.sh >/dev/null 2>&1
    1 16 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--048.sh >/dev/null 2>&1
    21 16 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--049.sh >/dev/null 2>&1
    41 16 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--050.sh >/dev/null 2>&1
    1 17 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--051.sh >/dev/null 2>&1
    21 17 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--052.sh >/dev/null 2>&1
    41 17 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--053.sh >/dev/null 2>&1
    1 18 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--054.sh >/dev/null 2>&1
    21 18 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--055.sh >/dev/null 2>&1
    41 18 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--056.sh >/dev/null 2>&1
    1 19 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--057.sh >/dev/null 2>&1
    21 19 28 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--058.sh >/dev/null 2>&1
    41 19 29 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--059.sh >/dev/null 2>&1
    1 20 27 */3 * sh /var/www/fonty.snails.email/crons/crawling-bot--060.sh >/dev/null 2>&1


To enable and access and edit the root cron scheduler on Ubuntu run following command:

   $ sudo crontab -e


## Mod URL Rewrite (Apache URL Rewrite Module)

This goes in your API_ROOT_PATH/.htaccess for the apache module rewrite!

    php_value memory_limit 145M
    php_value upload_max_filesize 39M
    php_value post_max_size 59M
    php_value error_reporting 1
    php_value display_errors 1

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    RewriteRule ^([a-z0-9]{2})/(.*?)/callback.api$ callback.php?version=$1&mode=$2 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(preview|naming)/image.(gif|jpg|png)$ ./index.php?version=$1&mode=$2&clause=$3&state=$5&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/survey/(preview)/(.*?)/image.(jpg|png|gif)$ ./survey-$2.php?version=$1&mode=$4&key=$3&output=$2 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(preview|naming)/(gif|jpg|png).api$ ./index.php?version=$1&mode=$2&clause=$3&state=$5&output=$4	[L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/survey/(preview)/(.*?)/(jpg|png|gif).api$ ./survey-$2.php?version=$1&mode=$4&key=$3&output=$2 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(glyph)/([0-9]+)/image.(gif|jpg|png)$ ./index.php?version=$1&mode=$2&clause=$3&state=$6&output=$4&char=$5 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(glyph)/([0-9]+)/(gif|jpg|png).api$ ./index.php?version=$1&mode=$2&clause=$3&state=$6&output=$4&char=$5	[L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(ufo).api$ ./index.php?version=$1&mode=$2&clause=$3&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(ufo).api/(.*?)$ ./index.php?version=$1&mode=$2&clause=$3&state=$5&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(font)/(.*?)/(.*?).api$ ./index.php?version=$1&mode=$2&clause=$3&state=$5&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/survey/page-([0-9]+)/(.*?)/(.*?).api$ ./survey-page-$2.php?version=$1&mode=$2&key=$3&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/survey/(.*?)/(.*?)/(.*?).api?(.*?)$ ./survey-$2.php?version=$1&mode=$2&key=$3&output=$4&$5 [L,NC,QSA]	
    RewriteRule ^([a-z0-9]{2})/survey/(.*?)/(.*?)/(.*?).api$ ./survey-$2.php?version=$1&mode=$2&key=$3&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/survey/(.*?)/(.*?).api$ ./survey-$2.php?version=$1&key=$2&output=$3 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(.*?)/upload.api$ ./upload.php?version=$1&field=$2 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(.*?)/releases.api$ ./releases.php?version=$1&field=$2 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(uploads|releases)/(.*?)/(.*?).api?(.*?)$ ./index.php?version=$1&mode=$2&clause=$3&state=&output=$4&$5 [L,NC,QSA]	
    RewriteRule ^([a-z0-9]{2})/(uploads|releases)/(.*?)/(.*?).api$ ./index.php?version=$1&mode=$2&clause=$3&state=&output=$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(uploads|releases)/(forms).api$ ./index.php?version=$1&mode=$2&clause=&state=&output=$3 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads)/(.*?)/(.*?)/(.*?)/(.*?).api?(.*?)$ ./index.php?version=$1&mode=$2&clause=$3&state=$4&output=$5&name=$6&$7 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads)/(.*?)/(.*?)/(.*?).api?(.*?)$ ./index.php?version=$1&mode=$2&clause=$3&state=$4&output=$5&$6 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads|fonthit|archive)/(.*?)/(.*?).api?(.*?)$ ./index.php?version=$1&mode=$2&clause=$3&state=&output=$4&$5 [L,NC,QSA]	
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads|identities)/(.*?).api?(.*?)$ ./index.php?version=$1&mode=$2&clause=&state=&output=$3&$4 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads)/(.*?)/(.*?)/(.*?)/(.*?).api$ ./index.php?version=$1&mode=$2&clause=$3&state=$4&output=$5&name=$6 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads)/(.*?)/(.*?)/(.*?).api$ ./index.php?version=$1&mode=$2&clause=$3&state=$4&output=$5 [L,NC,QSA]
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads|fonthit|archive)/(.*?)/(.*?).api$ ./index.php?version=$1&mode=$2&clause=$3&state=&output=$4 [L,NC,QSA]	
    RewriteRule ^([a-z0-9]{2})/(fonts|nodes|random|data|callbacks|downloads|identities)/(.*?).api$ ./index.php?version=$1&mode=$2&clause=&state=&output=$3 [L,NC,QSA]
    RewriteRule ^font-download-example.zip$ ./include/examples/sample-font-api2-download.zip [L,NC,QSA]
    RewriteRule ^callback-example.php$ ./include/examples/callback-example.php.txt [L,NC,QSA]
    RewriteRule ^(.*?).(css|txt|php|jpg|png|gif|ico)$ ./$1.$2 [L,NC,QSA]

To enable apaches module for URL rewrite run the following commands:

   $ sudo a2enmod rewrite
   $ sudo service apache2 restart


That's pretty much the basic of setting up, you can of course make changes to the paths, even store your SVN remotely for larger file support. I hope this installation guide will help you in configuring the Font's API
