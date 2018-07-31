unlink /fonts/Fonting/.git/index.lock
cd /fonts/Fonting
git add --verbose --force */*/*.json
git add --verbose --force */*.json
git add --verbose --force *.json
git commit -m "Updating JSON Fonts Index Files!"
git push origin master
