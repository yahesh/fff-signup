<?php
  define("HTTP_METHOD", strtoupper($_SERVER["REQUEST_METHOD"]));

  define("MAIL_ADMIN_VERIFY_TOKEN",    "{%ADMIN_VERIFY_TOKEN}");
  define("MAIL_CITY",                  "{%CITY}");
  define("MAIL_COUNTRY",               "{%COUNTRY}");
  define("MAIL_JOB",                   "{%JOB}");
  define("MAIL_MAIL",                  "{%MAIL}");
  define("MAIL_NAME",                  "{%NAME}");
  define("MAIL_NEWSLETTER",            "{%NEWSLETTER}");
  define("MAIL_UID",                   "{%UID}");
  define("MAIL_USER_NEWSLETTER_TOKEN", "{%USER_NEWSLETTER_TOKEN}");
  define("MAIL_USER_VERIFY_TOKEN",     "{%USER_VERIFY_TOKEN}");
  define("MAIL_WEBSITE",               "{%WEBSITE}");

