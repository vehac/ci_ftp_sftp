#!/bin/bash

echo "------------------ Permissions folder ---------------------"
bash -c 'chmod -R 777 /var/www/html/application/cache'

bash -c 'chmod -R 777 /var/www/html/upload/temp'

bash -c 'chmod -R 777 /var/www/html/upload/ci_ftp'

bash -c 'chmod -R 777 /var/www/html/upload/ci_ftp_passwd'

bash -c 'chmod -R 777 /var/www/html/upload/ci_sftp'

echo "------------------ Starting apache server ------------------"
exec "apache2-foreground"