<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  $link   = (array_key_exists("uid", $_GET) && array_key_exists("user", $_GET));
  $result = null;
  if ("GET" === HTTP_METHOD) {
    if ($link) {
      $result = preview_newsletter($_GET, $error);
    } else {
      $result = false; // show the initial form
    }
  } elseif ("POST" === HTTP_METHOD) {
    $result = newsletter(array_merge($_GET, $_POST), $error);
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET, POST");
  }

  if ("GET" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <style>input, select { display : block; }</style>
    <title>Filmmakers for Future - Newsletter (GET)</title>
  </head>
  <body>
<?php
    if ($result) {
?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <select name="newsletter" required>
        <option value="" disabled>Choose option...</option>
        <option value="0" <?= ($result[MAIL_NEWSLETTER]) ? "" : "selected" ?>>Just sign the statement.</option>
        <option value="1" <?= ($result[MAIL_NEWSLETTER]) ? "selected" : "" ?>>Please keep me updated.</option>
      </select>
      <input type="submit" value="Update">
    </form>
<?php
    } else {
      if ($link) {
?>
    The newsletter management link you used is invalid.<br>
    Please try again later or <a href="/contact">contact us</a> directly.
    <?= (array_key_exists(ERROR_ID, $error)) ? "<br>Please provide the following error id when contacting us about this issue: ".html($error[ERROR_ID]) : "" ?>
<?php
        }
?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      Request a new link:
      <input type="email" name="newmail" required maxlength="256" placeholder="Email address*">
      <input type="submit" value="Request">
      <b>Please note:</b> You can only manage your newsletter subscription if you have submitted the registration and have finished the verification process. We do not offer a general newsletters.
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
    <title>Filmmakers for Future - Newsletter (POST)</title>
  </head>
  <body>
<?php
    if ($result) {
      if ($link) {
?>
      Thank you for updating your newsletter subscription.
<?php
      } else {
?>
      Thank you for updating your newsletter subscription.<br>
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

