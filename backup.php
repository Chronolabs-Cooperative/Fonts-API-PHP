<?php
chdir("/var/backup-git");
mkdir("/var/backup-git/labs.coop/" . date("Y") . "/" . date("m") . "/" . date("d") . "/", 0777, true);
$cmd[] = "/usr/bin/mysqldump --host=localhost --user=root --password=##\$\$%%n0buxa=- --all-database > ./labs.coop//" . date("Y") . "/" . date("m") . "/" . date("d") . "/all-database.sql";
$cmd[] = "/usr/bin/zip -R ./labs.coop/" . date("Y") . "/" . date("m") . "/" . date("d") . "/all-files.zip /var/www/*";
$cmd[] = "/usr/bin/git add ./* --force";
$cmd[] = "/usr/bin/git commit ./ -m 'Commit Backup - " . date("D/m/y H:i:s") . "'";		
$cmd[] = "/usr/bin/git push origin master";

foreach($cmd as $command){
	$output = '';
	exec($commnd, $return, $output);
	echo $output;
}
?>