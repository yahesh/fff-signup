<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  $result = null;
  if ("GET" === HTTP_METHOD) {
    $result = get_subscribed($error);
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET");
  }

  if ("GET" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Subscribed (GET)</title>
  </head>
  <body>
<?php
    if (is_array($result)) {
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

