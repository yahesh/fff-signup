<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get subscribed users
    $error  = []; // has to be defined as an array to be used
    $result = get_subscribed($error);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Subscribed (GET)</title>
  </head>
  <body>
<?php
    if ($result) {
      if (0 < count($result)) {
?>
    <?= html(count($result)) ?> verified <?= (1 === count($result)) ? "person is" : "persons are" ?> subscribed to the newsletter.
<?php
      } else {
?>
    There are currently no verified persons subscribed to the newsletter.
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
    header("Allow: GET");
  }

