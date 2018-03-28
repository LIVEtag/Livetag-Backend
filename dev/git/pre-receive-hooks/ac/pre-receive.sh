#!/bin/bash
#
# check commit messages for AC issue numbers formatted as [task#<issue number>[-<issue number>]: <description>]
# check user email formatted as [<surname>-<ii>@gbksoft.com]
# check user author name formatted as [<name>[<surname>]|<surname>-<ii>]

REGEX_TASK='(task#[0-9]+(\-[0-9]+)?: [^ ].*|merge)'
REGEX_EMAIL='@gbksoft\.com$'
REGEX_NAME='[a-z]+?([a-z]+?)?$'

ERROR_MSG_TASK="[POLICY:ISSUE] The commit doesn't reference a AC issue"
ERROR_MSG_EMAIL="[POLICY:EMAIL] The commit doesn't contains valid email"
ERROR_MSG_NAME="[POLICY:NAME] The commit doesn't contains valid author name"

HAS_ERROR=false

while read OLDREV NEWREV REFNAME ; do
  if [ 0 -ne $(expr "$OLDREV" : "0*$") ]; then
    exit 0
  fi
  for COMMIT in `git rev-list $OLDREV..$NEWREV`; do
    COMMIT="${COMMIT:0:9}"
    NAME=`git log -1 --pretty=format:%an $COMMIT`
    if ! echo $NAME | grep -iqE "$REGEX_NAME"; then
      echo "$COMMIT | $ERROR_MSG_NAME:" >&2
      echo "$NAME:" >&2
      echo "" >&2
      HAS_ERROR=true
    fi
    EMAIL=`git log -1 --pretty=format:%ae $COMMIT`
    if ! echo $EMAIL | grep -iqE "$REGEX_EMAIL"; then
      echo "$COMMIT | $ERROR_MSG_EMAIL:" >&2
      echo "$EMAIL:" >&2
      echo "" >&2
      HAS_ERROR=true
    fi
    MESSAGE=`git cat-file commit $COMMIT | sed '1,/^$/d'`
    if ! echo $MESSAGE | grep -iqE "$REGEX_TASK"; then
      echo "$COMMIT | $ERROR_MSG_TASK:" >&2
      echo "$MESSAGE:" >&2
      echo "" >&2
      HAS_ERROR=true
    fi
  done
done

if $HAS_ERROR ; then
  echo "ERRORS!!!" >&2
  exit 1
fi
exit 0
