<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get information to be verified
    $link  = (array_key_exists("uid", $_GET) && (array_key_exists("admin", $_GET) || array_key_exists("user", $_GET)));
    $error = []; // has to be defined as an array to be used
    if ($link) {
      $result = preview_verify($_GET, $error);
      $admin = ($result && array_key_exists("uid", $_GET) && array_key_exists("admin", $_GET) && !array_key_exists("user", $_GET));
    } else {
      $result = false; // show the initial form
    }
?>
<!DOCTYPE html>
<html>
  <head>
    <style>input, select { display : block; }</style>
    <title>Filmmakers for Future - Verify (GET)</title>
  </head>
  <body>
<?php
    if ($result) {
?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <input type="text" name="name" required maxlength="256" placeholder="Full name*" value="<?= html($result[MAIL_NAME]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="mail" required maxlength="256" placeholder="Email address*" value="<?= html($result[MAIL_MAIL]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="job" required maxlength="256" placeholder="Job title*" value="<?= html($result[MAIL_JOB]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="country" required maxlength="256" placeholder="Country*" value="<?= html($result[MAIL_COUNTRY]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="city" maxlength="256" placeholder="City" value="<?= html($result[MAIL_CITY]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="website" maxlength="256" placeholder="Website / IMDB / Crew United" value="<?= html($result[MAIL_WEBSITE]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <select name="iscompany" required <?= ($admin) ? "" : "disabled readonly" ?>>
        <option value="" disabled>Choose option...</option>
        <option value="0" <?= ($result[MAIL_ISCOMPANY]) ? "" : "selected" ?> <?= ($admin) ? "" : "disabled readonly" ?>>I am an individual.</option>
        <option value="1" <?= ($result[MAIL_ISCOMPANY]) ? "selected" : "" ?> <?= ($admin) ? "" : "disabled readonly" ?>>We are a company.</option>
      </select>
      <select name="newsletter" required <?= ($admin) ? "" : "disabled readonly" ?>>
        <option value="" disabled>Choose option...</option>
        <option value="0" <?= ($result[MAIL_NEWSLETTER]) ? "" : "selected" ?> <?= ($admin) ? "" : "disabled readonly" ?>>Just sign the statement.</option>
        <option value="1" <?= ($result[MAIL_NEWSLETTER]) ? "selected" : "" ?> <?= ($admin) ? "" : "disabled readonly" ?>>Please keep me updated.</option>
      </select>
      <input type="submit" value="Verify">
    </form>
<?php
    } else {
      if ($link) {
?>
    The verification link you used is invalid.<br>
<?php
        if (array_key_exists(ERROR_ID, $error)) {
?>
    Please provide the following error id when contacting us about this issue: <?= $error[ERROR_ID] ?><br>
<?php
        }
      }
?>
    Request a new link:
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <input type="email" name="newmail" required maxlength="256" placeholder="Email address*">
      <input type="submit" value="Request">
    </form>
<?php
    }
?>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
    // verify given information
    $link  = (array_key_exists("uid", $_GET) && (array_key_exists("admin", $_GET) || array_key_exists("user", $_GET)));
    $error  = []; // has to be defined as an array to be used
    $result = verify(array_merge($_GET, $_POST), $error);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Verify (POST)</title>
  </head>
  <body>
    Thank you for verifying the registration.<br>
<?php
    if ($result) {
      if (!$link) {
?>
    If you have been registered but not verified before, we will send you an e-mail with further instructions.
<?php
      }
    } else {
?>
    Unfortunately, an error has occured. Please try again later or contact us directly.<br>
<?php
      if (array_key_exists(ERROR_ID, $error)) {
?>
    Please provide the following error id when contacting us about this issue: <?= $error[ERROR_ID] ?>
<?php
      }
    }
?>
  </body>
</html>
<?php
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET, POST");
  }

