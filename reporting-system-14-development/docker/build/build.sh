#!/bin/sh

REPO_URL='gcr.io/amjc-k8s/'
TAG='apache-stretch9.6'
IMAGE_NAME='php56'

usage(){
    EXIT_VAL=$1
    echo "Usage: $0 {-t TAG} [-r REPO_URL] [-i Image Name]"
    if [ "x$EXIT_VAL" != "x" ]; then
        exit $EXIT_VAL
    fi
}

while getopts ":t:r:i:" opt; do
  case ${opt} in
    t ) # process TAG <requried>
        TAG="$OPTARG"
      ;;
    r ) # process Repository
        REPO_URL="$OPTARG"
      ;;
    i ) # process iamge name
        IMAGE_NAME="$OPTARG"
      ;;
    \? ) 
      echo Invalid args
      usage 2
      ;;
  esac
done

if [ "x${TAG}" == "x" ]; then
  usage 1
fi

IMAGE=${REPO_URL}${IMAGE_NAME}

docker build -t $IMAGE:$TAG .
docker tag $IMAGE:$TAG $IMAGE:latest
