<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  if ("GET" === HTTP_METHOD) {
    // nothing to do
  } elseif ("POST" === HTTP_METHOD) {
    $result = contact($_POST, $error);
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
    <style>input, select, textarea { display : block; }</style>
    <title>Filmmakers for Future - Contact (GET)</title>
  </head>
  <body>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <select name="subject" class="browser-default custom-select mb-4" required>
        <option value="" disabled selected>Choose option...</option>
<?php
    if (is_array(CONTACT_SUBJECTS)) {
      for ($i = 0; $i < count(CONTACT_SUBJECTS); $i++) {
?>
        <option value="<?= html(strval($i)) ?>"><?= html(CONTACT_SUBJECTS[$i][MAIL_SUBJECT]) ?></option>
<?php
      }
    }
?>
      </select>
      <input type="text" name="name" required maxlength="256" placeholder="Full name*">
      <input type="email" name="mail" required maxlength="256" placeholder="Email address*">
      <textarea name="message" required rows="5" placeholder="Message*"></textarea>
      <input type="submit" value="Send">
    </form>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Contact (POST)</title>
  </head>
  <body>
<?php
    if ($result) {
?>
    Thank you for contacting us.<br>
    We will come back to you as soon as possible.
<?php
    } else {
?>
    <b>Unfortunately, an error has occured<?=  (array_key_exists(ERROR_OUTPUT, $error)) ? ":</b> ".html($error[ERROR_OUTPUT]) : ".</b>" ?><br>
    Please try again later or send an e-mail to <a href="mailto:<?= html(ADMIN_MAIL) ?>"><?= html(ADMIN_MAIL) ?></a>.
    <?= (array_key_exists(ERROR_ID, $error)) ? "<br>Please provide the following error id when contacting us about this issue: ".html($error[ERROR_ID]) : "" ?>
<?php
    }
?>
  </body>
</html>
<?php
  }

