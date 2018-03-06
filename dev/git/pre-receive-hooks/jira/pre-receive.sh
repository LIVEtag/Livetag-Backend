#!/bin/bash
#
# check commit messages for JIRA issue numbers formatted as [JIRA-<issue number>: <description>]

REGEX="([A-Z]+?\-\d+: [^ ][a-zA-Z0-9_\- ]+([\n\s]+.*)+|(m|M)erge)"

ERROR_MSG="[POLICY] The commit doesn't reference a Jira issue"

while read OLDREV NEWREV REFNAME ; do
  for COMMIT in `git rev-list $OLDREV..$NEWREV`;
  do
    MESSAGE=`git cat-file commit $COMMIT | sed '1,/^$/d'`
    if ! echo $MESSAGE | grep -iqE "$REGEX"; then
      echo "$ERROR_MSG: $MESSAGE" >&2
      exit 1
    fi
  done
done
exit 0
