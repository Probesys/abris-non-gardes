#!/bin/sh
set -e

DIR=/var/www/html/vendor
if [ -d "$DIR" ]; then
    php bin/console doctrine:migration:migrate --no-interaction
fi

exec $@
