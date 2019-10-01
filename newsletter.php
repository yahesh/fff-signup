<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get information to be verified
    $result = preview_newsletter($_GET);
    $link   = (array_key_exists("uid", $_GET) && array_key_exists("user", $_GET)); 
?>
<!DOCTYPE html>
<html>
  <head>
    <style>input, select { display : block; }</style>
    <title>Filmmakers for Future - Newsletter (GET)</title>
  </head>
  <body>
<?php if ($result) { ?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <select name="newsletter" required>
        <option value="" disabled>Choose option</option>
        <option value="0" <?= ($result["newsletter"]) ? "" : "selected" ?>>Just sign the statement.</option>
        <option value="1" <?= ($result["newsletter"]) ? "selected" : "" ?>>Please keep me updated.</option>
      </select>
      <input type="submit" value="Update">
    </form>
<?php } else { ?>
<?php if ($link) { ?>
    The newsletter subscription update link you used is invalid.<br>
<?php } ?>
    Request a new link:
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <input type="email" name="mail" required maxlength="256" placeholder="Mail">
      <input type="submit" value="Request">
    </form>
<?php } ?>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
    // verify given information
    $result = newsletter(array_merge($_GET, $_POST));
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Newsletter (POST)</title>
  </head>
  <body>
    Thank you for updating your newsletter subscription.<br>
<?php if ($result) { ?>
    At the moment there is nothing more to do for you.
<?php } else { ?>
    Unfortunately, an error has occured. Please try again later or contact us directly.
<?php } ?>
  </body>
</html>
<?php
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET, POST");
  }
