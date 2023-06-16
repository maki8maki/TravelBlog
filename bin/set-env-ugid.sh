#!/bin/sh
echo "LINUX_MYSQL_UID=$(id -u $USER)" >> .devcontainer/.env
echo "LINUX_MYSQL_GID=$(id -g $USER)" >> .devcontainer/.env