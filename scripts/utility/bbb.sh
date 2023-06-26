#! /bin/bash
#Example usage: bbb.sh dev uli
env=$1
command=$2

echo "Executing 'drush @sitename.01$env $command -y'"
echo "on BPC $env"
drush @bpc.01$env $command -y

echo "on BBB $env"
drush @bbb.01$env  $command -y

echo "on SUL $env"
drush @sul.01$env  $command -y

echo "on BBI $env"
drush @bbi.01$env  $command -y

if [ $env == "test" ]; then
  echo "on Preprod BPC $env"
  drush @preprodbpc.01$env $command -y

  echo "on Preprod BBB $env"
  drush @preprodbbb.01$env $command -y

  echo "on Preprod SUL $env"
  drush @preprodsul.01$env $command -y

  echo "on Preprod BBI $env"
  drush @preprodbbi.01$env $command -y
fi
