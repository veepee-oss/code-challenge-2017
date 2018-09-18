#!/bin/bash

dockerfile="docker-compose.yml"
dir=$(dirname $0)
cd $dir

echo -e "Stopping $dockerfile..."

docker-compose -f $dockerfile down
