<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get information to be verified
    $result = preview_verify($_GET);
    $admin = ($result && array_key_exists("uid", $_GET) && array_key_exists("admin", $_GET) && !array_key_exists("user", $_GET));
?>
<!DOCTYPE html>
<html>
  <head>
    <style>input, select { display : block; }</style>
    <title>Filmmakers for Future - Verify (GET)</title>
  </head>
  <body>
<?php if ($result) { ?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <input type="text" name="name" value="<?= html($result["name"]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="mail" value="<?= html($result["mail"]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="job" value="<?= html($result["job"]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="website" value="<?= html($result["website"]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="country" value="<?= html($result["country"]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <input type="text" name="city" value="<?= html($result["city"]) ?>" <?= ($admin) ? "" : "disabled readonly" ?>>
      <select name="newsletter" <?= ($admin) ? "" : "disabled readonly" ?>>
        <option value="" disabled>Choose option</option>
        <option value="0" <?= ($result["newsletter"]) ? "" : "selected" ?> <?= ($admin) ? "" : "disabled readonly" ?>>Just sign the statement.</option>
        <option value="1" <?= ($result["newsletter"]) ? "selected" : "" ?> <?= ($admin) ? "" : "disabled readonly" ?>>Please keep me updated.</option>
      </select>
      <input type="submit" value="Verify">
    </form>
<?php } else { ?>
    The verification link you used is invalid.
<?php } ?>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
    // verify given information
    $result = verify(array_merge($_GET, $_POST));
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Verify (POST)</title>
  </head>
  <body>
    Thank you for verifying the registration.<br>
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
