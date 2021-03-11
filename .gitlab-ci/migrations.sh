echo "> Do migrations"

if [ -z "${AWS_ACCESS_KEY_ID}" ] || [ -z "${AWS_SECRET_ACCESS_KEY}" ] || [ -z "${AWS_DEFAULT_REGION}" ]
  then
    echo "The deploy AWS credentials is not set."
    exit 1
  fi

echo "> Get a service paramaters"
SERVICE=`aws ecs describe-services --cluster=livetag --service=backend`
SUBNET=`echo "${SERVICE}" | jq -r .services[0].networkConfiguration.awsvpcConfiguration.subnets[0]`
SGROUP=`echo "${SERVICE}" | jq -r .services[0].networkConfiguration.awsvpcConfiguration.securityGroups[0]`
if [ -z "${SUBNET}" ] || [ -z "${SGROUP}" ]
  then
    echo "Cant get service parameters: SUBNET=${SUBNET} SGROUP=${SGROUP}"
    exit 2
  fi
echo "> Service parameters: SUBNET=${SUBNET} SGROUP=${SGROUP}"

echo "> Updare a task"
ecs update migrations \
    --tag ${CI_COMMIT_SHORT_SHA} \
    --diff \
    --deregister

echo ">> Run a task"
TASK_ID=`ecs run livetag migrations 1 \
    --subnet ${SUBNET} \
    --securitygroup ${SGROUP} \
    --launchtype FARGATE \
    --public-ip | grep "arn:aws:ecs:" | cut -f 3 -d "/"`
echo ">> Te task ID: ${TASK_ID}"

echo ">>> Wait for the task finish..."
aws ecs wait tasks-stopped \
    --cluster livetag \
    --tasks ${TASK_ID}

echo ">> The task result log:"
echo "----------------------------------------------------------------"
aws logs get-log-events \
    --log-group-name "/ecs/migrations" \
    --log-stream-name "ecs/migrations/${TASK_ID}" | jq -r .events[].message
echo "----------------------------------------------------------------"

echo "> Done"
