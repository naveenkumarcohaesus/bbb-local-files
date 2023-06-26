#!/bin/bash
#
# Cloud Hook: post-code-deploy
#
# The post-code-deploy hook is run whenever you use the Workflow page to
# deploy new code to an environment, either via drag-drop or by selecting
# an existing branch or tag from the Code drop-down list. See
# ../README.md for details.
#
# Usage: post-code-deploy site target-env source-branch deployed-tag repo-url
#                         repo-type

set -ev

site="$1"
target_env="$2"
source_branch="$3"
deployed_tag="$4"
repo_url="$5"
repo_type="$6"

# Prep for BLT commands.
repo_root="/var/www/html/$site.$target_env"
export PATH=$repo_root/vendor/bin:$PATH
cd $repo_root

blt artifact:ac-hooks:post-code-deploy $site $target_env $source_branch $deployed_tag $repo_url $repo_type --environment=$target_env -v --no-interaction -D drush.ansi=false

# This will trigger `db-update.sh` on ACSF correctly for every site.
if [ $target_env == '01dev' ] || [ $target_env == '01test'] || [ $target_env == '01live' ]
then
  FILE="$HOME/"$target_env"/nobackup/bbb_deployment.sh"
else
  echo "Notice: ACSF deployment to $target_env: environment not supported by this deployment script."
  exit
fi

if [ -f $FILE ]; then
  # Load the Slack webhook URL (which is not stored in this repo).
  . $FILE

  # Post deployment notice to Slack
  if [ "$source_branch" != "$deployed_tag" ]; then
    curl -X POST --data-urlencode "payload={\"username\": \"Acquia Cloud\", \"text\": \"An updated deployment has been made to *$site.$target_env* using branch *$source_branch* as *$deployed_tag*.\", \"icon_emoji\": \":acquiacloud:\"}" $SLACK_WEBHOOK_URL
  else
    curl -X POST --data-urlencode "payload={\"username\": \"Acquia Cloud\", \"text\": \"An updated deployment has been made to *$site.$target_env* using  *$deployed_tag*.\", \"icon_emoji\": \":acquiacloud:\"}" $SLACK_WEBHOOK_URL
  fi
else
  echo "File $FILE does not exist."
fi

set +v
