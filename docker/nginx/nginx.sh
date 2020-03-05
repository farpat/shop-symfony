#!/usr/bin/env bash

echo "################################## Run nginx"
export DOLLAR='$'
mkdir -p /etc/nginx/sites-enabled
envsubst < /var/sites-enabled/app.conf.template > /etc/nginx/sites-enabled/app.conf
nginx -g "daemon off;"
