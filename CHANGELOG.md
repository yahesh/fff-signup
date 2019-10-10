# Changelog

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
