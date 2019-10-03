<?php
  require_once(__DIR__."/config.php");
  require_once(__DIR__."/consts.php");
  require_once(__DIR__."/functs.php");

  if ("GET" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <style>input, select { display : block; }</style>
    <title>Filmmakers for Future - Register (GET)</title>
  </head>
  <body>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <input type="text" name="name" required maxlength="256" placeholder="Full name*">
      <input type="email" name="mail" required maxlength="256" placeholder="Email address*">
      <input type="text" name="job" required maxlength="256" placeholder="Job title*">
      <input type="text" name="country" required maxlength="256" placeholder="Country*">
      <input type="text" name="city" maxlength="256" placeholder="City">
      <input type="link" name="website" maxlength="256" placeholder="Website / IMDB / Crew United">
      <select name="iscompany" required>
        <option value="" disabled selected>Choose option...</option>
        <option value="0">I am an individual.</option>
        <option value="1">We are a company.</option>
      </select>
      <select name="newsletter" required>
        <option value="" disabled selected>Choose option...</option>
        <option value="0">Just sign the statement.</option>
        <option value="1">Please keep me updated.</option>
      </select>
      <input type="submit" value="Register">
    </form>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
    // register given information
    $error  = []; // has to be defined as an array to be used
    $result = register(array_merge($_GET, $_POST), $error);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Register (POST)</title>
  </head>
  <body>
    Thank you for your registration.<br>
<?php
    if ($result) {
?>
    If you haven't been registered yet, we will send you an e-mail with further instructions.
<?php
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

