# Filmmakers for Future signup

This is a prototype for a signup form.

## Setup the database

```
CREATE DATABASE fff CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE data (
  uid                   VARCHAR(40)  NOT NULL PRIMARY KEY,
  name                  VARCHAR(256) NOT NULL,
  mail                  VARCHAR(256) NOT NULL, 
  job                   VARCHAR(256),
  website               VARCHAR(256),
  country               VARCHAR(256),
  city                  VARCHAR(256),
  newsletter            BOOLEAN      NOT NULL,
  disabled              BOOLEAN      NOT NULL,
  admin_verify_token    VARCHAR(40),
  user_newsletter_token VARCHAR(40),
  user_verify_token     VARCHAR(40),
  verified              BOOLEAN      AS (disabled IS FALSE AND admin_verify_token IS NULL AND user_verify_token IS NULL),
  subscribed            BOOLEAN      AS (disabled IS FALSE AND admin_verify_token IS NULL AND user_verify_token IS NULL AND newsletter IS TRUE)
);

GRANT ALL ON fff.* TO 'fff'@'%' IDENTIFIED BY 'fff';
GRANT ALL ON fff.* TO 'fff'@'localhost' IDENTIFIED BY 'fff';
GRANT ALL ON fff.* TO 'fff'@'127.0.0.1' IDENTIFIED BY 'fff';

FLUSH PRIVILEGES;
```

