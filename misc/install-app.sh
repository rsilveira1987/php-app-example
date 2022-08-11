#!/bin/bash

sed -i "s/{{DB_HOST}}/192.168.5.167/" /home/ubuntu/workspace/github/php-app-example/src/index.php
sed -i "s/{{DB_USER}}/todo-app/" /home/ubuntu/workspace/github/php-app-example/src/index.php
sed -i "s/{{DB_PASS}}/todo-app-pass/" /home/ubuntu/workspace/github/php-app-example/src/index.php
sed -i "s/{{DB_NAME}}/todo/" /home/ubuntu/workspace/github/php-app-example/src/index.php