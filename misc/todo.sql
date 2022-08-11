CREATE DATABASE todo;
CREATE USER 'todo-app'@'localhost' IDENTIFIED WITH mysql_native_password BY 'todo-app-pass';
GRANT ALL PRIVILEGES ON *.* TO 'todo-app'@'localhost' WITH GRANT OPTION;
CREATE USER 'todo-app'@'%' IDENTIFIED WITH mysql_native_password BY 'todo-app-pass';
GRANT ALL PRIVILEGES ON *.* TO 'todo-app'@'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;

CONNECT todo;

CREATE TABLE `todo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

INSERT INTO todo VALUES(NULL, 'Sample TODO entry #1', FALSE);
INSERT INTO todo VALUES(NULL, 'Sample TODO entry #2', TRUE);
INSERT INTO todo VALUES(NULL, 'Sample TODO entry #3', FALSE);
INSERT INTO todo VALUES(NULL, 'Too many things todo', FALSE);