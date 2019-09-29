<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get information to be verified
    $result = preview_verify($_GET);
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
      <input type="text" value="<?= html($result["name"]) ?>" disabled readonly>
      <input type="text" value="<?= html($result["mail"]) ?>" disabled readonly>
      <input type="text" value="<?= html($result["job"]) ?>" disabled readonly>
      <input type="text" value="<?= html($result["website"]) ?>" disabled readonly>
      <input type="text" value="<?= html($result["country"]) ?>" disabled readonly>
      <input type="text" value="<?= html($result["city"]) ?>" disabled readonly>
      <select disabled readonly>
        <option value="0" <?= ($result["newsletter"]) ? "" : "selected" ?> disabled readonly>Just sign the statement.</option>
        <option value="1" <?= ($result["newsletter"]) ? "selected" : "" ?> disabled readonly>Please keep me updated.</option>
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
    $result = verify($_GET);
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
