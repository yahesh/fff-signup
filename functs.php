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
  function get_subscribed(&$error = null) {
    $result = false;

    // connect to the database
    if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
      try {
        // we will grab some information
        $city                  = null;
        $country               = null;
        $iscompany             = null;
        $job                   = null;
        $mail                  = null;
        $name                  = null;
        $newsletter            = null;
        $uid                   = null;
        $user_newsletter_token = null;
        $website               = null;

        if ($statement = mysqli_prepare($link, "SELECT city,country,iscompany,job,mail,name,newsletter,uid,".
                                        "user_newsletter_token,website FROM data WHERE subscribed IS TRUE")) {
          try {
            if (mysqli_stmt_execute($statement)) {
              if (mysqli_stmt_bind_result($statement, $city, $country, $iscompany, $job, $mail, $name, $newsletter,
                                          $uid, $user_newsletter_token, $website)) {
                $result = [];
                while (mysqli_stmt_fetch($statement)) {
                  $result[] = [MAIL_CITY                  => $city,
                               MAIL_COUNTRY               => $country,
                               MAIL_ISCOMPANY             => $iscompany,
                               MAIL_JOB                   => $job,
                               MAIL_MAIL                  => $mail,
                               MAIL_NAME                  => $name,
                               MAIL_NEWSLETTER            => $newsletter,
                               MAIL_UID                   => $uid,
                               MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                               MAIL_WEBSITE               => $website];
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "select statement result could not be bound";
                }
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "select statement could not be executed";
              }
            }
          } finally {
            mysqli_stmt_close($statement);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "select statement could not be prepared";
          }
        }

      } finally {
        mysqli_close($link);
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "database connection could not be established";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      store_error($error);
    }

    return $result;
  }

  // get verified entries
  function get_verified(&$error = null) {
    $result = false;

    // connect to the database
    if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
      try {
        // we will grab some information
        $city                  = null;
        $country               = null;
        $iscompany             = null;
        $job                   = null;
        $mail                  = null;
        $name                  = null;
        $newsletter            = null;
        $uid                   = null;
        $user_newsletter_token = null;
        $website               = null;

        if ($statement = mysqli_prepare($link, "SELECT city,country,iscompany,job,mail,name,newsletter,uid,".
                                        "user_newsletter_token,website FROM data WHERE verified IS TRUE")) {
          try {
            if (mysqli_stmt_execute($statement)) {
              if (mysqli_stmt_bind_result($statement, $city, $country, $iscompany, $job, $mail, $name, $newsletter,
                                          $uid, $user_newsletter_token, $website)) {
                $result = [];
                while (mysqli_stmt_fetch($statement)) {
                  $result[] = [MAIL_CITY                  => $city,
                               MAIL_COUNTRY               => $country,
                               MAIL_ISCOMPANY             => $iscompany,
                               MAIL_JOB                   => $job,
                               MAIL_MAIL                  => $mail,
                               MAIL_NAME                  => $name,
                               MAIL_NEWSLETTER            => $newsletter,
                               MAIL_UID                   => $uid,
                               MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                               MAIL_WEBSITE               => $website];
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "select statement result could not be bound";
                }
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "select statement could not be executed";
              }
            }
          } finally {
            mysqli_stmt_close($statement);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "select statement could not be prepared";
          }
        }

      } finally {
        mysqli_close($link);
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "database connection could not be established";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      store_error($error);
    }

    return $result;
  }

  // escape HTML in the given $string
  function html($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, "UTF-8", false);
  }

  // update newsletter subscription,
  // only proceed if user is verified
  function newsletter($info, &$error = null) {
    $result = false;

    if (is_array($info)) {
      //normalize texts
      $info["newmail"] = strtolower(trim(get_element($info, "newmail")));
      $info["uid"]     = strtolower(trim(get_element($info, "uid")));
      $info["user"]    = strtolower(trim(get_element($info, "user")));

      // normalize newsletter
      if ("1" === get_element($info, "newsletter")) {
        $info["newsletter"] = true;
      } else {
        $info["newsletter"] = false;
      }

      // check if the given parameters fulfil minimal requirements
      if ((0 < strlen($info["newmail"])) ||
          ((0 < strlen($info["uid"])) && (0 < strlen($info["user"])))) {
        // connect to the database
        if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
          try {
            if (0 < strlen($info["newmail"])) { // send newsletter subscription update mail
              // we will grab some information
              $admin_verify_token    = null;
              $city                  = null;
              $country               = null;
              $iscompany             = null;
              $job                   = null;
              $mail                  = null;
              $name                  = null;
              $newsletter            = null;
              $uid                   = null;
              $user_newsletter_token = null;
              $user_verify_token     = null;
              $website               = null;

              $selected = null;
              if ($statement = mysqli_prepare($link, "SELECT admin_verify_token,city,country,iscompany,job,mail,name,".
                                              "newsletter,uid,user_newsletter_token,user_verify_token,website FROM ".
                                              "data WHERE verified IS TRUE AND mail=?")) {
                try {
                  if (mysqli_stmt_bind_param($statement, "s", $info["newmail"])) {
                    if (mysqli_stmt_execute($statement)) {
                      if (mysqli_stmt_bind_result($statement, $admin_verify_token, $city, $country, $iscompany, $job,
                                                  $mail, $name, $newsletter, $uid, $user_newsletter_token,
                                                  $user_verify_token, $website)) {
                        if (mysqli_stmt_fetch($statement)) {
                          $selected = true;
                        } else {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "select statement result could not be fetched";
                          }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "select statement result could not be bound";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "select statement could not be executed";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "select statement parameters could not be bound";
                    }
                  }
                } finally {
                  mysqli_stmt_close($statement);
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "select statement could not be prepared";
                }
              }

              if ($selected) {
                // send newsletter subscription update mail to user
                $senderror = [];
                $result    = send_mail($mail, USER_NEWSLETTER_MAIL_SUBJECT, USER_NEWSLETTER_MAIL_BODY,
                                       [MAIL_ADMIN_VERIFY_TOKEN    => $admin_verify_token,
                                        MAIL_CITY                  => $city,
                                        MAIL_COUNTRY               => $country,
                                        MAIL_ISCOMPANY             => ($iscompany) ? "yes" : "no",
                                        MAIL_JOB                   => $job,
                                        MAIL_MAIL                  => $mail,
                                        MAIL_NAME                  => $name,
                                        MAIL_NEWSLETTER            => ($newsletter) ? "yes" : "no",
                                        MAIL_UID                   => $uid,
                                        MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                        MAIL_USER_VERIFY_TOKEN     => $user_verify_token,
                                        MAIL_WEBSITE               => $website],
                                       $senderror);

                if (!$result) {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "mail could not be sent";
                    $error["senderror"]   = $senderror;
                  }
                }
              }
            } elseif ((0 < strlen($info["uid"])) && (0 < strlen($info["user"]))) { // update newsletter subscription
              // generate a new token
              $user_newsletter_token = generate_token();

              if ($statement = mysqli_prepare($link, "UPDATE data SET newsletter=?,user_newsletter_token=? ".
                                              "WHERE verified IS TRUE AND uid=? AND user_newsletter_token=?")) {
                try {
                  if (mysqli_stmt_bind_param($statement, "isss", $info["newsletter"], $user_newsletter_token,
                                             $info["uid"], $info["user"])) {
                    if (mysqli_stmt_execute($statement)) {
                      $result = (1 === mysqli_affected_rows($link));

                      if (!$result) {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "update statement did not affect the required number of rows";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "update statement could not be executed";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "update statement parameters could not be bound";
                    }
                  }
                } finally {
                  mysqli_stmt_close($statement);
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "update statement could not be prepared";
                }
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "unknown code branch executed";
              }
            }
          } finally {
            mysqli_close($link);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "database connection could not be established";
          }
        }
      } else {
        if (is_array($error)) {
          $error[ERROR_MESSAGE] = "mandatory info is not provided";
        }
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "info is not an array";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      $error["info"]         = $info;
      store_error($error);
    }

    return $result;
  }

  // preview update of newsletter subscription,
  // only proceed if user is verified
  function preview_newsletter($info, &$error = null) {
    $result = false;

    if (is_array($info)) {
      //normalize texts
      $info["uid"]  = strtolower(trim(get_element($info, "uid")));
      $info["user"] = strtolower(trim(get_element($info, "user")));

      // check if the given parameters fulfil minimal requirements
      if ((0 < strlen($info["uid"])) && (0 < strlen($info["user"]))) {
        // connect to the database
        if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
          try {
            // we will grab some information
            $newsletter = null;

            $selected = null;
            if ($statement = mysqli_prepare($link, "SELECT newsletter FROM data WHERE verified IS TRUE AND uid=? ".
                                            "AND user_newsletter_token=?")) {
              try {
                if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["user"])) {
                  if (mysqli_stmt_execute($statement)) {
                    if (mysqli_stmt_bind_result($statement, $newsletter)) {
                      if (mysqli_stmt_fetch($statement)) {
                        $selected = true;
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "select statement result could not be fetched";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "select statement result could not be bound";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "select statement could not be executed";
                    }
                  }
                } else {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "select statement parameters could not be bound";
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "select statement could not be prepared";
              }
            }

            if ($selected) {
              $result = [MAIL_NEWSLETTER => $newsletter];
            }
          } finally {
            mysqli_close($link);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "database connection could not be established";
          }
        }
      } else {
        if (is_array($error)) {
          $error[ERROR_MESSAGE] = "mandatory info is not provided";
        }
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "info is not an array";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      $error["info"]         = $info;
      store_error($error);
    }

    return $result;
  }

  // preview verificatin of given information,
  // only proceed if user is not disabled
  function preview_verify($info, &$error = null) {
    $result = false;

    if (is_array($info)) {
      //normalize texts
      $info["admin"] = strtolower(trim(get_element($info, "admin")));
      $info["uid"]   = strtolower(trim(get_element($info, "uid")));
      $info["user"]  = strtolower(trim(get_element($info, "user")));

      // check if the given parameters fulfil minimal requirements
      if ((0 < strlen($info["uid"])) && ((0 < strlen($info["admin"])) || (0 < strlen($info["user"])))) {
        // connect to the database
        if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
          try {
            // we will grab some information
            $city       = null;
            $country    = null;
            $iscompany  = null;
            $job        = null;
            $mail       = null;
            $name       = null;
            $newsletter = null;
            $website    = null;

            $selected = null;
            if (0 < strlen($info["admin"])) { // verify admin token
              if ($statement = mysqli_prepare($link, "SELECT city,country,iscompany,job,mail,name,newsletter,website ".
                                              "FROM data WHERE disabled IS FALSE AND uid=? AND admin_verify_token=?")) {
                try {
                  if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["admin"])) {
                    if (mysqli_stmt_execute($statement)) {
                      if (mysqli_stmt_bind_result($statement, $city, $country, $iscompany, $job, $mail, $name,
                                                  $newsletter, $website)) {
                        if (mysqli_stmt_fetch($statement)) {
                          $selected = true;
                        } else {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "select statement result could not be fetched";
                          }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "select statement result could not be bound";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "select statement could not be executed";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "select statement parameters could not be bound";
                    }
                  }
                } finally {
                  mysqli_stmt_close($statement);
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "select statement could not be prepared";
                }
              }
            } elseif (0 < strlen($info["user"])) { // verify user token
              if ($statement = mysqli_prepare($link, "SELECT city,country,iscompany,job,mail,name,newsletter,website ".
                                              "FROM data WHERE disabled IS FALSE AND uid=? AND user_verify_token=?")) {
                try {
                  if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["user"])) {
                    if (mysqli_stmt_execute($statement)) {
                      if (mysqli_stmt_bind_result($statement, $city, $country, $iscompany, $job, $mail, $name,
                                                  $newsletter, $website)) {
                        if (mysqli_stmt_fetch($statement)) {
                          $selected = true;
                        } else {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "select statement result could not be fetched";
                          }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "select statement result could not be bound";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "select statement could not be executed";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "select statement parameters could not be bound";
                    }
                  }
                } finally {
                  mysqli_stmt_close($statement);
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "select statement could not be prepared";
                }
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "unknown code branch executed";
              }
            }

            if ($selected) {
              $result = [MAIL_CITY       => $city,
                         MAIL_COUNTRY    => $country,
                         MAIL_ISCOMPANY  => $iscompany,
                         MAIL_JOB        => $job,
                         MAIL_MAIL       => $mail,
                         MAIL_NAME       => $name,
                         MAIL_NEWSLETTER => $newsletter,
                         MAIL_WEBSITE    => $website];
            }
          } finally {
            mysqli_close($link);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "database connection could not be established";
          }
        }
      } else {
        if (is_array($error)) {
          $error[ERROR_MESSAGE] = "mandatory info is not provided";
        }
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "info is not an array";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      $error["info"]         = $info;
      store_error($error);
    }

    return $result;
  }

  // register given information
  function register($info, &$error = null) {
    $result = false;

    if (is_array($info)) {
      //normalize texts
      $info["city"]    = trim(get_element($info, "city"));
      $info["country"] = trim(get_element($info, "country"));
      $info["job"]     = trim(get_element($info, "job"));
      $info["mail"]    = strtolower(trim(get_element($info, "mail")));
      $info["name"]    = trim(get_element($info, "name"));
      $info["website"] = trim(get_element($info, "website"));

      // normalize iscompany
      if ("1" === get_element($info, "iscompany")) {
        $info["iscompany"] = true;
      } else {
        $info["iscompany"] = false;
      }

      // normalize newsletter
      if ("1" === get_element($info, "newsletter")) {
        $info["newsletter"] = true;
      } else {
        $info["newsletter"] = false;
      }

      // normalize website
      if ((0 < strlen($info["website"])) &&
          (0 !== stripos($info["website"], "http://")) &&
          (0 !== stripos($info["website"], "https://"))) {
        $info["website"] = "http://".$info["website"];
      }

      // check if the given parameters fulfil minimal requirements
      if ((0 < strlen($info["country"])) && (0 < strlen($info["job"])) && (0 < strlen($info["mail"])) &&
          (0 < strlen($info["name"]))) {
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
            if ($statement = mysqli_prepare($link, "INSERT IGNORE INTO data (uid,name,mail,job,country,city,website,".
                                            "iscompany,newsletter,disabled,admin_verify_token,user_newsletter_token,".
                                            "user_verify_token) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")) {
              try {
                if (mysqli_stmt_bind_param($statement, "sssssssiiisss", $uid, $info["name"], $info["mail"],
                                           $info["job"], $info["country"], $info["city"], $info["website"],
                                           $info["iscompany"], $info["newsletter"], $disabled, $admin_verify_token,
                                           $user_newsletter_token, $user_verify_token)) {
                  if (mysqli_stmt_execute($statement)) {
                    $inserted = (1 === mysqli_affected_rows($link));

                    if (!$inserted) {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "insert statement did not affect the required number of rows";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "insert statement could not be executed";
                    }
                  }
                } else {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "insert statement parameters could not be bound";
                  }
                }
              } finally {
                mysqli_stmt_close($statement);
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "insert statement could not be prepared";
              }
            }

            if ($inserted) {
              // send verification mail to user
              $senderror = [];
              $result    = send_mail($info["mail"], USER_VERIFY_MAIL_SUBJECT, USER_VERIFY_MAIL_BODY,
                                     [MAIL_ADMIN_VERIFY_TOKEN    => $admin_verify_token,
                                      MAIL_CITY                  => $info["city"],
                                      MAIL_COUNTRY               => $info["country"],
                                      MAIL_ISCOMPANY             => ($info["iscompany"]) ? "yes" : "no",
                                      MAIL_JOB                   => $info["job"],
                                      MAIL_MAIL                  => $info["mail"],
                                      MAIL_NAME                  => $info["name"],
                                      MAIL_NEWSLETTER            => ($info["newsletter"]) ? "yes" : "no",
                                      MAIL_UID                   => $uid,
                                      MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                      MAIL_USER_VERIFY_TOKEN     => $user_verify_token,
                                      MAIL_WEBSITE               => $info["website"]],
                                     $senderror);

              if (!$result) {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "mail could not be sent";
                  $error["senderror"]   = $senderror;
                }
              }
            }
          } finally {
            mysqli_close($link);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "database connection could not be established";
          }
        }
      } else {
        if (is_array($error)) {
          $error[ERROR_MESSAGE] = "mandatory info is not provided";
        }
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "info is not an array";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      $error["info"]         = $info;
      store_error($error);
    }

    return $result;
  }

  function send_mail($recipient, $subject, $body, $placeholders = null, &$error = null) {
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
          $response = curl_exec($curl);
          if (false !== $response) {
            $responsecode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            if (false !== $responsecode) {
              // check that we received a "200 OK" response code
              $result = (200 === $responsecode);

              if (!$result) {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE]  = "mailgun returned a wrong response code";
                  $error["response"]     = $response;
                  $error["responsecode"] = $responsecode;
                }
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "curl response code could not be retrieved";
                $error["response"]    = $response;
              }
            }
          } else {
            if (is_array($error)) {
              $error[ERROR_MESSAGE] = "curl response could not be retrieved";
            }
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "curl options could not be set";
          }
        }
      } finally {
        curl_close($curl);
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "curl could not be initialized";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      $error["recipient"]    = $recipient;
      $error["subject"]      = $subject;
      $error["body"]         = $body;
      $error["placeholders"] = $placeholders;
      store_error($error);
    }

    return $result;
  }

  function store_error(&$error = null) {
    $result = false;

    // check that error was given
    if (ERRORS_ENABLED && (null !== $error) && (is_array($error))) {
      // generate the corresponding error date and ID
      $error[ERROR_ID]   = generate_token();
      $error[ERROR_TIME] = date("c", time());

      $result = (false !== file_put_contents(trail(ERRORS_FOLDER, DIRECTORY_SEPARATOR).$error[ERROR_ID],
                                             var_export($error, true)));
    }

    return $result;
  }

  // checks if $string ends with $trail
  // if not then $trail is appended to $string
  function trail($string, $trail) {
    $result = $string;
    if ($trail !== substr($result, -strlen($trail))) {
      $result = $result.$trail;
    }
    return $result;
  }

  // verify given information,
  // only proceed if user is not disabled
  function verify($info, &$error = null) {
    $result = false;

    if (is_array($info)) {
      //normalize texts
      $info["admin"]   = strtolower(trim(get_element($info, "admin")));
      $info["city"]    = trim(get_element($info, "city"));
      $info["country"] = trim(get_element($info, "country"));
      $info["job"]     = trim(get_element($info, "job"));
      $info["mail"]    = strtolower(trim(get_element($info, "mail")));
      $info["name"]    = trim(get_element($info, "name"));
      $info["newmail"] = strtolower(trim(get_element($info, "newmail")));
      $info["uid"]     = strtolower(trim(get_element($info, "uid")));
      $info["user"]    = strtolower(trim(get_element($info, "user")));
      $info["website"] = trim(get_element($info, "website"));

      // normalize iscompany
      if ("1" === get_element($info, "iscompany")) {
        $info["iscompany"] = true;
      } else {
        $info["iscompany"] = false;
      }

      // normalize newsletter
      if ("1" === get_element($info, "newsletter")) {
        $info["newsletter"] = true;
      } else {
        $info["newsletter"] = false;
      }

      // normalize website
      if ((0 < strlen($info["website"])) &&
          (0 !== stripos($info["website"], "http://")) &&
          (0 !== stripos($info["website"], "https://"))) {
        $info["website"] = "http://".$info["website"];
      }

      if ((0 < strlen($info["newmail"])) ||
          ((0 < strlen($info["uid"])) && ((0 < strlen($info["admin"])) || (0 < strlen($info["user"]))))) {
        // connect to the database
        if ($link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
          try {
            // we will grab some information
            $admin_verify_token    = null;
            $city                  = null;
            $country               = null;
            $iscompany             = null;
            $job                   = null;
            $mail                  = null;
            $name                  = null;
            $newsletter            = null;
            $uid                   = null;
            $user_newsletter_token = null;
            $user_verify_token     = null;
            $website               = null;

            $selected = null;
            if (0 < strlen($info["newmail"])) { // send verification mail to user
              if ($statement = mysqli_prepare($link, "SELECT admin_verify_token,city,country,iscompany,job,mail,name,".
                                              "newsletter,uid,user_newsletter_token,user_verify_token,website FROM ".
                                              "data WHERE user_verify_token IS NOT NULL AND mail=?")) {
                try {
                  if (mysqli_stmt_bind_param($statement, "s", $info["newmail"])) {
                    if (mysqli_stmt_execute($statement)) {
                      if (mysqli_stmt_bind_result($statement, $admin_verify_token, $city, $country, $iscompany, $job,
                                                  $mail, $name, $newsletter, $uid, $user_newsletter_token,
                                                  $user_verify_token, $website)) {
                        if (mysqli_stmt_fetch($statement)) {
                          $selected = true;
                        } else {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "select statement result could not be fetched";
                          }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "select statement result could not be bound";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "select statement could not be executed";
                      }
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "select statement parameters could not be bound";
                    }
                  }
                } finally {
                  mysqli_stmt_close($statement);
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "select statement could not be prepared";
                }
              }
            } elseif ((0 < strlen($info["uid"])) && ((0 < strlen($info["admin"])) || (0 < strlen($info["user"])))) {
              $updated = null;
              if (0 < strlen($info["admin"])) { // verify admin token
                // check if the updatable parameters fulfil minimal requirements
                if ((0 < strlen($info["country"])) && (0 < strlen($info["job"])) && (0 < strlen($info["mail"])) &&
                    (0 < strlen($info["name"]))) {
                  if ($statement = mysqli_prepare($link, "UPDATE data SET name=?,mail=?,job=?,country=?,city=?,".
                                                  "website=?,iscompany=?,newsletter=?,admin_verify_token=NULL WHERE ".
                                                  "disabled IS FALSE AND uid=? AND admin_verify_token=?")) {
                    try {
                      if (mysqli_stmt_bind_param($statement, "ssssssiiss", $info["name"], $info["mail"], $info["job"],
                                                 $info["country"], $info["city"], $info["website"], $info["iscompany"],
                                                 $info["newsletter"], $info["uid"], $info["admin"])) {
                        if (mysqli_stmt_execute($statement)) {
                          $updated = (1 === mysqli_affected_rows($link));

                          if (!$updated) {
                            if (is_array($error)) {
                              $error[ERROR_MESSAGE] = "update statement did not affect the required number of rows";
                            }
                          }
                        } else {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "update statement could not be executed";
                          }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "update statement parameters could not be bound";
                        }
                      }
                    } finally {
                      mysqli_stmt_close($statement);
                    }
                  } else {
                    if (is_array($error)) {
                      $error[ERROR_MESSAGE] = "update statement could not be prepared";
                    }
                  }
                } else {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "mandatory info for admin verification is not provided";
                  }
                }
              } elseif (0 < strlen($info["user"])) { // verify user token
                if ($statement = mysqli_prepare($link, "UPDATE data SET user_verify_token=NULL WHERE ".
                                                "disabled IS FALSE AND uid=? AND user_verify_token=?")) {
                  try {
                    if (mysqli_stmt_bind_param($statement, "ss", $info["uid"], $info["user"])) {
                      if (mysqli_stmt_execute($statement)) {
                        $updated = (1 === mysqli_affected_rows($link));

                        if (!$updated) {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "update statement did not affect the required number of rows";
                            }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "update statement could not be executed";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "update statement parameters could not be bound";
                      }
                    }
                  } finally {
                    mysqli_stmt_close($statement);
                  }
                } else {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "update statement could not be prepared";
                  }
                }
              } else {
                if (is_array($error)) {
                  $error[ERROR_MESSAGE] = "unknown code branch executed";
                }
              }

              if ($updated) {
                if ($statement = mysqli_prepare($link, "SELECT admin_verify_token,city,country,iscompany,job,mail,".
                                                "name,newsletter,uid,user_newsletter_token,user_verify_token,website ".
                                                "FROM data WHERE disabled IS FALSE AND uid=?")) {
                  try {
                    if (mysqli_stmt_bind_param($statement, "s", $info["uid"])) {
                      if (mysqli_stmt_execute($statement)) {
                        if (mysqli_stmt_bind_result($statement, $admin_verify_token, $city, $country, $iscompany, $job,
                                                    $mail, $name, $newsletter, $uid, $user_newsletter_token,
                                                    $user_verify_token, $website)) {
                          if (mysqli_stmt_fetch($statement)) {
                            $selected = true;
                          } else {
                            if (is_array($error)) {
                              $error[ERROR_MESSAGE] = "select statement result could not be fetched";
                            }
                          }
                        } else {
                          if (is_array($error)) {
                            $error[ERROR_MESSAGE] = "select statement result could not be bound";
                          }
                        }
                      } else {
                        if (is_array($error)) {
                          $error[ERROR_MESSAGE] = "select statement could not be executed";
                        }
                      }
                    } else {
                      if (is_array($error)) {
                        $error[ERROR_MESSAGE] = "select statement parameters could not be bound";
                      }
                    }
                  } finally {
                    mysqli_stmt_close($statement);
                  }
                } else {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "select statement could not be prepared";
                  }
                }
              }
            } else {
              if (is_array($error)) {
                $error[ERROR_MESSAGE] = "unknown code branch executed";
              }
            }

            if ($selected) {
              if (0 < strlen($info["newmail"])) { // send verification mail to user
                $recipient = $mail;
                $subject   = USER_VERIFY_MAIL_SUBJECT;
                $body      = USER_VERIFY_MAIL_BODY;
              } elseif (0 < strlen($info["admin"])) { // send verified mail to user
                $recipient = $mail;
                $subject   = USER_VERIFIED_MAIL_SUBJECT;
                $body      = USER_VERIFIED_MAIL_BODY;
              } elseif (0 < strlen($info["user"])) { // send verification mail to admin
                $recipient = ADMIN_MAIL;
                $subject   = ADMIN_VERIFY_MAIL_SUBJECT;
                $body      = ADMIN_VERIFY_MAIL_BODY;
              }

              $senderror = [];
              $result    = send_mail($recipient, $subject, $body,
                                     [MAIL_ADMIN_VERIFY_TOKEN    => $admin_verify_token,
                                      MAIL_CITY                  => $city,
                                      MAIL_COUNTRY               => $country,
                                      MAIL_ISCOMPANY             => ($iscompany) ? "yes" : "no",
                                      MAIL_JOB                   => $job,
                                      MAIL_MAIL                  => $mail,
                                      MAIL_NAME                  => $name,
                                      MAIL_NEWSLETTER            => ($newsletter) ? "yes" : "no",
                                      MAIL_UID                   => $uid,
                                      MAIL_USER_NEWSLETTER_TOKEN => $user_newsletter_token,
                                      MAIL_USER_VERIFY_TOKEN     => $user_verify_token,
                                      MAIL_WEBSITE               => $website],
                                     $senderror);

                if (!$result) {
                  if (is_array($error)) {
                    $error[ERROR_MESSAGE] = "mail could not be sent";
                    $error["senderror"]   = $senderror;
                  }
                }
            }
          } finally {
            mysqli_close($link);
          }
        } else {
          if (is_array($error)) {
            $error[ERROR_MESSAGE] = "database connection could not be established";
          }
        }
      } else {
        if (is_array($error)) {
          $error[ERROR_MESSAGE] = "mandatory info is not provided";
        }
      }
    } else {
      if (is_array($error)) {
        $error[ERROR_MESSAGE] = "info is not an array";
      }
    }

    // handle error storage
    if ((!$result) && is_array($error)) {
      $error[ERROR_FUNCTION] = __FUNCTION__;
      $error["info"]         = $info;
      store_error($error);
    }

    return $result;
  }

