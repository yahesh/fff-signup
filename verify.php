<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  $link   = (array_key_exists("uid", $_GET) && (array_key_exists("admin", $_GET) || array_key_exists("user", $_GET)));
  $result = null;
  if ("GET" === HTTP_METHOD) {
    if ($link) {
      $result = preview_verify($_GET, $error);
    } else {
      $result = false; // show the initial form
    }
  } elseif ("POST" === HTTP_METHOD) {
    $result = verify(array_merge($_GET, $_POST), $error);
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET, POST");
  }
  $admin = ($result && array_key_exists("uid", $_GET) && array_key_exists("admin", $_GET) && !array_key_exists("user", $_GET));

  if ("GET" === HTTP_METHOD) {
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
      <input type="text" name="name" required maxlength="256" placeholder="Full name*" value="<?= html($result[MAIL_NAME]) ?>">
      <input type="text" name="mail" required maxlength="256" placeholder="Email address*" value="<?= html($result[MAIL_MAIL]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="job" required maxlength="256" placeholder="Job title*" value="<?= html($result[MAIL_JOB]) ?>">
      <input type="text" name="country" required maxlength="256" placeholder="Country*" value="<?= html($result[MAIL_COUNTRY]) ?>">
      <input type="text" name="city" maxlength="256" placeholder="City" value="<?= html($result[MAIL_CITY]) ?>">
      <input type="text" name="website" maxlength="256" placeholder="Website" value="<?= html($result[MAIL_WEBSITE]) ?>">
      <select name="iscompany" required>
        <option value="" disabled>Choose option...</option>
        <option value="0" <?= ($result[MAIL_ISCOMPANY]) ? "" : "selected" ?>>I am an individual.</option>
        <option value="1" <?= ($result[MAIL_ISCOMPANY]) ? "selected" : "" ?>>We are a company.</option>
      </select>
      <select name="newsletter" required <?= ($admin) ? "disabled readonly" : "" ?>>
        <option value="" disabled>Choose option...</option>
        <option value="0" <?= ($result[MAIL_NEWSLETTER]) ? "" : "selected" ?>>Just sign the statement.</option>
        <option value="1" <?= ($result[MAIL_NEWSLETTER]) ? "selected" : "" ?>>Please keep me updated.</option>
      </select>
      <input type="submit" value="Verify">
    </form>
<?php
    } else {
      if ($link) {
?>
    The verification link you used is invalid.<br>
    Please try again later or <a href="/contact">contact us</a> directly.
    <?= (array_key_exists(ERROR_ID, $error)) ? "<br>Please provide the following error id when contacting us about this issue: ".html($error[ERROR_ID]) : "" ?>
<?php
      }
?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      Request a new link:
      <input type="email" name="newmail" required maxlength="256" placeholder="Email address*">
      <input type="submit" value="Request">
      <b>Please note:</b> You can only verify your registration if you have submitted the registration and have not yet finished the verification process.
    </form>
<?php
    }
?>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Verify (POST)</title>
    <script>
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
      }
    </script>
  </head>
  <body>
<?php
    if ($result) {
      if ($link) {
        if ($admin) {
?>
    The registration has been fully verified.
<?php
        } else {
?>
    Thank you for verifying your registration.<br>
    We will now review your registration and send you an e-mail as soon as it is fully verified.
<?php
        }
      } else {
?>
    Thank you for verifying your registration.<br>
    We will send you an e-mail with further instructions.<br>
    Please check your spam folder, just in case.
<?php
      }
    } else {
?>
    <b>Unfortunately, an error has occured<?=  (array_key_exists(ERROR_OUTPUT, $error)) ? ":</b> ".html($error[ERROR_OUTPUT]) : ".</b>" ?><br>
    Please try again later or <a href="/contact">contact us</a> directly.
    <?= (array_key_exists(ERROR_ID, $error)) ? "<br>Please provide the following error id when contacting us about this issue: ".html($error[ERROR_ID]) : "" ?>
<?php
    }
?>
  </body>
</html>
<?php
  }

