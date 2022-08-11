#!/bin/bash

echo "Updating mysql configs in /etc/mysql/my.cnf."

sudo echo "[mysqld]" >> /etc/mysql/my.cnf
sudo echo "bind-address = 0.0.0.0" >> /etc/mysql/my.cnf

echo "Updated mysql bind address in /etc/mysql/my.cnf to 0.0.0.0 to allow external connections."

sudo /etc/init.d/mysql stop
sudo /etc/init.d/mysql start

