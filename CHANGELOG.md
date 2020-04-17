# Changelog

## 0.8 (18.04.2020)
* disable modification of the newsletter subscription during the admin verification step
* support default mail subject and body for newsletter submission
* introduced the configuration values `NEWSLETTER_MAIL_BODY` and `NEWSLETTER_MAIL_SUBJECT`

## 0.7 (17.04.2020)
* introduced `send.php`
* introduced `send_newsletter()` to send newsletters to verified subscribers
* introduced the configuration value `NEWSLETTER_SEND_PASSWORD`
* `send_mail()` now supports batch submission of mails

## 0.6 (06.11.2019)
* introduced `router.php` for local debugging
* `send_mail()` now supports to set the `Reply-To` header
* mails sent via the contact form and verification mails to the admin now have a set `Reply-To` header
* introduced mandatory `BASE_URL` configuration value as a basis for the CSRF detection
* before a form submission is handled the HTTP `Referer` header is checked against the `BASE_URL` value
* introduced JavaScript snippet to prevent form resubmits on browser refreshes
* fixed `verified.php` table columns

## 0.5 (10.10.2019)
* updated feature set of prototype to feature set of Filmmakers4Future
* introduced `contact.php`
* moved files into subfolders
* cleaned up page sources

## 0.4 (03.10.2019)
* introduced possibility to distinguish between individuals and companies

## 0.3 (03.10.2019)
* introduced the possibility to resend the verification link to the user
* introduced a file-based error reporting feature that can be enabled through `ERRORS_ENABLED`
* introduced `.htaccess` file to prevent unnecessary file accesses and prettier URLs
* improved the different form examples to provide more meaningful and less error-prone output
* introduced website address normalization by making sure that these begin with either `http://` or `https://`
* introduced `CHANGELOG.md`

## 0.2 (01.10.2019)
* improved `get_subscribed()` and `get_verified()`
* improved error handling in example output
* fixed race condition during registration through DB improvement
* take `disabled` and `verified` state into account during process steps

## 0.1 (30.09.2019)
* initial commit
* initial readme
