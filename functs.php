<?php

  // generate a random and collision-free token of length 40
  function generate_token() {
    return strtolower(dechex(time()).bin2hex(random_bytes(16)));
  }

  // get array element without throwing a notice
  function get_element($array, $id) {
    $result = null;

    if (array_key_exists($id, $array)) {
      $result = $array[$id];
    }

    return $result;
  }

  // get subscribed entries
  function get_subscribed() {
    $result = [];

    // connect to the database
    if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
      try {
        // we will grab some information
        $city                  = null;
        $country               = null;
        $job                   = null;
        $mail                  = null;
        $name                  = null;
        $newsletter            = null;
        $uid                   = null;
        $user_newsletter_token = null;
        $website               = null;

        if ($statement = mysqli_prepare($link, "SELECT city, country, job, mail, name, newsletter, uid, ".
                                        "user_newsletter_token, website FROM data WHERE subscribed IS TRUE")) {
          try {
            if (mysqli_stmt_execute($statement)) {
              if (mysqli_stmt_bind_result($statement, $city, $country, $job, $mail, $name, $newsletter, $uid,
                                          $user_newsletter_token, $website)) {
                  while (mysqli_stmt_fetch($statement)) {
                    $result[] = [MAIL_CITY                  => $city,
                                 MAIL_COUNTRY               => $country,
                                 MAIL_JOB                   => $job,
                                 MAIL_MAIL                  => $mail,
                                 MAIL_NAME                  => $name,
                                 MAIL_NEWSLETTER            => $newsletter,
                                 MAIL_UID                   => $uid,
                                 MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                 MAIL_WEBSITE               => $website];
                  }
                }
              }
          } finally {
            mysqli_stmt_close($statement);
          }
        }

      } finally {
        mysqli_close($link);
      }
    }

    return $result;
  }

  // get verified entries
  function get_verified() {
    $result = [];

    // connect to the database
    if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
      try {
        // we will grab some information
        $city                  = null;
        $country               = null;
        $job                   = null;
        $mail                  = null;
        $name                  = null;
        $newsletter            = null;
        $uid                   = null;
        $user_newsletter_token = null;
        $website               = null;

        if ($statement = mysqli_prepare($link, "SELECT city, country, job, mail, name, newsletter, uid, ".
                                        "user_newsletter_token, website FROM data WHERE verified IS TRUE")) {
          try {
            if (mysqli_stmt_execute($statement)) {
              if (mysqli_stmt_bind_result($statement, $city, $country, $job, $mail, $name, $newsletter, $uid,
                                          $user_newsletter_token, $website)) {
                  while (mysqli_stmt_fetch($statement)) {
                    $result[] = [MAIL_CITY                  => $city,
                                 MAIL_COUNTRY               => $country,
                                 MAIL_JOB                   => $job,
                                 MAIL_MAIL                  => $mail,
                                 MAIL_NAME                  => $name,
                                 MAIL_NEWSLETTER            => $newsletter,
                                 MAIL_UID                   => $uid,
                                 MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                 MAIL_WEBSITE               => $website];
                  }
                }
              }
          } finally {
            mysqli_stmt_close($statement);
          }
        }

      } finally {
        mysqli_close($link);
      }
    }

    return $result;
  }

  // escape HTML in the given $string
  function html($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, "UTF-8", false);
  }

  // preview update of newsletter subscription,
  // only proceed if user is verified
  function preview_newsletter($info) {
    $result = false;

    //normalize texts
    $info["uid"]  = strtolower(trim(get_element($info, "uid")));
    $info["user"] = strtolower(trim(get_element($info, "user")));

    // check if the given parameters fulfill minimal requirements
    if ((0 < strlen($info["uid"])) && (0 < strlen($info["user"]))) {
      // connect to the database
      if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        try {
          // we will grab some information
          $newsletter = null;

          $selected = null;
          if ($statement = mysqli_prepare($link, "SELECT newsletter FROM data WHERE verified IS TRUE AND uid = ? AND ".
                                          "user_newsletter_token = ?")) {
            try {
              if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["user"])) {
                if (mysqli_stmt_execute($statement)) {
                  if (mysqli_stmt_bind_result($statement, $newsletter)) {
                    if (mysqli_stmt_fetch($statement)) {
                      $selected = true;
                    }
                  }
		}
              }
            } finally {
              mysqli_stmt_close($statement);
            }
          }

          if ($selected) {
            $result = ["newsletter" => $newsletter];
          }
        } finally {
          mysqli_close($link);
        }
      }
    }

    return $result;
  }

  // update newsletter subscription,
  // only proceed if user is verified
  function newsletter($info) {
    $result = false;

    //normalize texts
    $info["mail"] = strtolower(trim(get_element($info, "mail")));
    $info["uid"]  = strtolower(trim(get_element($info, "uid")));
    $info["user"] = strtolower(trim(get_element($info, "user")));

    // normalize newsletter
    if ("1" === get_element($info, "newsletter")) {
      $info["newsletter"] = true;
    } else {
      $info["newsletter"] = false;
    }

    // check if the given parameters fulfill minimal requirements
    if ((0 < strlen($info["mail"])) || ((0 < strlen($info["uid"])) && (0 < strlen($info["user"])))) {
      // connect to the database
      if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        try {
          if (0 < strlen($info["mail"])) { // send newsletter subscription update mail
            // we will grab some information
            $admin_verify_token    = null;
            $city                  = null;
            $country               = null;
            $job                   = null;
            $mail                  = null;
            $name                  = null;
            $newsletter            = null;
            $uid                   = null;
            $user_newsletter_token = null;
            $user_verify_token     = null;
            $website               = null;

            $selected = null;
            if ($statement = mysqli_prepare($link, "SELECT admin_verify_token, city, country, job, mail, name, ".
                                            "newsletter, uid, user_newsletter_token, user_verify_token, website ".
                                            "FROM data WHERE verified IS TRUE AND mail = ?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "s", $info["mail"])) {
                  if (mysqli_stmt_execute($statement)) {
                    if (mysqli_stmt_bind_result($statement, $admin_verify_token, $city, $country, $job, $mail,
                                                $name, $newsletter, $uid, $user_newsletter_token, $user_verify_token,
                                                $website)) {
                      if (mysqli_stmt_fetch($statement)) {
                        $selected = true;
                      }
                    }
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            }

            if ($selected) {
              // send newsletter subscription update mail to user
              $result = send_mail($mail, USER_NEWSLETTER_MAIL_SUBJECT, USER_NEWSLETTER_MAIL_BODY,
                                  [MAIL_ADMIN_VERIFY_TOKEN    => $admin_verify_token,
                                   MAIL_CITY                  => $city,
                                   MAIL_COUNTRY               => $country,
                                   MAIL_JOB                   => $job,
                                   MAIL_MAIL                  => $mail,
                                   MAIL_NAME                  => $name,
                                   MAIL_NEWSLETTER            => ($newsletter) ? "yes" : "no",
                                   MAIL_UID                   => $uid,
                                   MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                   MAIL_USER_VERIFY_TOKEN     => $user_verify_token,
                                   MAIL_WEBSITE               => $website]);
            }
          } elseif ((0 < strlen($info["uid"])) && (0 < strlen($info["user"]))) { // update newsletter subscription
            // generate a new token
            $user_newsletter_token = generate_token();

            if ($statement = mysqli_prepare($link, "UPDATE data SET newsletter = ?, user_newsletter_token = ? ".
                                            "WHERE verified IS TRUE AND uid = ? AND user_newsletter_token = ?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "isss", $info["newsletter"], $user_newsletter_token,
                                           $info["uid"], $info["user"])) {
                  if (mysqli_stmt_execute($statement)) {
                    $result = (1 === mysqli_affected_rows($link));
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
	    }
          }
        } finally {
          mysqli_close($link);
        }
      }
    }

    return $result;
  }

  // register given information
  function register($info) {
    $result = false;

    //normalize texts
    $info["city"]    = trim(get_element($info, "city"));
    $info["country"] = trim(get_element($info, "country"));
    $info["job"]     = trim(get_element($info, "job"));
    $info["mail"]    = strtolower(trim(get_element($info, "mail")));
    $info["name"]    = trim(get_element($info, "name"));
    $info["website"] = trim(get_element($info, "website"));

    // normalize newsletter
    if ("1" === get_element($info, "newsletter")) {
      $info["newsletter"] = true;
    } else {
      $info["newsletter"] = false;
    }

    // check if the given parameters fulfill minimal requirements
    if ((0 < strlen($info["name"])) && (0 < strlen($info["mail"])) && (0 < strlen($info["job"])) &&
        (0 < strlen($info["country"]))) {
      // connect to the database
      if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        try {
          // generate new tokens
          $admin_verify_token    = generate_token();
          $disabled              = false;
          $uid                   = generate_token();
          $user_verify_token     = generate_token();
          $user_newsletter_token = generate_token();

          $inserted = null;
          if ($statement = mysqli_prepare($link, "INSERT IGNORE INTO data (uid,name,mail,job,website,country,city,".
                                          "newsletter,disabled,admin_verify_token,user_newsletter_token,".
                                          "user_verify_token) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")) {
            try {
              if (mysqli_stmt_bind_param($statement, "sssssssiisss", $uid, $info["name"], $info["mail"],
                                         $info["job"], $info["website"], $info["country"], $info["city"],
                                         $info["newsletter"], $disabled, $admin_verify_token,
                                         $user_newsletter_token, $user_verify_token)) {
                if (mysqli_stmt_execute($statement)) {
                  $inserted = (1 === mysqli_affected_rows($link));
                }
              }
            } finally {
              mysqli_stmt_close($statement);
            }
          }

          if ($inserted) {
            // send verification mail to user
            $result = send_mail($info["mail"], USER_VERIFY_MAIL_SUBJECT, USER_VERIFY_MAIL_BODY,
                                [MAIL_ADMIN_VERIFY_TOKEN    => $admin_verify_token,
                                 MAIL_CITY                  => $info["city"],
                                 MAIL_COUNTRY               => $info["country"],
                                 MAIL_JOB                   => $info["job"],
                                 MAIL_MAIL                  => $info["mail"],
                                 MAIL_NAME                  => $info["name"],
                                 MAIL_NEWSLETTER            => ($info["newsletter"]) ? "yes" : "no",
                                 MAIL_UID                   => $uid,
                                 MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                 MAIL_USER_VERIFY_TOKEN     => $user_verify_token,
                                 MAIL_WEBSITE               => $info["website"]]);
          }
        } finally {
          mysqli_close($link);
        }
      }
    }

    return $result;
  }

  function send_mail($recipient, $subject, $body, $placeholders = null) {
    $result = false;

    if ($curl = curl_init()) {
      try {
        // replace placeholders
	if (null !== $placeholders) {
          $body    = str_replace(array_keys($placeholders), array_values($placeholders), $body);
          $subject = str_replace(array_keys($placeholders), array_values($placeholders), $subject);
        }

        $parameters = http_build_query(["from"    => MAILGUN_FROM,
                                        "to"      => $recipient,
                                        "subject" => $subject,
                                        "text"    => $body]);

        if (curl_setopt_array($curl,
                              [CURLOPT_URL            => MAILGUN_ENDPOINT,
                               CURLOPT_POST           => true,
                               CURLOPT_POSTFIELDS     => $parameters,
                               CURLOPT_RETURNTRANSFER => true,
                               CURLOPT_USERPWD        => MAILGUN_AUTH])) {
          if (false !== curl_exec($curl)) {
            $responsecode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            if (false !== $responsecode) {
              // check that we received a "200 OK" response code
              $result = (200 === $responsecode);
            }
          }
        }
      } finally {
        curl_close($curl);
      }
    } 

    return $result;
  }

  // preview verificatin of given information,
  // only proceed if user is not disabled
  function preview_verify($info) {
    $result = false;

    //normalize texts
    $info["admin"] = strtolower(trim(get_element($info, "admin")));
    $info["uid"]   = strtolower(trim(get_element($info, "uid")));
    $info["user"]  = strtolower(trim(get_element($info, "user")));

    // check if the given parameters fulfill minimal requirements
    if ((0 < strlen($info["uid"])) && ((0 < strlen($info["admin"])) || (0 < strlen($info["user"])))) {
      // connect to the database
      if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        try {
          // we will grab some information
          $city       = null;
          $country    = null;
          $job        = null;
          $mail       = null;
          $name       = null;
          $newsletter = null;
          $website    = null;
	  
          $selected = null;
          if (0 < strlen($info["admin"])) { // verify admin token
            if ($statement = mysqli_prepare($link, "SELECT city, country, job, mail, name, newsletter, website FROM ".
                                            "data WHERE disabled IS FALSE AND uid = ? AND admin_verify_token = ?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["admin"])) {
                  if (mysqli_stmt_execute($statement)) {
                    if (mysqli_stmt_bind_result($statement, $city, $country, $job, $mail, $name, $newsletter,
                                                $website)) {
                      if (mysqli_stmt_fetch($statement)) {
                        $selected = true;
                      }
                    }
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            }
          } elseif (0 < strlen($info["user"])) { // verify user token
            if ($statement = mysqli_prepare($link, "SELECT city, country, job, mail, name, newsletter, website FROM ".
                                            "data WHERE disabled IS FALSE AND uid = ? AND user_verify_token = ?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["user"])) {
                  if (mysqli_stmt_execute($statement)) {
                    if (mysqli_stmt_bind_result($statement, $city, $country, $job, $mail, $name, $newsletter,
                                                $website)) {
                      if (mysqli_stmt_fetch($statement)) {
                        $selected = true;
                      }
                    }
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            }
          }

          if ($selected) {
            $result = ["city"       => $city,
                       "country"    => $country,
                       "job"        => $job,
                       "mail"       => $mail,
                       "name"       => $name,
                       "newsletter" => $newsletter,
                       "website"    => $website];
          }
        } finally {
          mysqli_close($link);
        }
      }
    }

    return $result;
  }

  // verify given information,
  // only proceed if user is not disabled
  function verify($info) {
    $result = false;

    //normalize texts
    $info["admin"]   = strtolower(trim(get_element($info, "admin")));
    $info["city"]    = trim(get_element($info, "city"));
    $info["country"] = trim(get_element($info, "country"));
    $info["job"]     = trim(get_element($info, "job"));
    $info["mail"]    = strtolower(trim(get_element($info, "mail")));
    $info["name"]    = trim(get_element($info, "name"));
    $info["uid"]     = strtolower(trim(get_element($info, "uid")));
    $info["user"]    = strtolower(trim(get_element($info, "user")));
    $info["website"] = trim(get_element($info, "website"));

    // normalize newsletter
    if ("1" === get_element($info, "newsletter")) {
      $info["newsletter"] = true;
    } else {
      $info["newsletter"] = false;
    }

    // check if the given parameters fulfill minimal requirements
    if ((0 < strlen($info["uid"])) && ((0 < strlen($info["admin"])) || (0 < strlen($info["user"])))) {
      // connect to the database
      if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        try {
          $updated = null;
	  if (0 < strlen($info["admin"])) { // verify admin token
            // check if the updatable parameters fulfill minimal requirements
            if ((0 < strlen($info["name"])) && (0 < strlen($info["mail"])) && (0 < strlen($info["job"])) &&
                (0 < strlen($info["country"]))) {
              if ($statement = mysqli_prepare($link, "UPDATE data SET name = ?, mail = ?, job = ?, website = ?, ".
                                              "country = ?, city = ?, newsletter = ?, admin_verify_token = NULL ".
                                              "WHERE disabled IS FALSE AND uid = ? AND admin_verify_token = ?")) {
                try {
                  if (mysqli_stmt_bind_param($statement, "ssssssiss", $info["name"], $info["mail"], $info["job"],
                                             $info["website"], $info["country"], $info["city"], $info["newsletter"],
                                             $info["uid"], $info["admin"])) {
                    if (mysqli_stmt_execute($statement)) {
                      $updated = (1 === mysqli_affected_rows($link));
                    }
                  }
                } finally {
                  mysqli_stmt_close($statement);
                }
              }
            }
          } elseif (0 < strlen($info["user"])) { // verify user token
            if ($statement = mysqli_prepare($link, "UPDATE data SET user_verify_token = NULL WHERE disabled IS FALSE ".
                                            "AND uid = ? AND user_verify_token = ?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["user"])) {
                  if (mysqli_stmt_execute($statement)) {
                    $updated = (1 === mysqli_affected_rows($link));
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            }
          }

          // we will grab some information
          $admin_verify_token    = null;
          $city                  = null;
          $country               = null;
          $job                   = null;
          $mail                  = null;
          $name                  = null;
          $newsletter            = null;
          $uid                   = null;
          $user_newsletter_token = null;
          $user_verify_token     = null;
          $website               = null;

          $selected = null;
          if ($updated) {
            if ($statement = mysqli_prepare($link, "SELECT admin_verify_token, city, country, job, mail, name, ".
                                            "newsletter, uid, user_newsletter_token, user_verify_token, website ".
                                            "FROM data WHERE disabled IS FALSE AND uid = ?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "s", $info["uid"])) {
                  if (mysqli_stmt_execute($statement)) {
                    if (mysqli_stmt_bind_result($statement, $admin_verify_token, $city, $country, $job, $mail,
                                                $name, $newsletter, $uid, $user_newsletter_token, $user_verify_token,
                                                $website)) {
                      if (mysqli_stmt_fetch($statement)) {
                        $selected = true;
                      }
                    }
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            }
          }

          if ($selected) {
            if (0 < strlen($info["admin"])) { // send verified mail to user
              $recipient = $mail;
              $subject   = USER_VERIFIED_MAIL_SUBJECT;
              $body      = USER_VERIFIED_MAIL_BODY;
            } elseif (0 < strlen($info["user"])) { // send verification mail to admin
              $recipient = ADMIN_MAIL;
              $subject   = ADMIN_VERIFY_MAIL_SUBJECT;
              $body      = ADMIN_VERIFY_MAIL_BODY;
            }

            $result = send_mail($recipient, $subject, $body,
                                [MAIL_ADMIN_VERIFY_TOKEN    => $admin_verify_token,
                                 MAIL_CITY                  => $city,
                                 MAIL_COUNTRY               => $country,
                                 MAIL_JOB                   => $job,
                                 MAIL_MAIL                  => $mail,
                                 MAIL_NAME                  => $name,
                                 MAIL_NEWSLETTER            => ($newsletter) ? "yes" : "no",
                                 MAIL_UID                   => $uid,
                                 MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                 MAIL_USER_VERIFY_TOKEN     => $user_verify_token,
                                 MAIL_WEBSITE               => $website]);
          }
        } finally {
          mysqli_close($link);
        }
      }
    }

    return $result;
  }

