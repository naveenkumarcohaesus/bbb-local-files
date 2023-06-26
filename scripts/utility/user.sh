#! /bin/bash
#Example usage: user.sh dev ~/users.csv

env=$1
uri=($env.british-business-bank.co.uk $env.startuploans.co.uk $env.britishpatientcapital.co.uk $env.bbinv.co.uk)
csv_path=$2
sitegroup=bbbwebsites
docroot=/var/www/html/$sitegroup.01$env/docroot
drush_alias=@$sitegroup.01$env


  #read the user.csv
  while IFS="," read -r mail role1 role2 role3 role4
  do
    #create users on all the domains
    for domain in "${uri[@]}"
    do
      uinf=$(drush $drush_alias --root=$docroot -l $domain uinf --mail=$mail --format=csv)
      echo "$domain"
      #Check if user is not existing
      if [[ $uinf != *"$mail"* ]]; then
        cache_dir=`/usr/bin/env php /mnt/www/html/$sitegroup.01$env/vendor/acquia/blt/scripts/blt/drush/cache.php $sitegroup 01$env $domain`
        echo "Generated temporary Drush cache directory: $cache_dir."
        echo "Creating User for $mail"
        echo "User Email Id: $mail"
        echo "Role1: $role1"
        echo "Role2: $role2"
        echo "Role3: $role3"
        echo "Role4: $role4"
        DRUSH_PATHS_CACHE_DIRECTORY="$cache_dir" drush $drush_alias --root=$docroot -l $domain ucrt $mail --mail=$mail
        if [ -n "$role1" ]; then
          DRUSH_PATHS_CACHE_DIRECTORY="$cache_dir" drush $drush_alias --root=$docroot -l $domain urol "$role1" $mail
        fi
        if [ -n "$role2" ]; then
           DRUSH_PATHS_CACHE_DIRECTORY="$cache_dir" drush $drush_alias --root=$docroot -l $domain urol "$role2" $mail
        fi
        if [ -n "$role3" ]; then
           DRUSH_PATHS_CACHE_DIRECTORY="$cache_dir" drush $drush_alias --root=$docroot -l $domain urol "$role3" $mail
        fi
        if [ -n "$role4" ]; then
           DRUSH_PATHS_CACHE_DIRECTORY="$cache_dir" drush $drush_alias --root=$docroot -l $domain urol "$role4" $mail
        fi
       else
         echo "User is already existing"
         echo "$uinf"
       fi
    done
done < <(tail -n +2 $csv_path)
