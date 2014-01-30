#!/bin/sh
if [ $1 ]
then 
find ./*.php -type f -exec sed -i "s/Planning Biblio, Plugin Congés Version .*/Planning Biblio, Plugin Congés Version $1/g" {} \;
find ./*/*.js -type f -exec sed -i "s/Planning Biblio, Plugin Congés Version .*/Planning Biblio, Plugin Congés Version $1/g" {} \;
find ./*.php -type f -exec sed -i "s/version=\".*\";/version=\"$1\";/g" {} \;
git add -A
git ci -m "Modification du numéro de version ($1)"
git tag -d $1
git tag $1

exit 0;
fi

echo "Argumennt manquant : numéro de version";
exit 1;
