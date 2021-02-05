echo "> Build and upload the project image"

if [ -z "${AWS_ACCESS_KEY_ID}" ] || [ -z "${AWS_SECRET_ACCESS_KEY}" ] || [ -z "${AWS_DEFAULT_REGION}" ]
  then
    echo "The deploy AWS credentials is not set."
    exit 1
  fi

echo ">> Create .env file"
cat "${ENV_PROD}" > ./.env
test -s ./.env || exit 2

echo ">> Create version.txt"
echo "${CI_COMMIT_SHORT_SHA} ${CI_COMMIT_TAG}" > ./version.txt

echo ">> Build the image"
docker build -t ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:${CI_COMMIT_SHORT_SHA} -f ./.gitlab-ci/Dockerfile . || exit 3

echo ">>>Push the image to ECR"
$(aws ecr get-login --no-include-email) >/dev/null || exit 4

docker tag  ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:${CI_COMMIT_SHORT_SHA} \
            ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:latest
docker push ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:${CI_COMMIT_SHORT_SHA} || exit 5
docker push ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:latest || exit 5

echo ">> Clear"
docker rmi  ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:${CI_COMMIT_SHORT_SHA} \
            ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/backend:latest >/dev/null 2>&1

echo "> Done"
