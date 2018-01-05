#!/bin/bash

service="docker_code_challenge_server";
user="david"

enabled=$( docker ps --format "{{.Names}}" | grep -i "$service" )
if [ "$enabled" == "" ]
then
    echo -e "\033[31mContainer \033[33m$service\033[31m not started!\033[0m\n";
    exit 1;
fi;

dir=$(dirname $0)
cd $dir

args=""
while [[ $# -ge 1 ]]; do
    args="$args $1"
    shift
done

if [ "$args" == "" ]; then
    docker exec -it -u $user $service bash
else
    docker exec -it -u $user $service $args
fi
