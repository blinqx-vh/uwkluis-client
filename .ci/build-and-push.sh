#!/usr/bin/env bash

ECR_URL="751567812571.dkr.ecr.eu-central-1.amazonaws.com"

if [[ -z "$BUILD_PHP_VERSION" ]]
then
  BUILD_PHP_VERSION="8.2"
fi

echo "Docker login"
aws ecr get-login-password --region eu-central-1 | docker login --username AWS --password-stdin "${ECR_URL}"

#change to the root of docker directory
cd "$( dirname "${BASH_SOURCE[0]}" )" || echo "folder does not exist"

VERSION=$(date '+%Y%m%d%H%M%S')

echo "tagging as php-${BUILD_PHP_VERSION}-alpine-${VERSION}"

## PROD IMAGES
docker image build --platform=linux/amd64 -t hypotheekbond/uwkluis-client-development:php-"${BUILD_PHP_VERSION}"-alpine --build-arg PHP_VERSION="${BUILD_PHP_VERSION}" ../  -f ./Dockerfile

docker tag hypotheekbond/uwkluis-client-development:php-"${BUILD_PHP_VERSION}"-alpine "${ECR_URL}"/hypotheekbond/uwkluis-client-development:php-"${BUILD_PHP_VERSION}"-alpine-"${VERSION}"

docker push "${ECR_URL}"/hypotheekbond/uwkluis-client-development:php-"${BUILD_PHP_VERSION}"-alpine-"${VERSION}"

echo "pushed as php-${BUILD_PHP_VERSION}-alpine-${VERSION}"
