# Filmmakers4Future Signup

This is a prototype for a signup and newsletter subscription form.

## Install the software

```
sudo apt-get install nginx php-fpm php-curl php-mysqli mariadb-server

sudo git clone https://github.com/yahesh/fff-signup /var/www/html
```

## Configure the webserver

### Open the configuration file

```
sudo vi /etc/nginx/sites-enabled/default 
```

### Insert the configuration:

```
server {
	listen 80 default_server;
	listen [::]:80 default_server;

	root /var/www/html;

	index index.php index.html index.htm;

	server_name _;

	# prevent access to certain locations
	location ~ ^\/\.git(\/.*)?$  { return 404; }
	location ~ ^\/\.gitignore$   { return 404; }
	location ~ ^\/\.htaccess$    { return 404; }
	location ~ ^\/CHANGELOG\.md$ { return 404; }
	location ~ ^\/config(\/.*)?$ { return 404; }
	location ~ ^\/errors(\/.*)?$ { return 404; }
	location ~ ^\/lib(\/.*)?$    { return 404; }
	location ~ ^\/README\.md$    { return 404; }
        location ~ ^\/router\.php$   { return 404; }

	# pretty URLs
	rewrite ^\/contact$    /contact.php    last;
	rewrite ^\/newsletter$ /newsletter.php last;
	rewrite ^\/register$   /register.php   last;
	rewrite ^\/send$       /send.php       last;
	rewrite ^\/subscribed$ /subscribed.php last;
	rewrite ^\/verified$   /verified.php   last;
	rewrite ^\/verify$     /verify.php     last;

	location / {
		try_files $uri $uri/ =404;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
	}
}
```

### Restart the webserver

```
sudo systemctl restart nginx.service
```

## Setup the database

### Log into the MariaDB database

```
sudo mysql
```

### Execute the SQL statements

```
CREATE DATABASE fff CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fff;

CREATE TABLE data (
	uid                   VARCHAR(40)  NOT NULL PRIMARY KEY,
	name                  VARCHAR(256) NOT NULL,
	mail                  VARCHAR(256) NOT NULL,
	job                   VARCHAR(256) NOT NULL,
	country               VARCHAR(256) NOT NULL,
	city                  VARCHAR(256),
	website               VARCHAR(256),
	iscompany             BOOLEAN      NOT NULL DEFAULT FALSE,
	newsletter            BOOLEAN      NOT NULL DEFAULT FALSE,
	disabled              BOOLEAN      NOT NULL DEFAULT FALSE,
	admin_verify_token    VARCHAR(40),
	user_newsletter_token VARCHAR(40),
	user_verify_token     VARCHAR(40),
	mailhash              VARCHAR(64)  AS (SHA2(mail, 256)) PERSISTENT UNIQUE KEY,
	subscribed            BOOLEAN      AS (disabled IS FALSE AND admin_verify_token IS NULL AND user_verify_token IS NULL AND newsletter IS TRUE),
	verified              BOOLEAN      AS (disabled IS FALSE AND admin_verify_token IS NULL AND user_verify_token IS NULL)
);

GRANT ALL ON fff.* TO 'fff'@'%' IDENTIFIED BY 'fff';
GRANT ALL ON fff.* TO 'fff'@'localhost' IDENTIFIED BY 'fff';
GRANT ALL ON fff.* TO 'fff'@'127.0.0.1' IDENTIFIED BY 'fff';

FLUSH PRIVILEGES;

EXIT;

```

## Configure the software

### Generate the Newsletter Send Password

```
php -r 'print(str_replace("\$", "\\\$", password_hash(readline("Password: "), PASSWORD_DEFAULT)."\n"));'
```

### Set the configuration values

```
sudo cp /var/www/html/config/config.php.example /var/www/html/config/config.php
sudo vi /var/www/html/config/config.php
```

### Modify the e-mail templates

```
sudo vi /var/www/html/conf/templates/admin_verify.txt
sudo vi /var/www/html/conf/templates/contact.txt
sudo vi /var/www/html/conf/templates/user_newsletter.txt
sudo vi /var/www/html/conf/templates/user_verified.txt
sudo vi /var/www/html/conf/templates/user_verify.txt
```

