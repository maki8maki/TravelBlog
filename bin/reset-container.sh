#!/bin/sh

cd .devcontainer
docker-compose down
cd ../

rm -rf ./docker/mysql/data
mkdir ./docker/mysql/data
touch ./docker/mysql/data/.gitkeep

cd .devcontainer
docker-compose up -d
cd ../