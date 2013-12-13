#!/bin/bash

cd /deskpro/www

cp /vm_local/init/conf/deskpro_config.php config.php
cp /vm_local/init/conf/deskpro_config.testing.php config.testing.php

php cmd.php dp:install --verbose

chown --silent -R vagrant:vagrant /deskpro/www
chown --silent -R vagrant:vagrant /deskpro-cache
chmod --silent -R 0777 /deskpro/www/data
chmod --silent -R 0777 /deskpro/www/app/sys/cache
chmod --silent -R 0777 /deskpro-cache

# Correct filemode on index.html files or else git will complain
find /deskpro/www/data/ -name 'index.html' -exec chmod 0644 {} +
chmod 0644 /deskpro/www/data/.htaccess