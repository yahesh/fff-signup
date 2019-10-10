<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  if ("GET" === HTTP_METHOD) {
    // nothing to do
  } elseif ("POST" === HTTP_METHOD) {
    $result = register($_POST, $error);
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
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Register (POST)</title>
  </head>
  <body>
<?php
    if ($result) {
?>
    Thank you for submitting the registration.<br>
    We will send you an e-mail with further instructions.<br>
    Please check your spam folder, just in case.
<?php
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

