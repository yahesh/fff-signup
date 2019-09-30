<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
    // get subscribed users
    $result = get_subscribed();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Subscribed (GET)</title>
  </head>
  <body>
<?php var_dump($result); ?>
  </body>
</html>
<?php
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET");
  }
