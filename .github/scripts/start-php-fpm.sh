#!/bin/bash

set -e
set -x

sudo apt-get update && sudo apt-get install nginx
sudo apt-get install php7.2-fpm -y
sudo ./.circleci/scripts/start-nginx.sh
sudo service php7.2-fpm restart
# Set Permission
chmod 777 -R ./conf
chmod 777 -R ./uploads
chmod 777 -R ./cache
