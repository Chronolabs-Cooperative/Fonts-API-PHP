# Chronolabs Cooperative
# Fonting Repository Services API  - http://fonts.labs.coop -

# INSTALLATION MANUAL
## by. Simon Antony Roberts (Sydney)
## simon@staff.labs.coop

# Foreword

In this manual we will take you through all the conditions which you will encounter in general Ubuntu or Debian environment setting up this API. It will include cronjobs as well as any basis of general configuration you will encounter with your API in configuring and definition operations parameters in most types of places you find Ubuntu or Debian.

Download the API archive of PHP files from: https://sourceforge.net/p/chronolabsapis/files/fonts.labs.coop

# Setting up the environment
You will first have to set up the environment running the following script at root on Ubuntu or Debian Server it will install all the system environment variables you require to do an installation:-

    
    $ sudo apt-get install p7zip-rar p7zip-full unace unrar zip unzip sharutils rar sharutils unrar uudeview mpack arj cabextract file-roller lmza fontforge tasksel ntpdate nano lzma bzip2
    

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

# Configuring MySQL
You will need to use with either MySQL Workbench or PHPMyAdmin create a MySQL Database for the fonting repository services API. You will find in the path of /sql the sql dump files for the database for the API.

You will need to restore these with either import with MySQL Workbench or within the database on PHPMyAdmin uploading each SQL to create the tables required.
You will also have to create a username which all these details are stored on /var/www/fonts-api/class/fontages.php which contains the configuration for MySQL, Database + Username and Password for the API.

## Configuring Scheduled Tasks (CronJobs)

Once you have configured the above you will have to set up the cronjobs for all the scheduled tasks on the fonts-api these are all found on /var/www/fonts-api/crons where they are all required, some are for recovery if you have to drop your database and start from scratch will rebuild from your current processing.
You need to run the following command from root with sudo at the start unless you are doing user basis of cronjobs to set them below the command is the listing with 'suggested' not 'finited' schedules for tasks to operate; remember adjusting these could leave your system paralised with to much to do in one hit so execute the following and put these lines in: $ sudo crontab -e

    11 23 5 * * sh /var/www/fonts-api/crons/crawling-fonts.sh
    11 11 */3 * * /usr/bin/php -q /var/www/fonts-api/crons/lost-fonts-uploads.php
    */11 */11 * * * /usr/bin/php -q /var/www/fonts-api/crons/poll-peers.php
    */13 * * * * /usr/bin/php -q /var/www/fonts-api/crons/check-cache.php
    */18 * * * * /usr/bin/php -q /var/www/fonts-api/crons/convert-fonts.php
    */11 */11 * * * /usr/bin/php -q /var/www/fonts-api/crons/clean-tmp.php
    */56 */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/surveying-fonts.php
    */26 */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/surveying-reminder.php
    */16 */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/surveying-expiring.php
    */11 * * * * /usr/bin/php -q /var/www/fonts-api/crons/zip-fonts.php
    * */2 */9 * * /usr/bin/php -q /var/www/fonts-api/crons/unknown-fonts.php
    */18 * * * * /usr/bin/php -q /var/www/fonts-api/crons/release-fonts.php
    */19 */2 */8 * * /usr/bin/php -q /var/www/fonts-api/crons/verify-fonts.php
    */3 * * * * /usr/bin/php -q /var/www/fonts-api/crons/uploading-fonts.php
    */45 * * * * /usr/bin/php -q /var/www/fonts-api/crons/processing-fonts.php
    */3 * * * * /usr/bin/php -q /var/www/fonts-api/crons/callbacks.php
    */11 * * * * /usr/bin/php -q /var/www/fonts-api/crons/mapping-repository.php
    */21 * * * * /usr/bin/php -q /var/www/fonts-api/crons/fix-uploads.php
    */11 */11 * * * /usr/bin/php -q /var/www/fonts-api/crons/poll-peers.php
    */1 */3 * * * /usr/bin/php -q /var/www/fonts-api/crons/check-cache.php
    */7 * * * * /usr/bin/php -q /var/www/fonts-api/crons/convert-fonts.php
    */56 */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/surveying-fonts.php
    */26 */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/surveying-reminder.php
    */16 */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/surveying-expiring.php
    */9 * * * * /usr/bin/php -q /var/www/fonts-api/crons/zip-fonts.php
    */18 */13 * * * /usr/bin/php -q /var/www/fonts-api/crons/verify-fonts.php
    */18 * * * * /usr/bin/php -q /var/www/fonts-api/crons/release-fonts.php
    */2 * * * * /usr/bin/php -q /var/www/fonts-api/crons/uploading-fonts.php
    */45 * * * * /usr/bin/php -q /var/www/fonts-api/crons/processing-fonts.php
    */3 * * * * /usr/bin/php -q /var/www/fonts-api/crons/callbacks.php
    * */2 * * * /usr/bin/php -q /var/www/fonts-api/crons/mapping-repository.php
    */21 * * * * /usr/bin/php -q /var/www/fonts-api/crons/fix-uploads.php
    */33 * * * * sh /fonts/svn-add.sh
    */10 */6 * * */2 sh /fonts/svn-all.sh
    */5 * * * * /usr/bin/ntpdate labs.coop

That's pretty much the basic of setting up, you can of course make changes to the paths, even store your SVN remotely for larger file support. I hope this installation guide will help you in configuring the Font's API
