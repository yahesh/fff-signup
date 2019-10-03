<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get information to be verified
    $link  = (array_key_exists("uid", $_GET) && array_key_exists("user", $_GET));
    $error = []; // has to be defined as an array to be used
    if ($link) {
      $result = preview_newsletter($_GET, $error);
    } else {
      $result = false; // show the initial form
    }
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
    The newsletter subscription update link you used is invalid.<br>
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
    $link   = (array_key_exists("uid", $_GET) && array_key_exists("user", $_GET));
    $error  = []; // has to be defined as an array to be used
    $result = newsletter(array_merge($_GET, $_POST), $error);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Newsletter (POST)</title>
  </head>
  <body>
    Thank you for updating your newsletter subscription.<br>
<?php
    if ($result) {
      if (!$link) {
?>
    If you have been registered and verified before, we will send you an e-mail with further instructions.
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

