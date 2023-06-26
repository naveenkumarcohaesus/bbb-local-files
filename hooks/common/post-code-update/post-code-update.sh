#!/bin/bash
#
# Cloud Hook: post-code-update
#
# The post-code-update hook runs in response to code commits. When you
# push commits to a Git branch, the post-code-update hooks runs for
# each environment that is currently running that branch.
#
# The arguments for post-code-update are the same as for post-code-deploy,
# with the source-branch and deployed-tag arguments both set to the name of
# the environment receiving the new code.
#
# post-code-update only runs if your site is using a Git repository. It does
# not support SVN.

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

#blt artifact:ac-hooks:post-code-update $site $target_env $source_branch $deployed_tag $repo_url $repo_type --environment=$target_env -v --no-interaction -D drush.ansi=false

# This will trigger `db-update.sh` on ACSF correctly for every site.
if [ $target_env == '01dev' ] || [ $target_env == '01test' ]  || [ $target_env == '01live' ]
then
  FILE="$HOME/"$target_env"/nobackup/bbb_deployment.sh"
else
  echo "Notice: ACSF deployment to $target_env: environment not supported by this deployment script."
  exit
fi

if [ -f $FILE ]; then
  echo "######: ACSF deployment to $target_env."

  # Load the ACSF API settings (which are not stored in this repo).
  echo "######: reading configuration from $FILE."

  . $FILE

  ACSF_API_URL="https://www.${target_env:2}-${site}.acsitefactory.com/api/v1/"

  # post-code-update hook does not give us the deployed branch name.
  # We need to call ACSF API to retrieve it.
  echo "######: Calling API to get currently deployed branch name."

  response=$(curl -s $ACSF_API_URL/vcs?type=sites \
       -u $ACSF_API_USER:$ACSF_API_KEY \
  )

  echo "######: Response from ACSF API:"
  echo $response

  # Parse current branch name from JSON reponse.
  current_branch=$( php -r  "try {\$o = json_decode('$response'); print isset(\$o->current) ? \$o->current : ''; } catch (Exception \$e) {}" )

  if [ "$current_branch" != "" ]; then
    echo "######: Currently deployed: $current_branch."
    echo "######: Calling API to start code update."

    response=$( curl -s -X POST $ACSF_API_URL/update \
         -u $ACSF_API_USER:$ACSF_API_KEY \
         -H 'Content-Type: application/json' \
         -d '{"scope": "sites", "sites_ref": "'$current_branch'", "sites_type": "code, db"}'
    )

    echo "######: Response from ACSF API:"
    echo $response
    echo ""
  else
    echo "Notice: Could not get currently deployed branch name. Deployment aborted."
  fi
else
  echo "Notice: ACSF deployment to $target_env: file $FILE does not exist."
fi

set +v
